<?php

$title                     = apply_filters('widget_title', $instance['title']);
$username                  = $instance['twitter_username'];
$limit                     = $instance['update_count'];
// Twitter API details
$oauth_access_token        = $instance['oauth_access_token'];
$oauth_access_token_secret = $instance['oauth_access_token_secret'];
$consumer_key              = $instance['consumer_key'];
$consumer_secret           = $instance['consumer_secret'];


echo $args['before_widget'];

if (!empty($title)) {
    echo $args['before_title'] . $title . $args['after_title'];
}

// Creating array of twitter names
$usernameArr = explode(',', $username);

echo '<div id="tweets">';

// Loops through each username and gets tweets.
foreach ($usernameArr as $user) {
    
    $timelines = $this->gettwitterAPI($user, $limit, $oauth_access_token, $oauth_access_token_secret, $consumer_key, $consumer_secret);
    
    if ($timelines) {
         
        $patterns = array( '@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '/@([A-Za-z0-9_]{1,15})/' );
        $replace = array( '<a href="$1">$1</a>', '<a href="http://twitter.com/$1">@$1</a>' );
        
        echo '<div class="twitter-section"><div class="twitter-author">Latest tweets from ' . $timelines[0]->user->name . '</div><br />';
        
        foreach ($timelines as $timeline) {

            $clickable = preg_replace( $patterns, $replace, $timeline->text );
            
            echo '<div class="twitter-containter">';
            echo  $clickable . '<br/>';
            echo '</div>';
            echo '<br/>';
        }
        
        echo '</div>';
        
    } else {
        echo ('Error loading the tweets.');
    }
}
echo '</div>';
echo '<button type="button" class="btn btn-tweet" id="addnewTweet">See tweets from other users</button>';

echo $args['after_widget'];