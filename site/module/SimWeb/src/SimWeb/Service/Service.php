<?php

namespace SimWeb\Service;

use Zend\ServiceManager\ServiceLocatorInterface,
	Zend\ServiceManager\ServiceLocatorAwareInterface;

class Service implements ServiceLocatorAwareInterface  {
	
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
		$this->services = $serviceLocator;
	}    

	public function getServiceLocator() {
		return $this->services;
	}
	
}
