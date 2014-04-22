<?php

namespace SimWeb\Model\Common;

class RestAPI {
	
	private $curl = NULL;
	
	private function init() {
		if ($this->curl === NULL)
			$this->curl = curl_init();	
		return $this->curl;
	}
	
	private function setCurlOpts( $opts = NULL ) {
		$defaults = array(
			CURLOPT_SSL_VERIFYPEER => FALSE,
			CURLOPT_URL => "http://google.com"
		);
		foreach( $opts as $k=>$v ) $defaults[$k] = $v;
		$defaults[ CURLOPT_HEADER ] = 1;
		$defaults[CURLOPT_RETURNTRANSFER] = TRUE;
		curl_setopt_array( $this->curl, $defaults );
	}
	
	private function execute( ) {
		if ($this->curl === NULL) throw new RestAPIError();
		$ch = $this->curl;
		$data = curl_exec( $ch );
		if (curl_error($ch)) {
			throw new RestAPIError( curl_error($ch) );	
		}
		
		$size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
		$header = substr( $data, 0, $size );
		$body = substr( $data, $size );
		
		$headers = explode("\r\n", $header);
		
		return array($this->understandHeaders($headers), $body);
	}
	
	private function close() {
		curl_close( $this->curl );
		$this->curl = NULL;	
	}
	
	private function understandHeaders( $headers ) {
		
		//get the status code and remove it from the regularly formatted headers
		$status = $headers[0];
		unset($headers[0]);
		//now iterate through teh array creating a new one of key value pairs
		$array = array();
		$array['Status'] = $status;
		
		preg_match( "#^HTTP.?/(?P<HTTPVERSION>\d[.]\d) *(?P<STATUSCODE>\d+)#i", $status, $matches );
		//get status code out of here
		if ($matches['STATUSCODE']) $array['Status-Code'] = $matches['STATUSCODE']; else $array['Status-Code'] = 200;
		if ($matches['HTTPVERSION']) $array['HTTP-Version'] = $matches['HTTPVERSION'];
		
		foreach( $headers as $header ) {
			$h = explode(":", $header, 2);
			if (count($h) < 2) {
				//invalid header?
			} else $array[trim($h[0])] = trim($h[1]);
		}
		
		return $array;
		
	}
	
	protected function RESTGet( $URL ) {
		$this->init();
		$this->setCurlOpts(array(
			CURLOPT_URL => $URL
		));
		//let extensions handle exception handling
		list($headers, $data) = $this->execute();
		if ($headers['Status-Code'] == 404) {
			throw new RestAPIError('404 for URL ', 404);	
		}
			
		$this->close();
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
	
	protected function Headers() {
		
		
			
	}
	
	
}

class RestAPIError extends \Exception {}