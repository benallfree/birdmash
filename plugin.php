<?php
/*
Plugin Name: Birdmash
Version: 1.0
Author: Haxor
*/

require "vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

class Birdmash_Widget extends WP_Widget {
    private $tweets = array();

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

        $this->get_tweets();

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

    /**
     * Retreives tweets cached in DB or places call to Twitter API
     */
    protected function save_tweets(){
        // Get settings for API keys/tokens/secrets
        $settings = $this->get_settings();
		if(array_key_exists($this->number, $settings)){
			$instance = $settings[$this->number];
        }

        $stored_tweets = get_transient('stored_tweets');
        if($stored_tweets && $stored_tweets['handles'] == $instance['handles']){
            $this->tweets = $stored_tweets['tweets'];
            return;
        }

        // Make new connection to Twitter
        $connection = new TwitterOAuth($instance['consumer_key'], $instance['consumer_secret'], $instance['access_token'], $instance['access_token_secret']);

        // Get 3 most recent statuses for each user, add to $tweets
        foreach($instance['handles'] as $twitter_handle){
            $twitter_handle = str_replace('@', '', $twitter_handle);
            $statuses = $connection->get("statuses/user_timeline", ["screen_name" => $twitter_handle, "count" => 3]);

            if($connection->getLastHttpCode() == 200){
                foreach($statuses as $tweet){
                    $this->tweets[] = $tweet;
                }
            }
            else{
                $this->tweets = null;
            }
        }

        // Sort tweets by creation date - most recent first
        usort($this->tweets, function($a, $b){
            return strtotime($a->created_at) < strtotime($b->created_at) ? 1 : -1;;
        });

        // We will save $instance as a transient, so add the tweets to this transient
        $instance['tweets'] = $this->tweets;

        // Store the transient
        set_transient('stored_tweets', $instance, HOUR_IN_SECONDS);
    }

    /**
     * Takes returned tweets and places data in HTML
     */
    public function get_tweets(){
        $this->save_tweets();

        if(!$this->tweets){
            ?>
            <p>Sorry, no tweets to dispay at this time.</p>
            <?php
            return;
        }

        //var_dump($this->tweets);
        foreach($this->tweets as $tweet):
        ?>
            <div class="bm_tweet">
                <span class="bm_tweet-text"><?php echo $tweet->text; ?></span>
                <span class="bm_tweet-time">--<?php echo $this->time_ago($tweet->created_at); ?></span>
                <span class="bm_tweet-user">@<?php echo $tweet->user->screen_name; ?></span>
                <img src="<?php echo $tweet->user->profile_image_url; ?>" class="bm_tweet-pic" />
            </div>
        <?php
        endforeach;
    }

    protected function time_ago($date_string){
        $now  = time();
        $then = strtotime($date_string);
        $diff = $now - $then;

        if($diff < 2){
            return "right now";
        }
        if($diff < MINUTE_IN_SECONDS){
            return "{$diff} seconds ago";
        }
        if($diff < MINUTE_IN_SECONDS * 2){
            return "about 1 minute ago";
        }
        if($diff < HOUR_IN_SECONDS){
            return floor($diff / MINUTE_IN_SECONDS) . " minutes ago";
        }
        if($diff < HOUR_IN_SECONDS * 2){
            return "about 1 hour ago";
        }
        if($diff < DAY_IN_SECONDS){
            return floor($diff / HOUR_IN_SECONDS) . " hours ago";
        }
        if($diff > DAY_IN_SECONDS && $diff < DAY_IN_SECONDS * 2){
            return "yesterday";
        }
        if($diff < DAY_IN_SECONDS * 365){
            return floor($diff / DAY_IN_SECONDS) . " days ago";
        }
        else{
            return "over a year ago";
        }
    }
}

add_action( 'widgets_init', function(){
    register_widget( 'Birdmash_Widget' );
});
