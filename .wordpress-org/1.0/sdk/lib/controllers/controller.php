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
 * @package    FeeligoController
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */

require_once(str_replace('//','/',dirname(__FILE__).'/').'../helpers/url.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'response.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'../presenters/factory.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'../payloads/processor.php');

/**
 * Exception used to break the controller's execution and set an error message into the response
 */
class FeeligoControllerException extends Exception {
  
  public function __construct($status, $type, $msg) {
    parent::__construct("$type: $msg");
    $this->_status = $status;
    $this->_type = $type;
    $this->_message = $msg;
  }
  
  public function status() { return $this->_status; }
  public function type() { return $this->_type; }
  public function message() { return $this->_message; }
}
 
 
/**
 * Single controller: determines the requested action, executes it and returns a response
 */ 
class FeeligoController {

  public function __construct(FeeligoApi $api = null) {
    $this->_api = $api;
    
    // URL helper
    $this->_url_helper = new FeeligoHelperUrl();
    
    // response
    $this->_response = new FeeligoControllerResponse($this->request());
    
    // pagination initial setting
    $this->_does_paginate = false;
  }
  
  public function run () {
    try {
      $this->_run();
    } catch (FeeligoControllerException $e) {
      // Controller exceptions
      $this->response()->error($e->type(), $e->message());
      return $this->response()->fail($e->status());
    }
    return $this->response()->success();
  }
  
  /**
   * Accessor for API object
   */
  public function api() {
    return $this->_api;
  }
  
  /**
   * Accessor for the authentication object
   */
  public function auth() {
    return $this->api()->auth();
  }
  
  /**
   * Accessor for the URL helper
   */
  public function url_helper() {
    return $this->_url_helper;
  }
  
  /**
   * Accessor for the Request
   */
  public function request() {
    return $this->url_helper()->request();
  }
  
  /**
   * Accessor for the URL
   */
  public function url($i = null) {
    return $this->request()->url($i);
  }
  
  /**
   * Accessor for the params
   */
  public function param($name, $default_val = null) {
    return $this->request()->param($name, $default_val);
  }
  
  /**
   * Accessor for the Response
   */
  public function response() {
    return $this->_response;
  }
  
  /**
   * url_for helper
   */
  public function url_for($new_flg_url = null, $new_params = null) {
    return $this->_url_helper->url_for($new_flg_url, $new_params);
  }

  /**
   * Actually performs the action
   */
  private function _run() {
    
    $data = null;
    $errors = array();
    
    // check instance of Feeligo_Api (useful in developement mode)
    if ($this->_api === null) {
      $this->_fail_method_not_allowed('FeeligoApi', 'FeeligoController expects an instance of FeeligoApi');
    }
    
    // decode Token from URL
    $token = $this->auth()->decode_community_api_user_token($this->param('token'));
    
    // in development env only
    if ($this->url(0) == 'test_token') {
      if ($token === null) {
        // no token or invalid
        if (getenv('FLG_ENV') == 'development' || (defined('FLG_ENV') && FLG_ENV == 'development')) {
          // dev mode : generate a Test Token
          $this->response()->set_data(array(
            'token' => $this->auth()->community_api_user_token('_test_')->encode()
          ));
          return;
        }else{
          // no dev mode : fail
          $this->_fail_bad_request('path', $this->url()." only available in development mode");
        }
      }else{
        // valid token
        $this->response()->set_data(array(
          'token' => 'the supplied token is valid.'
        ));
      }
      return;
    }
    
    // authentication
    if ($token === null) {
      $this->_fail_unauthorized('token', 'invalid');
    }
    
    // pagination
    $this->pagination_limit = (int) $this->param('lim', $this->param('limit', 100));
    $this->pagination_offset = (int) $this->param('off', $this->param('offset', 0));
      
    // routing
    
    if ($this->url(0) == 'info') {
      // path: info/
      $this->_require_permission($token, 'community_info');
      
      $this->response()->set_data(array(
        'time' => time(),
        'phpversion' => phpversion(),
        'sdkversion' => '2.2'
      ));
      
      return;
      
    }elseif ($this->url(0) == 'search') {
      // path: search/  :  search
      if (!($type = $this->param('t'))) { $this->_fail_bad_request('type', "missing"); }  
        
      if ($type == 'user') {
        // search among users
        $data = $this->_select_search($this->api()->users());
      } else {
        // only allow searching users
        $this->_fail_bad_request('type', "$type is not a valid type for $controller");
      }
    }elseif ($this->url(0) == 'users') {
      // path: users/  :  select community users
      $data = $this->api()->users();
      
      if ($this->url(1) == 'search') {
        // path: users/search  :  search among users
        $data = $this->_select_search($data);
        
      }else if ($this->url(1)) {
        
        // path: users/:id  :  select user by id  
        try {
          $data = $data->find($this->url(1));
          
          if ($this->url(2) == 'friends') {
            // users/:id/friends  :  select user's friends
            
            // access to friends is restricted : check token
            if (false && $token->user_id().'' !== $this->url(1)) {
              $this->_fail_unauthorized('privacy', "you are not allowed to access this user's friends");
            }
            
            // select user's friends
            $data = $data->friends_selector();
            
            if ($this->url(3) == 'search') {
              // path: users/:id/friends/search  :  search among friends
              $data = $this->_select_search($data);
               
            }else if ($this->url(3) !== null) {
              // path: users/:id/friends/:friend_id  :  select specific friend by id
              $data = $data->find($this->url(3));
              
              if ($this->url(4) !== null) {
                // path: users/:id/friend/:friend_id/:something  :  invalid path
                $this->_fail_bad_request('path', $this->url()." is not a valid path");  
              }
            }else{
              // path: users/:id/friends  :  list all friends of user :id
              $data = $this->_select_all($data);
            }
          }else if ($this->url(2) !== null) {
            // users/:id/something  :  invalid
            $this->_fail_bad_request('path', $this->url()." is not a valid path");
          }
        } catch (FeeligoNotFoundException $e) {
          // if either user :id or his friend :friend_id was not found
          $this->_fail_not_found($e->type(), $e->message());
        }
      }else{
        // path: users/  :  list all users of the community
        $data = $this->_select_all($this->api()->users());
      }
      
    }elseif ($this->url(0) == 'actions') {
      // only allow POST
      $this->_require_method('POST');
      
      // $this->api()->actions() will return NULL if not implemented
      if ($this->api()->actions() === null) {
        $this->_fail_not_implemented('actions', 'actions/ is not available on this server');
      }
      
      // create the action
      if (($payload = $token->payload()) !== null && is_array($payload)) {

        // pre-process the payload
        $payload = FeeligoPayloadProcessor::process($payload, $this->api());

        // pass the payload to the API to publish the action
        $data = $this->api()->actions()->create($payload);
        if ($data === null) {
          $this->_fail_bad_request('action', 'could not be saved');
        }
      }else{
        $this->_fail_bad_request('payload', 'missing');
      }
      
    }else{
      $this->_fail_bad_request('path', $this->url()." is not a valid path");
    }
    
    // data in JSON format
    if ($data !== null && ($presenter = FeeligoPresenterFactory::present($data)) !== null) {
      $data = $presenter->as_json();
    }
    
    // add pagination information if needed
    $data = $this->_add_pagination_info(array(
      'time' => time(),
      'data' => $data
    ));
      
    $this->response()->set_data($data);
  }
  
  /**
   * calls the search() method on $data, passing query, type and pagination parameters
   */
  private function _select_search($data, $query = null) {
    // ensure there is a query, either passed or in the params
    if ($query === null && ($query = $this->param('q')) === null) { $this->_fail_bad_request('query', "missing"); }
    // enable pagination
    $this->_does_paginate = true;
    // apply search()
    return $data->search($query, $this->pagination_limit, $this->pagination_offset);
  }
  
  /**
   * calls the all() method on $data, passing pagination parameters
   */
  private function _select_all($data) {
    // enable pagination
    $this->_does_paginate = true;
    // apply all()
    return $data->all($this->pagination_limit, $this->pagination_offset);
  }
  
  
  /**
   * add pagination information to data (if paginated)
   */
  private function _add_pagination_info($data) {
    if ($this->_does_paginate && $this->pagination_limit !== null) {
      // show 'previous' link if offset > 0
      if ($this->pagination_offset > 0) {
        $previous_offset = max(array($this->pagination_offset - $this->pagination_limit,0));
        $params = $this->request()->params();
        if ($previous_offset == 0) {
          unset($params['off']);
        }else{
          $params['off'] = $previous_offset;
        }
        if (!isset($data['paging'])) $data['paging'] = array();
        $data['paging']['previous'] = $this->url_for($params);
      }
      // show 'next' link if limit reached
      if (isset($data['data']) && $this->pagination_limit == sizeof($data['data'])) {
        $params = $this->request()->params();
        $params['off'] = $this->pagination_offset + $this->pagination_limit;
        if (!isset($data['paging'])) $data['paging'] = array();
        $data['paging']['next'] = $this->url_for($params);
      } 
    }
    return $data;
  }
  
  
  /**
   * make sure that the token has a certain permission, or raise an error
   */
  private function _require_permission($token, $permission, $throw = true) {
    if (!$token->has_permission($permission)) {
      if ($throw) $this->_fail_unauthorized('permission', "$permission needed");
      return false;
    }
    return true;
  }
  
  /**
   * make sure the request has a specific method
   */
  private function _require_method($method, $throw = true) {
    if (!$this->request()->method_is($method)) {
      if ($throw) $this->_fail_method_not_allowed($method.' method', 'not allowed');
      return false;
    }
    return true;
  }
  
  /**
   * convenience methods to throw controller exceptions
   */
  private function _fail_bad_request($type, $msg) { // 400
    throw new FeeligoControllerException(FeeligoControllerResponse::HTTP_BAD_REQUEST, $type, $msg);
  }
  private function _fail_unauthorized($type, $msg) { // 401
    throw new FeeligoControllerException(FeeligoControllerResponse::HTTP_UNAUTHORIZED, $type, $msg);
  }
  private function _fail_not_found($type, $msg) { // 404
    throw new FeeligoControllerException(FeeligoControllerResponse::HTTP_NOT_FOUND, $type, $msg);
  }
  private function _fail_method_not_allowed($type, $msg) { // 405
    throw new FeeligoControllerException(FeeligoControllerResponse::HTTP_METHOD_NOT_ALLOWED, $type, $msg);
  }
  private function _fail_not_implemented($type, $msg) { // 501
    throw new FeeligoControllerException(FeeligoControllerResponse::HTTP_NOT_IMPLEMENTED, $type, $msg);
  }
}