<?php

namespace CampaignRabbit\WooIncludes\Lib;




/**
 * Class Customer
 * @package Woocampaign\WooIncludes\Lib
 */
class Customer extends Request
{


    /**
     * @var
     */
    private $uri;


    private $request;



    public function __construct($api_token, $app_id)
    {
        $this->uri = 'customer';

        $this->request=(new Request($api_token, $app_id));


    }


    /**
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function getAll(){

        $response=$this->request->request('GET', $this->uri, '');
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;

    }



    public function get($email){


        $response=$this->request->request('GET',$this->uri.'/get_by_email/'.$email,'');
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;

    }


    /**
     * @param $body
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function create($body)
    {

        $json_body = json_encode($body);
        $response=$this->request->request('POST', $this->uri, $json_body);
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;

    }


    /**
     * @param $body
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function update($body, $old_email)
    {

        $json_body = json_encode($body);
        $customer_response=$this->request->request('GET',$this->uri.'/get_by_email/'.$old_email,'');
        if($customer_response->getStatusCode()!=200){
            $parsed_response=$this->request->parseResponse($customer_response);
            return $parsed_response;
        }
        $id=json_decode($customer_response->getBody()->getContents(),true)['id'];
        $response=$this->request->request('PUT', $this->uri . '/' . $id, $json_body);
        $parsed_response=$this->request->parseResponse($response);

        return $parsed_response;

    }



    public function delete($email)
    {
        $customer_response=$this->request->request('GET',$this->uri.'/get_by_email/'.$email,'');
        if($customer_response->getStatusCode()!=200){
            $parsed_response=$this->request->parseResponse($customer_response);
            return $parsed_response;
        }
        $id=json_decode($customer_response->getBody()->getContents(),true)['id'];
        $response = $this->request->request('DELETE', $this->uri . '/' . $id, '');
        $parsed_response = $this->request->parseResponse($response);

        return $parsed_response;

    }


}