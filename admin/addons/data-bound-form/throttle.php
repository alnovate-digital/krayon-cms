<?php

    if ( !defined('K_ENGINE_DIR') ) die(); // cannot be loaded directly

    /*
     * Throttle front-end post submisions by the specified time interval
     */
    class KThrottle extends KUserDefinedFormField{
        static function handle_params( $params, $node ){
            global $FUNCS;

            $attr = $FUNCS->get_named_vars(
                        array(
                               'interval'=>'', /* throttle interval in seconds */
                               'message'=>'',  /* message to be shown when throttling enforced */
                              ),
                        $params);
            $interval = intval( $attr['interval'] );
            $attr['interval'] = $FUNCS->is_non_zero_natural( $interval ) ? $interval : 300; // 5 minutes default
            $message = trim( $attr['message'] );
            $attr['message'] = strlen( $message ) ? $message : 'Insufficient interval between two posts';

            return $attr;

        }

        // Handle Posted data
        function store_posted_changes( $post_val ){
            return; // no data accepted
        }

        // Render input field
        function _render( $input_name, $input_id, $extra='', $dynamic_insertion=0 ){
            return; // no visible markup required
        }

        // This is where all the action lies
        function validate(){
            global $FUNCS, $DB, $CTX;
            if( $this->k_inactive ) return true;

            $ip_addr = trim( $FUNCS->cleanXSS(strip_tags($_SERVER['REMOTE_ADDR'])) );
            $ts = strtotime( $FUNCS->get_current_desktop_time() ) - $this->interval;

            $sql = "creation_IP='" .$DB->sanitize( $ip_addr ). "' AND ";
            $sql .= "creation_date>='".$DB->sanitize( date( 'Y-m-d H:i:s', $ts ) )."' ORDER BY creation_date DESC LIMIT 1";
            $rs = $DB->select( K_TBL_PAGES, array('id', 'creation_date'), $sql );
            if( count($rs) ){
                // calculate how many seconds to wait before submission is allowed
                $seconds_remaining = strtotime( $rs[0]['creation_date'] ) - $ts;
                $CTX->set( 'k_error_'.$this->name.'_wait', $seconds_remaining );

                // send back error
                $this->err_msg = $this->message;
                return false;
            }
            return true;
        }

    }// end class KThrottle

    $FUNCS->register_udform_field( 'throttle', 'KThrottle' );
