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
 * @package    FeeligoAdapterInterface
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
 
require_once(str_replace('//','/',dirname(__FILE__).'/').'api/auth.php'); 

abstract class FeeligoApi {
  
  const __community_api_key = FLG__community_api_key;
  const __community_secret  = FLG__community_secret;
  const __remote_server_url = FLG__server_url;
  
  /**
   * Accessors for Feeligo params
   */
  public function community_api_key() { 
    return self::__community_api_key;
  }
  public function community_secret() {
    return self::__community_secret;
  }

  public function remote_server_url() {
    return self::__remote_server_url ? self::__remote_server_url : "http://feeligo.com/";
  }
  public function remote_api_endpoint_url() {
    return self::remote_server_url().'c/'.self::community_api_key().'/api/';
  }
  
  /**
   * Accessors for Model Adapters
   */
  public abstract function viewer();
  
  public abstract function subject();
  
  public abstract function users();
  
  public abstract function actions();
  
  /**
   * tells whether the viewer and subject are defined
   */
  public abstract function has_viewer();
  public abstract function has_subject();
  
  
  /**
   * Accessor for Auth object
   */
  public function auth() {
    return $this->_auth;
  }
  
  /**
   * The singleton Api object
   *
   * @var FeeligoApi
   */
  protected static $_instance;

  /**
   * Get or create the current api instance
   *
   * @return FeeligoApi
   */
  public static function _() {
    if( is_null(self::$_instance) ) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }
  
  /**
   * protected constructor prevents instantiation from outside
   */
  protected function __construct() {
    $this->_auth = new FeeligoApiAuth($this);
  }
      
}