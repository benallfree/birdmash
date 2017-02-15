<?php

/*
Plugin Name: Birdmash
Version: 1.0
Author: Ivaylo Zahariev
*/

# print for hoomans
if( ! function_exists( 'd' ) ) {
    function d( $what ) {
        print '<pre>';
        print_r( $what );
        print '</pre>';
    }
}

# set some paths as static variable
if( ! defined( 'BIRDSMASH_DIR' ) ) {
	DEFINE( 'BIRDSMASH_DIR', plugin_dir_path( __FILE__ ) );
}
if( ! defined( 'BIRDSMASH_URL' ) ) {
	DEFINE( 'BIRDSMASH_URL', plugin_dir_url( __FILE__ ) );
}

# core plugin functions
include BIRDSMASH_DIR . 'inc/core.php';

# handle ajax
include BIRDSMASH_DIR . 'inc/ajax.php';

# build the widget
include BIRDSMASH_DIR . 'inc/widget.php';
