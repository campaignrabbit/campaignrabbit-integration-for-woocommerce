<?php

namespace CampaignRabbit\WooIncludes\Lib;

use CampaignRabbit\WooIncludes\CampaignRabbit;
use CampaignRabbit\WooIncludes\Helper\Site;
use GuzzleHttp\Client;



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



    protected function request($method, $uri, $body){

        try {

            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->site->getBaseUri()
            ]);

            $response=$client->request($method, $uri, [
                'body' => $body,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->api_token,
                    'Request-From-Domain' => $this->site->getDomain(),
                    'App-Id' => $this->app_id,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'

                ]]);



        } catch (\Exception $e) {
            $response=$e->getResponse();
        }

        return $response;

    }


    protected function parseResponse($response){


            $parsed_response=array(
                'reasonPhrase'=> $response->getReasonPhrase(),
                'statusCode'=>$response->getStatusCode(),
                'bodyContent'=> $response->getBody()->getContents()
            );




        return $parsed_response;

    }





}


