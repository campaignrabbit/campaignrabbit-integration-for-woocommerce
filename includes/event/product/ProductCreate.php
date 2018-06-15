<?php

namespace CampaignRabbit\WooIncludes\Event\Product;


use CampaignRabbit\WooIncludes\Api\Request;

class ProductCreate extends \WP_Background_Process {

    /**
     * @var string
     */
    protected $action = 'product_create';

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
    protected function task( $item ) {
        // Actions to perform
        if($item['type']=='simple'){

            (new Request())->request('POST', 'product', \GuzzleHttp\json_encode($item['body']));

        }else{

            foreach ($item['body'] as $body){

                (new Request())->request('POST','product',\GuzzleHttp\json_encode($body));
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
    protected function complete() {
        parent::complete();

        // Show notice to user or perform some other arbitrary task...
    }



}