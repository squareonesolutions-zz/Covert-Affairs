<?php
/*
	Plugin Name: Fold Category List
	Version: 2.02
	Plugin URI: http://www.webspaceworks.com/resources/cat/wp-plugins/31/
	Description: Provides PHP functions to display a folding category tree
	Author: Rob Schumann
	Author URI: http://www.webspaceworks.com/
*/
/*
	v2.02: New functionality [07 January, 2008]
		Added case to capture category of single post... In situations where more than one category assignemnt will use first listed category.
		Allowed easier changes to version switching
	v2.01: Header correction of version number [07 January, 2008][not released]
		Corrected version number announcement in header of plugin
	v2.00: Compatibility release for WP2.3 [06 january, 2008]
		Compatibility with underlying database changes introduced with WP 2.3. (Derived and then extended from mods implemented by Justin Fraser)
		Remains compatible with previous releases.
		Support for 'orderby' and 'order' for WP2.3+, and adopting default ordering consistent with WP2.3+ standard defaults (versions prior to 2.3 retain previous defaults)
	v1.11: Compatibility release for WP2.1 & Bugfix [20 February, 2007]
		Further changes to resolve issues with WP 2.1.
	v1.1: Compatibility release for WP2.1 [20 February, 2007]
		Changes to post count retrieval to address database changes implemented with WP 2.1.
	v1.0: Public release [18 February, 2007]
		Added running counts so that post-counts for categories can optionally include posts in child categories.
		Introduces revisions to 'hide_empty' and 'optioncount' arguments to allow running counts to work sensibly
	v1.0b6: Bug fix [11 September, 2006]
		Fixed bug that prevented description truncation from working on sub-categories.
	v1.0b5: Feature enhancement [11 September, 2006]
		Added Link deadening for the current category.
		Added 'current_cat_ancestor' class to parent categories of the current category.
	v1.0b4: Feature enhancement [28 January, 2006]
		Added wswwpx_category_description and the ability to specify a truncated description for use in category link title text (tooltips)
	v1.0b3: Bug fix & enhancement [5 January, 2006]
		Internal changes for better compatibility with WP Core, especially for WP2.0 & revised permalinks system
		Added _wswwpx_category_get_name function to obtain the name (title) for a specific category upon request.
	v1.0b2: Bug fix [8 November, 2005]
			Fix for when ancestors array wasn't being initialised.
	v1.0b1: Bug fix [8 November, 2005]
			Consolidates permalink support and implements progressive hierachical display, consistent with Fold Page List
			Reorganisation of code, extra commenting.
	v0.9a4: Bug fix [7 November, 2005]
			Fix to work under permalinks using category names instead of ids (internal build)
	v0.9a3: Bug fix [7 November, 2005]
			Re-implement based on 'list_cats' from wp 1.5.2. Fixes non-folding, non-sorting behaviour of previous alphas
	v0.9a2: Security fix [28 September, 2005]
			Check for integer $child added to _wswwpx_category_get_parent_id and _wswwpx_category_get_child_ids
	v0.9a1: Public alpha release [05 June, 2005]
	Copyright (c) 2005  Rob Schumann  (email : robs_wp@webspaceworks.com)
	Released under the GPL license
	http://www.gnu.org/licenses/gpl.txt

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

define ('VERSION_SWITCH', 2.3);

/*
 * _wswwpx_category_get_id
 *	 Converts from cat_name input to ID output
 *  - $cat is the identifier for a category.
 *			If a non-numeric string, find the corresponding ID for the category.
 *			If it's numeric, we keep it as the wanted value
 *			If neither criterion is met, return as zero
 *  - returns the ID of the given category
 */
function _wswwpx_category_get_id ( $cat = '' ) {
	global $wpdb;
	// Make sure there is a cat identifier to process
	if (($wp_version = get_bloginfo('version')) < VERSION_SWITCH) {
		$field = 'cat_ID';
		$table = $wpdb->categories;
		$where = 'category_nicename';
	} else {
		$field = 'term_ID';
		$table = $wpdb->terms;
		$where = 'name';
	}
	if ( !is_numeric($cat) && strlen($cat) > 0 ) {
		//
		//	This next bit to prevent SQL insertion attacks through the argument list.
		//		Breaks on the first semi-colon encountered and discards the trailing part.
		//		Then strips any trailing '/' character
		//
		$cats = explode(';', $cat, 2);
		$cats = explode('/', $cats[0]);
		$n = count($cats);
		$cats = array_reverse(array_slice($cats, 0, $n-1));
		$result = $wpdb->get_var("
									SELECT $field
										FROM $table
										WHERE $where = '{$cats[0]}'");
	} else if (is_numeric($cat)) {
		// ... Keep an existing numeric value
		$result = $cat;
	} else {
		// ... or set a zero result.
		$result = 0;
	}
	//
	return $result;
}

/*
 * _wswwpx_category_get_parent_id
 *  - $child is the ID of a category
 *  - returns the ID of the parent of the given category
 */
function _wswwpx_category_get_parent_id ( $child = 0 ) {
	global $wpdb;
	// Make sure there is a child ID to process
	if (($wp_version = get_bloginfo('version')) < VERSION_SWITCH) {
		$field = 'category_parent';
		$table = $wpdb->categories;
		$where = 'cat_ID';
	} else {
		$field = 'parent';
		$table = $wpdb->term_taxonomy;
		$where = 'term_ID';
	}
	if ( is_numeric($child) && $child > 0 ) {
		$result = $wpdb->get_var("
									SELECT $field
										FROM $table
										WHERE $where = $child");
	} else {
		// ... or set a zero result.
		$result = 0;
	}
	//
	return $result;
}
/*
 * _wswwpx_category_get_name
 *  - $cid is the ID of a category
 *  - returns the name of the given category
 */
function _wswwpx_category_get_name ( $cid = 0 ) {
	global $wpdb;
	// Make sure there is a child ID to process
	if (($wp_version = get_bloginfo('version')) < VERSION_SWITCH) {
		$field = 'cat_name';
		$table = $wpdb->categories;
		$where = 'cat_ID';
	} else {
		$field = 'name';
		$table = $wpdb->terms;
		$where = 'term_ID';
	}
	if ( is_numeric($cid) && $cid > 0 ) {
		$result = $wpdb->get_var("
									SELECT $field
										FROM $table
										WHERE $where = $cid");
	} else {
		// ... or set a null result.
		$result = NULL;
	}
	//
	return $result;
}

/*
 * get_ancestor_ids
 *  - $child is the ID of a category
 *  - returns an array of IDs of all ancestors of the requested category
 *  - default sort order is top down.
 */

function _wswwpx_category_get_ancestor_ids ( $child = 0, $inclusive=true, $topdown=true ) {
	//
	//	Make sure we are dealing with a $child that is a numeric ID ID and not a string cat_name
	//	Convert as necessary
	//
	$child = _wswwpx_category_get_id ($child);
	//
	//	And start processing
	//
	if ( $child && $inclusive ) $ancestors[] = $child;
 	while ($parent = _wswwpx_category_get_parent_id ( $child ) ) {
 		$ancestors[] = $parent;
 		$child = $parent;
 	}
 	//	If there are ancestors, test for resorting, and apply
 	if ($ancestors && $topdown) krsort($ancestors);
	if ( !$ancestors ) $ancestors[] = 0;
 	//
 	return $ancestors;
 }

/*
 * _wswwpx_category_get_child_ids
 *  - $parent is the ID of the parent category
 *  - returns an associative array containing the IDs of the children
 *    of the parent category
 */
function _wswwpx_category_get_child_ids ( $parent = 0 ) {
	global $wpdb;
	if (($wp_version = get_bloginfo('version')) < VERSION_SWITCH) {
		$field = 'cat_ID';
		$table = $wpdb->categories;
		$where = 'category_parent';
	} else {
		$field = 'term_ID';
		$table = $wpdb->term_taxonomy;
		$where = 'parent';
	}

	if ( is_numeric($parent) && $parent > 0 ) {
		// Get the ID of the parent.
		$results = $wpdb->get_results("
									SELECT $field
										 FROM $table
										 WHERE $where = $parent");

 		if ($results) {
			foreach ($results AS $r) {
			 	foreach ($r AS $v) {
			 		$result[] = $v;
			 	}
			 }
		} else {
			$result = false;
		}
	} else {
		$categories = get_cats();
		if (($wp_version = get_bloginfo('version')) < VERSION_SWITCH) {
			foreach ($categories AS $category) {
				$result[]=$category->cat_ID;
			}
		} else {
			foreach ($categories AS $category) {
				$result[]=$category->term_ID;
			}
		}

//		$result = false;
	}
	//
	return $result;
}

 /*
  * _wswwpx_category_get_descendant_ids
  *  - $parent is the ID of a category
  *  - $inclusive is a switch determining whether the parent ID is included in the returned array. Defaults TRUE
  *  - returns an array of IDs of all descendents of the requested category
  */
function _wswwpx_category_get_descendant_ids ( $parent = 0, $inclusive=true ) {
 	if ( $parent && $inclusive ) $descendants[] = $parent;
 	if ( $offspring = _wswwpx_category_get_child_ids ( $parent ) ) {
		if (is_array($offspring) && is_array($descendants) ) {
			$descendants = array_merge($descendants, $offspring);
		} else  if (is_array($offspring)){
			$descendants = $offspring;
		}
 		foreach ( $offspring as $child ) {
 			$grandchildren = _wswwpx_category_get_descendant_ids ( $child, false );
	 		if (is_array($grandchildren) && is_array($descendants)) {
	 			$descendants = @array_merge($descendants, $grandchildren);
	 		} else if (is_array($grandchildren)) {
	 			$descendants = $grandchildren;
	 		}
 		}
 	}
 	//
 	return $descendants;
 }



/*	F R O N T   E N D   Functions
 *-----------------------------------------------------------------------
 *	The following are taken from WP itself, and modified.
 * Modifed versions of:
 *		wp_list_cats: wswwpx_fold_category_list
 *		list_cats:    wswwpx_list_cats
 *
 *	Original comments from WP are left in place
 *
 */
   function wswwpx_fold_category_list ($args = '') {
   	parse_str($args, $r);
   	if (!isset($r['optionall'])) $r['optionall'] = 0;
       if (!isset($r['all'])) $r['all'] = 'All';
   	if (!isset($r['sort_column'])) $r['sort_column'] = 'ID';
   	if (!isset($r['sort_order'])) $r['sort_order'] = 'asc';
   	if (!isset($r['file'])) $r['file'] = '';
   	if (!isset($r['list'])) $r['list'] = true;
   	if (!isset($r['optiondates'])) $r['optiondates'] = 0;
   	if (!isset($r['optioncount'])) $r['optioncount'] = 0;
   	if (!isset($r['hide_empty'])) $r['hide_empty'] = 1;
   	if (!isset($r['use_desc_for_title'])) $r['use_desc_for_title'] = 1;
   	if (!isset($r['children'])) $r['children'] = true;
   	if (!isset($r['child_of'])) $r['child_of'] = 0;
   	if (!isset($r['categories'])) $r['categories'] = 0;
   	if (!isset($r['recurse'])) $r['recurse'] = 0;
   	if (!isset($r['feed'])) $r['feed'] = '';
   	if (!isset($r['feed_image'])) $r['feed_image'] = '';
   	if (!isset($r['exclude'])) $r['exclude'] = '';
   	if (!isset($r['hierarchical'])) $r['hierarchical'] = true;
// WSW Extras
   	if (isset($r['cut_desc'])) $cut_desc = $r['cut_desc'];
   	if (isset($r['expand']))  $expandlist = $r['expand'];

	if (($wp_version = get_bloginfo('version')) < VERSION_SWITCH) {
		wswwpx_list_cats($r['optionall'], $r['all'], $r['sort_column'], $r['sort_order'], $r['file'],	$r['list'], $r['optiondates'], $r['optioncount'], $r['hide_empty'], $r['use_desc_for_title'], $r['children'], $r['child_of'], $r['categories'], $r['recurse'], $r['feed'], $r['feed_image'], $r['exclude'], $r['hierarchical'], $cut_desc, $expandlist);
	} else {
		if (!isset($r['orderby'])) $r['orderby'] = 'name';
		if (!isset($r['order']))  $r['order'] = 'ASC';
		if (!isset($r['title_li'])) $r['title_li'] = __('Categories');
	   	if (!isset($r['style'])) $r['style'] = 'list';
		wswwpx_list_cats23($r['optionall'], $r['all'], $r['title_li'], $r['orderby'], $r['order'], $r['file'],	$r['style'], $r['optiondates'], $r['optioncount'], $r['hide_empty'], $r['use_desc_for_title'], $r['children'], $r['child_of'], $r['categories'], $r['recurse'], $r['feed'], $r['feed_image'], $r['exclude'], $r['hierarchical'], $cut_desc, $expandlist);
	}
   }
/*
//
//	Main driving function for WP versions prior to 2.3.1
//
*/
   function wswwpx_list_cats($optionall = 1, $all = 'All', $sort_column = 'ID', $sort_order = 'asc', $file = '', $list = true, $optiondates = 0, $optioncount = 0, $hide_empty = 1, $use_desc_for_title = 1, $children=FALSE, $child_of=0, $categories=0, $recurse=0, $feed = '', $feed_image = '', $exclude = '', $hierarchical=FALSE, $cut_desc='', $expandlist=0) {
   	global $wpdb, $category_posts, $wp_query;

//	Added for folding functionality... fix for permalink compatibility and modified after further suggestion from Laurence O.
		if (is_category()) {
			$current_cat = $wp_query->get_queried_object_id();
			$all_ancestors = _wswwpx_category_get_ancestor_ids($current_cat);
		} elseif (is_single()) {
			//
			//	Single posts (courtesy http://www.pqdb.com/wordpress-articles/using-wordpress-as-a-cms-content-management-system/)
			//	Returns the first category if more than one assignment, so may break navigational 'clarity'
			//
			$cat = get_the_category();
			$cat = $cat[0];
			$cat_ID = get_cat_ID($cat_name=$cat->cat_name);
			// get category ID for given category name
			$current_cat = $cat_ID;
			$all_ancestors = _wswwpx_category_get_ancestor_ids($current_cat);
		} else {
			//
			//	Default to zero for all other cases.
			//
			$all_ancestors[] = 0;
		}
/*//	Old version... replaced by above for greater compatibility with WP CORE
		if (isset($_GET['category_name'])) {
			$all_ancestors = _wswwpx_category_get_ancestor_ids($_GET['category_name']);
		} else if (isset($_GET['cat'])) {
			$all_ancestors = _wswwpx_category_get_ancestor_ids($_GET['cat']);
		} else {
			//
			//	Default to zero for all other cases.
			//
			$all_ancestors[] = 0;
		} */
//	End add
   	// Optiondates now works
   	if ('' == $file) {
   		$file = get_settings('home') . '/';
   	}

   	$exclusions = '';
   	if (!empty($exclude)) {
   		$excats = preg_split('/[\s,]+/',$exclude);
   		if (count($excats)) {
   			foreach ($excats as $excat) {
   				$exclusions .= ' AND cat_ID <> ' . intval($excat) . ' ';
   			}
   		}
   	}

   	$exclusions = apply_filters('list_cats_exclusions', $exclusions);

   	if (intval($categories)==0){
   		$sort_column = 'cat_'.$sort_column;

   		$query  = "
   			SELECT cat_ID, cat_name, category_nicename, category_description, category_parent
   			FROM $wpdb->categories
   			WHERE cat_ID > 0 $exclusions
   			ORDER BY $sort_column $sort_order";

   		$categories = $wpdb->get_results($query);
   	}
   	if (!count($category_posts)) {
   		$now = current_time('mysql', 1);
   		$sql = "SELECT cat_ID,
   					COUNT($wpdb->post2cat.post_id) AS cat_count
   					FROM $wpdb->categories 
   					INNER JOIN $wpdb->post2cat ON (cat_ID = category_id)
   					INNER JOIN $wpdb->posts ON (ID = post_id)
   					WHERE post_status = 'publish'";
   		if (($wp_version = get_bloginfo('version')) >= 2.1) $sql .= " AND post_type = 'post'";
   		$sql .= " AND post_date_gmt < '$now' $exclusions
   					GROUP BY category_id";
   		$cat_counts = $wpdb->get_results($sql);
           if (! empty($cat_counts)) {
               foreach ($cat_counts as $cat_count) {
                   if (1 != intval($hide_empty) || $cat_count > 0) {
                       $category_posts["$cat_count->cat_ID"] = $cat_count->cat_count;
                   }
               }
           }
   	}
   	
   	if ( $optiondates ) {
   		$cat_dates = $wpdb->get_results("	SELECT category_id,
   		UNIX_TIMESTAMP( MAX(post_date) ) AS ts
   		FROM $wpdb->posts, $wpdb->post2cat
   		WHERE post_status = 'publish' AND post_id = ID $exclusions
   		GROUP BY category_id");
   		foreach ($cat_dates as $cat_date) {
   			$category_timestamp["$cat_date->category_id"] = $cat_date->ts;
   		}
   	}
   	
   	$num_found=0;
   	$thelist = "";



   	foreach ($categories as $category) {
//
//	Add ability to have running totals, not just in category counts in the list.
//
		$all_children = _wswwpx_category_get_descendant_ids($category->cat_ID);
		$runningTotal = 0;
		foreach ($category_posts AS $k=>$v) {
			if (@in_array($k, $all_children) ) $runningTotal += $v;
		}
   		if (((intval($hide_empty) == 0 || (intval($hide_empty) == 2 && $runningTotal > 0)) || isset($category_posts["$category->cat_ID"])) && (!$hierarchical || $category->category_parent == $child_of) ) {
   			$num_found++;
//
//	WSW Deaden the link to the current category, and tag ancestor links for different styling
//	Adds one further conditional
//
			if ($category->cat_ID == $current_cat) {
				$link = wp_specialchars($category->cat_name);
			} else {
				//
				//	WSW Check for whether an ancestor of the current category
				//
				if (in_array($category->cat_ID, $all_ancestors)) {
					$link_class = 'class="current_cat_ancestor"';
				} else {
					$link_class = '';
				}
				$link = '<a ' . $link_class . ' href="'.get_category_link($category->cat_ID).'" ';
				if ($use_desc_for_title == 0 || empty($category->category_description)) {
					$link .= 'title="'. sprintf(__("View all posts filed under %s"), wp_specialchars($category->cat_name)) . '"';
				} else {
//
//	WSW change to allow for truncated descriptions within link titles.
//
//   					$link .= 'title="' . wp_specialchars(apply_filters('category_description',$category->category_description,$category)) . '"';
						$link .= 'title="' . wp_specialchars(wswwpx_category_description($category, $cut_desc, 0)) . '"';
				}
				$link .= '>';
				$link .= apply_filters('list_cats', $category->cat_name, $category).'</a>';
			}
//
//	End current link deadening...
//

   			if ( (! empty($feed_image)) || (! empty($feed)) ) {
   				
   				$link .= ' ';

   				if (empty($feed_image)) {
   					$link .= '(';
   				}

   				$link .= '<a href="' . get_category_rss_link(0, $category->cat_ID, $category->category_nicename)  . '"';

   				if ( !empty($feed) ) {
   					$title =  ' title="' . $feed . '"';
   					$alt = ' alt="' . $feed . '"';
   					$name = $feed;
   					$link .= $title;
   				}

   				$link .= '>';

   				if (! empty($feed_image)) {
   					$link .= "<img src='$feed_image' $alt$title" . ' />';
   				} else {
   					$link .= $name;
   				}
   				
   				$link .= '</a>';

   				if (empty($feed_image)) {
   					$link .= ')';
   				}
   			}
   			if (intval($optioncount) == 1) {
   				$link .= ' ('.intval($category_posts["$category->cat_ID"]).')';
   			} else if (intval($optioncount) == 2) {
//
//	Handle running count option here...
//
    			$link .= ' ('.$runningTotal.')';
  			}
   			if ( $optiondates ) {
   				if ( $optiondates == 1 ) $optiondates = 'Y-m-d';
   				$link .= ' ' . gmdate($optiondates, $category_timestamp["$category->cat_ID"]);
   			}
   			if ($list) {
   				$thelist .= "\t<li>$link\n";
   			} else {
   				$thelist .= "\t$link<br />\n";
   			}

//	Extra 'if' added for folding functionality
   			if (in_array($category->cat_ID, $all_ancestors) || $expandlist == 1) {
	   			if ($hierarchical && $children) $thelist .= wswwpx_list_cats($optionall, $all, $sort_column, $sort_order, $file, $list, $optiondates, $optioncount, $hide_empty, $use_desc_for_title, $hierarchical, $category->cat_ID, $categories, 1, $feed, $feed_image, $exclude, $hierarchical, $cut_desc, $expandlist);
			}
//	End add
   			if ($list) $thelist .= "</li>\n";
   		}
   	}
   	if (!$num_found && !$child_of){
   		if ($list) {
   			$before = '<li>';
   			$after = '</li>';
   		}
   		echo $before . __("No categories") . $after . "\n";
   		return;
   	}
   	if ($list && $child_of && $num_found && $recurse) {
   		$pre = "\t\t<ul class='children'>";
   		$post = "\t\t</ul>\n";
   	} else {
   		$pre = $post = '';
   	}
   	$thelist = $pre . $thelist . $post;
   	if ($recurse) {
   		return $thelist;
   	}
   	echo apply_filters('list_cats', $thelist);
   }
/*
//
//	Main driving function for WP versions after WP version 2.3
//
*/
   function wswwpx_list_cats23($optionall = 1, $all = 'All', $title_li='Categories', $sort_column = 'ID', $sort_order = 'asc', $file = '', $style = 'list', $optiondates = 0, $optioncount = 0, $hide_empty = 1, $use_desc_for_title = 1, $children=FALSE, $child_of=0, $categories=0, $recurse=0, $feed = '', $feed_image = '', $exclude = '', $hierarchical=FALSE, $cut_desc='', $expandlist=0) {
   	global $wpdb, $category_posts, $wp_query;

//	Added for folding functionality... fix for permalink compatibility and modified after further suggestion from Laurence O.
		if (is_category()) {
			$current_cat = $wp_query->get_queried_object_id();
			$all_ancestors = _wswwpx_category_get_ancestor_ids($current_cat);
		} elseif (is_single()) {
			//
			//	Single posts (courtesy http://www.pqdb.com/wordpress-articles/using-wordpress-as-a-cms-content-management-system/)
			//	Returns the first category if more than one assignment, so may break navigational 'clarity'
			//
			$cat = get_the_category();
			$cat = $cat[0];
			$cat_ID = get_cat_ID($cat_name=$cat->cat_name);
			// get category ID for given category name
			$current_cat = $cat_ID;
			$all_ancestors = _wswwpx_category_get_ancestor_ids($current_cat);
		} else {
			//
			//	Default to zero for all other cases.
			//
			$all_ancestors[] = 0;
		}
/*//	Old version... replaced by above for greater compatibility with WP CORE
		if (isset($_GET['category_name'])) {
			$all_ancestors = _wswwpx_category_get_ancestor_ids($_GET['category_name']);
		} else if (isset($_GET['cat'])) {
			$all_ancestors = _wswwpx_category_get_ancestor_ids($_GET['cat']);
		} else {
			//
			//	Default to zero for all other cases.
			//
			$all_ancestors[] = 0;
		} */
//	End add
   	// Optiondates now works
   	if ('' == $file) {
   		$file = get_settings('home') . '/';
   	}

   	$exclusions = '';
   	if (!empty($exclude)) {
   		$excats = preg_split('/[\s,]+/',$exclude);
   		if (count($excats)) {
   			foreach ($excats as $excat) {
   				$exclusions .= ' AND t.term_ID <> ' . intval($excat) . ' ';
   			}
   		}
   	}

   	$exclusions = apply_filters('list_cats_exclusions', $exclusions);

   	if (intval($categories)==0){
   		if ($sort_column == 'ID') $sort_column = 'term_'.$sort_column;

   		$query  = "
   			SELECT t.term_ID, name, description, parent
   			FROM $wpdb->terms t inner join $wpdb->term_taxonomy tt on tt.term_id=t.term_id
   			WHERE (t.term_id > 0 AND taxonomy='category') $exclusions
   			ORDER BY $sort_column $sort_order";

   		$categories = $wpdb->get_results($query);
   	}
   	if (!count($category_posts)) {
   		$now = current_time('mysql', 1);
   		$sql = "SELECT t.term_ID,
   					COUNT(tr.term_taxonomy_id) AS cat_count
   					FROM $wpdb->term_relationships tr  
   					INNER JOIN $wpdb->term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
   					INNER JOIN $wpdb->terms t ON (t.term_id = tt.term_id)
   					INNER JOIN $wpdb->posts p ON (p.ID = tr.object_id)
   					WHERE post_status = 'publish'";
   		if (($wp_version = get_bloginfo('version')) >= 2.1) $sql .= " AND post_type = 'post'";
   		$sql .= " AND post_date_gmt < '$now' $exclusions
   					GROUP BY t.term_id";
   		$cat_counts = $wpdb->get_results($sql);
		if (! empty($cat_counts)) {
		   foreach ($cat_counts as $cat_count) {
			   if (1 != intval($hide_empty) || $cat_count > 0) {
				   $category_posts["$cat_count->term_ID"] = $cat_count->cat_count;
			   }
		   }
	   }
   	}
   	
   	if ( $optiondates ) {
   		$cat_dates = $wpdb->get_results("	SELECT category_id,
   		UNIX_TIMESTAMP( MAX(post_date) ) AS ts
   		FROM $wpdb->posts, $wpdb->post2cat
   		WHERE post_status = 'publish' AND post_id = ID $exclusions
   		GROUP BY category_id");
   		foreach ($cat_dates as $cat_date) {
   			$category_timestamp["$cat_date->category_id"] = $cat_date->ts;
   		}
   	}
   	
   	$num_found=0;
   	$thelist = "";
//echo "TTL: $title_li, STYLE: $style<br>\n";
	if ( $title_li && $style == 'list' )
			$thelist = '<li class="categories">' . $title_li . '<ul>';

//echo "LIST: $thelist<br>";
   	foreach ($categories as $category) {

//echo "LOOPING:<br>\n";//
//	Add ability to have running totals, not just in category counts in the list.
//
		$all_children = _wswwpx_category_get_descendant_ids($category->term_ID);
		$runningTotal = 0;
		foreach ($category_posts AS $k=>$v) {
			if (@in_array($k, $all_children) ) $runningTotal += $v;
		}
   		if (((intval($hide_empty) == 0 || (intval($hide_empty) == 2 && $runningTotal > 0)) || isset($category_posts["$category->term_ID"])) && (!$hierarchical || $category->parent == $child_of) ) {
   			$num_found++;
//
//	WSW Deaden the link to the current category, and tag ancestor links for different styling
//	Adds one further conditional
//
			if ($category->term_ID == $current_cat) {
				$link = wp_specialchars($category->name);
			} else {
				//
				//	WSW Check for whether an ancestor of the current category
				//
				if (in_array($category->term_ID, $all_ancestors)) {
					$link_class = 'class="current_cat_ancestor"';
				} else {
					$link_class = '';
				}
				$link = '<a ' . $link_class . ' href="'.get_category_link($category->term_ID).'" ';
				if ($use_desc_for_title == 0 || empty($category->description)) {
					$link .= 'title="'. sprintf(__("View all posts filed under %s"), wp_specialchars($category->name)) . '"';
				} else {
//
//	WSW change to allow for truncated descriptions within link titles.
//
//   					$link .= 'title="' . wp_specialchars(apply_filters('category_description',$category->category_description,$category)) . '"';
						$link .= 'title="' . wp_specialchars(wswwpx_category_description($category, $cut_desc, 0)) . '"';
				}
				$link .= '>';
				$link .= apply_filters('wp_list_categories', $category->name, $category).'</a>';
			}
//
//	End current link deadening...
//

   			if ( (! empty($feed_image)) || (! empty($feed)) ) {
   				
   				$link .= ' ';

   				if (empty($feed_image)) {
   					$link .= '(';
   				}

   				$link .= '<a href="' . get_category_rss_link(0, $category->term_ID, $category->category_nicename)  . '"';

   				if ( !empty($feed) ) {
   					$title =  ' title="' . $feed . '"';
   					$alt = ' alt="' . $feed . '"';
   					$name = $feed;
   					$link .= $title;
   				}

   				$link .= '>';

   				if (! empty($feed_image)) {
   					$link .= "<img src='$feed_image' $alt$title" . ' />';
   				} else {
   					$link .= $name;
   				}
   				
   				$link .= '</a>';

   				if (empty($feed_image)) {
   					$link .= ')';
   				}
   			}
   			if (intval($optioncount) == 1) {
   				$link .= ' ('.intval($category_posts["$category->term_ID"]).')';
   			} else if (intval($optioncount) == 2) {
//
//	Handle running count option here...
//
    			$link .= ' ('.$runningTotal.')';
  			}
   			if ( $optiondates ) {
   				if ( $optiondates == 1 ) $optiondates = 'Y-m-d';
   				$link .= ' ' . gmdate($optiondates, $category_timestamp["$category->term_ID"]);
   			}
   			if ($style=='list') {
   				$thelist .= "\t<li>$link\n";
   			} else {
   				$thelist .= "\t$link<br />\n";
   			}

//	Extra 'if' added for folding functionality
   			if (in_array($category->term_ID, $all_ancestors) || $expandlist == 1) {
	   			if ($hierarchical && $children) $thelist .= wswwpx_list_cats23($optionall, $all, '', $sort_column, $sort_order, $file, $style, $optiondates, $optioncount, $hide_empty, $use_desc_for_title, $hierarchical, $category->term_ID, $categories, 1, $feed, $feed_image, $exclude, $hierarchical, $cut_desc, $expandlist);
			}
//	End add
   			if ($style='list') $thelist .= "</li>\n";
   		}
   	}
   	if (!$num_found && !$child_of){
   		if ($style=='list') {
   			$before = '<li>';
   			$after = '</li>';
   		}
   		echo $before . __("No categories") . $after . "\n";
   		return;
   	}
   	if ($style=='list' && $child_of && $num_found && $recurse) {
   		$pre = "\t\t<ul class='children'>";
   		$post = "\t\t</ul>\n";
   	} else {
   		if ($title_li && $style=='list') {
			$pre = '';
			$post = '</ul></li>';
   		} else {
	   		$pre = $post = '';
   		}
   	}
//echo "PRE: $pre, POST: $post<br>\n";
   	$thelist = $pre . $thelist . $post;
   	if ($recurse) {
   		return $thelist;
   	}
   	echo apply_filters('wp_list_categories', $thelist);
   }

   //
   //	Function to optionally cut the category description into two pieces, at a specified cut-mark, and to return the specified part.
   //
   function wswwpx_category_description($category = 0, $cut_at='', $fetch=0) {
   	global $cat;
   	if (($wp_version = get_bloginfo('version')) < VERSION_SWITCH) {
   		$filterName = 'category_description';
		$desc = $category->category_description;
   		$id   = $category->cat_ID;
   	} else {
   		$filterName = 'description';
		$desc = $category->description;
   		$id   = $category->term_ID;
   	}
   	if (!$category) $category = $cat;
   	if (is_numeric($category))$category = & get_category($category);
   	
   	if ( strlen($cut_at)>0 ) {
   		$desc = explode($cut_at, apply_filters($filterName, $desc, $id), 2);
   		$desc = $desc[$fetch];
   	} else {
   		$desc = apply_filters($filterName, $desc, $id);
   	}
   	return $desc;
   }
?>
