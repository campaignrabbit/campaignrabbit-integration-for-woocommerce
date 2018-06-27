<?php

namespace CampaignRabbit\WooIncludes\Migrate;


use CampaignRabbit\WooIncludes\Helper\FileHandler;
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
        $item=json_decode($item, true);
        $order_api=new Order($item['api_token'],$item['app_id']);
        $file_handler=new FileHandler();
        /**
         * Dont do anything if order_status is auto_draft
         */
        if($item['order_status']!='auto-draft'){
            $order_response = $order_api->get($item['order_id']);
            if ($order_response->code == 404) {
                $created = $order_api->create($item['order_body']);
                error_log('Order Created: '.$created->raw_body);
                $file_handler->append('Order Created: '. $created->raw_body);
            }elseif ($order_response->code==200){
                $order_update_body = array(
                    'status' => $item['order_status'],
                    'updated_at'=>$item['order_body']['updated_at']
                );
                $updated = $order_api->update($item['order_id'],$order_update_body);
                error_log('Order Updated: '.$updated->raw_body);
                $file_handler->append('Order Updated: '. $updated->raw_body);
            }else{
                error_log('Order Migrate Error: '.$order_response->raw_body);
                $file_handler->append('Order Migrate Error: '. $order_response->raw_body);
            }

            /**
             * Delete orders from campaignrabbit if not in woocommerce
             */
            $cr_orders=$order_api->getAll()->body->data;
            $woo_order_ids=$item['woo_order_ids'];
            foreach ($cr_orders as $cr_order){
                $order_id=$cr_order->order_ref;
                if(!in_array($order_id,$woo_order_ids) ){
                    $deleted=$order_api->delete($order_id);
                    error_log('Order Deleted:'.$deleted->raw_body);
                    $file_handler->append('Order Deleted: '. $deleted->raw_body);
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