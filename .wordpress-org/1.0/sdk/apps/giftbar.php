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
 * @package    FeeligoAppGiftbar
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
 
require_once(str_replace('//','/',dirname(__FILE__).'/').'../lib/api.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'../lib/presenters/factory.php');

class FeeligoGiftbarApp {
  
  /**
   * constructor
   *
   * @param FeeligoApi $api your own implementation of the FeeligoApi class
   */
  function __construct(FeeligoApi $api) {
    $this->_api = $api;
  }
  
  /**
   * accessor for the FeeligoApi instance
   *
   * @return FeeligoApi
   */
  function api() {
    return $this->_api;
  }
 
  /**
   * URL of the CSS stylesheet
   *
   * the <link> tag referencing this file should be placed in the page's <head>
   *
   * @return string
   */
  public function css_url($version = null) {
    return $this->_remote_app_file_url('giftbar'.(!!$version ? '-'.$version : '').'.css');
  }
  
  /**
   * URL of the JS file which loads the GiftBar
   *
   * the <script> tag referencing this file should be placed close to the bottom of the page,
   * (and not in the <head>) so that it does not block page load
   *
   * @return string
   */
  public function loader_js_url() {
    return $this->_remote_app_file_url('giftbar-loader-'.$this->api()->viewer()->id().'.js');
  }
  
  /**
   * Helper function which builds URL's of Feeligo app files
   */
  protected function _remote_app_file_url($path) {
    return $this->api()->remote_server_url()."c/".$this->api()->community_api_key()."/apps/".$path;
  }
  
  
  /**
   * Tells whether the GiftBar should be displayed based on current context
   *
   * @return bool
   */
  public function is_enabled() {
    return $this->api()->has_viewer();
  }
  
  /**
   * Sets some JS variables that the GiftBar expects to find in order to run
   * - context : the viewer and the subject
   * - auth : the tokens used to authenticate requests to the Feeligo API
   *
   * @return string
   */
  public function initialization_js() {
    $js = '(function(){if(!this.flg){this.flg={}}';
    $js .= 'if(!this.flg.config){this.flg.config={}}flg.config.api_key="'.$this->api()->community_api_key().'";';
    $js .= 'if(!this.flg.context){this.flg.context={}}flg.context='.json_encode($this->context_as_json()).';';
    $js .= 'flg.auth='.json_encode($this->auth_as_json()).'}).call(this);';
    return $js;
  }
  
  /**
   * Returns the json_encodable data for the Context
   *
   * @return Array
   */
  public function context_as_json() {
    return array(
      'viewer' => $this->_user_as_json($this->api()->viewer()),
      'subject' => $this->api()->has_subject() ? $this->_user_as_json($this->api()->subject()) : null,
    );
  }
  
  /**
   * Returns json_encodable Authentication data
   *
   * @return Array
   */
  public function auth_as_json() {
    $auth = $this->api()->auth();
    return array(
      'time' => $auth->time(),
      'password' => $auth->remote_api_user_token($this->api()->viewer()),
      'community_api_user_token' => $auth->community_api_user_token($this->api()->viewer())->encode()
    );
  }
  
  /**
   * Returns a user as JSON-encodable object
   *
   * @return Array
   */
  protected function _user_as_json($user_adapter) {
    $presenter = FeeligoPresenterFactory::present($user_adapter);
    if ($presenter === null) return null;
    return $presenter->as_json();
  }
}