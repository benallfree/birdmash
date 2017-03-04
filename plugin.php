<?php
/*
Plugin Name: Birdmash
Version: 1.0
Author: Haxor
*/

require "vendor/autoload.php";

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
        echo $args['before_widget'];

		if(!empty($instance['title'])){
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

        if(!empty($instance['handles'])){
            echo implode(', ', $instance['handles']);
        }

		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
        $title               = !empty($instance['title'])               ? $instance['title']                  : "";
        $handles             = !empty($instance['handles'])             ? implode(', ', $instance['handles']) : "";
        $consumer_key        = !empty($instance['consumer_key'])        ? $instance['consumer_key']           : "";
        $consumer_secret     = !empty($instance['consumer_secret'])     ? $instance['consumer_secret']        : "";
        $access_token        = !empty($instance['access_token'])        ? $instance['access_token']           : "";
        $access_token_secret = !empty($instance['access_token_secret']) ? $instance['access_token_secret']    : "";
		?>

        <?php // Title ?>
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo 'Title:'; ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>">
		</p>

        <?php // Twitter Handles ?>
		<p>
            <label for="<?php echo $this->get_field_id('handles'); ?>"><?php echo 'Twitter Handles (comma separated):'; ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('handles'); ?>" name="<?php echo $this->get_field_name('handles'); ?>" type="text" value="<?php echo $handles; ?>">
		</p>

        <?php // API Consumer Key ?>
		<p>
            <label for="<?php echo $this->get_field_id('consumer_key'); ?>"><?php echo '*Twitter API Consumer Key:'; ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('consumer_key'); ?>" name="<?php echo $this->get_field_name('consumer_key'); ?>" type="password" value="<?php echo $consumer_key; ?>">
		</p>

        <?php // API Consumer Secret ?>
		<p>
            <label for="<?php echo $this->get_field_id('consumer_secret'); ?>"><?php echo '*Twitter API Consumer Secret:'; ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('consumer_secret'); ?>" name="<?php echo $this->get_field_name('consumer_secret'); ?>" type="password" value="<?php echo $consumer_secret; ?>">
		</p>

        <?php // API Access Token ?>
		<p>
            <label for="<?php echo $this->get_field_id('access_token'); ?>"><?php echo '*Twitter API Access Token:'; ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('access_token'); ?>" name="<?php echo $this->get_field_name('access_token'); ?>" type="password" value="<?php echo $access_token; ?>">
		</p>

        <?php // API Access Token Secret ?>
		<p>
            <label for="<?php echo $this->get_field_id('access_token_secret'); ?>"><?php echo '*Twitter API Access Token Secret:'; ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('access_token_secret'); ?>" name="<?php echo $this->get_field_name('access_token_secret'); ?>" type="password" value="<?php echo $access_token_secret; ?>">
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
        // Split [handles] into array
        // Other values can be returned as is.
        $new_instance['handles'] = !empty($new_instance['handles']) ? explode(',', str_replace(' ', '', $new_instance['handles'])) : "";
        return $new_instance;
    }
}

add_action( 'widgets_init', function(){
	register_widget( 'Birdmash_Widget' );
});
