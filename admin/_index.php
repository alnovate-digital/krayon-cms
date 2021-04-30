<?php

    ob_start();
    define( 'K_ADMIN', 1 );

    if ( !defined('K_ENGINE_DIR') ) define( 'K_ENGINE_DIR', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/') );
    require_once( K_ENGINE_DIR.'header.php' );
    header( 'Content-Type: text/html; charset='.K_CHARSET );

    // sabotage credentials
    $cookie_name = 'k_engine_'. md5( K_SITE_URL );
    if( $_COOKIE[$cookie_name] ){
        unset( $_COOKIE[$cookie_name] );
        $AUTH->delete_cookie();
    }

    // authenticate if you can :)
    $AUTH = new KAuth( K_ACCESS_LEVEL_ADMIN );
