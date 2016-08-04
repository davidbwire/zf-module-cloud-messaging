<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace CloudMessaging\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of IndexControllerFactory
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class IndexControllerFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $indexController = new IndexController($serviceLocator->get('loggerService'));
        return $indexController;
    }

}
