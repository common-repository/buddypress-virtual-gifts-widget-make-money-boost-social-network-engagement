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
require_once(str_replace('//','/',dirname(__FILE__).'/').'factory.php');

/**
 * presenter class used to present a collection of objects
 */
 
class FeeligoCollectionPresenter extends FeeligoBasePresenter {
  
  /** 
   * override this method to specify a Presenter class
   * to use for every single model of the collection
   *
   * @return FeeligoModelPresenter
   */
  private function present($model) {
    return FeeligoPresenterFactory::present($model);
  }

  /**
   * the JSON representation of a Collection is a plain array
   * containing the JSON representations of all models of the collection
   *
   * @return array
   */
  public function as_json() {
    $json = array();
    foreach($this->item() as $model) {
      $json[] = $this->present($model)->as_json();
    }
    return $json;
  }

}