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
 * @package    FeeligoControllerResponseEncoderJsonp
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
 
/**
 * Encodes a $data variable (string|null|array) to JSONP format
 * (JSON string inside JS function call)
 */
 
require_once(str_replace('//','/',dirname(__FILE__).'/').'../encoder.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'json.php');
 
class FeeligoControllerResponseEncoderJsonp extends FeeligoControllerResponseEncoderJson implements FeeligoControllerResponseEncoder {
  
  public function __construct($callback) {
    $this->_callback = $callback;
  }

  public function encode($data) {
    return $this->_callback.'('.parent::encode($data).');';
  }
  
  public function content_type() {
    return 'text/javascript';
  }
  
}