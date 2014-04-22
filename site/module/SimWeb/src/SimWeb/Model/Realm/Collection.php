<?php

namespace SimWeb\Model\Realm;
 
use SimWeb\Model\ServiceAwareModel;
use SimWeb\Service\BlizzAPI as BlizzAPI;

use SimWeb\Model\Realm\Realm;

use Zend\Cache\StorageFactory;

class Collection extends ServiceAwareModel {

	protected $realms = NULL;
	protected $map = array(),
			  $slugMap = array();
	
	public function toArray() {
		$this->Fetch();
		return $this->realms;
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
	
	public function Fetch() {
		if ($this->realms !== NULL) return;
		$cache = $this->getCache();
		//now we check the cache
		$cached = $cache->getItem( 'realmList', $success);
		
		if (!$success) {
			$s = new BlizzAPI();
			$realms = $s->getRealms();
			
			$this->realms = array();
			
			foreach( $realms as $realm ) {
				$r = new Realm();
				$r->exchange($realm);
				$this->realms[] = $r;
				$this->map[$r->getSlug()] = $r->getName();
				$this->slugMap[] = $r->getSlug();
			}
			
			$cache->SetItem( 'realmList', serialize($this));
		} else {
			$r = unserialize($cached);
			list($this->realms,
				$this->map,
				$this->slugMap) = $r->export();
			unset($r);
		}
		
	}
	
	public function getRealm( $name, $slug=FALSE ) {
		$this->Fetch();
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