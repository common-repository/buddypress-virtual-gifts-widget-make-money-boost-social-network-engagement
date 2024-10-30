<?php
/**
 * Copyright 2013 Feeligo <tech@feeligo.com>
 *
 * This file is part of the Feeligo Plugin for BuddyPress.
 * The Feeligo Plugin for BuddyPress is free software; you can redistribute
 * it and/or modify it under the terms of the GNU General Public License,
 * version 2, as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 **/


/**
 * class FeeligoBuddypressApi
 *
 * extends FeeligoApi to provide an implementation specific to BuddyPress
 **/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

 
class FeeligoBuddypressApi extends FeeligoApi {

  /**
   * Returns the singleton instance of FeeligoBuddypressApi to be used wherever
   * needed. Do not instantiate this class directly.
   *
   * @return FeeligoBuddypressApi the singleton instance
   */
  public static function _() {
    if ( is_null(self::$_instance) ) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }


  /**
   * Protected constructor, caches references to common objects.
   * Do not instantiate this class, instead use the singleton function _()
   */
  protected function __construct() {
    global $bp;

    parent::__construct();
    // store the viewer
    $this->_adapter_viewer = new FeeligoBuddypressUser(get_user_by('id',$bp->loggedin_user->userdata->ID));

    // store the subject
    $this->_adapter_subject = null;
    if ( $bp->displayed_user->userdata->ID != null ) {
      $u_subj = new FeeligoBuddypressUser((get_user_by('id',$bp->displayed_user->userdata->ID))) ;
      // ensure the subject is not the same User as the viewer
      if ( $this->_adapter_viewer->id() != $u_subj->id() ) {
        $this->_adapter_subject = $u_subj;
      }
    }
  }


  /**
   * Returns whether or not a user is currently logged in. If so, we call that
   * user the `viewer`
   *
   * @return bool whether there is a viewer
   */
  public function has_viewer() {
    global $bp;
    $adpt_viewer = $bp->loggedin_user->userdata->ID;
    if ( $adpt_viewer != null ) {
      return true;
    }
    else {
      return false;
    }
  }


  /**
   * Returns whether or not the `viewer` is viewing another user's profile page
   * if so, we call the other user the `subject`
   *
   * @return bool whether there is a subject
   */
  public function has_subject() {
    global $bp;
    $adpt_viewer = $bp->loggedin_user->userdata->ID;
    $adpt_subject = $bp->displayed_user->userdata->ID;
    if ( $adpt_subject != null && $adpt_viewer!=$adpt_subject ) {
      return true;
    }
    else {
      return false;
    }
  }


  /**
   * Returns the adapter for the `viewer`
   *
   * @return FeeligoBuddypressUser the viewer adapter
   */
  public function viewer() {
    return $this->_adapter_viewer;
  }


  /**
   * Returns the adapter for the `subject`
   *
   * @return FeeligoBuddypressUser the subject adapter
   */
  public function subject() {
    return $this->_adapter_subject;
  }

  
  /**
   * Returns the selector for users
   *
   * @return FeeligoBuddypressModelUsersSelector the user selector
   */
  public function users() {
    if ( !isset($this->_users) ) {
      $this->_users = new FeeligoBuddypressModelUsersSelector();
    }
    return $this->_users;
  }
  

  /**
   * Returns the selector for actions
   *
   * @return FeeligoBuddypressModelActionsSelector the actions selector
   */
  public function actions() {
    if ( !isset($this->_actions) ) {
      $this->_actions = new FeeligoBuddypressModelActionsSelector();
    }
    return $this->_actions;
  }
  

  /**
   * Returns the API key in use, from settings or hardcoded constant
   *
   * @override
   * @return string the API key
   */
  public function community_api_key() {
    $options = get_option('flg_plugin_options');
    return isset($options['public_key']) ? $options['public_key'] : self::__community_api_key;
  }
  
  
  /**
   * Returns the Secret key in use, from settings or hardcoded constant
   *
   * @override
   * @return string the Secret key
   */
  public function community_secret() {
    $options = get_option('flg_plugin_options');
    return isset($options['secret_key']) ? $options['secret_key'] : self::__community_secret;
  }


  /**
   * Returns the URL of the Feeligo server ('http://www.feeligo.com' by default).
   * Define the FLG__server_url global constant somewhere to override.
   *
   * WARNING: must end with a slash!
   * TODO: consider integrating this into the SDK
   *
   * @override
   * @return string the URL of Feeligo's server
   */
  public function remote_server_url() {
    return substr(self::__remote_server_url, 0, 5) == 'FLG__' ? "http://feeligo.com/" : self::__remote_server_url; 
  }
  

  /**
   * Returns the hardcoded API key if defined, or null
   *
   * @return string|null the API key
   */
  public static function default_public_key() {
    return substr(self::__community_api_key, 0, 5) == 'FLG__' ? null : self::__community_api_key;
  }
  

  /**
   * Returns the hardcoded Secret key if defined, or null
   *
   * @return string|null the Secret key
   */
  public static function default_secret_key () {
    return substr(self::__community_secret, 0, 5) == 'FLG__' ? null : self::__community_secret;    
  }
  
}
?>