<?php

require(WP_PLUGIN_DIR . '/meethour/vendor/autoload.php');
require(WP_PLUGIN_DIR . '/meethour/vendor/meethour/php-sdk/src/autoload.php');

use MeetHourApp\Services\MHApiService;
use MeetHourApp\Types\ContactsList;
use MeetHourApp\Types\DeleteContact;
use MeetHourApp\Types\AddContact;

function wordpress_fetch_users()
{
    $members = get_users(
        array(
            'orderby' => 'ID',
            'order'   => 'ASC'
        )
    );
    $members = array_map(function ($member) {
        $member->usermeta = array_map(function ($data) {
            return reset($data);
        }, get_user_meta($member->ID));
        return $member;
    }, $members);

    $user_data = array_map(function ($member) {
        return $member->data;
    }, $members);
    return $user_data;
}

function meethour_fetch_users()
{

    $meetHourApiService = new MHApiService();
    $access_token = get_option('meethour_access_token', '');

    if (empty($access_token)) {
        return set_transient('meethour_error_message', "Access Code not Generated", 30);
    }

    $body = new ContactsList();
    $response = $meetHourApiService->ContactsList($access_token, $body);

    if ($response->success == false) {
        set_transient('meethour_error_message', $response->message, 30); // store the error message for 30 seconds
        return;
    }

    $data = $response->contacts;
    foreach ($data as $contact) {
        $username = $contact->first_name . $contact->last_name;
        $firstname = $contact->first_name;
        $lastname = $contact->last_name;
        $email = $contact->email;
        $user = get_user_by('email', $email);
        $meta_key = 'meethour_user_id';
        $meta_value = $contact->id;
        if (!$user) {
            $user_id = wp_insert_user(array(
                'user_login' => $firstname,
                'user_email' => $email,
                'first_name' => $firstname,
                'last_name' => $lastname,
                'display_name' => $username,
                'role' => 'meethour'
            ));
            update_user_meta($user_id, $meta_key, $meta_value, '');
        }
    }


    if (!empty($data)) {
        wp_send_json_success($data);
    } else {
        wp_send_json_error('No users found');
    }

    wp_die(); // Always call this at the end of an AJAX handler


    return $data ?? [];
}

$access_token = get_option('meethour_access_token', '');
if (!empty($access_token)) {
    add_action('admin_head', 'add_fetch_contacts_button');
}

function add_fetch_contacts_button()
{

?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            jQuery("body.users-php .wrap h1").append('<a style="margin-left:10px" href="#" id="sync-contacts" class="page-title-action my-btn">Fetch Meet Hour Contacts</a>');
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
            jQuery("#sync-contacts").on("click", function(event) {
                event.preventDefault();
                $('.my-btn').buttonLoader('start');
                console.log('Fetch contacts button clicked');

                jQuery.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: {
                        action: 'meethour_fetch_contacts'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Contacts fetched successfully!');
                            console.log('Fetch contacts success:', response.data);
                            location.reload();
                            $('.my-btn').buttonLoader('stop');

                        } else {
                            alert('Failed to fetch contacts: ' + response.data);
                            console.log('Fetch contacts error 1:', response);
                            location.reload();
                            $('.my-btn').buttonLoader('stop');

                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Failed to fetch contacts: ' + error);
                        console.log('Fetch contacts error 2:', error, status, xhr);
                    }
                });
            });
        });
    </script>
    <?php
}
// }
add_action('wp_ajax_meethour_fetch_contacts', 'meethour_fetch_users');

$access_token = get_option('meethour_access_token', '');
if (!empty($access_token)) {
    add_action('delete_user_form', 'meethour_delete_user_form', 10, 2);
}
function meethour_delete_user_form($user, $userids)
{
    if (!empty($userids)) {
        $users_with_meethour_id = array();

        foreach ($userids as $user_id) {
            $meethour_user_id = get_user_meta($user_id, 'meethour_user_id', true);

            if (!empty($meethour_user_id)) {
                $users_with_meethour_id[] = $user_id;
            }
        }
        if (!empty($users_with_meethour_id)) {
    ?>
            <h2><?php esc_html_e('Meet Hour Options'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row" style="padding-top: 5px;">
                        <label for="delete_meethour"><?php esc_html_e('Delete from Meet Hour'); ?></label>
                    </th>
                    <td style="padding-top: 5px;">
                        <label for="delete_meethour">
                            <input type="checkbox" name="delete_meethour" id="delete_meethour" value="yes" />
                            <?php esc_html_e('I Agree to delete these users from the Meet Hour Portal as well.'); ?>
                        </label>
                    </td>
                </tr>
            </table>
        <?php
        }
    }
}

add_action('delete_user', 'delete_meethour_user');
function delete_meethour_user($user_id)
{
    settings_errors('meethour_messages');

    $delete_meethour = isset($_POST['delete_meethour']) ? sanitize_text_field($_POST['delete_meethour']) : '';

    if ($delete_meethour === 'yes') {
        $meetHourApiService = new MHApiService();
        $access_token = get_option('meethour_access_token', '');
        $meethour_user_id = get_user_meta($user_id, 'meethour_user_id', true);
        if (!empty($meethour_user_id)) {
            $body = new DeleteContact($meethour_user_id);
            $response = $meetHourApiService->deleteContact($access_token, $body);
            if ($response->success == false) {
                set_transient('meethour_error_message', $response->message, 30);
            }
        }
    }
}


function create_user_in_my_app($user_id)
{
    settings_errors('meethour_messages');
    $meetHourApiService = new MHApiService();
    $user = get_userdata($user_id);
    $token = get_option('meethour_access_token', '');
    $email = $user->user_email;
    $first_name = $user->first_name;
    $last_name = $user->last_name;
    $username = $user->user_login;
    $role = $user->role;
    if ($role != 'meethour') {
        return;
    }
    $body = new AddContact($email, $first_name, $last_name, $username);
    $response = $meetHourApiService->AddContact($token, $body);
    if ($response->success == false) {
        set_transient('meethour_error_message', $response->message, 30); // store the error message for 30 seconds
        return;
    } else {
        $data = $response->data;
        $meta_value = $data->id;
        $meta_key = 'meethour_user_id';
        add_user_meta($user_id, $meta_key, $meta_value, true);
    }
}

add_action('user_register', 'create_user_in_my_app', 10, 1);
function meethour_display_error_message_guests()
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
add_action('admin_notices', 'meethour_display_error_message_guests');
