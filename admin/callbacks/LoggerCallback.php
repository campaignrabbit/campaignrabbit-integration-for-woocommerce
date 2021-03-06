<?php

namespace CampaignRabbit\WooAdmin\Callbacks;

use CampaignRabbit\WooIncludes\Helper\FileHandler;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}


class LoggerCallback extends \WP_List_Table
{

    public function get_log($per_page = 5, $page_number = 1){
        //logger read each line and display in it
        $file_handler=new FileHandler();
        if(!$file_handler->exists()){
            $log_lines=array();
        }else{
            //read each line and save in array
            $log_lines = $file_handler->getAsArray();
        }
        $data = array();
        foreach ($log_lines as $log_line) {
            $data[] = array(
                'data' => $log_line,
            );
        }
        return $data;
    }

    public function display(){
        $loggerTable = new LoggerTable();
        $loggerTable->prepare_items();

        ?>
        <div class="wrap">
            <div id="icon-users" class="icon32"><br/></div>

            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <h2><?php esc_html_e('Response Log', 'campaignrabbit-integration-for-woocommerce') ?></h2>
                <input type="hidden" name="action" value="clear_log"/>
                <input name="woo_connect" id="woo_connect" class="button button-primary" type="submit"
                       value="<?php esc_attr_e("Clear Log", "campaignrabbit-integration-for-woocommerce"); ?>"/>
            </form>
            <form id="campaignrabbit-customers" method="get">
                <input type="hidden" name="page"
                       value="<?php esc_attr_e($_REQUEST['page'], 'campaignrabbit-integration-for-woocommerce') ?>"/>
                <?php $loggerTable->display() ?>
            </form>

        </div>
        <?php

    }
}
