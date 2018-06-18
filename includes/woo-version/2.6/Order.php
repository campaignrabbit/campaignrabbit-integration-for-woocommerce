<?php

namespace CampaignRabbit\WooIncludes\WooVersion\v2_6;

class Order{

    public function get($order_id){


        $order = new \WC_Order($order_id);
        $post_order_items = $order->get_items();
        $meta_array = array(array(
            'meta_key' => 'dummy_key',
            'meta_value' => 'dummy_value',
            'meta_options' => 'dummy_options'
        ));

        $order_items = array();
        foreach ($post_order_items as $post_order_item) {
            $variation_id=$post_order_item['variation_id'];
            $product_id=$post_order_item['product_id'];
            $product_id= empty($variation_id)?$product_id:$variation_id;
            $product=wc_get_product($product_id);
            $order_items[] = array(
                'r_product_id' => $product_id,
                'sku' => $product->sku,
                'product_name' => $product->post->post_title,
                'product_price' => $product->price,
                'item_qty' => $post_order_item['qty'],
                'meta' => $meta_array
            );
        }

        $billing = array(
            'first_name' => $order->billing_first_name,
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


        $post_order = array(
            'r_order_id' => $order->id,
            'r_order_ref' => $order->id,
            'customer_email' => $order->billing_email,
            'customer_name' => $order->billing_first_name,
            'order_total' => $order->get_total(),
            'meta' => $meta_array,
            'order_items' =>$order_items,
            'shipping' => $shipping,
            'billing' => $billing,
            'status'=>$order_status

        );


        return $post_order;


    }

}