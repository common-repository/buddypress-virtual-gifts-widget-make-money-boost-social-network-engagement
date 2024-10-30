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
 * @package    FeeligoControllerRequest
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */

require_once(str_replace('//','/',dirname(__FILE__).'/').'../Zend/Controller/Request/Http.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'../helpers/url.php');
 
class FeeligoControllerRequest extends Feeligo_Zend_Controller_Request_Http {

  public function __construct () {
    parent::__construct();
  }
  
  /**
   * Get current full URL
   */
  public function full_uri() {
    return $this->getScheme() . '://' . $this->getHttpHost() . $this->getRequestUri();
  }
  
  /**
   * HTTP method
   */
   
  /**
   * get the HTTP method used for this request
   * 
   * returns REQUEST_METHOD, overridden by param 'method'
   * if present, for JSONP compatibility
   */ 
  public function method() {
    return strtoupper($this->param('method', parent::getMethod()));
  }
  
  public function method_is($method) {
    return $this->method() == strtoupper($method);
  } 
  
  public function is_get() {
    return $this->method_is('GET');
  }
  
  public function is_post() {
    return $this->method_is('POST');
  }
  
  public function is_put() {
    return $this->method_is('PUT');
  }
  
  public function is_delete() {
    return $this->method_is("DELETE");
  }
  
  /**
   * Accessor for the params
   */
  public function param($name, $default_val = null) {
    return $this->getParam($name, $default_val);
  }
  
  public function params() {
    return $this->getParams();
  }
  
  function format() {
    $format = $this->_valid_format($this->param('f', $this->_format()));
    return $format !== null ? $format : 'json';
  }
  
  protected function _format() {
    $parts = $this->_split_path();
    return $parts[1];
  }
  
  protected function _valid_format($format) {
    switch (strtolower($format)) {
      case 'xml': return 'xml';
      case 'json' : return 'json';
      case 'jsonp' : return 'jsonp';
      default: return null;
    }
  }
  
  protected function _path() {
    $parts = $this->_split_path();
    return $parts[0];
  }
  
  protected function _split_path() {
    $path = $this->param('path');
    if ($path !== null) {
      $parts = explode('.', $path);
      if (sizeof($parts) == 1) {
        return array($parts[0], null);
      }else{
        return $parts;
      }
    }
    return array(null, null);
  }
  
  /**
   * Accessor for URL parts
   */
  public function url($i = null) {
     if ($i === null) { return $this->_path(); }
     return $this->_url_part($i);
   }

   private function _url_part($i) {
     if (sizeof($parts = $this->_url_parts()) > $i) {
       return $parts[$i];
     }
     return null;
   }

   private function _url_parts() {
     return $this->_extract_url_parts($this->url());
   }
   
   private function _extract_url_parts($url) {
     if (($parts = explode('/', $url)) !== false) {
       return $parts;
     }
     return array();
   }
}