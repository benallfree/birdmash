<?php
/*
Plugin Name: Birdmash
Version: 1.0
Author: Haxor
*/

class Birdmash_Widget extends WP_Widget {
    
    /**
     * Sets up the widgets name etc
     */
    
    public function __construct() {
        
        $widget_ops = array(
            'classname' => 'birdmash_widget',
            'description' => 'Multiuser Twitter Mashup'
        );

        parent::__construct('birdmash_widget', 'Birdmash Widget', $widget_ops);
    }
    
    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    
    public function widget($args, $instance) {
        
        require_once 'widget-config.php';
        
    }
    
    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    
    
    public function form($instance) {
        // Populate the plugin settings in the back-end
        if (empty($instance)) {
            $twitter_username          = '';
            $update_count              = '';
            $oauth_access_token        = '';
            $oauth_access_token_secret = '';
            $consumer_key              = '';
            $consumer_secret           = '';
            $title                     = '';
        } else {
            $twitter_username          = $instance['twitter_username'];
            $update_count              = isset($instance['update_count']) ? $instance['update_count'] : 3;
            $oauth_access_token        = $instance['oauth_access_token'];
            $oauth_access_token_secret = $instance['oauth_access_token_secret'];
            $consumer_key              = $instance['consumer_key'];
            $consumer_secret           = $instance['consumer_secret'];
            
            if (isset($instance['title'])) {
                $title = $instance['title'];
            } else {
                $title = "Mashup Plugin Widget";
            }
        }
        
    ?>
    
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>">
            Widget Title
        </label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" 
        name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" 
        value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'twitter_username' ); ?>">
            Twitter Usernames (separated by comma)
        </label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'twitter_username' ); ?>" 
        name="<?php echo $this->get_field_name( 'twitter_username' ); ?>" type="text" 
        value="<?php echo esc_attr( $twitter_username ); ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'update_count' ); ?>">
            Number of tweets to display (default is 3)
        </label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'update_count' ); ?>" 
        name="<?php echo $this->get_field_name( 'update_count' ); ?>" type="number" 
        value="<?php echo esc_attr( $update_count ); ?>" />
    </p>
    <h3>Twitter API Settings</h3>
    <p>Go to <a href="https://apps.twitter.com/">Twitter's app page</a> to get your API settings.</p>
    <hr />
    <p>
        <label for="<?php echo $this->get_field_id( 'oauth_access_token' ); ?>">
            Twitter Access Token
        </label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'oauth_access_token' ); ?>" 
        name="<?php echo $this->get_field_name( 'oauth_access_token' ); ?>" type="text" 
        value="<?php echo esc_attr( $oauth_access_token ); ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'oauth_access_token_secret' ); ?>">
            Twitter Secret Access Token
        </label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'oauth_access_token_secret' ); ?>" 
        name="<?php echo $this->get_field_name( 'oauth_access_token_secret' ); ?>" type="text" 
        value="<?php echo esc_attr( $oauth_access_token_secret ); ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'consumer_key' ); ?>">
            Twitter API Key
        </label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'consumer_key' ); ?>" 
        name="<?php echo $this->get_field_name( 'consumer_key' ); ?>" type="text" 
        value="<?php echo esc_attr( $consumer_key ); ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id( 'consumer_secret' ); ?>">
            Twitter Secret API Key
        </label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'consumer_secret' ); ?>" 
        name="<?php echo $this->get_field_name( 'consumer_secret' ); ?>" type="text" 
        value="<?php echo esc_attr( $consumer_secret ); ?>" />
    </p>

    <?php
    }
    
    public function gettwitterAPI($username, $limit, $oauth_access_token, $oauth_access_token_secret, $consumer_key, $consumer_secret)
    {
        // Reguiring the PHP Wrapper for Twitter API v1.1
        require_once 'twitter-api/TwitterAPI.php';
        
        $settings = array(
            'oauth_access_token' => $oauth_access_token,
            'oauth_access_token_secret' => $oauth_access_token_secret,
            'consumer_key' => $consumer_key,
            'consumer_secret' => $consumer_secret
        );
        
        $url            = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $getfield       = '?screen_name=' . $username . '&count=' . $limit;
        $request_method = 'GET';
        
        $twitter_instance = new TwitterAPIExchange($settings);
        
        $query = $twitter_instance->setGetfield($getfield)->buildOauth($url, $request_method)->performRequest();
        
        $timeline = json_decode($query);
        
        return $timeline;
    }
    
    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        
        $instance['title']                     = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['title']                     = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['twitter_username']          = (!empty($new_instance['twitter_username'])) ? strip_tags($new_instance['twitter_username']) : '';
        $instance['update_count']              = (!empty($new_instance['update_count'])) ? strip_tags($new_instance['update_count']) : '';
        $instance['oauth_access_token']        = (!empty($new_instance['oauth_access_token'])) ? strip_tags($new_instance['oauth_access_token']) : '';
        $instance['oauth_access_token_secret'] = (!empty($new_instance['oauth_access_token_secret'])) ? strip_tags($new_instance['oauth_access_token_secret']) : '';
        $instance['consumer_key']              = (!empty($new_instance['consumer_key'])) ? strip_tags($new_instance['consumer_key']) : '';
        $instance['consumer_secret']           = (!empty($new_instance['consumer_secret'])) ? strip_tags($new_instance['consumer_secret']) : '';
        
        return $instance;
    }
}

add_action('widgets_init', function()
{
    register_widget('Birdmash_Widget');
});

// Adding custom CSS and JS to our plugin

function birdmash_scripts()
{
    wp_register_script('birdmash-script', plugins_url('/js/tweets.js', __FILE__), array(
        'jquery'
    ));
    wp_register_style('birdmash-style', plugins_url('/css/tweets-style.css', __FILE__), array(), '20120208', 'all');
    
    wp_enqueue_style('birdmash-style');
    wp_enqueue_script('birdmash-script');
    
    $birdmashData = array(
        'path' => plugin_dir_url(__FILE__) . 'twitter-api/tweets.php'
    );
    
    wp_localize_script('birdmash-script', 'birdmashVars', $birdmashData);
}

add_action('wp_enqueue_scripts', 'birdmash_scripts');
