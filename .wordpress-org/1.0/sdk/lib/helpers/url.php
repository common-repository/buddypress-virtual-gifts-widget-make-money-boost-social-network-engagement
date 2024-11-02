<?php
/**
 * Feeligo
 *
 * @category   Feeligo
 * @package    API Connector SDK for PHP
 * @copyright  Copyright 2012 Feeligo
 * @license    
 * @author     Davide Bonapersona <tech@feeligo.com>
 */

/**
 * @category   Feeligo
 * @package    FeeligoHelperUrl
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */

require_once(str_replace('//','/',dirname(__FILE__).'/').'../controllers/request.php'); 
require_once(str_replace('//','/',dirname(__FILE__).'/').'../Zend/Uri/Http.php');

class FeeligoHelperUrl {
  
  public function __construct() {
    $this->_request = new FeeligoControllerRequest();
  }
  
  public function request() {
    return $this->_request;
  }
  
  public function url_for($new_path_or_params = null, $new_params = null) {
    $req = $this->request();
    $uri = Feeligo_Zend_Uri_Http::fromString($req->getScheme() . '://' . $req->getHttpHost() . $req->getRequestUri());
    $uri->setQuery(array_merge(
      $new_path_or_params !== null && is_array($new_path_or_params) ? $new_path_or_params : array(),
      $new_params !== null ? $new_params : array(),
      $new_path_or_params !== null && is_string($new_path_or_params) ? array('path' => $new_path_or_params) : array()
    ));
    return $uri->getUri();
  }
  
  
  private function _pluralize($str) {
    if ($str[strlen($str)-1] !== 's') {
      return $str . 's';
    }
    return $str;
  }
    
}