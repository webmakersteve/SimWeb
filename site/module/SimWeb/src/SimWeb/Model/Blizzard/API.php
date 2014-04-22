<?php

/**
 * Acts as a dispatcher or service locator. Used as a service locator.
 */

namespace SimWeb\Model\Blizzard;

use SimWeb\Model\Common\RestAPI;
use SimWeb\Model\Common\Cacheable;

use Zend\Cache\StorageFactory;

class API extends RestAPI {
	
	const PATH = 'api/wow';
	const HOST = '%s.battle.net';
	
	private $Region = 'us';
	
	public function setRegion( $Region ) {
		$this->Region = $Region;
	}
	
	/** The rest of this stuff is a bit more fun */
	
	public function getCharacter( $RealmOrLink, $Name=NULL ) {
		if ($Name === NULL) {
			//this means realm specifies an armory link	
			return $this->getFromArmoryURL( $RealmOrLink );
		}
		
		$data = array(
			'realm' => $RealmOrLink,
			'name' => $Name,
			'type' => 'profile',
			'cache-expires' => 86400
		);
		return $this->dispatch($data);
		
	}
	
	public function getRealms($Realms=FALSE) {
		//this one is cacheable
		$data = array(
			'type' => 'realms',
			'cache-expires' => -1, //never uncache
			'cache-key'	=> 'realm-list'
		);
		
		if ($Realms && is_array($Realms)) $data['realms'] = $Realms;
		return $this->dispatch($data);
		
	}
	
	protected function dispatch( $data ) {
		$cache = $this->getCache();
		if (!$data['type']) throw new ArmoryError();
		//first check cache expiry
		if (isset($data['cache-key']) && isset($data['cache-expires'])) {
			$cached = $cache->getItem( $data['cache-key'], $success);
			if ($success) {
				$realms = unserialize($cached);
				if ($realms instanceof Cacheable) {
					if (!$realms->isExpired($data['cache-expires'])) return $realms; //check if it is expired
				} else return $realms;
			}
		} //not loading this from cache
		
		switch (strtolower($data['type'])) {
			case 'profile':
				$r = $this->characterRequest( $data );
				break;
			case 'realms':
				$r = $this->realmRequest( $data );
				break;
			default:
				throw new ArmoryError();
				
		}
		
		if (isset($data['cache-key'])) {
			if ($r instanceof Cacheable) $cache->setItem($data['cache-key'], $r->__toCache());
			     					else $cache->setItem($data['cache-key'], serialize($r));
		}
		
		return $r;
	}
	
	protected function realmRequest( $Opts ) {
		if (isset($Opts['realms'])) {
				
			$suffix = "?realms=";
			foreach( $Opts['realms'] as $realm ) {
				$suffix .= $realm.",";
			}
			$suffix = rtrim($suffix, ",");
		} else $suffix = "";
		
		$url = sprintf("https://%s/%s/realm/status",	
			sprintf(self::HOST, $this->Region),
			self::PATH,
			$suffix);
		
		try {
			$data = $this->RESTGet( $url );
			
			$arr = array();
			foreach( $data->realms as $realm ) {
				$arr[] = array(
					'type' => $realm->type,
					'pop' => $realm->population,
					'status' => $realm->status,
					'name' => $realm->name,
					'slug' => $realm->slug,
					'locale' => $realm->locale,
					'timezone' => $realm->timezone);
			}
			
			$r = new Data\Realm\Collection();
			$r->Exchange($arr);
			return $r;
			
		} catch (\Exception $e) {
				
		}
	}
	
	protected function characterRequest( $Opts ) {
		$url = sprintf("https://%s/%s/character/%s/%s?fields=items,professions,talents",
			sprintf(self::HOST, $this->Region),
			self::PATH,
			$this->n($Opts['realm']),
			$this->n($Opts['name'])); 
			
		try {
			$data = $this->RESTGet( $url );
		//now we need to parse it properly for an exchange
		
			$arr = array(
				'Name' => $data->name,
				'Realm' => $data->realm,
				'Class' => $data->class,
				'Race' => $data->race,
				'Gender' => $data->gender,
				'Level' => $data->level,
				'Thumb' => sprintf('http://%s/static-render/%s/%s', sprintf(self::HOST, $this->Region), $this->Region, $data->thumbnail ),
				'Region' => $this->Region
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
			$r = new Data\Character\Character();
			$r->Exchange($arr);
			return $r;
			
		} catch (\Exception $e) {
			//this will be errors fetching the data	
			throw $e;
		}
		
	}
	
	private function n( $str ) {
		return str_replace(" ", "-", preg_replace("#['\"!]#", "", strtolower($str) ) );
	}
	
	private function getCache() {
		$cache = StorageFactory::factory(array(
			'adapter'	=>	array(
				'name'		=> 'filesystem',
				'options'	=> array(
					'cache_dir'	=>	'data/cache'
				),
			),
		));
		return $cache;	
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
	
	protected function getFromArmoryURL( $URL ) {
		
		if (!$this->validateArmoryURL( $URL )) throw new ArmoryError("Invalid armory URL", ArmoryError::INVALID_URL);
		$data = $this->parseArmoryURL( $URL );
		
		return $this->dispatch( $data );
		
	}
	
}

class ArmoryError extends \Exception {
	const INVALID_URL = 2;	
}