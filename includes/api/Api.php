<?php

namespace CampaignRabbit\WooIncludes\Api;

class Api{

    public function update(){
        // Get the options that were sent
        $api_token = (!empty($_POST["api_token"])) ? $_POST["api_token"] : NULL;
        $app_id=(!empty($_POST["app_id"])) ? $_POST["app_id"] : NULL;

        // Validation would go here

        // Update the values
        update_option( "api_token", $api_token, TRUE );
        update_option( "app_id", $app_id, TRUE );


        // Redirect back to settings page
        // The ?page=github corresponds to the "slug"
        // set in the fourth parameter of add_submenu_page() above.
        $redirect_url = get_bloginfo("url") . "/wp-admin/admin.php?page=campaignrabbit-admin.php";
        header("Location: ".$redirect_url);
        exit;
    }
}

