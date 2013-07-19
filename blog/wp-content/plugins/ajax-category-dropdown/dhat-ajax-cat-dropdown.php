<?php
/*
Plugin Name:Ajax Category Dropdown
Plugin URI: http://www.dyasonhat.com/ajax-category-dropdown/
Description: Generates multi-level ajax populated category dropdown widget. Perfect for blog with large numbers of categories as it only loads category sub level via AJAX requests.
Author: DyasonHat
Version: 0.1.5
Author URI: http://www.dyasonhat.com
*/ 

/*  Copyright 2008  Dyason Hat  (email : PLUGIN AUTHOR EMAIL)

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
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
CHANGELOG
09-05-2009
    0.1.5 Version stable, no serious bugs reported
    Added options to widget to show/hide count
    Added options to widget to choose what to count ie: posts, sub cats etc
    Added options to widget to choose how to sort the categories in the select boxes.
21-04-2009
    Version 0.1.1b Fixed folder naming issue
19-04-2009
    Version 0.1.0b Beta Testing Release

*/
include_once(ABSPATH.'/wp-admin/includes/template.php');
DEFINE (DACD_FOLDER, "ajax-category-dropdown");
DEFINE (DACD_URL, plugins_url() . '/'. DACD_FOLDER);
DEFINE (DACD_WIDGET_OPTION, 'DACD_widget');

if (!class_exists('Dhat_Walker_Category_Checklist')) {
    class Dhat_Walker_Category_Checklist extends Walker {
        var $tree_type = 'category';
        var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this
    
        function start_lvl(&$output, $depth, $args) {
            $indent = str_repeat("\t", $depth);
            $output .= "<ul style='' class='children'>";
        }
    
        function end_lvl(&$output, $depth, $args) {
            $indent = str_repeat("\t", $depth);
            $output .= "</ul>";
        }
    
        function start_el(&$output, $category, $depth, $args) {
            extract($args);
    
            $class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
            $output .= "<li id='category-$category->term_id'$class>" . '<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="post_category[]" id="in-category-' . $category->term_id . '"' . (in_array( $category->term_id, $selected_cats ) ? ' checked="checked"' : "" ) . ' onclick="document.getElementById(\'category-'.$category->term_id.'\').childNodes[1].style.display = \'block\';" /> ' . wp_specialchars( apply_filters('the_category', $category->name )) . '</label>';
        }
    
        function end_el(&$output, $category, $depth, $args) {
            $output .= "</li>";
        }
    }
}

// Widget Initialization function
function WP_Widget_DACD_widget_init()
{
    new WP_Widget_DACD_Widget();
}

if (!class_exists('WP_Widget_DACD_Widget') AND class_exists('WP_Widget')) {
    class WP_Widget_DACD_Widget extends WP_Widget {
        function WP_Widget_DACD_Widget() {
            $widget_ops = array ('classname' => 'DACD_Widget', 'description' => __('The description for your Widget') );                
            //$widget_ops = array('description' => __('Ajax Category Dropdown', 'DACD_widget'));
            $this->WP_Widget('DACD_widget', __('Dadc_widget'), $widget_ops);
            
            //$control_ops = array ('width' => '300', 'height' => '400');
            //$widget_ops = array ('classname' => 'DACD_Widget', 'description' => __('The description for your Widget') );
            
            //$this->WP_Widget('DACD_Widget', __('Ajax Category Widget'), $widget_ops, $control_ops);
        
        }
     
        function widget($args, $instance) {
            extract($args, EXTR_SKIP);
            $myDACD = new dhat_ajax_cat_dropdown();
            $myDACD->widget_dacd_Category_Dropdown($args, $instance);
            
            /**
            echo $before_widget;
            $title = empty($instance['title']) ? '&nbsp;' : apply_filters('widget_title', $instance['title']);
            $entry_title = empty($instance['entry_title']) ? '&nbsp;' : apply_filters('widget_entry_title', $instance['entry_title']);
            $comments_title = empty($instance['comments_title']) ? '&nbsp;' : apply_filters('widget_comments_title', $instance['comments_title']);
     
            if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };
            echo '<ul id="rss">';
            echo '  <li><a href=" ' . get_bloginfo('rss2_url') . '" rel="nofollow" title=" ' . wp $entry_title . ' ">' . $entry_title . '</a></li>';
            echo '  <li><a href=" ' . get_bloginfo('comments_rss2_url') . '" rel="nofollow" title="  '. $comments_title . ' ">' . $comments_title . '</a></li>';
            echo '</ul>';
            echo $after_widget;
            **/
        }
     
        function update($new_instance, $old_instance) {
            $instance = $old_instance;
            $instance['title'] = strip_tags($new_instance['title']);
            $instance['entry_title'] = strip_tags($new_instance['entry_title']);
            $instance['comments_title'] = strip_tags($new_instance['comments_title']);
     
            return $instance;
        }
     
        function form($instance) {
            $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'entry_title' => '', 'comments_title' => '' ) );
            $title = strip_tags($instance['title']);
            $entry_title = strip_tags($instance['entry_title']);
            $comments_title = strip_tags($instance['comments_title']);
    ?>
                <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
                <p><label for="<?php echo $this->get_field_id('entry_title'); ?>">Title for entry feed: <input class="widefat" id="<?php echo $this->get_field_id('entry_title'); ?>" name="<?php echo $this->get_field_name('entry_title'); ?>" type="text" value="<?php echo attribute_escape($entry_title); ?>" /></label></p>
                <p><label for="<?php echo $this->get_field_id('comments_title'); ?>">Title for comments feed: <input class="widefat" id="<?php echo $this->get_field_id('comments_title'); ?>" name="<?php echo $this->get_field_name('comments_title'); ?>" type="text" value="<?php echo attribute_escape($comments_title); ?>" /></label></p>
    <?php
        }
    }
}


if (!class_exists('dhat_ajax_cat_dropdown')) {
    class dhat_ajax_cat_dropdown    {
        
        /**
        * @var string   The name the options are saved under in the database.
        */
        var $adminOptionsName = "dhat_ajax_cat_dropdown_options";
        

        
        /**
        * PHP 4 Compatible Constructor
        */
        function dhat_ajax_cat_dropdown(){
            $this->__construct();
        }
        
        /**
        * PHP 5 Constructor
        */        
        function __construct(){


            add_action("admin_menu", array(&$this,"add_admin_pages"));
            add_action("plugins_loaded",array(&$this,"register_widget_dacd_Category_Dropdown"));
            //add_action("widgets_init", array(&$this,"dacd_widgets_init"));
            //add_action("wp_head", array(&$this,"add_head_scripts"));
            // add_action('dbx_post_advanced', array(&$this, "edit_form_advanced"));
            //add_action('admin_head', array(&$this, 'add_admin_css'));
            
            add_action('wp_print_scripts',  array(&$this,"add_head_scripts"));

            $this->adminOptions = $this->getAdminOptions();
            $this->catLevels = $this->totalLevels();

        }
        
        /**
        * Retrieves the options from the database.
        * @return array
        */
        function getAdminOptions() {
            $adminOptions = array("postCategory" => "deep");
            $savedOptions = get_option($this->adminOptionsName);
            if (!empty($savedOptions)) {
                foreach ($savedOptions as $key => $option) {
                    $adminOptions[$key] = $option;
                }
            }
            update_option($this->adminOptionsName, $adminOptions);
            return $adminOptions;
        }
        
        /**
        * Checks if $parent is one $id's parent categories, checks back all levels.
        */
        function isParentCategory($id, $parent) {
            global $wpdb;
            
            if ($id == $parent) {
                return true;
            }
            
            $sql = "SELECT * FROM $wpdb->term_taxonomy WHERE term_id=$id AND taxonomy='category'";
            $cat = $wpdb->get_row($sql);
            
            if ($cat->parent > 0) {
                $sub = $cat->parent;
                
                if ($sub == $parent) {
                    return true;
                }
                else {
                    while ($sub > 0){
                        $sql = "SELECT * FROM $wpdb->term_taxonomy WHERE term_id=$sub and taxonomy='category'";
                        
                        $sub = $wpdb->get_row($sql);
                        $sub = $sub->parent;
                        
                        if ($sub == $parent) {
                            return true;
                        }                    
                    }
                }
                return false;
            }
            else {
                if ($id == $parent) {
                    return true;
                }
                else {
                    return false;
                }
            }            
        }
        
        /**
        * Checks $id's has sub categories, checks back all levels.
        */
        function hasChildCategories($id) {
            global $wpdb;
            
            $sql = "SELECT * FROM $wpdb->term_taxonomy WHERE parent=$id AND taxonomy='category'";
            $cat = $wpdb->get_results($sql);
            
            if ($cat) {
                return true;
            }
            else {
                return false;
            }
        }
        
        /**
        * Returns the depth adjusted parent(id) of category(id)
        * NOT COMPLETE function abandoned....
        */
        function categoryParentByDepth($id, $depth) {
            global $wpdb;
            
            $sql = "SELECT * FROM $wpdb->term_taxonomy WHERE term_id=$id AND taxonomy='category'";
            $cat = get_row($sql);
            
            if ($cat->parent > 0) {
                $sub = $cat->parent;
                $sub_parent = 1; //starting value above 0
                $i = 1;
                $cat_depth = 1;
                
                while ($sub_parent > 0){
                    $sql = "SELECT * FROM $wpdb->term_taxonomy WHERE term_id=$sub and taxonomy='category'";
                    
                    $sub_parent = $wpdb->get_row($sql);
                    $sub_parent = $sub_parent->parent;
                    //echo "Sub parent found: $sub_parent<br>";
                    if ($sub_parent > 0) {
                        $i++;
                        $sub = $sub_parent;
                        if ($i > $cat_depth) {
                            $cat_depth = $i;
                        }
                    }                    
                }
                return $cat_depth;
            }
            else {  // category is the main parent
                if ($depth == 0) {
                    return $id;
                }
                else {
                    return false; // this is the top category, so no depth other than 0 found.
                }
            }
        }
            
        /**
        * Calculates the nested depth of a category by ID
        */
        function categoryDepth($id) {
            global $wpdb;
            
            $sql = "SELECT * FROM $wpdb->term_taxonomy WHERE term_id=$id AND taxonomy='category'";
            $cat = $wpdb->get_row($sql);
            
            if ($cat->parent > 0) {
                $sub = $cat->parent;
                $sub_parent = 1; //starting value above 0
                $i = 1;
                $cat_depth = 1;
                
                while ($sub_parent > 0){
                    $sql = "SELECT * FROM $wpdb->term_taxonomy WHERE term_id=$sub and taxonomy='category'";
                    
                    $sub_parent = $wpdb->get_row($sql);
                    $sub_parent = $sub_parent->parent;
                    //echo "Sub parent found: $sub_parent<br>";
                    if ($sub_parent > 0) {
                        $i++;
                        $sub = $sub_parent;
                        if ($i > $cat_depth) {
                            $cat_depth = $i;
                        }
                    }                    
                }
                return $cat_depth;
            }
            else {
                return 0;
            }            
        }
        /**
        * Calculates if the current category has posts either within it's self, or it children
        */
        function categoryHasPosts($id, $countwhat = 'subposts') {
            global $wpdb;
            
            if ($countwhat == 'subposts') {
                $posts = new WP_Query("cat=$id&showposts=-1");
                $count = $posts->post_count;
                if ($count > 0) {
                    return $count;
                }
                else {
                    return false;
                }
            }
            elseif ($countwhat == 'parentposts') {
                $sql = "SELECT COUNT(*) FROM $wpdb->term_relationships LEFT JOIN
                        $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
                        WHERE
                        $wpdb->term_taxonomy.taxonomy = 'category'
                        AND $wpdb->term_taxonomy.term_id = $id
                        GROUP BY $wpdb->term_taxonomy.term_id";
                echo $sql;
                $count = $wpdb->get_var($sql);
                //$count = $posts->post_count;
                if ($count > 0) {
                    echo "FOUND $count";
                    return $count;
                }
                else {
                    echo "COULDNTFOUND $count";
                    $posts = new WP_Query("cat=$id&showposts=-1");
                    $count = $posts->post_count;
                    if ($count > 0) {
                        return $count;
                    }
                    else {
                        return false;
                    }
                }
            }
            elseif ($countwhat == 'subcats') {
                $posts = new WP_Query("cat=$id&showposts=-1");
                $count = $posts->post_count;
                if ($count > 0) {
                    $sql = "SELECT COUNT(*) FROM $wpdb->term_taxonomy
                            WHERE
                            $wpdb->term_taxonomy.taxonomy = 'category'
                            AND $wpdb->term_taxonomy.parent = $id
                            GROUP BY $wpdb->term_taxonomy.parent";
                    $count = $wpdb->get_var($sql);
                    
                    if ($count == false) {
                        return '0';
                    }
                    return $count;
                }
                else {
                    return false;
                }
                
            }
            else {
                return false;
            }
            
                     
        }
        /**
        * Returns the current category id;
        */
        function currentCategory(){
            global $wpdb;
            global $post;
            
            if (is_category()) {
                    return get_query_var('cat');
            }
            elseif (is_single()) {
                global $post;
                $dhat_ajax_topcat_query = "
                    SELECT $wpdb->term_taxonomy.term_taxonomy_id, $wpdb->term_taxonomy.term_id, $wpdb->term_taxonomy.parent FROM $wpdb->term_relationships 
                    LEFT JOIN $wpdb->term_taxonomy ON ($wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id )
                    WHERE $wpdb->term_relationships.object_id = '$post->ID'
                    AND $wpdb->term_taxonomy.taxonomy = 'category'
                    ORDER BY $wpdb->term_relationships.term_taxonomy_id DESC";
                $dhat_post_categories = $wpdb->get_results($dhat_ajax_topcat_query);
                
                if ($this->adminOptions['postCategory'] == 'deep') {
                    $deepest['id'] = 0;
                    $deepest['level'] = 0;
                        
                    foreach ($dhat_post_categories as $cat) {
                                              
                        $cat_depth = $this->categoryDepth($cat->term_id);
                        if ($deepest['level'] < $cat_depth) {
                            $deepest['id'] = $cat->term_id;
                            $deepest['level'] = $cat_depth;
                        }
                    }
                    
                    return $deepest['id'];
                }
                elseif ($this->adminOptions['postCategory'] == 'lowest') {
                    $lowest = 2000000;
                    foreach ($dhat_post_categories as $cat) {
                        if ($cat->term_taxonomy_id < $lowest) {
                            $lowest = $cat->term_taxonomy_id;
                        }
                    }
                    return $lowest;
                }
                else {
                    $highest = 0;
                    foreach ($dhat_post_categories as $cat) {
                        if ($cat->term_taxonomy_id > $highest) {
                            $highest = $cat->term_taxonomy_id;
                        }
                    }
                    return $highest;
                } 
        
            }
        }
        /**
        * Returns the total number of category levels;
        */
        function totalLevels(){
            global $wpdb;
            $levels = 0;
            $sql = "SELECT parent FROM $wpdb->term_taxonomy WHERE taxonomy='category' AND parent <> 0 GROUP BY parent";
            $subs = $wpdb->get_results($sql);
            //echo "Searched: $sql <br>";
            
            foreach ($subs as $sub) { // cycle through
                $sub = $sub->parent;
                $sub_parent = 1; //initial value
                //echo "Checking sub: $sub<br>";
                $i = 1;
                if ($i > $levels) {
                    $levels = $i;
                }
                while ($sub_parent > 0){
                      $sql = "SELECT * FROM $wpdb->term_taxonomy WHERE term_id=$sub and taxonomy='category'";
                      //echo "Sql: $sql<br>";
                      $sub_parent = $wpdb->get_row($sql);
                      $sub_parent = $sub_parent->parent;
                      //echo "Sub parent found: $sub_parent<br>";
                      if ($sub_parent > 0) {
                            $i++;
                            $sub = $sub_parent;
                            if ($i > $levels) {
                                $levels = $i;
                            }
                      }                    
                }               
            }
            
            //echo "Found $levels levels of categories";
            return $levels;
        }
        
        /**
        * Saves the admin options to the database.
        */
        function saveAdminOptions(){
            update_option($this->adminOptionsName, $this->adminOptions);
            $this->adminOptions = $this->getAdminOptions();
        }
        
        function add_admin_pages(){
                //add_submenu_page('options-general.php', "Ajax Category Dropdown", " Ajax Category Dropdown", 10, "Ajax Category Dropdown", array(&$this,"output_sub_admin_page_0"));
        }
        
        /**
        * Outputs the HTML for the admin sub page.
        */
        function output_sub_admin_page_0(){
            ?>
            <div class="wrap">
                <h2>Admin Menu Placeholder for Category Dropdown a subpage of 'options-general.php'</h2>
                <p>You can modify the content that is output to this page by modifying the method <strong>output_sub_admin_page_0</strong></p>
                <p>Levels found:<?php echo $this->totalLevels(); ?></p>
            </div>
            <?php
        } 
        
        /**
        * Registers the widget for use
        */
        function register_widget_dacd_Category_Dropdown($args) {
            //Awaiting 2.8 for OOP widget controls;
            //$dacd_widget = new WP_Widget_DACD_Widget();
                        
            // Check for the required API functions
            if ( !function_exists('wp_register_sidebar_widget') || !function_exists('wp_register_widget_control') )
                return;

            if ( !$options = get_option(DACD_WIDGET_OPTION) ) {
                $options = array();
            }
                
            $widget_ops = array('classname' => 'widget_dacd', 'description' => __('Ajax Category dropdown boxes widget.'));
            $control_ops = array('width' => 460, 'height' => 350, 'id_base' => 'dacd');
            $name = __('Ajax Category Dropdown');

            $id = false;
            foreach ( array_keys($options) as $o ) {
                // Old widgets can have null values for some reason
                if ( !isset($options[$o]['title']) || !isset($options[$o]['text']) )
                    continue;
                $id = "dacd-$o"; // Never never never translate an id
                wp_register_sidebar_widget($id, $name, array(&$this, 'widget_dacd'), $widget_ops, array( 'number' => $o ));
                wp_register_widget_control($id, $name, array(&$this, 'widget_dacd_control'), $control_ops, array( 'number' => $o ));
            }
            
            // If there are none, we register the widget's existance with a generic template
            if ( !$id ) {
                wp_register_sidebar_widget( 'dacd-1', $name, array(&$this, 'widget_dacd'), $widget_ops, array( 'number' => -1 ) );
                wp_register_widget_control( 'dacd-1', $name, array(&$this, 'widget_dacd_control'), $control_ops, array( 'number' => -1 ) );
            }
            
            //register_sidebar_widget("Ajax Category Dropdown",array(&$this,"widget_dacd_Category_Dropdown"));
        }
        
        /**
        * Widget control function, for settings.
        */
        function widget_dacd_control($widget_args) {
            global $wp_registered_widgets;
            static $updated = false;

            if ( is_numeric($widget_args) )
                $widget_args = array( 'number' => $widget_args );
            $widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
            extract( $widget_args, EXTR_SKIP );

            $options = get_option(DACD_WIDGET_OPTION);
            if ( !is_array($options) )
                $options = array();

            if ( !$updated && !empty($_POST['sidebar']) ) {
                $sidebar = (string) $_POST['sidebar'];

                $sidebars_widgets = wp_get_sidebars_widgets();
                if ( isset($sidebars_widgets[$sidebar]) ) {
                    $this_sidebar =& $sidebars_widgets[$sidebar];
                }
                else {
                    $this_sidebar = array();
                }

                foreach ( $this_sidebar as $_widget_id ) {
                    if ( 'widget_dacd' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
                        $widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
                        unset($options[$widget_number]);
                    }
                }

                foreach ( (array) $_POST['widget-dacd'] as $widget_number => $widget_text ) {
                    $title = strip_tags(stripslashes($widget_text['title']));
                    if ( current_user_can('unfiltered_html') )
                        $text = stripslashes( $widget_text['text'] );
                    else
                        $text = stripslashes(wp_filter_post_kses( $widget_text['text'] ));
                    
                    $countshow = $widget_text['countshow'];
                    $countwhat = $widget_text['countwhat'];
                    $direction = $widget_text['direction'];
                    $sortby    = $widget_text['sortby'];
                    $emptyshow = $widget_text['emptyshow'];
                    $wrapformwidth    = $widget_text['wrapformwidth'];
                    $categorywrapwidth= $widget_text['categorywrapwidth'];
                    
                    $widget_vars  = array('title', 'text');
                    $widget_vars[] = 'emptyshow';
                    $widget_vars[] = 'countshow';
                    $widget_vars[] = 'countwhat';
                    $widget_vars[] = 'direction';
                    $widget_vars[] = 'sortby';
                    $widget_vars[] = 'categorywrapwidth';
                    $widget_vars[] = 'wrapformwidth';
                    for($i=0;$i<=$this->totalLevels();$i++){
                        //set variable names eg: $level1    
                        $a[$i]   = "level".$i;
                        ${$a[$i]} = strip_tags(stripslashes($widget_text["level$i"]));
                        $widget_vars[] = "level".$i;
                    }
                    
                    
                    
                    
                    $options[$widget_number] = compact( $widget_vars );
                }

                update_option(DACD_WIDGET_OPTION, $options);
                $updated = true;
            }

            if ( -1 == $number ) {
                $title = '';
                $text = '';
                $number = '%i%';
                
                for($i=0;$i<=$this->totalLevels();$i++){
                        //set variable names eg: $level1    
                        $a[$i]   = "level".$i;
                        ${$a[$i]} = "Level " . $i;
                    }
                
                $countshow = 1;
                $countwhat = "subposts";
                $direction = "vertical";
                $sortby    = "titleasc";
                $wrapformwidth    = "100%";
                $categorywrapwidth= "100%";
                $emptyshow = 0;
                    
            } else {
                $title = attribute_escape($options[$number]['title']);
                $text = format_to_edit($options[$number]['text']);
                
                $emptyshow = attribute_escape($options[$number]["emptyshow"]);
                $countshow = attribute_escape($options[$number]["countshow"]);
                $countwhat = attribute_escape($options[$number]["countwhat"]);
                $direction = attribute_escape($options[$number]["direction"]);
                $sortby    = attribute_escape($options[$number]["sortby"]);
                $wrapformwidth    = attribute_escape($options[$number]["wrapformwidth"]);
                $categorywrapwidth= attribute_escape($options[$number]["categorywrapwidth"]);
                
                for($i=0;$i<=$this->totalLevels();$i++){
                        //set variable names eg: $level1    
                        $level[$i] = attribute_escape($options[$number]["level$i"]);
                    }
            }
        ?>
                <p>
                    <p>
                        <label for="dacd-title-<?php echo $number; ?>">Title</label>
                        <input class="widefat" id="dacd-title-<?php echo $number; ?>" name="widget-dacd[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>" />
                    </p>
                    <p>
                        <p>Text to display before the dropdown boxes eg, instructions etc.</p>
                        <textarea class="widefat" rows="5" cols="20" id="dacd-text-<?php echo $number; ?>" name="widget-dacd[<?php echo $number; ?>][text]"><?php echo $text; ?></textarea>
                    </p>
                    <?php
                      for($i=0;$i<=$this->totalLevels();$i++){
                        ?>
                        <p>
                            <label for="dacd-<?php echo $number; ?>-level<?php echo "$i"; ?>"><?php echo "Level $i"; ?></label>
                            <input class="widefat" id="dacd-<?php echo $number; ?>-level<?php echo "$i"; ?>" name="widget-dacd[<?php echo $number; ?>][level<?php echo "$i"; ?>]" type="text" value="<?php echo $level[$i]; ?>" />    
                        </p>
                        <?php
                        }                
                    ?>
                    <p>
                        <input type="checkbox" id="dacd-emptyshow-<?php echo $number; ?>" name="widget-dacd[<?php echo $number; ?>][emptyshow]" value="1" <?php if ($emptyshow == 1) {echo "Checked";} ?>/>
                        <label for="dacd-emptyshow-<?php echo $number; ?>">Show empty categories</label>
                    </p>
                    <p>
                        <input type="checkbox" id="dacd-countshow-<?php echo $number; ?>" name="widget-dacd[<?php echo $number; ?>][countshow]" value="1" <?php if ($countshow == 1) {echo "Checked";} ?>/>
                        <label for="dacd-countshow-<?php echo $number; ?>">Display count</label>
                    </p>                    
                    <p>
                        <p>Choose what you want counted.</p>
                        <select id="dacd-countwhat-<?php echo $number; ?>" name="widget-dacd[<?php echo $number; ?>][countwhat]">
                            <option value="parentposts" <?php if($countwhat == "parentposts") {echo "selected";}?> >Posts in Parent Category Only</option>
                            <option value="subposts" <?php if($countwhat == "subposts") {echo "selected";}?> >Posts in All Sub-Categories</option>
                            <option value="subcats" <?php if($countwhat == "subcats") {echo "selected";}?> >Categories Sub-Categoies</option>
                        </select>
                    </p>
                    <p>
                        <p>Choose the order you want the categories displayed in.</p>
                        <select id="dacd-sortby-<?php echo $number; ?>" name="widget-dacd[<?php echo $number; ?>][sortby]">
                            <option value="titleasc" <?php if($sortby == "titleasc") {echo "selected";}?> >Name A-Z</option>
                            <option value="titledesc" <?php if($sortby == "titledesc") {echo "selected";}?> >Name Z-A</option>
                            <option value="postcountasc" <?php if($sortby == "postcountasc") {echo "selected";}?> >Post count low-high</option>
                            <option value="postcountdesc" <?php if($sortby == "postcountdesc") {echo "selected";}?> >Post count high-low</option>
                        </select>
                    </p>
                    <p>
                        <p>Display dropdowns as a vertical list (ie: one above the other) or as a horizontal list (ie: next to each other).</p>
                        <input type="radio" id="dacd-direction-<?php echo $number; ?>" name="widget-dacd[<?php echo $number; ?>][direction]" value="vertical" <?php if($direction == "vertical") {echo "checked";}?>/>Vertical
                        <input type="radio" id="dacd-direction-<?php echo $number; ?>" name="widget-dacd[<?php echo $number; ?>][direction]" value="horizontal" <?php if($direction == "horizontal") {echo "checked";}?>/>Horizontal                       
                    </p>
                    <p>
                        <label for="dacd-wrapformwidth-<?php echo $number; ?>">Total Width of the Widget (eg 19px or 100%)</label>
                        <input class="widefat" id="dacd-wrapformwidth-<?php echo $number; ?>" name="widget-dacd[<?php echo $number; ?>][wrapformwidth]" type="text" value="<?php echo $wrapformwidth; ?>" />    
                    </p>
                    <p>
                        <label for="dacd-categorywrapwidth-<?php echo $number; ?>">Width of Each Select Box (eg 19px or 100%)</label>
                        <input class="widefat" id="dacd-categorywrapwidth-<?php echo $number; ?>" name="widget-dacd[<?php echo $number; ?>][categorywrapwidth]" type="text" value="<?php echo $categorywrapwidth; ?>" />    
                    </p>
                    <input type="hidden" id="dacd-submit-<?php echo $number; ?>" name="dacd-submit-<?php echo $number; ?>" value="1" />
                </p>
        <?php
        }
        
        /**
        * Displays the "View all [category] type link"
        */
        function displayCatLink($id) {
            $dhat_ajax_cat_link = get_category_link($id);
            $dhat_ajax_cat_display_name = get_cat_name($id);
            return '<div style=""><div id="catviewbutton1" class="rightbutton"><a href="'.$dhat_ajax_cat_link.'">View all '.$dhat_ajax_cat_display_name.'</a></div></div>';
        }
        
        /**
        * Allows user to echo widget from theme
        */
        function place_widget_dacd() {
            
            
            
        }
        
        /**
        * Contains the widget logic
        */
        function widget_dacd($args, $widget_args = 1) {
            global $wpdb;
            
            extract( $args, EXTR_SKIP );
            if ( is_numeric($widget_args) ) {
                $widget_args = array( 'number' => $widget_args );
            }
                
            $widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
            extract( $widget_args, EXTR_SKIP );

            $options = get_option(DACD_WIDGET_OPTION);
            if ( !isset($options[$number]) )
                return;

            $title      = $options[$number]['title'];
            $text       = $options[$number]['text'];
            $emptyshow  = $options[$number]['emptyshow'];
            $countshow  = $options[$number]['countshow'];
            $countwhat  = $options[$number]['countwhat'];
            $sortby     = $options[$number]['sortby'];
            $direction  = $options[$number]['direction'];
            $wrapformwidth  = $options[$number]['wrapformwidth'];
            $categorywrapwidth  = $options[$number]['categorywrapwidth'];
            
            //create the styles
            if ($direction == 'vertical') {
                $wrapform  = "width:$wrapformwidth;";
                $categorywrap  = "width:$categorywrapwidth;";
            }
            else {
                $wrapform  = "width:$wrapformwidth; float:left;";
                $categorywrap  = "width:$categorywrapwidth; float:left; clear:none;";
            }
            
            
            for($i=0;$i<=$this->totalLevels();$i++){
                //set variable names eg: $level1    
                $leveltitle[$i] = attribute_escape($options[$number]["level$i"]);
            }
            
            //Saving as $widgetNumber to ensure clarity
            $widgetNumber = $number;
            
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
                       
            ?>
            <?php echo $before_widget; ?>
            <?php echo $before_title . $title . $after_title; ?>
                <form method="get" id="categoryform" action="">
                    <div id="wrapform" style="<?php echo $wrapform; ?>">
                        <div id="wrapallcat">
                    <?php
                    
                    // If category or single then we need to load the appropriate levels into the widget.
                    if ((is_category()) OR (is_single())) {
                        // Loop through all the levels of cats and build dropdowns for them
                        
                        $currentCategory = $this->currentCategory();
                        //echo $currentCategory;
                        //Echo first level then loop through other levels ?>
                        <div class="categorywrap" style="<?php echo $categorywrap;?>;"> 
                            <label class="label" for="cat0"></label>
                            <div id="wcat0">
                                <select class="nav_select" name="cat0" id="cat0" size="1" onchange="setCat(this.form.cat0.value, '0', '<?php echo $widgetNumber; ?>');">
                                <option value="xselect"><?php echo $leveltitle[0]; ?></option>
                                <?php 
                                //Query mysql for the list of this levels cats.
                                $dhat_ajax_cat_query = "SELECT * FROM $wpdb->terms
                                                        LEFT JOIN $wpdb->term_taxonomy ON
                                                        ($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
                                                        WHERE $wpdb->term_taxonomy.taxonomy = 'category'
                                                        AND $wpdb->term_taxonomy.parent = '0'
                                                        ORDER BY $sort"                        
                                                        ;
                                $dhat_ajax_categorylist = $wpdb->get_results($dhat_ajax_cat_query);
                                
                                $hasParent = false; //Start with null parent.
                                $parent = '';
                                
                                foreach ($dhat_ajax_categorylist as $dhat_ajax_cat) {
                                    $totalPosts = $this->categoryHasPosts($dhat_ajax_cat->term_id, $countwhat);
                                    if (($totalPosts !== false) OR (($totalPosts == false) AND ($emptyshow))) {
                                        $dhat_ajax_option = '<option value="'.$dhat_ajax_cat->term_id.'" ';
                                    
                                        if ($this->isParentCategory($currentCategory, $dhat_ajax_cat->term_id)) {
                                            //echo "found parent!";
                                            $dhat_ajax_option .= 'selected';
                                            $hasParent = true;
                                            $parent = $dhat_ajax_cat->term_id;
                                        }
                                        $dhat_ajax_option .= '>';
                                        $dhat_ajax_option .= $dhat_ajax_cat->name;
                                        if ($countshow) {
                                            $dhat_ajax_option .= ' ('.$totalPosts.')';
                                        }                                                                                     
                                        $dhat_ajax_option .= '</option>
                                        ';
                                        echo $dhat_ajax_option;
                                        
                                    }
                                    
                                }
                                 ?>
                                </select>
                            </div>
                            <div id="pcat0" <?php if (!($hasParent)) { ?>style="display:none;"<?php }?>>
                                <?php 
                                    if ($hasParent) {
                                        echo $this->displayCatLink($parent);
                                    }                             
                                ?>
                            </div>
                        </div>
                        <?php

                        for($i=1;$i<=$this->totalLevels();$i++){ 
                            if ($hasParent AND $this->hasChildCategories($parent)) { //Have a parent so populate a select box
                            ?>
                                <div class="categorywrap" style="<?php echo $categorywrap;?>;"> 
                                    <label class="label" for="cat<?php echo $i; ?>"></label>
                                    <div id="wcat<?php echo $i; ?>">
                                        <select class="nav_select" name="cat<?php echo $i; ?>" id="cat<?php echo $i; ?>" size="1" onchange="setCat(this.form.cat<?php echo $i; ?>.value, '<?php echo $i; ?>', '<?php echo $widgetNumber; ?>');">
                                        <option value="xselect"><?php echo $leveltitle[$i]; ?></option>
                                        <?php 
                                        //Query mysql for the list of this levels cats.
                                        $dhat_ajax_cat_query = "SELECT * FROM $wpdb->terms
                                                                LEFT JOIN $wpdb->term_taxonomy ON
                                                                ($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
                                                                WHERE $wpdb->term_taxonomy.taxonomy = 'category'
                                                                AND $wpdb->term_taxonomy.parent = '$parent'
                                                                ORDER BY $sort"                        
                                                                ;
                                        $dhat_ajax_categorylist = $wpdb->get_results($dhat_ajax_cat_query);
                                        $hasParent = false; //reset to false and see if this level has more
                                        foreach ($dhat_ajax_categorylist as $dhat_ajax_cat) {
                                            $totalPosts = $this->categoryHasPosts($dhat_ajax_cat->term_id, $countwhat);
                                            if (($totalPosts !== false) OR (($totalPosts == false) AND ($emptyshow))) {
                                                $dhat_ajax_option = '<option value="'.$dhat_ajax_cat->term_id.'" ';
                                                if ($this->isParentCategory($currentCategory, $dhat_ajax_cat->term_id)) {
                                                    $dhat_ajax_option   .= 'selected';
                                                    $hasParent          = true;
                                                    $parent             = $dhat_ajax_cat->term_id;
                                                }
                                                
                                                $dhat_ajax_option .= '>'.$dhat_ajax_cat->name.'';
                                                if ($countshow) {
                                                    $dhat_ajax_option .= ' ('.$totalPosts.')';
                                                } 
                                                $dhat_ajax_option .= '</option>';
                                                
                                                echo $dhat_ajax_option;
                                            }
                                        }
                                         ?>
                                        </select>
                                    </div>    
                                    <div id="pcat<?php echo $i; ?>" <?php if (!($hasParent)) { ?>style="display:none;"<?php }?>>
                                        <?php 
                                            if ($hasParent) {
                                               echo $this->displayCatLink($parent);
                                            }                              
                                        ?>
                                    </div>
                                </div>
                            <?php                        
                            }
                            else {    // No parent to work with so output unfilled select
                                ?>
                                <div class="categorywrap" style="<?php echo $categorywrap;?>;">
                                    <label class="label" for="cat<?php echo $i; ?>"></label>
                                    <div id="wcat<?php echo $i; ?>">
                                        <select disabled class="nav_select" name="cat<?php echo $i; ?>" id="cat<?php echo $i; ?>" size="1" onchange="">
                                            <option value="xselect"><?php echo $leveltitle[$i]; ?></option>
                                        </select>
                                    </div>
                                    <div id="pcat<?php echo $i; ?>"></div>
                                </div>
                                <?php
                            }  
                        }            
                    }
                    else { // No need to Populate the levels just create the basic structure.
                        
                        //Echo first level then loop through other levels ?>
                        <div class="categorywrap" style="<?php echo $categorywrap;?>;"> 
                            <label class="label" for="cat0"></label>
                            <div id="wcat0">
                                <select class="nav_select" name="cat0" id="cat0" size="1" onchange="setCat(this.form.cat0.value, '0', '<?php echo $widgetNumber; ?>');">
                                <option value="xselect"><?php echo $leveltitle[0]; ?></option>
                                <?php 
                                //Query mysql for the list of this levels cats.
                                $dhat_ajax_cat_query = "SELECT * FROM $wpdb->terms
                                                        LEFT JOIN $wpdb->term_taxonomy ON
                                                        ($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
                                                        WHERE $wpdb->term_taxonomy.taxonomy = 'category'
                                                        AND $wpdb->term_taxonomy.parent = '0'
                                                        ORDER BY $sort"                        
                                                        ;
                                //echo $dhat_ajax_cat_query;
                                $dhat_ajax_categorylist = $wpdb->get_results($dhat_ajax_cat_query);
                                
                                foreach ($dhat_ajax_categorylist as $dhat_ajax_cat) {
                                    $totalPosts = $this->categoryHasPosts($dhat_ajax_cat->term_id, $countwhat);
                                    if (($totalPosts !== false) OR (($totalPosts == false) AND ($emptyshow))) {
                                        $dhat_ajax_option = '<option value="'.$dhat_ajax_cat->term_id.'" ';
                                        
                                        if ($dhat_ajax_cat->term_id == $dhat_ajax_cat_1) {
                                            $dhat_ajax_option .= 'selected';
                                        }
                                        $dhat_ajax_option .= '>';
                                        $dhat_ajax_option .= $dhat_ajax_cat->name;
                                        if ($countshow) {
                                            $dhat_ajax_option .= ' ('.$totalPosts.')';
                                        } 
                                        $dhat_ajax_option .= '</option>
                                        ';
                                        echo $dhat_ajax_option;
                                    }
                                }
                                 ?>
                                </select>
                            </div>
                            <div id="pcat0"></div>
                        </div>
                        <?php    
                        for($i=1;$i<=$this->totalLevels();$i++){ ?>
                            <div class="categorywrap" style="<?php echo $categorywrap;?>;"> 
                                <label class="label" for="cat<?php echo $i; ?>"></label>
                                <div id="wcat<?php echo $i; ?>">
                                    <select disabled class="nav_select" name="cat<?php echo $i; ?>" id="cat<?php echo $i; ?>" size="1" onchange="">
                                        <option value="xselect"><?php echo $leveltitle[$i]; ?></option>
                                    </select>
                                </div>
                                <div id="pcat<?php echo $i; ?>"></div>
                            </div>
                        <?php           
                        }
                    } ?>
                    </div>
                    <div id="loadin" style="display:none;"><div id="loadindiv"><div id="loadpic"></div></div></div>
                    </div>
                </form>
            <?php echo $after_widget; ?>
            <?php
        }
        
        /**
        * Displayes a checkbox style list of WP categories similar to the Write Form
        */
        function dacd_checkboxlist($post_id = 0, $descendants_and_self = 0, $selected_cats = false, $popular_cats = false ) {
            $walker = new dhat_ajax_Walker_Category_Checklist;
                $descendants_and_self = (int) $descendants_and_self;
            
                $args = array();
            
                if ( is_array( $selected_cats ) )
                    $args['selected_cats'] = $selected_cats;
                elseif ( $post_id )
                    $args['selected_cats'] = wp_get_post_categories($post_id);
                else
                    $args['selected_cats'] = array();
            
                if ( is_array( $popular_cats ) )
                    $args['popular_cats'] = $popular_cats;
                else
                    $args['popular_cats'] = get_terms( 'category', array( 'fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );
            
                if ( $descendants_and_self ) {
                    $categories = get_categories( "child_of=$descendants_and_self&hierarchical=0&hide_empty=0" );
                    $self = get_category( $descendants_and_self );
                    array_unshift( $categories, $self );
                } else {
                    $categories = get_categories('get=all');
                }
            
                // Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
                $checked_categories = array();
                for ( $i = 0; isset($categories[$i]); $i++ ) {
                    if ( in_array($categories[$i]->term_id, $args['selected_cats']) ) {
                        $checked_categories[] = $categories[$i];
                        unset($categories[$i]);
                    }
                }
            
                // Put checked cats on top
                $dhat_ajax_catchecklist = call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args));
                // Then the rest of them
                $dhat_ajax_catchecklist .= call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
                    
            return $dhat_ajax_catchecklist;
        }
        
        /**
        * Parent function which calls all the JS and CSS scripts
        */
        function add_head_scripts() {
                    
            if (!is_admin())
                { 
                    $this->add_scripts();
                    $this->add_css();
                }            
        }
        /**
        * Tells WordPress to load the scripts
        */
        function add_scripts(){
            
            $requesturl = DACD_URL .'/includes/dhat-ajax-cat-dropdown-request.php';
            
            wp_enqueue_script('sack');
            wp_enqueue_script('dacd_script', DACD_URL .'/js/script.js', array('sack')); 
            wp_localize_script( 'dacd_script', 'DACDSettings', array(
                  'requesturl' => $requesturl,
                  'mode' => "auto"
                  ));
        }

        /**
        * Adds a link to the stylesheet to the header
        */
        function add_css(){
            echo '<link rel="stylesheet" href="'. DACD_URL . '/css/style.css" type="text/css" media="screen"  />'; 
        }
        
        /**
        * Adds a link to the  admin stylesheet and Javascript to the header
        */
        function add_admin_css(){
        
            //wp_print_scripts( array( 'sack' ));
            //echo "<script type='text/javascript' src='http://youstay.co.za/wp-content/plugins/dhat_ajax_cat_dropdown/dhat_ajax_cat_dropdown_request.js'></script>";
            //<script type='text/javascript' src='http://youstay.co.za/wp-content/plugins/lightbox/scriptaculous.js?load=effects'></script>";
            
        }
        
        function dropdown_only_form_advanced($post_id)
        {
        //global $post_ID;
        global $wpdb;

        
            $edit_html = '<select class="nav_select" name="cat0" id="cat0" size="1" onchange="setpostid('.$post_id.'); setAdminCat(this.form.cat0.value, \'pcat1\', \'1\', \'2\');">
                <option selected>Select Province</option>';
            
                    $cat_query = "
                        SELECT * FROM $wpdb->terms
                        LEFT JOIN $wpdb->term_taxonomy ON($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
                        WHERE $wpdb->term_taxonomy.taxonomy = 'category'
                        AND $wpdb->term_taxonomy.parent = '0'"                        
                        ;
                        $toplevelcategorylist = $wpdb->get_results($cat_query);
                        foreach ($toplevelcategorylist as $cat) {
                            $option = '<option value="'.$cat->term_id.'">';
                            $option .= $cat->name;
                            $option .= ' ('.$cat->count.')';
                            $option .= '</option>';
                            $edit_html .= $option;
                        }
                
                $edit_html .= '
                </select>
                
            <span id="pcat1">
                <label class="label" for="cat1"></label>
                <select class="nav_select" name="cat1" id="cat1" size="1" disabled="disabled" onchange=\"setAdminCat(this.options[this.selectedIndex].value, \'pcat2\', \'2\', $post_ID);\">
                    <option>Select City</option>
                </select>
            </span>

            <span id="pcat2">
                <label class="label" for="cat2"></label>
                <select class="nav_select" name="cat2" id="cat2" size="1" disabled="disabled">
                    <option>Select Suburb</option>
                </select>
            </span>
            <span id="pcat3">
            </span>
            ';
            return $edit_html;
    }
    
    function dropdown_only_form_filterbox()
    {
        //global $post_ID;
        global $wpdb;

        
            $edit_html = '<input type="hidden" name="cat" id="cat" style="width:40px;" /><select class="dhat_ajax_drop_filter" name="catf0" id="catf0" size="1" onchange="setFiltercatf(this.form.catf0.value, \'pcatf1\', \'1\', \'2\'); document.getElementById(\'cat\').value = this.form.catf0.value;">
                <option selected>Select Province</option>';
            
                    $catf_query = "
                        SELECT * FROM $wpdb->terms
                        LEFT JOIN $wpdb->term_taxonomy ON($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
                        WHERE $wpdb->term_taxonomy.taxonomy = 'category'
                        AND $wpdb->term_taxonomy.parent = '0'"                        
                        ;
                        $toplevelcategorylist = $wpdb->get_results($catf_query);
                        foreach ($toplevelcategorylist as $cat) {
                            $option = '<option value="'.$cat->term_id.'">';
                            $option .= $cat->name;
                            $option .= ' ('.$cat->count.')';
                            $option .= '</option>';
                            $edit_html .= $option;
                        }
                
                $edit_html .= '
                </select>
                
            <span id="pcatf1">
                <label class="label" for="catf1"></label>
                <select class="dhat_ajax_drop_filter" name="catf1" id="catf1" size="1" disabled="disabled" onchange="setFiltercatf(this.options[this.selectedIndex].value, \'pcatf2\', \'2\'\">
                    <option>Select City</option>
                </select>
            </span>

            <span id="pcatf2">
                <label class="label" for="catf2"></label>
                <select class="dhat_ajax_drop_filter" name="catf2" id="catf2" size="1" disabled="disabled">
                    <option>Select Suburb</option>
                </select>
            </span>
            ';
            return $edit_html;
    }
        
        /* function edit_form_advanced()
    {
        global $post_ID;
        global $wpdb;

        
            $edit_html = ' <div class="dbx-b-ox-wrapper">
                <fieldset class="dbx-box">
                    <div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">Category Drop Down Manager</h3></div>
                    <div class="dbx-c-ontent-wrapper"><div class="dbx-content">
                        <select class="nav_select" name="cat0" id="cat0" size="1" onchange="setCat(this.form.cat0.value, \'pcat1\', \'1\', \'2\'); shownavlink(\'1\');">
                <option selected>Select Province</option>';
            
            echo $edit_html;
                    
                        $cat_query = "
                        SELECT * FROM $wpdb->terms
                        LEFT JOIN $wpdb->term_taxonomy ON($wpdb->terms.term_id = $wpdb->term_taxonomy.term_id)
                        WHERE $wpdb->term_taxonomy.taxonomy = 'category'
                        AND $wpdb->term_taxonomy.parent = '0'"                        
                        ;
                        $toplevelcategorylist = $wpdb->get_results($cat_query);
                        foreach ($toplevelcategorylist as $cat) {
                            $option = '<option value="'.$cat->term_id.'">';
                            $option .= $cat->name;
                            $option .= ' ('.$cat->count.')';
                            $option .= '</option>';
                            echo $option;
                        }
                
                $edit_html = '
                </select>
                
            <span id="pcat1">
                <label class="label" for="cat1"></label>
                <select class="nav_select" name="cat1" id="cat1" size="1" disabled="disabled">
                    <option>Select City</option>
                </select>
            </span>

            <span id="pcat2">
                <label class="label" for="cat2"></label>
                <select class="nav_select" name="cat2" id="cat2" size="1" disabled="disabled">
                    <option>Select Suburb</option>
                </select>
            </span>

            </form>
                    </div></div>
                </fieldset>
            </div>';
            echo $edit_html;
    } */
        
        

    }
}

//instantiate the class
if (class_exists('dhat_ajax_cat_dropdown')) {
    $dhat_ajax_cat_dropdown = new dhat_ajax_cat_dropdown();
    //register_widget('WP_Widget_DACD_Widget');
}
//add_action('widgets_init', 'WP_Widget_DACD_widget_init');

?>