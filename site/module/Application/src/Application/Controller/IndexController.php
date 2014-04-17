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

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
		$realms = array(
				'Maelstrom',
				'Wyrmrest Accord',
				'Ragnaros',
				'Hyjal'
			);
		
		$link = $this->getRequest()->getPost('Opt',  FALSE );
		
		if ($link) {
			$region = $this->getRequest()->getPost('Char-Region',  'us' );
			$realm = $this->getRequest()->getPost('Char-Realm',  FALSE );
			$name = $this->getRequest()->getPost('Char-Name',  FALSE );
				
			if (!$realm || !$name) {} else
				
			$this->redirect()->toRoute('simulate', array(
				'region' => strtolower($region),
				'realm' => strtolower($realm),
				'name' => strtolower($name)
			));
		}
			
		$viewData = array(
			'realms' => $realms,
			'regions' => array("US", "CN", "EU", "KR" , "TW" )
		);
		return new ViewModel($viewData);
		
    }
	
}