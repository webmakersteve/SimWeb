<?php

namespace SimWebTest\Controller;

use SimWebTest\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use SimWeb\Controller\IndexController;
use Zend\Http\Request;
use Zend\Http\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use PHPUnit_Framework_TestCase;

class IndexControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $controller;
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;

    protected function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $this->controller = new IndexController();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => 'index'));
        $this->event      = new MvcEvent();
        $config = $serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : array();
        $router = HttpRouter::factory($routerConfig);

        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
		
    }
	
	protected $requestLength;
	
	public function testIndexActionCanBeAccessed()
	{
		$this->routeMatch->setParam('action', 'index');
	
		$one = microtime();
		$result   = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();
		$two = microtime();
		$this->requestLength = $two - $one;
		$this->assertEquals(200, $response->getStatusCode());
		
	}
	
	public function testCheckIfRealmsAreCaching() {
		$this->routeMatch->setParam('action', 'index');
		$one = microtime();
		$result   = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();
		$two = microtime();
		$two = $two - $one;
		
		$this->assertGreaterThan($this->requestLength, $two);	
	}
	
	public function testIndexProperlyRoutesOnPost() {
		
		$this->routeMatch->setParam('action', 'index');
		$this->request->getPost()->set('Opt', TRUE);
		$this->request->getPost()->set('Char-Region',  'us' );
		$this->request->GetPost()->set('Char-Realm', 'maelstrom');
		$this->request->GetPost()->set('Char-Name', 'Chaosity');
		
		$result   = $this->controller->dispatch($this->request);
		$response = $this->controller->getResponse();
		
		$this->assertEquals(302, $response->getStatusCode());
			
	}
	
}