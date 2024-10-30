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
 * @package    FeeligoControllerResponseEncoderXml
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
 
/**
 * Encodes a $data variable (string|null|array) to a XML string
 */
 
require_once(str_replace('//','/',dirname(__FILE__).'/').'../encoder.php');
 
class FeeligoControllerResponseEncoderXml implements FeeligoControllerResponseEncoder {

  public function content_type() {
    return 'text/xml';
  }

  public function encode($data) {
    return '<'.'?xml version="1.0"?'.'>' . $this->_encode($data, "response");
  }
  
  protected function _encode($obj, $tagname = null) {
    if (isset($obj['type'])) {
      $type = $obj['type'];
      unset($obj['type']);
      if ($tagname === null) return $this->_encode($obj, $type);
      return "<$tagname>".$this->_encode($obj, $type)."</$tagname>";
    }
    $attrs = array();
    $children = array();
    foreach ($obj as $k => $v) {
      if (!is_array($v)) {
        $attrs[$k] = $v;
      }else{
        $children[] = $this->_encode($v, is_string($k) ? $k : null);
      }
    }
    return $this->_tag_string($tagname, $attrs, $children);
  }
  
  protected function _tag_string($name, $attrs, $children) {
    $a_str = $this->_attr_string($attrs);
    if (count($children)==0) {
      return "<$name$a_str/>";
    }
    return "<$name$a_str>".implode($children)."</$name>";
  }
  
  protected function _attr_string($attrs) {
    $str = "";
    if (sizeof($attrs)>0){
      foreach ($attrs as $k => $v) {
        $str .= " $k='$v'";
      }
    }
    return $str;
  }
}