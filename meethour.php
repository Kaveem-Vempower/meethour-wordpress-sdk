<?php
/*
 * Plugin Name: Meet Hour Video Conference
 * Description: Discover the power of video conferencing with Meet Hour. Learn what video conferencing is, explore its diverse applications across industries, and find out why Meet Hour stands out as your preferred choice. Explore key features, reliability, and seamless integration options for your technology stacks. Join the future of remote collaboration with Meet Hour.
 * Plugin URI: https://meethour.io/
 * Version: 1.0
 * Author: Meet Hour LLC 
 * Author URI: https://www.meethour.io/
 */

include("includes/meethour-recording.php");
include("includes/meethour-meeting.php");
include("includes/meethour-token.php");
include("includes/meethour-guests.php");
include("includes/meethour-scheduler.php");
include("includes/readme-meethour-page.php");
include("includes/meethour-instant.php");


// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
};
// Create the admin menu items
function meethour_admin_menu()
{
    // Add main menu item
    add_menu_page(
        'Meet Hour',                  // Page title
        'Meet Hour',                  // Menu title
        'manage_options',            // Required capability
        'meethour-settings',         // Menu slug
        'meethour_readme_page',       // Function to display the page
        'dashicons-video-alt2'       // Icon
    );
    $access_token = get_option('meethour_access_token', '');
    if (!empty($access_token)) {
        add_submenu_page(
            'meethour-settings',         // Parent slug
            'Instant Meeting',           // Page title
            'Instant Meeting',           // Menu title
            'manage_options',            // Required capability
            'meethour-instant',          // Menu slug
            'meethour_instant_page'      // Function to display the page
        );
        add_submenu_page(
            'meethour-settings', // Parent slug
            'Schedule Meeting', // Page title
            'Schedule Meeting', // Menu title
            'manage_options', // Capability
            'post-new.php?post_type=mh_meetings' // Menu slug
        );
        add_submenu_page(
            'meethour-settings', // Parent slug
            'Meetings', // Page title
            'Meetings', // Menu title
            'manage_options', // Capability
            'edit.php?post_type=mh_meetings' // Menu slug
        );
        add_submenu_page(
            'meethour-settings', // Parent slug
            'Recordings', // Page title
            'Recordings', // Menu title
            'manage_options', // Capability
            'edit.php?post_type=mh_recordings' // Menu slug
        );
        // add_submenu_page(
        //     'meethour-settings',         // Parent slug
        //     'Generate Token',            // Page title
        //     'Settings',            // Menu title
        //     'manage_options',            // Required capability
        //     'meethour-settings',         // Menu slug (same as parent to make it the default page)
        //     'meethour_token_page'        // Function to display the page
        // );
    }
}

add_action('admin_menu', 'meethour_admin_menu');
add_role('meethour', 'Meet Hour', array(
    'read' => true,
    'create_posts' => true,
    'edit_posts' => true,
    'edit_others_posts' => true,
    'publish_posts' => true,
    'manage_categories' => true,
));

add_filter('post_row_actions', 'remove_row_actions', 10, 1);
function remove_row_actions($actions)
{
    if (get_post_type() === 'mh_recordings') {
        unset($actions['edit']);
        unset($actions['view']);
        unset($actions['trash']);
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}

add_shortcode('meethour', 'meethour_shortcode');
function meethour_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'recording_id' => '',
        'meeting_id' => null
    ), $atts, 'meethour');
    $meeting_id = ($atts['meeting_id']);
    $recording_id = ($atts['recording_id']);

    if ($meeting_id) {
        return '<iframe allow="camera; microphone; display-capture; autoplay; clipboard-write" src="https://meethour.io/' . $meeting_id . '#interfaceConfig.applyMeetingSettings=true&interfaceConfig.disablePrejoinHeader=true&interfaceConfig.ENABLE_DESKTOP_DEEPLINK=false&interfaceConfig.disablePrejoinFooter=true&interfaceConfig.SHOW_MEET_HOUR_WATERMARK=false&interfaceConfig.HIDE_DEEP_LINKING_LOGO=true&interfaceConfig.MOBILE_APP_PROMO=false&interfaceConfig.ENABLE_MOBILE_BROWSER=true&appData.localStorageContent=null" name="mhConferenceFrame0" id="mhConferenceFrame0" allowfullscreen="true" style="width: 100vh; display: flex; height: 100vh;  border: 0px; justify-content: center; align-items: center; "></iframe>';
    }
    if ($recording_id) {
        $video_url = get_post_meta($recording_id, 'recording_path', true);
        return '<video controls style="width: 100%;"><source src="' . $video_url . '" type="video/mp4">Your browser does not support the video tag.</video>';
    }
};

register_uninstall_hook(__FILE__, 'meethour_deactivate');

// Function to delete custom post types and their data on deactivation
function meethour_deactivate()
{
    global $wpdb;

    // Delete all posts of custom post type 'mh_meetings'
    $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type = 'mh_meetings'");
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id NOT IN (SELECT ID FROM {$wpdb->posts})");

    // Delete all posts of custom post type 'mh_recordings'
    $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type = 'mh_recordings'");
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE post_id NOT IN (SELECT ID FROM {$wpdb->posts})");

    // Optionally, delete any options or other data related to your plugin
    delete_option('meethour_access_token');
    delete_option('meethour_client_id');
    delete_option('meethour_client_secret');
    delete_option('meethour_username');
    delete_option('meethour_password');
    delete_option('meethour_api_key');
    delete_option('mh_meetings_total_pages');
    delete_option('mh_meetings_current_page');
    delete_option('mh_recordings_total_pages');
    delete_option('mh_recordings_current_page');
    delete_option('meethour_main_user');
    delete_option('mh_recordings_post_limit');
    delete_option('mh_meetings_post_limit');
}
add_action('wp_ajax_meethour_deactivate', 'meethour_deactivate'); // For logged-in users 

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'my_plugin_action_links');

function my_plugin_action_links($links)
{
    $links[] = '<a href="' . esc_url(get_admin_url(null, '?page=meethour-settings')) . '">Settings</a>';
    return $links;
}
