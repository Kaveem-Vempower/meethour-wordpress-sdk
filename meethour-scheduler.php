<?php
require(WP_PLUGIN_DIR . '/meethour/vendor/autoload.php');
require(WP_PLUGIN_DIR . '/meethour/vendor/meethour/php-sdk/src/autoload.php');

use MeetHourApp\Services\MHApiService;
use MeetHourApp\Types\ScheduleMeeting;

function fetch_timeZone()
{
    $access_token = get_option('meethour_access_token', '');
    $meetHourApiService = new MHApiService();
    $response = $meetHourApiService->timezone($access_token);
    return $response->timezones;
};


add_action('add_meta_boxes', 'meethour_add_meeting_details_meta_box');

function meethour_add_meeting_details_meta_box()
{
    add_meta_box(
        'meeting_details_meta_box', // Unique ID
        'Meeting Details', // Title of the meta box
        'meethour_render_meeting_details_meta_box', // Callback function to render the content
        'mh_meetings', // Post type to attach the meta box to
        'normal', // Context (normal, side, advanced)
        'high' // Priority (high, core, default, low)
    );
}
add_action('manage_mh_meetings_posts_custom_column', 'meethour_custom_column_content', 10, 2);

function meethour_render_meeting_details_meta_box($post)
{
    $wordpress_user_data = wordpress_fetch_users();
    $timeZones = fetch_timeZone();
    // Retrieve existing meta data
    $meeting_date = get_post_meta($post->ID, 'meeting_date', true);
    $meeting_time = get_post_meta($post->ID, 'meeting_time', true);
    // ... other meta fields

    // Nonce for security
    wp_nonce_field('meethour_save_meeting_details', 'meethour_meeting_details_nonce');

    // Output the form fields
?>
    <table style="display: flex;" class="form-table">
        <tr>
            <th><label for="meeting_name">Meeting Name</label></th>
            <td><input autocomplete="off" type="text" value="" id="meeting_name" name="meeting_name" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="meeting_passcode">Meeting Passcode</label></th>
            <th><input autocomplete="new-password" type="password" value="" id="meeting_passcode" name="meeting_passcode" class="regular-text"></th>
        </tr>
        <tr>
            <th><label for="meeting_description">Meeting Description</label></th>
            <th><input type="text" id="meeting_description" name="meeting_description" class="regular-text"></th>
        </tr>
        <tr>
            <th><label for="meeting_date">Meeting Date</label></th>
            <td><input type="date" id="meeting_date" name="meeting_date" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="meeting_time">Meeting Time</label></th>
            <td><input type="time" id="meeting_time" name="meeting_time" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="duration_hr">Duration (H:MM)</label></th>
            <td><input type="number" id="duration_hr" name="duration_hr" value="1" min="0" max="24" class="small-text">
                <input type="number" id="duration_min" name="duration_min" value="30" min="0" max="59" class="small-text">
            </td>
        </tr>
        <tr>
            <th><label for="timezone">Timezone</label></th>
            <td>
                <select style="width: 90%;" id="timezone" name="timezone">
                    <?php foreach ($timeZones as $timezone) {
                    ?>
                        <option value="<?php echo $timezone->value ?>"><?php echo $timezone->name ?></option>
                    <?php
                    } ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Select Attendes</th>
            <td>
                <select name="attendes[]" id="attendes" class="select2-select req" style="width: 90%;" onChange="getSelectedAttendes(this)" type="multiselect" multiple>
                    <?php foreach ($wordpress_user_data as $user) { ?>
                        <option value='<?php echo htmlspecialchars(json_encode(array("firstName" => $user->metadata["first_name"], "lastName" => $user->metadata["last_name"], "email" => $user->user_email)), ENT_QUOTES); ?>'>
                            <?php echo htmlspecialchars($user->user_email); ?>
                        </option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Pick Moderator</th>
            <td id="Moderator">
            </td>
        </tr>
        <tr>
            <th><label>Instructions</label></th>
            <td>
                <textarea style="width: 90%;" name="comment" placeholder="Type your Instructions" minlength="10" rows="4"></textarea>
            </td>
        </tr>
    </table>
    <table style="display: flex;" class="form-table">
        <tr>
            <th><label for="recurring_meetings">Recurring Meeting</label></th>
            <td>
                <input id="id_AUTO_START_LIVESTREAMING" type="checkbox" name="options[]" value="AUTO_START_LIVESTREAMING" onclick="checklivestreamsettings();">
                <label for="id_AUTO_START_LIVESTREAMING">Auto Start Live Streaming + Recording</label><br />
                <input type="checkbox" name="options[]" value="ENABLE_EMBEED_MEETING" id="vertical-checkbox-enable-embed-meeting" checked="">
                <label for="vertical-checkbox-enable-embed-meeting">Enable Embed Meeting</label><br />
                <input type="checkbox" name="options[]" value="DONOR_BOX" id="vertical-checkbox-donorbox" checked="">
                <label for="vertical-checkbox-donorbox">Donorbox visibilty</label><br />
                <input type="checkbox" name="options[]" value="CP_CONNECT" id="vertical-checkbox-click-pledge" checked="">
                <label for="vertical-checkbox-click-pledge">Click&amp;Pledge Connect</label><br />
                <input type="checkbox" name="options[]" value="DISABLE_SCREEN_SHARING_FOR_GUEST" id="vertical-checkbox-disable-screen-sharing-guest">
                <label for="vertical-checkbox-disable-screen-sharing-guest">Disable Screen Sharing for Guest</label><br />
                <input type="checkbox" name="options[]" value="DISABLE_JOIN_LEAVE_NOTIFICATIONS" id="vertical-checkbox-disable-toast-for-participant-entry-and-exit">
                <label for="vertical-checkbox-disable-toast-for-participant-entry-and-exit">Disable Entry/Exit Toast Notifications</label>
            </td>
        </tr>
        <tr>
            <th><label for="sound_controls">Sound Controls</label></th>
            <td>

                <input type="checkbox" name="options[]" value="PARTICIPANT_JOINED_SOUND_ID" class="input border mr-2" id="vertical-checkbox-participant-joined-sound-id">
                <label for="vertical-checkbox-participant-joined-sound-id">Turn off Participant Entry Sound</label><br />
                <input type="checkbox" name="options[]" value="PARTICIPANT_LEFT_SOUND_ID" class="input border mr-2" id="vertical-checkbox-participant-left-sound-id">
                <label for="vertical-checkbox-participant-left-sound-id">Turn off Participant Exit Sound</label><br />
                <input type="checkbox" name="options[]" value="INCOMING_USER_REQ_SOUND_ID" class="input border mr-2" id="vertical-checkbox-incoming-user-req-sound-id">
                <label for="vertical-checkbox-incoming-user-req-sound-id">Turn off Lobby Sound for Moderator</label><br />
                <input type="checkbox" name="options[]" value="USER_WAITING_REGISTER" class="input border mr-2" id="vertical-checkbox-user-waiting-register">
                <label for="vertical-checkbox-user-waiting-register">Turn off Background Music in Waiting Room</label><br />
            </td>
        </tr>
        <tr>
            <th><label for="recording_storage">Set Recording Storage</label></th>
            <td>
                <select name="recording_storage" id="recording_storage" style="width: 90%;">
                    <option value="Dropbox">DropBox</option>
                    <option value="Local">Meet Hour</option>
                    <option value="Custom">Custom (S3)</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label>Enable Webinar Mode</label></th>
            <td>
                <input id="enable_pre_registration" type="checkbox" name="enable_pre_registration" value="true" onchange="check_pre_registration_feature();">
            </td>
        </tr>
        <tr>
            <th>General Options </th>
            <td>
                <input type="checkbox" name="options[]" value="ALLOW_GUEST" id="vertical-checkbox-guest-user" checked="">
                <label for="vertical-checkbox-guest-user">Guest user can join meeting</label>
                <br />
                <input type="checkbox" name="options[]" value="JOIN_ANYTIME" id="vertical-checkbox-allow-anytime" checked="true">
                <label for="vertical-checkbox-allow-anytime">Allow participants to join anytime</label>
                <br />
                <input type="checkbox" name="options[]" value="JOIN_ANYTIME" id="vertical-checkbox-allow-anytime" checked="true">
                <label for="vertical-checkbox-allow-anytime">Allow participants to join anytime</label>
                <br />
                <input type="checkbox" name="options[]" value="ENABLE_LOBBY" id="vertical-checkbox-enable-lobby" checked="">
                <label for="vertical-checkbox-enable-lobby">Enable Lobby</label>
                <br />
                <input type="checkbox" name="options[]" value="LIVEPAD" id="vertical-checkbox-live-pad" checked="">
                <label for="vertical-checkbox-live-pad">LivePad</label>
                <br />
                <input type="checkbox" name="options[]" value="WHITE_BOARD" id="vertical-checkbox-WhiteBoard" checked="">
                <label for="vertical-checkbox-WhiteBoard">WhiteBoard</label>
            </td>
        </tr>
        <tr>
        </tr>
    </table>
    <style>
        .dcard {
            width: 100%;
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            margin-top: 20px;
            padding: 20px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
            display: flex;
            flex-direction: row;
            gap: 10%;
        }

        @media (min-width:480px) {
            .dcard {
                width: 100%;
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 4px;
                margin-top: 20px;
                padding: 20px;
                box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
                display: flex;
                flex-direction: row;
                gap: 10%;
            }
        }

        .form-table {
            margin: 0px;
            padding: 0px;
        }

        /* .form-table  tr{
        margin: 0px;
        padding: 10px;
    } */
        .form-table tr td {
            margin: 0px;
            padding: 5px;
        }

        .form-table tr th {
            margin: 0px;
            padding: 5px;
        }

        .scard {
            width: 95%;
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            margin-top: 20px;
            padding: 20px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
            justify-content: center;
            align-items: center;
            text-align: center;
        }
    </style>
    <script>
        function getSelectedAttendes(sel) {
            attendes = document.getElementById('attendes')
            mod_element = document.getElementById('Moderator')
            var opts = [],
                opt;
            var len = sel.options.length;
            for (var i = 0; i < len; i++) {
                opt = sel.options[i];
                if (opt.selected) {
                    opts.push(opt.value);
                    console.log(opt.value)
                }
            }
            if (opts.length > 0) {
                mod_element.innerHTML = "";
                opts.forEach(opt => {
                    const options = document.createElement("span");
                    const br = document.createElement("br")
                    Jsonopt = JSON.parse(opt)
                    options.innerHTML = `
            <input type="checkbox" name="hosts[]" value='${opt}' id="vertical-checkbox-guest-user" >
            <label for="vertical-checkbox-guest-user">${Jsonopt.email}</label>
            `;
                    mod_element.appendChild(options);
                    mod_element.appendChild(br);
                    mod_element.appendChild(br);
                });
            }
            return opts;
        }
    </script>
    <?php // ... other form fields for passcode, timezone, attendees, etc. 
    ?>
<?php
}

add_action('save_post_mh_meetings', 'meethour_save_meeting_details');
$main_id;
function meethour_save_meeting_details($post_id)
{
    $meetHourApiService = new MHApiService();
    $access_token = get_option('meethour_access_token', '');
    if (isset($_POST['meethour_meeting_details_nonce']) && wp_verify_nonce($_POST['meethour_meeting_details_nonce'], 'meethour_save_meeting_details')) {
        update_post_meta($post_id, 'meeting_name', sanitize_text_field($_POST['meeting_name']));
        update_post_meta($post_id, 'meeting_description', sanitize_text_field($_POST['meeting_description']));
        update_post_meta($post_id, 'meeting_passcode', sanitize_text_field($_POST['meeting_passcode']));
        update_post_meta($post_id, 'duration_hr', sanitize_text_field($_POST['duration_hr']));
        update_post_meta($post_id, 'duration_min', sanitize_text_field($_POST['duration_min']));
        update_post_meta($post_id, 'timezone', sanitize_text_field($_POST['timezone']));
        update_post_meta($post_id, 'attendes', sanitize_text_field($_POST['attendes']));
        update_post_meta($post_id, 'hosts', sanitize_text_field($_POST['hosts']));
        update_post_meta($post_id, 'recording_storage', sanitize_text_field($_POST['recording_storage']));
        update_post_meta($post_id, 'instructions', sanitize_text_field($_POST['instructions']));
        update_post_meta($post_id, 'options', sanitize_text_field($_POST['options']));
        // define('MAINID', $post_id);
        // $my_post = get_post($id);
        // $content = "This is an Updated post 2 by Kaveem";
        // $my_post->post_content = $content;
        // wp_update_post($my_post);
        if (isset($_POST['attendes'])) {
            $attendes = array_map(function ($item) {
                return json_decode(stripslashes($item), true);
            }, $_POST['attendes']);
            $jsonAttendes = json_encode($attendes);
        };
        $mainAttendes = json_decode($jsonAttendes, true);
        if (isset($_POST['hosts'])) {
            $hostUsers = array_map(function ($item) {
                return json_decode(stripslashes($item), true);
            }, $_POST['hosts']);
            $jsonHostUsers = json_encode($hostUsers);
        }
        $meetingName = sanitize_text_field($_POST['meeting_name'] ?? '');
        $meeting_agenda = sanitize_text_field($_POST['meeting_description'] ?? '');
        $passcode = sanitize_text_field($_POST['meeting_passcode'] ?? '');
        $meetingDate = sanitize_text_field($_POST['meeting_date'] ?? '');
        $meetingTime = sanitize_text_field($_POST['meeting_time'] ?? '');
        $meetingMeridiem = date('A', strtotime($meetingTime));
        $timezone = sanitize_text_field($_POST['timezone'] ?? '');
        $duration_hr = absint($_POST['duration_hr'] ?? 1);
        $duration_min = absint($_POST['duration_min'] ?? 30);
        $options = $_POST['options'];
        if (isset($_POST['attendes'])) {
            $attendes = array_map(function ($item) {
                return json_decode(stripslashes($item), true);
            }, $_POST['attendes']);
            $jsonAttendes = json_encode($attendes);
        };
        $mainAttendes = json_decode($jsonAttendes, true);
        if (isset($_POST['hosts'])) {
            $hostUsers = array_map(function ($item) {
                return json_decode(stripslashes($item), true);
            }, $_POST['hosts']);
            $jsonHostUsers = json_encode($hostUsers);
        }
        $mainHostUsers = json_decode($jsonHostUsers, true);
        $default_recording_storage = ($_POST['recording_storage']);
        $instructions = ($_POST['comment']);
        // Schedule the meeting with MeetHour API
        $scheduleBody = new ScheduleMeeting($meetingName, $passcode, $meetingTime, $meetingMeridiem, $meetingDate, $timezone);
        $scheduleBody->attend = $mainAttendes;
        $scheduleBody->hostusers = $mainHostUsers;
        $scheduleBody->options = $options;
        $scheduleBody->is_show_portal = 1;
        $scheduleBody->default_recording_storage = $default_recording_storage;
        $scheduleBody->agenda = $meeting_agenda;
        $scheduleBody->duration_hr = $duration_hr;
        $scheduleBody->duration_min = $duration_min;
        $scheduleBody->instructions = $instructions;
        $response = $meetHourApiService->scheduleMeeting(
            $access_token,
            $scheduleBody
        );

        if (!is_wp_error($response) && isset($response->data->meeting_id)) {
            update_post_meta($post_id, 'meeting_id', $response->data->meeting_id);
            update_post_meta($post_id, 'join_url', $response->data->joinURL);
            // $updated_post = array(
            //     'ID'           => $post_id,  // Use the $post_id argument
            //     'post_content' => 'Your desired updated content here',
            // );
            // wp_update_post($updated_post);
        } else {
        }
    }
}



function meethour_meeting_shortcode($atts)
{
    $atts = shortcode_atts(['id' => null], $atts, 'meethour_meeting');

    $id = esc_url($atts['id']);
    if ($id) {
        return '<iframe allow="camera; microphone; display-capture; autoplay; clipboard-write" src="https://meethour.io/' . $id . '#interfaceConfig.applyMeetingSettings=true&interfaceConfig.disablePrejoinHeader=true&interfaceConfig.ENABLE_DESKTOP_DEEPLINK=false&interfaceConfig.disablePrejoinFooter=true&interfaceConfig.SHOW_MEET_HOUR_WATERMARK=false&interfaceConfig.HIDE_DEEP_LINKING_LOGO=true&interfaceConfig.MOBILE_APP_PROMO=false&interfaceConfig.ENABLE_MOBILE_BROWSER=true&appData.localStorageContent=null" name="mhConferenceFrame0" id="mhConferenceFrame0" allowfullscreen="true" style="height: 100%; width: 100rem; border: 0px; justify-content: center; align-items: center; "></iframe>';
    } else {
        return "Meeting ID required.";
    }
}
add_shortcode('meethour_meeting', 'meethour_meeting_shortcode');


function custom_js_to_head()
{
?>
    <script>
        jQuery(function() {
            jQuery("body.post-type-mh_meetings .wrap h1").append('<a href="#" id="sync-meetings" class="page-title-action">Sync Meetings from Meethour</a>');

            jQuery("#sync-meetings").on("click", function(event) {
                event.preventDefault();

                jQuery.ajax({
                    url: ajaxurl, // WordPress built-in AJAX URL
                    type: "POST",
                    data: {
                        action: 'meethour_fetch_upcoming_meetings'
                    },
                    success: function(response) {
                        alert('Meetings synced successfully!');
                    },
                    error: function(xhr, status, error) {
                        alert('Failed to sync meetings: ' + error);
                    }
                });
            });
        });

        jQuery(function() {
            jQuery("body.post-type-mh_recordings .wrap h1").append('<a href="#" id="sync-meetings" class="page-title-action">Fetch Recordings</a>');

            jQuery("#sync-meetings").on("click", function(event) {
                event.preventDefault();

                jQuery.ajax({
                    url: ajaxurl, // WordPress built-in AJAX URL
                    type: "POST",
                    data: {
                        action: 'meethour_fetch_recordings'
                    },
                    success: function(response) {
                        alert('Meetings synced successfully!');
                    },
                    error: function(xhr, status, error) {
                        alert('Failed to sync meetings: ' + error);
                    }
                });
            });
        });
    </script>

<?php
}
add_action('admin_head', 'custom_js_to_head');

function meethour_update_post_content($post_id)
{
    remove_action('save_post_mh_meetings', 'meethour_update_post_content', 11); // Remove action
    $content = get_post_meta($post_id, 'meeting_id', true);
    $updated_post = array(
        'ID'           => $post_id,
        'post_content' => '[meethour_meeting id="' . $content . '"]',
    );
    wp_update_post($updated_post);

    add_action('save_post_mh_meetings', 'meethour_update_post_content', 11); // Add back the action
}
add_action('save_post_mh_meetings', 'meethour_update_post_content', 11);


function add_custom_post_type_template($single_template)
{
    global $post;

    if ($post->post_type == 'mh_meetings') {
        $single_template = dirname(__FILE__) . '/single-mh_meetings.php';
    }
    return $single_template;
}

add_filter('single_template', 'add_custom_post_type_template');

?>