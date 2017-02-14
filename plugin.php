<?php

/**
 * Plugin Name: Birdmash
 * Plugin URI:  http://bytion.io
 * Description: Widget to track selected twitter account activities.
 * Version:     0.1.0
 * Author:      Mike Grotton
 * Author URI:  http://michaelgrotton.com
 * License:     GPLv2
 * Text Domain: bird-mash
 *
 * @package Birdmash
 * @version 0.1.0
 */

class Birdmash_Widget extends WP_Widget {

  // Twitter API Key
	protected $twitter_consumer = 'qyjtu5JFk5MsBnIKvD4KWcfrb';

	// Twitter Secret Key
	protected $twitter_secret = 'XjAs4hcAq4Lk1CI9RGnu4w2lGhxSsn8WHfuOoUs49cufORxyFU';

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		require plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';
		$widget_ops = array(
			'classname' => 'birdmash_widget',
			'description' => 'Multiuser Twitter Mashup',
		);
		parent::__construct( 'birdmash_widget', 'Birdmash Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'Birdmash_Widget' );
});
