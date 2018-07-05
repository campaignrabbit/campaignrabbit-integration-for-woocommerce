<?php

namespace CampaignRabbit\WooIncludes\Event;
use CampaignRabbit\WooIncludes\Helper\Site;

class Order
{

    protected $uri;

    protected $order_create_request;

    protected $order_update_request;

    protected $order_trash_request;

    public $woo_version;

    public function __construct($uri){
        $this->uri = $uri;
    }

    public function create($order_id){
        if (get_option('api_token_flag')) {
            global $order_create_request;
            $this->order_create_request=$order_create_request;
            $order_api= new \CampaignRabbit\WooIncludes\Lib\Order(get_option('api_token'),get_option('app_id'));
            $this->woo_version = (new Site())->getWooVersion();
            if ($this->woo_version < 3.0) {
                $order = (new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Order())->get($order_id);  //2.6
            } else {
                $order = (new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Order())->get($order_id);  //3.0
            }
            $created=$order_api->create($order);
            error_log('Order Created (Event):'.$created->raw_body);
        }
    }

    public function update($order_id,$old_status){
        if (get_option('api_token_flag')) {

            //experimental... however this should work.
            $this->create($order_id);
            return;
            global $order_update_request;
            $this->order_update_request=$order_update_request;
            $order = new \WC_Order($order_id);
            $data=array(
                'order_id'=>$order_id,
                'status'=>  $new_status,
                'updated_at'=>$order->modified_date
            );
            $order_api= new \CampaignRabbit\WooIncludes\Lib\Order(get_option('api_token'),get_option('app_id'));
            $updated=$order_api->update($data['order_id'],$data);
            error_log('Order Updated (Event):'.$updated->raw_body);
        }
    }

    public function trash($post_id){
        if (get_option('api_token_flag') && get_post_type($post_id) == 'shop_order') {
            global $order_trash_request;
            $this->order_trash_request = $order_trash_request;
            $order_api= new \CampaignRabbit\WooIncludes\Lib\Order(get_option('api_token'),get_option('app_id'));
            $deleted=$order_api->delete($post_id);
            error_log('Order Deleted (Event):'.$deleted->raw_body);
        }
    }




}