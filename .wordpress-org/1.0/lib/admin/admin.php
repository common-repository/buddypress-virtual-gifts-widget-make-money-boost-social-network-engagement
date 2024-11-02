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
 * Dialogs for the wp-admin interface to configure the plug-in.
 *
 * Provide instructions for configuration, which includes registering with
 * Feeligo and obtaining and entering API credentials.
 **/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


// Add menu to the admin interface
add_action( 'admin_menu', 'flg_plugin_menu' );


function flg_plugin_menu() {
  add_options_page( 'Feeligo Options', 'Feeligo', 'manage_options', 'flg_plugin', 'flg_plugin_options_page' );
}


// Add link to settings page
add_filter( 'plugin_action_links', 'flg_add_settings_link', 10, 2 );


function flg_add_settings_link( $links, $file ) {
  if ( function_exists( 'bp_core_do_network_admin' ) && bp_core_do_network_admin() ) {
    $url = network_admin_url( 'settings.php' );
  } else {
    $url = admin_url( 'options-general.php' );
  }
  if ( $file == "feeligo/loader.php" ) {
    $settings_link = '<a href="' . add_query_arg( array( 'page' => 'flg_plugin' ), $url ) . '">';
    $settings_link .= __( 'Settings', 'feeligo' ) . '</a>';
    array_unshift( $links, $settings_link );
  }

  return $links;
}


function flg_plugin_options_page() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	?> <div class="wrap">
	<?php screen_icon(); ?>
  <h2>Feeligo</h2>
  <p style="font-weight: bold; font-size: 1.1em">To get the most benefit out of your Giftbar,
    please visit <a href='http://www.feeligo.com/dashboard/sign_in?utm_source=WP+Admin&utm_medium=Title+Link&utm_campaign=BuddyPress+plugin'>Feeligo</a>.</p>
  
  <?php
    // Set the keys if they exist (ie. plugin was downloaded through feeligo)
    $options = get_option('flg_plugin_options');
    $edit_keys = false;
    
   // public_key is null or empty but we have a default public_key so we store it
   if ( !isset($options['public_key']) ||  empty($options['public_key']) ) {
     if ( FeeligoBuddypressApi::default_public_key() != null ) {   
       $options['public_key'] = FeeligoBuddypressApi::default_public_key();
       update_option('flg_plugin_options', $options);  
     }
     else {
       // Show form to edit
       $edit_keys = true;
     }
   }

  $t_data = base64_encode(json_encode(array(
          'api_key' => $options['public_key'],
          'host_url' => get_home_url()
          )));
   
   if ( !isset( $options['secret_key'] ) || empty( $options['secret_key'] ) ) {
     if ( FeeligoBuddypressApi::default_secret_key() != null ) {
       $options['secret_key'] = FeeligoBuddypressApi::default_secret_key();
       update_option('flg_plugin_options', $options);
     }
     else {
       $edit_keys = true;
     }
   }
   
   if ( $edit_keys ) {
    echo "<div class='updated'><p style='font-weight: bold; font-size: 1.1em;'>To finish installing this plugin, you need to obtain your API key and Secret key from Feeligo.<br/> Simply sign up or login to your <a href='http://www.feeligo.com/dashboard'>Feeligo dashboard</a>: you will find your keys under the Settings menu.</p></div>";
   }

   if ( $edit_keys ||  $_GET["edit"] == "true" ) {
     echo '<form action="options.php" method="post">';
     settings_fields('flg_plugin_options');
     do_settings_sections('flg_plugin');
     echo "<p style='margin-top: 20px;''><input name='Submit' type='submit' value='Save Changes'/>";
     ?> <a style="text-decoration:none; margin-left: 10px;" href="<?php echo $_SERVER['PHP_SELF']."?page=flg_plugin";?>"><input type='button' name='cancel' value='Close' /></a></p> <?php;
     echo "</form>";
     echo "</div>";
   }
   else {
     echo "<p style='font-weight: bold; font-size: 1.1em; padding: 10px 10px; background-color: #DFD; border-top: solid 1px #9F9; color: #363;'>To manage your catalog of gifts, track user activity, monitor and collect your earnings, please sign in to the Feeligo dashboard at <a target='_blank' href='http://www.feeligo.com/dashboard/sign_in?utm_source=WP+Admin&utm_medium=Banner+Link&utm_campaign=BuddyPress+plugin'>feeligo.com/dashboard</a>.</p>";
     flg_plugin_section_text();
     echo "<p>API key: ".$options['public_key']."<br/>";
     echo "Secret key: ".$options['secret_key']."</p>";
     ?> <form action="<?php echo $_SERVER['PHP_SELF'];?>" method='get'> <?php
     echo '<input type="hidden"  name="page"  value="flg_plugin">';
     echo '<input type="hidden"  name="edit"  value="true">';
     echo "<p style='margin-top: 20px;'><input name='submit' type='submit' value='Edit keys'/></p>";
     echo "</form>";
     echo "</div>";
   }

   // add warning note if Extended Profiles are disabled
   // (they are required for the Search functionality to work)
   if ( !bp_is_active( 'xprofile' ) ) {
      ?>
      <p style='margin-top:20px;color:red;'>
        <b>BuddyPress Extended Profiles are disabled!</b><br/>
        This plugin requires BuddyPress Extended Profiles. Please enable them in your BuddyPress
        settings, or contact <a href='mailto:tech@feeligo.com'>tech@feeligo.com</a> if unable
        to do so.
      <?php
   }

   echo '<img src="https://api.keen.io/3.0/projects/520a469c897a2c5f7d000004/events/admin_opened?api_key=20d6cd14399b567600b420d7c7980cab1219c6324e00ece8fcff252f2ed34e58d704cca81f8eed59d49b95d127367c305de42d51e5f7b19035171ceffbcd4ead7ca72b17089f96d84d65d8faa2cec7d8e7ef2c388417156b7f0e29521a91f71ab1e62c18a5eaaeac5f99b119dd46d2d1&data='.$t_data.'"></img>';
 }


// add the admin settings and such
add_action('admin_init', 'flg_plugin_admin_init');


function flg_plugin_admin_init() {
  register_setting( 'flg_plugin_options', 'flg_plugin_options', 'flg_plugin_options_validate' );
  add_settings_section('plugin_main', 'Your Feeligo Keys', 'flg_plugin_section_text', 'flg_plugin');
  add_settings_field('plugin_publickey_string', 'API key', 'flg_plugin_publickey_string', 'flg_plugin', 'plugin_main');
  add_settings_field('plugin_secretkey_string', 'Secret key', 'flg_plugin_secretkey_string', 'flg_plugin', 'plugin_main');
}


function flg_plugin_section_text() {
  echo "<p style='font-size: 0.9em; font-style: italic;'>The following keys establish a secure connection between the Giftbar and your website.<br/>They should be the same as the ones in your <a href='http://www.feeligo.com/dashboard/sign_in?utm_source=WP+Admin&utm_medium=Text+Link&utm_campaign=BuddyPress+plugin'>Feeligo dashboard</a></p>";
}


// callback for inputs
function flg_plugin_publickey_string() {
  $options = get_option('flg_plugin_options');
  echo "<input id='flg_plugin_publickey_string' name='flg_plugin_options[public_key]' size='40' type='text' value='{$options['public_key']}' />";
}


function flg_plugin_secretkey_string() {
  $options = get_option('flg_plugin_options');
  echo "<input id='flg_plugin_secretkey_string' name='flg_plugin_options[secret_key]' size='40' type='text' value='{$options['secret_key']}' />";
}


// validate our options - need to be hexadecimals
function flg_plugin_options_validate($input) {
  $newinput['public_key'] = trim($input['public_key']);
  $newinput['secret_key'] = trim($input['secret_key']);
  if ( !preg_match( '/^\S+$/i', $newinput['public_key'] ) ) {
    $newinput['public_key'] = '';
  }
  if ( !preg_match( '/^\S+$/i', $newinput['secret_key'] ) ) {
    $newinput['secret_key'] = '';
  }
  return $newinput;
}
?>