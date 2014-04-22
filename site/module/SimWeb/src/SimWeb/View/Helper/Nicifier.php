<?php

namespace SimWeb\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Nicifier extends AbstractHelper {
    public function __invoke( $str ) {
		return strtolower(str_replace(" ", "-", preg_replace("#['\"!]#", "", $str ) ));
    }
	
	
}