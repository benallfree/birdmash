<?php
/*
Plugin Name: Birdmash
Version: 1.0
Author: Haxor
*/

include_once dirname(__FILE__)."/includes/admin.php";

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
//    require "twitteroauth/autoload.php";

//use Abraham\TwitterOAuth\TwitterOAuth;
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$usernames = isset( $instance['usernames'] ) ?  $instance['usernames'] : '';
    if (!get_option('bm_twitter_consumer_key') || !get_option('bm_twitter_consumer_secret') || !get_option('bm_twitter_token') || !get_option('bm_twitter_token_secret')) { 
      $path = 'admin.php?page=birdmash-twitter';
      $url = admin_url($path);
    ?>
      <p><?php echo sprintf(__('You must configure <a href="%s">Birdmash Twitter</a> first.', 'birdmash'), $url); ?></p>
    <?php } else {
?>
  		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'birdmash' ); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
  
  		<p><label for="<?php echo $this->get_field_id( 'usernames' ); ?>"><?php _e( 'Usernames:', 'birdmash' ); ?></label>
  		<input class="widefat" id="<?php echo $this->get_field_id( 'usernames' ); ?>" name="<?php echo $this->get_field_name( 'usernames' ); ?>" type="text" value="<?php echo $usernames; ?>" /></p>
      <p><?php _e('Please, enter a comma-separated list of Twitter usernames', 'birdmas'); ?></p>
<?php
    }
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
		$instance['usernames'] = $new_instance['usernames'];
		// If widget is updated the cache must be deleted
    $this->flush_widget_cache();

  	return $instance;
	}
  
  /**
   * Deletes cache
   */
  public function flush_widget_cache() {
		wp_cache_delete('twitter_data', 'widget');
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'Birdmash_Widget' );
});
