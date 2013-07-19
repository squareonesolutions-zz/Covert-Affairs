<?php
include_once('../../../../wp-blog-header.php');
//include_once('../../../../wp-includes/wp-db.php');
	
function dya_get_subcat($main_cat, $cat_level, $number){
	global $wpdb;
	$i  = $cat_level + 1; 
    $cd = new dhat_ajax_cat_dropdown();
    
    if (!($cd->hasChildCategories($main_cat))) {
        $url = get_category_link($main_cat);
        die("window.location = '$url';");
    }
    
    $options = get_option(DACD_WIDGET_OPTION);
    if ( !isset($options[$number]) )
        return;

    //$title = $options[$widget_number]['title'];
    //$text  = $options[$widget_number]['text'];
    $title      = $options[$number]['title'];
    $text       = $options[$number]['text'];
    $emptyshow  = $options[$number]['emptyshow'];
    $countshow  = $options[$number]['countshow'];
    $countwhat  = $options[$number]['countwhat'];
    $sortby     = $options[$number]['sortby'];
    $direction  = $options[$number]['direction'];        
    //Creat MYSQL sort by
    switch ($sortby) {
       case "titleasc":
            $sort = "$wpdb->terms.name ASC";
         break;
       case "titledesc":
            $sort = "$wpdb->terms.name DESC";
         break;
       case "postcountasc":
            $sort = "$wpdb->term_taxonomy.count ASC";
         break;
       case "postcountdesc":
            $sort = "$wpdb->term_taxonomy.count DESC";
         break;
    }    
    //Show empty
    if ($emptyshow == '1') {
        $emptyshow = true;
    }
    else {
        $emptyshow = false;
    }    
    //show count 
    if ($countshow == '1') {
        $countshow = true;
    }
    else {
        $countshow = false;
    }
    for($j=0;$j<=$cd->totalLevels();$j++){
        //set variable names eg: $level1    
        $leveltitle[$j] = attribute_escape($options[$number]["level$j"]);
    }
    
    
    $pcat = $cd->displayCatLink($main_cat);
	
	//
	// 
	//
	$wcat  = "<label class=\"label\" for=\"cat$i\"></label><select class=\"nav_select\" name=\"cat$i\" id=\"cat$i\" size=\"1\" onchange=\"setCat(this.options[this.selectedIndex].value, \'$i\', \'$number\');\">";
    $wcat .= "<option value=\"xselect\" selected=\"selected\">".$leveltitle[$i]."</option>";
	
	$cat_query = "SELECT * FROM $wpdb->terms
				LEFT JOIN $wpdb->term_taxonomy ON($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
				WHERE $wpdb->term_taxonomy.taxonomy = 'category'
				AND $wpdb->term_taxonomy.parent = $main_cat
                ORDER BY $sort";
	$categorylist = $wpdb->get_results($cat_query);
	foreach ($categorylist as $cat) {
        $totalPosts = $cd->categoryHasPosts($cat->term_id, $countwhat);
        if (($totalPosts !== false) OR (($totalPosts == false) AND ($emptyshow))) {
		    $option = "<option value=\"$cat->term_id\">";
		    $option .= addslashes($cat->name);
		    if ($countshow) {
                $option .= " ($totalPosts)";
            }
		    $option .= "</option>";
		    $wcat .= $option;
        }
	} 
	
	//$cat_level = $cat_level + 1;
    $totalLevels = $cd->totalLevels();
    
    for($j=$i+1;$j<=$totalLevels;$j++){
        $other_wcats .= "document.getElementById(\"cat$j\").disabled = true;";
    }
    for($j=$i;$j<=$totalLevels;$j++){
        $other_pcats .= "document.getElementById(\"pcat$j\").style.display = 'none';";
    }
	die("	document.getElementById('pcat$cat_level').innerHTML   = '$pcat';
            document.getElementById('pcat$cat_level').style.display = 'block';
            document.getElementById('wcat$i').innerHTML           = '$wcat';
			document.getElementById('loadin').style.display       = 'none';
			$other_pcats
            $other_wcats
		");

}
function dya_get_admin_subcat($main_cat, $result_div_id, $cat_level, $cat_level2, $post_id){
	global $wpdb;
	
	$cat_link = get_category_link($main_cat);
	
	$cat_name = get_cat_name($main_cat);
	
	$post_id = $_COOKIE["POSTID"];
	//$mypost = $post_id;
	
	//$terminfo = $wpdb->get_results("SELECT term_id FROM $wpdb->term_relationships LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_id = $wpdb->term_taxonomy.term_id) WHERE $wpdb->term_taxonomy.taxonomy = 'category' AND object_ID = $mypost");
	
	

	// get the first term_id and assign it to $cat
	$iscategoryof = false;
		
	$categories = wp_get_object_terms($post_id, 'category');
	foreach( $categories as $category ) {
		if ( $category->term_id == $main_cat ) {
			$iscategoryof = true;
		}
	}
		
	if ($iscategoryof) {
		$ischecked = "checked=\"checked\"";
	}
	else {
		$ischecked = "";
	}
	
	$str  = "";
	$str .= "<li id=\"category-$main_cat\"><label for=\"in-category-$main_cat\" class=\"selectit\"><input value=\"1\" type=\"checkbox\" name=\"post_category[]\" id=\"in-category-$main_cat\" $ischecked onclick=\"saveposttocategory($post_id, $main_cat, this.checked);\">$cat_name, $post_id, $categories</label></li>";
	if ($cat_level == '1'){
	$str .= "<label class=\"label\" for=\"cat1\"></label><select class=\"nav_select\" name=\"cat1\" id=\"cat1\" size=\"1\" onchange=\"setAdminCat(this.options[this.selectedIndex].value, \'pcat2\', \'2\');\">";
	
	$str .= "\t\t<option selected=\"selected\">Select City</option>";
	
	$cat_query = "
						SELECT * FROM $wpdb->terms
						LEFT JOIN $wpdb->term_taxonomy ON($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
						WHERE $wpdb->term_taxonomy.taxonomy = 'category'
						AND $wpdb->term_taxonomy.parent = $main_cat"						
						;
					    $toplevelcategorylist = $wpdb->get_results($cat_query);
					    foreach ($toplevelcategorylist as $cat) {
							$option = '<option value="'.$cat->term_id.'">';
							$option .= addslashes($cat->name);
							$option .= ' ('.$cat->count.')';
							$option .= '</option>';
							$str .= $option;
						}
	}
	elseif ($cat_level == '2') {
	$str .= "<label class=\"label\" for=\"cat2\"></label><select class=\"nav_select\" name=\"cat2\" id=\"cat2\" size=\"1\" onchange=\"setAdminCat(this.options[this.selectedIndex].value, \'pcat3\', \'3\');\">";
	
	$str .= "\t\t<option selected=\"selected\">Select Suburb</option>";
	
	$cat_query = "
						SELECT * FROM $wpdb->terms
						LEFT JOIN $wpdb->term_taxonomy ON($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
						WHERE $wpdb->term_taxonomy.taxonomy = 'category'
						AND $wpdb->term_taxonomy.parent = $main_cat ORDER BY $wpdb->terms.name ASC"						
						;
	$categories = $wpdb->get_results($cat_query);
					    	
	//$cat_selection = array('type' => 'post', 'child_of' => $main_cat, 'orderby' => 'name', 'order' => 'ASC',	'hide_empty' => false, 'include_last_update_time' => false, 'hierarchical' => 0, 'exclude' => '', 'include' => '', 'number' => '', 'pad_counts' => false);
	//$categories=  get_categories($cat_selection); 
	foreach ($categories as $cat) {
		//$cat_link = get_category_link($cat->term_id);
	  	$option = '<option value="'.$cat->term_id.'">';
		$option .= addslashes($cat->name);
		$option .= ' ('.$cat->count.')';
		$option .= '</option>';
		$str .= $option;
	}
	$str .= '';
	}
	
	
	/*
	$categories=  get_categories('child_of='.$main_cat_id); 
		  foreach ($categories as $cat) {
		  	$option = '<option value="/category/archives/'.$cat->category_nicename.'">';
			$option .= $cat->cat_name;
			$option .= ' ('.$cat->category_count.')';
			$option .= '</option>';
			echo $option;
			$str .= $option;
			} */
	//$str .= "\t</select>";
	die("document.getElementById(\"".$result_div_id."\").innerHTML = '".$str."';");
	//echo( "document.getElementById('$results_id').innerHTML = '$str'" );
	//echo $str;
	//$str = "document.getElementById(\"".$results_id."\").innerHTML = '".$str."';";
	//echo($str);
}

function dya_get_filter_subcat($main_cat, $result_div_id, $cat_level, $cat_level2){
	global $wpdb;
	
	$cat_link = get_category_link($main_cat);
	
	$cat_name = get_cat_name($main_cat);
	
	//$post_id = $_COOKIE["POSTID"];
	//$mypost = $post_id;
	
	//$terminfo = $wpdb->get_results("SELECT term_id FROM $wpdb->term_relationships LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_id = $wpdb->term_taxonomy.term_id) WHERE $wpdb->term_taxonomy.taxonomy = 'category' AND object_ID = $mypost");
	
	

	// get the first term_id and assign it to $cat
	/*$iscategoryof = false;
		
	$categories = wp_get_object_terms($post_id, 'category');
	foreach( $categories as $category ) {
		if ( $category->term_id == $main_cat ) {
			$iscategoryof = true;
		}
	}
		
	if ($iscategoryof) {
		$ischecked = "checked=\"checked\"";
	}
	else {
		$ischecked = "";
	} */
	
	$str  = "";
	//$str .= "<li id=\"category-$main_cat\"><label for=\"in-category-$main_cat\" class=\"selectit\"><input value=\"1\" type=\"checkbox\" name=\"post_category[]\" id=\"in-category-$main_cat\" $ischecked onclick=\"saveposttocategory($post_id, $main_cat, this.checked);\">$cat_name, $post_id, $categories</label></li>";
	if ($cat_level == '1'){
	$str .= "<label class=\"label\" for=\"catf1\"></label><select class=\"dya_drop_filter\" name=\"catf1\" id=\"catf1\" size=\"1\" onchange=\"setFiltercatf(this.options[this.selectedIndex].value, \'pcatf2\', \'2\'); document.getElementById(\'cat\').value = this.options[this.selectedIndex].value;\">";
	
	$str .= "\t\t<option selected=\"selected\">Select City</option>";
	
	$cat_query = "
						SELECT * FROM $wpdb->terms
						LEFT JOIN $wpdb->term_taxonomy ON($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
						WHERE $wpdb->term_taxonomy.taxonomy = 'category'
						AND $wpdb->term_taxonomy.parent = $main_cat"						
						;
					    $toplevelcategorylist = $wpdb->get_results($cat_query);
					    foreach ($toplevelcategorylist as $cat) {
							$option = '<option value="'.$cat->term_id.'">';
							$option .= addslashes($cat->name);
							$option .= ' ('.$cat->count.')';
							$option .= '</option>';
							$str .= $option;
						}
	}
	elseif ($cat_level == '2') {
	$str .= "<label class=\"label\" for=\"catf2\"></label><select class=\"dya_drop_filter\" name=\"catf2\" id=\"catf2\" size=\"1\" onchange=\"document.getElementById(\'cat\').value = this.options[this.selectedIndex].value;\">";
	
	$str .= "\t\t<option selected=\"selected\">Select Suburb</option>";
	$cat_query = "
						SELECT * FROM $wpdb->terms
						LEFT JOIN $wpdb->term_taxonomy ON($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
						WHERE $wpdb->term_taxonomy.taxonomy = 'category'
						AND $wpdb->term_taxonomy.parent = $main_cat 
						AND $wpdb->term_taxonomy.count > 0 ORDER BY $wpdb->terms.name ASC"						
						;
					    $categories = $wpdb->get_results($cat_query);
	//$cat_selection = array('type' => 'post', 'child_of' => $main_cat, 'orderby' => 'name', 'order' => 'ASC',	'hide_empty' => false, 'include_last_update_time' => false, 'hierarchical' => 0, 'exclude' => '', 'include' => '', 'number' => '', 'pad_counts' => false);
	//$categories=  get_categories($cat_selection); 
	foreach ($categories as $cat) {
		$option = '<option value="'.$cat->term_id.'">';
		$option .= addslashes($cat->name);
		$option .= ' ('.$cat->count.')';
		$option .= '</option>';
		$str .= $option;
	}
	$str .= '';
	}
	
	
	/*
	$categories=  get_categories('child_of='.$main_cat_id); 
		  foreach ($categories as $cat) {
		  	$option = '<option value="/category/archives/'.$cat->category_nicename.'">';
			$option .= $cat->cat_name;
			$option .= ' ('.$cat->category_count.')';
			$option .= '</option>';
			echo $option;
			$str .= $option;
			} */
	//$str .= "\t</select>";
	die("document.getElementById(\"".$result_div_id."\").innerHTML = '".$str."';");
	//echo( "document.getElementById('$results_id').innerHTML = '$str'" );
	//echo $str;
	//$str = "document.getElementById(\"".$results_id."\").innerHTML = '".$str."';";
	//echo($str);
}

function dya_save_cat_admin($main_cat, $post_id) {
		global $wpdb;
		
		$savestr = "INSERT INTO $wpdb->term_relationships (object_id, term_taxonomy_id) VALUES ($post_id, $main_cat)" ;
		$wpdb->query($savestr);
		echo "alert('saved');";
		//wp_set_post_categories($post_id, $main_cat);
		//$str = "saved to". $main_cat;
		return $str;
}
function dya_delete_cat_admin($main_cat, $post_id) {
		global $wpdb;
		
		$deletestr = "DELETE FROM $wpdb->term_relationships WHERE object_id = $post_id AND term_taxonomy_id = $main_cat LIMIT 1";
		$wpdb->query($deletestr);
		
		echo "alert('$deletestr');";
		//wp_set_post_categories($post_id, $main_cat);
		//$str = "saved to". $main_cat;
		return $str;
}
function dya_get_form_cat($result_div_id, $main_cat) {
global $wpdb;
$str = '';
$str .= 'deleteOption(document.getElementById("'.$result_div_id.'"));';
$str .= 'addOption(document.getElementById("'.$result_div_id.'"),"Select", "Select");';
$cat_query = "
	SELECT * FROM bk_wp_terms
	LEFT JOIN bk_wp_term_taxonomy ON(bk_wp_terms.term_id = bk_wp_term_taxonomy.term_id)
	WHERE bk_wp_term_taxonomy.taxonomy = 'category'
	AND bk_wp_term_taxonomy.parent = $main_cat ORDER BY bk_wp_terms.name ASC"
	;
$categorylist = $wpdb->get_results($cat_query);
if ($categorylist) {
foreach ($categorylist as $cat) {
	$option = 'addOption(document.getElementById("'.$result_div_id.'"),"'.$cat->name.'", "'.$cat->term_id.'");';
	$str .= $option;
}
}
else {
$cat_query = "
	SELECT name FROM bk_wp_terms
	WHERE term_id = $main_cat LIMIT 1"
	;
	$categorylist = $wpdb->get_row($cat_query);
	$option = 'addOption(document.getElementById("'.$result_div_id.'"),"'.$categorylist->name.'", "'.$main_cat.'");';
	$str .= $option;
}

//die("document.getElementById(\"".$result_div_id."\").innerHTML = '".$str."';");
die ($str);
}


if (isset($_GET['savepostcat']))
	{
		$main_cat = $_GET['category_id'];
		$post_id = $_GET['post_id'];
		$result_div_id = $_GET['results_div_id'];
		dya_save_cat_admin($main_cat, $post_id);
}
elseif (isset($_GET['deletepostcat']))
	{
		$main_cat = $_GET['category_id'];
		$post_id = $_GET['post_id'];
		$result_div_id = $_GET['results_div_id'];
		dya_delete_cat_admin($main_cat, $post_id);
}
elseif (isset($_GET['get_form_countries']))
	{
		$result_div_id = $_GET['results_div_id'];
		$cat_id = $_GET['cat_id'];
		dya_get_form_cat($result_div_id, $cat_id);
}
elseif (isset($_GET['admin']))
	{
		$main_cat = $_GET['category_id'];
		$result_div_id = $_GET['results_div_id'];
		$cat_level = $_GET['category_level'];
		$cat_level2 = $_GET['category_level2'];
		$post_id = $_GET['post_id'];
		dya_get_admin_subcat($main_cat, $result_div_id, $cat_level, $cat_level2, $post_id);
}
elseif (isset($_GET['filter']))
	{
		$main_cat = $_GET['category_id'];
		$result_div_id = $_GET['results_div_id'];
		$cat_level = $_GET['category_level'];
		$cat_level2 = $_GET['category_level2'];
		dya_get_filter_subcat($main_cat, $result_div_id, $cat_level, $cat_level2);
}
elseif (isset($_GET['category_id']))
	{
		$main_cat = $_GET['category_id'];
		$cat_level = $_GET['category_level'];
        $widget_number = $_GET['widget_number'];
		dya_get_subcat($main_cat, $cat_level, $widget_number);
}



?>