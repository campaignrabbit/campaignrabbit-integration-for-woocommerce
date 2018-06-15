<?php

namespace CampaignRabbit\WooIncludes\Migrate;

use CampaignRabbit\WooIncludes\Api\Request;
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
    protected function task($item)
    {

        $request= new Request();

        $woo_version = (new Site())->getWooVersion();

        if ($woo_version < 3.0) {

            /*
             * 2.6
             */

            $order = (new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Order())->get($item);


        } else {

            /*
             * 3.0
             */

            $order = (new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Order())->get($item);
        }

        $remote_order=$request->parseResponse($request->request('GET','order/get_by_r_id/'.$item,''));

        if($remote_order['bodyContent']=='false'){
            $json_body = json_encode($order);

            (new Request())->request('POST', 'order', $json_body);

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