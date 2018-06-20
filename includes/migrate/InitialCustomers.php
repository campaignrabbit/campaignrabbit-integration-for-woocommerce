<?php

namespace CampaignRabbit\WooIncludes\Migrate;

use CampaignRabbit\WooIncludes\Helper\Site;
use CampaignRabbit\WooIncludes\Lib\Customer;


/**
 * Class Initial_Customers
 */
class InitialCustomers extends \WP_Background_Process
{


    /**
     * @var string
     */
    protected $action = 'customers_initial_migrate_process';


    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task( $item ) {

        $site = new Site();
        $woo_version = $site->getWooVersion();
        $customer_v2_6=new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Customer();
        $customer_v3_0=new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Customer();

        if ($woo_version < 3.0) {
            $customer_body = $customer_v2_6->get($item); //2.6
        } else {
            $customer_body = $customer_v3_0->get($item); //3.0
        }
        $customer_api=new Customer(get_option('api_token'),get_option('app_id'));
        $customer_response=$customer_api->get($customer_body['email']);

        if($customer_response->code==404){
            $created=$customer_api->create($customer_body);
            error_log($created);
        }elseif ($customer_response->code==200){
            $email=$customer_response->body->data->email;
            $updated=$customer_api->update($email,$customer_body);
            error_log($updated);
        }else {
            error_log($customer_response);
        }

        return false;
    }

    /**
     * Complete
     *
     * Override if applicable, but ensure that the below actions are
     * performed, or, call parent::complete().
     */
    protected function complete() {
           parent::complete();

        // Show notice to user or perform some other arbitrary task...
    }


}