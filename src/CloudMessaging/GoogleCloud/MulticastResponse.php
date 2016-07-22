<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace CloudMessaging\GoogleCloud;

/**
 * Description of Multicast
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class MulticastResponse
{

    /**
     *
     * @var int
     */
    private $success;

    /**
     *
     * @var int
     */
    private $failure;

    /**
     *
     * @var int
     */
    private $canonicalIds;

    /**
     * Big Int
     *
     * @var int
     */
    private $multicastId;

    /**
     *
     * @var array
     */
    private $results = [];

    /**
     *
     * @var int
     */
    private $responseCode;

    /**
     *
     * @param int $success
     * @param int $failure
     * @param int $canonicalIds
     * @param int $multicastId
     */
    public function __construct($success, $failure, $canonicalIds, $multicastId)
    {
        $this->success = $success;
        $this->failure = $failure;
        $this->canonicalIds = $canonicalIds;
        $this->multicastId = $multicastId;
    }

    /**
     * Add a result to the result property
     *
     * @param MulticastResult $result
     * @return MulticastResponse Description
     */
    public function addResult(MulticastResult $result)
    {
        $this->results[] = $result;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     *
     * @return int
     */
    public function getFailure()
    {
        return $this->failure;
    }

    /**
     *
     * @return int
     */
    public function getCanonicalIds()
    {
        return $this->canonicalIds;
    }

    /**
     *
     * @return int
     */
    public function getMulticastId()
    {
        return $this->multicastId;
    }

    /**
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     *
     * @param int $success
     * @return \CloudMessaging\GoogleCloud\MulticastResponse
     */
    public function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }

    /**
     *
     * @param int $failure
     * @return \CloudMessaging\GoogleCloud\MulticastResponse
     */
    public function setFailure($failure)
    {
        $this->failure = $failure;
        return $this;
    }

    /**
     *
     * @param int $canonicalIds
     * @return \CloudMessaging\GoogleCloud\MulticastResponse
     */
    public function setCanonicalIds($canonicalIds)
    {
        $this->canonicalIds = $canonicalIds;
        return $this;
    }

    /**
     *
     * @param int $multicastId
     * @return \CloudMessaging\GoogleCloud\MulticastResponse
     */
    public function setMulticastId($multicastId)
    {
        $this->multicastId = $multicastId;
        return $this;
    }

   /**
     *
     * @param array $results
     * @return \CloudMessaging\GoogleCloud\MulticastResponse
     * @throws \RuntimeException
     */
    public function setResults(array $results)
    {
        foreach ($results as $result) {
            if (!($result instanceof MulticastResult)) {
                throw new \RuntimeException('Expecting object of type MulticastResult, '
                . ' ' . gettype($result) . ' provided. ' . __FILE__);
            }
        }
        $this->results = $results;
        return $this;
    }

    /**
     * Check if there's need to process results
     *
     * @return bool
     */
    public function processResults()
    {
        return !(($this->canonicalIds === 0) && ($this->failure === 0));
    }
    /**
     *
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }
    /**
     *
     * @param int $responseCode
     * @return \CloudMessaging\GoogleCloud\MulticastResponse
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = $responseCode;
        return $this;
    }

}
