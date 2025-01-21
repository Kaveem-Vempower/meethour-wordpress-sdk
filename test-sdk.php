<?php

require(WP_PLUGIN_DIR . '/meethour/vendor/autoload.php');
require(WP_PLUGIN_DIR . '/meethour/vendor/meethour/php-sdk/src/autoload.php');


use MeetHourApp\Services\MHApiService;
use MeetHourApp\Types\Login;
use MeetHourApp\Types\ScheduleMeeting;
use MeetHourApp\Types\UpcomingMeetings;
use MeetHourApp\Types\ViewMeeting;
function meethour_recordings_test_page(){
    $client_id = "v5l2lnrtvk434yxt6msqm3y2s0m1m238l05t";
    $client_secret = "6a16bd2b2d1aedccfa5b8f499fc8914bdcc86330ce7a6aba5b83fb0d0f185996";
    $grant_type = "password";
    $username = "kaveem.uddin@v-empower.com";
    $password = "Kaveemuddin123";
    $meeting_id = "MHR241226141350 ";
    $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiJ2NWwybG5ydHZrNDM0eXh0Nm1zcW0zeTJzMG0xbTIzOGwwNXQiLCJqdGkiOiJiYjI4NDA4OTU5YmE2N2M2MzIyZDRmZDA1ZDFjNDFmZTlmMDA4YmJlOWM5OTY5ZGM3ODQ4NzcxNmVmYjYyOGNkZDAxNDAxYzExOGMwMDc3YyIsImlhdCI6MTczNTU0NTQ5NS4wMTU2MTgsIm5iZiI6MTczNTU0NTQ5NS4wMTU2MjEsImV4cCI6MTc2NzA4MTQ5NC45OTIwNDksInN1YiI6IjMzMTMxIiwic2NvcGVzIjpbXX0.Z4irtgIXmJbdccmxtXf_zk4zd6iD5No6tcQENviZl5rw6c01GUU2Op_QzKvqY0mt7RJBhwJjVfWda_6-GgCWmw";
    $meetHourApiService = new MHApiService();
    $login = new Login($client_id, $client_secret, $grant_type, $username, $password);
    $loginResponse = $meetHourApiService->login($login);
    // $scheduleBody = new ScheduleMeeting("Quick Meeting", "123456", date('h:i'), 'PM', date('d-m-Y'), 'Asia/Kolkata');  // You can give
    // $response = $meetHourApiService->scheduleMeeting($loginResponse->access_token, $scheduleBody);
    // var_dump($response);
    // echo $response;
    $test = new ViewMeeting($meeting_id);
    $response = $meetHourApiService->timezone($loginResponse->access_token);
    // var_dump($response);
    // class UpcomingMeeting {
    //     public ?int $limit;
    //     public ?int $page;
    //     public ?int $show_all;
    // }

    $body = new UpcomingMeetings();
    $response2 = $meetHourApiService->upcomingMeetings($token, $body);
    print_r($response2) ;   
};

?>