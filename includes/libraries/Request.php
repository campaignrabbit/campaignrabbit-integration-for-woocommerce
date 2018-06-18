<?php

namespace CampaignRabbit\WooIncludes\Lib;

use CampaignRabbit\WooIncludes\Helper\Site;
use Unirest\Request\Body;


/**
 * Class Request
 */
class Request{


    private $site;


    /**
     * @var
     */
    private $api_token;

    /**
     * @var
     */
    private $app_id;


    protected function __construct($api_token, $app_id)
    {
        $this->site=new Site();

        $this->api_token=$api_token;

        $this->app_id=$app_id;
    }



    protected function request($method, $uri, $data){

        try {

            $headers=array(
                'Authorization' => 'Bearer '. $this->api_token,
                'Request-From-Domain' => $this->site->getDomain(),
                'App-Id' =>$this->app_id,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            );

            $url=$this->site->getBaseUri().$uri;

            switch ($method){
                case 'POST':
                    $body = Body::json($data);
                    $response = \Unirest\Request::post($url,$headers,$body);
                    break;
                case 'GET':
                    $body = Body::json($data);
                    $response = \Unirest\Request::get($url,$headers,$body);
                    break;
                case 'PUT':
                    $body = Body::json($data);
                    $response = \Unirest\Request::put($url,$headers,$body);
                    break;
                case 'DELETE':
                    $body = Body::json($data);
                    $response = \Unirest\Request::delete($url,$headers,$body);
                    break;
                case 'PATCH':
                    $body = Body::json($data);
                    $response = \Unirest\Request::patch($url,$headers,$body);
                    break;
                default:
                    $response='';
                    break;
            }

        } catch (\Exception $e) {
            $response=$e;
        }

        /*
           $response->code;        // HTTP Status code
           $response->headers;     // Headers
           $response->body;        // Parsed body
           $response->raw_body;    // Unparsed body
        */

        return $response;

    }


}


