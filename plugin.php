<?php 
/**
 * Plugin Name: BirdMash
 * Plugin URI: http://www.sinead-oconnor.co.uk/
 * Description: Tech Test
 * Version: 1.0
 * Author: Sinead O'Connor
 * Author URI: http://www.sinead-oconnor.co.uk/
 */
include('functions.php');

// ---------- HOOKS

register_deactivation_hook( __FILE__, 'birdMashPlugUninstall' );


register_activation_hook  ( __FILE__, 'birdMashPluginstall' );


// -------- INSTALL PLUGIN

function birdMashPluginstall() {
   //global $wpdb;
}


// -------- UNINSTALL PLUGIN

function birdMashPlugUninstall() {
   //global $wpdb;
}



if (!defined('ABSPATH')) die('-1');
?>