<?php

add_action( 'wp_ajax_nopriv___bm_do_save', '__bm_do_save' );
add_action( 'wp_ajax___bm_do_save', '__bm_do_save' );

/*
 * the ajax..
 */
function __bm_do_save() {

    if( ! isset( $_POST['twitter_more'] ) ) {
        exit;
    }

    $twitter_more = esc_attr( $_POST['twitter_more'] );
    $twitter_users_system = esc_attr( $_POST['twitter_users_system'] );

    update_option( 'bm_twitter_more', $twitter_more );

    delete_transient( 'bm_tweets' );
    $tweets_arr = Bm_core::get_tweets( $twitter_users_system . ',' .  $twitter_more );

    include BIRDSMASH_DIR . 'templates/container.php';

    exit;

}
