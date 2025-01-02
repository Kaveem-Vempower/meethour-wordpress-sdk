<?php
function wordpress_fetch_users(){
    $members = get_users(
        array(

            'orderby' => 'ID',
            'order'   => 'ASC'
        )
    );
    $members = array_map(function($member) {
        $member->usermeta = array_map(function($data) {
            return reset($data);
        }, get_user_meta($member->ID));
        return $member;
    }, $members);
    
    $user_data = array_map(function($member) {
        return $member->data;
    }, $members);
    return $user_data;
}

// foreach ($user_data as $user) {
//     echo $user->user_email . "\n";
// }

function meethour_fetch_users() {
    $access_token = get_option('meethour_access_token', '');
    
    if (empty($access_token)) {
        return new WP_Error('no_token', 'Access token not found');
    }

    $response = wp_remote_post('https://api.meethour.io/api/v1.2/customer/contacts', [
        'headers' => [
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ],
        'body' => json_encode([
            'exclude_hosts' => 0, 
            'limit' => 10,
            'page' => 1,
        ])
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    return $body['contacts'] ?? [];
}

function meethour_contact_delete($contact_id){
    $access_token = get_option('meethour_access_token', '');
    
    if (empty($contact_id)) {
        return new WP_Error('no_contact_id', 'Contact ID not provided');
    }

    if (empty($access_token)) {
        return new WP_Error('no_token', 'Access token not found');
    }

    $response = wp_remote_post('https://api.meethour.io/api/v1.2/customer/deletecontact', [
        'headers' => [
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ],
        'body' => json_encode([
            'contact_id' => $contact_id
        ])
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $response_code = wp_remote_retrieve_response_code($response);
    if ($response_code !== 200) {
        return new WP_Error('request_failed', 'Failed to delete contact', ['status' => $response_code]);
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);
    return $body;
}

function meethour_guests_page() {
    $wordpress_users = wordpress_fetch_users();
    $user_data = meethour_fetch_users();
    print_r($wordpress_users);
?>
<div class="wrap">
    <h1>MeetHour Contacts List</h1>

    <?php if (is_wp_error($wordpress_users)): ?>
        <div class="notice notice-error">
            <p><?php echo esc_html($recording->get_error_message()); ?></p>
        </div>
    <?php else: ?>
        <div class="card">
            <table  style="width: 1000px;" class="widefat striped">
                <thead>
                    <tr>
                        <th>User Id</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($user_data as $user): ?>
                        <tr>
                            <td><?php echo esc_html($user['user_id']); ?></td>
                            <td><?php echo esc_html($user['first_name']." ".$user['last_name']); ?></td>
                            <td><?php echo esc_html($user['email']); ?></td>
                            <td><?php echo esc_html($user['phone']); ?></td>
                            <td><?php 
                                    $date = new DateTime($user['created_at']);
                                    echo esc_html($date->format('d-m-Y'));
                                ?>
                            </td>
                            <td>
                                <a href="https://portal.meethour.io/customer/editContact/<?php echo esc_attr($user['id']); ?>"><button class="copy-shortcode">Edit</button></a>
                                <button 
                                    type="button" 
                                    data-user-id="<?php echo esc_attr($user['id']); ?>"
                                    onclick="if(confirm('Are you sure you want to delete this user?')) meethour_contact_delete(this.dataset.userId)" 
                                    class="meethour-delete-btn"
                                    aria-label="Delete user">
                                    Delete
                                </button>                            
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php foreach ($wordpress_users as $user): ?>
                        <tr>
                            <td><?php echo $user->ID; ?></td>
                            <td><?php echo $user->user_login; ?></td>
                            <td><?php echo $user->user_email; ?></td>
                            <td><?php   ?></td>
                            <td><?php 
                                    $date = new DateTime($user->user_registered);
                                    echo esc_html($date->format('d-m-Y'));
                                ?>
                            </td>
                            <!-- <td>
                                <a href="https://portal.meethour.io/customer/editContact/<?php echo $user->ID; ?>"><button class="copy-shortcode">Edit</button></a>
                                <button 
                                    type="button" 
                                    data-user-id="<?php echo $user->ID; ?>"
                                    onclick="if(confirm('Are you sure you want to delete this user?')) meethour_contact_delete(this.dataset.userId)" 
                                    class="meethour-delete-btn"
                                    aria-label="Delete user">
                                    Delete
                                </button>                            
                            </td> -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>


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