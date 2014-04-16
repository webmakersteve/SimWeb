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

use Application\Model\Character;
use Application\Model\SimConfig;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
		$sim = new SimCraft();
		$link = $this->getRequest()->getPost('Link',  "http://us.battle.net/wow/en/character/wyrmrest-accord/Chaositi/simple" );
		
		$post = $link;
		$char = new Character();
		
		try {
			$char->Import( $link );	
		} catch (Exception $e) {
				
		}
		$c = new SimConfig();
		$c->importCharacter( $char );
		
		$data = $sim->simulateCharacter( $char );
			
		
			
        return new ViewModel(array(
			'toon' => $char->getName(),
			'realm' => $char->getRealm(),
			'thumb' => $char->getThumbnail(),
			'link' => $post,
			'data' => print_r($data, true),
			'gender' => $char->getGender(),
			'dps' => (int) $data['Player']['DPS']['value']
		));
    }
}