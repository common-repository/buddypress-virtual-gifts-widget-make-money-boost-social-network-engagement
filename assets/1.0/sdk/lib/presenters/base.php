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
 * @package    FeeligoBasePresenter
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */

/**
 * base presenter class
 */
 
class FeeligoBasePresenter {
  
  public function __construct($item) {
    $this->_item = $item;
  }
  
  public function item() {
    return $this->_item;
  }
  
  public function as_json() {
    return array();
  }

}