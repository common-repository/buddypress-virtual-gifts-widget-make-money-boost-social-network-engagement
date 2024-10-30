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
 * class BP_Feeligo_Component
 *
 * Sets up the basic structure of the component.
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class BP_Feeligo_Component extends BP_Component {

	/**
	 * Calls parent::start() to tell the parent BP_Component to begin its setup routine.
	 */
	function __construct() {
		global $bp;
    
		parent::start(
			'feeligo',
			__( 'Feeligo', 'bp-feeligo' ),
			BP_FEELIGO_PLUGIN_DIR
		);

		/* Puts component into the active components array, so that
		 *   bp_is_active( 'feeligo' );
		 * returns true when appropriate. We have to do this manually, because non-core
		 * components are not saved as active components in the database.
		 */
		$bp->active_components[$this->id] = '1';

		// register the register_post_types() method to store data
		add_action( 'init', array( &$this, 'register_post_types' ) );
		// register /feeligo/api route
		add_action('wp_loaded', 'flg_api_endpoint_handle_request');
		// render the GiftBar's HTML in the footer
		add_action('wp_footer', 'flg_giftbar_render_footer_html');
		// set allowed HTML tags and attributes in activity posts
		add_filter('bp_activity_allowed_tags', 'flg_activity_allowed_tags', 0);
	}


	/**
	 * Sets up the plugin's globals
	 *
	 * @global obj $bp BuddyPress's global object
	 */
	function setup_globals() {
		global $bp;
		// Defining the slug in this way makes it possible for site admins to override it
		if ( !defined( 'BP_FEELIGO_SLUG' ) )
			define( 'BP_FEELIGO_SLUG', $this->id );

		// Global tables
		$global_tables = array(
			'table_name' => $bp->table_prefix.'bp_feeligo'
		);

		// Set up the $globals array to be passed along to parent::setup_globals()
		$globals = array(
			'slug'                  => BP_FEELIGO_SLUG,
			'root_slug'             => isset( $bp->pages->{$this->id}->slug ) ? $bp->pages->{$this->id}->slug : BP_FEELIGO_SLUG,
			'has_directory'         => false, // Set to false if not required
			'notification_callback' => 'bp_feeligo_format_notifications',
			'search_string'         => __( 'Search Examples...', 'buddypress' ),
			'global_tables'         => $global_tables
		);

		// Let BP_Component::setup_globals() do its work.
		parent::setup_globals( $globals );
	}

}


/**
 * Loads component into the $bp global
 */
function bp_feeligo_load_core_component() {
	global $bp;
	$bp->feeligo = new BP_Feeligo_Component;
}
add_action( 'bp_loaded', 'bp_feeligo_load_core_component' );
?>