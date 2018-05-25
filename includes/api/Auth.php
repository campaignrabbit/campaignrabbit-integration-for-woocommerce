<?php
/**
 * Created by PhpStorm.
 * User: flycart
 * Date: 6/3/18
 * Time: 1:28 PM
 */

namespace CampaignRabbit\WooIncludes\Api;


use CampaignRabbit\WooIncludes\CampaignRabbit;
use GuzzleHttp\Client;

/**
 * Class Auth
 */
class Auth
{


    private $campaignrabbit;

    /**
     * Auth constructor.
     */
    public function __construct()
    {
        $this->campaignrabbit = new CampaignRabbit();
    }


    /**
     *
     */
    public function authenticate()
    {

        if(empty(get_option('api_token')) && empty(get_option('app_id'))){
            echo '<p style="color:red;">Authentication Fail: App Id and Api Token are required fields. Enter them and try again</p> ';
            return false;
        }

        if (!empty(get_option('api_token'))) {
            if (!empty(get_option('app_id'))) {

                try {
                    $client = new Client([
                        // Base URI is used with relative requests
                        'base_uri' => $this->campaignrabbit->get_base_uri(),
                    ]);

                    $response = $client->request('POST', 'user/store/auth', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . get_option('api_token'),
                            'Request-From-Domain' => $this->campaignrabbit->get_domain(),
                            'App-Id' => get_option('app_id'),
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json'

                        ]]);

                    if ($response->getStatusCode() == 200) {
                        echo '<p style="color:green;">';
                        esc_html_e('Authentication Success', 'campaignrabbit-integration-for-woocommerce');
                        echo '</p><p style="color: #363b3f">';
                        echo '</p> ';
                        update_option('api_token_flag', true);

                        return true;

                    }
                } catch (\Exception $e) {

                    $error_code = $e->getCode();
                    if ($error_code == 401) {
                        echo '<p style="color:red;">Authentication Fail: The Api Token and/or App Id provided is incorrect. Try again with a valid credentials</p> ';
                        echo "<p style='color:red;'>Error: " . $e->getResponse()->getBody() . "</p>";
                    } else {
                        echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
                    }

                    update_option('api_token_flag', false);

                    return false;

                }

            }else{
                echo '<p style="color:red;">Authentication Fail: App Id is a required field. Enter App Id and try again</p> ';
                return false;
            }
        }else{
            echo '<p style="color:red;">Authentication Fail: Api Token is a required field. Enter Api Token and try again</p> ';
            return false;
        }


        return false;
    }

}