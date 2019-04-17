<?php
if ( ! defined( 'WPINC' ) ) {die;}
include_once('quake_api.php');

/**
 * @link              https://github.com/rooirampokker/
 * @since             1.0.0
 * @package           Quake_blogger
 *
 * @wordpress-plugin
 * Plugin Name:       Quake Blogger
 * Plugin URI:        https://github.com/rooirampokker/quake_blogger
 * Description:       Checks <a href='https://earthquake.usgs.gov/fdsnws/event/1/'>https://earthquake.usgs.gov/fdsnws/event/1/</a> every hour and creates a post if any new information is available.
 * Version:           1.0.0
 * Author:            Leslie Albrecht
 * Author URI:        https://github.com/rooirampokker/
 */

//call quake_cron_activate when plugin activates
register_activation_hook(__FILE__, 'quake_cron_activate');
//standard deactivation - call function to remove cron and do cleanup
register_deactivation_hook (__FILE__, 'quake_cron_deactivate');
//for testing - create minutely cron
add_filter( 'cron_schedules', 'cron_add_minute' );


/*
* Schedule a new cron event if it doesn't already exist
*/
function quake_cron_activate() {
    if( !wp_next_scheduled( 'quake_blogger_cron' ) ) {
        wp_schedule_event( time(), 'everyminute', 'quake_blogger_cron' );
    }
}
/*
* Unschedule cron event on plugin deactivation
*/
function quake_cron_deactivate() {
    // find out when the last event was scheduled and unschedule it
    $timestamp = wp_next_scheduled ('quake_blogger_cron');
    wp_unschedule_event ($timestamp, 'quake_blogger_cron');
}
/*
* add custom interval - just is just to test
*/
function cron_add_minute( $schedules ) {
    // Adds once every minute to the existing schedules.
    $schedules['everyminute'] = array(
        'interval' => 60,
        'display' => __( 'Once Every Minute' )
    );
    return $schedules;
}
/*
*
*/
function quake_blogger() {
    print_r('here...');
}

//create a new object - no direct operations will be performed with this (yet) - purely just to trigger the add_action hook in the constructor to start the fetch -> write process
$quake_blogger = new Quake_api();