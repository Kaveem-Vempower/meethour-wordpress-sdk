<?php
function meethour_fetch_contacts() {
    $access_token = get_option('meethour_access_token', '');
    
    if (empty($access_token)) {
        return new WP_Error('no_token', 'Access token not found');
    }

    $response = wp_remote_post('https://api.meethour.io/api/v1.2/customer/videorecordinglist', [
        'headers' => [
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ],
        'body' => json_encode([
            'filter_by' => 'Local', 
            'limit' => 10,
            'page' => 1,
        ])
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    return $body['meethour_recordings'] ?? [];
}

function meethour_recordings_page(){
    $recording = meethour_fetch_contacts();
    ?>
    <div class="wrap">
        <h1>MeetHour Recordings List</h1>

        <?php if (is_wp_error($recording)): ?>
            <div class="notice notice-error">
                <p><?php echo esc_html($recording->get_error_message()); ?></p>
            </div>
        <?php else: ?>
            <div class="card">
                <table  style="width: 1000px;" class="widefat striped">
                    <thead>
                        <tr>
                            <th>Time Stamp</th>
                            <th>Recodings</th>
                            <th>Meetings</th>
                            <th>Duration</th>
                            <th>Size</th>
                            <th>Shortcode</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recording as $recording): ?>
                            <tr>
                                <td><?php echo esc_html($recording['recording_date']); ?></td>
                                <td><?php echo esc_html($recording['recording_name']); ?></td>
                                <td><?php echo esc_html($recording['topic']); ?></td>
                                <td>
                                    <?php 
                                    $duration = $recording['duration'];
                                    $duration_parts = explode(':', $duration);
                                    $hours = str_pad(intval($duration_parts[0]), 2, '0', STR_PAD_LEFT);
                                    $minutes = str_pad(intval($duration_parts[1]), 2, '0', STR_PAD_LEFT);
                                    $seconds = explode('.', $duration_parts[2])[0];
                                    $seconds = str_pad(intval($seconds), 2, '0', STR_PAD_LEFT);
                                    $formatted_duration = $hours . ':' . $minutes . ':' . $seconds;
                                    echo esc_html($formatted_duration);
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $size_in_mb = $recording['recording_size'] / 1024 / 1024;
                                    echo esc_html( number_format($size_in_mb, 2) . ' MB');
                                    ?>
                                </td>
                                <td>
                                    <code>[meethour_video url="<?php echo esc_attr($recording['recording_path']); ?>"]</code>
                                    <button class="button button-small copy-shortcode" 
                                            data-shortcode='[meethour_video url="<?php echo esc_attr($recording['recording_path']); ?>"]'>
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


// Shortcode to display video
function meethour_video_shortcode($atts) {
    $atts = shortcode_atts(array(
        'url' => ''
    ), $atts, 'meethour_video');

    $url = esc_url($atts['url']);
    if (empty($url)) {
        return '<p>No video URL provided.</p>';
    }

    return '<video controls style="width: 100%;"><source src="' . $url . '" type="video/mp4">Your browser does not support the video tag.</video>';
}
add_shortcode('meethour_video', 'meethour_video_shortcode');

?>