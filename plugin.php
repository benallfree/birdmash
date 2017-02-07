<?php

/*
Plugin Name: Birdmash
Version: 1.0
Author: Haxor
*/
require __DIR__ . '/vendor/autoload.php';

class Birdmash_Widget extends WP_Widget {
	protected $transient_name;

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'birdmash_widget',
			'description' => 'Multiuser Twitter Mashup',
		);
		parent::__construct( 'birdmash_widget', 'Birdmash Widget', $widget_ops );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );
		$this->transient_name = 'birdmash_widget_tweets';
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		echo $before_widget;
		$title             = empty( $instance['title'] ) ? ' ' : apply_filters( 'widget_title', $instance['title'] );
		$names             = isset( $instance['names'] ) ? explode( ',', $instance['names'] ) : '';
		$consumerKey       = trim( $instance['consumerKey'] );
		$consumerSecret    = trim( $instance['consumerSecret'] );
		$accessToken       = trim( $instance['accessToken'] );
		$accessTokenSecret = trim( $instance['accessTokenSecret'] );

		$count_tweet = 3;
		$interval    = 30;//in minute

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}


		if ( false == get_transient( $this->transient_name ) ):


			$conn = new Abraham\TwitterOAuth\TwitterOAuth(
				$consumerKey,
				$consumerSecret,
				$accessToken,
				$accessTokenSecret
			);

			$allTweets = array();
			foreach ( $names as $name ) {
				$allTweets[] = $conn->get(
					'statuses/user_timeline',
					array(
						'screen_name'     => $name,
						'count'           => $count_tweet,
						'exclude_replies' => $count_tweet
					)
				);
			}


			$tweets = array();


			foreach ( $allTweets as $perPersonTweets ) {

				foreach ( $perPersonTweets as $tweet ) {
					$name        = $tweet->user->name;
					$screen_name = $tweet->user->screen_name;
					if ( is_ssl() ) {
						$avatarLink = $tweet->user->profile_avatar_url_https;
					} else {
						$avatarLink = $tweet->user->profile_image_url;
					}
					$permalink = 'http://twitter.com/' . $screen_name . '/status/' . $tweet->id_str;
					$time      = $tweet->created_at;
					$time      = date_parse( $time );
					$uTime     = mktime( $time['hour'], $time['minute'], $time['second'], $time['month'], $time['day'], $time['year'] );

					$content  = $this->process_contents( $tweet );
					$tweets[] = array(
						'content'     => $content,
						'name'        => $name,
						'screen_name' => $screen_name,
						'permalink'   => $permalink,
						'avatar_link' => $avatarLink,
						'time'        => $uTime,
					);
				}
			}

			usort( $tweets, function ( $tweet1, $tweet2 ) {
				return $tweet2['time'] - $tweet1['time'];
			} );
			set_transient( $this->transient_name, $tweets, $interval*60 );
		else:
			$tweets = get_transient( $this->transient_name );
		endif;

		echo '<ul class="birdmash-tweet-list">';
		if ( $tweets ) :
			foreach ( $tweets as $key => $val ) {
				?>
                <li>
                    <div class="tweet-avatar-wrap">
                        <img src="<?php echo $val['avatar_link']; ?>" alt="<?php echo $val['name']; ?>">
                    </div>
                    <div class="tweet-details">
                        <a href="<?php echo $val['permalink'] ?>"><span
                                    class="screen-name">@<?php echo $val['screen_name'] ?></span></a>
                        <div class="tweet-content">
							<?php echo $val['content'] ?>
                        </div>

                        <span class="tweet-time"><?php echo date_i18n( get_option( 'date_format' ), $val['time'] ); ?></span>

                    </div>
                </li>
				<?php
			}
			?>
		<?php else : ?>
            <li><?php _e( 'Waiting for Twitter...', 'birdmash-widget' ); ?></li>
		<?php endif; ?>

		<?php

		echo '</ul>';

		echo $after_widget;
	}

	function process_contents( $tweet ) {

		if ( isset( $tweet->retweeted_status ) ) {
			$rt_section = current( explode( ":", $tweet->text ) );
			$text       = $rt_section . ": ";
			$text .= $tweet->retweeted_status->text;
		} else {
			$text = $tweet->text;
		}

		$text = preg_replace( '/((http)+(s)?:\/\/[^<>\s]+)/i', '<a href="$0" target="_blank" rel="nofollow">$0</a>', $text );
		$text = preg_replace( '/[@]+([A-Za-z0-9-_]+)/', '<a href="http://twitter.com/$1" target="_blank" rel="nofollow">@$1</a>', $text );
		$text = preg_replace( '/[#]+([A-Za-z0-9-_]+)/', '<a href="http://twitter.com/search?q=%23$1" target="_blank" rel="nofollow">$0</a>', $text );

		return $text;

	}

	function tweets_by_date_posted( $tweet1, $tweet2 ) {
		return $tweet2['time'] - $tweet1['time'];

	}


	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 * return void
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		$defaults = array(
			'title'             => __( '', 'birdmash-widget' ),
			'names'             => __( 'snumanik', 'birdmash-widget' ),
			'consumerKey'       => __( 'xxxxxxxxxxxx', 'birdmash-widget' ),
			'consumerSecret'    => __( 'xxxxxxxxxxxx', 'birdmash-widget' ),
			'accessToken'       => __( 'xxxxxxxxxxxx', 'birdmash-widget' ),
			'accessTokenSecret' => __( 'xxxxxxxxxxxx', 'birdmash-widget' ),

		);

		$instance = wp_parse_args( (array) $instance, $defaults );

		$title             = $instance['title'];
		$names             = $instance['names'];
		$consumerKey       = trim( $instance['consumerKey'] );
		$consumerSecret    = trim( $instance['consumerSecret'] );
		$accessToken       = trim( $instance['accessToken'] );
		$accessTokenSecret = trim( $instance['accessTokenSecret'] );
		// Show error if cURL not installed - extension required for Twitter API calls
		if ( ! in_array( 'curl', get_loaded_extensions() ) ) {
			echo '<p><strong>';
			_e( 'cURL is not installed.Its required to use Twitter API:', 'birdmash-widget' );
			echo ' <a href="http://curl.haxx.se/docs/install.html" target="_blank">';
			_e( 'cURL install', 'birdmash-widget' );
			echo '</a></strong></p>';
		}

		?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'birdmash-widget' ) ?>
                <input class="widefat"
                       id="<?php echo $this->get_field_id( 'title' ); ?>"
                       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                       value="<?php echo esc_attr( $title ); ?>"
                />
            </label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'names' ); ?>"><?php _e( 'Twitter Names (without @ symbol):', 'birdmash-widget' ) ?>
                <input class="widefat"
                       id="<?php echo $this->get_field_id( 'names' ); ?>"
                       name="<?php echo $this->get_field_name( 'names' ); ?>"
                       type="text"
                       value="<?php echo esc_attr( $names ); ?>"
                />
            </label>
            <span>Separate names with comma(,).</span>
        </p>


        <div class="secrets" style="margin-bottom:10px;">
            <h4 class="button-secondary"
                style="width:100%; text-align:center;"><?php _e( 'Twitter API settings', 'birdmash-widget' ) ?>
                <span style="font-size:75%;">&#9660;</span></h4>
            <div style="padding:10px;">
                <p>
                    <label for="<?php echo $this->get_field_id( 'consumerKey' ); ?>"><?php _e( 'Consumer Key:', 'birdmash-widget' ) ?>
                        <input class="widefat" id="<?php echo $this->get_field_id( 'consumerKey' ); ?>"
                               name="<?php echo $this->get_field_name( 'consumerKey' ); ?>" type="text"
                               value="<?php echo esc_attr( $consumerKey ); ?>"/></label>
                </p>

                <p>
                    <label for="<?php echo $this->get_field_id( 'consumerSecret' ); ?>"><?php _e( 'Consumer Secret:', 'birdmash-widget' ) ?>
                        <input class="widefat" id="<?php echo $this->get_field_id( 'consumerSecret' ); ?>"
                               name="<?php echo $this->get_field_name( 'consumerSecret' ); ?>" type="text"
                               value="<?php echo esc_attr( $consumerSecret ); ?>"/></label>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'accessToken' ); ?>"><?php _e( 'Access Token:', 'birdmash-widget' ) ?>
                        <input class="widefat" id="<?php echo $this->get_field_id( 'accessToken' ); ?>"
                               name="<?php echo $this->get_field_name( 'accessToken' ); ?>" type="text"
                               value="<?php echo esc_attr( $accessToken ); ?>"/></label>
                </p>
                <p>
                    <label for="<?php echo $this->get_field_id( 'accessTokenSecret' ); ?>"><?php _e( 'Access Token Secret:', 'birdmash-widget' ) ?>
                        <input class="widefat" id="<?php echo $this->get_field_id( 'accessTokenSecret' ); ?>"
                               name="<?php echo $this->get_field_name( 'accessTokenSecret' ); ?>" type="text"
                               value="<?php echo esc_attr( $accessTokenSecret ); ?>"/></label>
                </p>
            </div>
        </div>


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
		delete_transient( $this->transient_name );
		$instance = $old_instance;

		$instance['title']             = strip_tags( $new_instance['title'] );
		$instance['names']             = strip_tags( $new_instance['names'] );
		$instance['consumerKey']       = trim( $new_instance['consumerKey'] );
		$instance['consumerSecret']    = trim( $new_instance['consumerSecret'] );
		$instance['accessToken']       = trim( $new_instance['accessToken'] );
		$instance['accessTokenSecret'] = trim( $new_instance['accessTokenSecret'] );

		return $instance;
	}

	public function register_scripts() {
		wp_register_style( 'birdmash', plugins_url( '/assets/css/public.min.css', __FILE__ ) );
		wp_enqueue_style( 'birdmash' );
	}
}

add_action( 'widgets_init', function () {
	register_widget( 'Birdmash_Widget' );
} );
add_action( 'plugins_loaded', 'birdmash_widget_twitter_load_plugin_textdomain' );
// Now loads language files
function birdmash_widget_twitter_load_plugin_textdomain() {
	load_plugin_textdomain( 'birdmash-widget', false, dirname(plugin_basename(__FILE__)) . '/languages/' );
}
