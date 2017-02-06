<?php

/**
 * Birdmash widget
 */
class Birdmash_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = [
			'classname' => 'birdmash_widget',
			'description' => 'Multiuser Twitter Mashup',
		];
		parent::__construct( 'birdmash_widget', 'Birdmash Widget', $widget_ops );

		add_action( 'wp_enqueue_scripts', function() {
			wp_enqueue_style( 'birdmash-tweets', BIRDMASH_PLUGIN_DIR_URL . '/assets/css/tweets.css', [], '1.0.0', 'screen' );
		} );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$usernames = ( ! empty( $instance['usernames'] ) ) ? sanitize_text_field( $instance['usernames'] ) : '';
		$birdmash = new Birdmash_Tweets();
		$tweets = $birdmash->get_tweets( $usernames );
		require_once( BIRDMASH_PLUGIN_DIR . 'template-parts/widgets/tweets.php' );
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$title = ( ! empty( $instance['title'] ) ) ? sanitize_text_field( $instance['title'] ) : 'Latest Tweets';
		$usernames = sanitize_text_field( $instance['usernames'] );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Widget Title</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" type="text" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'usernames' ) ); ?>">Comma separated list of Twitter usernames:</label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'usernames' ) ); ?>" class="widefat" type="text" name="<?php echo esc_attr( $this->get_field_name( 'usernames' ) ); ?>" value="<?php echo esc_attr( $usernames ); ?>" placeholder="ex. 'twitter,jack'">
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
		// Process form values
		$instance = $old_instance;
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['usernames'] = ( ! empty( $new_instance['usernames'] ) ) ? sanitize_text_field( $new_instance['usernames'] ) : '';
		return $instance;
	}
}
