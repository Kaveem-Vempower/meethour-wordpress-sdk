<?php
require(WP_PLUGIN_DIR . '/meethour/vendor/autoload.php');
require(WP_PLUGIN_DIR . '/meethour/vendor/meethour/php-sdk/src/autoload.php');


use MeetHourApp\Services\MHApiService;
use MeetHourApp\Types\ScheduleMeeting;

function meethour_Instant_page()
{
    // Get access token
    $meetHourApiService = new MHApiService();

    $access_token = get_option('meethour_access_token', '');

    // Handle instant meeting request
    if (isset($_POST['meethour_instant_submit'])) {
        // Verify security nonce
        if (!wp_verify_nonce($_POST['meethour_instant_nonce'], 'meethour_instant_meeting')) {
            wp_die('Security check failed');
        }

        if (empty($access_token)) {
            add_settings_error(
                'meethour_messages',
                'meethour_error',
                'Access token not found. Please generate an access token first.',
                'error'
            );
        } else {

            $meeting_name = sanitize_text_field($_POST['meeting_name'] ?? '');
            $passcode = sanitize_text_field($_POST['meeting_passcode'] ?? '');
            $meeting_time = date("h:i");
            $meeting_meridiem = date("a");
            $meeting_date = date("d-m-Y");
            $timezone = 'Asia/Kolkata';
            $body = new ScheduleMeeting($meeting_name, $passcode, $meeting_time, $meeting_meridiem, $meeting_date, $timezone);
            $response = $meetHourApiService->scheduleMeeting($access_token, $body);
            $data = $response->data;
            $message = isset($data->meeting_id) ? $response->message : 'No message available';
            $meeting_link = isset($data->meeting_id) ? $data->joinURL : 'No link available';
            if ($response->success == false) {
                add_settings_error('meethour_messages', 'meethour_success', esc_html($response->message), 'error');
            } else {
                add_settings_error('meethour_messages', 'meethour_success', esc_html($message) . ' Meeting Link: ' . esc_html($meeting_link), 'success');
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
                    <table class="form-table">
                        <tr>
                            <th><label for="meeting_name">Meeting Name</label></th>
                            <td><input type="text" id="meeting_name" name="meeting_name" class="regular-text" required></td>
                        </tr>
                        <tr>
                            <th><label for="meeting_name">Meeting Passcode</label></th>
                            <td><input type="password" id="meeting_passcode" name="meeting_passcode" class="regular-text" required></td>
                        </tr>
                    </table>
                    <p class="submit">
                        <input type="submit" name="meethour_instant_submit" class="button button-primary" value="Create Instant Meeting">
                    </p>
                    <?php if (isset($meeting_link)) : ?>
                        <button style="margin-right: 10px;" class="button button-small copy-shortcode"
                            data-shortcode=<?php echo esc_attr($meeting_link); ?>>
                            Copy Meeting Link
                        </button>
                        <button class="button button-small copy-shortcode"
                            data-shortcode='[meethour meeting_id="<?php echo esc_attr($data->meeting_id); ?>"]'>
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
            box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
        }
    </style>
<?php
}
