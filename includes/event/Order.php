<?php

namespace CampaignRabbit\WooIncludes\Event;




use CampaignRabbit\WooIncludes\Api\Request;

class Order
{


    protected $uri;

    protected $order_create_request;

    protected $order_update_request;

    protected $order_trash_request;

    public function __construct($uri)
    {

        $this->uri = $uri;

    }


    public function create($order_id)
    {


        if (get_option('api_token_flag')) {

            global $order_create_request;

            $this->order_create_request=$order_create_request;

            $this->order_create_request->push_to_queue( $order_id );
            $this->order_create_request->save()->dispatch();

        }

    }

    public function update($order_id,$old_status,$new_status)
    {

        if (get_option('api_token_flag')) {

            global $order_update_request;

            $this->order_update_request=$order_update_request;

            $order=(new Request())->request('GET','order/get_by_r_id/'.$order_id,'');
            $r_order_id=json_decode($order->getBody()->getContents(),true)['data']['id'];

            $order_status=(new \CampaignRabbit\WooIncludes\Lib\Order(get_option('api_token'),get_option('app_id')))->getOrderStatus($new_status);

            $json_body = json_encode(array(
                'status'=>$order_status
            ));

            $data=array(
                'json_body'=>$json_body,
                'uri'=>  $this->uri.'/'.$r_order_id
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
            $order=(new Request())->request('GET','order/get_by_r_id/'.$post_id,'');
            $r_order_id=json_decode($order->getBody()->getContents(),true)['data']['id'];

            $order= new \WC_Order($post_id);
            $json_body=json_encode(array(
                'status'=> $order->get_status()
            ));
            $data=array(
                'json_body'=>$json_body,
                'uri'=>  $this->uri.'/'.$r_order_id
            );
            $this->order_trash_request->push_to_queue( $data );
            $this->order_trash_request->save()->dispatch();


        }

    }




}