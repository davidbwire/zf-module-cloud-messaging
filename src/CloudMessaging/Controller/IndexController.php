<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace CloudMessaging\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Log\Logger;
use CloudMessaging\Mapper\MulticastMapper;
use CloudMessaging\Mapper\MulticastResultMapper;

/**
 * Description of IndexController
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class IndexController extends AbstractActionController
{

    /**
     *
     * @var Logger
     */
    private $logger;

    /**
     *
     * @var MulticastMapper
     */
    private $multicastMapper;

    /**
     *
     * @var MulticastResultMapper
     */
    private $multicastResultMapper;

    public function __construct(Logger $logger,
            MulticastMapper $multicastMapper,
            MulticastResultMapper $multicastResultMapper)
    {
        $this->logger = $logger;
        $this->multicastMapper = $multicastMapper;
        $this->multicastResultMapper = $multicastResultMapper;
    }

    public function indexAction()
    {
        
    }

}
