<?php
/**
 * Created by PhpStorm.
 * User: flycart
 * Date: 19/3/18
 * Time: 11:58 AM
 */

namespace CampaignRabbit\WooAdmin\Callbacks;

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class CampaignRabbitCustomerSubmenuCallback extends \WP_List_Table {


    /**
     * Retrieve customer’s queue data from the database wp_options
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public function get_customers( $per_page = 5, $page_number = 1 ) {
        global $wpdb;
        $customers= $wpdb->get_results($wpdb->prepare( "SELECT * FROM {$wpdb->options} WHERE `option_name` LIKE %s", "%wp_customer%") );
        $data=array();
        foreach ($customers as $customer){
            $data[]=array(
                'data'=>$customer->option_name
            );
        }
        return $data;
    }

    public function display(){
        $customerTable = new CustomerTable();
        $customerTable->prepare_items();

        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"><br/></div>
            <h2><?php esc_html_e('Customers Queue','campaignrabbit-integration-for-woocommerce')?></h2>
            <form id="campaignrabbit-customers" method="get">
                <input type="hidden" name="page" value="<?php esc_attr_e( $_REQUEST['page'],'campaignrabbit-integration-for-woocommerce') ?>" />
                <?php $customerTable->display() ?>
            </form>

        </div>
        <?php

    }
}