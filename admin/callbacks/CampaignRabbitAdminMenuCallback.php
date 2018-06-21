<?php


namespace CampaignRabbit\WooAdmin\Callbacks;



use CampaignRabbit\WooIncludes\Api\Auth;

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


        $authenticated = (new Auth())->authenticate();
        $first_migrate = get_option('first_migrate');

        if ($authenticated) {
            if (!$first_migrate) {
                ?>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">

                    <input type="hidden" name="action" value="initial_bulk_migrate"/>
                    <label><?php esc_html_e("Initiate the recurring migration of customers,products,orders from woocommerce to CampaignRabbit- "); ?></label>
                    <input name="woo_connect" id="woo_connect" class="button button-primary" type="submit"
                           value="<?php esc_attr_e("Migrate", "campaignrabbit-integration-for-woocommerce"); ?>"/>

                </form>

                <?php
            } else {
                if (wp_get_schedule('campaignrabbit_recurring_bulk_migration')) {
                    echo 'Data Migration initiated successfully';
                } else {
                    ?>

                    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">

                        <input type="hidden" name="action" value="initial_bulk_migrate"/>
                        <label><?php esc_html_e("Initiate the recurring migration of customers, products, orders from woocommerce to CampaignRabbit- ",'campaignrabbit-integration-for-woocommerce'); ?></label>
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