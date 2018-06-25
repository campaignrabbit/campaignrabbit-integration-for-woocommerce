<?php

namespace CampaignRabbit\WooIncludes\Event\Order;

use CampaignRabbit\WooIncludes\Lib\Order;

class OrderTrash extends \WP_Background_Process {

    /**
     * @var string
     */
    protected $action = 'order_delete';

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

        $order_api= new Order(get_option('api_token'),get_option('app_id'));
        $deleted=$order_api->delete($item);
        error_log('Order Deleted (Event):'.$deleted->raw_body);

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