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

use SimWeb\Service\Simulator as SimCraft;

use SimWeb\Model\Character\Character;
use SimWeb\Model\Simcraft\Config as SimConfig;

use SimWeb\Model\Realm\Collection as Realms;
//= ([$]data\['[^']+[']\]) => = isset($1) ? $1 : NULL
class IndexController extends AbstractActionController
{
    public function indexAction() {
		
		$r = new Realms();
		$rarr = $r->toArray(); //lazy loaded
		
		foreach( $rarr as $realm ) {
			$realms[$realm->getSlug()] = $realm->getName();	
		}
		$viewData = array(
			'realms' => $realms,
			'regions' => array("US", "CN", "EU", "KR" , "TW" )
		);
		
		$link = $this->getRequest()->getPost('Opt',  FALSE );
		
		if ($link) {
			$region = $this->getRequest()->getPost('Char-Region',  'us' );
			$realm = $this->getRequest()->getPost('Char-Realm',  FALSE );
			$name = $this->getRequest()->getPost('Char-Name',  FALSE );
			
			try {
				if (!$realm || !$name) {
					
				} else
				$realm = $r->getRealm( $realm, true );
				
				$this->redirect()->toRoute('simulate', array(
					'region' => strtolower($region),
					'realm' => $realm->getSlug(),
					'name' => strtolower($name)
				));
				
			} catch (\Exception $e) {
				$msg = $e->getMessage();
			}
			
			if (isset($msg)) $viewData['error'] = $msg ?: '';
			
		}
			
		
		return new ViewModel($viewData);
		
    }
	
}