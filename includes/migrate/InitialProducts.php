<?php

namespace CampaignRabbit\WooIncludes\Migrate;


use CampaignRabbit\WooIncludes\Helper\Site;
use CampaignRabbit\WooIncludes\Lib\Product;


/**
 * Class Initial_Products
 */
class InitialProducts extends \WP_Background_Process
{


    /**
     * @var string
     */
    protected $action = 'products_initial_migrate_process';


    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task($item)
    {

        $site=new Site();
        $product_api= new Product(get_option('api_token'),get_option('app_id'));
        $woo_version = $site->getWooVersion();

        if ($woo_version < 3.0) {
            $product = (new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Product())->get($item); //2.6
        } else {
            $product = (new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Product())->get($item); //3.0
        }

        if ($product['type'] == 'simple') {

            $product_response=$product_api->get($product['body']['sku']);

            if($product_response['statusCode']==404){
                $created=$product_api->create($product['body']);
            }elseif ($product_response['statusCode']==200){
                $updated=$product_api->update($product['body'],$product['body']['sku']);
            }

        } else {

            foreach ($product['body'] as $body) {

                $product_response=$product_api->get($body['sku']);

                if($product_response['statusCode']==404){
                    $created=$product_api->create($body);
                }else{
                    $updated=$product_api->update($body,$body['sku']);
                }
            }
        }
        return false;
    }

    /**
     * Complete
     *
     * Override if applicable, but ensure that the below actions are
     * performed, or, call parent::complete().
     */
    protected function complete()
    {
        parent::complete();

        // Show notice to user or perform some other arbitrary task...
    }


}