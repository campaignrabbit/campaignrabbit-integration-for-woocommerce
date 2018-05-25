<?php

class Test_WP_Simple_Plugin extends WP_UnitTestCase {

    public function test_constants () {
        $this->assertSame( 'campaignrabbit-integration-for-woocommerce', CAMPAIGNRABBIT_NAME );


    }
}