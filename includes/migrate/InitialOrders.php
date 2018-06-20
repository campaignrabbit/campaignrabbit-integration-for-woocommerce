<?php

namespace CampaignRabbit\WooIncludes\Migrate;


use CampaignRabbit\WooIncludes\Helper\Site;
use CampaignRabbit\WooIncludes\Lib\Order;


/**
 * Class Initial_Orders
 */
class InitialOrders extends \WP_Background_Process
{


    /**
     * @var string
     */
    protected $action = 'orders_initial_migrate_process';


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
        $site = new Site();
        $order_v2_6=new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Order();
        $order_v3_0=new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Order();
        $order_api=new Order(get_option('api_token'),get_option('app_id'));
        $woo_version = $site->getWooVersion();

        if ($woo_version < 3.0) {
            $order_body = $order_v2_6->get($item); //2.6
            $order_status=$order_v2_6->getWooStatus($item);
        } else {
            $order_body = $order_v3_0->get($item); //3.0
            $order_status=$order_v3_0->getWooStatus($item);
        }

        $order_response = $order_api->get($item);

        if ($order_response->code == 404) {
            $created = $order_api->create($order_body);
            error_log('Order Created: '.$created->raw_body);
        }elseif ($order_response->code==200){
            $order_update_body = array(
                'status' => $order_status,
                'updated_at'=>$order_body['updated_at']
            );
            $updated = $order_api->update($item,$order_update_body);
            error_log('Order Updated: '.$updated->raw_body);
        }else{
            error_log('Order Migrate Error: '.$order_response->raw_body);
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