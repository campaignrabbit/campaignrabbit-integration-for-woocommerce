<?php

namespace CampaignRabbit\WooIncludes\Lib;


class Order extends Request {


    private $uri;

    private $request;


    public function __construct($api_token, $app_id){
        $this->uri = 'order';
        $this->request=new Request($api_token, $app_id);
    }


    public function getAll(){
        $response=$this->request->request('GET', $this->uri, '');
        return $response;
    }



    public function get($id){
        $response=$this->request->request('GET', $this->uri.'/get_by_r_id/'.$id, '');
        return $response;
    }


    public function create($body){
        $response=$this->request->request('POST', $this->uri, $body);
        return $response;
    }



    public function update($id, $body){

        $order_api=(new \CampaignRabbit\WooIncludes\Lib\Order(get_option('api_token'),get_option('app_id')));
        $order_response=$this->get($id);
        if($order_response->code!=200){
            return $order_response;
        }
        $id=$order_response->body->data->id;
        $order_status=$order_api->getStatus($body['status']);
        $body=array(
            'status'=>$order_status,
            'updated_at'=>$body['updated_at']
        );
        $response=$this->request->request('PUT', $this->uri . '/' . $id, $body);
        return $response;
    }



    public function delete($id){
        $order_response=$this->get($id);
        if($order_response->code!=200){
            return $order_response;
        }
        $id=$order_response->body->data->id;
        $response=$this->request->request('DELETE', $this->uri . '/' . $id, '');
        return $response;
    }

    public function getStatus($status){
        switch ($status){
            case 'pending':
                $order_status='unpaid';
                break;
            case 'wc-pending':
                $order_status='unpaid';
                break;
            case 'wc-processing':
                $order_status='pending';
                break;
            case 'wc-on-hold':
                $order_status='unpaid';
                break;
            case 'wc-completed':
                $order_status='completed';
                break;
            case 'wc-cancelled':
                $order_status='cancelled';
                break;
            case 'wc-refunded':
                $order_status='cancelled';
                break;
            case 'wc-failed':
                $order_status='unpaid';
                break;
            case 'processing':
                $order_status='pending';
                break;
            case 'on-hold':
                $order_status='unpaid';
                break;
            case 'completed':
                $order_status='completed';
                break;
            case 'cancelled':
                $order_status='cancelled';
                break;
            case 'refunded':
                $order_status='cancelled';
                break;
            case 'failed':
                $order_status='unpaid';
                break;
            case 'wc-shipping':
                $order_status='pending';
                break;
            case 'shipping':
                $order_status='pending';
                break;
            case 'wc-trash':
                $order_status='trash';
                break;
            case 'trash':
                $order_status='trash';
                break;
            default:
                $order_status='pending';
                break;
        }
        return $order_status;
    }


}