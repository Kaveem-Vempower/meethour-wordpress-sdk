<?php
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
            'show_all' => 0
        ])
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    return $body['meetings'] ?? [];
}

// Keep your existing functions: meethour_token_page() and meethour_scheduler_page()
// [Previous code for these functions remains unchanged]

// Meetings List Page
function meethour_meetings_page() {
    $meetings = meethour_fetch_upcoming_meetings();
    ?>
    <div class="wrap">
        <h1>MeetHour Upcomming Meetings List</h1>

        <?php if (is_wp_error($meetings)): ?>
            <div class="notice notice-error">
                <p><?php echo esc_html($meetings->get_error_message()); ?></p>
            </div>
        <?php else: ?>
            <div class="card">
                <table class="widefat striped" style="width: 1000px;">
                    <thead>
                        <tr>
                            <th>Meeting Name</th>
                            <th>Meeting ID</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Agenda</th>
                            <th>Shortcode</th>
                            <th>Meeting Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($meetings as $meeting): ?>
                            <tr>
                                <td><?php echo esc_html($meeting['topic']); ?></td>
                                <td><?php echo esc_html($meeting['meeting_id']); ?></td>
                                <td>
                                    <?php 
                                    echo esc_html(date('M d, Y h:i A', strtotime($meeting['start_time'] )));
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    echo esc_html( $meeting['duration'] . 'm');
                                    ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo esc_attr(strtolower($meeting['agenda'])); ?>">
                                        <?php echo esc_html($meeting['agenda']); ?>
                                    </span>
                                </td>
                                <td>
                                    <code>[meethour id="<?php echo esc_attr($meeting['meeting_id']); ?>"]</code>
                                    <button class="button button-small copy-shortcode" 
                                            data-shortcode='[meethour id="<?php echo esc_attr($meeting['meeting_id']); ?>"]'>
                                        Copy
                                    </button>
                                </td>
                                <td>
                                    <a href="https://meethour.io/<?php echo esc_attr($meeting['meeting_id']); ?>" target="_blank">
                                        Join Meeting
                                    </a>
                                    <button class="button button-small copy-shortcode" 
                                            data-shortcode='https://meethour.io/<?php echo esc_attr($meeting['meeting_id']); ?>'>
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
function meethour_scheduler_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => ''
    ), $atts, 'meethour');

    $id = esc_attr($atts['id']);
    if (empty($id)) {
        return '<p>No meeting ID provided.</p>';
    }

    $iframe_src = "https://meethour.io/$id#interfaceConfig.applyMeetingSettings=true&interfaceConfig.disablePrejoinHeader=true&interfaceConfig.ENABLE_DESKTOP_DEEPLINK=false&interfaceConfig.disablePrejoinFooter=true&interfaceConfig.SHOW_MEET_HOUR_WATERMARK=false&interfaceConfig.HIDE_DEEP_LINKING_LOGO=true&interfaceConfig.MOBILE_APP_PROMO=false&interfaceConfig.ENABLE_MOBILE_BROWSER=true&appData.localStorageContent=null";

    return '<iframe allow="camera; microphone; display-capture; autoplay; clipboard-write" src="' . esc_url($iframe_src) . '" name="mhConferenceFrame0" id="mhConferenceFrame0" allowfullscreen="true" style="height: 50rem; width: 50rem; display: flex; justify-content: center; border: 0px;"></iframe>';
}
add_shortcode('meethour', 'meethour_scheduler_shortcode');

