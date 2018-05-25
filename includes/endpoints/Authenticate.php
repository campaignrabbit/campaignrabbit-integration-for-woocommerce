<?php

namespace CampaignRabbit\WooIncludes\Endpoints;

class Authenticate extends \WP_REST_Controller {


    //The namespace and version for the REST SERVER
    protected $my_namespace = 'campaignrabbit/v';
    protected $my_version   = '1';


    public function init(){

        $namespace = $this->my_namespace . $this->my_version;
        $base      = 'auth';



        register_rest_route( $namespace, '/' . $base, array(
            array(
                'methods'         => \WP_REST_Server::READABLE,
                'callback'        => array( $this, 'test' ),
           ),
            array(
                'methods'         => \WP_REST_Server::CREATABLE,
                'callback'        => array( $this, 'is_store_set' ),
            )
        )  );

    }

    public function test(){

        return 'works :)';
    }

    public function is_store_set( \WP_REST_Request $request ){

        $remote_params= $request->get_body_params();

        $remote_app_id=$remote_params['app-id'];


        if(!isset($remote_app_id)){
            return new \WP_Error('Fail','Provide app-id',array('status'=>401));
        }

        if(get_option('app_id')==$remote_app_id){
            if(get_option('api_token_flag')){
                return array('store_set'=>true);
            }else{
                return new \WP_Error('Fail','API Token Error',array('status'=>401));
            }
        }else{
            return new \WP_Error('Fail','Incorrect App Id',array('status'=>401));
        }


    }


}
