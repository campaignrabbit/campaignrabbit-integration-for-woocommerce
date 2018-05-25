<?php

// change the next line to points to your wordpress dir
define( 'ABSPATH', '/var/www/html/dev-prash/wordpress-campaignrabbit/' );

define( 'WP_DEBUG', false );

// WARNING WARNING WARNING!
// tests DROPS ALL TABLES in the database. DO NOT use a production database

define( 'DB_NAME', 'wordpress_unit_test' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', 'password' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

$table_prefix = 'wptests_'; // Only numbers, letters, and underscores please!

define( 'WP_TESTS_DOMAIN', 'localhost' );
define( 'WP_TESTS_EMAIL', 'rvprashanthi@gmail.com' );
define( 'WP_TESTS_TITLE', 'Test CampaignRabbit' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );