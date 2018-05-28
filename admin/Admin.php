<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

namespace CampaignRabbit\WooAdmin;
use CampaignRabbit\WooAdmin\Callbacks\CampaignRabbit_Admin_Menu_Callback;
use CampaignRabbit\WooAdmin\Callbacks\CampaignRabbit_Customer_Submenu_Callback;
use CampaignRabbit\WooAdmin\Callbacks\CampaignRabbit_Order_Submenu_Callback;
use CampaignRabbit\WooAdmin\Callbacks\CampaignRabbit_Product_Submenu_Callback;
use CampaignRabbit\WooAdmin\Callbacks\CampaignRabbitAdminMenuCallback;
use CampaignRabbit\WooAdmin\Callbacks\CampaignRabbitCustomerSubmenuCallback;
use CampaignRabbit\WooAdmin\Callbacks\CampaignRabbitOrderSubmenuCallback;
use CampaignRabbit\WooAdmin\Callbacks\CampaignRabbitProductSubmenuCallback;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/campaignrabbit-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//TODO load only in woocommerce products page (for validation)

        //enqueue all scripts if admin and posttype=cr_bogo_deals
        if($GLOBALS['campaignrabbit_page'] === $hook) {
            wp_enqueue_script('campaignrabbit-js', plugin_dir_url(__FILE__) . 'js/campaignrabbit-admin.js', array('jquery'), $this->version, false);

        }



	}

	/**
     * Admin menu
     */

	public function admin_custom_menu(){

         $menu_callback=new CampaignRabbitAdminMenuCallback();

        $GLOBALS['campaignrabbit_page']=add_menu_page(
            __( 'CampaignRabbit', 'campaignrabbit-integration-for-woocommerce' ),
            'CampaignRabbit',
            'manage_options',
            'campaignrabbit-admin.php',
            array($menu_callback, 'display'),
            'dashicons-email',
            25
        );
    }

    /**
     * Admin Submenus
     */
    public function admin_custom_submenu(){

//        $submenu_callback=new CampaignRabbitProductSubmenuCallback();
//
//        /*
//         * Products Page
//         */
//        $GLOBALS['campaignrabbit_products_page']=add_submenu_page(
//            'campaignrabbit-admin.php',
//        __('Products','campaignrabbit-integration-for-woocommerce'),
//            'Products',
//            'manage_options',
//            'campaignrabbit-products.php',
//            array($submenu_callback, 'display')
//        );
//
//       $submenu_callback=new CampaignRabbitCustomerSubmenuCallback();
//        /*
//         * Customers Page
//         */
//        $GLOBALS['campaignrabbit_customers_page']=add_submenu_page(
//            'campaignrabbit-admin.php',
//            __('Customers','campaignrabbit-integration-for-woocommerce'),
//            'Customers',
//            'manage_options',
//            'campaignrabbit-customers.php',
//            array($submenu_callback, 'display')
//        );
//
//        $submenu_callback=new CampaignRabbitOrderSubmenuCallback();
//        /*
//         * Orders Page
//         */
//        $GLOBALS['campaignrabbit_orders_page']=add_submenu_page(
//            'campaignrabbit-admin.php',
//            __('Orders','campaignrabbit-integration-for-woocommerce'),
//            'Orders',
//            'manage_options',
//            'campaignrabbit-orders.php',
//            array($submenu_callback, 'display')
//        );
    }

}
