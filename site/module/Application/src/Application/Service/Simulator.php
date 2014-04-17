<?php

namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface,
	Zend\ServiceManager\ServiceLocatorInterface;
	
	
use Application\Model\Character\Character;

function logr( $in, $die=false ) {
	printf("<pre>%s</pre>", print_r( $in, true ));	
	if ($die) exit;
}

class Simulator {
	
	const Exe = 'C:\Users\Stephen\OneDrive\simc-530-6-win32\simc-530-6-win32\simc.exe';
	
	protected function instantiate() {
			
	}
	
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
		
		return $data;
		
	}
	
	const SALT = "FIREEXITGUILDMSCFOREVER";
	
	protected function generateUniqueFilename() {
		return md5(uniqid() . sha1(self::SALT));
	}
	
	public function simulateCharacter( Character $Char ) {
		//make filename
		$filename = sprintf("%s.xml", $this->generateUniqueFilename());
		//region,realm,name
		$x = sprintf("%s armory=%s,%s,%s xml=%s", self::Exe, $Char->getRegion(true), $Char->getRealm(true), $Char->getName(true), $filename );
		$results = shell_exec( $x );
		
		$XMLResults = new \SimpleXMLElement( $filename, null, true );
		
		
		//echo "<pre>" . print_r(explode("\n", $results), true) . "</pre>";
		return $this->interpret( $XMLResults );
		
	}
	
}