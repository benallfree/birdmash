<?php
/*
  Admin page for getting Twitter data
*/

// Init admin page
add_action('admin_init', 'bm_admin_init');

/**
 * Init admin page, registering settings
 */
function bm_admin_init() {
  // One setting for each twitter oauth data
	register_setting( 'bm_settings_group', 'bm_twitter_consumer_key' );
	register_setting( 'bm_settings_group', 'bm_twitter_consumer_secret' );
	register_setting( 'bm_settings_group', 'bm_twitter_token' );
	register_setting( 'bm_settings_group', 'bm_twitter_token_secret' );
}



// Create custom plugin settings menu
add_action('admin_menu', 'bm_create_menu');

/**
 * Creates admin page
 */
function bm_create_menu() {
  global $ic_fc_admin_page;
  $ic_fc_admin_page = add_menu_page('Birdmash Twitter', 'Birdmash Twitter', 'administrator', 'birdmash-twitter', 'bm_settings_page', 'dashicons-twitter');
  // When the admin page is shown loads a css  
  add_action('load-'.$ic_fc_admin_page, 'bm_admin_page_init');  
}



/**
 * Loads css when admin page is loaded
 */
function bm_admin_page_init() {
  global $ic_fc_admin_page;
  $screen = get_current_screen();
  // If is admin page enqueue styles
  if ($ic_fc_admin_page == $screen->id) {
    add_action( 'admin_enqueue_scripts', 'bm_admin_css' );
  }
}



/**
 * Displays admin page
 */
function bm_settings_page() {

?>
<div class="wrap bm_twitter">
<h2><?php _e('Twitter options for Birdmash Twitter widget', 'birdmash'); ?></h2>
<?php if( isset($_GET['settings-updated']) ) { ?>
    <div id="message" class="updated">
        <p><strong><?php _e('Options updated.', 'birdmash'); ?></strong></p>
    </div>
<?php } ?>
<form method="post" action="options.php">
    <?php settings_fields( 'bm_settings_group' ); ?>
    <?php do_settings_sections( 'bm_settings_page' ); ?>
    <h3><?php _e('Twitter OAuth Options', 'birdmash'); ?></h3>
    <p><?php _e('Create a new <a href="https://apps.twitter.com/" target="_blank">Twitter APP</a> and set the options', 'birdmash'); ?></p>
    <table class="form-table">
        <tr valign="top">
        <th scope="row"><?php _e('Consumer key', 'birdmash'); ?></th>
        <td><input type="text" name="bm_twitter_consumer_key" value="<?php echo get_option('bm_twitter_consumer_key'); ?>" class="regular-text" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row"><?php _e('Consumer secret', 'birdmas'); ?></th>
        <td><input type="text" name="bm_twitter_consumer_secret" value="<?php echo get_option('bm_twitter_consumer_secret'); ?>" class="regular-text" /></td>
        </tr>

        <tr valign="top">
        <th scope="row"><?php _e('Access token', 'birdmash'); ?></th>
        <td><input type="text" name="bm_twitter_token" value="<?php echo get_option('bm_twitter_token'); ?>" class="regular-text" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row"><?php _e('Access token secret', 'birdmas'); ?></th>
        <td><input type="text" name="bm_twitter_token_secret" value="<?php echo get_option('bm_twitter_token_secret'); ?>" class="regular-text" /></td>
        </tr>
        
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php }


function bm_admin_css() {
  $plugin = get_plugin_data( __FILE__, false, false);
  // The css only shows a Twitter icon using dashicons in the <h2> title
  wp_register_style( 'bm_admin', plugins_url( '../css/admin.css', __FILE__ ), false, $plugin['Version'] );
  wp_enqueue_style( 'bm_admin' );
}