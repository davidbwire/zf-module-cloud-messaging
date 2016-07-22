<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace CloudMessaging\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of MulticastResultMapperFactory
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class MulticastResultMapperFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $pushNotificationResultMapper = new MulticastResultMapper('multicast_result',
                $serviceLocator->get('DbAdapter'));
        return $pushNotificationResultMapper;
    }

}
