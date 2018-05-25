<?php

namespace CampaignRabbit\WooAdmin\Callbacks;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class CampaignRabbitProductSubmenuCallback extends \WP_List_Table
{


    public function get_products()
    {

        //TODO: get the queue data of products


        $products= array();

        //seperate products as variations and pass it as result

        return $products;

    }


    public function display()
    {
        //Prepare Table of elements
        //Create an instance of our package class...
        $productTable = new ProductTable();
        //Fetch, prepare, sort, and filter our data...
        $productTable->prepare_items();

        ?>
        <div class="wrap">

            <div id="icon-users" class="icon32"><br/></div>
            <h2>Products Migration Table</h2>


            <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
            <form id="campaignrabbit-products" method="get">
                <!-- For plugins, we also need to ensure that the form posts back to our current page -->
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <!-- Now we can render the completed list table -->
                <?php $productTable->display() ?>
            </form>

        </div>
        <?php
    }

}