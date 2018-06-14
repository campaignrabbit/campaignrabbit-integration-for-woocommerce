<?php

/**
 * Plugin Name:       CampaignRabbit Integration For WooCommerce
 * Description:       To intergrate campaignRabbit and woocommerce
 * Version:           1.1.0
 * Author:            Cartrabbit
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       campaignrabbit-integration-for-woocommerce
 * slug:              campaignrabbit-integration-for-woocommerce
 * Domain Path:       /languages
 */
header("Access-Control-Allow-Origin: *");

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*
 * To avoid being called directly
 */

defined('ABSPATH') or die('Cannot Access this File'); // Exit if accessed directly


/*
 * Composer autoload
 */

require_once __DIR__.'/vendor/autoload.php';



/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CAMPAIGNRABBIT_VERSION', '1.1.0' );
define('CAMPAIGNRABBIT_NAME','campaignrabbit-integration-for-woocommerce');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_campaignrabbit() {

    \CampaignRabbit\WooIncludes\Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_campaignrabbit() {

    \CampaignRabbit\WooIncludes\Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_campaignrabbit' );
register_deactivation_hook( __FILE__, 'deactivate_campaignrabbit' );

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
 * @since    1.0.0
 */



function run_campaignrabbit() {


    /**
     * Check if WooCommerce is active
     **/
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

        /*
         * Get Woocommerce version and define it in global variable
         */

        define('WOOCOMMERCE_VERSION',cr_get_woo_version_number());

        $plugin = new \CampaignRabbit\WooIncludes\CampaignRabbit();
        $plugin->run();

    }else{
        wp_die( __( 'Please install and Activate WooCommerce.', 'woocommerce-addon-slug' ), 'Plugin dependency check', array( 'back_link' => true ) );
    }


}

function cr_get_woo_version_number() {

    // If get_plugins() isn't available, require it
    if ( ! function_exists( 'get_plugins' ) )
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    // Create the plugins folder and file variables
    $plugin_folder = get_plugins( '/' . 'woocommerce' );
    $plugin_file = 'woocommerce.php';

    // If the plugin version number is set, return it
    if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
        return $plugin_folder[$plugin_file]['Version'];

    } else {
        // Otherwise return null
        return NULL;
    }
}



run_campaignrabbit();


