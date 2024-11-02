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
 * @package    FeeligoAuth
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
 
require_once(str_replace('//','/',dirname(__FILE__).'/').'auth/token.php'); 

class FeeligoApiAuth {
  
  public function __construct ($api) {
    $this->_api = $api;
    $this->_time = time();
  }
  
  public function api() {
    return $this->_api;
  }
  
  protected function api_key() {
    return $this->api()->community_api_key();
  }
  
  protected function secret() {
    return $this->api()->community_secret();
  }
  
  public function time() {
    return $this->_time;
  }
  
  /**
   * user token to access the remote API
   */
  function remote_api_user_token($user_adapter_or_id, $time = null) {
    if ($time === null) { $time = $this->time(); }
    $user_id = (string) (is_string($user_adapter_or_id) || is_int($user_adapter_or_id) ? $user_adapter_or_id : $user_adapter_or_id->id());
    return sha1("user:".$this->_api->community_secret().":".$user_id.":".(intval($this->time()/100)*100));
  }
  
  /**
   * Get a token for a given user on this community
   * - permissions can be added
   * - basic permissions are added by default
   */
  public function community_api_user_token($user_adapter_or_id, $permissions = array()) {
    $permissions = array_merge($permissions, array('see_self'));
    $user_id = (string) (is_string($user_adapter_or_id) || is_int($user_adapter_or_id) ? $user_adapter_or_id : $user_adapter_or_id->id());
    return $this->_community_api_user_token($user_id, $permissions);
  }
  
  private function _community_api_user_token($user_id, $permissions) {
    $fields = array(
      'api_key' => $this->api_key(),
      'user_id' => $user_id,
      'permissions' => $permissions
    );
    return FeeligoControllerAuthToken::make($fields, $this->secret());
  }
  
  /**
   * Check if a given string is a valid token on this community
   */
  public function is_valid_community_api_user_token($token_str) {
    return $this->decode_community_api_user_token($token_str) !== null;
  }
  
  /**
   * Decodes a token string and returns a token as an associative array
   * - ONLY returns the token if it is valid, i.e. if the signature and api_key match
   * - returns null otherwise
   */
  public function decode_community_api_user_token($token_str) {
    if ($token_str === null || !is_string($token_str)) { return null; }
    // decode token json object
    return FeeligoControllerAuthToken::decode($token_str, $this->secret());
  }
  
}