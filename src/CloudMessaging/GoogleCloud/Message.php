<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace CloudMessaging\GoogleCloud;

use CloudMessaging\GoogleCloud\MulticastResponse;
use CloudMessaging\GoogleCloud\MulticastResult;

/**
 * Description of Message
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class Message
{

    /**
     *
     * @var string
     */
    private $apiKey;

    /**
     *
     * @var array|object
     */
    protected $cloudMessagingConfig;

    /**
     *
     * @var array
     */
    protected $registrationIds = [];

    /**
     *
     * @var array
     */
    protected $userIds = [];

    /**
     *
     * @param string $apiKey
     */
    public function __construct($apiKey, array $cloudMessagingConfig)
    {
        $this->apiKey = $apiKey;
        $this->cloudMessagingConfig = $cloudMessagingConfig;
    }

    /**
     *
     * @param array $registrationIds
     * @param array $gcmDataOptions
     * @return false|MulticastResponse
     * @throws \RuntimeException
     */
    public function send(array &$registrationIds = [],
            array &$gcmDataOptions = ["title" => "You have a message."],
            &$userIds = [])
    {

        $gcmConfig = $this->cloudMessagingConfig['gcm_config'];
        if (count($registrationIds)) {
            // we have some ids passed thus replace the configured values
            $gcmConfig['registration_ids'] = $registrationIds;
            // since we're using custom reg_ids set user_ids
            $this->userIds = $userIds;
        }
        // remove actions key if it's not specified
        if (!isset($gcmDataOptions['actions']) ||
                empty($gcmDataOptions['actions'])) {
            if (array_key_exists('actions', $gcmConfig['data'])) {
                unset($gcmConfig['data']['actions']);
            }
        }
        // set the id's for later use
        $this->registrationIds = $gcmConfig['registration_ids'];

        // add or replace data on  $gcmConfig

        if (!empty($gcmDataOptions)) {
            foreach ($gcmDataOptions as $key => $value) {
                $gcmConfig['data'][$key] = $value;
            }
        }
        // prepare the headers
        $headers = [
            'Authorization: key=' . $this->apiKey,
            'Content-Type: application/json'
        ];

        $curlHandle = curl_init();


        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curlHandle, CURLOPT_URL,
                $this->cloudMessagingConfig['gcm_endpoint']);

        curl_setopt($curlHandle, CURLOPT_POST, true);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($gcmConfig));

        $result = curl_exec($curlHandle);

        if ($result === false) {
            // an error occured
            $curlError = curl_error($curlHandle);
            curl_close($curlHandle);
            throw new \RuntimeException('Curl error: ' . $curlError);
        }

        $responseCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

        // close connection
        curl_close($curlHandle);

        $aResponseData = json_decode($result, true, 512, JSON_BIGINT_AS_STRING);

        if ($aResponseData === null) {
            // decoding failed
            return false;
        }

        // check code
        if ($responseCode === 200) {
            // all went well
            return $this->processMultiCastResponseData($aResponseData, 200,
                            $gcmConfig);
        } else {
            /**
             * GCM error occured
             * 
             * 400 	Only applies for JSON requests. Indicates that the request could not be parsed as JSON, or it contained invalid fields (for instance, passing a string where a number was expected). The exact failure reason is described in the response and the problem should be addressed before the request can be retried.
             * 401 	There was an error authenticating the sender account.
             * 5xx 	Errors in the 500-599 range (such as 500 or 503) indicate that there was an internal error in the GCM connection server while trying to process the request, or that the server is temporarily unavailable (for example, because of timeouts). Sender must retry later, honoring any Retry-After header included in the response. Application servers must implement exponential back-off.
             */
            return $this->processMultiCastResponseData($aResponseData,
                            $responseCode, $gcmConfig);
        }
    }

    /**
     *
     * @param array $aResponseData
     * @param int $responseCode
     * @return MulticastResponse
     */
    protected function processMultiCastResponseData(array $aResponseData,
            $responseCode, &$gcmConfig)
    {

        $success = $aResponseData['success'];
        $failure = $aResponseData['failure'];
        $canonicalIds = $aResponseData['canonical_ids'];
        $multicastId = $aResponseData['multicast_id'];

        $multicastResponse = new MulticastResponse($success, $failure,
                $canonicalIds, $multicastId);

        // set the response code
        $multicastResponse->setResponseCode($responseCode);

        /**
         * If the value of failure and canonical_ids is 0, it's not necessary to parse the remainder of the response.
         * (!($failure === 0 && $canonicalIds === 0)) && isset($aResponseData['results'])
         * No need to apply above today
         * 
         */
        if (isset($aResponseData['results'])) {

            $results = $aResponseData['results'];

            foreach ($results as $index => $result) {

                $messageId = isset($result['message_id']) ? $result['message_id'] : null;
                $registrationId = isset($result['registration_id']) ? $result['registration_id'] : null;
                $error = isset($result['error']) ? $result['error'] : null;

                // attach the original index
                $recipientId = array_key_exists($index, $this->userIds) ?
                        $this->userIds[$index] : null;

                $multicastResult = new MulticastResult($messageId,
                        $registrationId, $recipientId);

                $multicastResult->setOriginalRegistrationId(
                                $this->registrationIds[$index])
                        ->setError($error)
                        ->setArrayIndex($index);

                $multicastResponse->addMulticastResult($multicastResult, $index);
            }
        }
        // attach the data and registration_ids
        $multicastResponse->setJsonData($gcmConfig['data'])
                ->setJsonRegistrationIds($gcmConfig['registration_ids']);

        return $multicastResponse;
    }

    /**
     * Get a list of all registration id's sent out
     * 
     * @return array
     */
    public function getRegistrationIdsSent()
    {
        return $this->registrationIds;
    }

}
