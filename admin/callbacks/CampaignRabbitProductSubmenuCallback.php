<?php

namespace CampaignRabbit\WooAdmin\Callbacks;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class CampaignRabbitProductSubmenuCallback extends \WP_List_Table{

    public function get_products(){
        global $wpdb;
        $products= $wpdb->get_results($wpdb->prepare( "SELECT * FROM {$wpdb->options} WHERE `option_name` LIKE %s", "%wp_product%") );
        $data=array();
        foreach ($products as $product){
            $data[]=array(
                    'data'=>$product->option_value
            );
        }
        return $data;
    }

    public function display()
    {
        $productTable = new ProductTable();
        $productTable->prepare_items();
        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"><br/></div>
            <h2><?php esc_html_e('Products Queue','campaignrabbit-integration-for-woocommerce')?></h2>
            <form id="campaignrabbit-products" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <?php $productTable->display() ?>
            </form>
        </div>
        <?php
    }

}