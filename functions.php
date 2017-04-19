<?php
define('CONSUMER_KEY', encrypt_decrypt( "decrypt", get_option( "birdmash_application_key" ) ) );
define('CONSUMER_SECRET', encrypt_decrypt( "decrypt", get_option( "birdmash_application_secret" ) ) );

function encrypt_decrypt($action, $string) {
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = get_option( "birdmash_application_salt", '1234' );
    $secret_iv = substr( get_option( "birdmash_application_salt", '123456' ), 0, 5 );

    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    }
    else if( $action == 'decrypt' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}

function sortTweetArray( $a, $b )
{
	$v1 = strtotime( $a->created_at );
	$v2 = strtotime( $b->created_at );

	return $v1 < $v2 ? -1 : 1;
}

function updateTwitterFeed()
{
	try
	{
	$authenticated_user = wp_get_current_user();
	$user_handles = array_filter( explode(",", $_POST['user_handles']) );
	$user_twitter_handles = ( is_user_logged_in() ) ? get_user_meta( $authenticated_user->ID, "birdmash_twitter_user_handles" ) : $_COOKIE["birdmash_twitter_user_handles"];
	$twitter_handles = array_filter( explode( ",", get_option( "birdmash_twitter_handles" ) ) );
	$user_handles = ( !is_array($user_handles) ) ? array($user_handles) : $user_handles;
	$tweet_array = array();

	if ( false !== $user_handles && !is_user_logged_in() ):
		unset($_COOKIE["birdmash_twitter_user_handles"]);
		setcookie( "birdmash_twitter_user_handles", null, -1, '/' );
		setcookie( "birdmash_twitter_user_handles", json_encode( $user_handles ), (time()+3600), "/" );
	elseif ( false !== $user_handles && is_user_logged_in() ):
		unset($_COOKIE["birdmash_twitter_user_handles"]);
		setcookie( "birdmash_twitter_user_handles", null, -1, '/' );
		setcookie( "birdmash_twitter_user_handles", json_encode( $user_handles ) );
		update_user_meta( $authenticated_user->ID, "birdmash_twitter_user_handles", $user_handles );
	elseif ( false === $user_handles && !is_user_logged_in() ):
		unset($_COOKIE["birdmash_twitter_user_handles"]);
		setcookie( "birdmash_twitter_user_handles", null, -1, '/' );
		setcookie( "birdmash_twitter_user_handles", json_encode($user_handles ) );
	elseif ( false === $user_handles && is_user_logged_in() ):
		unset($_COOKIE["birdmash_twitter_user_handles"]);
		setcookie( "birdmash_twitter_user_handles", null, -1, '/' );
		setcookie( "birdmash_twitter_user_handles", ( is_array( $user_handles ) ) ? json_encode( $user_handles ) : $user_handles );
		update_user_meta( $authenticated_user->ID, "birdmash_twitter_user_handles", $user_handles );
	endif;

	if ( !is_array($user_handles) ): $user_handles = json_decode($user_handles); endif;
	$unfiltered_handles = array_merge( $twitter_handles, $user_handles );
	$merged_handles = array_map( "arr_map", $unfiltered_handles );

		if ( !empty($merged_handles) && isset($merged_handles[0]) && $merged_handles[0] !== "" ):
				$twitter = new \Abraham\TwitterOAuth\TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
			foreach ($merged_handles as $handle ):
				if ( 0 === strlen( trim( $handle ) ) ): continue; endif;
					$tweets = $twitter->get('statuses/user_timeline', ['screen_name' => trim( $handle ), 'count' => 3]);
				error_log("Last HTTP: ".$twitter->getLastHttpCode() );

					if ( $twitter->getLastHttpCode() !== 200 ): continue; endif;
				if ( !empty($twitter->error) ):
				else:
					foreach ( $tweets as $tweet):
						array_push( $tweet_array, $tweet );
					endforeach;
				endif;
			endforeach;
			usort($tweet_array, "sortTweetArray");
			$reversed_array = array_reverse($tweet_array);
			foreach($reversed_array as $tweet):
				$created_at = $tweet->created_at;
				$id = $tweet->id;
				$id_str = $tweet->id_str;
				$text = $tweet->text;
				$truncated = $tweet->truncated;
				$entities = $tweet->entities;
				$hashtags = $entities->hashtags;
				$symbols = $entities->symbols;
				$user_mentions = $entities->user_mentions;
				$screen_name = $tweet->user->screen_name;
				$name = $tweet->user->name;
				$in_reply_to_status_id = $tweet->in_reply_to_status_id;
				$in_reply_to_status_id_str = $tweet->in_reply_to_status_id_str;
				$profile_image_url = $tweet->user->profile_image_url;

				echo '<div class="content">';
				echo '<div class="stream-item-header">';
				echo '<a class="account-group js-account-group js-action-profile js-user-profile-link js-nav" href="https://twitter.com/'.$name.'">';
				echo '<img class="avatar js-action-profile-avatar" src="'.$profile_image_url.'" alt="">';
				echo '<div class="FullNameSpanGroup">';
				echo '<span class="FullNameGroup"><strong>'.$name.'</strong></span>';
				echo ' <span class="username u-dir" dir="ltr" data-aria label-part="">@'.$screen_name.'</span>';
				echo '</div>';
				echo '</a>';
				echo '<a class="tweet-timestamp js-permalink js-nav js-tooltip" href="https://twitter.com/'.$screen_name.'/status/'.$id.'" target="_blank">View Tweet</a>';
				echo '</div>';
				echo '<div class="js-tweet-text-container">';
				echo '<p class="TweetTextSize TweetTextSize--normal js-tweet-text tweet-text" data-aria-label-part="0" lang="en">';
				echo $text;
				echo '</p>';
				echo '</div>';
				echo '</div>';
			endforeach;
		endif;
	} catch ( OAuthException $e ) {
		error_log("Caught exception: ".$e->getMessage());
	}
	exit;
}

add_action( 'wp_ajax_nopriv_update_twitter_feed', 'updateTwitterFeed' );
add_action( 'wp_ajax_update_twitter_feed', 'updateTwitterFeed' );

/*
	Trim each item in the array so we can remove duplicates, and so we can make sure items that are saved with a space are trimmed
*/
function arr_map( $input )
{
	return ( 0 < strlen(trim($input ) ) ) ? trim( $input) : "";
}

function user_saved_map( $item )
{
	if ( is_array( $item ) )
	{
		return implode(", ", $item );
	} else {
		$decoded = json_decode( $item );

		return ( isset($decoded[0] ) ) ? $decoded[0] : "";
	}
}

add_action( 'wp_login', function( $user_login, $user )
{

	$user_twitter_handles = $_COOKIE["birdmash_twitter_user_handles"];
	$user_saved_twitter_handles = get_user_meta( $user->ID, "birdmash_twitter_user_handles" );
	if ( empty($user_saved_twitter_handles ) )
	{
		update_user_meta( $user->ID, "birdmash_twitter_user_handles", $_COOKIE["birdmash_twitter_user_handles"] );
		$user_saved_twitter_handles = get_user_meta( $user->ID, "birdmash_twitter_user_handles" );
	}
}, 10, 2);