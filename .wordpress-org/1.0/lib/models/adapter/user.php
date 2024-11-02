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
 * class FeeligoBuddypressUser
 *
 * adapter to provide a standard interface to the Buddypress user model
 **/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

require_once(str_replace('//','/',dirname(__FILE__).'/').'../../../sdk/interfaces/user_adapter.php');


class FeeligoBuddypressUser implements FeeligoUserAdapter {

  /**
   * constructor stores a reference to the adapted User
   */
  public function __construct($bp_user) {
    $this->user = $bp_user;
  }

  /**
   * @return WP_User the adapted user object
   */
  public function user() {
    return $this->user;
  }

  /**
   * @return string the user's id as a string
   */
  public function id() {
    return $this->user->id . '';
  }
  
  /**
   * @return string the user's display name
   */
  public function name() {
    return bp_core_get_user_displayname($this->user->id);
  }
  
  /**
   * @return string the URL of the user's profile page
   */
  public function link() {
    $match = array();
    preg_match("/href=['|\"](.*?)['|\"]/", bp_core_get_userlink($this->user->id),$match);
    return sizeof($match) > 0 ? html_entity_decode($match[1]) : null;
  }
  
  /**
   * @return string the URL of the user's profile picture, as a 256x256px image
   */
  public function picture_url() {
    $match = array();
    preg_match("/src=['|\"](.*?)['|\"]/", get_avatar($this->user->id, 256),$match);
    return sizeof($match) > 0 ? html_entity_decode($match[1]) : null;
  }

  /**
   * @return FeeligoBuddypressModelUserFriendsSelector a selector for the friends
   *   of the adapted user
   */
  public function friends_selector() {
    return new FeeligoBuddypressModelUserFriendsSelector($this);
  }

}
?>