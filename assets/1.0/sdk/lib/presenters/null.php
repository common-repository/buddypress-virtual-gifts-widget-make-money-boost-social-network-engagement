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
 * @package    FeeligoNullPresenter
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */

require_once(str_replace('//','/',dirname(__FILE__).'/').'model.php');

/**
 * presenter class used for null values (for consistency)
 */
 
class FeeligoNullPresenter extends FeeligoModelPresenter {
  
  public function __construct($item = null) {
  }
  
  protected function path() {
    return '';
  }

  public function as_json() {
    return null;
  }
}