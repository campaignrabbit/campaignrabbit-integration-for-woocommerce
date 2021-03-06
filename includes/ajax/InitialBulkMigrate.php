<?php

namespace CampaignRabbit\WooIncludes\Ajax;

use CampaignRabbit\WooIncludes\Helper\FileHandler;
use CampaignRabbit\WooIncludes\Helper\Site;

class InitialBulkMigrate
{

    protected $migrate_initial_customers;

    protected $migrate_initial_orders;


    function __construct(){
    }

    public function initiate(){
        if ( ! wp_next_scheduled( 'campaignrabbit_recurring_bulk_migration' ) ) {
            wp_schedule_event( time(), 'campaignrabbit_every_five_minutes', 'campaignrabbit_recurring_bulk_migration' );
        }
        wp_redirect(admin_url() . 'admin.php?page=campaignrabbit-admin.php');
    }

    public function execute(){

        $file_handler= new FileHandler();
        $file_handler->erase();

        error_log('Executed');
        $file_handler->append('Data Migration Started');

        global $initial_bulk_migrate_customers_process;
        $this->migrate_initial_customers=$initial_bulk_migrate_customers_process;

        global $initial_bulk_migrate_orders_process;
        $this->migrate_initial_orders=$initial_bulk_migrate_orders_process;

        $customers = $this->get_customers();
        $orders = $this->get_orders();

        foreach ($customers as $customer) {
            $site = new Site();
            $woo_version = $site->getWooVersion();

            if ($woo_version < 3.0) {
                $customer_v2_6=new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Customer();
                $customer_body = $customer_v2_6->get($customer); //2.6
            } else {
                $customer_v3_0=new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Customer();
                $customer_body = $customer_v3_0->get($customer); //3.0
            }
            $this->migrate_initial_customers->push_to_queue($customer_body);  //Customers
        }

        $this->migrate_initial_customers->save()->dispatch();


        foreach ($orders as $order_id) {
            $site = new Site();

            $woo_version = $site->getWooVersion();

            if ($woo_version < 3.0) {
                $order_v2_6=new \CampaignRabbit\WooIncludes\WooVersion\v2_6\Order();
                $order_body = $order_v2_6->get($order_id); //2.6
                $order_status=$order_v2_6->getWooStatus($order_id);
            } else {
                $order_v3_0=new \CampaignRabbit\WooIncludes\WooVersion\v3_0\Order();
                $order_body = $order_v3_0->get($order_id); //3.0
                $order_status=$order_v3_0->getWooStatus($order_id);
            }
            if(!empty($order_body)) {
                $order_data = array(
                    'order_id' => $order_id,
                    'order_body' => $order_body,
                    'order_status' => $order_status,
                    'api_token' => get_option('api_token'),
                    'app_id' => get_option('app_id')

                );
                if ($order_status != 'auto-draft') {
                    $this->migrate_initial_orders->push_to_queue(json_encode($order_data));  //Orders
                }

            }

        }

        $this->migrate_initial_orders->save()->dispatch();
        update_option('first_migrate',true);    //set the wp_options first_migrate to true
        error_log('Execution Success');
        $file_handler->append('Data Migration Completed');
        return true;

    }

    public function reSync(){
        $first_migrate=update_option('first_migrate',false);   //update first migrate and set to 0
        //delete wp_order, wp_customer, wp_product
        global $wpdb;
        $delete_order_query = "DELETE FROM $wpdb->options WHERE option_name LIKE '%wp_order%'";
        $order_queue_deleted=$wpdb->query($delete_order_query);
        $delete_customer_query = "DELETE FROM $wpdb->options WHERE option_name LIKE '%wp_customer%'";
        $customer_queue_deleted=$wpdb->query($delete_customer_query);
        wp_safe_redirect(add_query_arg('first_migrate', $first_migrate, admin_url() . 'admin.php?page=campaignrabbit-admin.php' ));
    }

    public function get_customers(){
        if ( get_option('api_token_flag')) {
            $users = get_users();
            return $users;
        }
        return array();
    }

    public function get_orders(){
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