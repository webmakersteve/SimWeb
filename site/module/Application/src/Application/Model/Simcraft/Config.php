<?php

namespace Application\Model\Simcraft;

use Application\Model\Character\Character;

class Config {
	
	protected $character;
	
	public function importCharacter( Character $c ) {
		$this->character = $c;
	}
		
	public function generateText() {
		
		$return = array();
		$return[] = $this->kp($this->character->getClass(), $this->character->getName(true) );
		$return[] = $this->kp( "origin", $this->character->getLink() );
		$return[] = $this->kp( "level", $this->character->getLevel() );
		$return[] = $this->kp( "race", $this->character->getRace() );
		$return[] = $this->kp( "spec", $this->character->getActiveSpec()->Name() ); //need to get?
		if ($x = $this->character->getSimProfsStr())						$return[] = $this->kp( "professions", 	$x ); //need to get
		if ($x = $this->character->getActiveSpec()->choicesToString())		$return[] = $this->kp( "talents",	 	$x ); //need to get?
		if ($x = $this->character->getActiveSpec()->majorGlyphsToString()) 	$return[] = $this->kp( "glyphs", 		$x ); //need to extrapolate
		
		$return[] = ""; //new line
		
		foreach( $this->character->getEquipment() as $piece => $data ) {
			$return[] = $this->kp($piece, sprintf("%s,id=%d,stats=%s,gems=%s,enchant=%s,reforge=%s",
				$this->n($data->Name()),
				$data->ID(),
				$data->getSimStatsStr(), 
				$data->getSimGemStr(),
				$data->getSimEnchantStr(),
				$data->getSimReforgeStr()
			));
		};
		
		return implode( "\n", $return );
		
	}
	
	private function n( $str ) {
		return str_replace(" ", "_", preg_replace("#['\"!]#", "", $str ) );
	}
	
	private function kp( $str1, $str2 ) {
		$str1 = strtolower($str1);
		$str2 = strtolower($str2);
		return sprintf( "%s=%s", $str1, $str2 );	
	}
		
}

/*

warlock=chaositi
origin="http://www.askmrrobot.com/wow/gear/usa/wyrmrest_accord/chaositi"
level=90
race=human
role=spell
spec=destruction
professions=Enchanting=156/Tailoring=153
talents=231212

legs=leggings_of_the_horned_nightmare,id=99098,stats=2417armor_2081int_3362sta_1502crit_1267mastery,gems=320mastery_320mastery_120int,enchant=285int_165crit
shoulders=cloudscorcher_shoulderpads_of_the_unerring,id=101817,stats=1711armor_909int_1363sta_898hit,enchant=200int_100crit,reforge=hit_mastery
wrists=avools_ancestral_bracers,id=105093,stats=1102armor_918int_1377sta_621haste_597mastery,enchant=180int,reforge=haste_hit
back=drape_of_the_omega,id=105829,stats=1303armor_1027int_1541sta_651haste_705mastery,enchant=180int,reforge=haste_crit
off_hand=festering_primordial_globule,id=104858,stats=947int_1541sta_544hit_704haste,gems=160exp_160mastery_60int,enchant=165int,reforge=haste_mastery
feet=boneinlaid_sandals,id=104746,stats=1792armor_1289int_2054sta_878haste_858mastery,gems=320mastery_60hit,enchant=140mastery,reforge=haste_crit
chest=robes_of_the_horned_nightmare,id=99570,stats=2636armor_1673int_2870sta_1061crit_1212mastery,gems=160exp_160mastery_160exp_160mastery_160exp_160mastery_180int,enchant=80all
main_hand=ordon_sacrificial_dagger,id=105926,stats=491int_737sta_264crit_6564sp_365mastery,enchant=jade_spirit,reforge=crit_hit,weapon=dagger_1.8speed_1955min_3633max
waist=miasmic_skullbelt,id=104822,stats=1466armor_1209int_2054sta_912hit_699crit,gems=160exp_160mastery_320mastery_320mastery_120int,reforge=hit_mastery
head=flameslingers_fiery_cowl,id=104997,stats=2046armor_1408int_2472sta_715hit_1133haste,gems=burning_primal_320mastery_180int,reforge=haste_mastery
hands=blight_hurlers,id=104964,stats=1574armor_1064int_1837sta_620hit_812crit,gems=160exp_160mastery_160hit_160mastery_120int,enchant=170mastery,reforge=crit_mastery
finger2=petrified_pennyroyal_ring,id=104696,stats=1027int_1541sta_735hit_601crit,reforge=hit_mastery
neck=shaseeker_collar_of_the_fireflash,id=98200,stats=821int_1231sta_547crit_547haste,reforge=haste_mastery
finger1=felsoul_band_of_destruction,id=101264,stats=402haste_603int_402mastery_906sta,reforge=haste_hit
trinket2=yulons_bite,id=103687,stats=1152int
trinket1=purified_bindings_of_immerseus,id=104675,upgrade=2

default_pet=felhunter*/
