<?php


/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */

namespace CampaignRabbit\WooIncludes;


use CampaignRabbit\WooAdmin\Admin;
use CampaignRabbit\WooIncludes\Ajax\Analytics;
use CampaignRabbit\WooIncludes\Ajax\InitialBulkMigrate;
use CampaignRabbit\WooIncludes\Api\Api;
use CampaignRabbit\WooIncludes\Api\Request;
use CampaignRabbit\WooIncludes\Endpoints\Authenticate;
use CampaignRabbit\WooIncludes\Event\Customer;
use CampaignRabbit\WooIncludes\Event\Order;
use CampaignRabbit\WooIncludes\Event\Product;

use CampaignRabbit\WooIncludes\Helper\Site;
use CampaignRabbit\WooIncludes\Migrate\InitialCustomers;
use CampaignRabbit\WooIncludes\Migrate\InitialOrders;
use CampaignRabbit\WooIncludes\Migrate\InitialProducts;
use CampaignRabbit\WooPublic\CampaignRabbitPublic;

class CampaignRabbit
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Plugin_Name_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */

    /**
     * The domain of the store owner
     *
     * @var
     */
    protected $domain;

    /**
     * The campaignrabbit uri
     *
     * @var
     */
    protected $base_uri;

    /**
     * Woocommerce Version
     */
    protected $woo_version;


    /**
     * CampaignRabbit constructor.
     */
    public function __construct()
    {

        if (defined('CAMPAIGNRABBIT_VERSION')) {
            $this->version = CAMPAIGNRABBIT_VERSION;
        } else {
            $this->version = '1.0.0';
        }

        if (defined('CAMPAIGNRABBIT_NAME')) {
            $this->plugin_name = CAMPAIGNRABBIT_NAME;
        } else {
            $this->plugin_name = 'campaignrabbit-integration-for-woocommerce';
        }
        do_action( 'woocommerce_loaded' );

        $this->load_dependencies();
        $this->set_locale();

        $this->define_public_hooks();

        $this->define_admin_hooks();

        //define ajax hooks

        $this->define_ajax_hooks();

        //campaignrabbit

        $this->define_event_hooks();

        //rest api hook init

        $this->define_rest_api_hook();


    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Plugin_Name_Loader. Orchestrates the hooks of the plugin.
     * - Plugin_Name_i18n. Defines internationalization functionality.
     * - Plugin_Name_Admin. Defines all hooks for the admin area.
     * - Plugin_Name_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        $this->loader = new Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new CampaignRabbiti18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Admin($this->get_plugin_name(), $this->get_version());
        $api = new Api();


        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        //admin menu

        $this->loader->add_action('admin_menu', $plugin_admin, 'admin_custom_menu');

        //admin submenus

        $this->loader->add_action('admin_menu', $plugin_admin, 'admin_custom_submenu');

        //admin post options update

        $this->loader->add_action('admin_post_update_campaignrabbit_settings', $api, 'update');


        /*
         * an interval of 5 minutes to the WP Cron schedules
         */
        $this->loader->add_filter('cron_schedules', $this, 'add_cron_recurrence_interval');

        global $initial_bulk_migrate_customers_process;

        $initial_bulk_migrate_customers_process = new InitialCustomers();

        global $initial_bulk_migrate_products_process;

        $initial_bulk_migrate_products_process = new InitialProducts();

        global $initial_bulk_migrate_orders_process;

        $initial_bulk_migrate_orders_process = new InitialOrders();

        /*
         *Recurring initial migration
         */

        $initial_bulk_migration = new InitialBulkMigrate();
        add_action('campaignrabbit_recurring_bulk_migration', array($initial_bulk_migration, 'execute'));


    }

    public function add_cors_http_header()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

    }

    function add_allowed_origins($origins)
    {
        $origins[] = 'http://192.168.1.9';
        $origins[] = 'http://192.168.1.6';
        $origins[] = 'http://localhost';
        return $origins;
    }

    /**
     * @param $schedules
     * @return mixed
     */
    public function add_cron_recurrence_interval($schedules)
    {

        $schedules['campaignrabbit_every_five_minutes'] = array(
            'interval' => 300,
            'display' => __('CampaignRabbit Every 5 Minutes', 'campaignrabbit-integration-for-woocommerce')
        );

        return $schedules;
    }

    public function add_rest_post_dispatch(\WP_REST_Response $result)
    {

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            $result->header('Access-Control-Allow-Headers', 'Authorization, Content-Type', true);
        }
        return $result;
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new CampaignRabbitPublic($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

        /*
         * Enable CORS
         */

        $this->loader->add_action('init', $this, 'add_cors_http_header');

        $this->loader->add_filter('allowed_http_origins', $this, 'add_allowed_origins');

        $this->loader->add_filter('rest_post_dispatch', $this, 'add_rest_post_dispatch');


    }

    /**
     * Register all of the hooks related to campaignrabbit events- Product, Customer and Order
     */
    private function define_event_hooks()
    {


        global $product_create_request;

        $product_create_request = new Product\ProductCreate();

        global $product_update_request;

        $product_update_request = new Product\ProductUpdate();

        global $product_delete_request;

        $product_delete_request = new Product\ProductDelete();

        global $product_restore_request;

        $product_restore_request = new Product\ProductRestore();

        /*
         * Product events
         */

        $product = new Product('product');

        // Create New Product

        $this->loader->add_action('added_post_meta', $product, 'create', 10, 4);

        // Update Existing Product


        //store old product sku in global variable

        $this->loader->add_action('save_post_product', $product, 'save_sku', 10, 3);

        $this->loader->add_action('updated_post_meta', $product, 'update', 10, 4); //gets triggered on product create. so we have to check if it already exist to trigger post

        //Delete Existing Product

        $this->loader->add_action('wp_trash_post', $product, 'delete', 1, 1);

        //Restore deleted product

        $this->loader->add_action('untrash_post', $product, 'restore', 10, 1);


        global $customer_create_request;

        $customer_create_request = new Customer\CustomerCreate();

        global $customer_update_request;

        $customer_update_request = new Customer\CustomerUpdate();

        global $customer_delete_request;

        $customer_delete_request = new Customer\CustomerDelete();

        /*
         * Customer alias User events
         */

        $customer = new Customer('customer');

        //create New Customer

        $this->loader->add_action('user_register', $customer, 'create', 10, 1);

        //Update Existing Customer

        $this->loader->add_action('profile_update', $customer, 'update', 10, 2);

        //Delete Existing Customer

        $this->loader->add_action('delete_user', $customer, 'delete', 10, 2);


        global $order_create_request;

        $order_create_request = new Order\OrderCreate();

        global $order_update_request;

        $order_update_request = new Order\OrderUpdate();

        global $order_trash_request;

        $order_trash_request = new Order\OrderTrash();

        /*
         * Order events
         */

        $order = new Order('order');

        //create new Order

        $this->loader->add_action('woocommerce_checkout_order_processed', $order, 'create', 10, 1);


        //update existing order

        $this->loader->add_action('woocommerce_order_status_changed', $order, 'update', 10, 3);


        //delete existing order
        $this->loader->add_action('wp_trash_post', $order, 'trash', 1, 1);


    }

    /**
     * Ajax Hooks
     */
    private function define_ajax_hooks()
    {

        global $initial_bulk_migrate_customers_process;

        $initial_bulk_migrate_customers_process = new InitialCustomers();

        global $initial_bulk_migrate_products_process;

        $initial_bulk_migrate_products_process = new InitialProducts();

        global $initial_bulk_migrate_orders_process;

        $initial_bulk_migrate_orders_process = new InitialOrders();

        $initial_bulk_migrate = new InitialBulkMigrate();


        $this->loader->add_action('admin_post_nopriv_initial_bulk_migrate', $initial_bulk_migrate, 'initiate', 10);
        $this->loader->add_action('admin_post_initial_bulk_migrate', $initial_bulk_migrate, 'initiate', 10);

        if (is_admin()) {

            $analytics = new Analytics();

            $this->loader->add_action('wp_ajax_analytics', $analytics, 'getAppId', 10);
            $this->loader->add_action('wp_ajax_nopriv_analytics', $analytics, 'getAppId', 10);


        }


    }


    /**
     * Rest API Initiate
     */

    private function define_rest_api_hook()
    {

        $auth = new Authenticate();

        $this->loader->add_action('rest_api_init', $auth, 'init');


    }


    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }


}