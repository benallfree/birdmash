<?php

// Add Scripts
function ttw_add_scripts(){
	wp_enqueue_style('ttw-main-style', plugins_url().'/twitter-timeline-widget/css/style.css');
	wp_enqueue_script('ttw-main-script', plugins_url().'/twitter-timeline-widget/js/main.js', array('jquery'));
}

add_action('wp_enqueue_scripts', 'ttw_add_scripts');
