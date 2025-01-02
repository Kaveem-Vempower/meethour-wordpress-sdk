<?php
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
                        <tr>
                            <th>General Options </th>
                            <td>
                                <input type="checkbox" name="options[]" value="ALLOW_GUEST" id="vertical-checkbox-guest-user" checked="">
                                <label class="cursor-pointer select-none" for="vertical-checkbox-guest-user">Guest user can join meeting</label>
                            </td>
                            <td>
                                <input type="checkbox" name="options[]" value="JOIN_ANYTIME" id="vertical-checkbox-allow-anytime" checked="" onclick="if(($(this).is(':checked'))) { $('#allow_join_before_sec').hide(); } else {$('#allow_join_before_sec').show();} ">
                                <label class="cursor-pointer select-none" for="vertical-checkbox-allow-anytime">Allow participants to join anytime</label>
                            </td>
                            <td>
                                <input type="checkbox" name="options[]" value="ENABLE_LOBBY" id="vertical-checkbox-enable-lobby" checked="">
                                <label class="cursor-pointer select-none" for="vertical-checkbox-enable-lobby">Enable Lobby</label>
                            </td>
                            <td>
                                <input type="checkbox" name="options[]" value="LIVEPAD" id="vertical-checkbox-live-pad" checked="">
                                <label class="cursor-pointer select-none" for="vertical-checkbox-live-pad">LivePad</label>
                            </td>
                            <td>
                                <input type="checkbox" name="options[]" value="WHITE_BOARD" id="vertical-checkbox-WhiteBoard" checked="">
                                <label class="cursor-pointer select-none" for="vertical-checkbox-WhiteBoard">WhiteBoard</label>
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