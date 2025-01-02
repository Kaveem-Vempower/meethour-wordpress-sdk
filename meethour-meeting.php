<?php
function meethour_register_meeting_post_type() {
    register_post_type('mh_meetings', [
        'labels' => [
            'name' => 'MeetHour Meetings',
            'singular_name' => 'MeetHour Meeting',
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
    ]);
}
add_action('init', 'meethour_register_meeting_post_type');

add_filter('manage_mh_meetings_posts_columns', 'meethour_add_custom_columns');
function meethour_add_custom_columns($columns) {
    $columns = [
        'cb' => '<input type="checkbox" />',
        'title' => 'Meeting Name',
        'meeting_id' => 'Meeting ID',
        'date_time' => 'Date & Time',
        'duration' => 'Duration',
        'agenda' => 'Agenda',
        'shortcode' => 'Shortcode',
        'meeting_link' => 'Meeting Link',
    ];
    return $columns;
}

add_action('manage_mh_meetings_posts_custom_column', 'meethour_custom_column_content', 10, 2);
function meethour_custom_column_content($column, $post_id) {
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
            echo esc_html(get_post_field('post_content', $post_id));
            break;
        case 'shortcode':
            echo '<code>[meethour id="' . esc_attr(get_post_meta($post_id, 'meeting_id', true)) . '"]</code>';
            break;
        case 'meeting_link':
            $meeting_link = get_post_meta($post_id, 'meeting_link', true);
            echo '<a href="' . esc_url($meeting_link) . '" target="_blank">Join Meeting</a>';
            break;
    }
}

add_filter('manage_edit-mh_meetings_sortable_columns', 'meethour_sortable_columns');
function meethour_sortable_columns($columns) {
    $columns['meeting_id'] = 'meeting_id';
    $columns['date_time'] = 'date_time';
    $columns['duration'] = 'duration';
    return $columns;
}

add_action('pre_get_posts', 'meethour_sortable_columns_orderby');
function meethour_sortable_columns_orderby($query) {
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

add_action('admin_head', 'meethour_custom_columns_css');
function meethour_custom_columns_css() {
    echo '<style>
        .column-meeting_id, .column-date_time, .column-duration, .column-agenda, .column-shortcode, .column-meeting_link {
\        }
        .column-shortcode code {
            display: inline-block;
            padding: 2px 4px;
            background: #f1f1f1;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
    </style>';
}



function meethour_fetch_upcoming_meetings() {
    $access_token = get_option('meethour_access_token', '');
    
    if (empty($access_token)) {
        return new WP_Error('no_token', 'Access token not found');
    }

    $response = wp_remote_post('https://api.meethour.io/api/v1.2/meeting/upcomingmeetings', [
        'headers' => [
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ],
        'body' => json_encode([
            'limit' => 10,
            'page' => 1,
            'show_all' => 0
        ])
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    $meetings = $body['meetings'] ?? [];

        // Debugging: Log the meetings array
        error_log(print_r($meetings, true));


    // Save meetings to custom post type
    foreach ($meetings as $meeting) {
        $post_id = wp_insert_post([
            'post_title' => $meeting['topic'],
            'post_content' => $meeting['agenda'],
            'post_type' => 'mh_meetings',
            'post_status' => 'publish',
            'meta_input' => [
                'meeting_id' => $meeting['meeting_id'],
                'start_time' => $meeting['start_time'],
                'duration' => $meeting['duration'],
                'meeting_link' => 'https://meethour.io/' . $meeting['meeting_id'],
            ],
        ]);
               // Debugging: Log the post ID
               error_log('Inserted post ID: ' . $post_id);
    }

    return $meetings;
}
?>


<?php
function meethour_meetings_page() {
    $meetings_query = new WP_Query([
        'post_type' => 'mh_meetings',
        'posts_per_page' => -1,
    ]);

    $meetings = $meetings_query->posts;
    // $meetings = meethour_fetch_upcoming_meetings() ;
    // print_r($meetings);
    echo json_encode($meetings);
    ?>
    <div class="wrap">
        <h1>MeetHour Upcoming Meetings List</h1>

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