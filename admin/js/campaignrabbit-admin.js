jQuery(document).ready(function ($) {


    'use strict';

    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
	 *
	 * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
	 *
	 * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */


    var options_api_token_flag = $('#hidden_api_token_flag').val();
    var post_api_token = $('#api_token').val();
    if (!post_api_token.trim() || options_api_token_flag!=true){
        //if post empty or api token is not correct
        $("#woo_connect").attr('value', 'Connect');
    } else {
        $("#woo_connect").attr('value', 'Reconnect');
    }






});
