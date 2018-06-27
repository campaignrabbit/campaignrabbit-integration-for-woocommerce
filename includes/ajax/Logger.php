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

    public function saveEnableLog(){

        $enable_log=isset($_POST['enable_log'])?$_POST['enable_log']:'';
        if($enable_log=='on'){
            update_option('cr_enable_log',true);
        }else{
            update_option('cr_enable_log',false);
        }
        wp_safe_redirect(add_query_arg('erased', true, admin_url() . 'admin.php?page=campaignrabbit-admin.php' ));

    }

}