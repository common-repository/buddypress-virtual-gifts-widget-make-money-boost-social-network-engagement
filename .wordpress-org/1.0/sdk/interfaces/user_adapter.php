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
 * @package    FeeligoUserAdapter
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */

/**
 * interface of the User adapter class
 */
 
interface FeeligoUserAdapter {
  
  /**
   * returns the unique identifier of the user
   *
   * @return string
   */
  public function id();
  
  /**
   * the user's display name
   *
   * human-readable name which is shown to other users
   *
   * @return string
   */
  public function name();
  
  /**
   * the URL of the user's profile page (full URL, not only the path)
   *
   * @return string
   */
  public function link();
  
  /**
   * the URL of the user's profile picture
   *
   * @return string
   */
  public function picture_url();
  
  /**
   * get a selector for the user's friends
   *
   * @return FeeligoUsersSelector
   */
  public function friends_selector();

}