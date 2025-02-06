<?php
include("meethour-recording.php");
include("meethour-meeting.php");
include("meethour-token.php");
include("meethour-guests.php");
include("meethour-scheduler.php");
include("meethour-instant.php");
include("join_meeting.php");
include("test-sdk.php");

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
function meethour_admin_menu()
{
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

    // add_submenu_page(
    //     'meethour-settings',         // Parent slug
    //     'Schedule Meeting',          // Page title
    //     'Schedule Meeting',          // Menu title
    //     'manage_options',            // Required capability
    //     'meethour-schedule',        // Menu slug
    //     'join_meeting'    // Function to display the page
    // );

    // add_submenu_page(
    //     'meethour-settings',         // Parent slug
    //     'Meetings List',             // Page title
    //     'Meetings List',             // Menu title
    //     'manage_options',            // Required capability
    //     'meethour-meetings',         // Menu slug
    //     'meethour_meetings_page'     // Function to display the page
    // );

    // add_submenu_page(
    //     'meethour-settings',         // Parent slug
    //     'Guests',                    // Page title
    //     'Guests',                    // Menu title
    //     'manage_options',            // Required capability
    //     'meethour-guests',           // Menu slug
    //     'meethour_guests_page'       // Function to display the page
    // );

    // add_submenu_page(
    //     'meethour-settings',         // Parent slug
    //     'Recordings',                // Page title
    //     'Recordings',                // Menu title
    //     'manage_options',            // Required capability
    //     'meethour-recordings',       // Menu slug
    //     'meethour_recordings_page'   // Callback function
    // );
}
add_action('admin_menu', 'meethour_admin_menu');



add_action('admin_head', 'meethour_custom_columns_css');
function meethour_custom_columns_css()
{
    echo '<style>
        .column-meeting_id, .column-date_time, .column-duration, .column-agenda, .column-shortcode, .column-meeting_link {
\        }
        .column-shortcode code {
            display: inline-block;
            padding: 2px 4px;
            background: #f1f1f1;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
    </style>';
}
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
    if (get_post_type() === 'mh_recordings')
        unset($actions['edit']);
    unset($actions['view']);
    // unset($actions['trash']);
    unset($actions['inline hide-if-no-js']);
    return $actions;
}
