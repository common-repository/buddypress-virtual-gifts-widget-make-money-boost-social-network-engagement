<?php
/*
Plugin Name: Feeligo
Plugin URI:http://www.feeligo.com/giftbar/buddypress
Description: Feeligo is the new way to monetize, engage and expand a specialized social network. Feeligo enables users to buy virtual gifts and send them to each other.
Version: 1.0
Author: Feeligo <tech@feeligo.com>
Author URI:http://www.feeligo.com/
License: A "Slug" license name e.g. GPL2
*/


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

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


// Define a constant that we can use to construct file paths throughout the component
define( 'BP_FEELIGO_PLUGIN_DIR', dirname( __FILE__ ) );


// Only load the component if BuddyPress is loaded and initialized.
function bp_feeligo_init() {
	// Because our loader file uses BP_Component, it requires BP 1.5 or greater.
	if ( version_compare( BP_VERSION, '1.3', '>' ) )
		require( dirname( __FILE__ ) . '/lib/component.php' );
}
add_action( 'bp_include', 'bp_feeligo_init' );


// require_once required files
foreach(array(
  'sdk/apps/giftbar.php',
  'sdk/lib/api.php',
  'sdk/lib/controllers/controller.php',

  'lib/admin/admin.php',
  'lib/api.php',
  'lib/api_endpoint.php',
  'lib/filters/flg_activity_allowed_tags.php',
  'lib/filters/flg_core_get_paged_users_sql.php',
  'lib/apps/giftbar.php',
  'lib/models/adapter/action.php',
  'lib/models/adapter/user.php',
  'lib/models/selector/actions.php',
  'lib/models/selector/users.php',
  'lib/models/selector/userfriends.php',
) as $path) {
  require_once(str_replace('//','/',dirname(__FILE__).'/').$path);
}
?>