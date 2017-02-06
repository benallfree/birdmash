<?php
/*
Plugin Name: Birdmash
Version: 1.0
Author: Haxor
*/

require_once ( plugin_dir_path( __FILE__ ) . '/classes/class-wp-twitter-api.php' );

class Birdmash_Widget extends WP_Widget {

    private $settings = array();

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {

	    $this->settings['access_token'] = '101055279-rkEe2sy6318dEMk613Cx2w9tRzr8DeYBhYBXFMRX';
	    $this->settings['access_token_secret'] = 'imGb0vFTJlO7z3vhlH04o9PA6o61e17T1lLBkPEAryKSc';
        $this->settings['consumer_key'] = '5LuLne5DqLqwKOEv9cS7qbRQn';
	    $this->settings['consumer_secret'] = 'bLSHiuk6i29OstjQWKgrpOSfuS9T7nXFCdI4nX9ATZkGSYHvuv';

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
        $title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
        $content = isset( $instance['content'] ) ? strip_tags( $instance['content'] ) : '';



        echo $args['before_widget'];

        if ( ! empty( $title ) ) {
            echo $args['before_title'] . esc_html( $title ) . $args['after_title'];
        }

        if ( ! empty( $content ) ) {

            $users = explode( ',', $content );

            $twitter_connect = new OauthConnect( $this->settings, 'usertimeline' );
            $twitter_connect->setUrlBase();

            foreach( $users as $user ) {
                echo "<p><strong>@$user</strong></p>";

                $get_fields['screen_name'] = $user;
                $get_fields['count'] = 3;
                
                $twitter_connect->setGetFields( $get_fields );
                $twitter_connect->setRequestMethod( 'GET' );

                $response = $twitter_connect->performRequest();

                $tweets = json_decode( $response->json , $assoc = true );

                if( !empty($tweets) )
                foreach ($tweets as $tweet) {
                    echo "<p>" . $tweet['text'] . "</p>";
                } else {
                    echo "<p>No data available</p>";
                }

            }
        } else {
            echo "No data available";
        }

        echo $args['after_widget'];
    }

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
        $title = isset( $instance['title'] ) ? $instance['title'] : '';
        $content = isset ( $instance['content'] ) ? strip_tags( $instance['content'] ) : '';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>"><?php _e( 'List of comma-separated Twitter usernames:' ); ?></label>
            <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>" rows="5"><?php echo strip_tags( $content ); ?></textarea>
        </p>
        <?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['content'] = ( ! empty( $new_instance['content'] ) ) ? strip_tags( $new_instance['content'] ) : '';

        return $instance;
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'Birdmash_Widget' );
});
