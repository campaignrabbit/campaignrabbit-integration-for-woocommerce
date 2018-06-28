<?php

namespace CampaignRabbit\WooAdmin\Callbacks;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class CampaignRabbitQueueSubmenuCallback extends \WP_List_Table{

    public function display()
    {
        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"><br/></div>
            <?php


            $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'customers';
            ?>
            <h2 class="nav-tab-wrapper">
                <a href="?page=campaignrabbit-queue.php&tab=customers" class="nav-tab <?php echo $active_tab == 'customers' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Customers','campaignrabbit-integration-for-woocommerce')?></a>
                <a href="?page=campaignrabbit-queue.php&tab=orders" class="nav-tab <?php echo $active_tab == 'orders' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e('Orders','campaignrabbit-integration-for-woocommerce')?></a>
            </h2>
            <form id="campaignrabbit-products" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <?php
                if($active_tab=='customers'){
                    $customerTable = new CustomerTable();
                    $customerTable->prepare_items();
                    $customerTable->display();
                }elseif ($active_tab=='orders'){
                    $orderTable = new OrderTable();
                    $orderTable->prepare_items();
                    $orderTable->display();
                }
                ?>
            </form>
        </div>
        <?php
    }

    public function get_orders($per_page = 5, $page_number = 1){
        global $wpdb;
        $orders= $wpdb->get_results($wpdb->prepare( "SELECT * FROM {$wpdb->options} WHERE `option_name` LIKE %s", "%wp_order%") );
        $data=array();
        foreach ($orders as $order){
            $data[]=array(
                'option_name'=>$order->option_name,
                'option_value'=>$order->option_value
            );
        }
        return $data;
    }

    public function get_customers( $per_page = 5, $page_number = 1 ) {
        global $wpdb;
        $customers= $wpdb->get_results($wpdb->prepare( "SELECT * FROM {$wpdb->options} WHERE `option_name` LIKE %s", "%wp_customer%") );
        $data=array();
        foreach ($customers as $customer){
            $data[]=array(
                'option_name'=>$customer->option_name,
                'option_value'=>$customer->option_value
            );
        }
        return $data;
    }

}