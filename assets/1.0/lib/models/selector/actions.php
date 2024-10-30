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
 * class FeeligoBuddypressModelActionsSelector
 *
 * allows to publish actions in the newsfeed
 **/

 
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

require_once(str_replace('//','/',dirname(__FILE__).'/').'../../../sdk/interfaces/actions_selector.php');

class FeeligoBuddypressModelActionsSelector implements FeeligoActionsSelector {
  
  /**
   * Posts the update to the activity feed
   *
   * @param FeeligoActionUserSentGiftToUserPayload payload with action data
   * @return FeeligoBuddypressModelActionAdapter adapter for the last created action
   */
  function create($payload) {
    global $bp;

    // at the moment, only "user sent gift to user" action is supported
    if ( $payload->name() != 'user_sent_gift_to_user' ) return null;

    // protection against missing / incomplete installs of BP
    if ( !function_exists('bp_activity_add') ) return null;
    
    // build the strings for the action and content fields of the activity
    $action = $this->_get_activity_action($payload);
    $content = $this->_get_activity_content($payload);

    // publish two activities, one for the sender and one for the recipient
    $act = null;
    foreach (array($payload->adapter_recipient(), $payload->adapter_sender()) as $a) {
      $act = bp_activity_add( array( 
        'id' => false,
        'user_id' => $a->id(),
        'action' => $action,
        'content' => $content,
        'component' => 'all', 
        'type' => 'activity_update', 
        'hide_sitewide' => !($act === null) // only the 1st activity is sitewide
      ) );
      if ( !$act ) {
        error_log("mod/feeligo : failed posting to activity feed");
        return null;
      }
    }

    // return an adapter with the ID of the last created activity record
    return new FeeligoBuddypressModelActionAdapter($act);
  }
  
  
  /**
   * builds the string to be used as the 'action' field of the activity
   *
   * @param FeeligoActionUserSentGiftToUserPayload payload with action data
   * @return the action HTML code
   */
  protected function _get_activity_action($payload) {
    
    // get localized raw body from payload
    $m = $payload->localized_raw_body(get_bloginfo('language', 'raw'));

    // replace subject with link to sender
    $m = str_replace("\${subject}",
      $this->_get_activity_action_link_to_user($payload->adapter_sender()), $m);

    // replace direct_object with link to gift
    $m = str_replace("\${direct_object}",
      $this->_get_activity_action_link_to_gift($payload->gift()), $m);

    // replace indirect_object with link to recipient
    $m = str_replace("\${indirect_object}",
      $this->_get_activity_action_link_to_user($payload->adapter_recipient()), $m);

    // return message
    return $m;
  }


  /** 
   * builds the <a> tag with a link to the user's profile page and feeligo data-attrs
   *
   * @param FeeligoBuddypressUser user adapter
   * @return string the HTML code of the <a> tag
   */
  protected function _get_activity_action_link_to_user($user_adapter) {
    return '<a href="'.$user_adapter->link().'" data-flg-role="link" data-flg-type="user"
        data-flg-id="'.$user_adapter->id().'"
        data-flg-source="action"">'.$user_adapter->name().'</a>';
  }


  /**
   * builds the <span> tag with the name of the gift and feeligo data-attrs
   *
   * @param StdClass gift object
   * @return string the HTML code of the <span> tag
   */
  protected function _get_activity_action_link_to_gift($gift) {
    return '<span data-flg-role="link" data-flg-type="gift"
        data-flg-id="'.$gift->id.'"
        data-flg-source="action">'.$gift->name.'</span>';
  }


  /**
   * builds the string to be used as the 'content' field of the activity
   * contains the image of the gift as well as the message if present
   *
   * @param FeeligoActionUserSentGiftToUserPayload payload with action data
   * @return the content HTML code
   */
  protected function _get_activity_content($payload) {

    $g = $payload->gift();
    
    $c = "<img alt='flg_feed' src='".($payload->medium_url('medium', '60x72'))."'
      data-flg-role='link' data-flg-type='gift' data-flg-source='action'
      data-flg-id='".$g->id."'/> ";

    if ( $g->message !== null && strlen($g->message) ) {
      $c .= '<p class="sent-gift-message">&laquo;';
      $c .= htmlentities(utf8_decode($g->message));
      $c .= '&raquo;</p>';
    }
    return $c;
  }

}
?>