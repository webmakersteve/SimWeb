<?php

namespace SimWeb\Model\Blizzard\Data\Character;
 
class Specialization {
	
	public function __construct( $data = NULL) {
		if ($data !== NULL) $this->exchangeArray($data);
	}
	
	protected $Name, $Icon, $Choices = array();
	
	public function exchangeArray( $Data ) {
				
		$this->Name = isset($Data['Name']) ? $Data['Name'] : NULL;
		$this->Icon = isset($Data['Icon']) ? $Data['Icon'] : NULL;
		
		if ( isset($Data['Talents']) ) $this->Choices = $Data['Talents'];
				
		return $this;
	}
	
	//combo settergetters
	
	public function majorGlyphsToString() {
		return "";	
	}
	
	public function choicesToString() {
		$str = '';
		foreach( $this->Choices as $v ) {
			$str .= $v['Value']+1;	
		}
		return $str;
	}
	
	public function Name($s=NULL) {
		if ($s === NULL) return $this->Name;
		else return $this->ID = $Name;	
	}
	
	public function Icon($s=NULL) {
		if ($s === NULL) return $this->Icon;
		else return $this->Icon = $s;	
	}
	
	public function Choice( $Num=NULL, $s=NULL ) {
		if ($num === NULL) {
			return $this->Choices;
		} else {
			if (!array_key_exists( $Num, $this->Choices )) return NULL;
			if ($s === NULL) return $this->Choices[$num];
			else return $this->Choices[$num] = $s;
		}
	}
	
	
}