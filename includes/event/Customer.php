<?php
/**
 * Created by PhpStorm.
 * User: flycart
 * Date: 6/3/18
 * Time: 12:35 PM
 */

namespace CampaignRabbit\WooIncludes\Event;


use CampaignRabbit\WooIncludes\Api\Request;

class Customer {


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
        $this->uri=$uri;

    }

    /**
     *
     */
    public function create(){

        if ( get_option('api_token_flag')) {

            global $customer_create_request;

            $this->customer_create_request = $customer_create_request;

            $meta_array = array(array(
                'meta_key' => 'dfs',
                'meta_value' => 'fsf',
                'meta_options' => 'as'
            ));
            $post_customer = array(

                'email' =>isset($_POST['email'])?$_POST['email']:'',
                'name' =>isset($_POST['user_login'])?$_POST['user_login']:'',
                'meta' => $meta_array

            );

            if(isset($_POST['createaccount'])?$_POST['createaccount']:false){
                $post_customer = array(

                    'email' =>isset($_POST['billing_email'])?$_POST['billing_email']:'',
                    'name' =>isset($_POST['billing_first_name'])?$_POST['billing_first_name']:'',
                    'meta' => $meta_array

                );
            }

            $json_body = json_encode($post_customer);

            $this->customer_create_request->push_to_queue( $json_body );
            $this->customer_create_request->save()->dispatch();
       }

    }

    /**
     * @param $user_id
     * @param $old_user_data
     */
    public function update($user_id, $old_user_data){

        if ( get_option('api_token_flag')) {

            global $customer_update_request;

            $this->customer_update_request = $customer_update_request;
            $customer_response=(new Request())->request('GET',$this->uri.'/get_by_email/'.$old_user_data->user_email,'');
            $id=json_decode($customer_response->getBody()->getContents(),true)['data']['id'];


                $meta_array = array(array(
                    'meta_key' => 'dummy',
                    'meta_value' => 'dummy',
                    'meta_options' => 'dummy'
                ));
                $post_customer = array(

                    'email' =>isset($_POST['email'])?$_POST['email']:'',
                    'name' =>$old_user_data->user_login,
                    'meta' => $meta_array

                );
                $json_body = json_encode($post_customer);
                $data=array(
                  'json_body'=>$json_body,
                  'uri'=>  $this->uri.'/'.$id
                );

                $this->customer_update_request->push_to_queue( $data );
                $this->customer_update_request->save()->dispatch();

        }

    }


    public function delete($user_id){

        if(get_option('api_token_flag')){

            global $customer_delete_request;

            $this->customer_delete_request = $customer_delete_request;

            $old_user_data=get_userdata($user_id);
            $customer_response=(new Request())->request('GET',$this->uri.'/get_by_email/'.$old_user_data->user_email,'');
            $id=json_decode($customer_response->getBody()->getContents(),true)['id'];
            $this->customer_delete_request->push_to_queue( $this->uri.'/'.$id );
            $this->customer_delete_request->save()->dispatch();

        }
    }





}