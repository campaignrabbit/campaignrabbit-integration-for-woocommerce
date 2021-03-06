<?php

/**
 * Plugin Name:       CampaignRabbit Integration For WooCommerce
 * Description:       To intergrate campaignRabbit and woocommerce
 * Version:           1.1.2
 * Author:            Cartrabbit
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       campaignrabbit-integration-for-woocommerce
 * slug:              campaignrabbit-integration-for-woocommerce
 * Domain Path:       /languages
 */


// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


/**
 * Composer autoload
 */

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Current plugin version.
 */
define('CAMPAIGNRABBIT_VERSION', '1.1.2');
define('CAMPAIGNRABBIT_NAME', 'campaignrabbit-integration-for-woocommerce');

/**
 * The code that runs during plugin activation.
 */
function activate_campaignrabbit(){
    \CampaignRabbit\WooIncludes\Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_campaignrabbit(){
    \CampaignRabbit\WooIncludes\Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_campaignrabbit');
register_deactivation_hook(__FILE__, 'deactivate_campaignrabbit');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.1.0
 */
function run_campaignrabbit(){
    /**
     * Check if WooCommerce is active
     **/
    if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ||
        in_array('woocommerce-2.6.0/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) ||
        in_array('woocommerce-2.6.14/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))
    ) {
        $plugin = new \CampaignRabbit\WooIncludes\CampaignRabbit();
        $plugin->run();
        error_log('Yay! CampaignRabbit Integration Plugin Installed!');

    } else {
        ?>
        <script>
            alert('Please install and Activate WooCommerce.')
        </script>
        <?php
        error_log('Ops! CampaignRabbit Integration Plugin Installation Failed');
    }
}

add_action('wp_loaded',function (){
    run_campaignrabbit();
},0);



