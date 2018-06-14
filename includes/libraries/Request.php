<?php

namespace CampaignRabbit\WooIncludes\Lib;

use CampaignRabbit\WooIncludes\CampaignRabbit;
use GuzzleHttp\Client;



/**
 * Class Request
 */
class Request{


    private $campaignrabbit;


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
        $this->campaignrabbit=new CampaignRabbit();

        $this->api_token=$api_token;

        $this->app_id=$app_id;
    }



    protected function request($method, $uri, $body){

        try {

            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->campaignrabbit->get_base_uri()
            ]);

            $response=$client->request($method, $uri, [
                'body' => $body,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->api_token,
                    'Request-From-Domain' => $this->campaignrabbit->get_domain(),
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


