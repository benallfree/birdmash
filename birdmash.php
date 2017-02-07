<?php
/**
 * Birdmash Widget bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link             https://github.com/benallfree/birdmash
 * @package          birdmash
 * @version          1.1
 * @author           Haxor, Travis Seitler <travis@webseitler.com>
 *
 * @wordpress-plugin
 * Plugin Name:      Birdmash
 * Plugin URI:       https://github.com/benallfree/birdmash
 * Description:      This multiuser Twitter mashup widget displays a timeline of the three most recent posts from selected accounts.
 * Version:          1.1
 * Text Domain:      birdmash
 * Author:           Haxor, Travis Seitler
 * License:          GPL-2.0+
 * License URI:      http://www.gnu.org/licenses/gpl-2.0.txt
 */

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Include the class that provides the functionality for the plugin.
 */
include_once( 'class-birdmash-widget.php' );

/**
 * Instantiates the plugin and and initializes the functionality necessary for WordPress.
 *
 * @see 'widgets_init'
 */
add_action( 'widgets_init', function () {
	register_widget( 'Birdmash_Widget' );
} );
