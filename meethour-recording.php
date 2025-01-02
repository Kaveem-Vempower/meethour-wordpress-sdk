<?php
function meethour_register_recordings_post_type() {
    register_post_type('mh_recordings', [
        'labels' => [
            'name' => 'MeetHour Recordings',
            'singular_name' => 'MeetHour Recordings',
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
    ]);
}
add_action('init', 'meethour_register_recordings_post_type');

add_filter('manage_mh_recordings_posts_columns', 'meethour_recordings_columns');
function meethour_recordings_columns($columns) {
    $columns = [
        'cb' => '<input type="checkbox" />',
        'title' => 'Recording Name',
        'recording_date' => 'Recording Date',
        'recording_size' => 'Size',
        'duration' => 'Duration',
        'shortcode' => 'Shortcode',
        'RefreshShortcode' => 'Refresh Shortcode'
    ];
    return $columns;
}

add_action('manage_mh_recordings_posts_custom_column', 'meethour_recordings_custom_columns', 10, 2);
function meethour_recordings_custom_columns($column, $post_id) {
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
            $recording_path = get_post_meta($post_id, 'recording_path', true);
            echo '<button class="button button-small copy-shortcode" data-shortcode="[meethour_video url=\'' . esc_attr($recording_path) . '\']">Copy Shortcode</button>';
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
            case 'RefreshShortcode':
                $recording_id = get_post_meta($post_id, 'recording_id', true);
                $recording_expiry = get_post_meta($post_id, 'recording_expiry', true);
                echo '<div id="countdown"></div>';
                echo '<button id="refreshButton" style="display:none;" onclick="meethour_update_recording(' . $recording_id . ')">Refresh</button>';
                echo '<script>
                function startCountdown(expiryDateStr) {
                    var countdownElement = document.getElementById("countdown");
                    var refreshButton = document.getElementById("refreshButton");
                    var expiryDate = new Date(expiryDateStr);
                    var countdownInterval = setInterval(function () {
                        var now = new Date();
                        var distance = expiryDate.getTime() - now.getTime();
            
                        if (distance < 0) {
                            clearInterval(countdownInterval);
                            countdownElement.style.display = "none";
                            refreshButton.style.display = "inline-block";
                            return;
                        }
            
                        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
                        hours = hours < 10 ? "0" + hours : hours;
                        minutes = minutes < 10 ? "0" + minutes : minutes;
                        seconds = seconds < 10 ? "0" + seconds : seconds;
            
                        countdownElement.textContent = "Expires in: " + hours + ":" + minutes + ":" + seconds;
                    }, 1000);
                }
            
                document.addEventListener("DOMContentLoaded", function() {
                    var expiryTime = "' . esc_js($recording_expiry) . '";
                    startCountdown(expiryTime);
                });
                </script>';
                break;
            


            }
        }

function meethour_update_recording($recording_id) {
    $access_token = get_option('meethour_access_token', '');
    
    if (empty($access_token)) {
        return new WP_Error('no_token', 'Access token not found');
    }

    $response = wp_remote_post('https://api.meethour.io/api/v1.2/customer/getsinglerecording', [
        'headers' => [
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ],
        'body' => json_encode([
            'recording_id' => $recording_id
        ])
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    $recording = $data['recording'] ?? [];
    if (isset($data['error'])) {
        return new WP_Error('api_error', $data['error']);
    }

    $post_id = wp_update_post([
        'ID' => $recording_id,
        'post_title' => $recording['recording_name'],
        'post_content' => $recording['topic'],
        'post_type' => 'mh_recordings',
        'post_status' => 'publish',
        'meta_input' => [
            'recording_date' => $recording['recording_date'],
            'recording_size' => $recording['recording_size'],
            'recording_path' => $recording['recording_path'],
            'duration' => $recording['duration'],
            'recording_id' => $recording['recording_id'],
            'recording_expiry' => $recording['recording_expiry']
        ]
    ]);

    return $recording;
}

function meethour_fetch_recordings() {
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
    $recordings = $body['meethour_recordings'] ?? [];

    foreach ($recordings as $recording){
        $post_id = wp_insert_post([
            'post_title' => $recording['recording_name'],
            'post_content' => $recording['topic'],
            'post_type' => 'mh_recordings',
            'post_status' => 'publish',
            'meta_input' => [
                'recording_date' => $recording['recording_date'],
                'recording_size' => $recording['recording_size'],
                'recording_path' => $recording['recording_path'],
                'duration' => $recording['duration'],
                'recording_id' => $recording['recording_id'],
                'recording_expiry' => $recording['recording_expiry']
            ]
        ]);
    }

    return $recordings;
}

function meethour_recordings_page(){
    $recording_query = new WP_Query([
        'post_type' => 'mh_recordings',
        'posts_per_page' => -1
    ]);
    $recordings = $recording_query->posts;
    $recording = meethour_fetch_recordings();
    echo json_encode($recording);
    ?>
    <div class="wrap">
        <h1>MeetHour Recordings List</h1>

        <?php if (empty($recordings)): ?>
            <div class="notice notice-error">
                <p>No recordings found.</p>
            </div>
        <?php else: ?>
            <div class="card">
                <table style="width: 1000px;" class="widefat striped">
                    <thead>
                        <tr>
                            <th>Recording Date</th>
                            <th>Recording Name</th>
                            <th>Topic</th>
                            <th>Duration</th>
                            <th>Size</th>
                            <th>Shortcode</th>
                            <th>Refresh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recordings as $recording): ?>
                            <tr>
                                <td><?php echo esc_html(get_post_meta($recording->ID, 'recording_date', true)); ?></td>
                                <td><?php echo esc_html($recording->post_title); ?></td>
                                <td><?php echo esc_html($recording->post_content); ?></td>
                                <td>
                                    <?php 
                                    $duration = get_post_meta($recording->ID, 'duration', true);
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
                                    $size = get_post_meta($recording->ID, 'recording_size', true);
                                    $size_in_mb = $size / 1024 / 1024;
                                    echo esc_html(number_format($size_in_mb, 2) . ' MB');
                                    ?>
                                </td>
                                <td>
                                    <code>[meethour_video url="<?php echo esc_attr(get_post_meta($recording->ID, 'recording_path', true)); ?>"]</code>
                                    <button class="button button-small copy-shortcode" 
                                            data-shortcode='[meethour_video url="<?php echo esc_attr(get_post_meta($recording->ID, 'recording_path', true)); ?>"]'>
                                        Copy
                                    </button>
                                </td>
                                <td>
                                <?php $recording_id = get_post_meta($recording->ID, 'recording_id', true);
                $recording_expiry = get_post_meta($recording->ID, 'recording_expiry', true); ?>
                <div id="countdown"></div>';
                <button id="refreshButton" style="display:none;" onclick="meethour_update_recording('<?php $recording->ID ?>')">Refresh</button>;
                <script>
                function startCountdown(expiryDateStr) {
                    var countdownElement = document.getElementById("countdown");
                    var refreshButton = document.getElementById("refreshButton");
                    var expiryDate = new Date(expiryDateStr);
                    var countdownInterval = setInterval(function () {
                        var now = new Date();
                        var distance = expiryDate.getTime() - now.getTime();
            
                        if (distance < 0) {
                            clearInterval(countdownInterval);
                            countdownElement.style.display = "none";
                            refreshButton.style.display = "inline-block";
                            return;
                        }
            
                        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            
                        hours = hours < 10 ? "0" + hours : hours;
                        minutes = minutes < 10 ? "0" + minutes : minutes;
            
                        countdownElement.textContent = "Expires in: " + hours + ":" + minutes ;
                    }, 1000);
                }
            
                document.addEventListener("DOMContentLoaded", function() {
                    var expiryTime = "<?php echo esc_js($recording_expiry); ?>";
                    startCountdown(expiryTime);
                });
                </script>';
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