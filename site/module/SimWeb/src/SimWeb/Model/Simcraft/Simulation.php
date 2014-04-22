<?php

namespace SimWeb\Model\Simcraft;

use SimWeb\Model\Character\Character;

class Simulation {
	
	private $raw = NULL;
	
	public function importSimulationArray( $Array ) {
		$this->raw = $Array;	
	}
	
	public function getDPS() {
		return (int) $this->raw['Player']['DPS']['value'];
	}
	
	public function getRaw() {
		return $this->raw;	
	}
		
}