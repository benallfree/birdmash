<?php

require_once  'class-loader.php';

if ( ! class_exists( 'Birdmash_Widget', false ) ) {
class Birdmash_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 
			'classname'   => 'birdmash_widget',
			'description' => 'Multiuser Twitter Mashup',
		);
		parent::__construct( 'birdmash_widget', 'Birdmash Widget', $widget_ops );
	}

	private function loader() {
        $this->loader = new birdMashPlugloader ($this->get_version());
    }

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		 
 		 extract($args, EXTR_SKIP);
  		 $title    = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 		 $handles  = empty($instance['text']) ? '' : $instance['text'];
 		 $count    = empty($instance['count']) ? '' : $instance['count'];
 		 $retweets = empty($instance['retweets']) ? '' : $instance['retweets'];; // 0 to exclude, 1 to include
  		
  		 // Twitter Handle Array
    	 $usernames = explode(",", $handles);

    	// -- Write the Widget output
  		echo (isset($before_widget)?$before_widget:'');
		if (!empty($title)):
    		echo $before_title . $title . $after_title;;
    	endif;
  		
  		//$this->callTwitterFeed($usernames, $count, $retweets);
  		

  		// -- Twitter Output
  		foreach($usernames as $user){
  			//$output  = '<h2>Tweets from '.$user .'</h2>';
    	 	$this->callTwitterFeed($usernames, $user, $count, $retweets);

    	}

    	// -- End Twitter Output

		echo (isset($after_widget)?$after_widget:'');

  		
	}

	private function oauth_encode($data){
		if(is_array($data)){
			return array_map('oauth_encode', $data);
		} else if(is_scalar($data)) {
			return str_ireplace(array('+', '%7E'), array(' ', '~'), rawurlencode($data));
		} else {
			return '';
		}
	}


	public function callTwitterFeed($usernames, $user, $count, $retweets){
	
		foreach($usernames as $userhandle=>$key){
			if($key == $user):
				$username = $key;
			endif;
		}

			$settings = array(
			  'consumer_key'        => 'bfM8gupQD01771lL8RZz6rPZy',
			  'consumer_secret'     => 'mi8H1mBtbOaGg9x5YtiL3ievEvRVct7o4wIsIoqpJlUdCGTx2M',
			  'access_token'        => '3130953237-7YJ7P5oqfdGXPc47ywPTV2qPitB1CHRsH3Cg7Am',
			  'access_token_secret' => 'Oo4QVAcAOPwFYOCfw5TF19D8TxUtgLmYKS88gNC8NxtgM'
			);

			// BUILD THE API AUTH
			$api_url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
			$api_params = array(
				'screen_name' => $username,
				'count' => '3',
				'include_rts' => $retweets
			);

			$oauth_params = array(
				'oauth_consumer_key' => $settings['consumer_key'],
				'oauth_nonce' => md5(microtime() . mt_rand()),
				'oauth_signature_method' => 'HMAC-SHA1',
				'oauth_timestamp' => time(),
				'oauth_token' => $settings['access_token'],
				'oauth_version' => '1.0',
			);

		
			// SIGNATURES FOR API

			$sign_params = array_merge($oauth_params, $api_params);
			uksort($sign_params, 'strcmp');
			foreach ($sign_params as $k => $v) {
				$sparam[] = $this->oauth_encode($k) . '=' . $this->oauth_encode($v);
			}
			
			$sparams     = implode('&', $sparam);
			$base_string = 'GET&' . $this->oauth_encode($api_url) . '&' . $this->oauth_encode($sparams);
			$signing_key = $this->oauth_encode($settings['consumer_secret']) . '&' . $this->oauth_encode($settings['access_token_secret']);
			$oauth_params['oauth_signature'] = $this->oauth_encode(base64_encode(hash_hmac('sha1', $base_string, $signing_key, TRUE)));

			// AUTH HEADER
			uksort($oauth_params, 'strcmp');
			foreach ($oauth_params as $k => $v) {
			  $hparam[] = $k . '="' . $v . '"';
			} 

			$hparams    = implode(', ', $hparam);
			$headers    = array();
			$headers['Expect'] = '';
			$headers['Authorization'] = 'OAuth ' . $hparams; 

			foreach ($headers as $k => $v) {
				$curlheaders[] = trim($k . ': ' . $v);
			}
			
			// REQUIRED PARAMETERS
			foreach ($api_params as $k => $v) {
				$rparam[] = $k . '=' . $v;
			}
		
			$rparams = implode('&', $rparam);

			// CURL - GET THE RESPONSE AND OUTPUT

			$ch = curl_init();    
			curl_setopt($ch, CURLOPT_URL, $api_url . '?' . $rparams);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $curlheaders);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 0 );
			$response = curl_exec($ch);
			$code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$info     = curl_getinfo($ch);
			$error    = curl_error($ch);
			$errno    = curl_errno($ch);
			
			
			if($code != 200){
				echo "<script>console.log('".$username ." Not found to be a valid user');</script>";
			} else {
				
				$jsonTweets = json_decode($response, true);
				$this->passitOn($jsonTweets, $username);
			}
	}

	public function passitOn($jsonTweets, $username){
		echo '<h2>Tweets from '.$username .'</h2>';

		// DISPLAY THE TWEETS
		foreach($jsonTweets as $status):
			$userStatus = htmlentities($status['text'], ENT_QUOTES, 'UTF-8');
			$userStatus = preg_replace('/http:\/\/t.co\/([a-zA-Z0-9]+)/i', '<a href="http://t.co/$1">http://$1</a>', $userStatus);
			$userStatus = preg_replace('/https:\/\/t.co\/([a-zA-Z0-9]+)/i', '<a href="https://t.co/$1">http://$1</a>', $userStatus);
			$output  = '<p>'.$userStatus.'</p>';
			$output .= '<p><a href="https://twitter.com/intent/user?screen_name='.$username.'">@'.$username.'</a></p>';
			echo $output;	
		endforeach;
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// PART 1: Extract the data from the instance variable
   			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'count' => '', 'retweets' => '' ) );
   			$title    = $instance['title'];
   			$handles  = $instance['text'];
   			$count    = $instance['count'];
   			$retweets = $instance['retweets']; 

   			$noSelected  = '';
   			$yesSelected = '';

   			if ($retweets == '0'){ $noSelected = 'selected';}
   			if ($retweets == '1'){ $yesSelected = 'selected';}


		   	$output  = '<p><label for="'.$this->get_field_id('title').'">Twitter Feed Title:';
		   	$output .= '<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.esc_attr($title).'" />';
		    $output .= '</label></p>';
		    $output .= '<label for="'.$this->get_field_id('text').'">Twitter Handles (seperate with a comma): ';
		    $output .= '<input class="widefat" id="'.$this->get_field_id('text').'" name="'.$this->get_field_name('text').'" type="text" value="'.esc_attr($handles).'" />';
		    $output .= '</label>';
		    $output .= '<label for="'.$this->get_field_id('count').'">Tweets per User: ';
		    $output .= '<input class="widefat" id="'.$this->get_field_id('count').'" name="'.$this->get_field_name('count').'" type="text" value="'.esc_attr($count).'" />';
		    $output .= '</label>';
		    $output .= '<label for="'.$this->get_field_id('retweets').'">Allow Retweets: ';
		    $output .= '<select class="widefat" id="'.$this->get_field_id('retweets').'" name="'.$this->get_field_name('retweets').'">
		    		       <option value="0" '.$noSelected.'>No</option>
		    		       <option value="1" '.$yesSelected.'>Yes</option>
		    		    </select>';
		    		
		    $output .= '</label>';
    
    echo  $output;
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$instance              = $old_instance;
  		$instance['title']     = $new_instance['title'];
  		$instance['text']      = $new_instance['text'];
  		$instance['count']     = $new_instance['count'];
  		$instance['retweets']  = $new_instance['retweets'];
  		return $instance;
	}
}

}

add_action( 'widgets_init', function(){
	register_widget( 'Birdmash_Widget' );
});
