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
 * class FeeligoBuddypressModelUsersSelector
 *
 * allows to fetch users
 **/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
 
require_once(str_replace('//','/',dirname(__FILE__).'/').'../../../sdk/interfaces/users_selector.php'); 
require_once(str_replace('//','/',dirname(__FILE__).'/').'../../../sdk/lib/exceptions/not_found_exception.php');
 

class FeeligoBuddypressModelUsersSelector implements FeeligoUsersSelector {

  /**
   * Finds a single user by ID
   *
   * @param int|string $id the id of the requested user
   * @param bool $throw whether to throw an exception if user not found
   * @return FeeligoBuddypressUser|null adapter with the requested user
   */ 
  public function find($id, $throw = true) {
    if (($user = get_user_by('id', $id)) != false && $user->id == $id) {
      return new FeeligoBuddypressUser($user);
    }
    if ($throw) throw new FeeligoNotFoundException('type',
      'could not find '.'user'.' with id='.$id);
    return null;
  }
  

  /**
   * Finds multiple users by ID
   * no error is thrown if some or all ID's do not match any user
   *
   * @param array $ids the id's of the requested users
   * @return array[FeeligoBuddypressUser] adapters with the requested users
   */
  public function find_all($ids) { 
    $ids = implode(',', $ids);
    return $this->_get_user_adapters($this->_args_find_all(), $limit, $offset);
  }


  /**
   * Arguments for `_get_user_adapters` called by the `find_all` method
   * overriding this in a subclass allows to control the returned results.
   *
   * @return array arguments as expected by bp_core_get_users
   */
  protected function _args_find_all() {
    return array('include' => $ids);
  }
 

  /**
   * Returns all users ordered by name
   *
   * @param int $limit maximum number of users to be returned
   * @param int $offset number of users to skip from the beginning
   * @return array[FeeligoBuddypressUser] adapters with the requested users
   */
  public function all($limit = null, $offset = 0) {
    return $this->_get_user_adapters($this->_args_all(), $limit, $offset);
  }


  /**
   * Arguments for `_get_user_adapters` called by the `all` method
   * overriding this in a subclass allows to control the returned results.
   *
   * @return array arguments as expected by bp_core_get_users
   */
  protected function _args_all() {
    return array();
  }
  

  /**
   * Finds all users whose names match a given query
   *
   * @param string $query the query for user names
   * @param int $limit maximum number of users to be returned
   * @param int $offset number of users to skip from the beginning
   * @return array[FeeligoBuddypressUser] adapters with the requested users
   */
  public function search($query, $limit = null, $offset = 0) {
    return $this->_get_user_adapters($this->_args_search($query), $limit, $offset);
  }

  
  /**
   * Arguments for `_get_user_adapters` called by the `search` method
   * overriding this in a subclass allows to control the returned results.
   *
   * @param string $query the query for user names
   * @return array arguments as expected by bp_core_get_users
   */
  protected function _args_search($query) {
    return array('search_terms' => $query);
  }


  /**
   * Helper function which calls bp_core_get_users and returns the results
   * passed through calling _collect_users which returns an adapter for each result
   *
   * @param array $args the args to pass to bp_core_get_users
   * @return array[FeeligoBuddypressUser] adapters with the requested users
   */
  protected function _get_user_adapters($args, $limit = null, $offset = 0) {
    // convert $lim and $off to BP's page-based indexation
    if ($limit !== null) {
      $args['per_page'] = $limit;
      if ($offset > 0) $args['page'] = intval($offset / $limit) + 1; // starts at 1 (!)
    }
    // force alphabetical type for constant ordering
    $args['type'] = 'alphabetical';
    // get results from BuddyPress
    $result = bp_core_get_users($args);
    // TODO: check the flg_core_get_paged_users_sql filter to see how we patch
    // the bp_core_get_users function, and its implications
    return $this->_collect_users($result['users']);
  }
  
  /**
   * Helper function to instantiate an adapter for each WP_User
   *
   * @param array[WP_User] $users the users to adapt
   * @return array[FeeligoBuddypressUser] adapters with the requested users
   */
  private function _collect_users($users) {
    $collection = array();
    if (sizeof($users) > 0) {
      foreach($users as $user) {
        $collection[] = new FeeligoBuddypressUser($user, null);
      }
    }
    return $collection;
  }
 
}
?>