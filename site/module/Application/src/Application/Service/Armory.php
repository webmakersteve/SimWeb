<?php

namespace Application\Service;

use Application\Service\RestAPI;

class Armory extends RestAPI {
	
	const PATH = 'api/wow';
	const HOST = 'battle.net';
	
	protected function validateArmoryURL( $URL ) {
		if (!filter_var($URL, FILTER_VALIDATE_URL)) return false;
		$p = parse_url($URL);
		
		if (!strstr($p['host'], 'battle.net')) return false;
		
		if (!preg_match("#^/wow/[a-z_A-Z]{2,5}/character/#", $p['path'])) return false;
		
		//lastly get headers
		$headers = get_headers( $URL );
		
		if (!strstr($headers[0], "200 OK")) return false;
		return true;	
	}
	
	public function Get( $URL ) {
		if (!$this->validateArmoryURL( $URL )) throw new ArmoryError("Invalid armory URL", ArmoryError::INVALID_URL);
		$data = $this->parseArmoryURL( $URL );
		
		return $this->dispatch( $URL, $data );
		
	}
	
	
	protected function dispatch( $URL, $data ) {
		
		if ($data['name']) return $this->characterRequest( $data );
	}
	
	protected function characterRequest( $Opts ) {
		$url = sprintf("https://%s.%s/%s/character/%s/%s?fields=items,professions,talents", $Opts['region'], self::HOST, self::PATH, $Opts['realm'], $Opts['name'] ); 
		
		$data = $this->RESTGet( $url );
		//now we need to parse it properly for an exchange
		
		$arr = array(
			'Name' => $data->name,
			'Realm' => $data->realm,
			'Class' => $data->class,
			'Race' => $data->race,
			'Gender' => $data->gender,
			'Level' => $data->level,
			'Thumb' => sprintf('http://%s.%s/static-render/%1$s/%s', $Opts['region'], self::HOST, $data->thumbnail ),
			'Region' => $Opts['region']
		);
		
				//do items if it is set
		if ($data->items) {
			$x = &$arr['Items'];
			$arr['iLevel'] = $data->items->averageItemLevelEquipped;
			
			foreach( $data->items as $k => $v ) {
				if (stristr($k, "itemlevel")) continue;
				//otherwise
				$me = $data->items->$k;
				
				if (!isset($me->id)) continue;
				
				$x[$k] = array(
					'Type' => $k,
					'ID' => $me->id,
					'Name' => $me->name,
					'Icon' => sprintf("http://us.media.blizzard.com/wow/icons/56/%s.jpg", $me->icon),
					'Quality' => $me->quality,
					'iLevel' => $me->itemLevel,
					'Mods' => $me->tooltipParams,
					'Stats' => $me->stats,
					'Armor' => $me->armor
				);
			}
			
		}
		
		if ($data->professions) {
			
			//i guess we only care about primary professions?
			$profs = $data->professions->primary;
			
			$x = &$arr['Professions'];
			
			foreach( $profs as $v ) {
				$x[] = array(
					'Skill' => $v->rank,
					'ID' => $v->id,
					'Name' => $v->name
				);
			}
			
				
		}
		
		if ($data->talents) {
			
			$Spec = $data->talents;
			
			//array of talents usually 0 or 1 i guess. Maybe check if array first?
			if (is_array( $Spec )) {
				//now we can iterate through it
				foreach( $Spec as $talentData ) {
					
					if (isset($talentData->selected) && $talentData->selected == 1) {
						$crtSpec = &$arr['Talents']['Active'];
					} else $crtSpec = &$arr['Talents']['Inactive'];
					
					if (!isset($talentData->spec->name)) {
					
						$crtSpec = NULL;
						continue;
						
					}
					
					$crtSpec['Name'] = $talentData->spec->name;
					$crtSpec['Icon'] = sprintf("http://us.media.blizzard.com/wow/icons/56/%s.jpg", $talentData->spec->icon);
					
					foreach( $talentData->talents as $talent ) {
						//this is point based talents
						$crtSpec['Talents'][$talent->tier] = array(
							'Value' => $talent->column,
							'Name' => $talent->spell->name
						);
					}
					
					//glyph time
					
					foreach( $talentData->glyphs as $GlyphData ) {
						//throw new NotYetImplementedException
					}
					
					//spec
						
				}
			}
				
		}
		
		return $arr;
		
	}
	
	const PARSING_REGEX = "#^/wow/(?P<lang>[a-z_A-Z]{2,5})/character/(?P<realm>\b[^/]+\b)/(?P<name>\b[^/]+\b)/#";
	
	protected function parseArmoryURL( $URL ) {
		
		$url = parse_url( $URL );	
		
		$country = substr($url['host'], 0, strpos($url['host'], '.' ));
		
		if (preg_match( self::PARSING_REGEX, $url['path'], $matches )) {
			return array(
				'name' => $matches['name'],
				'realm' => $matches['realm'],
				'lang' => $matches['lang'],
				'region' => $country
			);
		} else return explode("/", $URL);
		
	}
	
}

class ArmoryError extends \Exception {
	const INVALID_URL = 2;	
}