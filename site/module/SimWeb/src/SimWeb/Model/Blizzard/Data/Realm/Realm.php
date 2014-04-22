<?php

namespace SimWeb\Model\Blizzard\Data\Realm;
 
class Realm {
	
	protected $type, $pop, $status, $name, $slug, $locale, $timezone;
	
	public function exchange( $data ) {
		
		$this->type = isset($data['type']) ? $data['type'] : NULL;
		$this->pop = isset($data['pop']) ? $data['pop'] : NULL;
		$this->status = isset($data['status']) ? $data['status'] : NULL;
		$this->name = isset($data['name']) ? $data['name'] : NULL;
		$this->slug = isset($data['slug']) ? $data['slug'] : NULL;;
		$this->locale = isset($data['locale']) ? $data['locale'] : NULL;
		$this->timezone = isset($data['timezone']) ? $data['timezone'] : NULL;
		
	}
	
	public function getName() {
		return $this->name;	
	}
	
	public function getSlug() {
		return $this->slug;	
	}
	
}