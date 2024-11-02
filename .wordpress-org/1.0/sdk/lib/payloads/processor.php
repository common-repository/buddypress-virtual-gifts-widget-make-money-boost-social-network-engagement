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
 * @package    FeeligoPayloadProcessor
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */

require_once(str_replace('//','/',dirname(__FILE__).'/').'action_user_sent_gift_to_user.php');

/**
 * factory for Payload classes
 *
 * provides the static `process` method which takes the payload data as a PHP associative array,
 * as well as an instance of Feeligo_Api, and returns an instance of Payload which wraps the payload data.
 */
 
class FeeligoPayloadProcessor {

  protected function __construct($api) {
    $this->_api = $api;
  }

  public function api() {
    return $this->_api;
  }

  /**
   * replaces User arguments with User adapters from the API
   */
  protected function _discover_users($data) {
    if (isset($data['arguments']) && is_array($data['arguments']) && sizeof($data['arguments']) > 0) {
      foreach ($data['arguments'] as $k => $arg) {
        if (isset($arg['type']) && isset($arg['domain'])
            && $arg['type'] == 'user' && $arg['domain'] == 'community'
            && isset($arg['properties']) && isset($arg['properties']['id'])
          ) {
          // get an Adapter for the user, and store it in $arg['adapter']
          $arg['adapter'] = $this->api()->users()->find($arg['properties']['id'], false);
        }else{
          $arg['adapter'] = null;
        }
        $data['arguments'][$k] = $arg;
      }
    }
    return $data;
  }

  /**
   * instance factory method which actually processes the data
   */
  protected function _process($data) {
    if ($data && isset($data['type']) && isset($data['name'])) {

      // process Users
      $data = $this->_discover_users($data);

      // Action: user sent gift to user
      if ($data['type'] == 'action' && $data['name'] == 'user_sent_gift_to_user') {
        return new FeeligoActionUserSentGiftToUserPayload($data);
      }
    }
  }
  
  /**
   * The public interface : class method which instantiates a FeeligoPayloadProcessor
   * and calling its `process` method which returns the appropriate Payload class
   * for the data
   */
  public static function process($data, $api) {
    $processor = new self($api);
    return $processor->_process($data);
  }  
  
}