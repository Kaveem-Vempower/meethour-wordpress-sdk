<?php
require(WP_PLUGIN_DIR . '/meethour/vendor/autoload.php');
require(WP_PLUGIN_DIR . '/meethour/vendor/meethour/php-sdk/src/autoload.php');

use MeetHourApp\Services\MHApiService;
use MeetHourApp\Types\Login;

function meethour_token_page()
{
    $meetHourApiService = new MHApiService();
    $wordpress_user_data = wordpress_fetch_users();

    // Handle form submission for token generation
    if (isset($_POST['meethour_generate_token'])) {
        if (!wp_verify_nonce($_POST['meethour_token_nonce'], 'meethour_generate_token')) {
            wp_die('Security check failed');
        }

        $client_id = sanitize_text_field($_POST['client_id']);
        $client_secret = sanitize_text_field($_POST['client_secret']);
        $api_key = sanitize_text_field($_POST['api_key']);
        update_option('meethour_api_key', $api_key);
        $username = sanitize_email($_POST['username']);
        $password = $_POST['password'];
        $grant_type = "password";
        $main_user = sanitize_text_field($_POST['main_user']);
        update_option('meethour_main_user', $main_user);

        $body = new Login($client_id, $client_secret, $grant_type, $username, $password);
        $response = $meetHourApiService->login($body);

        $body = json_decode(wp_remote_retrieve_body($response->access_token), true);
        if (isset($response->access_token)) {
            update_option('meethour_access_token', $response->access_token);
            update_option('meethour_access_token_expirey', $response->expires_in);
            update_option('meethour_client_id', $client_id);
            update_option('meethour_client_secret', $client_secret);
            update_option('meethour_username', $username);
            update_option('meethour_password', $password);
            add_settings_error(
                'meethour_messages',
                'meethour_success',
                'Access token generated and stored successfully!',
                'success'
            );
            echo '<script type="text/javascript">window.location.reload()</script>';
        } else {
            add_settings_error(
                'meethour_messages',
                'meethour_error',
                'Failed to generate access token. Please check your credentials.',
                'error'
            );

            echo '<script type="text/javascript">window.location.reload()</script>';
        }
    }
    settings_errors('meethour_messages');
?>
    <div class="wrap">
        <h1 style="display: flex; justify-content: space-between;">Generate MeetHour Access Token
        </h1>
        <div class=" card">
            <form method="post" action="">
                <?php wp_nonce_field('meethour_generate_token', 'meethour_token_nonce'); ?>

                <table class="form-table">
                    <tr>
                        <th><label for="client_id">Client ID</label></th>
                        <td><input type="text" id="client_id" value='<?php echo get_option('meethour_client_id', ''); ?>' name="client_id" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="client_secret">Client Secret</label></th>
                        <td><input type="password" id="client_secret" value='<?php echo get_option('meethour_client_secret', ''); ?>' name="client_secret" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="api_key">API Key</label></th>
                        <td><input type="text" id="api_key" value='<?php echo get_option('meethour_api_key', ''); ?>' name="api_key" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="username">Username (Email)</label></th>
                        <td><input type="email" id="username" value='<?php echo get_option('meethour_username', ''); ?>' name="username" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="password">Password</label></th>
                        <td><input type="password" id="password" value='<?php echo get_option('meethour_password', ''); ?>' name="password" class="regular-text" required></td>
                    </tr>
                    <tr>
                        <th><label for="main_user">Owner</label></th>
                        <td> <select name="main_user" id="main_user" class="select2-select req" style="width: 100%;">
                                <?php foreach ($wordpress_user_data as $user) { ?>
                                    <option value='<?php echo ($user->id); ?>'>
                                        <?php echo ($user->user_email); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="meethour_generate_token" class="button button-primary"
                        value="Generate Access Token">
                </p>
                <?php $access_token = get_option('meethour_access_token', ''); ?>
                <?php if (!empty($access_token)): ?>

                    <div style="word-wrap:break-word;" class="notice notice-success">
                        <p><strong>Access Token:</strong> <?php echo esc_html($access_token); ?></p>
                        <button style="margin-bottom: 10px;" class="button button-small copy-shortcode"
                            data-shortcode='<?php echo esc_attr($access_token); ?>'>
                            Copy Access Token
                        </button>
                        <br />
                    </div>
                    <script>
                        jQuery(document).ready(function($) {
                            $('.copy-shortcode').click(function() {
                                var shortcode = $(this).data('shortcode');
                                navigator.clipboard.writeText(shortcode).then(function() {
                                    var button = $(this);
                                    button.text('Copied!');
                                    setTimeout(function() {
                                        button.text('Copy Access Token');
                                    }, 2000);
                                }.bind(this));
                            });
                        });
                    </script>
                <?php endif; ?>
            </form>
        </div>
    </div>
<?php
}
