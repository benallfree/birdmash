<?php

use Abraham\TwitterOAuth\TwitterOAuth as Twitter;

/**
 * Get tweets from Twitter user timeslines based on usernames
 * passed into widget settings.
 */
class Birdmash_Tweets {

	/**
	 * Twitter oAuth connection
	 * @var TwitterOAuth object
	 */
	private $connection;

	/**
	 * Max number of tweets/username
	 * @var integer
	 */
	private $count = 3;

	/**
	 * Exclude replies in timelines retrieved.
	 * @var boolean
	 */
	private $exclude_replies = true;

	/**
	 * Get tweets from Twitter based on Birdmash Widget settings
	 *
	 * @param  string $usernames comma separated usernames
	 * @return array formatted array of tweets
	 */
	public function get_tweets( $usernames ) {
		$config = $this->get_config();
		$this->connection = new Twitter(
			$config['consumer'],
			$config['consumer_secret'],
			$config['access'],
			$config['access_secret']
		);
		// Get the usernames from the widget's settings as an array
		$usernames = array_map( 'trim', explode( ',', $usernames ) );
		if ( ! empty( $usernames ) ) {
			return $this->get_timelines( $usernames );
		}
	}

	/**
	 * Create a reverse chronological group of tweets
	 *
	 * @param  array $usernames array of usernames to query API for
	 * @return array formatted array of tweets
	 */
	private function get_timelines( $usernames ) {
		$tweets = [];
		if ( false === $tweets = get_transient( 'birdmash_tweets' ) ) {
			foreach ( $usernames as $username ) {
				$timelines[] = $this->connection->get( "statuses/user_timeline/{$username}", [ 'count' => $this->count, 'exclude_replies' => $this->exclude_replies ] );
				if ( 200 === $this->connection->getLastHttpCode() ) {
					foreach ( $timelines as $timeline ) {
						foreach ( $timeline as $key => $tweet_data ) {
							$tweets[ $tweet_data->id ] = [
								'id' => $tweet_data->id,
								'text' => $tweet_data->text,
								'created_at' => $this->get_formatted_timestamp( $tweet_data->created_at ),
								'name' => $tweet_data->user->name,
								'username' => $tweet_data->user->screen_name,
								'avatar' => $tweet_data->user->profile_image_url_https ? $tweet_data->user->profile_image_url_https : '',
							];
						}
					}
				}
			}
			arsort( $tweets );
			set_transient( 'birdmash_tweets', $tweets, HOUR_IN_SECONDS );
		}
		return $tweets;
	}

	/**
	 * Get configuration array
	 *
	 * @return array Twitter config values
	 */
	private function get_config() {
		require_once( BIRDMASH_PLUGIN_DIR . 'inc/config.php' );
		return $config;
	}

	/**
	 * Format a timestamp to a relative equivalent
	 *
	 * @param  string $timestamp timestamp
	 * @return string relative time
	 */
	public function get_formatted_timestamp( $timestamp ) {
		$relative_time = '';
		$postfix = ' ago';
		$tz = new DateTimeZone( 'America/New_York' );
		$now = new DateTime();
		$dt = new DateTime( $timestamp );
		$now->setTimezone( $tz );
		$dt->setTimezone( $tz );

		$diff = intval( $now->getTimestamp() - $dt->getTimestamp() );
		if ( $diff < 60 ) {
			return $diff . ' second' . ( 1 !== $diff ? 's' : '' ) . $postfix;
		}

		$diff = round( $diff / 60 );
		if ( $diff < 60 ) {
			return $diff . ' minute' . ( 1 !== $diff ? 's' : '' ) . $postfix;
		}

		$diff = round( $diff / 60 );
		if ( $diff < 24 ) {
			return $diff . ' hour' . ( 1 !== $diff ? 's' : '' ) . $postfix;
		}

		$diff = round( $diff / 24 );
		if ( $diff < 7 ) {
			return $diff . ' day' . ( 1 !== $diff ? 's' : '' ) . $postfix;
		}

		$diff = round( $diff / 7 );
		if ( $diff < 4 ) {
			return $diff . ' week' . ( 1 !== $diff ? 's' : '' ) . $postfix;
		}

		$diff = round( $diff / 4 );
		if ( $diff < 12 ) {
			return $diff . ' month' . ( 1 !== $diff ? 's' : '' ) . $postfix;
		}

		return $dt->format( 'M d @ g:i A' );
	}
}
