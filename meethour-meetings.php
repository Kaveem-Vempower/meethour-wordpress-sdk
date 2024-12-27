<?php
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
        'Schedule Meeting',          // Page title
        'Schedule Meeting',          // Menu title
        'manage_options',            // Required capability
        'meethour-scheduler',        // Menu slug
        'meethour_scheduler_page'    // Function to display the page
    );

    // Add new Meetings List submenu
    add_submenu_page(
        'meethour-settings',         // Parent slug
        'Meetings List',             // Page title
        'Meetings List',             // Menu title
        'manage_options',            // Required capability
        'meethour-meetings',         // Menu slug
        'meethour_meetings_page'     // Function to display the page
    );
}
add_action('admin_menu', 'meethour_admin_menu');

// Function to fetch upcoming meetings
function meethour_fetch_upcoming_meetings() {
    $access_token = get_option('meethour_access_token', '');
    
    if (empty($access_token)) {
        return new WP_Error('no_token', 'Access token not found');
    }

    $response = wp_remote_post('https://api.meethour.io/api/v1.2/meeting/upcomingmeetings', [
        'headers' => [
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ],
        'body' => json_encode([
            'limit' => 10,
            'page' => 1,
            'show_all' => 1
        ])
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    return $body['data'] ?? [];
}

// Keep your existing functions: meethour_token_page() and meethour_scheduler_page()
// [Previous code for these functions remains unchanged]

// Meetings List Page
function meethour_meetings_page() {
    $meetings = meethour_fetch_upcoming_meetings();
    ?>
    <div class="wrap">
        <h1>MeetHour Meetings</h1>

        <?php if (is_wp_error($meetings)): ?>
            <div class="notice notice-error">
                <p><?php echo esc_html($meetings->get_error_message()); ?></p>
            </div>
        <?php else: ?>
            <div class="card">
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th>Meeting Name</th>
                            <th>Meeting ID</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Shortcode</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($meetings as $meeting): ?>
                            <tr>
                                <td><?php echo esc_html($meeting['meeting_name']); ?></td>
                                <td><?php echo esc_html($meeting['meeting_id']); ?></td>
                                <td>
                                    <?php 
                                    echo esc_html(date('M d, Y h:i A', strtotime($meeting['meeting_date'] . ' ' . $meeting['meeting_time'])));
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    echo esc_html($meeting['duration_hr'] . 'h ' . $meeting['duration_min'] . 'm');
                                    ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo esc_attr(strtolower($meeting['status'])); ?>">
                                        <?php echo esc_html($meeting['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <code>[meethour id="<?php echo esc_attr($meeting['meeting_id']); ?>"]</code>
                                    <button class="button button-small copy-shortcode" 
                                            data-shortcode='[meethour id="<?php echo esc_attr($meeting['meeting_id']); ?>"]'>
                                        Copy
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <script>
            jQuery(document).ready(function($) {
                $('.copy-shortcode').click(function() {
                    var shortcode = $(this).data('shortcode');
                    navigator.clipboard.writeText(shortcode).then(function() {
                        var button = $(this);
                        button.text('Copied!');
                        setTimeout(function() {
                            button.text('Copy');
                        }, 2000);
                    }.bind(this));
                });
            });
            </script>

            <style>
            .status-badge {
                padding: 5px 10px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 500;
            }
            .status-active {
                background-color: #e6f3e6;
                color: #1e7e34;
            }
            .status-scheduled {
                background-color: #e6f3ff;
                color: #0056b3;
            }
            .status-completed {
                background-color: #e9ecef;
                color: #495057;
            }
            .copy-shortcode {
                margin-left: 5px !important;
            }
            </style>
        <?php endif; ?>
    </div>
    <?php
}

// Keep your existing register_settings and shortcode implementations
// [Previous code for these functions remains unchanged]

// Token Generation Page
function meethour_token_page() {
    // Handle form submission for token generation
    if (isset($_POST['meethour_generate_token'])) {
        // Verify security nonce
        if (!wp_verify_nonce($_POST['meethour_token_nonce'], 'meethour_generate_token')) {
            wp_die('Security check failed');
        }

        // Get and sanitize form data
        $client_id = sanitize_text_field($_POST['client_id']);
        $client_secret = sanitize_text_field($_POST['client_secret']);
        $username = sanitize_email($_POST['username']);
        $password = $_POST['password'];

        // Prepare data for API request
        $data = array(
            'grant_type' => 'password',
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'username' => $username,
            'password' => $password
        );

        // Make API request to get token
        $response = wp_remote_post('https://api.meethour.io/oauth/token', array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ),
            'body' => json_encode($data),
            'timeout' => 30
        ));

        // Handle the API response
        if (is_wp_error($response)) {
            add_settings_error('meethour_messages', 'meethour_error', 
                'Error: ' . $response->get_error_message(), 'error');
        } else {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($body['access_token'])) {
                update_option('meethour_access_token', $body['access_token']);
                add_settings_error('meethour_messages', 'meethour_success', 
                    'Access token generated and stored successfully!', 'success');
            } else {
                add_settings_error('meethour_messages', 'meethour_error', 
                    'Failed to generate access token. Please check your credentials.', 'error');
            }
        }
    }

    // Display any error/success messages
    settings_errors('meethour_messages');
    ?>
    <div class="wrap">
        <h1>Generate MeetHour Access Token</h1>
        
        <div class="card">
            <form method="post" action="">
                <?php wp_nonce_field('meethour_generate_token', 'meethour_token_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th><label for="client_id">Client ID</label></th>
                        <td><input type="text" id="client_id" name="client_id" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="client_secret">Client Secret</label></th>
                        <td><input type="password" id="client_secret" name="client_secret" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="username">Username (Email)</label></th>
                        <td><input type="email" id="username" name="username" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="password">Password</label></th>
                        <td><input type="password" id="password" name="password" class="regular-text" required></td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="meethour_generate_token" class="button button-primary" 
                        value="Generate Access Token">
                </p>
            </form>
        </div>
    </div>
    <?php
}

// Meeting Scheduler Page
function meethour_scheduler_page() {
    // Get access token
    $access_token = get_option('meethour_access_token', '');

    // Handle meeting scheduler form submission
    if (isset($_POST['meethour_submit'])) {
        // Verify security nonce
        if (!wp_verify_nonce($_POST['meethour_schedule_nonce'], 'meethour_schedule_meeting')) {
            wp_die('Security check failed');
        }
        
        // Get and sanitize form data
        $meetingName = sanitize_text_field($_POST['meeting_name'] ?? '');
        $meetingDescription = sanitize_text_field($_POST['meeting_description'] ?? '');
        $meetingDate = sanitize_text_field($_POST['meeting_date'] ?? '');
        $meetingTime = sanitize_text_field($_POST['meeting_time'] ?? '');
        $duration_hr = absint($_POST['duration_hr'] ?? 1);
        $duration_min = absint($_POST['duration_min'] ?? 30);

        if (empty($access_token)) {
            add_settings_error('meethour_messages', 'meethour_error', 
                'Access token not found. Please generate an access token first.', 'error');
        } else {
            // Prepare meeting data for API
            $data = [
                'meeting_name' => $meetingName,
                'agenda' => $meetingDescription,
                'passcode' => 'passcode',
                'meeting_date' => date('d-m-Y', strtotime($meetingDate)),
                'meeting_time' => date('H:i', strtotime($meetingTime)),
                'meeting_meridiem' => date('A', strtotime($meetingTime)),
                'duration_hr' => $duration_hr,
                'duration_min' => $duration_min,
                "is_show_portal"=> 1,
                "endBy"=> "END_DATETIME",
                "end_date_time"=> "25-01-2025",
                'timezone' => 'Asia/Kolkata',
                'is_recurring' => 1,
                'recurring_type' => 'daily',
                'repeat_interval' => 2,
                'options' => ['ALLOW_GUEST', 'JOIN_ANYTIME']
            ];

            // Make API request to schedule meeting
            $response = wp_remote_post('https://api.meethour.io/api/v1.2/meeting/schedulemeeting', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'body' => json_encode($data),
                'timeout' => 30
            ]);

            // Handle the API response
            if (is_wp_error($response)) {
                add_settings_error('meethour_messages', 'meethour_error', 
                    'Error: ' . $response->get_error_message(), 'error');
            } else {
              // Handle the API response
if (is_wp_error($response)) {
    add_settings_error('meethour_messages', 'meethour_error', 
        'Error: ' . $response->get_error_message(), 'error');
} else {
    $result = json_decode(wp_remote_retrieve_body($response), true);
    $message = isset($result['data']['meeting_id']) ? $result['message'] : 'No message available';

    // Add settings error message
    add_settings_error('meethour_messages', 'meethour_success', 
        esc_html($message), 'success');

    // Display meeting ID and message
    // echo "Meeting ID: " . $result['data']['meeting_id'] . "<br>";
    // echo "Message: " . $message;
}

            }
        }
    }

    // Display any error/success messages
    settings_errors('meethour_messages');
    ?>
    <div class="wrap">
        <h1>Schedule MeetHour Meeting</h1>

        <?php if (empty($access_token)): ?>
            <div class="notice notice-error">
                <p>Access token not found. Please <a href="<?php echo admin_url('admin.php?page=meethour-settings'); ?>">generate an access token</a> first.</p>
            </div>
        <?php else: ?>
            <div class="card">
                <form method="POST">
                    <?php wp_nonce_field('meethour_schedule_meeting', 'meethour_schedule_nonce'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th><label for="meeting_name">Meeting Name</label></th>
                            <td><input type="text" id="meeting_name" name="meeting_name" class="regular-text" required></td>
                        </tr>
                        <!-- New Fields -->
                        <tr>
                            <th><label for="meeting_description">Meeting Description</label></th>
                            <th><input type="text" id="meeting_description" name="meeting_description" class="regular-text"></th>
                        </tr>
                        <!-- Ends Here -->
                        <tr>
                            <th><label for="meeting_date">Meeting Date</label></th>
                            <td><input type="date" id="meeting_date" name="meeting_date" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="meeting_time">Meeting Time</label></th>
                            <td><input type="time" id="meeting_time" name="meeting_time" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="duration_hr">Duration (Hours)</label></th>
                            <td><input type="number" id="duration_hr" name="duration_hr" value="1" min="0" max="24" class="small-text"></td>
                        </tr>
                        <tr>
                            <th><label for="duration_min">Duration (Minutes)</label></th>
                            <td><input type="number" id="duration_min" name="duration_min" value="30" min="0" max="59" class="small-text"></td>
                        </tr>
                        <tr>
                            <th><label for="timezone">Timezone</label></th>
                            <td>
                                <select id="timezone" name="timezone">
                                    <option value="Asia/Kolkata">Asia/Kolkata</option>
                                    <option value="America/New_York">America/New_York</option>
                                    <option value="Europe/London">Europe/London</option>
                                </select>
                            </td>
                        </tr>
                    </table>

                    <p class="submit">
                        <input type="submit" name="meethour_submit" class="button button-primary" value="Schedule Meeting">
                    </p>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <style>
        .card {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            margin-top: 20px;
            padding: 20px;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
        }
    </style>
    <?php
}

// Register plugin settings
function meethour_register_settings() {
    register_setting('meethour_settings', 'meethour_access_token');
}
add_action('admin_init', 'meethour_register_settings');

// Keep the shortcode implementation for frontend use
function meethour_scheduler_shortcode() {
    // ... (keep the existing shortcode function as is)
}
add_shortcode('meethour', 'meethour_scheduler_shortcode');


