<?php

namespace CampaignRabbit\WooIncludes\Migrate;

use CampaignRabbit\WooIncludes\Api\Request;
use CampaignRabbit\WooIncludes\Helper\Site;


/**
 * Class Initial_Products
 */
class InitialProducts extends \WP_Background_Process
{


    /**
     * @var string
     */
    protected $action = 'products_initial_migrate_process';


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
    protected function task($item)
    {

        $woo_version = (new Site())->getWooVersion();


        if ($woo_version < 3.0) {

            /*
             * 2.6
             */

            $product = (new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Product())->get($item);


        } else {

            /*
             * 3.0
             */

            $product = (new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Product())->get($item);
        }

        if ($product['type'] == 'simple') {


            (new Request())->request('POST', 'product', json_encode($product['body']));


        } else {
            //variable products

            foreach ($product as $body) {


                (new Request())->request('POST', 'product', json_encode($body['body']));


            }


        }

        return false;
    }

    /**
     * Complete
     *
     * Override if applicable, but ensure that the below actions are
     * performed, or, call parent::complete().
     */
    protected function complete()
    {
        parent::complete();

        // Show notice to user or perform some other arbitrary task...
    }


}