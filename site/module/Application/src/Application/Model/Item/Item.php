<?php

namespace Application\Model\Item;

use Application\Model\Common\Stat;
 
class Item {
	
	public function __construct( $data = NULL) {
		if ($data !== NULL) $this->exchangeArray($data);
	}
	
	protected $ID, $Type, $Name, $Icon, $Quality, $iLevel, $Mods, $Stats, $Armor;
	
	public function exchangeArray( $Data ) {
		
		$this->ID = isset($Data['ID']) ? $Data['ID'] : 0;
		$this->Type = isset($Data['Type']) ? $Data['Type'] : NULL;
		$this->Name = isset($Data['Name']) ? $Data['Name'] : NULL;
		$this->Icon = isset($Data['Icon']) ? $Data['Icon'] : NULL;
		$this->Quality = isset($Data['Quality']) ? $Data['Quality'] : NULL;
		$this->iLevel = isset($Data['iLevel']) ? $Data['iLevel'] : NULL;
		$this->Armor = isset($Data['Armor']) ? $Data['Armor'] : NULL;
		if (isset($Data['Stats'])) {
			$stats = array();
			
			foreach( $Data['Stats'] as $stat ) {
				$stats[] = new Stat( $stat ) ;	
			}
			
			$this->Stats = $stats;	
		}
		return $this;
	}
	
	//combo settergetters
	
	public function ID($s=NULL) {
		if ($s === NULL) return $this->ID;
		else return $this->ID = $s;	
	}
	
	public function Type($s=NULL) {
		if ($s === NULL) return $this->Type;
		else return $this->Type = $s;	
	}
	
	public function Name($s=NULL) {
		if ($s === NULL) return $this->Name;
		else return $this->Name = $s;	
	}
	
	public function Icon($s=NULL) {
		if ($s === NULL) return $this->Icon;
		else return $this->Icon = $s;	
	}
	
	public function Quality($s=NULL) {
		if ($s === NULL) return $this->Quality;
		else return $this->Quality = $s;	
	}
	
	public function iLevel($s=NULL) {
		if ($s === NULL) return $this->iLevel;
		else return $this->iLevel = $s;	
	}
	
	public function Armor($s=NULL) {
		if ($s === NULL) return $this->Armor;
		else return $this->Armor = $s;	
	}
	
	protected function interpretModifiers( ) {
			
	}
	
	public function getSimStatsStr() { 
		$str = "";
		foreach($this->Stats as $stat ) {
			$str .= $stat->Value() . strtolower($stat->Type()) . "_";
		}
		return rtrim($str, "_");
	}
	public function getSimEnchantStr() { return ""; }
	public function getSimReforgeStr() { return ""; }
	public function getSimGemStr() { return ""; }

}