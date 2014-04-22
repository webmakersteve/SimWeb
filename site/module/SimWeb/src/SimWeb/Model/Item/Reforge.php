<?php

namespace SimWeb\Model\Item;

use SimWeb\Model\Common\Stat;
 
class Reforge {
	
	private static $reforges = array(
		113 => "spirit_dodge",
		114 => "spirit_parry",
		115 => "spirit_hit",
		116 => "spirit_crit",
		117 => "spirit_haste",
		118 => "spirit_expertise",
		119 => "spirit_mastery",
		120 => "dodge_spirit",
		121 => "dodge_parry",
		122 => "dodge_hit",
		123 => "dodge_crit",
		124 => "dodge_haste",
		125 => "dodge_expertise",
		126 => "dodge_mastery",
		127 => "parry_spirit",
		128 => "parry_dodge",
		129 => "parry_hit",
		130 => "parry_crit",
		131 => "parry_haste",
		132 => "parry_expertise",
		133 => "parry_mastery",
		134 => "hit_spirit",
		135 => "hit_dodge",
		136 => "hit_parry",
		137 => "hit_crit",
		138 => "hit_haste",
		139 => "hit_expertise",
		140 => "hit_mastery",
		141 => "crit_spirit",
		142 => "crit_dodge",
		143 => "crit_parry",
		144 => "crit_hit",
		145 => "crit_haste",
		146 => "crit_expertise",
		147 => "crit_mastery",
		148 => "haste_spirit",
		149 => "haste_dodge",
		150 => "haste_parry",
		151 => "haste_hit",
		152 => "haste_crit",
		153 => "haste_expertise",
		154 => "haste_mastery",
		155 => "expertise_spirit",
		156 => "expertise_dodge",
		157 => "expertise_parry",
		158 => "expertise_hit",
		159 => "expertise_crit",
		160 => "expertise_haste",
		161 => "expertise_mastery",
		162 => "mastery_spirit",
		163 => "mastery_dodge",
		164 => "mastery_parry",
		165 => "mastery_hit",
		166 => "mastery_crit",
		167 => "mastery_haste",
		168 => "mastery_expertise"
	);
	
	private $reforge = NULL;
	private $id = NULL;
	
	public function __construct( $id ) {
		if (!array_key_exists( $id, self::$reforges )) {
			throw new \Exception("Reforge not found");	
		}
		$this->id = $id;
		$this->reforge=self::$reforges[$id];
		
	}
	
	public function __toString() {
		return (string) $this->reforge;	
	}

}