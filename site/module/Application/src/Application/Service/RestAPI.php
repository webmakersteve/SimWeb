<?php

namespace Application\Service;

class RestAPI {
	
	private $curl = NULL;
	
	private function init() {
		if ($this->curl === NULL)
			$this->curl = curl_init();	
		return $this->curl;
	}
	
	private function setCurlOpts( $opts = NULL ) {
		$defaults = array(
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_URL => "http://google.com"
		);
		foreach( $opts as $k=>$v ) $defaults[$k] = $v;
		curl_setopt_array( $this->curl, $defaults );
	}
	
	private function execute( ) {
		if ($this->curl === NULL) throw new RestAPIError();
		$ch = $this->curl;
		$data = curl_exec( $ch );
		if (curl_error($ch)) {
			throw new RestAPIError( curl_error($ch) );	
		}
		return $data;
	}
	
	protected function close() {
		curl_close( $this->curl );
		$this->curl = NULL;	
	}
	
	protected function RESTGet( $URL ) {
		$this->init();
		$this->setCurlOpts(array(
			CURLOPT_URL => $URL
		));
		try {
			$data = $this->execute();
			$this->close();
		} catch (RestAPIError $e) {
			echo $e->getMessage();
		}
		
		return $this->format($data);
			
	}
	
	private function format( $data ) {
		//check if it is json
		if (preg_match("#^{\"#", $data)) {
			//json	
			return json_decode($data);
		} elseif (preg_match("#^ *<?xml#i", $data )) {
			//xml
			return $this->XMLFormatter($data);
		} else return $data;
	}
	
	
}

class RestAPIError extends \Exception {}