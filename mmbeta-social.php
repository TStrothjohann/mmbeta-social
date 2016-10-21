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


// Cronjob that triggers API calls
function hourly_social_api_call(){
  function get_facebook_feed(){
    $response = wp_remote_get( 'https://graph.facebook.com/mediummagazin/feed?access_token=312152239170808|6e57a42a6f2be8c5a76507bf35b38e48'
    );

    if ( is_array( $response ) && ! is_wp_error( $response ) ) {
        $headers = $response['headers'];
        $body    = $response['body'];

        // Cache the body
        set_transient( 'mmbeta_facebook_feed', $body, 60*60 );
    }
  }

  function get_fresh_facebook_post($post_id) {
    $cached_post = json_decode( get_transient( 'mmbeta_fresh_facebook_post' ) );

    if (!$cached_post || $post_id !== $cached_post->id) {
      $response = wp_remote_get( 'https://graph.facebook.com/' . $post_id );

      if ( is_array( $response ) && ! is_wp_error( $response ) ) {
          $headers = $response['headers'];
          $body    = $response['body'];

          // Cache the body
          set_transient( 'mmbeta_fresh_facebook_post', $body, 60*60 );
      } 
    } 
  }

  $mmbeta_facebook_feed = get_transient( 'mmbeta_facebook_feed' );
   
  if( false === $mmbeta_facebook_feed ) {
    // Transient expired, refresh the data
    get_facebook_feed();
  }

  $mmbeta_facebook_feed = json_decode($mmbeta_facebook_feed);
  $newest_post_from_feed = $mmbeta_facebook_feed->data[0]->id;

  get_fresh_facebook_post($newest_post_from_feed);
}

/* The activation hook is executed when the plugin is activated. */
register_activation_hook(__FILE__,'mmbeta_social_activation');

/* The deactivation hook is executed when the plugin is deactivated */
register_deactivation_hook(__FILE__,'mmbeta_social_deactivation');

/* This function is executed when the user activates the plugin */
function mmbeta_social_activation(){  wp_schedule_event(time(), 'hourly', 'hourly_social_api_call');}

/* This function is executed when the user deactivates the plugin */
function mmbeta_social_deactivation(){  wp_clear_scheduled_hook('hourly_social_api_call');}

/* Registering the action hook */
add_action('my_periodic_action','hourly_social_api_call');