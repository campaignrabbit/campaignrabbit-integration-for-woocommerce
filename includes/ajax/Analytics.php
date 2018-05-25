<?php


namespace CampaignRabbit\WooIncludes\Ajax;


class Analytics{

    public function __construct()
    {

    }

    public function getAppId(){

        echo get_option('app_id');
        exit();
    }

}