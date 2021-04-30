<?php

    ob_start();

    if ( !defined('K_ENGINE_DIR') ) define( 'K_ENGINE_DIR', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/') );
    require_once( K_ENGINE_DIR.'header.php' );

    define( 'K_ADMIN', 1 );

    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( '<h3>Please login as admin.</h3>' );

    $tbls = array();
    $tbls[K_TBL_TEMPLATES] = 'K_TBL_TEMPLATES';
    $tbls[K_TBL_FIELDS] = 'K_TBL_FIELDS';
    $tbls[K_TBL_PAGES] = 'K_TBL_PAGES';
    $tbls[K_TBL_FOLDERS] = 'K_TBL_FOLDERS';
    $tbls[K_TBL_DATA_TEXT] = 'K_TBL_DATA_TEXT';
    $tbls[K_TBL_DATA_NUMERIC] = 'K_TBL_DATA_NUMERIC';
    $tbls[K_TBL_FULLTEXT] = 'K_TBL_FULLTEXT';
    $tbls[K_TBL_COMMENTS] = 'K_TBL_COMMENTS';
    $tbls[K_TBL_RELATIONS] = 'K_TBL_RELATIONS';

    /* output header */
    header( "Expires: Fri, 01 Jan 1990 00:00:00 GMT" );
    header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
    header( "Pragma: no-cache" );
    header( "Content-Type: application/octet-stream" );
    header( "Content-Disposition: attachment; filename=install-ex.php" );
    echo '<';
    echo "?php\n";
    echo "if ( !defined('K_INSTALLATION_IN_PROGRESS') ) die(); // cannot be loaded directly\n";

    /* loop through each core table */
    @set_time_limit( 0 );
    foreach( $tbls as $tbl_name=>$tbl_alias ){
        echo "\n/* " . $tbl_name . " (";
        $result = mysql_query( 'select * from '. $tbl_name );
        if( !$result ){
            die('Query failed: ' . mysql_error());
        }

        /* get column metadata */
        $i = 0;
        $meta_fields = array();
        $cnt_fields = mysql_num_fields($result);
        $sep = '';
        while( $i < $cnt_fields ){
            $meta_fields[] = mysql_fetch_field( $result, $i );
            echo $sep . $meta_fields[$i]->name;
            $sep = ', ';
            $i++;
        }
        echo ") */\n";
        echo '$k_stmt_pre = "INSERT INTO ".'.$tbl_alias.'." VALUES ";'."\n";
        $stmt_pre = '$k_stmts[] = $k_stmt_pre."(';


        /* get the column values */
        $val_from = array( "\'", '$' );
        $val_to = array( "'", '\$' );
        while( $row = mysql_fetch_row($result) ){
            $sep = '';
            $stmt = $stmt_pre;
            for( $i=0; $i<$cnt_fields; $i++ ){
                if( is_null($row[$i]) || !isset($row[$i]) ){
                    $val = 'NULL';
                }
                elseif( $meta_fields[$i]->numeric ){
                    $val = $row[$i];
                }
                else{
                    // Sanitize will add slashes to backslash, quote, doubleqoute, newline and return chars.
                    // We need to slash all of them again for PHP, except the single quote.
                    // Plus slash any dollar char that misleads PHP into thinking it is dealing with a variable.
                    $val = '\'' . str_replace($val_from, $val_to, addslashes($DB->sanitize($row[$i]))) . '\'';
                }
                $stmt .= $sep . $val;
                $sep = ', ';
            }
            $stmt .= ');";' . "\n";

            /* output the complete statement  */
            echo $stmt;
        }
        /* clean up */
        mysql_free_result($result);

    }
