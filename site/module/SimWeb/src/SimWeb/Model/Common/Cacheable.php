<?php

namespace SimWeb\Model\Common;
 
class Cacheable {
	
	public function __toCache() {
		return serialize($this);
	}
	
	public function __construct() {
		$this->created = time();
	}
	
	private $created;
	public function isExpired( $expires ) {
		if ($expires < 0) return false;
		if ($expires == 0) return true;
		return $this->created + $expires < time();
	}
	
}