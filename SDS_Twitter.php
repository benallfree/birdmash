<?php

class SDS_Twitter
{
	public $tweets;
	private $arg;

	public function __construct($args)
	{
		try
		{
			// required
			if ( !isset($args['oauth_access_token']) ) throw new Exception('OAuth access token not set');
			if ( !isset($args['oauth_access_token_secret']) ) throw new Exception('OAuth access token secret not set');
			if ( !isset($args['consumer_key']) ) throw new Exception('Consumer key not set');
			if ( !isset($args['consumer_secret']) ) throw new Exception('Consumer secret not set');
			if ( !isset($args['screen_name']) ) throw new Exception('Screen name is not set');

			// optional
			if ( !isset($args['num_tweets']) ) $args['num_tweets'] = 3;

			$this->args = $args;

			$cached_tweets = get_option('sds_tweets_'.$this->args['screen_name']);

			if ( $cached_tweets == '' || ( isset($cached_tweets['timestamp']) && (current_time('timestamp') - $cached_tweets['timestamp']) > (60 * 60) ) )
			{

				$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";

				$oauth = array(
					'screen_name' => $args['screen_name'],
					'count' => $args['num_tweets'],
					'oauth_consumer_key' => $args['consumer_key'],
					'oauth_nonce' => time(),
					'oauth_signature_method' => 'HMAC-SHA1',
					'oauth_token' => $args['oauth_access_token'],
					'oauth_timestamp' => time(),
					'oauth_version' => '1.0'
				);

				$base_info = $this->build_base_string($url, 'GET', $oauth);
				$composite_key = rawurlencode($args['consumer_secret']) . '&' . rawurlencode($args['oauth_access_token_secret']);
				$oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
				$oauth['oauth_signature'] = $oauth_signature;

				// Make Requests
				$header = array($this->build_authorization_header($oauth), 'Expect:');
				$options = array(
					CURLOPT_HTTPHEADER => $header,
					CURLOPT_HEADER => false,
					CURLOPT_URL => $url . '?screen_name='.$args['screen_name'].'&count='.$args['num_tweets'],
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_CONNECTTIMEOUT => 4
				);

				$feed = curl_init();
				curl_setopt_array($feed, $options);
				$json = curl_exec($feed);
				curl_close($feed);

				if ( $json )
				{
					$thetweets = json_decode($json);
					$new_tweets['timestamp'] = current_time('timestamp');
					foreach ( $thetweets as $thetweet )
					{
						$new_tweets['tweets'][] = array(
							'text' => $thetweet->text,
							'created_at' => $thetweet->created_at,
							'link' => 'https://twitter.com/'.$args['screen_name'].'/statuses/'.$thetweet->id
						);
					}
					update_option('sds_tweets_'.$this->args['screen_name'],$new_tweets);
				}
				else
				{
					$new_tweets = array(
						'timestamp' => current_time('timestamp'),
						'tweets' => false
					);
					update_option('sds_tweets_'.$this->args['screen_name'],$new_tweets);
				}

				$this->tweets = $new_tweets['tweets'];
			}
			else if ( isset($cached_tweets['tweets']) && is_array($cached_tweets['tweets']) )
			{
				$this->tweets = $cached_tweets['tweets'];
			}
			else
			{
				$this->tweets = false;
			}
		}
		catch ( Exception $e )
		{
			$this->error = $e->getMessage();
		}
	}

	private function build_base_string($baseURI, $method, $params)
	{
		$r = array();
		ksort($params);
		foreach($params as $key=>$value){
			$r[] = "$key=" . rawurlencode($value);
		}
		return $method."&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $r));
	}

	private function build_authorization_header($oauth)
	{
		$r = 'Authorization: OAuth ';
		$values = array();
		foreach($oauth as $key=>$value)
			$values[] = "$key=\"" . rawurlencode($value) . "\"";
		$r .= implode(', ', $values);
		return $r;
	}

	public function get_tweets()
	{
		$posts = array();
		if ( $this->tweets ) {
			foreach ( $this->tweets as $tweet )
			{
				$timestamp = strtotime($tweet['created_at']);
				$posts[$timestamp] = array(
					'screen_name' => $this->args['screen_name'],
					'type' => 'twitter',
					'text' => $tweet['text'],
					'link' => $tweet['link'],
					'created_at' => $tweet['created_at']
				);
			}

			return $posts;
		}
		else 
		{
			return 'no posts';
		}
	}
}