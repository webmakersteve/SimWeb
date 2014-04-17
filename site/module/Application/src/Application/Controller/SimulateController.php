<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Application\Service\Simulator as SimCraft;

use Application\Model\Character\Character;
use Application\Model\Simcraft\Config as SimConfig;

class SimulateController extends AbstractActionController
{
    public function indexAction()
    {
		
		$sim = new SimCraft();
		$Name = $this->params()->fromRoute('name', FALSE);
		$Region = $this->params()->fromRoute('region', FALSE);
		$Realm = $this->params()->fromRoute('realm', FALSE);
		
		if (!$Realm || !$Name || !$Realm) {
			$this->redirect()->toRoute( 'home' )->setStatusCode(201);
			exit;
		}
		
		$char = new Character();
		
		$char->setName( $Name );
		$char->setRealm( $Realm );
		$char->setRegion( $Region );
		
		$viewData = array(
			'toon' => $char->getName(),
			'realm' => $char->getRealm(),
		);
		
		try {
			
			$char->Fetch();	 //after we set the name and realm this model will utilize the BlizzAPI service
			//When it does that it will exchange the data recieved by the character request into the object thus overriding any custom sets
			//Only use it when you want to override or initially fetch the data
			$c = new SimConfig();
			$c->importCharacter( $char );
			
			$data = $sim->simulateCharacter( $char );
			$viewData['thumb'] = $char->getThumbnail();
			$viewData['data'] = print_r($data, true);
			$viewData['gender'] = $char->getGender();
			$viewData['race'] = $char->getRace();
			$viewData['dps'] = (int) $data['Player']['DPS']['value'];
			
			//MAKES AN HTTP REQUEST
		} catch (Exception $e) {
			
		}	
		
		$view = new ViewModel($viewData);
		
		return $view;
    }
	
	public function awesomeAction() {
		die('hey');	
	}
	
}