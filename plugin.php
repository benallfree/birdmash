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
        
        //Twitter API Settings
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $requestMethod = 'GET';
        $settings = array(
            'oauth_access_token' => "204855451-1PYYPzkfDH0ow8sZFLwn3XuGW0Znb6dMw8cmh3ly",
            'oauth_access_token_secret' => "zpS7OW8VUPU6RRrmC8arsGK9grckcnrXcAmkxXSx7jPP5",
            'consumer_key' => "lrf22drvht235ROffWGbEwpXM",
            'consumer_secret' => "HZ2Sx0KfWvbPBYOYoUrLTV358f0bw1rBpUwB50MkQNq1WXZ7rp"
        );
        
        //Include & Initialize TwitterAPIExchange Class
        include( plugin_dir_path( __FILE__ ) . '/TwitterAPIExchange.php');
        $twitter = new TwitterAPIExchange($settings);
        
        $output = '';
        
        //If title is empty
        if (!empty($instance['title'])){
            echo '<h4>' . $instance['title'] . '</h4>';
        }
        
        //If username is not empty, get the tweets data
        if (!empty($instance['usernames'])) {
            
            $arrAccounts = explode(",",$instance['usernames']);
            
            for($i=0;$i<sizeof($arrAccounts);$i++){
                
                $getfield = '?exclude_replies=true&include_rts=false&count=3&trim_user=true&screen_name=' . trim($arrAccounts[$i]);
                
                //Data for current account
                $objData = json_decode($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest());
                
                echo '<h5>@'.$arrAccounts[$i].'</h5>';
                
                if(sizeof($objData)>0){
                
                    for($j=0;$j<sizeof($objData);$j++){
                        
                        $tweet = $objData[$j]->text;
                        
                        //Convert urls to <a> links
                        $tweet = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $tweet);

                        //Convert hashtags to twitter searches in <a> links
                        $tweet = preg_replace("/#([A-Za-z0-9\/\.]*)/", "<a target=\"_new\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $tweet);

                        //Convert attags to twitter profiles in &lt;a&gt; links
                        $tweet = preg_replace("/@([A-Za-z0-9\/\.]*)/", "<a href=\"http://www.twitter.com/$1\">@$1</a>", $tweet);
                        
                        
                        echo '<p>'.$tweet.'</p>';
                    }
                    
                }
                else{//If not data available
                    echo '<p>No data available</p>';
                }
            }
        }
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		
        $title      =  ! empty( $instance['title'] ) ? $instance['title'] : '';
        $usernames  =  ! empty( $instance['usernames'] ) ? $instance['usernames'] : '';
        $output     = '';
        
        if(isset($instance['error'])){
            $output .= '<p>'.$instance['error'].'</p>';
        }
        
        $output .= '<p><label for="'.$this->get_field_id('title').'">Title:</label><br>';
        $output .= '<input type="text" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" value="'.esc_attr($title).'" /></p>';
        
        $output .= '<p><label for="'.$this->get_field_id('usernames').'">Twitter Usernames:</label><br>';
        $output .= '<input type="text" id="'.$this->get_field_id('usernames').'" name="'.$this->get_field_name('usernames').'" value="'.esc_attr($usernames).'" />';
        $output .= '<br><small>Enter a comma-separated list of own accounts.</small></p>';
        
		echo $output;
		
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
        
        $instance['title'] = strip_tags( $new_instance[ 'title' ] );
        
        if (empty($new_instance['usernames'])) {
            $instance['error'] = "You must enter at least one Twitter account.";
        }
        else{
            $instance['usernames'] = strip_tags( $new_instance[ 'usernames' ] );
        }
        
        return $instance;
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'Birdmash_Widget' );
});
