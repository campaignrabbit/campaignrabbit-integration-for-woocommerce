<?php

namespace CampaignRabbit\WooIncludes\Lib;


class Product extends Request {



    private $uri;


    private $request;



    public function __construct($api_token, $app_id)
    {
        $this->uri = 'product';

        $this->request=new Request($api_token, $app_id);


    }



    public function getAll(){

        $response=$this->request->request('GET', $this->uri, '');

        return $response;

    }



    public function get($sku){

        $response=$this->request->request('GET', $this->uri.'/'.$sku, '');

        return $response;



    }


    public function create($body){


        $response=$this->request->request('POST', $this->uri, $body);

        return $response;
    }



    public function update($body, $old_sku){


        $response=$this->request->request('PUT', $this->uri . '/' . $old_sku, $body);

        return $response;

    }


    public function delete($sku){

        $response=$this->request->request('DELETE', $this->uri . '/' . $sku, '');

        return $response;

    }


    public function restore($sku){

       $response=$this->request->request('PUT', 'product/restore/' . $sku . '?allowSearchTrash', '');

       return $response;


    }




}