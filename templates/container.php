<div class="bm-header">
    <a href="#" class="bm-settings">
        <i></i>
        <span><?php esc_html_e( 'Settings', 'bm-txd' ); ?></span>
    </a>
    <div class="bw-settings-container">
        <p><?php esc_html_e( 'Add more Twitter users:', 'bm-txd' ); ?></p>
        <input type="text" id="bm-twitter-more" name="bm-twitter-more" value="<?php echo esc_attr( get_option('bm_twitter_more') ); ?>">
        <a href="#" class="bm-save"><?php esc_html_e( 'Save', 'bm-txd' ); ?></a>
    </div>
</div>
<?php if( is_array( $tweets_arr ) and ! empty( $tweets_arr ) ) : ?>
    <ul>
        <?php foreach( $tweets_arr as $row ) : ?>
            <?php include BIRDSMASH_DIR . 'templates/row.php'; ?>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p><?php esc_html_e( 'Nothing was found!', 'bm-txd' ); ?></p>
<?php endif; ?>
