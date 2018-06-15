<?php

namespace CampaignRabbit\WooIncludes\Helper;

class Site{


    public $base_uri;

    public $domain;

    public $woo_version;

    public function __construct()
    {
        $this->domain=site_url();

        $this->base_uri='https://app.campaignrabbit.com/api/v1/';

        if ( defined( 'WOOCOMMERCE_VERSION' ) ) {
            $this->woo_version = WOOCOMMERCE_VERSION;
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





}
