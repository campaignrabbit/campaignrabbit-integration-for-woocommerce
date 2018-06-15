<?php

namespace CampaignRabbit\WooIncludes\WooVersion\v3_0;

class Order{


    public function get($order_id){

        $order = new \WC_Order($order_id);
        $post_order_items = $order->get_items();
        $meta_array = array(array(
            'meta_key' => 'dfs',
            'meta_value' => 'fsf',
            'meta_options' => 'as'
        ));

        $order_items = array();
        foreach ($post_order_items as $post_order_item) {
            $x=$post_order_item['item_meta']['_variation_id'][0];
            $product_id= empty($post_order_item->get_variation_id())?$post_order_item->get_product_id():$post_order_item->get_variation_id();
            $product=wc_get_product($product_id);
            $order_items[] = array(
                'r_product_id' => $product_id,
                'sku' => $product->get_sku(),
                'product_name' => $product->get_title(),
                'product_price' => $product->get_price(),
                'item_qty' => $post_order_item->get_quantity(),
                'meta' => $meta_array
            );
        }

        $billing = array(
            'first_name' => $order->get_billing_first_name(),
            'company_name' => $order->get_billing_company(),
            'email' => $order->get_billing_email(),
            'mobile' => $order->get_billing_phone(),
            'address_1' => $order->get_billing_address_1(),
            'address_2' => $order->get_billing_address_2(),
            'city' => $order->get_billing_city(),
            'state' => $order->get_billing_state(),
            'country' => $order->get_billing_country(),
            'zipcode' => $order->get_billing_postcode()
        );

        $shipping = array(

            'first_name' => !empty($order->get_shipping_first_name())?$order->get_shipping_first_name():$order->get_billing_first_name(),
            'company_name' => !empty($order->get_shipping_company())?$order->get_shipping_company():$order->get_billing_company(),
            'email' => $order->get_billing_email(),             //note: No Shipping Email
            'mobile' => $order->get_billing_phone(),            //note: No Shipping Phone
            'address_1' => !empty($order->get_shipping_address_1())?$order->get_shipping_address_1():$order->get_billing_address_1(),
            'address_2' => !empty($order->get_shipping_address_2())?$order->get_shipping_address_2():$order->get_billing_address_2(),
            'city' => !empty($order->get_shipping_city())?$order->get_shipping_city():$order->get_billing_state(),
            'state' => !empty($order->get_shipping_state())?$order->get_shipping_state():$order->get_billing_state(),
            'country' => !empty($order->get_shipping_country())?$order->get_shipping_country():$order->get_billing_country(),
            'zipcode' => !empty($order->get_shipping_postcode())?$order->get_shipping_postcode():$order->get_billing_postcode(),

        );


        $order_status=(new \CampaignRabbit\WooIncludes\Lib\Order(get_option('api_token'),get_option('app_id')))->getOrderStatus($order->get_status());



        $post_order = array(
            'r_order_id' => $order->get_id(),
            'r_order_ref' => $order->get_id(),
            'customer_email' => $order->get_billing_email(),
            'customer_name' => $order->get_billing_first_name(),
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