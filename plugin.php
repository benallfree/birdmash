<?php
/*
Plugin Name: Birdmash
Version: 1.0
Author: Haxor
*/

if ( ! defined( 'BIRDMASH_PLUGIN_FILE' ) ) {
	define( 'BIRDMASH_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'BIRDMASH_PLUGIN_DIR' ) ) {
	define( 'BIRDMASH_PLUGIN_DIR', plugin_dir_path( BIRDMASH_PLUGIN_FILE ) );
}

if ( ! defined( 'BIRDMASH_PLUGIN_DIR_URL' ) ) {
	define( 'BIRDMASH_PLUGIN_DIR_URL', plugin_dir_url( BIRDMASH_PLUGIN_FILE ) );
}

require_once( 'vendor/autoload.php' );

add_action( 'widgets_init', function() {
	register_widget( 'Birdmash_Widget' );
} );

