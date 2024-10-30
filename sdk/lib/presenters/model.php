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
 * @package    FeeligoModelPresenter
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */

require_once(str_replace('//','/',dirname(__FILE__).'/').'base.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'../helpers/url.php');

/**
 * presenter class used to present a single object
 */
 
abstract class FeeligoModelPresenter extends FeeligoBasePresenter {

  /**
   * override this function to specify the entity's path on the API
   */
  protected abstract function path();

  /**
   * default options for as_json()
   */
  public function as_json() {
    return array_merge(parent::as_json(), array(
      'id' => $this->item()->id(),
      'url' => $this->url()
    ));
  }
  
  /**
   * returns the URL for this model, based on its path
   */
  protected function url() {
    if (!isset($this->_url_helper) || $this->_url_helper === null) $this->_url_helper = new FeeligoHelperUrl();
    return $this->_url_helper->url_for($this->path());
  }

}