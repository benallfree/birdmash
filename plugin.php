<?php

/**
 * Plugin Name: Birdmash
 * Plugin URI:  https://github.com/CrowdForge/birdmash
 * Description: Widget to track selected twitter account activities.
 * Version:     0.1.0
 * Author:      Mike Grotton
 * Author URI:  http://michaelgrotton.com
 * License:     GPLv2
 * Text Domain: bird-mash
 *
 * @link https://github.com/CrowdForge/birdmash
 *
 * @package Birdmash
 * @version 0.1.0
 */
class Birdmash_Widget extends WP_Widget {

	/**
	 * Settings for the twitter api library.
	 *
	 * @var array
	 */
	public $twitter_settings = array(
	    'oauth_access_token' => '8312152-cdqlf2advJxlnWgUoaLC4sWBM2VlZrhjiq612v75zh',
	    'oauth_access_token_secret' => 'rtgkwEocTEFRX466rDFZwccIPdsoimTQSJmW6oz0CDbQP',
	    'consumer_key' => 'qyjtu5JFk5MsBnIKvD4KWcfrb',
	    'consumer_secret' => 'XjAs4hcAq4Lk1CI9RGnu4w2lGhxSsn8WHfuOoUs49cufORxyFU',
	);

	/**
	 * The cache for tweets, stored in transient/wp cache.
	 *
	 * @var string
	 */
	public $tweets_cache = null;

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
	 * @param array $args the arguments.
	 * @param array $instance the instance settings.
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget.
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options.
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
	 * Function to flush widget cache when options are updated.
	 *
	 * @return void
	 */
	public function flush_widget_cache() {
		delete_transient( 'birdmash-public-cache' );
	}

	/**
	 * Get tweets from the Twitter API.
	 *
	 * @param Array $accounts comma separated list of accounts.
	 * @return Array An array of sorted tweets.
	 */
	public function get_tweets( $accounts ) {

		// No accounts configured? Bail early.
		if ( empty( $accounts ) ) {
			return;
		}

		// If the cache is full, return it.
		if ( ! is_null( $this->twitter_cache ) ) {
			return $this->twitter_cache;
		}

		// If no cache defined, try to get it from the transient first. If that exists, return it.
		$this->twitter_cache = get_transient( 'birdmash-public-cache' );
		if ( $this->twitter_cache ) {
			return $this->twitter_cache;
		}

		// No cache, let's build the tweet cache.
		$tweets = array();
		$screen_names = explode( ',', $accounts );
		foreach ( $screen_names as $screen_name ) {
			$some_tweets = $this->retrieve_tweets( $screen_name );
			$tweets = array_merge( $tweets, $some_tweets );
		}

		$this->twitter_cache = $tweets;
		set_transient( 'birdmash-public-cache', $this->twitter_cache, HOUR_IN_SECONDS );
		return $this->twitter_cache;
	}

	/**
	 * Get tweets from Twitter API.
	 *
	 * @param  string $screen_name The screen name to get tweets from.
	 * @return array the tweets retrieved.
	 */
	public function retrieve_tweets( $screen_name ) {
		// Connect to the api.
		$twitter = new TwitterAPIExchange( $this->twitter_settings );
		$retrieved = array();
		$tweets = json_decode( $twitter->setGetfield( '?screen_name=' . $screen_name . '&count=3&include_rts=false' )
		->buildOauth( 'https://api.twitter.com/1.1/statuses/user_timeline.json', 'GET' )->performRequest(), true );

		// If there are errors with request, bail early.
		if ( array_key_exists( 'errors', $tweets ) ) {
			return $retrieved;
		}
		foreach ( $tweets as $tweet ) {
			$retrieved[]['created'] = $tweet['created_at'];
			$retrieved[]['screen_name'] = $tweet['user']['screen_name'];
			$retrieved[]['text'] = $tweet['text'];
			$retrieved[]['id'] = $tweet['id_str'];
			$retrieved[]['profile_img'] = $tweet['user']['profile_image_url'];
		}
		return $retrieved;
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'Birdmash_Widget' );
});
