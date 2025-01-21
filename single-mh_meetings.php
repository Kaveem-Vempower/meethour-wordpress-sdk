<?php

use MeetHourApp\Services\MHApiService;
use MeetHourApp\Types\AddContact;
use MeetHourApp\Types\GenerateJwt;

require(WP_PLUGIN_DIR . '/meethour/vendor/autoload.php');
require(WP_PLUGIN_DIR . '/meethour/vendor/meethour/php-sdk/src/autoload.php');


add_action('user_register', 'create_user_in_my_app');

function create_user_in_my_app($user_id)
{
    $meetHourApiService = new MHApiService();

    $user = get_userdata($user_id);
    $token = get_option('meethour_access_token', '');
    $email = $user->user_email;
    $first_name = $user->first_name;
    $last_name = $user->last_name;
    $username = $user->user_login;

    // Make the API call
    $body = new AddContact($email, $first_name, $last_name, $username);
    $response = $meetHourApiService->AddContact($token, $body);
    echo $response;
    // Check for a successful response
    if (is_wp_error($response)) {
        return;
    }

    $body = wp_remote_retrieve_body($response->data);
    $data = json_decode($body, true);

    if (isset($data['id'])) {
        // Store the returned user ID in user meta
        update_user_meta($user_id, 'meethour_user_id', $data['id']);
    }
}

function generate_jwt_token() {}
?>
<?php while (have_posts()) : the_post(); ?>

    <?php
    $meetHourApiService = new MHApiService();
    $token = get_option('meethour_access_token', '');

    $meeting_id = get_post_meta(get_the_ID(), 'meeting_id', true);
    $join_url = get_post_meta(get_the_ID(), 'join_url', true);
    $contact_id = get_user_meta(get_current_user_id(), 'meethour_user_id');
    $meeting_passcode = get_post_meta(get_current_user_id(), 'meeting_passcode');
    $parsed_url = parse_url($join_url);
    $query = $parsed_url['query']; // Parse the query string to get the pcode --
    parse_str($query, $params);
    $pcode = $params['pcode']; // Output the pcode  --
    $body = new GenerateJwt($meeting_id, $contact_id);
    $response = $meetHourApiService->generateJwt($token, $body);
    $jwt_token = $response->jwt; //--
    ?>


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <script src='https://api.meethour.io/libs/v2.4.5/external_api.min.js?apiKey=' .<?php get_option('meethour_api_key', '') ?>></script>
        <style>

        </style>
    </head>

    <body>
        <div id="conference" style="height: 100vh;"></div>
        <script>
            var domain = " meethour.io";
            var options = {
                roomName: "<?php echo esc_html($meeting_id); ?>",
                width: "100%",
                height: "100%",
                parentNode: document.querySelector("#conference"),
                jwt: "<?php echo esc_html(isset($jwt_token) ?  $jwt_token : ''); ?>",
                // jwt: "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImFjY2Vzc190b2tlbiI6ImV5SjBlWEFpT2lKS1YxUWlMQ0poYkdjaU9pSlNVekkxTmlKOS5leUpoZFdRaU9pSTVNemxrWmpVeE5pMDJNekEzTFRRNVkyUXRPVGMxTXkwek1XRTNNemRrT1RGaE1HWWlMQ0pxZEdraU9pSXpaalJrTmpnNE56QmhZakk0TlRWbFpHRm1OR0UxTmpFNE1tVTJObVJpTTJJeVkyWTJZMkkxWVRGaFpXRm1NR05pWm1OaE5EVTRZVEk1Tm1FMk5UQTVNMlF6WVdFM00yVmpZakJtWW1Rd055SXNJbWxoZENJNk1UY3pOalV4Tnprd01pNDRNREEwTWpRc0ltNWlaaUk2TVRjek5qVXhOemt3TWk0NE1EQTBNamNzSW1WNGNDSTZNVGMyT0RBMU16a3dNaTQzT0RVME1qY3NJbk4xWWlJNklqTXpNVE14SWl3aWMyTnZjR1Z6SWpwYlhYMC5EWTEtWGI3Y3ozZXJwcGNuTkRTRlFKT1J0bHRDZVEySTBSUndTNF91RGFVVzZDdVEyS3c1N2l6SGNIQWdpaEQ1Y1RISW1DUWRGNkZwWjVtQThCT0RMZyJ9.eyJjb250ZXh0Ijp7ImZlYXR1cmVzIjp7ImxpdmVzdHJlYW1pbmciOmZhbHNlLCJpbmJvdW5kLWNhbGwiOmZhbHNlLCJvdXRib3VuZC1jYWxsIjpmYWxzZSwic2lwLWluYm91bmQtY2FsbCI6ZmFsc2UsInNpcC1vdXRib3VuZC1jYWxsIjpmYWxzZSwidHJhbnNjcmlwdGlvbiI6ZmFsc2UsInJlY29yZGluZyI6ZmFsc2V9fSwiYXVkIjoibWVldGhvdXIuaW8iLCJpc3MiOiJteV93ZWJfY2xpZW50Iiwic3ViIjoibWVldGhvdXIiLCJyb29tIjoiTUhSMjUwMTE2MTQzNjM2IiwiZXhwIjoxODk0ODc4ODkyLCJtb2RlcmF0b3IiOnRydWUsIm9wdGlvbnMiOnsiY29uZmlnb3ZlcndyaXRlIjpbXSwiaW50ZXJmYWNlY29uZmlnb3ZlcndyaXRlIjp7ImFwcGx5TWVldGluZ1NldHRpbmdzIjp0cnVlLCJkaXNhYmxlUHJlam9pbkhlYWRlciI6dHJ1ZSwiZGlzYWJsZVByZWpvaW5Gb290ZXIiOnRydWUsIkVOQUJMRV9ERVNLVE9QX0RFRVBMSU5LIjp0cnVlLCJkaXNwbGF5TG9nb0luUmVjb3JkZXIiOnRydWUsImhpZGVQYXJ0aWNpcGFudFRpbGVzT25SZWNvcmRpbmciOmZhbHNlLCJNT0JJTEVfQVBQX1BST01PIjp0cnVlLCJFTkFCTEVfTU9CSUxFX0JST1dTRVIiOnRydWUsIkhJREVfREVFUF9MSU5LSU5HX0xPR08iOnRydWUsIlNIT1dfTUVFVF9IT1VSX1dBVEVSTUFSSyI6ZmFsc2V9fSwiQVBJS2V5IjoiNzA3M2M2OTY4MzE5N2RkZGM5MjQyN2YzMzdmMGU5YTMyNjU3ZjFlODZmMThlNTE3NWIyZDM5MmY1ZTA0MDA0NSIsInVzZXJfdHlwZSI6MSwibWVldGluZyI6eyJpZCI6MTQzNjM2LCJtZWV0aW5nX2lkIjoiTUhSMjUwMTE2MTQzNjM2Iiwib2NjdXJyZW5jZV9pZCI6MCwidG9waWMiOiJTY2hlZHVsZSBNZWV0aW5nIFBvc3QgVHlwZSBGaW5hbCBQb3N0IDE0Iiwic2V0dGluZ3MiOnsiRU5BQkxFX0VNQkVFRF9NRUVUSU5HIjoxLCJET05PUl9CT1giOjEsIkNQX0NPTk5FQ1QiOjEsIkFMTE9XX0dVRVNUIjoxLCJKT0lOX0FOWVRJTUUiOjEsIkVOQUJMRV9MT0JCWSI6MSwiTElWRVBBRCI6MSwiV0hJVEVfQk9BUkQiOjF9LCJzdGFydF90aW1lIjoiMjAyNS0wMS0yNSAwNzo1NDowMCIsImVuZF90aW1lIjoiMjAyNS0wMS0yNSAxMDoyNDowMCIsInRpbWV6b25lIjoiRXRjXC9HTVQrMTIiLCJtYXhfcGFydGljaXBhbnRzIjo1MDAsIm9faWQiOiIifX0.rMYzIC0QIUsNGaGDB29pNPI9PlGKExxzndEwX_dLIms",
                apiKey: "<?php get_option('meethour_api_key', '') ?>",
                // apiKey: "7073c69683197dddc92427f337f0e9a32657f1e86f18e5175b2d392f5e040045",
                pcode: "<?php echo esc_html(isset($pcode) ?  $pcode : ''); ?>",
                // pcode: "734d67108094c754070c51133c62afc3",
                configOverwrite: {
                    prejoinPageEnabled: false,
                    disableInviteFunctions: true,
                },
                interfaceConfigOverwrite: {
                    applyMeetingSettings: true,
                    disablePrejoinHeader: true,
                    disablePrejoinFooter: true,
                    SHOW_MEET_HOUR_WATERMARK: false,
                    ENABLE_DESKTOP_DEEPLINK: false,
                    HIDE_DEEP_LINKING_LOGO: true,
                    MOBILE_APP_PROMO: false,
                    ENABLE_MOBILE_BROWSER: true
                },
            };
            // Initialization of MeetHour External API
            var api = new MeetHourExternalAPI(domain, options);
        </script>
    </body>

    </html>


<?php endwhile; ?>