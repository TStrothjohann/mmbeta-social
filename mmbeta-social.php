<?php
/*
Plugin Name: mmbeta Social
Plugin URI: http://mmbeta.de/social/
Description: Wordpress Plugin integrating mediummagazin's Facebook and Twitter feeds
Author: Thomas Strothjohann
Version: 0.1
Author URI: http://codereporter.de
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

/**
 * Enqueue scripts and styles.
 */
function mmbeta_social_scripts() {}
add_action( 'wp_enqueue_scripts', 'mmbeta_social_scripts' );


