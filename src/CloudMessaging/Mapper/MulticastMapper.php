<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace CloudMessaging\Mapper;

use Helper\Mapper\TableGateway;
use CloudMessaging\GoogleCloud\MulticastResponse;
use CloudMessaging\GoogleCloud\MulticastResult;
use Zend\Db\Sql\Sql;
use Exception;

/**
 * Description of MulticastMapper
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class MulticastMapper extends TableGateway
{

    /**
     *
     * @param MulticastResponse $multicastResponse
     * @return boolean true|false
     */
    public function addMulticastResponse(MulticastResponse $multicastResponse)
    {
        try {
            $sql = new Sql($this->getAdapter());
            $id = $this->generateUuid4String();
            $jsonRegistrationIds = $multicastResponse->getJsonRegistrationIds();
            $jsonData = $multicastResponse->getJsonData();
            $DataMulticast = [
                'id' => $id,
                'json_registration_ids' => $jsonRegistrationIds,
                'json_data' => $jsonData,
                'multicast_id' => $multicastResponse->getMulticastId(),
                'success' => $multicastResponse->getSuccess(),
                'failure' => $multicastResponse->getFailure(),
                'canonical_ids' => $multicastResponse->getCanonicalIds(),
                'response_code' => $multicastResponse->getResponseCode(),
                'create_time' => time()
            ];

            $multicastInsert = $sql->insert()
                    ->into('multicast')
                    ->values($DataMulticast);
            $result = $sql->prepareStatementForSqlObject($multicastInsert)
                    ->execute();
            if ($result->getAffectedRows()) {
                // add each result
                return $this->addMulticastResults($id,
                                $multicastResponse->getResults());
            }
            $this->getLogger()->crit('Adding  MulticastResponse with id ' . $id
                    . ' , json_data ' . $jsonData . ' and json_registration_ids '
                    . $jsonRegistrationIds . ' failed. See - ' . __METHOD__);
            return false;
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->crit($this->exceptionSummary($ex, __FILE__, __LINE__));
            return false;
        }
    }

    /**
     *
     * @param string $multiCastId
     * @param array $multicastResults
     * @return boolean
     */
    protected function addMulticastResults($multiCastId, array $multicastResults)
    {
        try {

            $multiStepStatus = true;

            foreach ($multicastResults as $multicastResult) {

                if ($multicastResult instanceof MulticastResult) {

                    try {
                        $addMulticastResult = $this->addMulticastResult($multiCastId,
                                $multicastResult);
                        if ($addMulticastResult === false) {
                            // only change $multiStepStatus in case of an error
                            $multiStepStatus = false;
                        }
                    } catch (\Exception $ex) {
                        $this->getLogger()
                                ->crit($this->exceptionSummary($ex, __FILE__,
                                                __LINE__));
                        // only change $multiStepStatus in case of an error
                        $multiStepStatus = false;
                    }
                }
            }
            return $multiStepStatus;
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->crit($this->exceptionSummary($ex, __FILE__, __LINE__));
            return false;
        }
    }

    protected function addMulticastResult($multiCastId,
            MulticastResult $multicastResult)
    {
        $sql = new Sql($this->getAdapter());
        $multicastResultInsert = $sql->insert()
                ->into('multicast_result');

        $registrationId = $multicastResult->getRegistrationId();
        $originalRegistrationId = $multicastResult->getOriginalRegistrationId();
        $recipientId = $multicastResult->getRecipientId();
        $id = $this->generateUuid4String();

        $DataMulticastResult = [
            'id' => $id,
            'multicast_id' => $multiCastId,
            'message_id' => $multicastResult->getMessageId(),
            'array_index' => $multicastResult->getArrayIndex(),
            'registration_id' => $registrationId,
            'original_registration_id' => $originalRegistrationId,
            'error' => $multicastResult->getError(),
            'should_delete_original_registration_id' => $multicastResult
                    ->shouldDeleteOriginalRegistrationId(),
            'should_replace_original_registration_id' => $multicastResult
                    ->shouldReplaceOriginalRegistrationId(),
            'should_retry_sending_later' => $multicastResult
                    ->shouldRetrySendingLater(),
            'recipient_id' => $recipientId,
            'create_time' => time()
        ];
        // delete if needed

        if ($multicastResult->shouldDeleteOriginalRegistrationId()) {
            $multiStepStatus = $this->deleteOriginalRegistrationId($recipientId,
                    $originalRegistrationId);
        }
        if ($multicastResult->shouldReplaceOriginalRegistrationId()) {
            $multiStepStatus = $this->replaceOriginalRegistrationId($recipientId,
                    $originalRegistrationId, $registrationId);
        }

        $multicastResultInsert->values($DataMulticastResult);

        $result = $sql->prepareStatementForSqlObject($multicastResultInsert)
                ->execute();
        if (!$result->getAffectedRows()) {
            $this->getLogger()
                    ->crit('Adding  MulticastResult with id ' . $id
                            . ' for user_id ' . $recipientId . 'with orig_reg_id '
                            . $originalRegistrationId . ' failed. See - ' . __METHOD__);
            return false;
        }
        return true;
    }

    /**
     *
     * @param string $userId
     * @param string $originalRegistrationId
     * @param string $newRegistrationId
     * @return boolean
     */
    protected function replaceOriginalRegistrationId($userId,
            $originalRegistrationId, $newRegistrationId)
    {
        try {
            $sql = new Sql($this->getAdapter());
            $update = $sql->update()
                    ->table('user')
                    ->set(['registration_id' => $newRegistrationId])
                    ->where(['id' => $userId,
                'registration_id' => $originalRegistrationId]);
            $result = $sql->prepareStatementForSqlObject($update)
                    ->execute();
            if ($result->getAffectedRows()) {
                return true;
            }
            $this->getLogger()
                    ->crit('Updating reg_id ' . $originalRegistrationId
                            . ' for user_id ' . $userId . 'with new_reg_id '
                            . $newRegistrationId . ' failed. See - ' . __METHOD__);
            return false;
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->crit($this->exceptionSummary($ex, __FILE__, __LINE__));
            return false;
        }
    }

    /**
     *
     * @param string $userId
     * @param string $originalRegistrationId
     * @return boolean
     */
    protected function deleteOriginalRegistrationId($userId,
            $originalRegistrationId)
    {
        try {
            $sql = new Sql($this->getAdapter());
            $update = $sql->update()
                    ->table('user')
                    ->set(['registration_id' => ''])
                    ->where(['id' => $userId,
                'registration_id' => $originalRegistrationId]);
            $result = $sql->prepareStatementForSqlObject($update)
                    ->execute();
            if ($result->getAffectedRows()) {
                return true;
            }
            $this->getLogger()
                    ->crit('updating reg_id ' . $originalRegistrationId
                            . ' for user_id ' . $userId . ' failed. See - ' . __METHOD__);
            return false;
        } catch (\Exception $ex) {
            $this->getLogger()
                    ->crit($this->exceptionSummary($ex, __FILE__, __LINE__));
            return false;
        }
    }

}
