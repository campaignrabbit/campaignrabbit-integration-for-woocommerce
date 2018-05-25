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
     * Retrieve customer’s data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public function get_customers( $per_page = 5, $page_number = 1 ) {


        //TODO: get the queue data of customers

        $args = array(
            'role'=>'subscriber'
        );

        $customers=array();
        $result=array();
        foreach ($customers as $customer){
            $result[]=array(
              'ID'=>$customer->ID,
              'user_login'=>$customer->user_login,
              'user_email'=>$customer->user_email
            );
        }
        return $result;
    }


    public function display(){

        //Prepare Table of elements
        //Create an instance of our package class...
        $customerTable = new CustomerTable();
        //Fetch, prepare, sort, and filter our data...
        $customerTable->prepare_items();

        ?>
        <div class="wrap">

            <div id="icon-users" class="icon32"><br/></div>
            <h2><?php esc_html_e('Customers Migration Table','campaignrabbit-integration-for-woocommerce')?></h2>


            <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
            <form id="campaignrabbit-customers" method="get">
                <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                <input type="hidden" name="page" value="<?php esc_attr_e( $_REQUEST['page'],'campaignrabbit-integration-for-woocommerce') ?>" />
                <!-- Now we can render the completed list table -->
                <?php $customerTable->display() ?>
            </form>

        </div>
        <?php

    }
}