<?php
/*
Plugin Name: Birdmash
Description: Shows Twitter feed as a widget
Version: 1.1
Author: Haxor and Chuck Hriczko
*/
//Require our Composer packages
require "vendor/autoload.php";

class Birdmash_Widget extends WP_Widget {
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		//Set up our widget options
		$widget_ops = array( 
			'classname' => 'birdmash_widget',
			'description' => 'Multiuser Twitter Mashup',
		);

		//Call our constructor
		parent::__construct( 'birdmash_widget', 'Birdmash Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		//Create the default twitter feed array
		$twitter_feeds = array();

		//Get the usernames
		$usernames = get_option('bmw-usernames', '');

		/********************************************************************************
		 * NOTE: WAS HAVING TROUBLE WITH TWITTER API SO THE API CODE WAS REMOVED UNTIL
		 * I HAVE MORE TIME TO DETERMINE WHY IT IS NOT ACCEPTING MY TOKENS
		 *******************************************************************************/
		//Get tweets
		$tweets = json_decode(file_get_contents(__DIR__.'/cache/cached-tweets.json'));

		//Generate our potential theme template path
		$theme_tpl_path = get_template_directory().'/bmw/tpl/display.php';

		//Check if our frontend template exists in our theme directory
		$tpl = file_exists($theme_tpl_path) ? $theme_tpl_path : 'tpl/frontend/display.php';

		//Include our frontend template, which contains our loop include
		include($tpl);
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		//Get the usernames that were previously submitted, if any
		$usernames = get_option('bmw-usernames', '');

		//Show our form template
		@include('tpl/admin/form.php');
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		//Get the usernames
		$usernames = $_POST['bmw-usernames'] ?? '';

		//Update the option for the usernames
		update_option('bmw-usernames', $usernames);
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'Birdmash_Widget' );
});
?>