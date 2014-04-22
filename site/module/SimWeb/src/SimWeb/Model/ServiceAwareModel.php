<?php

namespace SimWeb\Model;

use Zend\ServiceManager\ServiceLocatorInterface,
	Zend\ServiceManager\ServiceLocatorAwareInterface;

class ServiceAwareModel  {
	
	protected static $services;
	
	public static function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		self::$services = $serviceLocator;
	}    

	public function getServiceLocator() {
		return self::$services;
	}
	
}
