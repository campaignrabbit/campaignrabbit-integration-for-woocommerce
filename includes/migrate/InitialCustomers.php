<?php

namespace CampaignRabbit\WooIncludes\Migrate;

use CampaignRabbit\WooIncludes\Api\Request;
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

        //Check if Customer Exists- get_by_email- If Status is 404-> create req

        $customer_api= new Customer(get_option('api_token'),get_option('app_id'));

        $customer_response=$customer_api->create('GET','customer/get_by_email/'.$item['email'],'');

        $customer=$request->parseResponse($customer_response);
        if($customer['statusCode']==404){

            /*
             * Create Customer
             */

            $created=$request->request('POST','customer',json_encode($item));

        }else if ($customer['status']==200){

            /*
             * Update Customer
             */
            $id=json_decode($customer_response->getBody()->getContents(),true)['data']['id'];

            $uodated=$request->request('PUT','customer/'.$id,json_encode($item));

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