<?php

namespace CampaignRabbit\WooIncludes\WooVersion\v2_6;

class Product{

    public function get($product_id){

      $woo_product=wc_get_product( $product_id );

        $meta_array = array(array(
            'meta_key' => 'dummy_key',
            'meta_value' => 'dummy_value',
            'meta_options' => 'dummy_options'
        ));

        if ($woo_product->is_type('simple')) {

            //simple product

            $post_product = array(
                'r_product_id' => $woo_product->id,
                'sku' => empty($woo_product->sku)?$woo_product->id:$woo_product->sku,
                'product_name' => $woo_product->post->post_title,
                'product_price' => $woo_product->price,
                'parent_id' => $woo_product->id,
                'meta' => $meta_array

            );

            $customer=array(
                'body'=>$post_product,
                'type'=>'simple'
            );


        } else {

            //variable products

            $woo_variation_ids = $woo_product->get_children();

            $body=array();

            foreach ($woo_variation_ids as $woo_variation_id) {

                $woo_variable_product = wc_get_product($woo_variation_id);

                $post_product = array(
                    'r_product_id' => $woo_variation_id,
                    'sku' => empty($woo_variable_product->sku)?$woo_variation_id:$woo_variable_product->sku,
                    'product_name' => $woo_product->post->post_title,
                    'product_price' => $woo_variable_product->price,
                    'parent_id' => $woo_product->id,
                    'meta' => $meta_array

                );

                $body[] = $post_product;


            }

            $customer=array(
                'body'=>$body,
                'type'=>'variable'
            );

        }


        return $customer;

    }

    public function getVariationTitle($variation_id){

        global $wpdb;

        $variation = new \WC_Product_Variation($variation_id);
        $title_slug = current($variation->get_variation_attributes());

        $results = $wpdb->get_results("SELECT * FROM wp_terms WHERE slug = '{$title_slug}'", ARRAY_A);
        $variation_title = $results[0]['name'];

        return $variation_title;

    }

    public function getSKU($product_id){

        $woo_product = wc_get_product($product_id);
        $sku=empty($woo_product->sku)?$woo_product->id:$woo_product->sku;
        return $sku;
    }

}