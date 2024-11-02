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
 * class FeeligoBuddypressGiftbarApp
 *
 * extends FeeligoGiftbarApp to provide a method which renders the HTML to be
 * injected in the footer of the Buddypress site
 **/

class FeeligoBuddypressGiftbarApp extends FeeligoGiftbarApp {

  /**
   * Disables the GiftBar if Extended Profiles are disabled
   * 
   * this is because the API Endpoint for BuddyPress currently relies on the 
   * `bp_core_get_users` function, which as of BP 1.6 requires Extended Profiles.
   *
   * See `filters/flg_core_get_paged_users_sql.php` for details.
   */
  public function is_enabled() {
    return parent::is_enabled() && bp_is_active( 'xprofile' );
  }


  /**
   * renders the HTML with the Giftbar's initialization JS code and placeholder <div>
   *
   * @return null
   */
  public function render_footer_html() {
    if ( $this->is_enabled() ) {
      echo "<script type='text/javascript'>".$this->initialization_js()."</script>";
      echo "<script type='text/javascript' src='".$this->loader_js_url()."'></script>";
      echo "<div id='flg_giftbar_container'></div>";
    }
  }

}


/**
 * called by a Wordpress hook to render the HTML 
 *
 * @return null
 */
function flg_giftbar_render_footer_html() {
  $giftbar = new FeeligoBuddypressGiftbarApp( FeeligoBuddypressApi::_() );
  $giftbar->render_footer_html();
}
?>