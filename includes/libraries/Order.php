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
    public function getAllOrders(){

        $response=$this->request->request('GET', $this->uri, '');
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;

    }


    /**
     * @param $id
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function getOrder($id){

        $response=$this->request->request('GET', $this->uri.'/'.$id, '');
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;



    }

    /**
     * @param $body
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function createOrder($body){

        $json_body = json_encode($body);
        $response=$this->request->request('POST', $this->uri, $json_body);
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;

    }


    /**h
     * @param $body
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function updateOrder($body){

        $json_body = json_encode($body);
        $response=$this->request->request('PUT', $this->uri . '/' . $body['id'], $json_body);
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;

    }


    /**
     * @param $id
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function deleteOrder($id){

        $response=$this->request->request('DELETE', $this->uri . '/' . $id, '');
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;

    }


}