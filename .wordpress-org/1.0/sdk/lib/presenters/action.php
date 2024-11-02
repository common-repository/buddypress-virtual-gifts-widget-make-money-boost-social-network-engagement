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
 * @package    FeeligoActionPresenter
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */

require_once(str_replace('//','/',dirname(__FILE__).'/').'model.php');

/**
 * presenter class used for a single Action
 */
 
class FeeligoActionPresenter extends FeeligoModelPresenter {
  
  public function __construct($item, $path = 'action') {
    parent::__construct($item, $path);
  }
  
  protected function path() {
    return 'actions/'.$this->item()->id();
  }

  public function as_json() {
    return array_merge(parent::as_json(), array(
    ));
  }
}