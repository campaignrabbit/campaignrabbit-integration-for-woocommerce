<?php

namespace CampaignRabbit\WooIncludes\Ajax;


use CampaignRabbit\WooIncludes\Helper\Site;
use CampaignRabbit\WooIncludes\Lib\Customer;
use CampaignRabbit\WooIncludes\Lib\Order;
use CampaignRabbit\WooIncludes\Lib\Product;



class InitialBulkMigrate
{

    protected $migrate_initial_customers;

    protected $migrate_initial_products;

    protected $migrate_initial_orders;


    function __construct()
    {

    }



    public function initiate()
    {

        if ( ! wp_next_scheduled( 'campaignrabbit_recurring_bulk_migration' ) ) {
            wp_schedule_event( time(), 'campaignrabbit_every_five_minutes', 'campaignrabbit_recurring_bulk_migration' );
        }

        wp_redirect(admin_url() . 'admin.php?page=campaignrabbit-admin.php');

    }

    public function execute(){

        global $initial_bulk_migrate_customers_process;

        $this->migrate_initial_customers=$initial_bulk_migrate_customers_process;

        global $initial_bulk_migrate_products_process;

        $this->migrate_initial_products=$initial_bulk_migrate_products_process;

        global $initial_bulk_migrate_orders_process;

        $this->migrate_initial_orders=$initial_bulk_migrate_orders_process;
        //get all details

        $customers = $this->get_customers();

        $products = $this->get_products();

        $orders = $this->get_orders();


        //iterate through them and start the cron and queue(wp_bg-process)

        /*
         * Customers
         */


        foreach ($customers as $customer) {

            $roles=array();
            $user = get_userdata( $customer->ID );
            foreach ($user->roles as $customer_role){

                $roles[]=array(
                    'meta_key'=>'CUSTOMER_GROUP',
                    'meta_value'=>$customer_role,
                    'meta_options'=>''
                );
            }

            $post_customer = array(

                'email' =>$customer->user_email,
                'name' =>$customer->user_login,
                'meta' => $roles

            );

           $this->migrate_initial_customers->push_to_queue($post_customer);
        }

        $this->migrate_initial_customers->save()->dispatch();


        /*
         * Products
         */

        foreach ($products as $product) {
            $this->migrate_initial_products->push_to_queue($product);
        }


        $this->migrate_initial_products->save()->dispatch();

        /*
         * Orders
         */


        foreach ($orders as $order_id) {


           $this->migrate_initial_orders->push_to_queue($order_id);
        }


        $this->migrate_initial_orders->save()->dispatch();

        //set the wp_options first_migrate to true

        update_option('first_migrate',true);


    }

    private function get_customers()
    {


        if ( get_option('api_token_flag')) {


            $users = get_users();

            return $users;


        }

        return array();
    }

    private function get_products()
    {


        if ( get_option('api_token_flag')) {

            $args=array(
                'post_type'      => 'product',
                'posts_per_page' => -1,

            );
            $products = get_posts($args);

            $product_ids=wp_list_pluck($products,'ID');

            return $product_ids;


        }

        return array();

    }

    private function get_orders()
    {



        if ( get_option('api_token_flag')) {
            global $wpdb;

            $sql = "SELECT * FROM $wpdb->posts WHERE post_type='shop_order'";

            $orders = $wpdb->get_results( $sql);

            $order_ids=wp_list_pluck($orders,'ID');

            return $order_ids;


        }

        return array();
    }

}