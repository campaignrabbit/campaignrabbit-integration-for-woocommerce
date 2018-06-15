<?php

namespace CampaignRabbit\WooIncludes\Event;

use CampaignRabbit\WooIncludes\Api\Request;
use CampaignRabbit\WooIncludes\Helper\Site;


/**
 * Class Product
 */
class Product
{


    /**
     * @var
     */
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
    public function __construct($uri)
    {

        $this->uri = $uri;


    }


    public function create($meta_id, $post_id, $meta_key, $meta_value)
    {


        if (get_option('api_token_flag') && get_post_type($post_id) == 'product' && get_post($post_id)->post_title != "AUTO-DRAFT") {


            $this->woo_version = (new Site())->getWooVersion();

            if ($this->woo_version < 3.0) {

                /*
                 * 2.6
                 */

                $product = (new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Product())->get($post_id);


            } else {

                /*
                 * 3.0
                 */

                $product = (new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Product())->get($post_id);
            }


            global $product_create_request;

            $this->product_create_request = $product_create_request;


            $this->product_create_request->push_to_queue($product);

            $this->product_create_request->save()->dispatch();


        }


    }


    public function update($meta_id, $post_id, $meta_key, $meta_value)
    {

        if (get_option('api_token_flag') && $meta_key == '_edit_lock' && get_post_type($post_id) == 'product') {

            global $product_update_request;

            global $woo_product_sku;

            $this->product_update_request = $product_update_request;

            if ($this->woo_version < 3.0) {

                /*
                 * 2.6
                 */

                $product = (new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Product())->get($post_id);


            } else {

                /*
                 * 3.0
                 */

                $product = (new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Product())->get($post_id);
            }

            if ($product['type'] == 'simple') {

                $data = array(
                    'uri' => $this->uri . '/' . $woo_product_sku[$product['body']['r_product_id']],
                    'json_body' => \GuzzleHttp\json_encode($product['body'])
                );

                $this->product_update_request->push_to_queue($data);
                $this->product_update_request->save()->dispatch();


            } else {
                //variable products

                foreach ($product as $body) {

                    $data = array(
                        'uri' => $this->uri . '/' . $woo_product_sku[$body['body']['r_product_id']],
                        'json_body' => \GuzzleHttp\json_encode($body['body'])
                    );

                    $this->product_update_request->push_to_queue($data);
                    $this->product_update_request->save()->dispatch();

                }


            }


        }

    }


    public function delete($post_id)
    {

        //TODO not working

        if (get_option('api_token_flag') && get_post_type($post_id) == 'product') {

            global $product_delete_request;

            $this->product_delete_request = $product_delete_request;

            if ($this->woo_version < 3.0) {

                /*
                 * 2.6
                 */

                $product_sku = (new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Product())->getSKU($post_id);


            } else {

                /*
                 * 3.0
                 */

                $product_sku = (new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Product())->getSKU($post_id);
            }

            $this->product_delete_request->push_to_queue($product_sku);
            $this->product_delete_request->save()->dispatch();


        }
    }

    public function restore($post_id)
    {

        //TODO not checked yet

        if (get_option('api_token_flag') && get_post_type($post_id) == 'product') {

            global $product_restore_request;

            $this->product_restore_request = $product_restore_request;

            if ($this->woo_version < 3.0) {

                /*
                 * 2.6
                 */

                $product_sku = (new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Product())->getSKU($post_id);


            } else {

                /*
                 * 3.0
                 */

                $product_sku = (new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Product())->getSKU($post_id);
            }

            $this->product_restore_request->push_to_queue($product_sku);
            $this->product_restore_request->save()->dispatch();


        }

    }


    public function save_sku($post_id, $post, $update)
    {

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