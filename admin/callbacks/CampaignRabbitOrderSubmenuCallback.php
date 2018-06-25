<?php
/**
 * Created by PhpStorm.
 * User: flycart
 * Date: 19/3/18
 * Time: 11:59 AM
 */

namespace CampaignRabbit\WooAdmin\Callbacks;

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class CampaignRabbitOrderSubmenuCallback extends \WP_List_Table {

    public function get_orders($per_page = 5, $page_number = 1){
        global $wpdb;
        $orders= $wpdb->get_results($wpdb->prepare( "SELECT * FROM {$wpdb->options} WHERE `option_name` LIKE %s", "%wp_order%") );
        $data=array();
        foreach ($orders as $order){
            $data[]=array(
                'data'=>$order->option_name
            );
        }
        return $data;
    }


    public function display(){
        $orderTable = new OrderTable();
        $orderTable->prepare_items();
        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"><br/></div>
            <h2><?php esc_html_e('Orders Queue','campaignrabbit-integration-for-woocommerce')?></h2>
            <form id="campaignrabbit-orders" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <?php $orderTable->display() ?>
            </form>

        </div>
        <?php
    }

}