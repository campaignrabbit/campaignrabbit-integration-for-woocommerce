<?php

namespace CampaignRabbit\WooIncludes\WooVersion\v2_6;

class Customer{


    public function get($product_id){

        $woo_product = wc_get_product($product_id);

        $meta_array = array(array(
            'meta_key' => 'dummy_key',
            'meta_value' => 'dummy_value',
            'meta_options' => 'dummy_options'
        ));

        if ($woo_product->is_type('simple')) {

            //simple product

            $post_product = array(
                'r_product_id' => $woo_product->id,
                'sku' => $woo_product->sku,
                'product_name' => $woo_product->post->post_title,
                'product_price' => $woo_product->price,
                'parent_id' => $woo_product->id,
                'meta' => $meta_array

            );

            $json_body = json_encode($post_product);

            $customer=array(
                'json_body'=>$json_body,
                'type'=>'simple'
            );


        } else {

            //variable products

            $woo_variation_ids = $woo_product->get_children();

            $json_body=array();

            foreach ($woo_variation_ids as $woo_variation_id) {

                $woo_variable_product = wc_get_product($woo_variation_id);

                $post_product = array(
                    'r_product_id' => $woo_variation_id,
                    'sku' => $woo_variable_product->sku,
                    'product_name' => $woo_variable_product->title,
                    'product_price' => $woo_variable_product->price,
                    'parent_id' => $woo_product->id,
                    'meta' => $meta_array

                );

                $json_body[] = json_encode($post_product);


            }

            $customer=array(
                'json_body'=>$json_body,
                'type'=>'variable'
            );

        }


        return $customer;

    }

}