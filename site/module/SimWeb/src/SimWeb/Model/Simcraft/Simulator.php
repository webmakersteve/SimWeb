<?php

namespace SimWeb\Model\Simcraft;

use	Zend\ServiceManager\InitializerInterface,
	Zend\ServiceManager\ServiceLocatorInterface;
	
	
use SimWeb\Model\Character\Character,
    SimWeb\Model\Simcraft\Config as SimConfig;

class Simulator {
	
	protected $config;
	
	public function __construct( $config ) {
		$this->config = $config['simcraft'] ? $config['simcraft'] : array();	
	}
	
	private function getConfig() {return $this->config;}
	
	protected $data = array(
		'Player' => NULL,
		'Stats' => NULL
	);
	
	const REGEX_HEADERS = '#[A-Za-z-]{2,}[:=][ \t]?#';
	
	protected function getArrayFromXMLDom( $input, $attribute ) {
		$array = array();
		
		foreach( $input as $value ) {
			if ((string) $value[$attribute])
			$array[] = (string) $value[$attribute];	
			else $array[] = (string) $value['@attributes'][$attribute];
		}
		return $array;
	}
	
	protected function getAssocArrayFromRepeatedAttribute( $input, $items, $key = NULL, $value = NULL) {
		$array = array();
		foreach ($input->$items as $item ) {
			if ($key === NULL) $k = (string) $item;
			else $k = $item[$key] ? (string) $item[$key] : (string) $item['@attributes'][$key];
			if ($value === NULL) $v = (string) $item;
			else $v = $item[$value] ? (string) $item[$value] : (string) $item['@attributes'][$value];
			$array[$k] = $v;
		}
		return $array;
	}
	
	protected function getArrayFromAttributes( \SimpleXMLElement $input ) {
		$array = array();
		foreach( $input->attributes() as $k => $v ) {
			$array[$k] = (string) $v;
		}
		return $array;
	}
	
	protected function interpret( $Doc ) {
		
		$player = $Doc->players->player[0];
		
		$data = array(
			'Player' => array(
				'Name' => (string) $player['name'],
				'Spec' => (string) $player->specialization,
				'Stats' => $this->getAssocArrayFromRepeatedAttribute( $player, 'attribute', 'name', 'buffed'),
				'Glyphs' => $this->getArrayFromXMLDom( $player->glyphs->glyph, 'name' ),
				'DPS' => $this->getArrayFromAttributes( $player->dps )
			),
			'Simulation' => array(
				'Rotation' => $this->getArrayFromXMLDom( $player->priorities->action, 'value' ),
				'Actions' => NULL
			)
		);
		$s = new Simulation();
		$s->importSimulationArray( $data );
		return $s;
		
	}
	
	const SALT = "FIREEXITGUILDMSCFOREVER";
	
	protected function generateUniqueFilename($x) {
		return md5($x->generateText());
	}
	
	public function simulateCharacter( SimConfig $SimConf ) {
		//make filename
		
		$filename = sprintf("data/tmp/sim/%s.xml", $this->generateUniqueFilename($SimConf));
		//check if file exists already because then we don't need to duplicate the request
		if (is_readable($filename)) {
			//woohoo
			
		} else {
			//region,realm,name
			$config = $this->getConfig();
			
			$str = implode(" ", explode("\n", $SimConf->generateText()));
			
			$x = sprintf("%s xml=%s %s", $config['path'], $filename, $str );
			$results = shell_exec( $x );
		}
		
		$XMLResults = new \SimpleXMLElement( $filename, null, true );
		
		
		//echo "<pre>" . print_r(explode("\n", $results), true) . "</pre>";
		return $this->interpret( $XMLResults );
		
	}
	
}
