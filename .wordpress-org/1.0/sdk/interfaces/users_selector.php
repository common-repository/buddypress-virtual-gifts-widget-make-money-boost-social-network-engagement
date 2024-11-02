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
 * @package    FeeligoUsersSelector
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
 
require_once(str_replace('//','/',dirname(__FILE__).'/').'../lib/exceptions/not_found_exception.php'); 
 
/**
 * interface of the Users Selector class
 */ 
 
interface FeeligoUsersSelector {
  
  /**
   * returns an array containing all the Users
   *
   * @param int $limit argument for the SQL LIMIT clause
   * @param int $offset argument for the SQL OFFSET clause
   * @return FeeligoUserAdapter array
   */
  public function all($limit = null, $offset = 0);
 
 
  /**
   * finds a specific User by its id
   *
   * @param mixed $id argument for the SQL id='$id' condition
   * @return FeeligoUserAdapter
   */
  public function find($id, $throw = true);
 
 
  /**
   * finds a list of Users by their id's
   *
   * @param mixed array $ids
   * @return FeeligoUserAdapter[] array
   */
  public function find_all($ids);  
    
    
  /**
   * returns an array containing all the Users which name matches the query
   *
   * @param string $query the search query, argument to a SQL LIKE '%$query%' clause
   * @param int $limit argument for the SQL LIMIT clause
   * @param int $offset argument for the SQL OFFSET clause
   * @return FeeligoUserAdapter[] array
   */  
  public function search($query, $limit = null, $offset = 0);
  
}