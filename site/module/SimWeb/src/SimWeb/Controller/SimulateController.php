<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace SimWeb\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SimulateController extends AbstractActionController
{
    public function indexAction()
    {
		
		$sim = $this->getServiceLocator()->get('simcraft');
		$BlizzAPI = $this->getServiceLocator()->get('blizzapi');
		
		$Name = $this->params()->fromRoute('name', FALSE);
		$Region = $this->params()->fromRoute('region', FALSE);
		$Realm = $this->params()->fromRoute('realm', FALSE);
		
		if (!$Realm || !$Name || !$Realm) {
			$this->redirect()->toRoute( 'home' )->setStatusCode(201);
			exit;
		}
		
		$BlizzAPI->setRegion( $Region );
		
		$viewData = array(
			'toon' => $Name,
			'realm' => $Realm,
		);
		
		try {
			
			$char = $BlizzAPI->getCharacter( $Realm, $Name ); //MAKES AN HTTP REQUEST
			$c = $char->getSimConfig();
			$simulation = $sim->simulateCharacter( $c ); //MAKES AN HTTP REQUEST WHEN CHARACTER IS NOT SUPPLIED AND INSTEAD ARMORY LINK IS
			
			$viewData['thumb'] = $char->getThumbnail();
			$viewData['config'] = $c->generateText();
			$viewData['data'] = print_r($simulation->getRaw(), true);
			$viewData['gender'] = $char->getGender();
			$viewData['race'] = $char->getRace();
			$viewData['dps'] = $simulation->getDPS();
			
			
		} catch (Exception $e) {
			
		}	
		
		$view = new ViewModel($viewData);
		
		return $view;
    }
	
}