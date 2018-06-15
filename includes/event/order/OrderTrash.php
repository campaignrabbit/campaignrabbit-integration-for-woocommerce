<?php

namespace CampaignRabbit\WooIncludes\Event\Order;


use CampaignRabbit\WooIncludes\Api\Request;

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
        $order=(new Request())->request('GET','order/get_by_r_id/'.$item,'');

        if(!$order){
            $r_order_id=json_decode($order->getBody()->getContents(),true)['data']['id'];

            $order= new \WC_Order($item);
            $json_body=json_encode(array(
                'status'=> $order->get_status()
            ));

            (new Request())->request('PUT','order/'.$r_order_id, $json_body);
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