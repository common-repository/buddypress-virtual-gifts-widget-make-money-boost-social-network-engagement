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
 * Feeligo API endpoint
 **/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * registers the /feeligo/api route
 *
 * because Wordpress lacks a system to dynamically register routes, we register
 * this function on the send_headers hook. It will examine the query string path
 * and intercept the request if it matches /feeligo/api
 */
function flg_api_endpoint_handle_request() {
  global $route, $wp_query, $window_title;

  $uri_parts = explode('?', $_SERVER['REQUEST_URI']);
  $uri_parts = explode('/', trim($uri_parts[0], '/'));

  // match '/feeligo/api'
  if (($n = sizeof($uri_parts)) >= 2 && $uri_parts[$n-2].'/'.$uri_parts[$n-1] == 'feeligo/api') {
    // the route matches, instantiate the Feeligo controller
    $ctrl = new FeeligoController(FeeligoBuddypressApi::_());

    // fix ugly BP alphabetical user search
    add_filter('bp_core_get_paged_users_sql', 'flg_core_get_paged_users_sql', 0, 2);

    // handle the request
    $response = $ctrl->run();

    // set headers and echo response body
    header("HTTP/1.1 ".$response->code());
    foreach($response->headers() as $k => $v) {
      header("$k: $v");
    }
    echo $response->body();

    // exit to prevent further processing of the request
    exit();
  }

  // if the request did not mach /feeligo/api, let Wordpress handle it
}
?>