<?php

class Bm_core {


	/*
	 * number of tweets per user
	 *
	 */
	static $tweets_per_user = 3;

	/*
	 * I will use the Twitter Api to get the tweets.
	 * here are the required keys for the connection,
	 * this could be also added in the widget stub
	 */
	static $settings = array(
		'oauth_access_token' 			=> "335981205-EDK4gZGkXCkXt73cqp1ueQ2wJRJTu2uuLa2wO7EI",
		'oauth_access_token_secret' 	=> "OZP9FAW6g3Sl4TYmxsUl9zhADDK9WLBFIzhTfqXqToX59",
		'consumer_key' 					=> "vf4hbQtMbXNBAmZGitzkvA",
		'consumer_secret' 				=> "iQCUJWNJ0D62uVGglavkKYGXXtZ7PJRqHXRHebUpGc"
	);

	/*
	 * first function to start
	 *
	 */
	static function init() {

		# load some scripts
		add_action( 'wp_enqueue_scripts', array( 'Bm_core', 'enqueue_scripts' ) );

	}

	/*
	 * the enqueue ..
	 *
	 */
	static function enqueue_scripts() {

		# css
		wp_enqueue_style( 'bm-style', BIRDSMASH_URL . 'css/style.css' );

		# js
		wp_register_script( 'bm-script', BIRDSMASH_URL . 'js/main.js', array('jquery'), '1.0', true );

		wp_localize_script( 'bm-script', 'bm_vars', array(
			'ajax' => admin_url( 'admin-ajax.php' ),
		));

		wp_enqueue_script( 'bm-script' );

	}

	/*
	 * caching the query results for 60 minutes
	 * via transient
	 */
	static function get_tweets( $usernames ) {

		if( false === ( $tweets = get_transient( 'bm_tweets' ) ) ) {

			# load the twitter api lib
			require_once BIRDSMASH_DIR . 'lib/twitter-api-php/TwitterAPIExchange.php';

			# init twitter api lib
			$twitter = new TwitterAPIExchange( self::$settings );

			$tweets = array();

			foreach( array_filter( explode( ',', $usernames ) ) as $username ) {

				$getfield = '?screen_name=' . esc_attr( $username ) . '&count=' . self::$tweets_per_user;

				$response_json = $twitter->setGetfield( $getfield )
					->buildOauth( 'https://api.twitter.com/1.1/statuses/user_timeline.json', 'GET' )
					->performRequest();

				$response = json_decode( $response_json );

				if( is_array( $response ) ) {
					foreach( $response as $tweet ) {
						$tweets[ strtotime( $tweet->created_at ) ] = $tweet;
					}
				}

			}

			# sort the array by timestamp
			krsort( $tweets, SORT_NUMERIC );

			set_transient( 'bm_tweets', $tweets, HOUR_IN_SECONDS );

		}

		return $tweets;
	}

}

Bm_core::init();
