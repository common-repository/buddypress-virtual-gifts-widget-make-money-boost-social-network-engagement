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
 * function flg_activity_allowed_tags
 *
 * Attached to the bp_activity_allowed_tags filter, it allows feeligo
 * custom HTML5 data attributes on <a>, <img> and <span> tags
 */

/**
 * @param array $allowed tags that are already allowed
 * @return array allowed tags
 */
function flg_activity_allowed_tags ($allowed) {
  $flg_allowed = array(
    'data-flg-id',
    'data-flg-type',
    'data-flg-role',
    'data-flg-source',
  );
  foreach($flg_allowed as $attr) {
    foreach(array('a','span','img') as $tag) {
      if (!isset($allowed[$tag])) $allowed[$tag] = array();
      $allowed[$tag][$attr] = true;
    }
  }
  return $allowed;
}
?>