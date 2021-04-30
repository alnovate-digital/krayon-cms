<?php

    if ( !defined('K_ENGINE_DIR') ) die(); // cannot be loaded directly

    class KGrayscale{

        function grayscale_handler( $params, $node ){
            global $FUNCS, $Config;

            extract( $FUNCS->get_named_vars(
                array(
                    'src'=>'',
                ),
                $params)
            );
            $src = trim( $src );
            if( !$src ) return;

            // Make sure the source image lies within our upload image folder
            $domain_prefix = $Config['k_append_url'] . $Config['UserFilesPath'] . 'image/';
            if( strpos($src, $domain_prefix)===0 ){ // process image only if local
                $orig_src =  $src;
                $src = substr( $src, strlen($domain_prefix) );
                if( $src ){
                    $src = $Config['UserFilesAbsolutePath'] . 'image/' . $src;

                    $image = KGrayscale::_create_grayscale_image( $src );
                    if( $FUNCS->is_error($image) ){
                        return 'ERROR: ' . $image->err_msg;
                    }

                    // return URL of the grayscale version
                    $path_parts = $FUNCS->pathinfo( $orig_src );
                    return  $path_parts['dirname'] . '/' . $image;
                }
            }
            else{
                return 'ERROR: Can only create grayscale version of images that are found within or below '. $domain_prefix;
            }

            return $html;

        }

        function _create_grayscale_image( $src ){
            global $FUNCS;

            if( file_exists($src) ){

                // check if grayscale version already exists
                $path_parts = $FUNCS->pathinfo( $src );
                $name = $path_parts['filename'] . '-grayscale.' . $path_parts['extension'];
                $dest = $path_parts['dirname'] . '/' . $name;
                if( file_exists($dest) ){
                    return $name;
                }

                require_once( K_ENGINE_DIR.'includes/timthumb.php' );

                @ini_set( 'memory_limit', "128M" );

                // get mime type of src
                $mime_type = mime_type( $src );

                if( !valid_src_mime_type($mime_type) ){
                    return displayError( "Invalid src mime type: " .$mime_type );
                }

                // open image
                $image = open_image( $mime_type, $src );
                if( $image === false ){
                    return displayError( 'Unable to open image : ' . $src );
                }

                if( imagefilter($image, IMG_FILTER_GRAYSCALE) ){
                    // save the grayscale version
                    save_image( $mime_type, $image, $dest, 100 );
                }
                else{
                    return displayError( 'Conversion to grayscale failed.' );
                }

                imagedestroy( $image );
                return $name;

            }
            else{
                return displayError( "image " . $src . " not found" );
            }

        }

    }

    $FUNCS->register_tag( 'grayscale', array('KGrayscale', 'grayscale_handler') );
