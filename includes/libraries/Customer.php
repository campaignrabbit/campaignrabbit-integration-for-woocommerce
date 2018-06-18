<?php

namespace CampaignRabbit\WooIncludes\Lib;


class Customer extends Request
{


    private $uri;

    private $request;


    public function __construct($api_token, $app_id){
        $this->uri = 'customer';
        $this->request=new Request($api_token, $app_id);
    }


    public function getAll(){
        $response=$this->request->request('GET', $this->uri, '');
        return $response;
    }


    public function get($email){
        $response=$this->request->request('GET',$this->uri.'/get_by_email/'.$email,'');
        return $response;
    }


    public function create($body){
        $response=$this->request->request('POST', $this->uri, $body);
        return $response;
    }


    public function update($old_email, $body){
        $customer_response=$this->get($old_email);
        if($customer_response->code!=200){
            return $customer_response;
        }
        $id=$customer_response->body->data->id;
        $response=$this->request->request('PUT', $this->uri . '/' . $id, $body);
        return $response;
    }


    public function delete($email){
        $customer_response=$this->get($email);
        if($customer_response->code!=200){
            return $customer_response;
        }
        $id=$customer_response->body->data->id;
        $response = $this->request->request('DELETE', $this->uri . '/' . $id, '');
        return $response;
    }



}