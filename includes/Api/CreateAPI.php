<?php

namespace SixamTech\Api;

class CreateAPI
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_contacts_api_routes'));
    }
    public function register_contacts_api_routes()
    {
        register_rest_route('6amTech/v1', '/contacts/', array(
            'methods' => "POST",
            "callback" => array($this, "add_contact"),
            'permission_callback' => array($this, 'check_authentication'),
        ));
        register_rest_route('6amTech/v1', '/contacts/', array(
            'methods' => "DELETE",
            "callback" => array($this, "delete_contact"),
            'permission_callback' => array($this, 'check_authentication'),
        ));

        register_rest_route('6amTech/v1', '/contacts/', [
            'methods'             => 'PUT',
            'callback'            => array($this, 'update_contact'),
            'permission_callback' => array($this, 'check_authentication'),
        ]);
    }

    public function check_authentication()
    {
        return is_user_logged_in();
    }


    public function update_contact($request)
    {
        global $wpdb;

        $table_name = 'contact_list';

        $data = $request->get_json_params();

        $id      = intval($data['id']);
        $name    = sanitize_text_field($data['name']);
        $email   = sanitize_email($data['email']);
        $mobile  = sanitize_text_field($data['mobile']);
        $address = sanitize_textarea_field($data['address']);


        if (!$id || !$name || !$email || !$mobile || !$address) {
            return new \WP_REST_Response(array('error' => 'Please provide all fields.'), 400);
        }

        $mobile_pattern = '/^01[3-9][0-9]{8}$/';

        if (!preg_match($mobile_pattern, $mobile)) {
            return new \WP_REST_Response(array(
                'message' => 'Invalid phone number format. Please use a valid Bangladeshi phone number (e.g., 01791322247)',
            ), 400); // Return error response with status 400
        }

        $result = $wpdb->update(
            $table_name,
            array(
                'name'    => $name,
                'email'   => $email,
                'mobile'  => $mobile,
                'address' => $address,
            ),
            array('id' => $id)
        );

        if ($result === false) {
            return new \WP_REST_Response(array('error' => 'Failed to update contact.'), 500);
        }
        return new \WP_REST_Response(array('message' => 'Contact updated successfully.'), 200);
    }


    public function delete_contact($request)
    {
        $contact_id = $request->get_param('id');

        if (!$contact_id) {
            return new \WP_REST_Response(array(
                'error' => 'Invalid contact ID.',
            ), 400);
        }

        global $wpdb;
        $table = 'contact_list';

        $deleted = $wpdb->delete($table, array('id' => $contact_id));

        if ($deleted === false) {
            return new \WP_REST_Response(array(
                'error' => 'Failed to delete contact.',
            ), 500);
        }

        return new \WP_REST_Response(array(
            'message' => 'Contact deleted successfully.',
        ), 200);
    }

    public function add_contact($request)
    {
        global $wpdb;

        $params = $request->get_json_params();

        $name    = sanitize_text_field($params['name']);
        $email   = sanitize_email($params['email']);
        $mobile  = sanitize_text_field($params['mobile']);
        $address = sanitize_textarea_field($params['address']);

        if (!$name || !$email || !$mobile || !$address) {
            return new \WP_REST_Response(array(
                'message' => 'All fields are required.',
            ), 400);
        }

        $wpdb->insert('contact_list', array(
            'name'    => $name,
            'email'   => $email,
            'mobile'  => $mobile,
            'address' => $address,
        ));

        if ($wpdb->insert_id) {
            return new \WP_REST_Response(array(
                'message' => "Contact added successfully!",
                'id'      => $wpdb->insert_id
            ), 200);
        } else {
            return new \WP_REST_Response(array(
                'message' => "Something went wrong while saving the contact.",
            ), 500);
        }
    }
}