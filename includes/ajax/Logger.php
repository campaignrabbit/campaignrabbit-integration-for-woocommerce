<?php

namespace CampaignRabbit\WooIncludes\Ajax;


use CampaignRabbit\WooIncludes\Helper\FileHandler;

class Logger
{
    private $file_handler;

    public function __construct()
    {
        $this->file_handler=new FileHandler();

    }

    public function clearLog(){

        $this->file_handler->erase();
        wp_safe_redirect(add_query_arg('erased', true, admin_url() . 'admin.php?page=campaignrabbit-logger.php' ));

    }

}