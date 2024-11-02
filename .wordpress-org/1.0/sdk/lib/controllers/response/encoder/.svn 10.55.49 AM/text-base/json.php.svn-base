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
 * @package    FeeligoControllerResponseEncoderJson
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
 
/**
 * Encodes a $data variable (string|null|array) to a JSON string
 */
 
require_once(str_replace('//','/',dirname(__FILE__).'/').'../encoder.php');
 
class FeeligoControllerResponseEncoderJson implements FeeligoControllerResponseEncoder {

  public function encode($data) {
    return json_encode($data);
  }
  
  public function content_type() {
    return 'application/json';
  }
  
}