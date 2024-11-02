<?php
/**
 * Custom filter to be added to bp_core_get_paged_users_sql.
 * Attempts to patch BuddyPress 1.6's crappy "alphabetical" `get_users` query
 *
 * Without this filter, requesting all users in alphabetical order, or searching
 * for users and retrieving results in alphabetical order, using the BP API
 * `bp_core_get_users` function, simply would not work in some cases.
 * This is because it reles on finding the user's name in the wp_bp_xprofile_data
 * table, which for some reason is not always up to date.
 * Instead, this filter also considers the WordPress display_name, user_nicename
 * and user_login fields of the wp_users table, which gives more consistent results.
 *
 * At the moment, the filter is only added in the Feeligo API endpoint and not
 * everywhere, to prevent undesirable side effects.
 *
 *
 * TODO: two alternatives
 *   - get rid of the bp_core_get_users call altogether, and write good old SQL
 *     in the plugin. This would have the nice side effect of suppressing the
 *     dependency on Extended Profiles.
 *   - OR improve this filter by implementing the same functionality into the
 *     bp_core_get_total_users_sql function, which suffers from the same problem
 */

function flg_core_get_paged_users_sql($sql, $fragments) {

  // make sure the query is valid even if $bp->profile->table_name_data is NULL
  flg__ensure_xprofile_table_name($sql, $fragments);

  // patch alphabetical and search queries
  $is_alpha = flg__is_alpha($fragments);
  $is_search = flg__is_search($fragments);

  if ($is_alpha || $is_search) {

    // instead of using the pd.value column for alphabetical ranking, we use the first non-null
    // value between:
    $robustname = "COALESCE(pd.value, u.display_name, u.user_nicename, u.user_login)";
    if ($is_search) $robustname = str_replace('pd.', 'spd.', $robustname);

    // patch `select_alpha`, replace pd.value with the more robust name alias
    flg__patch_fragment('/pd\.value/',
      $robustname, $sql, $fragments, 'select_alpha');

    // patch `join_profiledata_{alpha|search}`, use an OUTER JOIN instead of a JOIN
    flg__patch_fragment('/LEFT\s+JOIN/',
      'LEFT OUTER JOIN', $sql, $fragments, 'join_profiledata_alpha');
    flg__patch_fragment('/LEFT\s+JOIN/',
      'LEFT OUTER JOIN', $sql, $fragments, 'join_profiledata_search');

    // patch `where_alpha`, allowing pd.field_id to be NULL (due to the OUTER JOIN)
    flg__patch_fragment('/AND pd\.field_id = 1/',
      'AND (pd.field_id = 1 OR pd.field_id IS NULL)', $sql, $fragments, 'where_alpha');

    // patch `where_searchterms`, using $robustname for the comparison instead of spd.value
    flg__patch_fragment('/AND spd\.value LIKE/',
      'AND '.$robustname.' LIKE', $sql, $fragments, 'where_searchterms');

    // patch the order
    flg__patch_fragment('/pd\.value/', $robustname, $sql, $fragments, 0);
  }

  return $sql;
}


/**
 * Checks the SQL fragments to determine whether it is a $type='alphabetical' query
 */
function flg__is_alpha($fragments) {
  return isset($fragments['join_profiledata_alpha'])
      && isset($fragments['select_alpha'])
      && isset($fragments['where_alpha']);
}


/**
 * Checks the SQL fragments to determine whether it is a 'search' query
 */
function flg__is_search($fragments) {
  return isset($fragments['join_profiledata_search'])
      && isset($fragments['where_searchterms']);
}


/**
 * Patches a single fragment, by key, updating both $fragments and $sql.
 *
 * Replaces $search with $replace in $fragments[$key], updating the $fragments variable
 * passed by reference.
 * Then replaces the fragment with its new value in the $sql string.
 */
function flg__patch_fragment($search, $replace, &$sql, &$fragments, $key) {
  if (isset($fragments[$key])) {
    $patched = preg_replace($search, $replace, $fragments[$key]);
    $sql = str_replace($fragments[$key], $patched, $sql);
    $fragments[$key] = $patched;
  }
}


/**
 * Ensures the name of the `wp_bp_xprofile_data` is in the query.
 *
 * If $bp->profile->table_name_data is NULL, the `wp_bp_xprofile_data` table name
 * will be missing from the query, which causes the query to fail.
 * This only affects query of type 'alphabetical' and 'search' which use that table.
 * [see buddypress/bp-core/bp-core-classes.php:232]
 *
 * This happens when Extended Profiles are disabled in the BuddyPress settings, and
 * is NOT checked by `BP_Core_User::get_users` when building the query.
 */
function flg__ensure_xprofile_table_name(&$sql, &$fragments) {
  global $wpdb;
  if ($bp->profile->table_name_data === NULL) {
    foreach(array(
      'join_profiledata_search',
      'join_profiledata_alpha'
    ) as $key) {
      flg__patch_fragment('/JOIN\s+pd/',
        'JOIN '.$wpdb->prefix.'bp_xprofile_data pd', $sql, $fragments, $key);  
    }
  }
}
