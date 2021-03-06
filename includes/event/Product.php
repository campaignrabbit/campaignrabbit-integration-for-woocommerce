<?php

namespace CampaignRabbit\WooIncludes\Event;

use CampaignRabbit\WooIncludes\Api\Request;
use CampaignRabbit\WooIncludes\Helper\Site;


/**
 * Class Product
 */
class Product
{

    protected $uri;

    protected $product_create_request;

    protected $product_update_request;

    protected $product_delete_request;

    protected $product_restore_request;

    public $woo_version;

    /**
     * Product constructor.
     * @param $uri
     */
    public function __construct($uri){
        $this->uri = $uri;
    }

    public function create($meta_id, $post_id, $meta_key, $meta_value){
        if (get_option('api_token_flag') && get_post_type($post_id) == 'product' && get_post($post_id)->post_title != "AUTO-DRAFT") {
            $this->woo_version = (new Site())->getWooVersion();
            if ($this->woo_version < 3.0) {
                $product = (new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Product())->get($post_id);  //2.6
            } else {
                $product = (new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Product())->get($post_id);   //3.0
            }
            global $product_create_request;
            $this->product_create_request = $product_create_request;
            $product_api= new \CampaignRabbit\WooIncludes\Lib\Product(get_option('api_token'),get_option('app_id'));
            if($product['type']=='simple'){
                $created=$product_api->create($product['body']);
                error_log('Product Created (Event):'.$created->raw_body);
            }else{
                foreach ($product['body'] as $body){
                    $created=$product_api->create($body);
                    error_log('Product Created (Event):'.$created->raw_body);
                }
            }
        }
    }


    public function update($meta_id, $post_id, $meta_key, $meta_value){
        if (get_option('api_token_flag') && $meta_key == '_edit_lock' && get_post_type($post_id) == 'product') {
            global $product_update_request;
            global $woo_product_sku;
            $product_api= new \CampaignRabbit\WooIncludes\Lib\Product(get_option('api_token'),get_option('app_id'));
            $this->product_update_request = $product_update_request;
            $this->woo_version = (new Site())->getWooVersion();
            if ($this->woo_version < 3.0) {
                $product = (new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Product())->get($post_id);  //2.6
            } else {
                $product = (new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Product())->get($post_id);   //3.0
            }
            if ($product['type'] == 'simple') {
                $data = array(
                    'sku' => $woo_product_sku[$product['body']['sku']],
                    'body' => $product['body']
                );
                $updated=$product_api->update($data['body'],$data['sku']);
                error_log('Product Updated (Event):'.$updated->raw_body);

            } else {
                foreach ($product['body'] as $body) {      //variable products
                    $data = array(
                        'sku' => $woo_product_sku[$body['sku']],
                        'body' => $body
                    );
                    $updated=$product_api->update($data['body'],$data['sku']);
                    error_log('Product Updated (Event):'.$updated->raw_body);

                }
            }
        }
    }


    public function delete($post_id){

        //TODO not working

        if (get_option('api_token_flag') && get_post_type($post_id) == 'product') {
            global $product_delete_request;
            $this->product_delete_request = $product_delete_request;
            $this->woo_version = (new Site())->getWooVersion();
            if ($this->woo_version < 3.0) {
                $product_sku = (new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Product())->getSKU($post_id);   //2.6
            } else {
                $product_sku = (new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Product())->getSKU($post_id);   //3.0
            }
            $product_api= new \CampaignRabbit\WooIncludes\Lib\Product(get_option('api_token'),get_option('app_id'));
            $deleted=$product_api->delete($product_sku);
            error_log('Product Deleted (Event):'.$deleted->raw_body);
        }
    }

    public function restore($post_id){

        //TODO not checked yet

        if (get_option('api_token_flag') && get_post_type($post_id) == 'product') {
            global $product_restore_request;
            $this->product_restore_request = $product_restore_request;
            $this->woo_version = (new Site())->getWooVersion();
            if ($this->woo_version < 3.0) {
                $product_sku = (new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Product())->getSKU($post_id);   //2.6
            } else {
                $product_sku = (new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Product())->getSKU($post_id);   //3.0
            }
            $product_api= new \CampaignRabbit\WooIncludes\Lib\Product(get_option('api_token'),get_option('app_id'));
            $restored=$product_api->restore($product_sku);
            error_log('Product Restored (Event):'.$restored->raw_body);
        }
    }


    public function save_sku($post_id, $post, $update){
        global $woo_product_sku;
        $woo_product = wc_get_product($post_id);
        $woo_product_sku_array = array();
        if ($woo_product->is_type('simple')) {
            $woo_product_sku_array[$woo_product->get_id()] = $woo_product->get_sku();
        } else {
            //variable products
            $woo_variation_ids = $woo_product->get_children();
            foreach ($woo_variation_ids as $woo_variation_id) {
                $woo_variable_product = wc_get_product($woo_variation_id);
                $woo_product_sku_array[$woo_variable_product->get_id()] = $woo_variable_product->get_sku();
            }
        }
        $woo_product_sku = $woo_product_sku_array;
    }


}