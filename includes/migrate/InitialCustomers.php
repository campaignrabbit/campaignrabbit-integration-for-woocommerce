<?php

namespace CampaignRabbit\WooIncludes\Migrate;

use CampaignRabbit\WooIncludes\Api\Request;


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


        $meta_array = array(array(
            'meta_key' => 'dummy_key',
            'meta_value' => 'dummy_value',
            'meta_options' => 'dummy_options'
        ));

        $post_customer = array(
            'email' =>$item->user_email,
            'name' =>$item->user_login,
            'meta' => $meta_array

        );

        $json_body= \GuzzleHttp\json_encode($post_customer);

        (new Request())->request('POST', 'customer', $json_body);

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