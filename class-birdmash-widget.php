<?php

/**
 * Class Birdmash_Widget
 */
class Birdmash_Widget extends WP_Widget {

	/**
	 * Let's get it started in here
	 *
	 * Sets up the widget's name, description, etc.
	 *
	 * @see WP_Widget::__construct()
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'birdmash_widget',
			'description' => 'Multiuser Twitter Mashup',
		);
		parent::__construct( 'birdmash_widget', 'Birdmash Widget', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * Displays a combined list of the three most recent items from each Twitter
	 * user, sorted by post date (most recent first). These items are fetched via
	 * server-side API requests and cached.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args Display arguments including before_title, after_title,
	 *                        before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		/** TODO: fetch Twitter handles from widget settings. */
		/** TODO: (bonus) check #1 - if user is logged into WordPress account, check for user_meta with additional Twitter handles. */
		/** TODO: (bonus) check #2 - if user is not logged in, check for session cookie with additional Twitter handles. */
		/** TODO: request feeds of 3 most recent posts for each user. */
		/** TODO: (bonus) cache results for 60 minutes. */
		/** TODO: sort results by timestamp (newest first). */
		/** TODO: http://giphy.com/gifs/K3qwA91Bs4FLW/html5 ? */
		/** TODO: output tweets. */
		/** TODO: (bonus) add gear icon that, when clicked, allows the user to modify tweet list */
		/** TODO: (bonus) update option #1 - if user is logged into WordPress account, update user_meta. */
		/** TODO: (bonus) update option #2 - if user is not logged in, save settings to session cookie. */
	}

	/**
	 * Outputs the options form on admin
	 *
	 * The options set here define the site's default set of Twitter users to poll
	 * for recent tweets. In the future, individual users will be able to edit the
	 * list of users for their own view of the widget's output.
	 *
	 * @param array $instance The current widget settings.
	 *
	 * @return string The HTML markup for the form.
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		/** TODO: create form that collects comma-separated list of Twitter user names. */
		/** TODO: (bonus) set interface to check validity of Twitter handles? */
		/** TODO: values set here are treated as the site default.  */
		return '';
	}

	/**
	 * Processing widget options on save
	 *
	 * Save the options set in the widget form on admin.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance The new instance of the widget.
	 * @param array $old_instance The old instance of the widget.
	 *
	 * @return array The updated instance of the widget.
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		return $new_instance;
	}
}

