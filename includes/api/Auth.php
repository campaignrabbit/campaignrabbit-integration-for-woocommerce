<?php
/**
 * Created by PhpStorm.
 * User: flycart
 * Date: 6/3/18
 * Time: 1:28 PM
 */

namespace CampaignRabbit\WooIncludes\Api;


use CampaignRabbit\WooIncludes\CampaignRabbit;
use CampaignRabbit\WooIncludes\Helper\Site;
use CampaignRabbit\WooIncludes\Lib\Store;
use GuzzleHttp\Client;

/**
 * Class Auth
 */
class Auth
{


    private $site;

    private $store;

    /**
     * Auth constructor.
     */
    public function __construct()
    {
        $this->site = new Site();

        $this->store = new Store(get_option('api_token'), get_option('app_id'));
    }

    
    public function authenticate()
    {

        if (empty(get_option('api_token')) && empty(get_option('app_id'))) {
            echo '<p style="color:red;">';
            esc_html_e('Authentication Fail: App Id and Api Token are required fields. Enter them and try again', 'campaignrabbit-integration-for-woocommerce');
            echo '</p>';
            return false;
        }

        if (!empty(get_option('api_token'))) {
            if (!empty(get_option('app_id'))) {
                $auth_response = $this->store->authenticate();
                if ($auth_response->code == 200) {
                    echo '<p style="color:green;">';
                    esc_html_e('Authentication Success', 'campaignrabbit-integration-for-woocommerce');
                    echo '</p><p style="color: #363b3f">';
                    echo '</p> ';
                    update_option('api_token_flag', true);
                    return true;

                } elseif ($auth_response->code == 401) {
                    echo '<p style="color:red;">';
                    esc_html_e('Authentication Fail: The Api Token and/or App Id provided is incorrect. Try again with a valid credentials', 'campaignrabbit-integration-for-woocommerce');
                    echo '</p>';
                    echo "<p style='color:red;'>";
                    esc_html_e('Error: ' . $auth_response->body->error, 'campaignrabbit-integration-for-woocommerce');
                    echo "</p>";
                    update_option('api_token_flag', false);
                    return false;
                } else {
                    echo "<p style='color:red;'>";
                    esc_html_e('Error: ' . $auth_response->raw_body, 'campaignrabbit-integration-for-woocommerce');
                    echo "</p>";
                    update_option('api_token_flag', false);
                    return false;
                }


            } else {
                echo '<p style="color:red;">';
                esc_html_e('Authentication Fail: App Id is a required field. Enter App Id and try again','campaignrabbit-integration-for-woocommerce');
                echo '</p>';
                update_option('api_token_flag', false);
                return false;
            }
        } else {
            echo '<p style="color:red;">';
            esc_html_e('Authentication Fail: Api Token is a required field. Enter Api Token and try again','campaignrabbit-integration-for-woocommerce');
            echo '</p>';
            update_option('api_token_flag', false);
            return false;
        }

    }

}