<?php

    if( !defined('KCFINDER_AUTHENTICATED') ){
        $k_engine_dir = str_replace( '\\', '/', dirname(dirname(dirname(dirname(realpath(__FILE__))))).'/' );
        if( !(file_exists($k_engine_dir . 'cms.php') && file_exists($k_engine_dir . 'header.php')) ){
            die( "The KCFinder folder should be properly placed inside your CMS installation's 'includes' folder." );
        }
        define( 'K_ENGINE_DIR', $k_engine_dir );
        require_once( K_ENGINE_DIR.'header.php' );

        if( !session_id() ) @session_start();

        // HOOK: kcfinder_alter_access
        $__kcfinder_allow_access = 0;
        $FUNCS->dispatch_event( 'kcfinder_alter_access', array(&$__kcfinder_allow_access) );

        if( $AUTH->user->access_level >= K_ACCESS_LEVEL_ADMIN || $__kcfinder_allow_access ){

            // check nonce
            $FUNCS->validate_nonce( 'kc_finder' );

            if( !isset($_SESSION['KCFINDER']) ){
                $_SESSION['KCFINDER'] = array();
            }

            // User has permission, so make sure KCFinder is not disabled!
            if( !isset($_SESSION['KCFINDER']['disabled']) ){
                $_SESSION['KCFINDER']['disabled'] = false;
            }

            $_SESSION['KCFINDER']['uploadURL'] = $Config['k_append_url'] . $Config['UserFilesPath'];
            $_SESSION['KCFINDER']['uploadDir'] = $Config['UserFilesAbsolutePath'];
        }
        else{
            //unset( $_SESSION['KCFINDER'] );
            ob_end_clean();
            die();
        }

        define( 'KCFINDER_AUTHENTICATED', '1' );
    }

