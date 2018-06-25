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

        $customer_api=new Customer(get_option('api_token'),get_option('app_id'));
        $customer_response=$customer_api->get($item['email']);

        if($customer_response->code==404){
            $created=$customer_api->create($item);
            error_log('Customer Created: '. $created->raw_body);
        }elseif ($customer_response->code==200){
            error_log('Update Customer Data: '.print_r($item,true));
            $email=$customer_response->body->data->email;
            $updated=$customer_api->update($email,$item);
            error_log('Customer Updated: '.$updated->raw_body);
        }else {
            error_log('Customer Migrate Error: '.$customer_response->raw_body);
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