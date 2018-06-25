<?php

namespace CampaignRabbit\WooIncludes\Event\Customer;



use CampaignRabbit\WooIncludes\Api\Request;
use CampaignRabbit\WooIncludes\Lib\Customer;

class CustomerUpdate extends \WP_Background_Process {

    /**
     * @var string
     */
    protected $action = 'customer_update';

    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task( $item ) {
        // Actions to perform

        $customer_api= new Customer(get_option('api_token'),get_option('app_id'));
        $user = get_userdata( $item['user_id']);
        $roles='';
        foreach ($user->roles as $customer_role){
            if($roles==''){
                $roles=$customer_role;
            }else{
                $roles=$roles.'|'.$customer_role;
            }

        }
        $meta_roles=array(
            array(
                'meta_key'=>'CUSTOMER_GROUP',
                'meta_value'=>$roles,
                'meta_options'=>''
            )
        );
        $post_customer = array(
            'email' =>$item['post_email'],
            'name' =>$item['user_login'],
            'created_at'=>$user->user_registered,
            'updated_at'=>get_user_meta($item['user_login'],'cr_user_updated',true),
            'meta' => $meta_roles

        );

        $updated=$customer_api->update($item['user_email'],$post_customer);
        error_log($updated->raw_body);

        return false;
    }

    /**
     * Complete
     *
     * Override if applicable, but ensure that the below actions are
     * performed, or, call parent::complete().
     */
    protected function complete() {
        parent::complete();

        // Show notice to user or perform some other arbitrary task...
    }


}