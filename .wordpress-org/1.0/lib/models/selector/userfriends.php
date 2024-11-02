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
 * class FeeligoBuddypressModelUserFriendsSelector
 *
 * allows to fetch the friends of a given user
 **/
 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

require_once(str_replace('//','/',dirname(__FILE__).'/').'../../../sdk/interfaces/users_selector.php'); 
require_once(str_replace('//','/',dirname(__FILE__).'/').'../../../sdk/lib/exceptions/not_found_exception.php');

 
class FeeligoBuddypressModelUserFriendsSelector
  extends FeeligoBuddypressModelUsersSelector
  implements FeeligoUsersSelector {
 
  /**
   * @param FeeligoBuddypressUser adapter with the reference user
   */
  public function __construct($user_adapter) {
    $this->_user_adapter = $user_adapter;
    $this->_uid = $user_adapter->user->id;
  }
 

  /**
   * Finds a single friend by ID
   *
   * @param int|string $id the id of the requested friend
   * @param bool $throw whether to throw an exception if friend not found
   * @return FeeligoBuddypressUser|null adapter with the requested friend
   */ 
  public function find($id, $throw = true) {
    if ( function_exists('friends_check_friendship')
        && friends_check_friendship($this->_uid, $id)
        && ($user = get_user_by('id', $id)) != false
        && $user->id == $id
      ) {
      return new FeeligoBuddypressUser($user);
    }
    if ($throw) throw new FeeligoNotFoundException('type',
      'could not find '.'user'.' with id='.$id);
    return null;
  }
  

  /**
   * Arguments for `_get_user_adapters` called by the `find_all` method
   * overrides the parent class implementation to add user_id
   *
   * @override
   * @return array arguments as expected by bp_core_get_users
   */
  protected function _args_find_all($ids) {
    return array_merge(parent::_args_find_all(), 
      array('user_id' => $this->_uid));
  }
 

  /**
   * Arguments for `_get_user_adapters` called by the `all` method
   * overrides the parent class implementation to add user_id
   *
   * @override
   * @return array arguments as expected by bp_core_get_users
   */
  public function _args_all() {
    return array_merge(parent::_args_all(), 
      array('user_id' => $this->_uid));
  }


  /**
   * Arguments for `_get_user_adapters` called by the `search` method
   * overrides the parent class implementation to add user_id
   *
   * @override
   * @param string $query the query for user names
   * @return array arguments as expected by bp_core_get_users
   */
  public function _args_search($query) {
    return array_merge(parent::_args_search($query), 
      array('user_id' => $this->_uid));
  }

}
?>