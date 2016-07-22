<?php

/**
 * Copyright Bitmarshals Digital. All rights reserved.
 */

namespace CloudMessaging\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of MulticastMapperFactory
 *
 * @author David Bwire <israelbwire@gmail.com>
 */
class MulticastMapperFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $pushNotificationMapper = new MulticastMapper('multicast',
                $serviceLocator->get('DbAdapter'));
        return $pushNotificationMapper;
    }

}
