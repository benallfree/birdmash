<?php
/*
  Plugin Name: Twitter Timeline Widget
  Plugin URI: http://www.dancortes.com
  Description: Displays latest tweets from Twitter.
  Author: Daniel Cortes
  Author URI: http://www.dancortes.com
 */


// Load Scripts
require_once(plugin_dir_path(__FILE__) . '/includes/twitter-timeline-widget-scripts.php');

// Load Class
require_once(plugin_dir_path(__FILE__) . '/includes/twitter-timeline-widget-class.php');

// Load PHP Wrapper for Twitter API
require_once(plugin_dir_path(__FILE__) . '/includes/TwitterAPIExchange.php');

// Register Widget
function register_twitter_timeline_widget(){
	register_widget('Twitter_Timeline_Widget');
}

add_action('widgets_init', 'register_twitter_timeline_widget');
