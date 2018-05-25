<?php

namespace CampaignRabbit\WooIncludes\Migrate;
use CampaignRabbit\WooIncludes\Api\Request;


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
    protected function task( $item ) {

        $woo_product = wc_get_product($item);

        $meta_array = array(array(
            'meta_key' => 'dummy_key',
            'meta_value' => 'dummy_value',
            'meta_options' => 'dummy_options'
        ));

        if ($woo_product->is_type('simple')) {

            //simple product

            $post_product = array(
                'r_product_id' => $woo_product->get_id(),
                'sku' => $woo_product->get_sku(),
                'product_name' => $woo_product->get_title(),
                'product_price' => $woo_product->get_price(),
                'parent_id' => $woo_product->get_parent_id(),
                'meta' => $meta_array

            );

            $json_body = json_encode($post_product);
            (new Request())->request('POST', 'product', $json_body);

        } else {

            //variable products

            $woo_variation_ids = $woo_product->get_children();

            foreach ($woo_variation_ids as $woo_variation_id) {

                $woo_variable_product = wc_get_product($woo_variation_id);

                $post_product = array(
                    'r_product_id' => $woo_variable_product->get_id(),
                    'sku' => $woo_variable_product->get_sku(),
                    'product_name' => $woo_variable_product->get_title(),
                    'product_price' => $woo_variable_product->get_price(),
                    'parent_id' => $woo_variable_product->get_parent_id(),
                    'meta' => $meta_array

                );

                $json_body = json_encode($post_product);
                (new Request())->request('POST','product', $json_body);

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
    protected function complete() {
          parent::complete();

        // Show notice to user or perform some other arbitrary task...
    }


}