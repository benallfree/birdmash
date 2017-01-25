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
	//widget output
		public function widget($args, $instance) {
			extract($args);
			if(!empty($instance['title'])){ $title = apply_filters( 'widget_title', $instance['title'] ); }
			
			echo $before_widget;				
			if ( ! empty( $title ) ){ echo $before_title . $title . $after_title; }
					
						if(!require_once('vendor/dg/twitter-php/src/twitter.class.php')){
							echo '<strong>'.__('Couldn\'t find twitter.class.php!','birdmash_widget').'</strong>' . $after_widget;
							return;
						}

						if(!require_once('vendor/dg/twitter-php/src/OAuth.php')){ 
							echo '<strong>'.__('Couldn\'t find OAuth.php!','birdmash_widget').'</strong>' . $after_widget;
							return;
						}

						function getConnectionWithAccessToken($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret) {
						  $connection = new Twitter($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
						  return $connection;
						}
						  
						$connection = getConnectionWithAccessToken($instance['consumerkey'], $instance['consumersecret'], $instance['accesstoken'], $instance['accesstokensecret']);
						$tweets = $connection->loadUserInfo("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$instance['username']."&count=10&exclude_replies=".$instance['excludereplies']) or die('Couldn\'t retrieve tweets! Wrong username?');
						
													
						if(!empty($tweets->errors)){
							if($tweets->errors[0]->message == 'Invalid or expired token'){
								echo '<strong>'.$tweets->errors[0]->message.'!</strong><br />' . __('You\'ll need to regenerate it <a href="https://apps.twitter.com/" target="_blank">here</a>!','birdmash_widget') . $after_widget;
							}else{
								echo '<strong>'.$tweets->errors[0]->message.'</strong>' . $after_widget;
							}
							return;
						}
						
						$tweets_array = array();
						for($i = 0;$i <= count($tweets); $i++){
							if(!empty($tweets[$i])){
								$tweets_array[$i]['created_at'] = $tweets[$i]->created_at;
								
									//clean tweet text
									$tweets_array[$i]['text'] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $tweets[$i]->text);
								
								if(!empty($tweets[$i]->id_str)){
									$tweets_array[$i]['status_id'] = $tweets[$i]->id_str;			
								}
							}	
						}							
					
											
				$tp_twitter_plugin_tweets = maybe_unserialize(get_option('tp_twitter_plugin_tweets'));
				if(!empty($tp_twitter_plugin_tweets) && is_array($tp_twitter_plugin_tweets)){
					print '
					<div class="tp_recent_tweets">
						<ul>';
						$fctr = '1';
						foreach($tp_twitter_plugin_tweets as $tweet){					
							if(!empty($tweet['text'])){
								if(empty($tweet['status_id'])){ $tweet['status_id'] = ''; }
								if(empty($tweet['created_at'])){ $tweet['created_at'] = ''; }
							
								print '<li><span>'.tp_convert_links($tweet['text']).'</span><a class="twitter_time" target="_blank" href="http://twitter.com/'.$instance['username'].'/statuses/'.$tweet['status_id'].'">'.tp_relative_time($tweet['created_at']).'</a></li>';
								if($fctr == $instance['tweetstoshow']){ break; }
								$fctr++;
							}
						}
					
					print '
						</ul>';

					print '</div>';
				}else{
					print '
					<div class="tp_recent_tweets">
						' . __('<b>Error!</b> Couldn\'t retrieve tweets for some reason!','birdmash_widget') . '
					</div>';
				}
			
			echo $after_widget;
		}
	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$defaults = array( 'title' => '', 'consumerkey' => '', 'consumersecret' => '', 'accesstoken' => '', 'accesstokensecret' => '', 'cachetime' => '', 'username' => '', 'tweetstoshow' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults );
				
		echo '
		<p><label>' . __('Title:','birdmash_widget') . '</label>
			<input type="text" name="'.$this->get_field_name( 'title' ).'" id="'.$this->get_field_id( 'title' ).'" value="'.esc_attr($instance['title']).'" class="widefat" /></p>
		<p><label>' . __('Consumer Key:','birdmash_widget') . '</label>
			<input type="text" name="'.$this->get_field_name( 'consumerkey' ).'" id="'.$this->get_field_id( 'consumerkey' ).'" value="'.esc_attr($instance['consumerkey']).'" class="widefat" /></p>
		<p><label>' . __('Consumer Secret:','birdmash_widget') . '</label>
			<input type="text" name="'.$this->get_field_name( 'consumersecret' ).'" id="'.$this->get_field_id( 'consumersecret' ).'" value="'.esc_attr($instance['consumersecret']).'" class="widefat" /></p>					
		<p><label>' . __('Access Token:','birdmash_widget') . '</label>
			<input type="text" name="'.$this->get_field_name( 'accesstoken' ).'" id="'.$this->get_field_id( 'accesstoken' ).'" value="'.esc_attr($instance['accesstoken']).'" class="widefat" /></p>									
		<p><label>' . __('Access Token Secret:','birdmash_widget') . '</label>		
			<input type="text" name="'.$this->get_field_name( 'accesstokensecret' ).'" id="'.$this->get_field_id( 'accesstokensecret' ).'" value="'.esc_attr($instance['accesstokensecret']).'" class="widefat" /></p>														
		<p><label>' . __('Cache Tweets in every:','birdmash_widget') . '</label>
			<input type="text" name="'.$this->get_field_name( 'cachetime' ).'" id="'.$this->get_field_id( 'cachetime' ).'" value="'.esc_attr($instance['cachetime']).'" class="small-text" /> hours</p>																			
		<p><label>' . __('Comma Separated Twitter usernames:','birdmash_widget') . '</label>
			<input type="text" name="'.$this->get_field_name( 'usernames' ).'" id="'.$this->get_field_id( 'usernames' ).'" value="'.esc_attr($instance['usernames']).'" class="widefat" /></p>																			
		<p><label>' . __('Tweets to display:','birdmash_widget') . '</label>
			<select type="text" name="'.$this->get_field_name( 'tweetstoshow' ).'" id="'.$this->get_field_id( 'tweetstoshow' ).'">';
			$i = 1;
			for($i; $i <= 10; $i++){
				echo '<option value="'.$i.'"'; if($instance['tweetstoshow'] == $i){ echo ' selected="selected"'; } echo '>'.$i.'</option>';						
			}
			echo '
			</select></p>
		<p><label>' . __('Exclude replies:','birdmash_widget') . '</label>
			<input type="checkbox" name="'.$this->get_field_name( 'excludereplies' ).'" id="'.$this->get_field_id( 'excludereplies' ).'" value="true"'; 
			if(!empty($instance['excludereplies']) && esc_attr($instance['excludereplies']) == 'true'){
				print ' checked="checked"';
			}					
			print ' /></p>';	
	}
	

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['consumerkey'] = strip_tags( $new_instance['consumerkey'] );
		$instance['consumersecret'] = strip_tags( $new_instance['consumersecret'] );
		$instance['accesstoken'] = strip_tags( $new_instance['accesstoken'] );
		$instance['accesstokensecret'] = strip_tags( $new_instance['accesstokensecret'] );
		$instance['cachetime'] = strip_tags( $new_instance['cachetime'] );
		$instance['usernames'] = strip_tags( explode(',', $new_instance['usernames']) );
		$instance['tweetstoshow'] = strip_tags( $new_instance['tweetstoshow'] );
		$instance['excludereplies'] = strip_tags( $new_instance['excludereplies'] );

		if($old_instance['username'] != $new_instance['username']){
			delete_option('tp_twitter_plugin_last_cache_time');
		}
		
		return $instance;
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'Birdmash_Widget' );
});
