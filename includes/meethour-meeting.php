<?php

require(WP_PLUGIN_DIR . '/meethour/vendor/autoload.php');
require(WP_PLUGIN_DIR . '/meethour/vendor/meethour/php-sdk/src/autoload.php');


use MeetHourApp\Services\MHApiService;
use MeetHourApp\Types\UpcomingMeetings;
use MeetHourApp\Types\ArchiveMeeting;
use MeetHourApp\Types\DeleteMeeting;
use MeetHourApp\Types\DeleteRecording;

function meethour_register_meeting_post_type()
{
    register_post_type('mh_meetings', [
        'labels' => [
            'name' => 'Meetings',
            'singular_name' => 'Meetings',
            'add_new_item' => __('Schedule Meeting'),
            'add_new' => __('Schedule Meetings'),
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor'],
        'show_in_menu' => false,
        'position'

    ]);
}
add_action('init', 'meethour_register_meeting_post_type');
add_filter('manage_mh_meetings_posts_columns', 'meethour_add_custom_columns');
function meethour_add_custom_columns($columns)
{
    $columns = [
        'cb' => '<input type="checkbox" />',
        'title' => 'Meeting Name',
        'meeting_id' => 'Meeting ID',
        'date_time' => 'Date & Time',
        'duration' => 'Duration',
        'agenda' => 'Agenda',
        'shortcode' => 'Shortcode',
        'meeting_link' => 'Meeting Link',
        'meethour_link' => 'External Link',
    ];
    return $columns;
}
function meethour_custom_column_content($column, $post_id)
{

    switch ($column) {
        case 'meeting_id':
            echo esc_html(get_post_meta($post_id, 'meeting_id', true));
            break;
        case 'date_time':
            $start_time = get_post_meta($post_id, 'meeting_date', true) . get_post_meta($post_id, 'meeting_time', true);
            echo esc_html(date('M d, Y h:i A', strtotime($start_time)));
            break;
        case 'duration':
            echo esc_html(get_post_meta($post_id, 'duration_hr', true) . 'h' . " " . get_post_meta($post_id, 'duration_min', true) . 'm');
            break;
        case 'agenda':
            $post = get_post($post_id);
            echo esc_html($post->post_content);
            break;
        case 'shortcode':
            echo '<button class="button button-small copy-shortcode" data-shortcode="[meethour meeting_id=' . get_post_meta($post_id, 'meeting_id', true) . ']">Copy Shortcode</button>';
            echo '<script>
            jQuery(document).ready(function($) {
                $(".copy-shortcode").click(function() {
                    var shortcode = $(this).data("shortcode");
                    navigator.clipboard.writeText(shortcode).then(function() {
                        var button = $(this);
                        button.text("Copied!");
                        setTimeout(function() {
                            button.text("Copy Shortcode");
                        }, 2000);
                    }.bind(this));
                });
            });
            </script>';
            break;
        case 'meeting_link':
            echo '<a href="' . get_permalink($post_id) . '" target="_blank">Join Meeting</a>';
            break;
        case 'meethour_link':
            $meeting_link = get_post_meta($post_id, 'join_url', true);
            if (empty($meeting_link)) {
                echo '<a href="' . get_permalink($post_id) . '" target="_blank">Join Meeting</a>';
            } else {
                echo '<a href="' . ($meeting_link) . '" target="_blank">Join Meeting 🔗</a>';
            }
            break;
    }
}
add_filter('manage_edit-mh_meetings_sortable_columns', 'meethour_sortable_columns');
function meethour_sortable_columns($columns)
{
    $columns['meeting_id'] = 'meeting_id';
    $columns['date_time'] = 'date_time';
    $columns['duration'] = 'duration';
    return $columns;
}
add_action('pre_get_posts', 'meethour_sortable_columns_orderby');
function meethour_sortable_columns_orderby($query)
{
    if (!is_admin()) {
        return;
    }
    $orderby = $query->get('orderby');

    if ('meeting_id' === $orderby) {
        $query->set('meta_key', 'meeting_id');
        $query->set('orderby', 'meta_value');
    } elseif ('date_time' === $orderby) {
        $query->set('meta_key', 'start_time');
        $query->set('orderby', 'meta_value');
    } elseif ('duration' === $orderby) {
        $query->set('meta_key', 'duration');
        $query->set('orderby', 'meta_value_num');
    }
}

function meethour_fetch_upcoming_meetings()
{

    $meetHourApiService = new MHApiService();
    $access_token = get_option('meethour_access_token', '');


    $current_page = get_option('mh_meetings_current_page', 1);
    $total_pages = get_option('mh_meetings_total_pages', null);

    if (empty($current_page)) {
        $current_page = 1;
        update_option('mh_meetings_current_page', $current_page);
    };

    $posts_per_page = 20;
    $start = ($current_page - 1) * $posts_per_page + 1;
    $end = $current_page * $posts_per_page;
    $post_limit = "{$start}-{$end}";
    update_option('mh_meetings_post_limit', $post_limit);

    $body = new UpcomingMeetings();
    $body->limit = $posts_per_page;
    $body->page = $current_page;
    $response = $meetHourApiService->upcomingMeetings($access_token, $body);

    if ($response->success == false) {
        set_transient('meethour_error_message', $response->message, 30); // store the error message for 30 seconds
        return;
    }

    if (is_null($total_pages)) {
        $total_pages = $response->total_pages;
        update_option('mh_meetings_total_pages', $total_pages);
    }
    $meetings_array = json_decode(json_encode($response->meetings), true);
    foreach ($meetings_array as $meet) {
        $existing_posts = get_posts([
            'post_type'   => 'mh_meetings',
            'meta_key'    => 'meeting_id',
            'meta_value'  => $meet['meeting_id'],
            'numberposts' => 1,
        ]);

        if (empty($existing_posts)) {
            wp_insert_post([
                'post_title'   => $meet['topic'],
                'post_content' => $meet['agenda'],
                'post_type'    => 'mh_meetings',
                'post_status'  => 'publish',
                'meta_input'   => [
                    'id'         => $meet['id'],
                    'meeting_id' => $meet['meeting_id'],
                    'meeting_date'       => explode(" ", $meet['start_time'])[0],
                    'meeting_time'       => explode(" ", $meet['start_time'])[1],
                    'duration_hr'   => explode(":", $meet['duration'])[0],
                    'duration_min'  => explode(":", $meet['duration'])[1],
                    'meeting_name'      => $meet['topic'],
                    'meeting_agenda'     => $meet['agenda'],
                    'timezone'   => $meet['timezone'],
                    'join_url'   => $meet['joinURL'],
                    'meeting_passcode'   => $meet['passcode'],
                    'options'    => $meet['settings'],
                    'instructions' => $meet['instructions'],
                    'recording_storage' => $meet['recording_storage'],
                ],
            ]);
        }
    }

    if ($current_page < $total_pages) {
        $current_page++;
        update_option('mh_meetings_current_page', $current_page);
    } else {
        delete_option('mh_meetings_current_page');
        delete_option('mh_meetings_total_pages');
    }

    return $response;
}

add_action('wp_ajax_meethour_fetch_upcoming_meetings', 'meethour_fetch_upcoming_meetings');
add_action('wp_ajax_nopriv_meethour_fetch_upcoming_meetings', 'meethour_fetch_upcoming_meetings');


add_action('wp_trash_post', 'Archive_Meethour_Post', 1, 1);
function Archive_Meethour_Post($post_id)
{
    $currentPageUrl = 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $parsed_url = parse_url($currentPageUrl);
    $query = $parsed_url['query'];
    parse_str($query, $params);
    $meeting_archive = $params['meeting_trash'];
    if ($meeting_archive == 'yes') {
        $meetHourApiService = new MHApiService();
        $post = get_post($post_id);
        $post_type = $post->post_type;
        $token = get_option('meethour_access_token', '');
        if ($post_type == 'mh_meetings') {
            $meeting_id = get_post_meta($post_id, 'meeting_id', true);
            $body = new ArchiveMeeting($meeting_id);
            $response = $meetHourApiService->archiveMeeting($token, $body);
            if ($response->success == false) {
                set_transient('meethour_error_message', $response->message, 30);
                return;
            }
        }
    }
}

// add action
add_action('before_delete_post', 'Delete_Meethour_Post');
function Delete_Meethour_Post($post_id)
{

    $currentPageUrl = 'https://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    $parsed_url = parse_url($currentPageUrl);
    $query = $parsed_url['query'];
    parse_str($query, $params);
    $meeting_delete = $params['meeting_delete'];
    $recording_delete = $params['recording_delete'];

    $meetHourApiService = new MHApiService();
    $post = get_post($post_id);
    $post_type = $post->post_type;
    $token = get_option('meethour_access_token', '');
    $meeting_id = get_post_meta($post_id, 'meeting_id', true);

    if ($meeting_delete == 'yes') {
        if ($post_type == 'mh_meetings') {
            echo '<script>alert("This is mh_meeting Post Type");</script>';
            $body = new DeleteMeeting($meeting_id);
            $meeting_response = $meetHourApiService->deleteMeeting($token, $body);
            if ($meeting_response->success == false) {
                set_transient('meethour_error_message', $meeting_response->message, 30);
            }
        }
    }
    if ($recording_delete == 'yes') {
        if ($post_type == 'mh_recordings') {
            $recording_id = get_post_meta($post_id, 'recording_id', true);
            $main = new DeleteRecording($recording_id);
            $response = $meetHourApiService->deleteRecording($token, $main);
            set_transient('meethour_error_message', $response->message, 30);
        }
    }
}

function add_delete_confirmation()
{
    global $pagenow,  $typenow;
    if ($pagenow == 'edit.php' && $typenow == 'mh_meetings') {
?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('a.submitdelete, .bulkactions option[value="trash"]').forEach(function(element) {
                    element.addEventListener('click', function(e) {
                        const queryString = element.closest('a').href;
                        const urlParams = new URLSearchParams(queryString);
                        const action = urlParams.get('action');

                        if (action == 'trash') {
                            if (!confirm('Are you sure you want to Trash this Meeting from Meet Hour Portal as well ?')) {
                                e.preventDefault();
                                let url = element.closest('a').href;
                                console.log("This is the Current URL: " + url);
                                url = url + "&meeting_trash=no"
                                window.location.href = url;
                                console.log("This is URL : " + url)
                            } else {
                                e.preventDefault();
                                let url = element.closest('a').href;
                                console.log("This is the Current URL: " + url);
                                url = url + "&meeting_trash=yes"
                                window.location.href = url;
                                console.log("This is URL : " + url);
                            }
                        }

                        if (action == 'delete') {
                            if (!confirm('Are you sure you want to Delete this Meeting from Meet Hour Portal as well ?')) {
                                e.preventDefault();
                                let url = element.closest('a').href;
                                url = url + "&meeting_delete=no"
                                window.location.href = url;
                            } else {
                                e.preventDefault();
                                let url = element.closest('a').href;
                                url = url + "&meeting_delete=yes"
                                window.location.href = url;
                            }
                        };
                    });
                });
            });
        </script>
    <?php
    }
    if ($pagenow == 'edit.php' && $typenow == 'mh_recordings') {
    ?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('a.submitdelete, .bulkactions option[value="trash"]').forEach(function(element) {
                    element.addEventListener('click', function(e) {
                        if (!confirm('Are you sure you want to Delete this Meeting from Meet Hour Portal as well ?')) {
                            e.preventDefault();
                            let url = element.closest('a').href;
                            url = url + "&recording_delete=no"
                            window.location.href = url;
                        } else {
                            e.preventDefault();
                            let url = element.closest('a').href;
                            url = url + "&recording_delete=yes"
                            window.location.href = url;
                        };
                    });
                });
            });
        </script>
    <?php
    }
}
add_action('admin_footer', 'add_delete_confirmation');


function meethour_display_error_message_meeting()
{
    $error_message = get_transient('meethour_error_message');
    if ($error_message) {
    ?>
        <div class="notice notice-error">
            <p><?php echo esc_html($error_message); ?></p>
        </div>
<?php
        delete_transient('meethour_error_message');
    }
}
add_action('admin_notices', 'meethour_display_error_message_meeting');
