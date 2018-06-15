<?php

namespace CampaignRabbit\WooIncludes\Event;




use CampaignRabbit\WooIncludes\Api\Request;
use CampaignRabbit\WooIncludes\Helper\Site;

class Order
{


    protected $uri;

    protected $order_create_request;

    protected $order_update_request;

    protected $order_trash_request;

    public $woo_version;



    public function __construct($uri)
    {

        $this->uri = $uri;

    }


    public function create($order_id)
    {


        if (get_option('api_token_flag')) {

            $this->woo_version = (new Site())->getWooVersion();

            if ($this->woo_version < 3.0) {

                /*
                 * 2.6
                 */

                $order = (new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Order())->get($order_id);


            } else {

                /*
                 * 3.0
                 */

                $order = (new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Order())->get($order_id);
            }


            global $order_create_request;

            $this->order_create_request=$order_create_request;

            $this->order_create_request->push_to_queue( $order );
            $this->order_create_request->save()->dispatch();

        }

    }

    public function update($order_id,$old_status,$new_status)
    {

        if (get_option('api_token_flag')) {

            global $order_update_request;

            $this->order_update_request=$order_update_request;

            $data=array(
                'order_id'=>$order_id,
                'status'=>  $new_status
            );

            $this->order_update_request->push_to_queue( $data );
            $this->order_update_request->save()->dispatch();

        }

    }

    public function trash($post_id)
    {


        if (get_option('api_token_flag') && get_post_type($post_id) == 'shop_order') {

            global $order_trash_request;

            $this->order_trash_request = $order_trash_request;


            $this->order_trash_request->push_to_queue( $post_id );
            $this->order_trash_request->save()->dispatch();



        }

    }




}