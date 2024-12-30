<?php
function meethour_Instant_page() {
    // Get access token
    $access_token = get_option('meethour_access_token', '');

    // Handle instant meeting request
    if (isset($_POST['meethour_instant_submit'])) {
        // Verify security nonce
        if (!wp_verify_nonce($_POST['meethour_instant_nonce'], 'meethour_instant_meeting')) {
            wp_die('Security check failed');
        }

        if (empty($access_token)) {
            add_settings_error('meethour_messages', 'meethour_error', 
                'Access token not found. Please generate an access token first.', 'error');
        } else {
            // Prepare default meeting data for API
            $data = [
                'meeting_name' => 'Instant Meeting',
                'agenda' => 'This is an instant meeting.',
                'passcode' => 'passcode',
                'meeting_date' => date('d-m-Y'),
                'meeting_time' => date('h:i'),
                'meeting_meridiem' => date('A'),
                'duration_hr' => 1,
                'duration_min' => 0,
                'timezone' => 'Asia/Kolkata',
                'is_recurring' => 0,
                'options' => ['ALLOW_GUEST', 'JOIN_ANYTIME']
            ];

            // Make API request to schedule instant meeting
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
                $result = json_decode(wp_remote_retrieve_body($response), true);
                $message = isset($result['data']['meeting_id']) ? $result['message'] : 'No message available';
                $meeting_link = isset($result['data']['joinURL']) ? $result['data']['joinURL'] : 'No link available';
                add_settings_error('meethour_messages', 'meethour_success', 
                    esc_html($message) . ' Meeting Link: ' . esc_html($meeting_link), 'success');
            }
        }
    }

    // Display any error/success messages
    settings_errors('meethour_messages');
    ?>
    <div class="wrap">
        <h1>Instant MeetHour Meeting</h1>
            
        <?php if (empty($access_token)): ?>
            <div class="notice notice-error">
                <p>Access token not found. Please <a href="<?php echo admin_url('admin.php?page=meethour-settings'); ?>">generate an access token</a> first.</p>
            </div>
        <?php else: ?>
            <div class="card">
                <form method="POST">
                    <?php wp_nonce_field('meethour_instant_meeting', 'meethour_instant_nonce'); ?>
                    <p class="submit">
                        <input type="submit" name="meethour_instant_submit" class="button button-primary" value="Create Instant Meeting">
                    </p>
                    <?php if (isset($meeting_link)) : ?>
                    <button style="margin-right: 10px;" class="button button-small copy-shortcode" 
                     data-shortcode=<?php echo esc_attr($meeting_link); ?>>
                    Copy Meeting Link
                    </button>
                    <!-- <code>[meethour id="<?php echo esc_attr($result['data']['meeting_id']); ?>"]</code> -->
                    <button class="button button-small copy-shortcode" 
                    data-shortcode='[meethour id="<?php echo esc_attr($result['data']['meeting_id']); ?>"]'>
                    Copy Shortcode
                </button>
                <?php endif; ?>
                
                </form>
                <script>
            jQuery(document).ready(function($) {
                $('.copy-shortcode').click(function(event) {
                    event.preventDefault();
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
            </div>
        <?php endif; ?>
    </div>

    <style>
        .card {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding-right: 20px;
            padding-left: 20px;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
        }
    </style>
<?php
}