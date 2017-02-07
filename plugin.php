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

		require_once('TwitterAPIExchange.php');
		
		/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
		$settings = array(
		    'oauth_access_token' => "210286456-K81mZF2rOmjVDMXLEmALlrf51T9xfWCava28DqrO",
		    'oauth_access_token_secret' => "HKvjuqnGvHlq9PuWINRGjASWNfm8Sk438iLg8rHIAma9V",
		    'consumer_key' => "0V23nNmrn7hmy46gtB4MV08hp",
		    'consumer_secret' => "jknjZ7Zce8noixUbvu2rfAMHiRI7lNOk0mOTbbYLbqqLxvCv8F"
		);

		$url = 'https://api.twitter.com/1.1/search/tweets.json';
		$requestMethod = 'GET';
		$twitter = new TwitterAPIExchange($settings);
    
		$users = explode( ',', trim($instance['users']) );
		foreach ($users as $key => $user) {

			$getfield = '?q=from:'.$user.'&count=3&result_type=recent';
			$result[] = $twitter->setGetfield($getfield)
					     ->buildOauth($url, $requestMethod)
					     ->performRequest();  

		}
		foreach ($result as $user_tweets) {
			$array[] = json_decode($user_tweets, true);
		}

		echo $args['before_widget'];
		if ( ! empty( $instance['users'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', 'Lastest tweets from '.$instance['users'] ) . $args['after_title'];
		}
		foreach ($array as $user_content) {
			foreach ($user_content['statuses'] as $key => $value) {
			  echo '<h5>' . $value['created_at'] . '</h5>';
			  echo '<p>' . $value['text'] . '</p>';
			}
		}
    
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$users = ! empty( $instance['users'] ) ? $instance['users'] : 'twitter';
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'users' ) ); ?>"><?php esc_attr_e( 'Users (comma separated):' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'users' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'users' ) ); ?>" type="text" value="<?php echo esc_attr( $users ); ?>">
		</p>
		<?php 
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['users'] = ( ! empty( $new_instance['users'] ) ) ? strip_tags( $new_instance['users'] ) : '';
		return $instance;
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'Birdmash_Widget' );
});
