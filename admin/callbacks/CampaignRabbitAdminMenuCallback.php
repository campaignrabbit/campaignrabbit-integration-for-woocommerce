<?php


namespace CampaignRabbit\WooAdmin\Callbacks;

use CampaignRabbit\WooIncludes\Api\Auth;
use CampaignRabbit\WooIncludes\Helper\Site;
use CampaignRabbit\WooIncludes\WooVersion\v2_6\Customer;
use CampaignRabbit\WooIncludes\WooVersion\v2_6\Order;

class CampaignRabbitAdminMenuCallback
{

    public function display()
    {
        ?>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">

            <input type="hidden" name="action" value="update_campaignrabbit_settings"/>
            <input type="hidden" name="hidden_api_token_flag" id="hidden_api_token_flag"
                   value="<?php echo get_option('api_token_flag') ?>"/>
            <h3><?php esc_attr_e("CampaignRabbit Authentication Information", "campaignrabbit-integration-for-woocommerce"); ?></h3>
            <p>
                <label class="woo-label"><?php esc_attr_e("API Token:", "campaignrabbit-integration-for-woocommerce"); ?></label>
                <input type="text" size="80" name="api_token" id="api_token"
                       value="<?php echo get_option('api_token'); ?>"/>
            </p>
            <p>
                <label class="woo-label"><?php esc_attr_e("App ID:", "campaignrabbit-integration-for-woocommerce"); ?></label>
                <input type="text" size="80" name="app_id" id="app_id" value="<?php echo get_option('app_id'); ?>"/>
            </p>

            <input name="woo_connect" id="woo_connect" class="button button-primary" type="submit"
                   value="<?php esc_attr_e("Connect", "campaignrabbit-integration-for-woocommerce"); ?>"/>

        </form>
        <?php
        if (isset($_GET['sync'])) {

            if( $_GET['sync'] ){
                ?> <h4><?php esc_html_e('Sync Success', 'campaignrabbit-integration-for-woocommerce') ?></h4><?php
            }else{
                ?> <h4><?php esc_html_e('Sync Failed', 'campaignrabbit-integration-for-woocommerce') ?></h4><?php
            }

        }
        $authenticated = (new Auth())->authenticate();
        $first_migrate = get_option('first_migrate');
        if ($authenticated) {
            if (!$first_migrate) {
                if (wp_get_schedule('campaignrabbit_recurring_bulk_migration')) { ?>
                    <h3><?php esc_html_e('Sync Every 5 minutes', 'campaignrabbit-integration-for-woocommerce') ?></h3>
                    <?php
                    $cron_timestamp = wp_next_scheduled('campaignrabbit_recurring_bulk_migration');
                    $date = get_date_from_gmt(date('Y-m-d H:i:s', $cron_timestamp)); ?>
                    <h4><?php esc_html_e('Next Run: '.$date, 'campaignrabbit-integration-for-woocommerce') ?></h4>
                    <?php
                    /*
                     * TODO
                     * Resync
                     * 1. delete all cron events
                     * 2. delete all data from wp_options aka queue
                     * 3. reinitiate bulk migration
                     */

                }
                ?>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <input type="hidden" name="action" value="initial_bulk_migrate"/>
                    <label><?php esc_html_e("Initiate the recurring migration of customers,products,orders from woocommerce to CampaignRabbit- "); ?></label>
                    <input name="woo_connect" id="woo_connect" class="button button-primary" type="submit"
                           value="<?php esc_attr_e("Sync", "campaignrabbit-integration-for-woocommerce"); ?>"/>
                </form>
                <?php
            } else {
                if (wp_get_schedule('campaignrabbit_recurring_bulk_migration')) { ?>
                    <h3><?php esc_html_e('Sync Every 5 minutes', 'campaignrabbit-integration-for-woocommerce') ?></h3>
                    <?php
                    $cron_timestamp = wp_next_scheduled('campaignrabbit_recurring_bulk_migration');
                    $date = get_date_from_gmt(date('Y-m-d H:i:s', $cron_timestamp)); ?>
                    <h4><?php esc_html_e('Next Run: '.$date, 'campaignrabbit-integration-for-woocommerce') ?></h4>
                    <?php
                    /*
                     * TODO
                     * Resync
                     * 1. delete all cron events
                     * 2. delete all data from wp_options aka queue
                     * 3. reinitiate bulk migration
                     */
                } else {
                    ?>
                    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                        <input type="hidden" name="action" value="initial_bulk_migrate"/>
                        <label><?php esc_html_e("Initiate the recurring migration of customers, products, orders from woocommerce to CampaignRabbit- ", 'campaignrabbit-integration-for-woocommerce'); ?></label>
                        <input name="woo_connect" id="woo_connect" class="button button-primary" type="submit"
                               value="<?php esc_attr_e("Sync", "campaignrabbit-integration-for-woocommerce"); ?>"/>
                    </form>
                    <?php
                }
            }
        } else {
            echo 'Not Authenticated';
        }
    }
}