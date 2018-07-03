<?php

namespace CampaignRabbit\WooIncludes\WooVersion\v2_6;

class Order{

    public function get($order_id){

        $post_order=array();
        $order = new \WC_Order($order_id);
        $order_meta=array();
        if(empty($order->billing_email)){
            return array();
        }
        foreach ($order as $order_key=>$order_value){
            if(!empty($order_value) && gettype($order_value)=='string'){
                $order_meta[]=array(
                    'meta_key'=>$order_key,
                    'meta_value'=>$order_value,
                    'meta_options'=>''
                );
            }

        }
        if($order && gettype($order)=='object'){
            $post_order_items = $order->get_items();
            $order_items = array();
            if(!empty($post_order_items)){
                foreach ($post_order_items as $post_order_item) {
                    $variation_id=isset($post_order_item['variation_id'])?$post_order_item['variation_id']:'';
                    $product_id=isset($post_order_item['product_id'])?$post_order_item['product_id']:'';
                    $product_id= empty($variation_id)?$product_id:$variation_id;
                    $product=wc_get_product($product_id);
                    $order_item_meta=array();
                    foreach ($post_order_item as $line_order_item_key=>$line_order_item_value){
                        if(!empty($line_order_item_value) && gettype($line_order_item_value)=='string'){
                            $order_item_meta[]=array(
                                'meta_key'=>$line_order_item_key,
                                'meta_value'=>$line_order_item_value,
                                'meta_options'=>''
                            );
                        }

                    }
                    if(gettype($product)=='object'){
                        $order_items[] = array(
                            'r_product_id' => $product_id,
                            'sku' =>  empty($product->sku)?$product_id:$product->sku,
                            'product_name' => isset($post_order_item['name'])?$post_order_item['name']:'',
                            'product_price' =>isset($post_order_item['line_total'])?$post_order_item['line_total']:'',
                            'item_qty' => $post_order_item['qty'],
                            'meta' => $order_item_meta
                        );
                    }else{
                        $order_items[] = array(
                            'r_product_id' => $product_id,
                            'sku' =>  $product_id,
                            'product_name' => isset($post_order_item['name'])?$post_order_item['name']:'',
                            'product_price' =>isset($post_order_item['line_total'])?$post_order_item['line_total']:'',
                            'item_qty' => isset($post_order_item['qty'])?$post_order_item['qty']:'',
                            'meta' => $order_item_meta
                        );
                    }

                }
            }else{
                return array();
            }
            $billing = array(
                'first_name' => $order->billing_first_name,
                'last_name'=>$order->billing_last_name,
                'company_name' => $order->billing_company,
                'email' => $order->billing_email,
                'mobile' => $order->billing_phone,
                'address_1' => $order->billing_address_1,
                'address_2' => $order->billing_address_2,
                'city' => $order->billing_city,
                'state' => $order->billing_state,
                'country' => $order->billing_country,
                'zipcode' => $order->billing_postcode
            );

            $shipping = array(

                'first_name' => !empty($order->shipping_first_name)?$order->shipping_first_name:$order->billing_first_name,
                'last_name'=> !empty($order->shipping_last_name)?$order->shipping_last_name:$order->billing_last_name,
                'company_name' => !empty($order->shipping_company)?$order->shipping_company:$order->billing_company,
                'email' => $order->billing_email,             //note: No Shipping Email
                'mobile' => $order->billing_phone,            //note: No Shipping Phone
                'address_1' => !empty($order->shipping_address_1)?$order->shipping_address_1:$order->billing_address_1,
                'address_2' => !empty($order->shipping_address_2)?$order->shipping_address_2:$order->billing_address_2,
                'city' => !empty($order->shipping_city)?$order->shipping_city:$order->billing_state,
                'state' => !empty($order->shipping_state)?$order->shipping_state:$order->billing_state,
                'country' => !empty($order->shipping_country)?$order->shipping_country:$order->billing_country,
                'zipcode' => !empty($order->shipping_postcode)?$order->shipping_postcode:$order->billing_postcode,

            );

            $order_status=(new \CampaignRabbit\WooIncludes\Lib\Order(get_option('api_token'),get_option('app_id')))->getStatus($order->post_status);

            $created_at=empty($order->order_date)?$order->get_date_created():$order->order_date;
            $updated_at=empty($order->modified_date)?$order->get_date_created():$order->modified_date;

            //customer data

            $customer=$this->getWooCustomer($order->billing_email);
            if(gettype($customer)=='object'){
                $customer_created_at=$customer->data->user_registered;
                $customer_updated_at=get_user_meta($customer->ID,'cr_user_updated',true);
                if(empty($customer_updated_at)){
                    $customer_updated_at=current_time('mysql');
                }
            }else{
                $customer_created_at=current_time('mysql');
                $customer_updated_at=current_time('mysql');
            }

            $post_order = array(
                'r_order_id' => $order->id,
                'r_order_ref' => $order->id,
                'customer_email' => $order->billing_email,
                'customer_name' => $order->billing_first_name,
                'order_total' => $order->get_total(),
                'meta' => $order_meta,
                'order_items' =>$order_items,
                'shipping' => $shipping,
                'billing' => $billing,
                'status'=>$order_status,
                'created_at'=>$created_at,
                'updated_at'=>$updated_at,
                'customer_created_at'=>$customer_created_at,
                'customer_updated_at'=>$customer_updated_at

            );
        }

        return $post_order;
    }

    public function getWooStatus($order_id){
        $order = new \WC_Order($order_id);
        $order_status = $order->post_status;
        return $order_status;
    }

    public function getWooCustomer($email){
        //check if email exists in users and if yes, send the data else false
        $user = get_user_by( 'email', $email );

        return $user;
    }

}