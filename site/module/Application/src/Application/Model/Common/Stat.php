<?php

namespace Application\Model\Common;
 
class Stat {
	
	/* STAT TYPES */
	/* primary */
	
	const INTELLECT = 5;
	const STAMINA = 7;
	
	
	const HIT = 31;
	const HASTE = 36;
	
	protected static $types = array(
		3 => "AGILITY",
		4 => "STRENGTH",
		5 => "INTELLECT",
		6 => "SPIRIT",
		7 => "STAMINA",
		13 => "PARRY",
		14 => "DODGE",
		31 => "HIT",
		32 => "CRIT",
		36 => "HASTE",
		37 => "EXPERTISE",
		45 => "SP",
		49 => "MASTERY",
		57 => "PVPPOWER",
	);
	
	
	public function __construct( $data = NULL) {
		if ($data !== NULL) $this->exchangeArray($data);
	}
	
	protected $Type, $Amount;
	
	public function exchangeArray( $Data ) {
		try {
			
			$this->Type = $this->getTypeFromInt($Data->stat);
			
		} catch (StatException $e ) {
			$m = $e->getMessage() . " amount " . $Data->amount;
			throw new StatException($m);
		}
		$this->Amount = $Data->amount;
		
		return $this;
	}
	
	protected function getTypeFromInt( $int ) {
		
		if (array_key_exists( $int, self::$types )) return self::$types[$int];
		else throw new StatException("Unknown type: ".$int);
			
	}
	
	//combo settergetters
	
	public function Value($s=NULL) {
		if ($s === NULL) return $this->Amount;
		else return $this->Amount = $s;	
	}
	
	public function Type($s=NULL) {
		if ($s === NULL) return $this->Type;
		else return $this->Type = $s;	
	}
	
	protected function interpretModifiers( ) {
			
	}
	
}

class StatException extends \Exception {}