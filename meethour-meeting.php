<?php

require(WP_PLUGIN_DIR . '/meethour/vendor/autoload.php');
require(WP_PLUGIN_DIR . '/meethour/vendor/meethour/php-sdk/src/autoload.php');


use MeetHourApp\Services\MHApiService;
use MeetHourApp\Types\UpcomingMeetings;



function meethour_register_meeting_post_type()
{
    register_post_type('mh_meetings', [
        'labels' => [
            'name' => 'MeetHour Meeting',
            'singular_name' => 'MeetHour Meeting',
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor'],
        'show_in_menu' => 'meethour-settings',
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
            $start_time = get_post_meta($post_id, 'start_time', true);
            echo esc_html(date('M d, Y h:i A', strtotime($start_time)));
            break;
        case 'duration':
            echo esc_html(get_post_meta($post_id, 'duration', true) . 'm');
            break;
        case 'agenda':
            echo esc_html(get_post_meta($post_id, 'agenda', true));
            break;
        case 'shortcode':
            echo '<code>[meethour id="' . esc_attr(get_post_meta($post_id, 'meeting_id', true)) . '"]</code>';
            break;
        case 'meeting_link':
            echo '<a href="' . get_permalink($post_id) . '" target="_blank">Join Meeting</a>';
            break;
        case 'meethour_link':
            $meeting_link = get_post_meta($post_id, 'join_url', true);
            echo '<a href="' . esc_url($meeting_link) . '" target="_blank">Join Meeting 🔗</a>';
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

    if (empty($access_token)) {
        return new WP_Error('no_token', 'Access token not found');
    }

    $body = new UpcomingMeetings();
    $body->limit = 10;
    $body->show_all = 1;
    $response = $meetHourApiService->upcomingMeetings($access_token, $body);
    if (is_wp_error($response)) {
        return $response;
    }

    $meetings = json_encode($response->meetings);
    // echo $meetings;
    $meeting = json_decode($meetings, true);
    // echo $meeting;
    print_r(json_decode($meetings, true));

    foreach ($meeting as $meet) {
        $post_id = wp_insert_post([
            'post_title' => $meet->topic,
            'post_content' => '[meethour_meeting id=' . $meet->meeting_id . ']',
            'post_type' => 'mh_meetings',
            'post_status' => 'publish',
            'meta_input' => [
                'meeting_id' => $meet->meeting_id,
                'start_time' => $meet->start_time,
                'duration' => $meet->duration,
                'meeting_link' => 'https://meethour.io/' . $meet->meeting_id,
            ],
        ]);
        error_log('Inserted post ID: ' . $post_id);
    }
    wp_send_json_success('Meetings have been fetched from Meethour.');
    return $meetings;
}

add_action('wp_ajax_meethour_fetch_upcoming_meetings', 'meethour_fetch_upcoming_meetings');
add_action('wp_ajax_nopriv_meethour_fetch_upcoming_meetings', 'meethour_fetch_upcoming_meetings');

function meethour_meetings_page()
{
    $meetings_query = new WP_Query([
        'post_type' => 'mh_meetings',
        'posts_per_page' => -1,
    ]);

    $meetings = $meetings_query->posts;
?>
    <div class="wrap">
        <h1>MeetHour Upcoming Meetings List</h1>
        <form method="post">
            <input type="submit" name="fetch_meetings" value="Fetch Upcoming Meetings" />
        </form>

        <?php if (empty($meetings)): ?>
            <div class="notice notice-error">
                <p>No meetings found.</p>
            </div>
            <?php print_r($meetings); ?>
        <?php else: ?>
            <div class="card">
                <table class="widefat striped" style="width: 1000px;">
                    <thead>
                        <tr>
                            <th>Meeting Name</th>
                            <th>Meeting ID</th>
                            <th>Date & Time</th>
                            <th>Duration</th>
                            <th>Agenda</th>
                            <th>Shortcode</th>
                            <th>Meeting Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($meetings as $meeting): ?>
                            <tr>
                                <td><?php echo esc_html($meeting->post_title); ?></td>
                                <td><?php echo esc_html(get_post_meta($meeting->ID, 'meeting_id', true)); ?></td>
                                <td>
                                    <?php
                                    echo esc_html(date('M d, Y h:i A', strtotime(get_post_meta($meeting->ID, 'start_time', true))));
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo esc_html(get_post_meta($meeting->ID, 'duration', true) . 'm');
                                    ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo esc_attr(strtolower($meeting->post_content)); ?>">
                                        <?php echo esc_html($meeting->post_content); ?>
                                    </span>
                                </td>
                                <td>
                                    <code>[meethour id="<?php echo esc_attr(get_post_meta($meeting->ID, 'meeting_id', true)); ?>"]</code>
                                    <button class="button button-small copy-shortcode"
                                        data-shortcode='[meethour id="<?php echo esc_attr(get_post_meta($meeting->ID, 'meeting_id', true)); ?>"]'>
                                        Copy
                                    </button>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url(get_post_meta($meeting->ID, 'meeting_link', true)); ?>" target="_blank">
                                        Join Meeting
                                    </a>
                                    <button class="button button-small copy-shortcode"
                                        data-shortcode='<?php echo esc_url(get_post_meta($meeting->ID, 'meeting_link', true)); ?>'>
                                        Copy
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <script>
                jQuery(document).ready(function($) {
                    $('.copy-shortcode').click(function() {
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

            <style>
                .status-badge {
                    padding: 5px 10px;
                    border-radius: 12px;
                    font-size: 12px;
                    font-weight: 500;
                }

                .status-active {
                    background-color: #e6f3e6;
                    color: #1e7e34;
                }

                .status-scheduled {
                    background-color: #e6f3ff;
                    color: #0056b3;
                }

                .status-completed {
                    background-color: #e9ecef;
                    color: #495057;
                }

                .copy-shortcode {
                    margin-left: 5px !important;
                }
            </style>
        <?php endif; ?>
    </div>
<?php
}
