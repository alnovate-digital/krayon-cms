<?php

    ob_start();

    if ( !defined('K_ENGINE_DIR') ) define( 'K_ENGINE_DIR', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/') );
    $get = isset( $_GET['redirect'] ) ? $_GET['redirect'] : null; // get it before header.php sanitizes and converts '&' to '&amp;';
    require_once( K_ENGINE_DIR.'header.php' );
    $_GET['redirect'] = $get; // can bypass sanitization because we'll sanitize URL ourselves later on.

    $default_dest = ( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) ? K_SITE_URL : K_ADMIN_URL . K_ADMIN_PAGE;
    $dest = isset($_GET['redirect']) ? $_GET['redirect'] : $default_dest;

    if( $AUTH->user->id != -1 ){ // if user logged-in
        // check if logout requested
        if( isset($_GET['act'][0]) && $_GET['act'] == 'logout' ){
            $AUTH->logout();
        }
        $AUTH->redirect( $dest );
    }
    else{
        // login
        if( $_POST['k_login'] ){
            $res = $AUTH->login();

            if( !$FUNCS->is_error($res) ){
                $AUTH->redirect( $dest );
            }
        }

        $AUTH->show_login( $res );
    }
