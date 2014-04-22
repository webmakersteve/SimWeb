<?php

namespace SimWeb\Model\Blizzard\Data\Item;

use SimWeb\Model\Blizzard\Data\Common\Stat,
	SimWeb\Model\Blizzard\Data\Item\Reforge;
 
class Equipment {
	
	public function __construct( $data = NULL) {
		if ($data !== NULL) $this->exchangeArray($data);
	}
	
	protected $ID, $Type, $Name, $Icon, $Quality, $iLevel, $Mods, $Stats, $Armor;
	
	public function exchangeArray( $Data ) {
		
		$this->ID = isset($Data['ID']) ? $Data['ID'] : 0;
		if (isset($Data['Mods'])) {
			$this->interpretModifiers($Data['Mods']);
		}
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
	
	private function addGem( $data ) {
		//echo $data."<br>";
	}
	
	private $reforge = FALSE;
	private function addReforge( $data ) {
		$this->reforge = new Reforge( $data );
	}
	
	public function getReforge() {
		return $this->reforge;	
	}
	public function setReforge( $id ) {
		$this->addReforge( $id );
	}
	
	private $upgrade = FALSE;
	private function addUpgrade( $data ) {
		$this->upgrade = $data->current;
	}
	
	public function getUpgrade() {
		return $this->upgrade;
	}
	
	protected function interpretModifiers( $mods=array() ) {
		
		//first do gems
		foreach( $mods as $k=>$v ) {
			if (stristr( $k, 'gem' )) {
				$this->addGem( $v );
			} elseif (stristr( $k, 'reforge' )) {
				$this->addReforge( $v );
			} elseif (stristr( $k, 'upgrade' )) {
				$this->addUpgrade( $v );
			}
			
		}
		
	}
	
	public function getSimStatsStr() { 
		$str = "";
		foreach($this->Stats as $stat ) {
			$str .= $stat->Value() . strtolower($stat->Type()) . "_";
		}
		return rtrim($str, "_");
	}
	public function getSimEnchantStr() {
		return "";
		//enchant=285int_165crit
		return sprintf("enchant=%s,", "");
	}
	public function getSimReforgeStr() {
		if (!$this->reforge) return "";
		return sprintf("reforge=%s,", (string) $this->reforge);
		
	}
	public function getSimGemStr() {
		return "";
		//gems=320mastery_320mastery_120int
		return sprintf("gems=%s,", '');
	
	}
	public function getSimUpgradeStr() {
		if (!$this->upgrade) return "";
		return sprintf("upgrade=%s,", $this->upgrade);
	}

}