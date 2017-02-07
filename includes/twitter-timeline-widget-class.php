<?php

// To develop a widget you need only create a class extending the WP_Widget  parent class
class Twitter_Timeline_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		// widget actual processes
		parent::__construct(
			'twitter-timeline-widget', // Base ID
			__( 'Twitter Timeline Widget', 'twitter_timeline_widget' ), // Name
			array( 'description' => __( 'Displays latest tweets from Twitter', 'twitter_timeline_widget' ), ) // Args
		);
	}

	public function twitter_timeline_widget( $username, $limit, $oauth_access_token, $oauth_access_token_secret, $consumer_key, $consumer_secret ) {
    		require_once 'TwitterAPIExchange.php';
 
    		/** Set access tokens here - see: https://dev.twitter.com/apps/ */
    		$settings = array(
        		'oauth_access_token'        => $oauth_access_token,
        		'oauth_access_token_secret' => $oauth_access_token_secret,
        		'consumer_key'              => $consumer_key,
        		'consumer_secret'           => $consumer_secret
    		);
 
    		$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    		$getfield = '?screen_name=' . $username . '&count=' . $limit;
    		$request_method = 'GET';
     
    		$twitter_instance = new TwitterAPIExchange( $settings );
     
    		$query = $twitter_instance
        		->setGetfield( $getfield )
        		->buildOauth( $url, $request_method )
        		->performRequest();
     
    		$timeline = json_decode($query);
 
    		return $timeline;
	}	

	public function tweet_timeline( $time ) {
    		// Get current timestamp.
    		$now = strtotime( 'now' );
 
    		// Get timestamp when tweet created.
    		$created = strtotime( $time );
 
    		// Get difference.
    		$difference = $now - $created;
 
    		// Calculate different time values.
    		$minute = 60;
    		$hour = $minute * 60;
    		$day = $hour * 24;
    		$week = $day * 7;
 
    		if ( is_numeric( $difference ) && $difference > 0 ) {
 
		        // If less than 3 seconds.
        		if ( $difference < 3 ) {
            			return __( 'right now', 'twitter_timeline_widget' );
       			 }		
 
        		// If less than minute.
        		if ( $difference < $minute ) {
           			 return floor( $difference ) . ' ' . __( 'seconds ago', 'twitter_timeline_widget' );;
        		}
 
        		// If less than 2 minutes.
       		 	if ( $difference < $minute * 2 ) {
            			return __( 'about 1 minute ago', 'twitter_timeline_widget' );
        		}
 
        		// If less than hour.
        		if ( $difference < $hour ) {
            			return floor( $difference / $minute ) . ' ' . __( 'minutes ago', 'twitter_timeline_widget' );
        		}
 
		        // If less than 2 hours.
        		if ( $difference < $hour * 2 ) {
            			return __( 'about 1 hour ago', 'twitter_timeline_widget' );
        		}
 
       			// If less than day.
        		if ( $difference < $day ) {
            			return floor( $difference / $hour ) . ' ' . __( 'hours ago', 'twitter_timeline_widget' );
        		}
 
        		// If more than day, but less than 2 days.
        		if ( $difference > $day && $difference < $day * 2 ) {
            			return __( 'yesterday', 'twitter_timeline_widget' );;
        		}
 
        		// If less than year.
        		if ( $difference < $day * 365 ) {
            			return floor( $difference / $day ) . ' ' . __( 'days ago', 'twitter_timeline_widget' );
        		}
 
        		// Else return more than a year.
        			return __( 'over a year ago', 'twitter_timeline_widget' );
   			 }
		}
	
	/**
	 * Outputs the content of the widget in WordPress front-end
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		$title                     = apply_filters( 'widget_title', $instance['title'] );
   		$username                  = $instance['twitter_username'];
    		$limit                     = $instance['update_count'];
    		$oauth_access_token        = $instance['oauth_access_token'];
    		$oauth_access_token_secret = $instance['oauth_access_token_secret'];
    		$consumer_key              = $instance['consumer_key'];
    		$consumer_secret           = $instance['consumer_secret'];
 
    		echo $args['before_widget'];
 
    		if ( ! empty( $title ) ) {
        		echo $args['before_title'] . $title . $args['after_title'];
    		}		
 
    		// Get the tweets.
    		$timelines = $this->twitter_timeline_widget( $username, $limit, $oauth_access_token, $oauth_access_token_secret, $consumer_key, $consumer_secret );
 
    		if ( $timelines ) {
 
        	// Add links to URL and username mention in tweets.
        	$patterns = array( '@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '/@([A-Za-z0-9_]{1,15})/' );
        	$replace = array( '<a href="$1">$1</a>', '<a href="http://twitter.com/$1">@$1</a>' );
 
        	foreach ( $timelines as $timeline ) {
            		$result = preg_replace( $patterns, $replace, $timeline->text );
 
            		echo '<div>';
                		echo $result . '<br/>';
                		echo $this->tweet_timeline( $timeline->created_at );
            		echo '</div>';
            		echo '<br/>';
        	}
 
    	    } else {
        	_e( 'Error fetching feeds. Please verify the Twitter settings in the widget.', 'twitter_timeline_widget' );
   	    }	
 
    	    echo $args['after_widget'];
	}

	/**
	 * This form  method below creates the widget settings form that will save the OAuth credentials to the database for reuse later by the widget
	 *
	 * @param array $instance
	 */
	public function form( $instance ) {
		if(empty($instance)){		
			$twitter_username = '';
			$update_count = '';
			$oauth_access_token = '';
			$oauth_access_token_secret = '';
			$consumer_key = '';
			$consumer_secret = '';
			$title = '';
		} else {
			$twitter_username = $instance['twitter_username'];
			$update_count = isset($instance['update_count']) ? $instance['update_count'] : 5;
			$oauth_access_token = $instance['oauth_access_token'];
			$oauth_access_token_secret = $instance['oauth_access_token_secret'];
			$consumer_key = $instance['consumer_key'];
			$consumer_secret = $instance['consumer_secret'];

			if (isset($instance['title'])) {
				$title = $instance['title'];
			} else {
				$title = __('Twitter feed', 'twitter-timeline-widget');
			}
		}
	?>
		<p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">
            <?php echo __( 'Title', 'twitter_timeline_widget' ) . ':'; ?>
        </label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'twitter_username' ); ?>">
            <?php echo __( 'Twitter Username (without @)', 'twitter_timeline_widget' ) . ':'; ?>
        </label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'twitter_username' ); ?>" name="<?php echo $this->get_field_name( 'twitter_username' ); ?>" type="text" value="<?php echo esc_attr( $twitter_username ); ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'update_count' ); ?>">
            <?php echo __( 'Number of Tweets to Display', 'twitter_timeline_widget' ) . ':'; ?>
        </label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'update_count' ); ?>" name="<?php echo $this->get_field_name( 'update_count' ); ?>" type="number" value="<?php echo esc_attr( $update_count ); ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'oauth_access_token' ); ?>">
            <?php echo __( 'OAuth Access Token', 'twitter_timeline_widget' ) . ':'; ?>
        </label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'oauth_access_token' ); ?>" name="<?php echo $this->get_field_name( 'oauth_access_token' ); ?>" type="text" value="<?php echo esc_attr( $oauth_access_token ); ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'oauth_access_token_secret' ); ?>">
            <?php echo __( 'OAuth Access Token Secret', 'twitter_timeline_widget' ) . ':'; ?>
        </label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'oauth_access_token_secret' ); ?>" name="<?php echo $this->get_field_name( 'oauth_access_token_secret' ); ?>" type="text" value="<?php echo esc_attr( $oauth_access_token_secret ); ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'consumer_key' ); ?>">
            <?php echo __( 'Consumer Key', 'twitter_timeline_widget' ) . ':'; ?>
        </label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'consumer_key' ); ?>" name="<?php echo $this->get_field_name( 'consumer_key' ); ?>" type="text" value="<?php echo esc_attr( $consumer_key ); ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'consumer_secret' ); ?>">
            <?php echo __( 'Consumer Secret', 'twitter_timeline_widget' ) . ':'; ?>
        </label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'consumer_secret' ); ?>" name="<?php echo $this->get_field_name( 'consumer_secret' ); ?>" type="text" value="<?php echo esc_attr( $consumer_secret ); ?>" />
    </p>
	
	<?php	
	}
	/**
	* When values are entered into the settings form field, they need to be saved to the database. The update method sanitizes the widget form values
	* by stripping out malicious data and then saving the sanitized data to the database.
	*
	*/
	public function update( $new_instance, $old_instance ) {
 	   $instance = array();
     
 	   $instance['title']                      = ( ! empty( $new_instance['title'] ) )                     ? strip_tags( $new_instance['title'] ) : '';
 	   $instance['title']                      = ( ! empty( $new_instance['title'] ) )                     ? strip_tags( $new_instance['title'] ) : '';
 	   $instance['twitter_username']           = ( ! empty( $new_instance['twitter_username'] ) )          ? strip_tags( $new_instance['twitter_username'] ) : '';
 	   $instance['update_count']               = ( ! empty( $new_instance['update_count'] ) )              ? strip_tags( $new_instance['update_count'] ) : '';
 	   $instance['oauth_access_token']         = ( ! empty( $new_instance['oauth_access_token'] ) )        ? strip_tags( $new_instance['oauth_access_token'] ) : '';
 	   $instance['oauth_access_token_secret']  = ( ! empty( $new_instance['oauth_access_token_secret'] ) ) ? strip_tags( $new_instance['oauth_access_token_secret'] ) : '';
 	   $instance['consumer_key']               = ( ! empty( $new_instance['consumer_key'] ) )              ? strip_tags( $new_instance['consumer_key'] ) : '';
 	   $instance['consumer_secret']            = ( ! empty( $new_instance['consumer_secret'] ) )           ? strip_tags( $new_instance['consumer_secret'] ) : '';
 	
 	   return $instance;
	}

}

