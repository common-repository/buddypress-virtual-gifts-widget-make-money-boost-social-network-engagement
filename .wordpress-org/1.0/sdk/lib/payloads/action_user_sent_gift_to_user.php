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
 * @package    FeeligoActionUserSentGiftToUserPayload
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */

/**
 * Payload for "user sent gift to user" action
 */
 
class FeeligoActionUserSentGiftToUserPayload {

  public function __construct($data) {
    $this->_data = $data;
  }

  public function type() {
    return isset($this->_data['type']) ? $this->_data['type'] : null;
  }
  
  public function name() {
    return isset($this->_data['name']) ? $this->_data['name'] : null;
  }

  /**
   * Returns the UserAdapter for the sender of the Gift
   * the sender is the action's subject
   */
  public function adapter_sender() {
    $arg = $this->_find_argument(array('subject'), 'community', 'user');
    if ($arg && isset($arg['adapter'])) return $arg['adapter'];
  }

  /**
   * Returns the UserAdapter for the recipient of the Gift
   * the recipient is the action's indirect object
   */
  public function adapter_recipient() {
    $arg = $this->_find_argument(array('indirect_object'), 'community', 'user');
    if ($arg && isset($arg['adapter'])) return $arg['adapter'];
  }

  /** 
   * Accessors for properties of the Gift
   */
  public function gift() {
    $arg = $this->_find_argument(array('direct_object'), 'feeligo', 'gift');
    if ($arg && isset($arg['properties'])) {
      $gift = new stdClass;
      $gift->id = $arg['properties']['id'];
      $gift->name = $arg['properties']['name'];
      $gift->message = $arg['properties']['message'];
      return $gift;
    }
  }

  /**
   * localized raw action body (without ${..} replaced)
   */
  public function localized_raw_body($locale) {
    // try the passed locale, its short form (if it is long), and finally default to 'en'
    if (isset($this->_data['message']) && isset($this->_data['message']['i18n'])) {
      foreach(array($locale, strtolower(substr($locale, 0, 2)), 'en') as $l) {
        if (isset($this->_data['message']['i18n'][$l])) {
          return $this->_data['message']['i18n'][$l];
        }
      }  
    }
    return '';
  }

  /**
   * Returns the best image URL for a certain medium at a certain desired size
   * 
   * If the exact size is available in the payload, the corresponding URL will be returned.
   * otherwise, it will return the URL for the size which is the closest.
   * If no size is specified, the first URL is returned.
   */
  public function medium_url($medium_name = 'medium', $size = null) {
    if (($medium = $this->_find_medium($medium_name)) !== null) {
      // process size
      $wh = $this->_split_w_h($size);
      
      if (isset($medium['sizes']) && is_array($medium['sizes']) && sizeof($medium['sizes'])) {

        // if no size is requested, return the first URL 
        if ($wh[0] === null || $wh[1] === null) {
          $vals = array_values($medium['sizes']);
          return $vals[0];
        }

        // if the exact size is available, return its URL
        $size = implode('x', $wh);
        if (isset($medium['sizes'][$size])) return $medium['sizes'][$size];

        // otherwise, look for the closest image and return its URL
        $closest_size = null;
        $closest_area_diff = null;
        foreach($medium['sizes'] as $size => $url) {
          // if no size given, return the first URL
          if ($wh[0] === null || $wh[1] === null) return $url;
          // else, look for the closest
          $_wh = $this->_split_w_h($size);
          $_diff = abs($_wh[0] * $_wh[1] - $wh[0]*$wh[1]);
          if ($closest_area_diff === null || $closest_area_diff > $_diff) {
            $closest_size = $size;
            $closest_area_diff = $_diff;
          }
        }
        return $medium['sizes'][$closest_size];
      }
    }
  }

  protected function _find_medium($name) {
    if (isset($this->_data['media']) && is_array($this->_data['media']) && sizeof($this->_data['media'])) {
      foreach($this->_data['media'] as $medium) {
        if (isset($medium['name']) && $medium['name'] == $name) return $medium;
      }
    }
  }

  protected function _split_w_h($size) {
    if ($size && is_string($size) && ($wh = explode('x', $size, 2)) && sizeof($wh) == 2) {
      return array(intval($wh[0]), intval($wh[1]));
    }
    return array(null, null);
  }

  /**
   * Looks for an argument matching function(s), domain, type
   */
  protected function _find_argument($functions, $domain = null, $type = null) {
    foreach ($functions as $fun) {
      foreach ($this->_arguments() as $arg) {
        if (($type === null || ($arg['type'] == $type))
            && ($domain === null || ($arg['domain'] == $domain))
            && $arg['properties']['function'] == $fun
          ) {
          return $arg;
        }
      }
    }
    return null;
  }

  /**
   * Accessor for the arguments array. If no 'arguments' key, returns an empty array
   */
  protected function _arguments() {
    if ($this->_data && isset($this->_data['arguments']) && is_array($this->_data['arguments'])) {
      return $this->_data['arguments'];
    }
    return array();
  }
}