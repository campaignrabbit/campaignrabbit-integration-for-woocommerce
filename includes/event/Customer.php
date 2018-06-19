<?php


namespace CampaignRabbit\WooIncludes\Event;


class Customer
{


    /**
     * @var
     */
    protected $uri;

    protected $customer_create_request;

    protected $customer_update_request;

    protected $customer_delete_request;


    /**
     * Customer constructor.
     * @param $uri
     */
    public function __construct($uri)
    {
        $this->uri = $uri;

    }

    /**
     *
     */
    public function create()
    {

        if (get_option('api_token_flag')) {

            global $customer_create_request;

            $user_login=isset($_POST['user_login']) ? $_POST['user_login'] : '';

            $this->customer_create_request = $customer_create_request;
            update_user_meta( $user_login, 'cr_user_updated', current_time( 'mysql' ) );
            $meta_array = array(array(
                'meta_key' => 'CUSTOMER_GROUP',
                'meta_value' => isset($_POST['role']) ? $_POST['role'] : '',
                'meta_options' => ''
            ));
            $post_customer = array(
                'email' => isset($_POST['email']) ? $_POST['email'] : '',
                'name' => $user_login,
                'meta' => $meta_array
            );
            if (isset($_POST['createaccount']) ? $_POST['createaccount'] : false) {
                $post_customer = array(
                    'email' => isset($_POST['billing_email']) ? $_POST['billing_email'] : '',
                    'name' => isset($_POST['billing_first_name']) ? $_POST['billing_first_name'] : '',
                    'created_at'=>get_user_meta($user_login,'cr_user_updated',true),
                    'updated_at'=>get_user_meta($user_login,'cr_user_updated',true),
                    'meta' => $meta_array
                );
            }

            $this->customer_create_request->push_to_queue($post_customer);
            $this->customer_create_request->save()->dispatch();
        }

    }

    /**
     * @param $user_id
     * @param $old_user_data
     */
    public function update($user_id, $old_user_data)
    {

        if (get_option('api_token_flag')) {

            global $customer_update_request;
            $this->customer_update_request = $customer_update_request;

            update_user_meta( $old_user_data->user_login, 'cr_user_updated', current_time( 'mysql' ) );

            $data = array(
                'user_email' => $old_user_data->user_email,
                'user_id' => $user_id,
                'user_login'=>$old_user_data->user_login,
                'post_email'=>isset($_POST['email'])?$_POST['email']:''
            );
            $this->customer_update_request->push_to_queue($data);
            $this->customer_update_request->save()->dispatch();

        }

    }


    public function delete($user_id)
    {

        if (get_option('api_token_flag')) {

            global $customer_delete_request;

            $this->customer_delete_request = $customer_delete_request;


            $this->customer_delete_request->push_to_queue($user_id);
            $this->customer_delete_request->save()->dispatch();

        }
    }


}