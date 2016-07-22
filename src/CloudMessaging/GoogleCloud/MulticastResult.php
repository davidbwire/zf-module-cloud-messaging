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
    private $error;

    /**
     *
     * @param string $messageId
     * @param string $registrationId
     * @param string $error
     */
    public function __construct($messageId, $registrationId = null,
            $error = null)
    {
        $this->messageId = $messageId;
        $this->registrationId = $registrationId;
        $this->error = $error;
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
     *
     */
    public function replaceOriginalRegistrationId()
    {
        return !(empty($this->messageId) && empty($this->registrationId));
    }
    /**
     *
     * @return boolean
     */
    public function deleteOriginalRegistrationId()
    {
        return ($this->error === 'NotRegistered' || $this->error === 'InvalidRegistration');
    }

    public function retrySendingLater()
    {
        return $this->error === 'Unavailable';
    }

}
