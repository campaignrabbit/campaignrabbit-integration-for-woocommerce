<?php

namespace CampaignRabbit\WooIncludes\Event\Product;


use CampaignRabbit\WooIncludes\Lib\Product;

class ProductDelete extends \WP_Background_Process {

    /**
     * @var string
     */
    protected $action = 'product_delete';

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
        $product_api= new Product(get_option('api_token'),get_option('app_id'));
        $deleted=$product_api->delete($item);
        error_log($deleted->raw_body);

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