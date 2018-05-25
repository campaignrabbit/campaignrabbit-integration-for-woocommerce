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


        //TODO: get the queue data of orders

        global $wpdb;

        $sql = "SELECT * FROM $wpdb->posts WHERE post_type='shop_order'";

        $result = array();

        return $result;
    }


    public function display(){
        //Prepare Table of elements
        //Create an instance of our package class...
        $orderTable = new OrderTable();
        //Fetch, prepare, sort, and filter our data...
        $orderTable->prepare_items();

        ?>
        <div class="wrap">

            <div id="icon-users" class="icon32"><br/></div>
            <h2>Orders Migration Table</h2>



            <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
            <form id="campaignrabbit-orders" method="get">
                <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <!-- Now we can render the completed list table -->
                <?php $orderTable->display() ?>
            </form>

        </div>
        <?php
    }

}