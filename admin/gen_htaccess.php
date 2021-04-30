<?php


    ob_start();

    if ( !defined('K_ENGINE_DIR') ) define( 'K_ENGINE_DIR', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/') );
    require_once( K_ENGINE_DIR.'header.php' );
    header( 'Content-Type: text/plain; charset='.K_CHARSET );
    header( 'Content-Disposition: inline; filename=.htaccess' );

    define( 'K_ADMIN', 1 );

    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );


    echo $FUNCS->generate_rewrite_rules();
