<?php

namespace CampaignRabbit\WooIncludes\Ajax;

class InitialBulkMigrate
{

    protected $migrate_initial_customers;

    protected $migrate_initial_products;

    protected $migrate_initial_orders;


    function __construct(){
        do_action( 'woocommerce_loaded');
    }

    public function initiate(){
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

        $customers = $this->get_customers();
        $products = $this->get_products();
        $orders = $this->get_orders();

        foreach ($customers as $customer) {
            $this->migrate_initial_customers->push_to_queue($customer);  //Customers
        }

        $this->migrate_initial_customers->save()->dispatch();

        foreach ($products as $product) {
            $this->migrate_initial_products->push_to_queue($product);  //Products
        }

        $this->migrate_initial_products->save()->dispatch();

        foreach ($orders as $order_id) {

           $this->migrate_initial_orders->push_to_queue($order_id);  //Orders
        }

        $this->migrate_initial_orders->save()->dispatch();
        update_option('first_migrate',true);    //set the wp_options first_migrate to true


    }

    private function get_customers(){
        if ( get_option('api_token_flag')) {
            $users = get_users();
            return $users;
        }
        return array();
    }

    private function get_products(){
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

    private function get_orders(){
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