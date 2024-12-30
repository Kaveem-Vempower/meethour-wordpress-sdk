<?php
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