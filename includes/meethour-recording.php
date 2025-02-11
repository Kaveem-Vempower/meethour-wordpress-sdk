<?php
require(WP_PLUGIN_DIR . '/meethour/vendor/autoload.php');
require(WP_PLUGIN_DIR . '/meethour/vendor/meethour/php-sdk/src/autoload.php');

use MeetHourApp\Services\MHApiService;
use MeetHourApp\Types\GetSingleRecording;
use MeetHourApp\Types\RecordingsList;


function meethour_register_recordings_post_type()
{
    register_post_type('mh_recordings', [
        'labels' => [
            'name' => 'Recordings',
            'singular_name' => 'Recordings',
            'add_new_item' => __('Recordings Menu'),
            'add_new' => __('Recordings'),
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor'], // added to show the custom fields in the menu 
        'show_in_menu' => false,
    ]);
}
add_action('init', 'meethour_register_recordings_post_type');

add_filter('manage_mh_recordings_posts_columns', 'meethour_recordings_columns');
function meethour_recordings_columns($columns)
{
    $columns = [
        'cb' => '<input type="checkbox" />',
        'title' => 'Recording Name',
        'recording_date' => 'Recording Date',
        'recording_size' => 'Size',
        'duration' => 'Duration',
        'shortcode' => 'Shortcode',
        'Link' => 'Link',
        'RefreshShortcode' => 'Refresh Shortcode'
    ];
    return $columns;
}
function meethour_update_recording_ajax()
{
    $recording_id = intval($_POST['recording_id']);
    meethour_update_recording($recording_id);
}
add_action('wp_ajax_meethour_update_recording', 'meethour_update_recording_ajax');

add_action('manage_mh_recordings_posts_custom_column', 'meethour_recordings_custom_columns', 10, 2);
function meethour_recordings_custom_columns($column, $post_id)
{
    switch ($column) {
        case 'recording_date':
            echo esc_html(get_post_meta($post_id, 'recording_date', true));
            break;
        case 'recording_size':
            $size = get_post_meta($post_id, 'recording_size', true);
            echo number_format($size / 1024 / 1024, 2) . ' MB';
            break;
        case 'duration':
            $duration = get_post_meta($post_id, 'duration', true);
            $duration_parts = explode(':', $duration);
            $hours = str_pad(intval($duration_parts[0]), 2, '0', STR_PAD_LEFT);
            $minutes = str_pad(intval($duration_parts[1]), 2, '0', STR_PAD_LEFT);
            $seconds = explode('.', $duration_parts[2])[0];
            $seconds = str_pad(intval($seconds), 2, '0', STR_PAD_LEFT);
            $formatted_duration = $hours . ':' . $minutes . ':' . $seconds;
            echo esc_html($formatted_duration);
            break;
        case 'shortcode':
            echo '<button class="button button-small copy-shortcode" data-shortcode="[meethour recording_id=' . $post_id . ']">Copy Shortcode</button>';
            echo '<script>
            jQuery(document).ready(function($) {
                $(".copy-shortcode").click(function() {
                    var shortcode = $(this).data("shortcode");
                    navigator.clipboard.writeText(shortcode).then(function() {
                        var button = $(this);
                        button.text("Copied!");
                        setTimeout(function() {
                            button.text("Copy Shortcode");
                        }, 2000);
                    }.bind(this));
                });
            });
            </script>';

            break;
        case 'Link':
            echo '<a href="' . get_permalink($post_id) . '" target="_blank">View Recording</a>';
            break;
        case 'RefreshShortcode':
            $recording_id = get_post_meta($post_id, 'recording_id', true);
            $recording_expiry = get_post_meta($post_id, 'recording_expiry', true);
            error_log('This is the recording expiry Kaveem: ' . $recording_expiry);
            echo '<div id="countdown-' . $recording_id . '"></div>';
            echo '<button id="refreshButton-' . $recording_id . '" style="display:none;" onclick="meethour_update_recording(' . $recording_id . ')">Refresh video link</button>';
            echo '<script>
        function startCountdown(expiryDateStr, recordingId) {
            var countdownElement = document.getElementById("countdown-" + recordingId);
            var refreshButton = document.getElementById("refreshButton-" + recordingId);
            var expiryDate = new Date(expiryDateStr);
            var countdownInterval = setInterval(function () {
                var now = new Date();
                var distance = expiryDate - now;
                if (distance < 0) {
                    clearInterval(countdownInterval);
                    countdownElement.style.display = "none";
                    refreshButton.style.display = "inline-block";
                    meethour_update_recording(recordingId);
                    return;
                } else {
                    refreshButton.style.display = "none";
                    countdownElement.style.display = "inline-block"; 
                }
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                days = days < 10 ? "0" + days : days;
                hours = hours < 10 ? "0" + hours : hours;
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                countdownElement.textContent = "Expires in: " + days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
            }, 1000);
        }

        document.addEventListener("DOMContentLoaded", function() {
            var expiryTime = "' . ($recording_expiry) . '";
            startCountdown(expiryTime, ' . $recording_id . ');
        });

        function meethour_update_recording(recordingId) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "' . admin_url('admin-ajax.php') . '", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log("Recording updated successfully.");
                    // Parse the response to get the new expiry date
                    var response = JSON.parse(xhr.responseText);
                    var newExpiryDate = response.data.recording_expiry;
                    // Restart the countdown with the new expiry date
                    startCountdown(newExpiryDate, recordingId);
                }
            };
            xhr.send("action=meethour_update_recording&recording_id=" + recordingId);
        }
    </script>';
            break;
    }
}

function meethour_update_recording()
{
    $recording_id = "33231";
    $meetHourApiService = new MHApiService();
    $access_token = get_option('meethour_access_token', '');

    if (empty($access_token)) {
        return new WP_Error('no_token', 'Access token not found');
    }

    $body = new GetSingleRecording($recording_id);
    $response = $meetHourApiService->getSingleRecording($access_token, $body);
    if ($response->success == false) {
        add_settings_error('meethour_messages', 401, esc_html($response->message), 'error');
        return;
    }
    settings_errors('meethour_messages');
    error_log("This is GetSingleMeeting" . json_encode($response));

    // $response = wp_remote_post('https://api.meethour.io/api/v1.2/customer/getsinglerecording', [
    //     'headers' => [
    //         'Authorization' => 'Bearer ' . $access_token,
    //         'Content-Type' => 'application/json',
    //         'Accept' => 'application/json'
    //     ],
    //     'body' => json_encode([
    //         'recording_id' => $recording_id
    //     ])
    // ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (isset($data['error'])) {
        return new WP_Error('api_error', $data['error']);
    }

    $recording = $data['recording'];

    $args = array(
        'post_type' => 'mh_recordings', // Change this to your specific post type if needed
        'meta_key' => 'recording_id',
        'meta_value' => '33379',
        'fields' => 'ids', // Only return post IDs
        'posts_per_page' => 1 // Limit to one result
    );

    $posts = get_posts($args);
    update_post_meta($posts[0], 'recording_expiry', sanitize_text_field($recording['recording_expiry']));
    return $recording;
}



function meethour_fetch_recordings()
{
    $meetHourApiService = new MHApiService();
    $access_token = get_option('meethour_access_token', '');

    if (empty($access_token)) {
        error_log('Access token not found');
        wp_send_json_error('Access token not found');
        return;
    }

    $current_page = get_option('mh_recordings_current_page', 1);
    $total_pages = get_option('mh_recordings_total_pages', null);

    if (empty($current_page)) {
        $current_page = 1;
        update_option('mh_recordings_current_page', $current_page);
    };

    $posts_per_page = 20;
    $start = ($current_page - 1) * $posts_per_page + 1;
    $end = $current_page * $posts_per_page;
    $post_limit = "{$start}-{$end}";
    update_option('mh_recordings_post_limit', $post_limit);

    $main = new RecordingsList();
    $main->limit = $posts_per_page;
    $main->page = $current_page;
    $response = $meetHourApiService->recordingsList($access_token, $main);
    if ($response->success == false) {
        add_settings_error('meethour_messages', 401, esc_html($response->message), 'error');
        return;
    }
    settings_errors('meethour_messages');

    if (is_null($total_pages)) {
        $total_pages = $response->total_pages;
        update_option('mh_recordings_total_pages', $total_pages);
    }

    $recordings = json_encode($response->meethour_recordings);
    $recording_array = json_decode($recordings, true);

    if (!$recording_array) {
        wp_send_json_error('Failed to decode recordings JSON');
        return;
    }

    foreach ($recording_array as $record) {
        $existing_posts = new WP_Query([
            'post_type' => 'mh_recordings',
            'meta_query' => [
                [
                    'key' => 'recording_id',
                    'value' => $record['recording_id'],
                    'compare' => '='
                ]
            ]
        ]);

        if (!$existing_posts->have_posts()) {
            $post_id = wp_insert_post([
                'post_title' => $record['recording_name'],
                'post_content' => $record['topic'],
                'post_type' => 'mh_recordings',
                'post_status' => 'publish',
                'meta_input' => [
                    'recording_date' => $record['recording_date'],
                    'recording_size' => $record['recording_size'],
                    'recording_path' => $record['recording_path'],
                    'duration' => $record['duration'],
                    'recording_id' => $record['recording_id'],
                    'recording_expiry' => $record['recording_expiry'],
                ]
            ]);
        }
    }

    if ($current_page < $total_pages) {
        $current_page++;
        update_option('mh_recordings_current_page', $current_page);
        error_log("These is the Current Page No. #268 : " . $current_page);
    } else {
        delete_option('mh_recordings_current_page');
        delete_option('mh_recordings_total_pages');
    }

    wp_send_json_success($recording_array);
}
add_action('wp_ajax_meethour_fetch_recordings', 'meethour_fetch_recordings');

function meethour_hide_add_new_post_button()
{
    global $pagenow;
    if ($pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'mh_recordings') {
        echo '<style>
                .wrap .wp-heading-inline+.page-title-action{
                    display: none;
                };
                #sync-recordings{
                    margin-left:20px;
                }
        </style>';
    }
}
add_action('admin_head', 'meethour_hide_add_new_post_button');
