<?php


    if ( !defined('K_ENGINE_DIR') ) die(); // cannot be loaded directly

    class KSession{

        // contents (kept in session)
        var $old_msgs = array();
        var $new_msgs = array();

        function __construct(){
            global $FUNCS;

            // get flash data from session
            $this->deserialize();

            // discard old data
            $this->old_msgs = $this->new_msgs;
            $this->new_msgs = array();
            $this->serialize();

            // register custom tags
            $FUNCS->register_tag( 'set_session', array('KSession', 'set_session_handler') );
            $FUNCS->register_tag( 'get_session', array('KSession', 'get_session_handler') );
            $FUNCS->register_tag( 'delete_session', array('KSession', 'delete_session_handler') );
            $FUNCS->register_tag( 'set_flash', array('KSession', 'set_flash_handler') );
            $FUNCS->register_tag( 'get_flash', array('KSession', 'get_flash_handler') );
        }

        // get flash data from session (sort of an extended constructor)
        function deserialize(){
            if(!session_id()) @session_start();
            $data = $_SESSION['KSESSIONmsgs'];

            if( is_array($data) && count($data) ){
                // fill variables
                $this->old_msgs = $data['old_msgs'];
                $this->new_msgs = $data['new_msgs'];
            }
            else{
                // no data in session. Store default values to begin with.
                $this->serialize();
            }
        }

        // store data in session
        function serialize(){

            $data = array(
                'old_msgs' => $this->old_msgs,
                'new_msgs' => $this->new_msgs
            );

            // why are we not storing the complete object in seesion?
            // because if 'session.auto_start' is on, there is problem in storing objects in session.
            $_SESSION['KSESSIONmsgs'] = $data;
        }

        function set_var( $name, $value ){
            $name = trim( $name );
            if( $name ){
                $_SESSION[$name] = $value;
            }
        }

        function get_var( $name ){
            $name = trim( $name );
            if( $name ){
                return $_SESSION[$name];
            }
        }

        function delete_var( $name ){
            $name = trim( $name );
            if( $name ){
                unset( $_SESSION[$name] );
            }
        }

        function set_flash( $name, $value ){
            $name = trim( $name );
            if( $name ){
                $this->new_msgs[$name] = $value;
                $this->serialize();
            }
        }

        function get_flash( $name ){
            $name = trim( $name );
            if( $name ){
                return $this->old_msgs[$name];
            }
        }

        ////////////////////// tag handlers ////////////////////////
        static function set_session_handler( $params, $node ){
            global $FUNCS, $KSESSION;
            if( count($node->children) ) {die("ERROR: Tag \"".$node->name."\" is a self closing tag");}

            extract( $FUNCS->get_named_vars(
                array(
                    'name'=>'',
                    'value'=>''
                ),
                $params)
            );

            $KSESSION->set_var( $name, $value );
        }

        static function get_session_handler( $params, $node ){
            global $FUNCS, $KSESSION;
            if( count($node->children) ) {die("ERROR: Tag \"".$node->name."\" is a self closing tag");}

            extract( $FUNCS->get_named_vars(
                array(
                    'name'=>'',
                    'default'=>'',
                ),
                $params)
            );
            $has_default = ( strlen($default) ) ? 1 : 0;

            $val = $KSESSION->get_var( $name );
            if( $has_default && !strlen($val) ){ $val = $default; }

            return $val;
        }

        static function delete_session_handler( $params, $node ){
            global $FUNCS, $KSESSION;
            if( count($node->children) ) {die("ERROR: Tag \"".$node->name."\" is a self closing tag");}

            extract( $FUNCS->get_named_vars(
                array(
                    'name'=>''
                ),
                $params)
            );

            $KSESSION->delete_var( $name );
        }

        static function set_flash_handler( $params, $node ){
            global $FUNCS, $KSESSION;
            if( count($node->children) ) {die("ERROR: Tag \"".$node->name."\" is a self closing tag");}

            extract( $FUNCS->get_named_vars(
                array(
                    'name'=>'',
                    'value'=>''
                ),
                $params)
            );

            $KSESSION->set_flash( $name, $value );

        }

        static function get_flash_handler( $params, $node ){
            global $FUNCS, $KSESSION;
            if( count($node->children) ) {die("ERROR: Tag \"".$node->name."\" is a self closing tag");}

            extract( $FUNCS->get_named_vars(
                array(
                    'name'=>'',
                    'default'=>'',
                ),
                $params)
            );
            $name = trim( $name );
            $has_default = ( strlen($default) ) ? 1 : 0;

            $val = $KSESSION->get_flash( $name );
            if( $has_default && !strlen($val) ){ $val = $default; }

            return $val;
        }

    } // end class

    $KSESSION = new KSession();
