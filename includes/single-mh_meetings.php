<?php

use MeetHourApp\Services\MHApiService;
use MeetHourApp\Types\GenerateJwt;

require(WP_PLUGIN_DIR . '/meethour/vendor/autoload.php');
require(WP_PLUGIN_DIR . '/meethour/vendor/meethour/php-sdk/src/autoload.php');




while (have_posts()) : the_post();
    error_log("This is Post ID asdbasdmbamsd :" + get_the_ID());
    $meetHourApiService = new MHApiService();
    $token = get_option('meethour_access_token', '');
    $meeting_id = get_post_meta(get_the_ID(), 'meeting_id', true);
    $join_url = get_post_meta(get_the_ID(), 'join_url', true);
    $contact_id = get_user_meta(get_current_user_id(), 'contact_id', true);
    $meeting_passcode = get_post_meta(get_current_user_id(), 'meeting_passcode');
    $parsed_url = parse_url($join_url);
    $query = $parsed_url['query'];
    parse_str($query, $params);
    $pcode = $params['pcode'];

    $current_user = get_the_ID();
    $attendes = get_post_meta(get_the_ID(), 'attendes', true);
    $attendes_ids = [];
    foreach ($attendes as $attendes) {
        $attendes_ids[] = $attendes->id;
    }

    $body = new GenerateJwt($meeting_id);
    // for meeting owner to login in meeting 
    if ($current_user !== get_option('meethour_main_user', '')) {
        $body->$contact_id = $contact_id;
    }
    // checks where wordpress user is invited in meeting & for not wordpress logged-in user and guest
    elseif (in_array(get_current_user_id(), $attendes_ids) && !empty(get_the_ID())) {
        $response = $meetHourApiService->generateJwt($token, $body);
    } else {
        $response = $meetHourApiService->generateJwt($token, $body);
    }


    if ($response->success == true) {
        $jwt_token = $response->jwt;
    }

    if (!empty($meeting_id)) {
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <script src='https://api.meethour.io/libs/v2.4.5/external_api.min.js?apiKey=' .<?php get_option('meethour_api_key', '') ?>></script>
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
                    apiKey: "<?php get_option('meethour_api_key', '') ?>",
                    pcode: "<?php echo esc_html(isset($pcode) ?  $pcode : ''); ?>",
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
                var api = new MeetHourExternalAPI(domain, options);
            </script>
        </body>

        </html>

    <?php } else {
        echo '<div>No Meeting ID Found</div>';
    } ?>
<?php endwhile; ?>