<?php

    ob_start();
    define( 'K_ADMIN', 1 );

    //if ( !defined('K_ENGINE_DIR') ) define( 'K_ENGINE_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR );
    if ( !defined('K_ENGINE_DIR') ) define( 'K_ENGINE_DIR', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/') );
    require_once( K_ENGINE_DIR.'header.php' );


    require( K_ENGINE_DIR. 'includes/securimage/securimage.php' );
    class securimage_ex extends securimage{
        var $captcha_num;
        function __construct(){
            parent::__construct();

            // get the control's name from querystring
            $this->captcha_num = intval($_GET['c']);
        }

        function saveData(){
            $_SESSION['securimage_code_value'.$this->captcha_num] = strtolower($this->code);
        }

        function validate(){
            return false;
        }
    }

    if( isset($_GET['c']) && is_numeric($_GET['c']) && !preg_match("/[^0-9]/", $_GET['c']) ){
        $img = new securimage_ex();
        $img->ttf_file = K_ENGINE_DIR. 'includes/securimage/AHGBold.ttf';
        $img->show();
    }
