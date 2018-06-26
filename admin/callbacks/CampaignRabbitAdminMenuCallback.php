<?php


namespace CampaignRabbit\WooAdmin\Callbacks;

use CampaignRabbit\WooIncludes\Api\Auth;

class CampaignRabbitAdminMenuCallback
{

    public function display()
    {
        $this->displayAuth();
        if (isset($_GET['sync'])) {
            if ($_GET['sync']) {
                ?> <h4><?php esc_html_e('Sync Success', 'campaignrabbit-integration-for-woocommerce') ?></h4><?php
            } else {
                ?> <h4><?php esc_html_e('Sync Failed', 'campaignrabbit-integration-for-woocommerce') ?></h4><?php
            }
        }
        $authenticated = (new Auth())->authenticate();

        $first_migrate = get_option('first_migrate');
        if ($authenticated) {
            ?>  <br>
            <h2><?php esc_attr_e("CampaignRabbit Synchronization", "campaignrabbit-integration-for-woocommerce"); ?></h2>
            <?php
            $this->displayDisabledCron();
            if (!$first_migrate) {
                echo '<p style="color:red;">';
                esc_html_e('Synchronization Yet to be Initiated', 'campaignrabbit-integration-for-woocommerce');
                echo '</p>';
            } else {
                echo '<p style="color:green;">';
                esc_html_e('Initial Synchronization Success', 'campaignrabbit-integration-for-woocommerce');
                echo '</p><p style="color: #363b3f">';
            }
            if (wp_get_schedule('campaignrabbit_recurring_bulk_migration')) {
                $this->displayCronEvent();
            }
            $this->displaySync();
         //   $this->displayResync();
        } else {
            echo 'Not Authenticated';
        }
    }

    private function displayCronEvent()
    {
        $cron_timestamp = wp_next_scheduled('campaignrabbit_recurring_bulk_migration');
        $next_run = get_date_from_gmt(date('Y-m-d H:i:s', $cron_timestamp));
        $now = date('Y-m-d H:i:s');
        $diff = strtotime($next_run) - strtotime($now);
        if ($diff < 0) {
            ?>
            <h4><?php esc_html_e('Cron Event campaignrabbit_recurring_bulk_migration has ended. Please try again!', 'campaignrabbit-integration-for-woocommerce') ?></h4>
            <?php
        } else {
            $minutes = floor(($diff) / (60));
            $seconds = floor(($diff - $minutes * 60));
            ?>
            <h3><?php esc_html_e('Sync Every 5 minutes', 'campaignrabbit-integration-for-woocommerce') ?></h3>
            <h4><?php esc_html_e('Next Run: ' . $next_run . ' (' . $minutes . ' min ' . $seconds . ' sec )', 'campaignrabbit-integration-for-woocommerce') ?></h4>
            <?php
        }
    }

    private function displayDisabledCron()
    {
        if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
            echo '<p style="color:red;">';
            esc_html_e('The DISABLE_WP_CRON constant is set to true. WP-Cron spawning is disabled.', 'campaignrabbit-integration-for-woocommerce');
            echo '</p>';
        }
    }

    private function displaySync()
    {
        ?>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="initial_bulk_migrate"/>
            <label><?php esc_html_e("Initiate the recurring migration of customers,products,orders from woocommerce to CampaignRabbit- "); ?></label>
            <input name="woo_connect" id="woo_connect" class="button button-primary" type="submit"
                   value="<?php esc_attr_e("Sync", "campaignrabbit-integration-for-woocommerce"); ?>"/>
        </form>
        <?php
    }

    private function displayAuth()
    {
        ?>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="update_campaignrabbit_settings"/>
            <input type="hidden" name="hidden_api_token_flag" id="hidden_api_token_flag"
                   value="<?php echo get_option('api_token_flag') ?>"/>
            <h2><?php esc_attr_e("CampaignRabbit Authentication Information", "campaignrabbit-integration-for-woocommerce"); ?></h2>
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
    }

    private function displayResync()
    {
                /*
                * TODO
                * Resync

                * 2. delete all data from wp_options aka queue
                *
                * 4. set first_migrate to zero
                */
        ?>
        <br>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="resync_migration"/>
            <label><?php esc_html_e("Re-Sync the recurring migration of customers,products,orders from woocommerce to CampaignRabbit- "); ?></label>
            <input name="woo_connect" id="woo_connect" class="button button-primary" type="submit"
                   value="<?php esc_attr_e("Re-Sync", "campaignrabbit-integration-for-woocommerce"); ?>"/>
        </form>
        <?php
    }
}