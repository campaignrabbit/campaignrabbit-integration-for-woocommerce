<?php

namespace CampaignRabbit\WooIncludes\WooVersion\v3_0;

class Customer{

    public function get($customer){
        $roles='';
        $user = get_userdata( $customer->ID );
        foreach ($user->roles as $customer_role){
            if($roles==''){
                $roles=$customer_role;
            }else{
                $roles=$roles.'|'.$customer_role;
            }

        }

        $roles=array(
            array(
                'meta_key'=>'CUSTOMER_GROUP',
                'meta_value'=>$roles,
                'meta_options'=>''
            )
        );
        $updated_at=get_user_meta($customer->user_login,'cr_user_updated',true);
        if(!$updated_at){
            $updated_at=$user->user_registered;
        }
        $post_customer = array(
            'email' =>$customer->user_email,
            'name' =>$customer->first_name.' '.$customer->last_name,
            'created_at'=>$user->user_registered,
            'updated_at'=>$updated_at,
            'meta' => $roles
        );

        return $post_customer;
    }

}