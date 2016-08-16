<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace CloudMessaging\GoogleCloud;

/**
 * Description of MulticastResult
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class MulticastResult
{

    /**
     *
     * @var string
     */
    private $messageId;

    /**
     * Canonical Registration Id
     * 
     * @var string
     */
    private $registrationId;

    /**
     *
     * @var string
     */
    private $originalRegistrationId;

    /**
     *
     * @var string
     */
    private $error;

    /**
     *
     * @var int
     */
    private $arrayIndex;

    /**
     *
     * @var string
     */
    private $recipientId;

    /**
     *
     * @param int $messageId
     * @param string $registrationId
     * @param string $recipientId
     */
    public function __construct($messageId, $registrationId, $recipientId)
    {
        $this->messageId = $messageId;
        $this->registrationId = $registrationId;
        $this->recipientId = $recipientId;
    }

    /**
     *
     * @return string
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     *
     * @return string
     */
    public function getRegistrationId()
    {
        return $this->registrationId;
    }

    /**
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     *
     * @param string $messageId
     * @return \CloudMessaging\GoogleCloud\MulticastResult
     */
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
        return $this;
    }

    /**
     *
     * @param string $registrationId
     * @return \CloudMessaging\GoogleCloud\MulticastResult
     */
    public function setRegistrationId($registrationId)
    {
        $this->registrationId = $registrationId;
        return $this;
    }

    /**
     *
     * @param string $error
     * @return \CloudMessaging\GoogleCloud\MulticastResult
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * Check if we should replace the original registration_id
     * You need to replace the original id by matching the current index and
     * the passed in index
     *
     */
    public function shouldReplaceOriginalRegistrationId()
    {
        return (!empty($this->messageId) && !empty($this->registrationId));
    }

    /**
     *
     * @return boolean
     */
    public function shouldDeleteOriginalRegistrationId()
    {
        return (
                // application was uninstalled from the device
                $this->error === 'NotRegistered' ||
                // value got corrupted in the database
                $this->error === 'InvalidRegistration');
    }

    /**
     *
     * @return boolean
     */
    public function shouldRetrySendingLater()
    {
        return $this->error === 'Unavailable';
    }

    /**
     *
     * @return string
     */
    public function getOriginalRegistrationId()
    {
        return $this->originalRegistrationId;
    }

    /**
     *
     * @param string $originalRegistrationId
     * @return \CloudMessaging\GoogleCloud\MulticastResult
     */
    public function setOriginalRegistrationId($originalRegistrationId)
    {
        $this->originalRegistrationId = $originalRegistrationId;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getArrayIndex()
    {
        return $this->arrayIndex;
    }

    /**
     *
     * @param int $arrayIndex
     * @return \CloudMessaging\GoogleCloud\MulticastResult
     */
    public function setArrayIndex($arrayIndex)
    {
        $this->arrayIndex = $arrayIndex;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getRecipientId()
    {
        return $this->recipientId;
    }

    /**
     *
     * @param string $recipientId
     * @return \CloudMessaging\GoogleCloud\MulticastResult
     */
    public function setRecipientId($recipientId)
    {
        $this->recipientId = $recipientId;
        return $this;
    }

}
