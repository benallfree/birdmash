<?php

/**
 * Plugin Name: Birdmash
 * Plugin URI:  http://bytion.io
 * Description: Widget to track selected twitter account activities.
 * Version:     0.1.0
 * Author:      Mike Grotton
 * Author URI:  http://michaelgrotton.com
 * License:     GPLv2
 * Text Domain: bird-mash
 *
 * @package Birdmash
 * @version 0.1.0
 */

class Birdmash_Widget extends WP_Widget {

	public $twitter_settings = array(
	    'oauth_access_token' => "8312152-cdqlf2advJxlnWgUoaLC4sWBM2VlZrhjiq612v75zh",
	    'oauth_access_token_secret' => "rtgkwEocTEFRX466rDFZwccIPdsoimTQSJmW6oz0CDbQP",
	    'consumer_key' => "qyjtu5JFk5MsBnIKvD4KWcfrb",
	    'consumer_secret' => "XjAs4hcAq4Lk1CI9RGnu4w2lGhxSsn8WHfuOoUs49cufORxyFU"
	);

	/**
	 * Default Widget title if none is entered.
	 *
	 * @var string
	 */
	protected $default_widget_title = 'BirdMash!';

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		require plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';
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

		<h1>hi</h1><?php

	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance,
			array(
				'title' => $this->default_widget_title,
			)
		);

		?>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'bird-mash' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_html( $instance['title'] ); ?>" placeholder="optional" /></p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'accounts' ) ); ?>"><?php esc_html_e( 'Twitter Accounts (Comma separated, no \'@\'):', 'bird-mash' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'accounts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'accounts' ) ); ?>" type="text" value="<?php echo esc_html( $instance['accounts'] ); ?>" placeholder="accout one, account two, ..." /></p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 */
	public function update( $new_instance, $old_instance ) {
		// Previously saved values.
		$instance = $old_instance;

		// Sanitize title before saving to database.
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['accounts'] = $new_instance['accounts'];

		// Flush cache.
		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * TODO: function to flush widget cache when options are updated.
	 *
	 * @return void
	 */
	public function flush_widget_cache() {
		// to do.
	}

	/**
	 * Get tweets from the Twitter API.
	 *
	 * @return Array An array of sorted tweets.
	 */
	public function get_tweets( $accounts ) {

		// No accounts configured? Bail early.
		if ( empty( $accounts ) ) {
			return;
		}

}

add_action( 'widgets_init', function(){
	register_widget( 'Birdmash_Widget' );
});
