<?php

namespace CampaignRabbit\WooIncludes\Helper;

class Site{


    public $base_uri;

    public $domain;

    public function __construct()
    {
        $this->domain=site_url();

        $this->base_uri='https://app.campaignrabbit.com/api/v1/';
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





}
