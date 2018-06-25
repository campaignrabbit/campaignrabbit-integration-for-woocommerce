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


    public function create(){
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
            $first_name=isset($_POST['first_name'])?$_POST['first_name']:'';
            $last_name=isset($_POST['last_name'])?$_POST['last_name']:'';
            $name=$first_name.' '.$last_name;
            if(empty($name)){
                $name=isset($_POST['user_login'])?$_POST['user_login']:'';
            }
            if (isset($_POST['createaccount']) ? $_POST['createaccount'] : false) {
                $post_customer = array(
                    'email' => isset($_POST['billing_email']) ? $_POST['billing_email'] : '',
                    'name' => $name,
                    'created_at'=>get_user_meta($user_login,'cr_user_updated',true),
                    'updated_at'=>get_user_meta($user_login,'cr_user_updated',true),
                    'meta' => $meta_array
                );
            }
            $this->customer_create_request->push_to_queue($post_customer);
            $this->customer_create_request->save()->dispatch();
        }

    }

    public function update($user_id, $old_user_data){
        if (get_option('api_token_flag')) {
            global $customer_update_request;
            $this->customer_update_request = $customer_update_request;
            update_user_meta( $old_user_data->user_login, 'cr_user_updated', current_time( 'mysql' ) );
            $first_name=isset($_POST['first_name'])?$_POST['first_name']:'';
            $last_name=isset($_POST['last_name'])?$_POST['last_name']:'';
            $name=$first_name.' '.$last_name;
            if(empty($name)){
                $name=$old_user_data->user_login;
            }
            $data = array(
                'user_email' => $old_user_data->user_email,
                'user_id' => $user_id,
                'user_name'=>$name,
                'post_email'=>isset($_POST['email'])?$_POST['email']:''
            );
            error_log('Update Customer Data: '.print_r($data,true));
            $this->customer_update_request->push_to_queue($data);
            $this->customer_update_request->save()->dispatch();
        }
    }


    public function delete($user_id){
        if (get_option('api_token_flag')) {
            global $customer_delete_request;
            $this->customer_delete_request = $customer_delete_request;
            $this->customer_delete_request->push_to_queue($user_id);
            $this->customer_delete_request->save()->dispatch();
        }
    }


}