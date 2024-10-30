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
 * class FeeligoBuddypressModelActionAdapter
 *
 * adapter to provide a standard interface for a Buddypress newsfeed action
 **/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

require_once(str_replace('//','/',dirname(__FILE__).'/').'../../../sdk/interfaces/action_adapter.php');


class FeeligoBuddypressModelActionAdapter implements FeeligoActionAdapter {

  /**
   * @param mixed the id of the action record
   */
  public function __construct($id) {
    $this->id = $id;
  }

  
  /**
   * @return mixed the id of the action record
   */
  function id() {
    return $this->id;
  }

}
?>