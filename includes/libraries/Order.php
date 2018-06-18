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
        $response=$this->request->request('PUT', $this->uri . '/' . $id, $body);
        return $response;
    }



    public function delete($id){
        $response=$this->request->request('DELETE', $this->uri . '/' . $id, '');
        return $response;
    }

    public function getStatus($status){
        switch ($status){
            case 'pending':
                $order_status='pending';
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
            default:
                $order_status='pending';
                break;
        }
        return $order_status;
    }


}