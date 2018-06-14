<?php

namespace CampaignRabbit\WooIncludes\Lib;


class Order extends Request {


    /**
     * @var
     */
    private $uri;


    /**
     * @var Request
     */
    private $request;


    /**
     * Order constructor.
     * @param $api_token
     * @param $app_id
     */
    public function __construct($api_token, $app_id)
    {
        $this->uri = 'order';

        $this->request=new Request($api_token, $app_id);


    }


    /**
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function getAll(){

        $response=$this->request->request('GET', $this->uri, '');
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;

    }


    /**
     * @param $id
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function get($id){

        $response=$this->request->request('GET', $this->uri.'/'.$id, '');
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;



    }

    /**
     * @param $body
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function create($body){

        $json_body = json_encode($body);
        $response=$this->request->request('POST', $this->uri, $json_body);
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;

    }


    /**h
     * @param $body
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function update($body){

        $json_body = json_encode($body);
        $response=$this->request->request('PUT', $this->uri . '/' . $body['id'], $json_body);
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;

    }


    /**
     * @param $id
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function delete($id){

        $response=$this->request->request('DELETE', $this->uri . '/' . $id, '');
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;

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