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
 * @package    FeeligoActionAdapter
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
 
/**
 * interface of the Action adapter class
 */ 
 
interface FeeligoActionAdapter {
  
  /**
   * returns the unique identifier of the action
   *
   * @return string
   */
  public function id();
  
}