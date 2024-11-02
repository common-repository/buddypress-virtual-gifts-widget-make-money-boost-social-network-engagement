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
 * @package    FeeligoUserPresenter
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */

require_once(str_replace('//','/',dirname(__FILE__).'/').'model.php');

/**
 * presenter class used for a single User
 */
 
class FeeligoUserPresenter extends FeeligoModelPresenter {
  
  public function __construct($item) {
    parent::__construct($item);
  }
  
  protected function path() {
    return 'users/'.$this->item()->id();
  }

  public function as_json() {
    return array_merge(parent::as_json(), array(
      'name' => $this->item()->name(),
      'link' => $this->item()->link(),
      'picture_url' => $this->item()->picture_url()
    ));
  }
}