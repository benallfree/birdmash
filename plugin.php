<?php
/*
Plugin Name: Birdmash
Description: Simple Twitter Display Widget
Version: 1.0
Author: Haxor
Text Domain: birdmash
*/

class birdmash extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'birdmash',
			'description' => 'Multiuser Twitter Mashup'
		);
		parent::__construct( 'birdmash', 'Birdmash Widget', $widget_ops );
    
 		// Load (enqueue) some JS in Admin ONLY on widgets page
		add_action('admin_enqueue_scripts', array(&$this, 'BM_load_admin_scripts'));

	}

	// ADMIN - Lets load some JS to aid widget display in Appearance->Widgets
	function BM_load_admin_scripts( $hook ) {
		if( $hook != 'widgets.php' )
			return;

		wp_enqueue_script('BM_admin_js', plugins_url( '/birdmash-master/js/birdmashAdmin.js' , dirname(__FILE__) ), array('jquery'));
	}
	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		// Include Twitter OAUTH library
  	if ( ! class_exists('TwitterOAuth') )
			include 'twitteroauth/twitteroauth.php';

		extract($args, EXTR_SKIP);

		//Setup Twitter API OAuth tokens
		$BM_consumerKey 		  = trim( $instance['consumerKey'] );
		$BM_consumerSecret 	  = trim( $instance['consumerSecret'] );
		$BM_accessToken 		  = trim( $instance['accessToken'] );
		$BM_accessTokenSecret	= trim( $instance['accessTokenSecret'] );
		$BM_cache_time      	= $instance['cache-time'];
    
		echo $before_widget;
		$BM_title 				= empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    
		if (!empty($BM_title))
			echo $before_title . $BM_title . $after_title;
    
    ?>
    <ul class="tweets">
    <?php
    
    $follow         = $instance['twitter-ids'];
    $transientName  = $instance['UID'];
    
    if ( false === ($tweets = get_transient( $transientName ) ) ) {
      $connection = new TwitterOAuth(
        $BM_consumerKey,   		// Consumer key
        $BM_consumerSecret,  	// Consumer secret
        $BM_accessToken,   		// Access token
        $BM_accessTokenSecret	// Access token secret
      );
    
      $tweets = $this->getTweets( $connection, $BM_cache_time, $follow );
			set_transient( $transientName, $tweets, 60 * $BM_cache_time );  // cache tweets
      
    }
    
    foreach ( $tweets as $tweet ) {
      $screen_name  = $tweet['screen_name'];
      $text         = $tweet['text'];
      echo '<li class="tweet">@' . $screen_name . ' - ' . $text . '</li>';
    }
      
    ?>
    </ul>
    <?php

		echo $after_widget;

	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
    // setup form defaults
    $title              = ( $instance['title'] ?: __( 'Recent Tweets', 'birdmash' ) );
    $twitter_ids        = ( $instance['twitter-ids'] ?: __( '@945country,@wibwnews', 'birdmash' ) );
    $consumerKey        = ( $instance['consumerKey'] ?: __( 'xxxxxxxxxxxx', 'birdmash' ) );
    $consumerSecret     = ( $instance['consumerSecret'] ?: __( 'xxxxxxxxxxxx', 'birdmash' ) );
    $accessToken        = ( $instance['accessToken'] ?: __( 'xxxxxxxxxxxx', 'birdmash' ) );
    $accessTokenSecret  = ( $instance['accessTokenSecret'] ?: __( 'xxxxxxxxxxxx', 'birdmash' ) );
    $cache_time         = ( $instance['cache-time'] ?: __( 60, 'birdmash' ) );
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'birdmash') ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('twitter-ids'); ?>"><?php _e('Twitter IDs (comma separated):', 'birdmash') ?> <input class="widefat" id="<?php echo $this->get_field_id('twitter-ids'); ?>" name="<?php echo $this->get_field_name('twitter-ids'); ?>" type="text" value="<?php echo esc_attr($twitter_ids); ?>" /></label>
		</p>
		<div class="secrets" style="background:#d6eef9; margin-bottom:10px;">
			<h4 class="button-secondary" style="width:100%; text-align:center;"><?php _e('Twitter API settings', 'birdmash') ?> <span style="font-size:75%;">&#9660;</span></h4>
			<div style="padding:10px;">
				<p>
					<label for="<?php echo $this->get_field_id('consumerKey'); ?>"><?php _e('Consumer Key:', 'birdmash') ?> <input class="widefat" id="<?php echo $this->get_field_id('consumerKey'); ?>" name="<?php echo $this->get_field_name('consumerKey'); ?>" type="text" value="<?php echo esc_attr($consumerKey); ?>" /></label>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('consumerSecret'); ?>"><?php _e('Consumer Secret:', 'birdmash') ?> <input class="widefat" id="<?php echo $this->get_field_id('consumerSecret'); ?>" name="<?php echo $this->get_field_name('consumerSecret'); ?>" type="text" value="<?php echo esc_attr($consumerSecret); ?>" /></label>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('accessToken'); ?>"><?php _e('Access Token:', 'birdmash') ?> <input class="widefat" id="<?php echo $this->get_field_id('accessToken'); ?>" name="<?php echo $this->get_field_name('accessToken'); ?>" type="text" value="<?php echo esc_attr($accessToken); ?>" /></label>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('accessTokenSecret'); ?>"><?php _e('Access Token Secret:', 'birdmash') ?> <input class="widefat" id="<?php echo $this->get_field_id('accessTokenSecret'); ?>" name="<?php echo $this->get_field_name('accessTokenSecret'); ?>" type="text" value="<?php echo esc_attr($accessTokenSecret); ?>" /></label>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('cache-time'); ?>"><?php _e('Cache Time (in minutes):', 'birdmash') ?> <input class="widefat" id="<?php echo $this->get_field_id('cache-time'); ?>" name="<?php echo $this->get_field_name('cache-time'); ?>" type="text" value="<?php echo esc_attr($cache_time); ?>" /></label>
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
    $instance = $old_instance;
    
 		$instance['UID']                = ( empty( $instance['UID']) ) ? uniqid() : $instance['UID'] ;
    $instance['title'] 		  		    = strip_tags( $new_instance['title'] );
 		$instance['twitter-ids']		    = strip_tags( $new_instance['twitter-ids'] );
 		$instance['consumerKey']		    = trim( strip_tags( $new_instance['consumerKey'] ) );
 		$instance['consumerSecret']	    = trim( strip_tags( $new_instance['consumerSecret'] ) );
 		$instance['accessToken']		    = trim( strip_tags( $new_instance['accessToken'] ) );
 		$instance['accessTokenSecret']	= trim( strip_tags( $new_instance['accessTokenSecret'] ) );
 		$instance['cache-time']		      = $new_instance['cache-time'];
    
    // delete any cached tweets for this widget
    delete_transient( $instance['UID'] );
    
    return $instance;

	}
  
  function getTweets( $connection, $cache_time, $handles ) {
    
    $ret = array();

    $users = explode( ',', $handles );

    foreach ( $users as $user ) {
      $user = trim( $user, '@ ' );  // remove leading @
      $tweets = $connection->get(
        'statuses/user_timeline',
          array(
            'screen_name'     => $user,
            'count'           => 3,
            'exclude_replies' => true
          )
      );
      
      // Return tweets away if fetch was successful
      if($connection->http_code == 200) {
        // Process tweets
        foreach( $tweets as $tweet) {
          $time = date_parse( $tweet->created_at );
          $text = $tweet->text;
          $ret[] = array(
            'tweet-id'    => $tweet->id_str,
            'screen_name' => $tweet->user->screen_name,
            'tweet-link'  => 'https://twitter.com/' . $tweet->user->screen_name . '/status/' . $tweet->id_str,
            'image'       => $tweet->user->profile_image_url_https,
            'text'        => $this->process_links( $text ),
            'date_create' => mktime($time['hour'], $time['minute'], $time['second'], $time['month'], $time['day'], $time['year']),  // convert to unix time
          );
        }
      }
    }
    usort( $ret, function ( $i1, $i2 ) {
      if ($i1['date_create'] == $i2['date_create']) return 0;
      return $i1['date_create'] < $i2['date_create'] ? 1 : -1; }
    );
    return $ret;
  }

	function process_links( $text ) {

		// NEW Link Creation from clickable items in the text
		$text = preg_replace('/((http)+(s)?:\/\/[^<>\s]+)/i', '<a href="$0" target="_blank" rel="nofollow">$0</a>', $text );
		// Clickable Twitter names
		$text = preg_replace('/[@]+([A-Za-z0-9-_]+)/', '<a href="http://twitter.com/$1" target="_blank" rel="nofollow">@$1</a>', $text );
		// Clickable Twitter hash tags
		$text = preg_replace('/[#]+([A-Za-z0-9-_]+)/', '<a href="http://twitter.com/search?q=%23$1" target="_blank" rel="nofollow">$0</a>', $text );
		// END TWEET CONTENT REGEX
		return $text;

	}
  
  
  }

add_action( 'widgets_init', function(){
	register_widget( 'birdmash' );
});
