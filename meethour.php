<?php 
include ("meethour-recording.php");
include ("meethour-meeting.php");
include ("meethour-token.php");
include ("meethour-guests.php");
include ("meethour-scheduler.php");
include ("meethour-instant.php");

/*
 * Plugin Name: MeetHour Integration
 * Description: Integrates MeetHour meeting scheduler with WordPress
 * Version: 1.0
 * Author: MeetHour
 */

// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

// Create the admin menu items
function meethour_admin_menu() {
    // Add main menu item
    add_menu_page(
        'MeetHour',                  // Page title
        'MeetHour',                  // Menu title
        'manage_options',            // Required capability
        'meethour-settings',         // Menu slug
        'meethour_token_page',       // Function to display the page
        'dashicons-video-alt2'       // Icon
    );

    // Add submenu items
    add_submenu_page(
        'meethour-settings',         // Parent slug
        'Generate Token',            // Page title
        'Generate Token',            // Menu title
        'manage_options',            // Required capability
        'meethour-settings',         // Menu slug (same as parent to make it the default page)
        'meethour_token_page'        // Function to display the page
    );

    add_submenu_page(
        'meethour-settings',         // Parent slug
        'Instant Meeting',           // Page title
        'Instant Meeting',           // Menu title
        'manage_options',            // Required capability
        'meethour-instant',          // Menu slug
        'meethour_instant_page'      // Function to display the page
    );

    add_submenu_page(
        'meethour-settings',         // Parent slug
        'Schedule Meeting',          // Page title
        'Schedule Meeting',          // Menu title
        'manage_options',            // Required capability
        'meethour-scheduler',        // Menu slug
        'meethour_scheduler_page'    // Function to display the page
    );

    add_submenu_page(
        'meethour-settings',         // Parent slug
        'Meetings List',             // Page title
        'Meetings List',             // Menu title
        'manage_options',            // Required capability
        'meethour-meetings',         // Menu slug
        'meethour_meetings_page'     // Function to display the page
    );

    add_submenu_page(
        'meethour-settings',         // Parent slug
        'Guests',                    // Page title
        'Guests',                    // Menu title
        'manage_options',            // Required capability
        'meethour-guests',           // Menu slug
        'meethour_guests_page'       // Function to display the page
    );

    add_submenu_page(
        'meethour-settings',         // Parent slug
        'Recordings',                // Page title
        'Recordings',                // Menu title
        'manage_options',            // Required capability
        'meethour-recordings',       // Menu slug
        'meethour_recordings_page'   // Callback function
    );
}
add_action('admin_menu', 'meethour_admin_menu');

?>