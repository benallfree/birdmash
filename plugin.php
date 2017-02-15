<?php

/*
Plugin Name: Birdmash
Version: 1.0
Author: Haxor
*/

require "twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
		
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
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		
		// get stuff inputted in the widget by user
		$title = apply_filters('widget_title', $instance['title']);
		$twitter_handles_list = $instance['twitter_handles'];
		
		// validate user input
		// make sure list is provided in comma separated values with one-word values
		
		$twitter_handles_array = explode(",",$twitter_handles_list);
		
		// output before widget
		echo $args['before_widget'];

		
		// output title with before and after formatting
		if (!empty($title)){
			echo $args['before_title'] . $title . $args['after_title'];
		}
	 
		// twitter api keys
		$access_token = "831642827176214530-MUu4ypvEmegpi9mPJN4QrVAreeNSIRy";
		$access_token_secret = "OO48esJ8g2voPAFX5BvdhXBxYxqc4LsEz2miEVLRtaXIv";
		$consumerkey = "QiPOGQnlHTPD5PmDo6U8Cquwf";
		$consumersecret = "Tyo6teaoxrfi0CbYsQmCuOvPLof0jx87leB4c2K1Gaui2Jbi9T";
 
 		// make connection to twitter using twitteroauth library
		$connection = new TwitterOAuth($consumerkey, $consumersecret, $access_token, $access_token_secret);
		
		// get tweets for each handle stipulated
		for($i=0;$i<count($twitter_handles_array);$i++){
			$tweets = $connection->get("search/tweets",array("q"=>$twitter_handles_array[$i],"count"=>3));
			
			// loop through tweets and create feed with elements we want to include
			foreach($tweets->statuses as $s){
				// add fancy formatting if css was setup
?>
				<div class="tfeed-date-created"><?php echo $s->created_at; ?></div>
				<div class="tfeed-text" style="margin-bottom:20px;"><?php echo $s->text; ?></div>
<?php
			}

		}	

		// output after widget
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// get saved title if exists
		$title = (isset($instance[ 'title' ])) ? $instance[ 'title' ] : "";  
		
		// get saved list of handles if exists
		$twitter_handles = (isset($instance['twitter_handles'])) ? $instance['twitter_handles'] : ""; 
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'twitter_handles' ); ?>"><?php _e( 'Twitter Handles:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'twitter_handles' ); ?>" name="<?php echo $this->get_field_name( 'twitter_handles' ); ?>" type="text" value="<?php echo esc_attr( $twitter_handles ); ?>" />
		</p>
<?
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		
		// need more validation here - be sure it is a comma separated list and valid (one-word) twitter handles
		$instance['twitter_handles'] = strip_tags($new_instance['twitter_handles']);
		return $instance;
		
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'Birdmash_Widget' );
});

?>