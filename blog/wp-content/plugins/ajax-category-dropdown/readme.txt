=== Ajax Category Dropdown ===
Contributors: Dyasonhat
Tags: categories, ajax, dropdown, widget, select, multi, level
Requires at least: 2.7
Tested up to: 2.7.1
Stable tag: 0.1.5
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=1793510

Generates a multi-level (multiple select boxes) AJAX populated category dropdown widget. Perfect for blogs with large numbers of categories as it only loads category sub levels via AJAX as the user selects parent categories.

== Description ==

Generates a multi-level (multiple select boxes) AJAX populated category dropdown widget. Perfect for blogs with large numbers of categories as it only loads category sub levels via AJAX as the user selects parent categories.

The plugin automatically detects how many sublevels of categories your blog has and shows a form with the corresponding number of select boxes, as a user selects from the first select box is populates the second with sub categories of the first etc.

A real world example would be a blog that's category structure is based on towns eg. State, City, Suburb 

Administrators can customize the text in the different level of select boxes. eg "Select a State" for the first level and "Select a city" for the second level.

Support forum at [forums.dyasohat.com](http://forums.dyasonhat.com/ "DyasonHat Coding Support Forum")

The plugin will also detect the current category and load it's subcategories automatically.

Please note this plugin is a beta release, it's probably still got a few bugs but's not likely to break your blog, so test it out and let me know what TLC it needs.

Future Versions (most of the code for this exists already but needs a bit more work before I enable these features):
* Optionally replace the category browser for posts with an AJAX'ed version to speed up blogs with massive category lists. 
* Optionally replace the category filter dropdown on the admin posts page with multilevel AJAX select boxes.
* Better widget CSS styling options
* WP 2.8 widget API (code actually 80% complete, but awaiting a stable 2.8)

Support this Plugin with your donation.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Unzip the file and upload entire `dhat-ajax-cat-dropdown` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the widgets page and add the Ajax Category Dropdown widget to your sidebar. Configure the widget options.

== Frequently Asked Questions ==

= How can I include the widget into a theme template file? =

The plugin is widgetized and you can simply add the widget to your side bar. 

Alternatively you can call the dropdown any where in you theme by including the following.

NB: This function is not active yet.
‹?php if (class_exists('dhat_ajax_cat_dropdown')) { 
        $dacd = new dhat_ajax_cat_dropdown();
        if (method_exists($dadc,'place_widget_dacd')) {
           $wdadc->place_widget_dacd();
        }
    } ?›
    
== Screenshots ==

None Yet


== Change Log ==
09-05-2009
    0.1.5 Version stable, no serious bugs reported
    Added options to widget to show/hide count
    Added options to widget to choose what to count ie: posts, sub cats etc
    Added options to widget to choose how to sort the categories in the select boxes.
21-04-2009
    Version 0.1.1b Fixed folder naming issue
19-04-2009
    Version 0.1.0b Beta Testing Release