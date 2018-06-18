<?php

namespace CampaignRabbit\WooIncludes\WooVersion\v3_0;

class Customer{

    public function get($customer){
        $roles=array();
        $user = get_userdata( $customer->ID );
        foreach ($user->roles as $customer_role){
            $roles[]=array(
                'meta_key'=>'CUSTOMER_GROUP',
                'meta_value'=>$customer_role,
                'meta_options'=>''
            );
        }
        $post_customer = array(
            'email' =>$customer->user_email,
            'name' =>$customer->user_login,
            'meta' => $roles
        );

        return $post_customer;
    }

}