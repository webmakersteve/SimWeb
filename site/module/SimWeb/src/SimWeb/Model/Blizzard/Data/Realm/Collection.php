<?php

namespace SimWeb\Model\Blizzard\Data\Realm;
 
use SimWeb\Model\ServiceAwareModel;
use SimWeb\Service\BlizzAPI as BlizzAPI;

use SimWeb\Model\Common\Cacheable;

use Zend\Cache\StorageFactory;

class Collection extends Cacheable  {

	protected $realms = NULL;
	protected $map = array(),
			  $slugMap = array();
	
	public function toArray() {
		return $this->realms;
	}
	
	public function toAssocArray() {
		return $this->map;	
	}
	
	private function getCache() {
		$cache = StorageFactory::factory(array(
			'adapter'	=>	array(
				'name'		=> 'filesystem',
				'options'	=> array(
					'cache_dir'	=>	'data/cache'
				),
			),
		));
		return $cache;	
	}
	
	public function Exchange($Data) {
			
		$this->realms = array();
		
		foreach( $Data as $realm ) {
			$r = new Realm();
			$r->exchange($realm);
			$this->realms[] = $r;
			$this->map[$r->getSlug()] = $r->getName();
			$this->slugMap[] = $r->getSlug();
		}
		
	}
	
	public function getRealm( $name, $slug=FALSE ) {
		if ($slug) {
			//this is the easiest since they are stored by slug
			if ($key = array_search($name, $this->slugMap)) {
				return $this->realms[$key];
			} else throw new \Exception("Realm slug '$name' does not exist");
		} else {
			//not slug we need to search map and then search
			if ($key = array_search( $name, $this->map )) {
				return $this->getRealm( $key, true );	
			} else throw new \Exception("Realm '$name' does not exist");
		}
	}
	
	public function export() {
		return array(
			$this->realms,
			$this->map,
			$this->slugMap
		);	
	}
	
}