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
 * @package    FeeligoActionsSelector
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
 
require_once(str_replace('//','/',dirname(__FILE__).'/').'../lib/exceptions/not_found_exception.php');
 
/**
 * interface of the Actions Selector class
 */ 
 
interface FeeligoActionsSelector {
  
  /**
   * creates a new action
   *
   * @param array $data the data used to create the action
   * @return FeeligoActionAdapter
   */
  public function create($data);
  
}