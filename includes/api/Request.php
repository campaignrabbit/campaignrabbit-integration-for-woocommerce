<?php

namespace CampaignRabbit\WooIncludes\Api;


use CampaignRabbit\WooIncludes\Helper\Site;
use GuzzleHttp\Client;

/**
 * Class Request
 */
class Request{


    private $site;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->site=new Site();
    }


    /**
     * @param $method
     * @param $uri
     * @param $json_body
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    public function request($method, $uri, $json_body){

        try {

            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->site->getBaseUri()
            ]);

            $response=$client->request($method, $uri, [
                'body' => $json_body,
                'headers' => [
                    'Authorization' => 'Bearer ' . get_option('api_token'),
                    'Request-From-Domain' => $this->site->getDomain(),
                    'App-Id' => get_option('app_id'),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'

                ]]);



        } catch (\Exception $e) {

            $error_message=$e->getMessage();
            echo "<script type='text/javascript'>alert('$error_message');</script>";
            $response=$e->getResponse();;


        return $response;

        }

        return $response;

    }


    public function parseResponse($response){


        $parsed_response=array(
            'reasonPhrase'=> $response->getReasonPhrase(),
            'statusCode'=>$response->getStatusCode(),
            'bodyContent'=> $response->getBody()->getContents()
        );




        return $parsed_response;

    }





}