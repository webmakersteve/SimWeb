<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace SimWeb;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
		
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
	
	public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'simcraft' => function (\Zend\ServiceManager\ServiceLocatorInterface $sl) {
                    $config = $sl->get('config'); 
                    return new Service\Simulator($config);
                },
				'blizzapi' => function(\Zend\ServiceManager\ServiceLocatorInterface $sl) {
					$x = new Service\BlizzAPI();
					$x->setServiceLocator($sl);
					return $x;
				},
            ),
        );
    }
	
	public function getViewHelperConfig()
    {	
        return array(
            'invokables' => array(
                // the array key here is the name you will call the view helper by in your view scripts
                'nicify' =>  '\SimWeb\View\Helper\Nicifier',
            ),
        );
    }
	
}
