<?php

add_action( 'widgets_init', function() {
	register_widget( 'Birdmash_Widget_Peenapo' );
});

if( ! class_exists( 'Birdmash_Widget_Peenapo' ) ) :
class Birdmash_Widget_Peenapo extends WP_Widget {

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

        $usernames = isset( $instance['usernames'] ) ? esc_attr( $instance['usernames'] ) : '';
        $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';

        echo $args['before_widget'];

        if ( ! empty( $title ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $title ) . $args['after_title'];
		}

        if( ! empty( $usernames ) ) {

            $tweets_arr = Bm_core::get_tweets( $usernames );

            echo '<div class="bm-container" id="bm-container" data-title="' . $usernames . '">';
        	include BIRDSMASH_DIR . 'templates/container.php';
            echo '</div>';

        }else{

            echo '<p>' . esc_html__( 'Please add Twitter Username', 'bm-txd' ) . '</p>';

        }

		echo $args['after_widget'];

	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
    public function form( $instance ) {

        $title = isset( $instance['title'] ) ? $instance['title'] : __( 'Birdmash', 'bm-txd' );
        $usernames = isset( $instance['usernames'] ) ? $instance['usernames'] : '';

        ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'usernames' ); ?>"><?php _e( 'Twitter usernames ( Comma-separated ):' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'usernames' ); ?>" name="<?php echo $this->get_field_name( 'usernames' ); ?>" type="text" value="<?php echo esc_attr( $usernames ); ?>" />
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
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['usernames'] = ( ! empty( $new_instance['usernames'] ) ) ? esc_html( $new_instance['usernames'] ) : '';

        // use the ajax and flush the tweets
        delete_transient( 'bm_tweets' );
        Bm_core::get_tweets( $instance['usernames'] );

        return $instance;

	}
}
endif;
