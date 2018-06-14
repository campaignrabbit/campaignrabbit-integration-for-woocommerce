<?php

namespace CampaignRabbit\WooIncludes\Lib;

use CampaignRabbit\WooIncludes\Lib\Request;



class Product extends Request {


    /**
     * @var
     */
    private $uri;


    private $request;



    public function __construct($api_token, $app_id)
    {
        $this->uri = 'product';

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



    public function get($sku){

        $response=$this->request->request('GET', $this->uri.'/'.$sku, '');
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


    /**
     * @param $body
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function update($body, $old_sku){

        $json_body = json_encode($body);
        $response=$this->request->request('PUT', $this->uri . '/' . $old_sku, $json_body);
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;

    }


    public function delete($sku){

        $response=$this->request->request('DELETE', $this->uri . '/' . $sku, '');
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;

    }


    public function restore($sku){

       $response=$this->request->request('PUT', 'product/restore/' . $sku . '?allowSearchTrash', '');
       $parsed_response=$this->request->parseResponse($response);

       return $parsed_response;


    }




}