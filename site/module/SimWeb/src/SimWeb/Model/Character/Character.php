<?php

namespace SimWeb\Model\Character;
 
use SimWeb\Service\BlizzAPI;
use SimWeb\Model\Item\Equipment;
use SimWeb\Model\Character\Specialization;
 
class Character {
	
	protected function exchange($data) {
			
	}
	
	public function __construct( $data = NULL) {
		if ($data !== NULL) $this->exchange($data);
	}
	
	protected $URL = NULL;
	
	protected $name, $realm, $class, $level, $race, $gender, $thumb, $region;
	
	protected $activeSpec;
	protected $inactiveSpec;
	protected $equipment;
	
	public function exchangeArray( $Data ) {
		
		$this->name = isset($Data['Name']) ? $Data['Name'] : NULL;
		$this->realm = isset($Data['Realm']) ? $Data['Realm'] : NULL;
		$this->class = isset($Data['Class']) ? $Data['Class'] : NULL;
		$this->level = isset($Data['Level']) ? $Data['Level'] : NULL;
		$this->race = isset($Data['Race']) ? $Data['Race'] : NULL;
		$this->gender = isset($Data['Gender']) ? $Data['Gender'] : NULL;
		$this->thumb = isset($Data['Thumb']) ? $Data['Thumb'] : NULL;
		$this->region = isset($Data['Region']) ? $Data['Region'] : "us";
		
		//there is a LOT more
		if (isset($Data['Items'])) {
			$items = array();
			
			foreach( $Data['Items'] as $ItemData ) {
				
				$items[$ItemData['Type']] = new Equipment( $ItemData );
					
			}
			$this->equipment = $items;
		}
		
		if (isset($Data['Talents'])) {
			$active = $Data['Talents']['Active']; //should always be set
			if ($active) {
				$spec = new Specialization( $active );	
				$this->activeSpec = $spec;
			}
		}
		
		return $this;
	}
	
	public function Fetch(  ) {
		$armory = new BlizzAPI();
		try {
			if ($this->URL) $CharData = $armory->getCharacter( $this->URL );
			else $CharData = $armory->getCharacter( $this->realm, $this->name );
			return $this->exchangeArray($CharData);
		} catch (\Exception $e) {
			throw $e;
		}
		
	}
	
	public function getLink() {return $this->URL; }
	public function setLink( $s ) {$this->URL = $s; }
	
	
	private static $races = array(
		1 => "Human",
		2 => "Orc",
//		3 => "Dwarf",
//		4 => "Gnome",
		5 => "Undead",
		6 => "Tauren",
//		7 => "Draenei",
		8 => "Troll",
		9 => "Goblin",
		10 => "Blood Elf",
		26 => "Pandaren",
	);
	
	private static $genders = array(
		'male', 'female'
	);
	
	private static $classes = array(
		5 => "priest",
		8 => "mage",
		9 => "warlock"
	);
	
	private function Nicify( $str ) {
		return str_replace(" ", "-", strtolower( $str ));
	}
	
	/** Setters and getters for general data
	======================================= */
	
	public function getEquipment() {
		return $this->equipment;	
	}
	
	public function getActiveSpec() {
		return $this->activeSpec;	
	}
	
	public function switchSpecs() {
			
	}
	
	public function getName($nicify=FALSE) {
		if ($nicify) return $this->Nicify( $this->name );
		return $this->name;}
	public function setName($name) {$this->name = $name;}
	
	public function getRegion($nicify=FALSE) {
		if ($nicify) return $this->Nicify( $this->region );
		return $this->region;
	}
	public function setRegion($region) {$this->region = $region;}
	
	public function getRealm($nicify=FALSE) {
		if ($nicify) return $this->Nicify( $this->realm );
		return $this->realm;
	}
	public function setRealm($realm) {$this->realm = $realm;}
	
	public function getClass() {return self::$classes[$this->class];}
	public function setClass($class) {$class = strtolower($class);if (in_array($class)) $this->class = array_search( $class ); }
	
	public function getRace() {return self::$races[$this->race];}
	public function setRace($race) {
		$race = strtolower($race);
		if (in_array($race, self::$races)) $this->race = array_search( $race );
	}
	
	public function getFaction() {
			
	}
	
	public function getLevel() {return $this->level;}
	public function setLevel($level) {$this->level = $level;}
	
	
	
	public function getGender( $n = FALSE ) {
		if ($n) return $this->gender;
		return self::$genders[$this->gender];
		
	}
	public function setGender($gender) {$this->gender = $gender;}
	
	public function getThumbnail() {return $this->thumb;}
	
	public function getSimProfsStr() {
		return "";	
	}
	
}