<?php

namespace CampaignRabbit\WooIncludes\Helper;

class Site{


    public $base_uri;

    public $domain;

    public $woo_version;

    public function __construct(){

        $this->domain=site_url();

        $this->base_uri='https://app.campaignrabbit.com/api/v1/';

        if ( defined( 'WOOCOMMERCE_VERSION' ) ) {
            $this->woo_version = WOOCOMMERCE_VERSION;
        }else{
            $this->woo_version= $this->wpbo_get_woo_version_number();
        }


    }

    /**
     * @return mixed
     */
    public function getBaseUri()
    {
        return $this->base_uri;
    }


    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    public function getWooVersion(){

        return $this->woo_version;

    }

    function wpbo_get_woo_version_number() {
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





}
