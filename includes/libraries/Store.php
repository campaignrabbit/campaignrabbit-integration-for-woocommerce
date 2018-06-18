<?php

namespace CampaignRabbit\WooIncludes\Lib;

class Store extends Request{


    private $uri;


    private $request;



    public function __construct($api_token, $app_id)
    {
        $this->uri = 'user/store/auth';

        $this->request=(new Request($api_token, $app_id));

    }

    public function authenticate(){


        $response=$this->request->request('POST', $this->uri, array());

        return $response;
    }

}