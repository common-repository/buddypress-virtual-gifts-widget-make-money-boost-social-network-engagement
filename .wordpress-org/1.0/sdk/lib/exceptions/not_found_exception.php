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
 * @package    FeeligoNotFoundException
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */

class FeeligoNotFoundException extends Exception {
  
  public function __construct($type, $msg) {
    $this->_type = $type;
    $this->_message = $msg;
  }
  
  public function type() {
    return $this->_type;
  }
  
  public function message () {
    return $this->_message;
  }
  
  public function as_json () {
    return array(
      $this->_type => $this->_message
    );
  }
  
}