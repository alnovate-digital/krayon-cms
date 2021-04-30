<?php

    /*
     * Credits:
     *
     * Loosely based on
     * https://github.com/andrewbiggart/latest-tweets-php-o-auth/
     *
     * Twitteroauth for authentication.
     * https://github.com/abraham/twitteroauth
     *
     */

    if( !defined('TWEETS_DIR') ) die(); // cannot be loaded directly
    class TweetsEx{

        var $node;
        var $handle;
        var $count;
        var $exclude_replies;
        var $include_rts;
        var $twitter_style_dates;
        var $date_format;
        var $startcount;

        function TweetsEx( $params, $node ){
            global $FUNCS;

            $this->node = $node;

            extract( $FUNCS->get_named_vars(
                    array(
                           'handle'=>'',
                           'count'=>'5',
                           'exclude_replies'=>'0',
                           'include_rts'=>'1',
                           'twitter_style_dates'=>'1',
                           'date_format'=>'',
                           'startcount'=>'1',
                    ),
                $params)
            );

            // sanitize params
            $this->handle = trim( $handle );
            $this->count = $FUNCS->is_non_zero_natural( $count ) ? intval( $count ) : 5; // default 5
            $this->exclude_replies = ( $exclude_replies==1 ) ? 1 : 0; // default false
            $this->include_rts = ( $include_rts==0 ) ? 0 : 1; // default true
            $this->twitter_style_dates = ( $twitter_style_dates==0 ) ? 0 : 1; // default true
            $this->date_format = trim( $date_format );
            if( !strlen($this->date_format) ) $this->date_format = 'g:i A M jS';
            $this->startcount = $FUNCS->is_int( $startcount ) ? intval( $startcount ) : 1;
        }

        function fetch_tweets(){
            global $FUNCS;

            // config values
            require( TWEETS_DIR . "config.php" );

            $cachetime = $FUNCS->is_non_zero_natural( $cachetime ) ? intval( $cachetime ) : 5;
            $cachetime *= 60; // Convert to seconds.

            $twitter_user_id = $this->handle;
            $tweets_to_fetch = $this->count;
            $include_rts = ( $this->include_rts )? 'true' : 'false';
            $exclude_replies = ( $this->exclude_replies )? 'true' : 'false';
            if( !$this->include_rts || $this->exclude_replies ){
                $tweets_to_fetch *= 3;
            }
            $url = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=".$twitter_user_id."&count=".$tweets_to_fetch."&include_rts=".$include_rts."&exclude_replies=".$exclude_replies;

            // Before contacting Twitter first check if cached tweets are available
            $cache_file = K_ENGINE_DIR . 'cache/tweets_'.$FUNCS->hash_hmac( $url, $FUNCS->hash_hmac($url, $FUNCS->_get_nonce_secret_key()) ).'.txt';
            $cache_file_created = 0;

            if( (file_exists($cache_file)) && ($fp = @fopen($cache_file, 'rb')) ){
                flock( $fp, LOCK_SH );
                $cached_json = @fread( $fp, filesize($cache_file) );
                flock( $fp, LOCK_UN );
                fclose( $fp );

                $cache_file_created = filemtime( $cache_file );
                $cached_tweets = json_decode( $cached_json );
            }

            if( is_array($cached_tweets) && (time()-$cachetime < $cache_file_created) ){ // check if the cache has not expired
                $tweets = $cached_tweets;
            }
            else{
                // fetch fresh tweets from Twitter
                require_once( TWEETS_DIR . "twitteroauth/twitteroauth.php" );
                $oauth = new TwitterOAuth( $consumerkey, $consumersecret, $accesstoken, $accesstokensecret );
                if( $oauth ){

                    $oauth->decode_json = false;
                    $fresh_json = $oauth->get( $url );
                    $fresh_tweets = json_decode( $fresh_json );
                    if( !is_array($fresh_tweets) || isset($fresh_tweets->errors) ){
                        if( isset($fresh_tweets->errors) ){
                            $error_code = $fresh_tweets->errors[0]->code;
                            $error_msg = $fresh_tweets->errors[0]->message;

                            if( $error_code!=88 && $error_code!=130 ){ // Error but not 'Rate limit exceeded' or 'Over capacity'
                                return $error_msg;
                            }
                        }

                        // if we are here, either we couldn't connect to twitter
                        // or twitter replied back with a 'Rate limit exceeded' or 'Over capacity' error.
                        // In either case, let us use the cached tweets, if available, even if the cache has expired.
                        if( is_array($cached_tweets) ){
                            $fresh_json = $cached_json;
                            $fresh_tweets = $cached_tweets;
                        }
                        else{
                            if( !isset($error_msg)  ) $error_msg = 'Unspecified error. Could not fetch tweets';
                            return $error_msg;
                        }
                    }

                    // cache fetched tweets
                    if( $fp = @fopen($cache_file, 'wb') ){
                        flock( $fp, LOCK_EX );
                        fwrite( $fp, $fresh_json );
                        flock( $fp, LOCK_UN );
                        fclose( $fp );
                        @chmod( $cache_file, 0777 );
                    }
                    $tweets = $fresh_tweets;
                }
            }

            // process tweets array
            $html = $this->process_tweets( $tweets );

            return $html;
        }

        function process_tweets( $tweets ){
            global $FUNCS, $CTX;

            $tweets_to_display = min( $this->count, count($tweets) );
            $date_format         = $this->date_format;    // Date formatting. (http://php.net/manual/en/function.date.php)
            $twitter_style_dates = $this->twitter_style_dates;
            $startcount = $this->startcount;

            // Iterate over tweets.
            $tweet_count = 0;
            foreach( $tweets as $tweet){

                // is it a retweet?
                $retweeter = null;
                if( isset($tweet->retweeted_status) ){
                    $retweeter = $tweet->user;
                    $tweet = $tweet->retweeted_status;
                }

                $tweet_desc = $tweet->text;

                // Add hyperlink html tags to any urls, twitter ids or hashtags in the tweet.
                $tweet_desc = preg_replace( "/((http)+(s)?:\/\/[^<>\s]+)/i", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $tweet_desc );
                $tweet_desc = preg_replace( "/[@]+([A-Za-z0-9-_]+)/", "<a href=\"http://twitter.com/\\1\" target=\"_blank\">\\0</a>", $tweet_desc );
                $tweet_desc = preg_replace( "/[#]+([A-Za-z0-9-_]+)/", "<a href=\"http://twitter.com/search?q=%23\\1\" target=\"_blank\">\\0</a>", $tweet_desc );

                // Convert Tweet display time to a UNIX timestamp. Twitter timestamps are in UTC/GMT time.
                $tweet_time = strtotime( $tweet->created_at );
                if( $twitter_style_dates ){
                    // Current UNIX timestamp.
                    $current_time = time();
                    $time_diff = abs( $current_time - $tweet_time );
                    switch( $time_diff ){
                        case( $time_diff < 60 ):
                            $display_time = $time_diff.' seconds ago';
                            break;
                        case( $time_diff >= 60 && $time_diff < 3600 ):
                            $min = floor( $time_diff/60 );
                            $display_time = $min.' minutes ago';
                            break;
                        case( $time_diff >= 3600 && $time_diff < 86400 ):
                            $hour = floor( $time_diff/3600 );
                            $display_time = 'about '.$hour.' hour';
                            if( $hour > 1 ){ $display_time .= 's'; }
                            $display_time .= ' ago';
                            break;
                        default:
                            $display_time = date( $date_format, $tweet_time );
                            break;
                    }
                }
                else{
                    $display_time = date( $date_format, $tweet_time );
                }

                // set extracted info in context..
                $CTX->set( 'id', $tweet->id_str );
                $CTX->set( 'tweet_text', $FUNCS->cleanXSS($tweet_desc) );
                $CTX->set( 'tweet_display_time', $display_time );
                $CTX->set( 'tweet_date', date('Y-m-d H:i:s', strtotime($tweet->created_at)) );
                $CTX->set( 'retweet_count', $tweet->retweet_count );
                $CTX->set( 'favorite_count', $tweet->favorite_count );
                $CTX->set( 'permalink', 'http://twitter.com/'. $tweet->user->name .'/status/'. $tweet->id_str );
                $CTX->set( 'reply_link', 'https://twitter.com/intent/tweet?in_reply_to=' . $tweet->id_str );
                $CTX->set( 'retweet_link', 'https://twitter.com/intent/retweet?tweet_id=' . $tweet->id_str );
                $CTX->set( 'favorite_link', 'https://twitter.com/intent/favorite?tweet_id=' . $tweet->id_str );
                $CTX->set( 'user_name', $tweet->user->name );
                $CTX->set( 'user_screen_name', $tweet->user->screen_name );
                $CTX->set( 'user_profile_image', $tweet->user->profile_image_url );
                if( $retweeter ){
                    $CTX->set( 'is_retweet', '1' );
                    $CTX->set( 'retweeter_name', $retweeter->name );
                    $CTX->set( 'retweeter_screen_name', $retweeter->screen_name );
                    $CTX->set( 'retweeter_profile_image', $retweeter->profile_image_url );
                }
                else{
                    $CTX->set( 'is_retweet', '0' );
                    $CTX->set( 'retweeter_name', '' );
                    $CTX->set( 'retweeter_screen_name', '' );
                    $CTX->set( 'retweeter_profile_image', '' );
                }
                $CTX->set( 'k_count', $tweet_count + $startcount );
                $CTX->set( 'k_total_records', $tweets_to_display );

                // .. and call the children
                foreach( $this->node->children as $child ){
                    $html .= $child->get_HTML();
                }

                $tweet_count++;

                // If we have processed enough tweets, stop.
                if( $tweet_count >= $tweets_to_display ){
                    break;
                }
            }

            return $html;
        }
    }
