<?php

namespace CampaignRabbit\WooIncludes\Migrate;

use CampaignRabbit\WooIncludes\Helper\FileHandler;
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
    protected function task($item){


        $product_api= new Product(get_option('api_token'),get_option('app_id'));
        $file_handler=new FileHandler();
        if ($item['type'] == 'simple') {
            $product_response=$product_api->get($item['body']['sku']);
            if($product_response->code==404){
                $created=$product_api->create($item['body']);
                error_log('Simple Product Created: '.$created->raw_body);
                $file_handler->append('Simple Product Created: '. $created->raw_body);
            }elseif ($product_response->code==200){
                $updated=$product_api->update($item['body'],$item['body']['sku']);
                error_log('Simple Product Updated: '.$updated->raw_body);
                $file_handler->append('Simple Product Updated: '. $updated->raw_body);
            }else{
                error_log('Simple Product Migrate Error: '.$product_response->raw_body);
                $file_handler->append('Simple Product Migrate Error: '. $product_response->raw_body);
            }
        }elseif($item['type']=='variable') {
            foreach ($item['body'] as $body) {
                $product_response=$product_api->get($body['sku']);
                if($product_response->code==404){
                    $created=$product_api->create($body);
                    error_log('Variable Product Created: '.$created->raw_body);
                    $file_handler->append('Variable Product Created: '. $created->raw_body);
                }elseif ($product_response->code==200){
                    $updated=$product_api->update($body,$body['sku']);
                    error_log('Variable Product Updated: '.$updated->raw_body);
                    $file_handler->append('Variable Product Updated: '. $updated->raw_body);
                }else{
                    error_log('Variable Product Migrate Error: '.$product_response->raw_body);
                    $file_handler->append('Variable Product Migrate Error: '. $product_response->raw_body);
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