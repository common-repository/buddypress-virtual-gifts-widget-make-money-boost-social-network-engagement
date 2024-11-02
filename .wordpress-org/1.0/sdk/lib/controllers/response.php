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
 * @package    FeeligoControllerResponse
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */
 
require_once(str_replace('//','/',dirname(__FILE__).'/').'response/encoder/json.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'response/encoder/jsonp.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'response/encoder/xml.php');
 
class FeeligoControllerResponse {

  public function __construct ($request) {
    $this->_data = null;
    $format = $request->format();
    $callback = $request->param('callback');
    if ($format == 'xml') {
      $this->_encoder = new FeeligoControllerResponseEncoderXml();
    }elseif($format == 'jsonp' || $format == 'js' || ($format == 'json' && $callback !== null)){
      $this->_encoder = new FeeligoControllerResponseEncoderJsonp($callback !== null ? $callback : 'callback');
    }else{
      $this->_encoder = new FeeligoControllerResponseEncoderJson();
    }
    $this->_code = self::HTTP_OK;
    $this->_errors = array();
    $this->_headers = array();
  }
  
  public function encoder() {
    return $this->_encoder;
  }
  
  public function headers() {
    return $this->_headers;
  }
  
  public function code() {
    return $this->_code;
  }
  
  public function body() {
    return $this->encoder()->encode($this->_data);
  }
  
  public function error($type, $message) {
    $this->_errors[] = array($type => $message);
    return $this;
  }
  
  public function errors() {
    return $this->_errors;
  }
  
  public function set_data($data) {
    $this->_data = $data;
    return $this;
  }
  
  public function fail($status) {
    $this->set_data(array('errors' => $this->errors()));
    return $this->respond($status);
  }
  
  public function respond($status) {
    $this->_code = $status;
    $this->_set_default_headers();
    return $this;
  }
  
  public function set_header($key, $value) {
    $this->_headers[$key] = $value;
  }
  
  private function _set_default_headers() {
    $this->_headers['Content-type'] = $this->encoder()->content_type();
  }
  
  /**
   * HTTP success status codes and shorthand methods
   */
  const HTTP_OK = 200;
  const HTTP_CREATED = 201;
  const HTTP_ACCEPTED = 202;
  const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
  const HTTP_NO_CONTENT = 204;
  
  public function success() { return $this->respond(self::HTTP_OK); }
  public function success_created() { return $this->respond(self::HTTP_CREATED); }
  public function success_accepted() { return $this->respond(self::HTTP_ACCEPTED); }
  public function success_non_authoritative_information() { return $this->respond(self::HTTP_NON_AUTHORITATIVE_INFORMATION); }
  public function success_no_content() { return $this->respond(self::HTTP_NO_CONTENT); }
  
  
  /**
   * HTTP failure status codes and shorthand methods
   */
  
  const HTTP_BAD_REQUEST = 400;
  const HTTP_UNAUTHORIZED = 401;
  const HTTP_PAYMENT_REQUIRED = 402;
  const HTTP_FORBIDDEN = 403;
  const HTTP_NOT_FOUND = 404;
  const HTTP_METHOD_NOT_ALLOWED = 405;
  const HTTP_NOT_ACCEPTABLE = 406;
  
  public function fail_bad_request() { return $this->fail(self::HTTP_BAD_REQUEST); }
  public function fail_unauthorized() { return $this->fail(self::HTTP_UNAUTHORIZED); }
  public function fail_payment_required() { return $this->fail(self::HTTP_PAYMENT_REQUIRED); }
  public function fail_forbidden() { return $this->fail(self::HTTP_FORBIDDEN); }
  public function fail_not_found() { return $this->fail(self::HTTP_NOT_FOUND); }
  public function fail_method_not_allowed() { return $this->fail(self::HTTP_METHOD_NOT_ALLOWED); }
  public function fail_not_acceptable() { return $this->fail(self::HTTP_NOT_ACCEPTABLE); }
  
  
  /**
  * HTTP server error codes
  */
  const HTTP_INTERNAL_SERVER_ERROR = 500;
  const HTTP_NOT_IMPLEMENTED = 501;
  const HTTP_BAD_GATEWAY = 502;
  const HTTP_SERVICE_UNAVAILABLE = 503;
  const HTTP_GATEWAY_TIMEOUT = 504;
  const HTTP_VERSION_NOT_SUPPORTED = 505;
  
}