<?php
    class Tweets{

        // Tag handler
        function tweets_handler( $params, $node ){
            if( version_compare( phpversion(), '5.2.0', '<' ) ) { die("ERROR: Tag \"".$node->name."\" requires atleast PHP 5.2.0"); }

            if( !defined('TWEETS_DIR') ){
                define( 'TWEETS_DIR', K_ENGINE_DIR . 'addons/tweets/' );
            }

            // delegate to tweets_ex.php
            require_once( TWEETS_DIR . "tweets_ex.php" );
            $obj = new TweetsEx( $params, $node );
            $html = $obj->fetch_tweets();

            return $html;
        }
    }

    // Register tag
    $FUNCS->register_tag( 'tweets', array('Tweets', 'tweets_handler'), 1, 1 );
