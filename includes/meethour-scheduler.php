<?php
require(WP_PLUGIN_DIR . '/meethour/vendor/autoload.php');
require(WP_PLUGIN_DIR . '/meethour/vendor/meethour/php-sdk/src/autoload.php');

use MeetHourApp\Services\MHApiService;
use MeetHourApp\Types\ScheduleMeeting;
use MeetHourApp\Types\EditMeeting;

function fetch_timeZone()
{
    $access_token = get_option('meethour_access_token', '');
    $meetHourApiService = new MHApiService();
    $response = $meetHourApiService->timezone($access_token);
    if ($response->success == false) {
        set_transient('meethour_error_message', $response->message, 30); // store the error message for 30 seconds
    }
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
    $post_id = $post->ID;
    $wordpress_user_data = wordpress_fetch_users();
    $timeZones = fetch_timeZone();
    $attendes_response = json_decode(get_post_meta($post_id, 'attendes', true));
    if (!empty($attendes_response)) {
        $selected_attendes_emails = array_column($attendes_response, 'email');
    }
    wp_nonce_field('meethour_save_meeting_details', 'meethour_meeting_details_nonce');
?>
    <table style="display: flex;" class="form-table">
        <tr>
            <th><label for="meeting_name">Meeting Name</label></th>
            <td><input autocomplete="off" type="text" value="<?php echo $post->post_title ?>" id="meeting_name" name="meeting_name" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="meeting_passcode">Meeting Passcode</label></th>
            <th><input autocomplete="new-password" type="password" value="<?php echo get_post_meta($post_id, 'meeting_passcode', true) ?>" id="meeting_passcode" name="meeting_passcode" class="regular-text"></th>
        </tr>
        <tr>
            <th><label for="meeting_description">Meeting Description</label></th>
            <th><input type="text" id="meeting_description" value="<?php echo get_post_meta($post_id, 'meeting_description', true) ?>" name="meeting_description" class="regular-text"></th>
        </tr>
        <tr>
            <th><label for="meeting_date">Meeting Date</label></th>
            <td><input type="date" id="meeting_date" value="<?php echo get_post_meta($post_id, 'meeting_date', true) ?>" name="meeting_date" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="meeting_time">Meeting Time</label></th>
            <td><input type="time" id="meeting_time" value="<?php echo get_post_meta($post_id, 'meeting_time', true) ?>" name="meeting_time" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="duration_hr">Duration (H:MM)</label></th>
            <td><input type="number" id="duration_hr" name="duration_hr" value="<?php echo get_post_meta($post_id, 'duration_hr', true) ?>" min="0" max="24" class="small-text">
                <input type="number" id="duration_min" name="duration_min" value="<?php echo get_post_meta($post_id, 'duration_min', true) ?>" min="0" min="0" max="59" class="small-text">
            </td>
        </tr>
        <tr>
            <th><label for="timezone">Timezone</label></th>
            <td>
                <select style="width: 90%;" value="<?php echo get_post_meta($post_id, 'timezone', true) ?>" id="timezone" name="timezone">
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
                    <?php foreach ($wordpress_user_data as $user) {
                        $firstName = isset($user->usermeta["first_name"]) ? $user->usermeta["first_name"] : '';
                        $lastName = isset($user->usermeta["last_name"]) ? $user->usermeta["last_name"] : '';
                        $email = isset($user->user_email) ? $user->user_email : '';
                        if (!empty($attendes_response)) {
                            $selected = in_array($email, $selected_attendes_emails) ? 'selected' : '';
                        } else {
                            $selected = '';
                        }
                    ?>
                        <option value='<?php echo htmlspecialchars(json_encode(array("firstName" => $firstName, "lastName" => $lastName, "email" => $email)), ENT_QUOTES); ?>' <?php echo $selected ?>>
                            <?php echo htmlspecialchars($email); ?>
                        </option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Pick Moderator</th>
            <td id="Moderator"></td>
        </tr>
        <tr>
            <th><label>Instructions</label></th>
            <td>
                <textarea style="width: 90%;" name="comment" placeholder="Type your Instructions" minlength="10" value="<?php echo get_post_meta($post_id, 'instructions', true) ?>" id="instructions" rows="4"></textarea>
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
            <p style="display: none;" id="hosts"><?php echo get_post_meta($post_id, 'hosts', true); ?></p>
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
        function selectedHosts() {
            var dnd = document.getElementById('attendes');
            const mod_element = document.getElementById('Moderator');
            mod_element.innerHTML = '';
            var selected = dnd.selectedOptions;
            selectedArr = (Array.from(selected).map(option => JSON.parse(option.value)));
            console.log(selectedArr);
            const selectedOptions = Array.from(dnd.selectedOptions);
            var ishost = document.getElementById('hosts')
            var jsonishost = JSON.parse(ishost.innerText);
            console.log(jsonishost);

            selectedOptions.forEach((option, index) => {
                const firstName = option.getAttribute('data-firstname');
                const lastName = option.getAttribute('data-lastname');
                const email = (option.value);
                const text = option.innerText;
                const checkboxId = `moderator-checkbox-${index}`;

                const ishostEmail = jsonishost[index].email;
                console.log(ishostEmail);

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'hosts[]';
                checkbox.value = email;
                checkbox.id = checkboxId;
                if (ishostEmail == text) {
                    checkbox.checked = true;
                } else {
                    checkbox.checked = false;
                }

                const label = document.createElement('label');
                label.htmlFor = checkboxId;
                label.textContent = `${text}`;

                const lineBreak = document.createElement('br');

                mod_element.appendChild(checkbox);
                mod_element.appendChild(label);
                mod_element.appendChild(lineBreak);
            });
        }
        selectedHosts();

        function getSelectedAttendes(sel) {
            var dnd = document.getElementById('attendes');
            const mod_element = document.getElementById('Moderator');
            mod_element.innerHTML = ''; // Clear previous moderators
            var selected = dnd.selectedOptions;
            selectedArr = (Array.from(selected).map(option => JSON.stringify(option.value)));
            console.log("These are Selected Options Value #294: " + selectedArr);
            const selectedOptions = Array.from(dnd.selectedOptions);

            selectedOptions.forEach((option, index) => {
                const firstName = option.getAttribute('data-firstname');
                const lastName = option.getAttribute('data-lastname');
                const email = (option.value);
                const text = option.innerText;


                const checkboxId = `moderator-checkbox-${index}`;

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.name = 'hosts[]';
                checkbox.value = email;
                checkbox.id = checkboxId;

                const label = document.createElement('label');
                label.htmlFor = checkboxId;
                label.textContent = `${text}`;

                const lineBreak = document.createElement('br');

                mod_element.appendChild(checkbox);
                mod_element.appendChild(label);
                mod_element.appendChild(lineBreak);
            });
        }

        function fetchAndUpdateCheckboxes() {
            APIoptions = document.getElementById('api-options');
            var optionsString = APIoptions.innerText;
            var options = JSON.parse(optionsString);
            console.log(options)
            updateCheckboxes(options);
        }

        function updateCheckboxes(options) {
            var checkboxes = document.querySelectorAll('input[name="options[]"]');
            checkboxes.forEach(function(checkbox) {
                var optionName = checkbox.value;
                if (options.hasOwnProperty(optionName)) {
                    checkbox.checked = options[optionName] === 1;
                } else {
                    checkbox.checked = false;
                }
            });
        }

        fetchAndUpdateCheckboxes();
    </script>
<?php
}

add_action('save_post_mh_meetings', 'meethour_save_meeting_details');
function meethour_save_meeting_details($post_id)
{
    $post_meeting_id = get_post_meta($post_id, 'meeting_id', true);
    $meetHourApiService = new MHApiService();
    $access_token = get_option('meethour_access_token', '');
    if (isset($_POST['meethour_meeting_details_nonce']) && wp_verify_nonce($_POST['meethour_meeting_details_nonce'], 'meethour_save_meeting_details')) {
        update_post_meta($post_id, 'meeting_name', sanitize_text_field($_POST['meeting_name']));
        update_post_meta($post_id, 'meeting_description', sanitize_text_field($_POST['meeting_description']));
        update_post_meta($post_id, 'meeting_passcode', sanitize_text_field($_POST['meeting_passcode']));
        update_post_meta($post_id, 'meeting_date', sanitize_text_field($_POST['meeting_date']));
        update_post_meta($post_id, 'meeting_time', sanitize_text_field($_POST['meeting_time']));
        update_post_meta($post_id, 'duration_hr', sanitize_text_field($_POST['duration_hr']));
        update_post_meta($post_id, 'duration_min', sanitize_text_field($_POST['duration_min']));
        update_post_meta($post_id, 'timezone', sanitize_text_field($_POST['timezone']));
        update_post_meta($post_id, 'recording_storage', sanitize_text_field($_POST['recording_storage']));
        update_post_meta($post_id, 'instructions', sanitize_text_field($_POST['instructions']));
        update_post_meta($post_id, 'options', sanitize_text_field($_POST['options']));
        if (isset($_POST['attendes'])) {
            $attendes = array_map(function ($item) {
                return json_decode(stripslashes($item), true);
            }, $_POST['attendes']);
            $jsonAttendes = json_encode($attendes);
        };
        $mainAttendes = json_decode($jsonAttendes);
        if (isset($_POST['hosts'])) {
            $hostUsers = array_map(function ($item) {
                return json_decode(stripslashes($item), true);
            }, $_POST['hosts']);
            $jsonHostUsers = json_encode($hostUsers);
        }
        $mainHostUsers = json_decode($jsonHostUsers, true);
        update_post_meta($post_id, 'attendes', $jsonAttendes);
        update_post_meta($post_id, 'hosts', $jsonHostUsers);
        $meetingName = sanitize_text_field($_POST['meeting_name'] ?? '');
        if (empty($meetingName)) {
            $meetingName = get_the_title($post_id);
        }
        $meeting_agenda = sanitize_text_field($_POST['meeting_description'] ?? '');
        $passcode = sanitize_text_field($_POST['meeting_passcode'] ?? '');
        $meetingDate = sanitize_text_field($_POST['meeting_date'] ?? '');
        $Time = sanitize_text_field($_POST['meeting_time'] ?? '');
        $meetingTime = date("h:i", strtotime($Time));
        $meetingMeridiem = date('a', strtotime("$Time"));
        $timezone = sanitize_text_field($_POST['timezone'] ?? '');
        $duration_hr = absint($_POST['duration_hr'] ?? 1);
        $duration_min = absint($_POST['duration_min'] ?? 30);
        $options = $_POST['options'];
        $default_recording_storage = ($_POST['recording_storage']);
        $instructions = ($_POST['comment']);

        if ($post_meeting_id == NULL) {
            $scheduleBody = new ScheduleMeeting($meetingName, $passcode, $meetingTime, $meetingMeridiem, $meetingDate, $timezone);
            $scheduleBody->attend = $mainAttendes;
            $scheduleBody->hostusers = $mainHostUsers;
            $scheduleBody->options = $options;
            $scheduleBody->is_show_portal = 1;
            $scheduleBody->send_calendar_invite = 1;
            $scheduleBody->default_recording_storage = $default_recording_storage;
            $scheduleBody->agenda = $meeting_agenda;
            $scheduleBody->duration_hr = $duration_hr;
            $scheduleBody->duration_min = $duration_min;
            $scheduleBody->instructions = $instructions;
            $scheduleresponse = $meetHourApiService->scheduleMeeting(
                $access_token,
                $scheduleBody
            );
            $meeting_id = $scheduleresponse->data->meeting_id;
            update_post_meta($post_id, 'meeting_id', $meeting_id);
            update_post_meta($post_id, 'join_url', $scheduleresponse->data->joinURL);
            update_post_meta($post_id, 'attendes', json_encode($scheduleresponse->data->meeting_attendees));

            if ($scheduleresponse->success == false) {
                set_transient('meethour_error_message', $scheduleresponse->message, 30); // store the error message for 30 seconds
            }
        } else {
            //Updated Meeting API
            $updateBody = new EditMeeting($post_meeting_id);
            $updateBody->meeting_time = $meetingTime;
            $updateBody->meeting_meridiem = $meetingMeridiem;
            $updateBody->meeting_date = $meetingDate;
            $updateBody->timezone = $timezone;
            $updateBody->passcode = $passcode;
            $updateBody->meeting_name = $meetingName;
            $updateBody->attend = $mainAttendes;
            $updateBody->hostusers = $mainHostUsers;
            $updateBody->options = $options;
            $updateBody->is_show_portal = 1;
            $updateBody->default_recording_storage = $default_recording_storage;
            $updateBody->agenda = $meeting_agenda;
            $updateBody->duration_hr = $duration_hr;
            $updateBody->duration_min = $duration_min;
            $updateBody->instructions = $instructions;
            $editresponse = $meetHourApiService->editMeeting(
                $access_token,
                $updateBody
            );
            update_post_meta($post_id, 'meeting_id', $editresponse->data->meeting_id);
            update_post_meta($post_id, 'join_url', $editresponse->data->joinURL);
            update_post_meta($post_id, 'attendes', json_encode($editresponse->data->meeting_attendees));
            if ($editresponse->success == false) {
                set_transient('meethour_error_message', $editresponse->message, 30); // store the error message for 30 seconds
            }
        }
    }
    settings_errors('meethour_messages');
}


function custom_js_to_head()
{
    $page_limit_meetings = get_option('mh_meetings_post_limit', '');
    if ($page_limit_meetings == null) {
        $page_limit_meetings = "1-20";
    }
    $page_limit_recordings = get_option('mh_recordings_post_limit', '');
    if ($page_limit_recordings == null) {
        $page_limit_recordings = "1-20";
    }
?>
    <script>
        jQuery(function($) {
            $("body.post-type-mh_meetings .wrap h1").append('<button href="#" id="sync-meetings" style="margin-left:10px" class="page-title-action my-btn">Sync Upcomming Meetings from Meet Hour <strong>( <?php echo $page_limit_meetings ?> )</strong></button>');

            $.fn.buttonLoader = function(action) {
                var self = $(this);
                if (action == 'start') {
                    if ($(self).attr("disabled") == "disabled") {
                        return false;
                    }
                    $('.has-spinner').attr("disabled", true);
                    $(self).attr('data-btn-text', $(self).text());
                    var text = 'Fetching Data...';
                    if ($(self).attr('data-load-text') !== undefined && $(self).attr('data-load-text') !== "") {
                        text = $(self).attr('data-load-text');
                    }
                    $(self).html('<span class="spinner"><i class="fa fa-spinner fa-spin" title="button-loader"></i></span> ' + text);
                    $(self).addClass('active');
                }
                if (action == 'stop') {
                    $(self).html($(self).attr('data-btn-text'));
                    $(self).removeClass('active');
                    $('.has-spinner').attr("disabled", false);
                }
            };

            $("#sync-meetings").on("click", function(event) {
                event.preventDefault();
                $('.my-btn').buttonLoader('start');
                console.log('Sync Meetings button clicked');

                $.ajax({
                    url: my_ajax_object.ajax_url, // Use localized AJAX URL
                    type: "POST",
                    data: {
                        action: 'meethour_fetch_upcoming_meetings'
                    },
                    success: function(response) {
                        alert('Meetings synced successfully!');
                        location.reload();
                        console.log('Fetch upcoming meetings success:', response);
                        $('.my-btn').buttonLoader('stop');
                    },
                    error: function(xhr, status, error) {
                        $('.my-btn').buttonLoader('stop');
                        alert('Failed to sync meetings: ' + error);
                        console.log('Fetch upcoming meetings error:', error, status, xhr);
                    }
                });
            });
            $("body.post-type-mh_recordings .wrap h1").append('<a href="#" id="sync-recordings"  style="margin-left:10px" class="page-title-action my-btn">Fetch Recordings from Meet Hour <strong>( <?php echo $page_limit_recordings ?> )</strong></a>');
            $("#sync-recordings").on("click", function(event) {
                event.preventDefault();
                $('.my-btn').buttonLoader('start');
                console.log('Fetch Recordings button clicked');

                $.ajax({
                    url: my_ajax_object.ajax_url, // Use localized AJAX URL
                    type: "POST",
                    data: {
                        action: 'meethour_fetch_recordings'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Recordings fetched successfully!');
                            location.reload();
                            console.log('Fetch recordings success:', response.data);
                            $('.my-btn').buttonLoader('stop');
                        } else {
                            alert('Failed to fetch recordings: ' + response.data);
                            console.log('Fetch recordings error:', response.data);
                            $('.my-btn').buttonLoader('stop');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Failed to fetch recordings: ' + error);
                        console.log('Fetch recordings error:', error, status, xhr);
                        $('.my-btn').buttonLoader('stop');
                    }
                });
            });
            <?php
            $access_token = get_option('meethour_access_token', '');
            if (!empty($access_token)) {
            ?>
                $("body.toplevel_page_meethour-settings .wrap h1").append('<a href="#" id="reset-plugin" class="page-title-action my-btn">Reset Plugin</a>');
                $("#reset-plugin").on("click", function(event) {
                    event.preventDefault();
                    console.log('Reset Plugin button clicked');

                    $.ajax({
                        url: my_ajax_object.ajax_url, // Use localized AJAX URL
                        type: "POST",
                        data: {
                            action: 'meethour_deactivate'
                        },
                        success: function(response) {
                            if (response.success) {
                                alert('Plugin reset successfully!');
                                location.reload();
                                console.log('Plugin Rest success:', response.data);
                            } else {
                                location.reload();
                                // alert('Failed to Reset Plugin 1: ' + response.data);
                                console.log('Reset Plugin error 1:', response.data);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Failed to Reset Plugin 2: ' + error);
                            console.log('Reset Plugin error 2:', error, status, xhr);
                        }
                    });
                });
            <?php } ?>
        });
    </script>


    <?php
}
add_action('admin_head', 'custom_js_to_head');

// Enqueue your script and localize the AJAX URL
function enqueue_my_script()
{
    wp_enqueue_script('my-custom-script', plugin_dir_url(__FILE__) . 'js/my-custom-script.js', array('jquery'), null, true);
    wp_localize_script('my-custom-script', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'enqueue_my_script');



function add_custom_post_type_template($single_template)
{
    global $post;

    if ($post->post_type == 'mh_meetings') {
        $single_template = dirname(__FILE__) . '/single-mh_meetings.php';
    }
    if ($post->post_type == 'mh_recordings') {
        $single_template = dirname(__FILE__) . '/single-mh_recordings.php';
    }
    return $single_template;
}

add_filter('single_template', 'add_custom_post_type_template');


function meethour_display_error_message()
{
    $error_message = get_transient('meethour_error_message');
    if ($error_message) {
    ?>
        <div class="notice notice-error">
            <p><?php echo esc_html($error_message); ?></p>
        </div>
<?php
        delete_transient('meethour_error_message'); // delete the transient
    }
}
add_action('admin_notices', 'meethour_display_error_message');


add_filter('redirect_post_location', 'Custom_post_Redirection');
function Custom_post_Redirection($location)
{

    if ('mh_meetings' == get_post_type()) {
        if (isset($_POST['save']) || isset($_POST['publish']))
            return admin_url("edit.php?post_type=mh_meetings");
    }
    return $location;
}
