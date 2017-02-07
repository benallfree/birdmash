<?php

/**
 * Class Birdmash_Widget
 */
class Birdmash_Widget extends WP_Widget {

	/**
	 * Let's get it started in here
	 *
	 * Sets up the widget's name, description, etc.
	 *
	 * @see WP_Widget::__construct()
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'birdmash_widget',
			'description' => 'Multiuser Twitter Mashup',
		);
		parent::__construct( 'birdmash_widget', 'Birdmash Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * Displays a combined list of the three most recent items from each Twitter
	 * user, sorted by post date (most recent first). These items are fetched via
	 * server-side API requests and cached.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Display arguments including before_title, after_title,
	 *                        before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		/** TODO: (bonus) check #1 - if user is logged into WordPress account, check for user_meta with additional Twitter handles. */
		/** TODO: (bonus) check #2 - if user is not logged in, check for session cookie with additional Twitter handles. */
		/** TODO: http://giphy.com/gifs/K3qwA91Bs4FLW/html5 ? */
		/** TODO: (bonus) add gear icon that, when clicked, allows the user to modify tweet list */
		/** TODO: (bonus) update option #1 - if user is logged into WordPress account, update user_meta. */
		/** TODO: (bonus) update option #2 - if user is not logged in, save settings to session cookie. */

		// Set the location for our cache file.
		$cache_dir = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'cache/';
		wp_mkdir_p( $cache_dir );

		// Create a Twitter App at https://apps.twitter.com/ and load its access token data here.
		$twitter_config = array(
			'directory'    => $cache_dir, // The path used to store the .tweetcache cache file.
			'key'          => '',
			'secret'       => '',
			'token'        => '',
			'token_secret' => '',
			'cache_expire' => 3600, // The duration of the cache
		);

		$twitter = new StormTwitter( $twitter_config );

		// Default twitter handle(s) if nothing is set in the Widget admin.
		$usernames = array( 'benallfree' );

		// Replace the default if the Widget's list has been set.
		if ( trim( $instance['usernames'] ) ) {
			$usernames = explode( ',', $instance['usernames'] );
		}

		$merged_timeline = array();

		foreach ( $usernames as $username ) {

			$username = trim( $username );

			$tweets = $twitter->getTweets( $username, 3, array(
				'trim_user'       => false,
				'include_rts'     => true,
				'exclude_replies' => false,
			) );

			// Prepare a date-sorted array of items.
			foreach ( $tweets as $tweet ) {

				$merged_timeline[ strtotime( $tweet->created_at ) ] = $tweet;

			}

		}

		// Make sure we aren't sitting on an empty array.
		if ( ! empty( array_filter( $merged_timeline ) ) ) {

			// Sort entries by their timestamps.
			krsort( $merged_timeline );

			echo '<section class="widget widget_birdmash">';

			foreach ( $merged_timeline as $timestamp_id => $tweet ) {

				$timestamp = date( 'j M Y', strtotime( $tweet->created_at ) );

				$permalink = 'https://twitter.com/'
				             . $tweet->user->screen_name
				             . '/status/'
				             . $tweet->id;

				$formatted_tweet = $this->format_tweet( $tweet->text );

				print '<blockquote class="twitter-tweet">';

				print '<p>' . $formatted_tweet . '</p>';

				print '<cite> &ndash; ' . $tweet->user->name . '</cite> ';

				print '<a href="' . $permalink . '">' . $timestamp . '</a>';

				print '</blockquote>';
			}


			echo '</section>';

		}

	}

	private function format_tweet( $tweet ) {

		$tweet = preg_replace( "/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $tweet );

		$tweet = preg_replace( "/#([A-Za-z0-9\/\.]*)/", "<a target=\"_blank\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $tweet );

		$tweet = preg_replace( "/@([A-Za-z0-9\/\.]*)/", "<a target=\"_blank\" href=\"http://www.twitter.com/$1\">@$1</a>", $tweet );

		return $tweet;

	}

	/**
	 * Outputs the options form on admin
	 *
	 * The options set here define the site's default set of Twitter users to poll
	 * for recent tweets. In the future, individual users will be able to edit the
	 * list of users for their own view of the widget's output.
	 *
	 * @param array $instance The current widget settings.
	 *
	 * @return string The HTML markup for the form.
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		/** TODO: (extra) set interface to check validity of Twitter handles? */
		$usernames = esc_attr( $instance['usernames'] );

		?>
        <p>
            <label for="<?php echo $this->get_field_id( 'usernames' ); ?>"><?php _e( 'Twitter Usernames' ); ?></label>
            <input class="widefat"
                   id="<?php echo $this->get_field_id( 'usernames' ); ?>"
                   name="<?php echo $this->get_field_name( 'usernames' ); ?>"
                   type="text"
                   value="<?php echo $usernames; ?>"/>
        </p>
		<?php

	}

	/**
	 * Processing widget options on save
	 *
	 * Save the options set in the widget form on admin.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance The new instance of the widget.
	 * @param array $old_instance The old instance of the widget.
	 *
	 * @return array The updated instance of the widget.
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		return $new_instance;
	}
}

