<?php

ini_set('display_errors', 1);

require_once('TwitterAPI.php');

$settings = array(
    'oauth_access_token' => "1449019849-lj28YlWh6EPV2BNclewFsyYSXmpm2akkP7q1mno",
    'oauth_access_token_secret' => "h14tn38vdut0cuIFuon2K0owO2Pr31Pif50EZ269pDV5Z",
    'consumer_key' => "iktOSS4h376n9tvFJfsSuy5N9",
    'consumer_secret' => "K9qrOOy8A4JuvKcdXExGzaNTQKiw9orOqNh6lhFy0VPZH37iQM"
);

$limit = 3;
$username = $_GET["namevar"];
$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
$request_method = 'GET';
$twitter_instance = new TwitterAPIExchange( $settings );

// For accepting comma-separated names from the front-end
// $my_tweets = array();
// 	foreach($username as $name) {
 		
//  		$getfield = '?screen_name=' . $name . '&count=' . $limit;

//  		$my_tweets[] = $twitter_instance
//         ->setGetfield( $getfield )
//         ->buildOauth( $url, $request_method )
//         ->performRequest();
// 	}		

$getfield = '?screen_name=' . $username . '&count=' . $limit;

 		$my_tweets = $twitter_instance
        ->setGetfield( $getfield )
        ->buildOauth( $url, $request_method )
        ->performRequest();

    if(isset($my_tweets->errors))
		{           
		    echo 'Error :'. $my_tweets->errors[0]->code. ' - '. $my_tweets->errors[0]->message;
		} else {
		
		echo $my_tweets;
		

		}

?>