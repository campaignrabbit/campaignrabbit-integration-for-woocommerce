<?php

namespace CampaignRabbit\WooIncludes\WooVersion\v2_6;

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
            $updated_at=get_user_meta($customer->user_login,'cr_user_updated',true);
            if(!$updated_at){
                $updated_at=$user->user_registered;
            }
            $post_customer = array(
                'email' =>$customer->user_email,
                'name' =>$customer->user_login,
                'created_at'=>$user->user_registered,
                'updated_at'=>$updated_at,
                'meta' => $roles
            );

            return $post_customer;
        }

}