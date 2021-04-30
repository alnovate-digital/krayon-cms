<?php

    if ( !defined('K_ENGINE_DIR') ) die(); // cannot be loaded directly


    class KCheckSpam{

        // tag handler
        static function check_spam_handler( $params, $node ){
            global $CTX, $FUNCS, $AUTH, $SFS;
            if( $AUTH->user->access_level >= K_ACCESS_LEVEL_ADMIN ){ return; } // exempt admins from check

            extract( $FUNCS->get_named_vars(
                        array(
                              'email'=>'',
                              'username'=>'',
                              'ip'=>'',
                              'display_message'=>'<h2>Flagged as spam!</h2>',
                              'notify_email'=>'',
                              ),
                        $params)
                   );

            $email = trim( $email );
            if( $email=='' ) $email = $CTX->get('frm_k_email'); // get from submitted comment form
            if( !$email ) return; // email is mandatory - no email, nothing to check

            $username = trim( $username );
            if( $username=='' ) $username = $CTX->get('frm_k_author'); // get from submitted comment form

            $ip = trim( $ip );
            if( $ip=='' ) $ip = $_SERVER["REMOTE_ADDR"];
            $ip = preg_replace( '/[^0-9A-F:., ]/i', '', $ip );

            $display_message = trim( $display_message );
            $notify_email = trim( $notify_email );

            // contact stopforumspam.com to get spam score
            $spam_score = KCheckSpam::check_stopforumspam( $username, $email, $ip );

            // Spammer?
            $spam_threshold = ( $username ) ? 3: 2;
            if( $spam_score >= $spam_threshold ){

                if( $notify_email ){
                   $from = $to = $notify_email;
                   $subject = 'Spam Stopped!';
                   $message = "Stopped a spam posting...\n\nUsername: ".$username
                   ."\nEmail: ".$email.
                   "\nIP: ".$IP.
                   "\nScore: ".$spam_score;

                   $FUNCS->send_mail( $from, $to, $subject, $message );
                }

                // Kill the posting process
                ob_end_clean();
                die( $display_message );
            }
        }

        static function check_stopforumspam($username, $email, $ip){
            global $FUNCS;

            $score = 0;

            // query stopforumspam.com
            $username = trim( $username );
            $email = trim( $email );
            $url = 'http://www.stopforumspam.com/api?ip='.urlencode( $ip );
            if( strlen($username) ) $url .= '&username=' . urlencode( $username );
            $url .= '&email=' . urlencode( $email );
            $url .= '&f=serial';

            $res = @unserialize( $FUNCS->file_get_contents($url) );

            if( is_array($res) && $res['success'] ){

                $freq_email = $res['email']['frequency'];
                $freq_ip = $res['ip']['frequency'];

                if( strlen($username) ){
                    $freq_username = $res['username']['frequency'];
                    if( $freq_email + $freq_ip == 0 ) return 0;
                    if( $freq_username + $freq_email == 0 ) return 0;
                }
                else{
                    $freq_username = 0;
                }

                // Return the total score
                $score = ( $freq_username + $freq_email + $freq_ip );
            }

            return $score;
        }

    } //end class KCheckSpam

    // register custom tag
    $FUNCS->register_tag( 'check_spam', array('KCheckSpam', 'check_spam_handler') );
