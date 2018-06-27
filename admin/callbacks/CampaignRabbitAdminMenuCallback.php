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
            <h2><?php esc_attr_e("Woocommerce Synchronization", "campaignrabbit-integration-for-woocommerce"); ?></h2>
            <?php
            $this->displayDisabledCron();
            if (!$first_migrate) {
                echo '<p style="color:red;">';
                esc_html_e('Synchronization Yet to be Initiated', 'campaignrabbit-integration-for-woocommerce');
                echo '</p>';
                $this->displaySync();
            } else {
                echo '<p style="color:green;">';
                esc_html_e('Initial Synchronization Success', 'campaignrabbit-integration-for-woocommerce');
                echo '</p><p style="color: #363b3f">';
                $this->displayResync();
            }
            if (wp_get_schedule('campaignrabbit_recurring_bulk_migration')) {
                $this->displayCronEvent();
            }


        } else {
            echo 'Not Authenticated';
        }
        $this->displayLogger();
    }

    private function displayCronEvent()
    {
        $cron_timestamp = wp_next_scheduled('campaignrabbit_recurring_bulk_migration');
        $next_run = get_date_from_gmt(date('Y-m-d H:i:s', $cron_timestamp));
        $now = get_date_from_gmt(date('Y-m-d H:i:s'));
        $diff = strtotime($next_run) - strtotime($now);

        if ($diff < 0) {
            $diff = abs($diff);
            $minutes = floor(($diff) / (60));
            $seconds = floor(($diff - $minutes * 60));
            ?>
            <table>
                <tbody>
                <tr>
                    <td>
                        <br> <label
                                class="woo-label"><?php esc_attr_e("CRON:", "campaignrabbit-integration-for-woocommerce"); ?></label>
                    </td>
                    <td>
                        <br><input type="url" size="80" readonly name="api_token" id="api_token"
                                   value="<?php echo esc_attr_e('Terminated', "campaignrabbit-integration-for-woocommerce"); ?>"/>
                    </td>

                </tr>
                <tr>
                    <td>
                        <br> <label
                                class="woo-label"><?php esc_attr_e("Last CRON Run:", "campaignrabbit-integration-for-woocommerce"); ?></label>
                    </td>
                    <td>
                        <br><input type="text" size="80" readonly name="last_run" id="last_run"
                                   value="<?php echo esc_attr_e($next_run . ' (' . $minutes . ' min ' . $seconds . ' sec ago)', "campaignrabbit-integration-for-woocommerce"); ?>"/>
                    </td>
                </tr>

                </tbody>
            </table>

            <?php
        } else {
            $minutes = floor(($diff) / (60));
            $seconds = floor(($diff - $minutes * 60));
            ?>
            <table>
                <tbody>
                <tr>
                    <td>
                        <br> <label
                                class="woo-label"><?php esc_attr_e("CRON:", "campaignrabbit-integration-for-woocommerce"); ?></label>
                    </td>
                    <td>
                        <br><input type="url" size="80" readonly name="api_token" id="api_token"
                                   value="<?php echo esc_attr_e('Every 5 minutes', "campaignrabbit-integration-for-woocommerce"); ?>"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <br> <label
                                class="woo-label"><?php esc_attr_e("Next Run:", "campaignrabbit-integration-for-woocommerce"); ?></label>
                    </td>
                    <td>
                        <br><input type="text" size="80" readonly name="next_run" id="next_run"
                                   value="<?php echo esc_attr_e($next_run . ' (' . $minutes . ' min ' . $seconds . ' sec )', "campaignrabbit-integration-for-woocommerce"); ?>"/>
                    </td>
                </tr>

                </tbody>
            </table>
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
        $execute_url = site_url() . '/wc-api/campaignrabbit_sync'
        ?>
        <table>
        <tbody>

        <tr>
            <td>
                <br> <label
                        class="woo-label"><?php esc_attr_e("Synchronization URL:", "campaignrabbit-integration-for-woocommerce"); ?></label>
            </td>
            <td>
                <br><input type="text" size="80" readonly name="sync_url" id="sync_url"
                           value="<?php echo esc_attr_e($execute_url, "campaignrabbit-integration-for-woocommerce"); ?>"/>
            </td>
        </tr>

        </tbody>
        </table><?php


    }

    private function displaySync()
    {
        ?>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="initial_bulk_migrate"/>
            <table>
                <tbody>
                <tr>
                    <td>
                        <br> <label
                                class="woo-label"><?php esc_attr_e("Sync:", "campaignrabbit-integration-for-woocommerce"); ?></label>
                    </td>
                    <td>
                        <br><input name="woo_sync" id="woo_sync" class="button button-primary" type="submit"
                                   value="<?php esc_attr_e("Sync", "campaignrabbit-integration-for-woocommerce"); ?>"/>
                    </td>
                </tr>

                </tbody>
            </table>
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
        ?>

        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="resync_migration"/>
            <table>
                <tbody>
                <tr>
                    <td>
                        <br> <label
                                class="woo-label"><?php esc_attr_e("Re-Sync:", "campaignrabbit-integration-for-woocommerce"); ?></label>
                    </td>
                    <td>
                        <br><input name="woo_resync" id="woo_resync" class="button button-primary" type="submit"
                                   value="<?php esc_attr_e("Re-Sync", "campaignrabbit-integration-for-woocommerce"); ?>"/>
                    </td>
                </tr>

                </tbody>
            </table>
        </form>
        <?php
    }

    private function displayLogger()
    {
        ?>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="enable_log"/>
            <table>
                <tbody>
                <tr>
                    <td>
                        <br> <label
                                class="woo-label"><?php esc_attr_e("Log:", "campaignrabbit-integration-for-woocommerce"); ?></label>
                    </td>
                    <td>
                        <br><input type='checkbox' name='enable_log'
                                   id='enable_log'
                            <?php get_option('cr_enable_log') ? print_r('checked') : print_r('') ?>><?php esc_html_e('Enable', 'campaignrabbit-integration-for-woocommerce') ?>
                    </td>

                </tr>
                <tr>
                    <td>
                        <br><br><input name="enable_log_save" id="enable_log_save" class="button button-primary" type="submit"
                                   value="<?php esc_attr_e("Save", "campaignrabbit-integration-for-woocommerce"); ?>"/>
                    </td>
                </tr>

                </tbody>
            </table>
        </form>
        <?php

    }
}