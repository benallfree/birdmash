<?php
/*
Plugin Name: Birdmash
Version: 1.0
Author: Carl Wuensche
*/
require "functions.php";
require "vendor/autoload.php";

use Abraham\TwitterOAuth\TiwtterOAuth;

class Birdmash_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'birdmash_widget',
			'description' => 'Multiuser Twitter Mashup',
		);
		parent::__construct( 'birdmash_widget', 'Birdmash Widget', $widget_ops );
		if ( is_active_widget( false, false, $this->id_base ) )
		{
			wp_enqueue_style( 'birdmashwidget', site_url('wp-content/plugins/birdmash/assets/css/birdmash.css' ) );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'birdmashwidget', site_url('wp-content/plugins/birdmash/assets/js/birdmash.js' ), array( 'jquery' ) );
		}
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		
		$authenticated_user = wp_get_current_user();
		$twitter_handles = explode( ",", get_option( "birdmash_twitter_handles" ) );
		$birdmash_widget_title = get_option( "birdmash_widget_title" );
		$user_twitter_handles = ( is_user_logged_in() ) ? get_user_meta( $authenticated_user->ID, "birdmash_twitter_user_handles" ) : $_COOKIE["birdmash_twitter_user_handles"];
		$friendly_handles = ( isset($_COOKIE["birdmash_twitter_user_handles"]) && !is_user_logged_in() ) ? implode(", ", json_decode( stripslashes( $user_twitter_handles ) ) ) : implode(", ", array_map( "user_saved_map", $user_twitter_handles) );
		$application_key = get_option("birdmash_application_key");
		$application_secret = get_option("birdmash_application_secret");
		$application_salt = get_option("birdmash_application_salt");

		if ( false === $application_key || false === $application_secret || "" == $application_key || "" == $application_secret ):
			echo "The Birdmash Widget has not been fully setup<br> yet.
			Please go to <a href='".admin_url('admin.php?page=birdmash-settings')."' target='_blank'>Birdmash Settings Page</a> and fill in the Twitter App key, secret, and salt.<br> If you don't have an app key and secret yet, please go to <a href='http://dev.twitter.com/apps'>Twitter</a> and create an application.";
		else:
			echo ( !empty( $birdmash_widget_title ) ) ? "<strong>".$birdmash_widget_title."</strong><br>" : "";
			echo '<a href="javascript:void(0);" class="edit_tweethandles"><img src="'.site_url('wp-admin/images/generic.png').'"> Edit Tweet Handles</a><br>';
			echo '<div class="user_tweet_settings"><strong>Twitter Handles (separated by commas without the @ symbol.)</strong><br><input type="text" value="'.$friendly_handles.'" name="user_tweet_settings"><br><input type="submit" name="update_twitter_settings" value="Save Twitter Handles"><br></div>';
			echo '<div class="loading_message">Fetching tweets from API...<br><img src="'.site_url('wp-content/plugins/birdmash/images/loadingAnimation.gif').'"></div>';
			echo '<div class="loading_status"></div>';
			echo '<div class="timeline">';
			echo '</div>';
		endif;
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		$twitter_handles = get_option( "birdmash_twitter_handles" );
		$birdmash_widget_title = get_option( "birdmash_widget_title" );
		echo 'Title:<br>';
		echo '<input type="text" class="widefat" name="birdmash_widget_title" value="'.$birdmash_widget_title.'"><br>';
		echo '<strong>'.__('Twitter handles').'</strong><br>';
		echo '<input type="text" class="widefat" name="twitter_handles" value="'.$twitter_handles.'"><br>';

		wp_nonce_field( 'update_twitter_handles', 'twitter_handles_nonce' );
		$twitter = new \Abraham\TwitterOAuth\TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
		$tweets = $twitter->get('statuses/user_timeline', ['screen_name' => 'stuffradio']);
		return true;
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		if ( !isset( $_POST['twitter_handles_nonce'] ) || !wp_verify_nonce( $_POST['twitter_handles_nonce'], 'update_twitter_handles' ) )
		{
			return false;
		} else {
			$twitter_handles = sanitize_text_field( $_POST['twitter_handles'] );
			$birdmash_widget_title = sanitize_text_field( $_POST['birdmash_widget_title'] );
			update_option( 'birdmash_twitter_handles', $twitter_handles );
			update_option( 'birdmash_widget_title', $birdmash_widget_title );
		}
	}

}

add_action( 'widgets_init', function(){
	register_widget( 'Birdmash_Widget' );
});

register_activation_hook( __FILE__, function()
{
	$application_key = get_option( "birdmash_application_key" );
	$application_secret = get_option( "birdmash_application_secret" );
	$application_salt = get_option( "birdmash_salt" );
	if ( empty( $application_key ) )
	{
		add_option( "birdmash_application_key" );
	}
	if ( empty( $application_secret ) )
	{
		add_option( "birdmash_application_secret" );
	}
	if ( empty( $application_salt ) )
	{
		add_option( "birdmash_application_salt" );
	}
});

add_action( 'admin_menu', function()
{
	add_menu_page("Birdmash Settings", "Birdmash Settings", "manage_options", "birdmash-settings", function()
	{
		if ( !isset( $_POST['twitter_settings_nonce'] ) || !wp_verify_nonce( $_POST['twitter_settings_nonce'], 'update_twitter_settings' ) )
		{
		} else {
			update_option( "birdmash_application_key", ( 0 === strlen($_POST['birdmash_application_key']) ) ? $_POST['birdmash_application_key'] : sanitize_text_field( encrypt_decrypt( "encrypt", $_POST['birdmash_application_key'] ) ) );
			update_option( "birdmash_application_secret", ( 0 === strlen($_POST['birdmash_application_secret'] ) ) ? $_POST['birdmash_application_secret'] : sanitize_text_field( encrypt_decrypt( "encrypt", $_POST['birdmash_application_secret'] ) ) );
			update_option( "birdmash_application_salt", $_POST['birdmash_application_salt'] );
		}

		$application_key = get_option( "birdmash_application_key" );
		$application_secret = get_option( "birdmash_application_secret" );
		$application_salt = get_option( "birdmash_application_salt" );
		echo '<form action="" method="post">';
		echo '<strong>Twitter App Key</strong><br>';
		echo '<input type="text" name="birdmash_application_key" value="'.encrypt_decrypt( "decrypt", $application_key ).'"><br>';
		echo '<strong>Twitter App Secret</strong><br>';
		echo '<input type="text" name="birdmash_application_secret" value="'.encrypt_decrypt( "decrypt", $application_secret ) .'"><br>';
		echo '<strong>Twitter App Salt</strong><br>';
		echo '<input type="text" name="birdmash_application_salt" value="'.$application_salt.'"><br>';
		wp_nonce_field( 'update_twitter_settings', 'twitter_settings_nonce' );
		echo '<input type="submit" value="Update Settings">';
		echo '<form>';
	});
});

add_action( 'init', function()
{
	if ( !is_user_logged_in() && !isset($_COOKIE["birdmash_twitter_user_handles"]) )
	{
		setcookie("birdmash_twitter_user_handles", "", 60, "/" );
	}
});