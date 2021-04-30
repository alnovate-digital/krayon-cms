<?php

    if ( !defined('K_ENGINE_DIR') ) die(); // cannot be loaded directly

    define( 'K_TBL_TEMPLATES', K_DB_TABLES_PREFIX . 'k_templates' );
    define( 'K_TBL_FIELDS', K_DB_TABLES_PREFIX . 'k_fields' );
    define( 'K_TBL_PAGES', K_DB_TABLES_PREFIX . 'k_pages' );
    define( 'K_TBL_FOLDERS', K_DB_TABLES_PREFIX . 'k_folders' );
    define( 'K_TBL_USERS', K_DB_TABLES_PREFIX . 'k_users' );
    define( 'K_TBL_USER_LEVELS', K_DB_TABLES_PREFIX . 'k_levels' );
    define( 'K_TBL_SETTINGS', K_DB_TABLES_PREFIX . 'k_settings' );
    define( 'K_TBL_DATA_TEXT', K_DB_TABLES_PREFIX . 'k_data_text' );
    define( 'K_TBL_DATA_NUMERIC', K_DB_TABLES_PREFIX . 'k_data_numeric' );
    define( 'K_TBL_FULLTEXT', K_DB_TABLES_PREFIX . 'k_fulltext' );
    define( 'K_TBL_COMMENTS', K_DB_TABLES_PREFIX . 'k_comments' );
    define( 'K_TBL_RELATIONS', K_DB_TABLES_PREFIX . 'k_relations' );
    define( 'K_TBL_ATTACHMENTS', K_DB_TABLES_PREFIX . 'k_attachments' );


    class KDB{
        var $host_name = '';
        var $database = '';
        var $user_name = '';
        var $password = '';
        var $conn = 0;
        var $result;
        var $rows_affected = 0;
        var $last_insert_id = 0;

        var $db = 0;
        var $ref = 0; // reference counting of transactions

        // debug
        var $debug = 0;
        var $selects = 0;
        var $inserts = 0;
        var $updates = 0;
        var $deletes = 0;
        var $queries = 0;
        var $query_time = 0;

        function __construct( $host_name='', $database='', $user_name='', $password='' ){
            if( empty($host_name) ) $host_name = K_DB_HOST;
            if( empty($database) ) $database = K_DB_NAME;
            if( empty($user_name) ) $user_name = K_DB_USER;
            if( empty($password) ) $password = K_DB_PASSWORD;

            $this->host_name = $host_name;
            $this->database = $database;
            $this->user_name = $user_name;
            $this->password = $password;
        }

        function connect(){
            $this->conn = @mysql_connect( $this->host_name, $this->user_name, $this->password );
            if( !$this->conn ) return 0;
            $db_selected = @mysql_select_db( $this->database, $this->conn );
            if( $db_selected ){
                @mysql_query( "SET NAMES 'utf8'", $this->conn );
                @mysql_query( "SET COLLATION_CONNECTION=utf8_general_ci", $this->conn );
                @mysql_query( "SET sql_mode = ''", $this->conn );
            }
            return $db_selected;
        }

        function disconnect(){
            if( $this->conn ) mysql_close( $this->conn );
        }

        function _query( $sql ){
            $sql = trim( $sql );
            if( $sql=='' ) return;

            if( !$this->conn ){
                if( !$this->connect() ) die( "Unable to connect to database. " . mysql_error() );
            }

            $t=0;
            if( $this->debug ){ $t = microtime(true); }
            $this->result = @mysql_query( $sql, $this->conn );
            if( $this->debug ){ $this->query_time += (microtime(true) - $t); }

            if( !$this->result ){
                //die( "Could not successfully run query (".$sql.") from DB: " . mysql_error() );
                ob_end_clean();
                die( "Could not successfully run query: " . mysql_error( $this->conn ) );
            }

            $this->queries++;
            return $this->result;

        }

        function select( $tbl, $params, $clause='', $distinct='' ){
            $sep = '';
            foreach( $params as $field ){
                $fields .= $sep . $field;
                $sep = ', ';
            }
            $sql = ( $distinct ) ? 'SELECT DISTINCT ' : 'SELECT ';
            $sql .= $fields . ' FROM ' . $tbl;
            if( $clause ) $sql .= ' WHERE ' . $clause;

            $this->_query( $sql );

            $rows = array();
            while( $row = mysql_fetch_assoc($this->result) ) {
                $rows[] = $row;
            }
            mysql_free_result( $this->result );

            $this->selects++;
            return $rows;
        }

        function raw_select( $sql ){
            $this->_query( $sql );

            $rows = array();
            while( $row = mysql_fetch_assoc($this->result) ) {
                $rows[] = $row;
            }
            mysql_free_result( $this->result );

            $this->selects++;
            return $rows;
        }

        function insert( $tbl, $params ){
            $sep = '';
            foreach( $params as $k=>$v ){
                $fields .= $sep . $k;
                $values .= $sep. "'" . $this->sanitize( $v ) . "'";
                $sep = ', ';
            }
            $sql = 'INSERT INTO ' . $tbl . ' (' . $fields . ') VALUES(' . $values . ')';
            $this->_query( $sql );

            $this->rows_affected = mysql_affected_rows( $this->conn );
            $this->last_insert_id = mysql_insert_id( $this->conn );

            $this->inserts++;
            return $this->rows_affected;
        }

        function update( $tbl, $params, $clause ){
            $sep = '';
            foreach( $params as $k=>$v ){
                $values .= $sep. $k. " = '" . $this->sanitize( $v ) . "'";
                $sep = ', ';
            }
            $sql = 'UPDATE ' . $tbl . ' SET ' . $values . ' WHERE ' . $clause;
            $this->_query( $sql );

            $this->rows_affected = mysql_affected_rows( $this->conn );

            $this->updates++;
            return $this->rows_affected;
        }

        function delete( $tbl, $clause ){
            if( trim($clause)=='' ) return 0;

            $sql = 'DELETE FROM ' . $tbl . ' WHERE ' . $clause;
            $this->_query( $sql );

            $this->rows_affected = mysql_affected_rows( $this->conn );

            $this->deletes++;
            return $this->rows_affected;

        }

        function sanitize( $str ){
            if( function_exists('mysql_real_escape_string') ){
                if( !$this->conn ){
                    if( !$this->connect() ) die("Unable to connect to database" );
                }
                return @mysql_real_escape_string( $str, $this->conn );
            }
            else{
                return mysql_escape_string( $str );
            }
        }

        // Transaction control is pretty hackish .. but is serving my purpose for now.
        function begin(){
            //@mysql_query( "SET autocommit=0" );
            //@mysql_query( "BEGIN" );
            $this->ref++;
            if( $this->ref==1 ){
                @mysql_query( "START TRANSACTION", $this->conn );
            }
        }

        function commit( $force=0 ){
            $this->ref--;
            if( $this->ref==0 || $force ){
                @mysql_query( "COMMIT", $this->conn );
            }
        }

        function rollback( $force=0 ){
            $this->ref--;
            if( $this->ref==0|| $force ){
                @mysql_query( "ROLLBACK", $this->conn );
            }
        }

        /*
            Process level lock.
            Returns 1 if lock obtained else 0
            Obtained lock will be freed by either explictly calling 'release_lock'
            or automatically when the PHP script ends
            Note: locks are not released when transactions commit or roll back.
        */
        function get_lock( $name ){
            $name = trim( $name );
            if( $name=='' ) return 0;

            if( !$this->is_free_lock($name) ) return 0;

            $sql = "SELECT GET_LOCK('".$this->sanitize( $name )."', 0) AS lck";
            $rs = $this->raw_select( $sql );
            $ret = ( count($rs) ) ? $rs[0]['lck'] : 0;

            return (int)$ret;
        }

        function release_lock( $name ){
            $name = trim( $name );
            if( $name=='' ) return 0;

            $sql = "SELECT RELEASE_LOCK('".$this->sanitize( $name )."') AS lck";
            $rs = $this->raw_select( $sql );
            $ret = ( count($rs) ) ? $rs[0]['lck'] : 0;

            return (int)$ret;
        }

        function is_free_lock( $name ){
            $name = trim( $name );
            if( $name=='' ) return 0;

            $sql = "SELECT IS_FREE_LOCK('".$this->sanitize( $name )."') AS lck";
            $rs = $this->raw_select( $sql );
            $ret = ( count($rs) ) ? $rs[0]['lck'] : 0;

            return (int)$ret;
        }
    }
