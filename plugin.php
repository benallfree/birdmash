<?php
/*
Plugin Name: Birdmash
Version: 1.0
Author: Haxor
*/

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
		// outputs the content of the widget
		$output = '';
		$twitter_usernames = $instance['twitter_usernames'];
		if ( !empty($twitter_usernames) )
		{
			str_replace(' ', '', $twitter_usernames);
			str_replace('@', '', $twitter_usernames);
			$twitter_usernames_array = explode(',',$twitter_usernames);
			$output .= sds_display_tweets($twitter_usernames_array);
		}
		echo $output;
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		$output = '';
		$twitter_usernames = !empty( $instance['twitter_usernames'] ) ? $instance['twitter_usernames'] : ''; 
		$output .= '<p>';
		$output .= '<label for="'.$this->get_field_id( 'twitter_usernames' ).'">Twitter Usernames: </label>';
		$output .= '<input type="text" id="'.$this->get_field_id( 'twitter_usernames' ).'" name="'.$this->get_field_name( 'twitter_usernames' ).'" value="'.esc_attr( $twitter_usernames ).'" />';
		$output .= '</p>';
		echo $output;
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
		$instance[ 'twitter_usernames' ] = strip_tags( $new_instance[ 'twitter_usernames' ] );
		return $instance;
	}
}

add_action( 'widgets_init', function() {
	register_widget( 'Birdmash_Widget' );
});

require_once('SDS_Twitter.php');

function sds_display_tweets($twitter_usernames) 
{
	$output = '';
	$tweets = false;
	foreach ( $twitter_usernames as $twitter_username )
	{
		$settings = array(
			'screen_name' => $twitter_username,
		    'oauth_access_token' => "808528092939567106-WFvOy9FeVRhxsRqnHZksP0KTqhpXSOM",
		    'oauth_access_token_secret' => "foogp3x2LHpk4CAqIoS68TWsCHlZGgfYXdZHxDWWla8rg",
		    'consumer_key' => "5M1czj0Muuooid4c4htW34VAv",
		    'consumer_secret' => "uDbdkLMRGDGxY55SmutBAZlNn4dkO1YUVNJ303rEJnm31bRsB2"
		);

		$twitter = new SDS_Twitter($settings);
		$new_tweets = $twitter->get_tweets();
		if ( $tweets === false )
		{
			$tweets = $new_tweets;
		}
		else
		{
			$tweets = $new_tweets + $tweets;
		}
	}
	krsort($tweets);

	$output .= '<ul>';
	foreach ( $tweets as $index => $tweet )
	{
		$output .= '<li><a class="tweet-item" href="'.$tweet['link'].'">@'.$tweet['screen_name'].': '.$tweet['text'].'</a></li>';
	}
	$output .= '</ul>';

	return $output;
}