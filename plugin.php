<?php
/*
Plugin Name: Birdmash
Version: 1.0
Author: Jim Gibbs
*/

// Prevent direct file access
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Birdmash_Widget')){

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
		public function widget($args, $instance) {
			// outputs the content of the widget

			extract($args);

			global $interval;
			
			$title = apply_filters('widget_title', $instance['title']);
			$username = $instance['username'];
			$posts = $instance['posts'];
			$cachetime = $instance['cachetime'];
			$consumerKey = $instance['consumerKey'];
			$consumerSecret = $instance['consumerSecret'];
			$accessToken = $instance['accessToken'];
			$accessTokenSecret = $instance['accessTokenSecret'];
			$uniqueId = $instance['uniqueId'];

			echo $before_widget;

			$settings = array(
				'oauth_access_token' => $accessToken,
				'oauth_access_token_secret' => $accessTokenSecret,
				'consumer_key' => $consumerKey,
				'consumer_secret' => $consumerSecret
			);

			if (!empty($title)){
				echo $before_title . $title . $after_title;
			}

			add_filter('wp_feed_cache_transient_lifetime', array(&$this, 'setInterval'));
			include_once(ABSPATH . WPINC . '/feed.php');

			$upload = wp_upload_dir();
			$cachefile = $upload['basedir'] . '/_twitter_' . $uniqueId . '.txt';

			if (!file_exists($cachefile)){
				$usernames = explode(',', str_replace(' ', '', $username));
				$tweets = array();
				$sortedTweets = array();

				foreach ($usernames as &$user) {
					$url            = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
					$getfield       = '?screen_name=' . $user . '&count=' . $posts; // . '&trim_user=true';
					$request_method = 'GET';

					$twitter_instance = new Twitter_API_WordPress($settings);

					$jsonData = $twitter_instance
						->set_get_field( $getfield )
						->build_oauth( $url, $request_method )
						->process_request();

					$tweets = array_merge($tweets, json_decode($jsonData, true));		
				}	

				foreach	($tweets as &$tweet){
					$time = date_parse($tweet[created_at]);
					$sortedTweets[] = array(
						'createDate' => $tweet[created_at],
						'postText' => $tweet[text],
						'postName' => $user,
						'tweetId' => $tweet[id_str],
						'link' => 'https://twitter.com/' . $tweet[user][screen_name] . '/status/' . $tweet[id_str],
						'time' => mktime($time['hour'], $time['minute'], $time['second'], $time['month'], $time['day'], $time['year']),
					);
				}				

				usort($sortedTweets, function($item1, $item2) {
			    	if ($item1['time'] == $item2['time']) {
			    		return 0;
			    	}
			    	return $item1['time'] < $item2['time'] ? 1 : -1; }
			    );

				$result = '<ul>';

				foreach	($sortedTweets as &$tweet){
					$createDate = $tweet[createDate];
					$postText = $tweet[postText];
					$postName = $tweet[postName];
					$tweetId = $tweet[tweetId];
					$link = $tweet[link];

					$result .= '<li>';

					$time = strtotime($createDate);
					
					if ((abs(time() - $time)) < 86400){
						$time = human_time_diff($time) . ' ago';
					}
					else{
						$time = date('F j, Y', $time);
					}

					$text = htmlspecialchars_decode($postText);
					//urls
					$text = preg_replace('/((http)+(s)?:\/\/[^<>\s]+)/i', '<a href="$0" target="_blank" rel="nofollow">$0</a>', $text);
					//users
			    	$text = preg_replace('/[@]+([A-Za-z0-9-_]+)/', '<a href="https://twitter.com/$1" target="_blank" rel="nofollow">@$1</a>', $text);
			    	//hash tags
					$text = preg_replace('/[#]+([A-Za-z0-9-_]+)/', '<a href="https://twitter.com/search/?q=%23$1" target="_blank" rel="nofollow">$1</a>', $text);
					
					$result .= '<div>';
					$result .= '<div style="font-size:14px;padding-top: 15px;">' . $text . '</div>';
					$result .= '<div style="text-align:right;"><a style="font-size:85%" href="' . $link .'">' . $postName . ' at '. $time .'</a></div>';
					$result .= '</div>';
					$result .= '</li>';

				}
				
				$result .= '</ul>';
				
				@file_put_contents($cachefile, $result);

				echo $result;
			}
			else{ 
				$result = @file_get_contents($cachefile);

				if (!empty($result)){
					echo $result;
				}
			}

			echo $after_widget;
		}

		function setInterval() {
			global $interval;
			return $interval;
		}

		/**
		 * Outputs the options form on admin
		 *
		 * @param array $instance The widget options
		 */
		public function form( $instance ) {
			// outputs the options form on admin
			// Set up some default widget settings
			$defaults = array(
				'title' => 'Latest Tweets', 
				'username' => '', 
				'posts' => 3,
				'cachetime' => 30,
				'accessToken' => "594651970-6dKo4P3k6MsXHKVb8hJrh8fqeZAVMALiU40AzVdV",
				'accessTokenSecret' => "ZRpMF7YNPsvKaU0EPKkJTlu9QlskUKwp7OcVOjX4AV2uc",
				'consumerKey' => "lMR1T8Cb9KPIDscMsG9oMFvUZ",
				'consumerSecret' => "b9MGYKLncJVba3lQbhu9iVUKzxirH99neKSBmXIKwn1QjQUKE5"
				);

			$instance = wp_parse_args((array) $instance, $defaults);
?>
				
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
				<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>">
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('username'); ?>">Your Twitter username:</label>
				<input class="widefat" type="text" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" value="<?php echo $instance['username']; ?>">
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('posts'); ?>">Number of posts to display</label>
				<input class="widefat" type="text" id="<?php echo $this->get_field_id('posts'); ?>" name="<?php echo $this->get_field_name('posts'); ?>" value="<?php echo $instance['posts']; ?>">
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('cachetime'); ?>">Number of minutes to cache</label>
				<input class="widefat" type="text" id="<?php echo $this->get_field_id('cachetime'); ?>" name="<?php echo $this->get_field_name('cachetime'); ?>" value="<?php echo $instance['cachetime']; ?>">
			</p>

			<br/>
			<h3>Twitter Required Feilds</h3>
			<p>
				<label for="<?php echo $this->get_field_id('consumerKey'); ?>">Consumer Key</label>
				<input class="widefat" type="text" id="<?php echo $this->get_field_id('consumerKey'); ?>" name="<?php echo $this->get_field_name('consumerKey'); ?>" value="<?php echo $instance['consumerKey']; ?>">
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('consumerSecret'); ?>">Consumer Secret</label>
				<input class="widefat" type="text" id="<?php echo $this->get_field_id('consumerSecret'); ?>" name="<?php echo $this->get_field_name('consumerSecret'); ?>" value="<?php echo $instance['consumerSecret']; ?>">
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('accessToken'); ?>">Access Token</label>
				<input class="widefat" type="text" id="<?php echo $this->get_field_id('accessToken'); ?>" name="<?php echo $this->get_field_name('accessToken'); ?>" value="<?php echo $instance['accessToken']; ?>">
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('accessTokenSecret'); ?>">Access Token Secret</label>
				<input class="widefat" type="text" id="<?php echo $this->get_field_id('accessTokenSecret'); ?>" name="<?php echo $this->get_field_name('accessTokenSecret'); ?>" value="<?php echo $instance['accessTokenSecret']; ?>">
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
			// processes widget options to be saved
			$instance = $old_instance;

 			$instance['uniqueId'] = (empty($instance['uniqueId'])) ? uniqid() : $instance['uniqueId'];
			$instance['title'] = $new_instance['title'];
			$instance['username'] = $new_instance['username'];
			$instance['posts'] = $new_instance['posts'];
			$instance['cachetime'] = $new_instance['cachetime'];

			$instance['consumerKey'] = trim(strip_tags($new_instance['consumerKey']));
			$instance['consumerSecret'] = trim(strip_tags($new_instance['consumerSecret']));
			$instance['accessToken'] = trim(strip_tags($new_instance['accessToken']));
			$instance['accessTokenSecret'] = trim(strip_tags($new_instance['accessTokenSecret']));

			$upload = wp_upload_dir();
			$cachefile = $upload['basedir'] . '/_twitter_' . $old_instance['uniqueId'] . '.txt';
			@unlink($cachefile);
			
			return $instance;
		}
	}
}


if (class_exists('Birdmash_Widget')){

	add_action( 'widgets_init', function(){
		register_widget( 'Birdmash_Widget' );
	});

}





/**
THIS SECTION WAS TAKEN FROM GITHUB TO HELP EXPODITE THE PROCESS OF GETTING TWEETS
**/


/*
Plugin Name: Twitter-WordPress-HTTP-Client
Plugin URI: http://w3guy.com
Description: A class powered by WordPress API for for consuming Twitter API.
Version: 1.0
Author: Agbonghama Collins
Author URI: http://w3guy.com
License: GPL2
*/


class Twitter_API_WordPress {

	/** @var string OAuth access token */
	private $oauth_access_token;

	/** @var string OAuth access token secrete */
	private $oauth_access_token_secret;

	/** @var string Consumer key */
	private $consumer_key;

	/** @var string consumer secret */
	private $consumer_secret;

	/** @var array POST parameters */
	private $post_fields;

	/** @var string GET parameters */
	private $get_field;

	/** @var array OAuth credentials */
	private $oauth_details;

	/** @var string Twitter's request URL or endpoint */
	private $request_url;

	/** @var string Request method or HTTP verb */
	private $request_method;


	/** Class constructor */
	public function __construct( $settings ) {

		if ( ! isset( $settings['oauth_access_token'] )
		     || ! isset( $settings['oauth_access_token_secret'] )
		     || ! isset( $settings['consumer_key'] )
		     || ! isset( $settings['consumer_secret'] )
		) {
			return new WP_Error( 'twitter_param_incomplete', 'Make sure you are passing in the correct parameters' );
		}

		$this->oauth_access_token        = $settings['oauth_access_token'];
		$this->oauth_access_token_secret = $settings['oauth_access_token_secret'];
		$this->consumer_key              = $settings['consumer_key'];
		$this->consumer_secret           = $settings['consumer_secret'];
	}


	/**
	 * Store the POST parameters
	 *
	 * @param array $array array of POST parameters
	 *
	 * @return $this
	 */
	public function set_post_fields( array $array ) {
		$this->post_fields = $array;

		return $this;
	}


	/**
	 * Store the GET parameters
	 *
	 * @param $string
	 *
	 * @return $this
	 */
	public function set_get_field( $string ) {
		$this->get_field = $string;

		return $this;
	}


	/**
	 * Build, generate and include the OAuth signature to the OAuth credentials
	 *
	 * @param string $request_url Twitter endpoint to send the request to
	 * @param string $request_method Request HTTP verb eg GET or POST
	 *
	 * @return $this
	 */
	public function build_oauth( $request_url, $request_method ) {
		if ( ! in_array( strtolower( $request_method ), array( 'post', 'get' ) ) ) {
			return new WP_Error( 'invalid_request', 'Request method must be either POST or GET' );
		}

		$oauth_credentials = array(
			'oauth_consumer_key'     => $this->consumer_key,
			'oauth_nonce'            => time(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_token'            => $this->oauth_access_token,
			'oauth_timestamp'        => time(),
			'oauth_version'          => '1.0'
		);

		if ( ! is_null( $this->get_field ) ) {
			// remove question mark(?) from the query string
			$get_fields = str_replace( '?', '', explode( '&', $this->get_field ) );

			foreach ( $get_fields as $field ) {
				// split and add the GET key-value pair to the post array.
				// GET query are always added to the signature base string
				$split                          = explode( '=', $field );
				$oauth_credentials[ $split[0] ] = $split[1];
			}
		}

		// convert the oauth credentials (including the GET QUERY if it is used) array to query string.
		$signature = $this->_build_signature_base_string( $request_url, $request_method, $oauth_credentials );

		$oauth_credentials['oauth_signature'] = $this->_generate_oauth_signature( $signature );

		// save the request url for use by WordPress HTTP API
		$this->request_url = $request_url;

		// save the OAuth Details
		$this->oauth_details = $oauth_credentials;

		$this->request_method = $request_method;

		return $this;
	}


	/**
	 * Create a signature base string from list of arguments
	 *
	 * @param string $request_url request url or endpoint
	 * @param string $method HTTP verb
	 * @param array $oauth_params Twitter's OAuth parameters
	 *
	 * @return string
	 */
	private function _build_signature_base_string( $request_url, $method, $oauth_params ) {
		// save the parameters as key value pair bounded together with '&'
		$string_params = array();

		ksort( $oauth_params );

		foreach ( $oauth_params as $key => $value ) {
			// convert oauth parameters to key-value pair
			$string_params[] = "$key=$value";
		}

		return "$method&" . rawurlencode( $request_url ) . '&' . rawurlencode( implode( '&', $string_params ) );
	}


	private function _generate_oauth_signature( $data ) {

		// encode consumer and token secret keys and subsequently combine them using & to a query component
		$hash_hmac_key = rawurlencode( $this->consumer_secret ) . '&' . rawurlencode( $this->oauth_access_token_secret );

		$oauth_signature = base64_encode( hash_hmac( 'sha1', $data, $hash_hmac_key, true ) );

		return $oauth_signature;
	}


	/**
	 * Generate the authorization HTTP header
	 * @return string
	 */
	public function authorization_header() {
		$header = 'OAuth ';

		$oauth_params = array();
		foreach ( $this->oauth_details as $key => $value ) {
			$oauth_params[] = "$key=\"" . rawurlencode( $value ) . '"';
		}

		$header .= implode( ', ', $oauth_params );

		return $header;
	}


	/**
	 * Process and return the JSON result.
	 *
	 * @return string
	 */
	public function process_request() {

		$header = $this->authorization_header();

		$args = array(
			'headers'   => array( 'Authorization' => $header ),
			'timeout'   => 45,
			'sslverify' => false
		);

		if ( ! is_null( $this->post_fields ) ) {
			$args['body'] = $this->post_fields;

			$response = wp_remote_post( $this->request_url, $args );

//echo wp_remote_retrieve_body( $response );
			return wp_remote_retrieve_body( $response );
		}

		else {

			// add the GET parameter to the Twitter request url or endpoint
			$url = $this->request_url . $this->get_field;

			$response = wp_remote_get( $url, $args );

//echo wp_remote_retrieve_body( $response );
			return wp_remote_retrieve_body( $response );

		}

	}
}
