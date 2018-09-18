<?php


namespace CampaignRabbit\WooIncludes\Event;


class Customer
{

    protected $uri;

    protected $customer_create_request;

    protected $customer_update_request;

    protected $customer_delete_request;


    /**
     * Customer constructor.
     * @param $uri
     */
    public function __construct($uri){
        $this->uri = $uri;

    }


    public function create($user_id = '')
    {
        if (get_option('api_token_flag')) {
            global $customer_create_request;
            $this->customer_create_request = $customer_create_request;
            $user = new \stdClass();
            if (!empty($user_id) && is_numeric($user_id)) {
                $user = get_userdata($user_id);
            }
            $post_customer = false;
            if (isset($user->ID) && $user->ID > 0) {
                $post_customer = $this->create_registered_user($user);
            } else {
                $post_customer = $this->create_guest_user();
            }

            if ($post_customer) {
                $customer_api = new \CampaignRabbit\WooIncludes\Lib\Customer(get_option('api_token'), get_option('app_id'));
                $created = $customer_api->create($post_customer);
                error_log('Customer Created (Event):' . $created->raw_body);
            }

        }

    }

    public function create_registered_user($user) {

        $first_name = get_user_meta($user->ID, 'first_name', true);
        $last_name = get_user_meta($user->ID, 'last_name', true);
        if(empty($first_name) && empty($last_name)) {
            $name = $user->user_login;
        }else {
            $name=$first_name.' '.$last_name;
        }
        $roles = '';
        if(isset($user->roles)) {
            if(is_array($user->roles)) {
                $roles = implode(' | ', $user->roles);
            }elseif(is_string($user->roles)) {
                $roles = $user->roles;
            }
        }

        $meta_array = array(array(
            'meta_key' => 'CUSTOMER_GROUP',
            'meta_value' => $roles,
            'meta_options' => ''
        ));
        $post_customer = array(
            'email' => $user->user_email,
            'name' => $name,
            'created_at'=>current_time( 'mysql' ),
            // 'updated_at'=>current_time( 'mysql' ),
            'meta' => $meta_array
        );

        return $post_customer;
    }

    public function create_guest_user() {

        $post_customer = false;

        if(
            (isset($_POST['email']) && !empty($_POST['email']) ) ||
            (isset($_POST['billing_email']) || !empty($_POST['billing_email']))
        ) {

            $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
            $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
            $name = $first_name . ' ' . $last_name;
            if ($name == ' ') {
                $name = isset($_POST['user_login']) ? $_POST['user_login'] : '';
            }
            $meta_array = array(array(
                'meta_key' => 'CUSTOMER_GROUP',
                'meta_value' => isset($_POST['role']) ? $_POST['role'] : '',
                'meta_options' => ''
            ));
            $post_customer = array(
                'email' => isset($_POST['email']) ? $_POST['email'] : '',
                'name' => $name,
                'created_at' => current_time('mysql'),
                // 'updated_at'=>current_time( 'mysql' ),
                'meta' => $meta_array
            );

            if (isset($_POST['createaccount']) ? $_POST['createaccount'] : false) {
                $post_customer = array(
                    'email' => isset($_POST['billing_email']) ? $_POST['billing_email'] : '',
                    'name' => $name,
                    'created_at' => current_time('mysql'),
                    //   'updated_at'=>current_time( 'mysql' ),
                    'meta' => $meta_array
                );
            }
        }
        return $post_customer;
    }

    public function update($user_id, $old_user_data)
    {
        if (get_option('api_token_flag')) {
            global $customer_update_request;
            $this->customer_update_request = $customer_update_request;

            $user = get_userdata($user_id);
            if (!isset($user->ID) || $user->ID < 1) return;

            update_user_meta($user_id, 'cr_user_updated', current_time('mysql'));
            $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
            $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
            $name = $first_name . ' ' . $last_name;
            if ($name == ' ') {
                $name = $old_user_data->user_login;
            }
            $data = array(
                'user_email' => $old_user_data->user_email,
                'user_id' => $user_id,
                'user_name' => $name,
                'post_email' => isset($_POST['email']) ? $_POST['email'] : '',
            );


            $roles = '';
            foreach ($user->roles as $customer_role) {
                if ($roles == '') {
                    $roles = $customer_role;
                } else {
                    $roles = $roles . '|' . $customer_role;
                }

            }
            $meta_roles = array(
                array(
                    'meta_key' => 'CUSTOMER_GROUP',
                    'meta_value' => $roles,
                    'meta_options' => ''
                )
            );
            $post_customer = array(
                'email' => $data['post_email'],
                'name' => $data['user_name'],
                'created_at' => $user->user_registered,
                // 'updated_at'=>get_user_meta($data['user_id'],'cr_user_updated',true),
                'meta' => $meta_roles

            );

            $customer_api = new \CampaignRabbit\WooIncludes\Lib\Customer(get_option('api_token'), get_option('app_id'));
            $updated = $customer_api->update($data['user_email'], $post_customer);
            error_log('Customer Updated (Event):' . $updated->raw_body);

        }
    }


    public function delete($user_id){
        if (get_option('api_token_flag')) {
            global $customer_delete_request;
            $this->customer_delete_request = $customer_delete_request;
            $customer_api= new \CampaignRabbit\WooIncludes\Lib\Customer(get_option('api_token'),get_option('app_id'));
            $old_user_data = get_userdata($user_id);
            $deleted=$customer_api->delete($old_user_data->user_email);
            error_log('Customer Deleted (Event):'.$deleted->raw_body);
        }
    }


}
