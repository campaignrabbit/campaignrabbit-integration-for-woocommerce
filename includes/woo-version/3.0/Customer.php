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
        $first_name=isset($customer->first_name)?$customer->first_name:'';
        $last_name=isset($customer->last_name)?$customer->last_name:'';
        $name=$first_name.' '.$last_name;
        if($name==' '){
            $name=$customer->user_login;
        }
        $roles=array(
            array(
                'meta_key'=>'CUSTOMER_GROUP',
                'meta_value'=>$roles,
                'meta_options'=>''
            ),
            array(
                'meta_key'=>'last_name',
                'meta_value'=>$last_name,
                'meta_options'=>''
            )
        );
        $updated_at=get_user_meta($customer->ID,'cr_user_updated',true);
        if(empty($updated_at)){
            $updated_at=current_time('mysql');
        }
        $post_customer = array(
            'email' =>$customer->user_email,
            'name' =>$name,
            'created_at'=>$user->user_registered,
           // 'updated_at'=>$updated_at,
            'meta' => $roles
        );

        return $post_customer;
    }

}