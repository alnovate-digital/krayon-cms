<?php

    ob_start();
    // define( 'K_ADMIN', 1 );  // Can now be invoked from frond-end forms too

    if ( !defined('K_ENGINE_DIR') ) define( 'K_ENGINE_DIR', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/') );

    require_once( K_ENGINE_DIR.'header.php' );
    header( 'Content-Type: text/html; charset='.K_CHARSET );

    $AUTH->check_access( K_ACCESS_LEVEL_ADMIN, 1 );

    if( ($_GET['o'] == 'gallery') ){
        require( K_ENGINE_DIR. 'includes/plupload/upload.php' );
    }
    else{
        require( K_ENGINE_DIR. 'includes/fileuploader/connector.php' );
    }
