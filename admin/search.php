<?php

    if ( !defined('K_ENGINE_DIR') ) die(); // cannot be loaded directly
    define( 'K_RANGE_LEN', 128 );

    class KKeyword{
        var $name = '';
        var $len = '';
        var $ranges = array();

        function __construct( $name, $orig_text, &$existing_ranges ){
            $this->name = trim( strtolower($name) );
            $this->len = strlen( $this->name );
            $text = strtolower( $orig_text );

            // create a range object for each occurrence of this keyword in text
            $pos = -1;
            $abs_end = strlen( $text );
            while( $pos!==false ){
                $pos = strpos( $text, $this->name, $pos+1 );
                if( $pos !==false ){

                    // extract a range of chars from both sides of the keyword
                    $start = ( ($pos - K_RANGE_LEN) < 0 ) ? 0 : ($pos - K_RANGE_LEN);
                    $end = ( ($pos + $this->len + K_RANGE_LEN) > $abs_end ) ? $abs_end : ($pos + $this->len + K_RANGE_LEN);

                    // expand range to include full words if possible
                    if( $start != 0 ){
                        $tmp = substr( $text, 0, $start );
                        $prev_space = strrpos( $tmp, ' ' );
                        if( $prev_space!==false ){
                            $start = $prev_space + 1;
                        }
                        else{
                            $start = 0;
                        }
                    }
                    if( $end != $abs_end ){
                        $next_space = strpos( $text, ' ', $end );
                        if( $next_space!==false ) $end = $next_space;
                    }

                    // does this range intersect with any existing range?
                    for( $x=0; $x<count($existing_ranges); $x++ ){
                        $r = &$existing_ranges[$x];
                        // if so and it does not already contain this keyword, use existing
                        if( $r->intersects($start, $end) ){
                            if( !$r->keyword_exists($this->name) ){
                                $r->inflate( $start, $end, $orig_text );
                                $r->keywords[] = $this->name;
                                $this->ranges[] = &$r;
                            }
                            $exists = 1;
                            break;
                        }
                        unset( $r );
                    }
                    if( !$exists ){
                        $range = new KRange( $start, $end, $orig_text );
                        $range->keywords[] = $this->name;

                        $existing_ranges[] = &$range;
                        $this->ranges[] = &$range;
                    }
                    unset( $range );
                }

                // move to next occurrence of this keyword..
            }
        }

        // selects and adds one (or none if a range already selected )
        // of the candidate ranges containing this keyword
        function get_selected_range( &$selected_ranges ){
            $selected = 0;
            for( $x=0; $x<count($this->ranges); $x++ ){
                $r = &$this->ranges[$x];
                if( $r->selected ){
                    $selected = 1;
                    break;
                }
                unset( $r );
            }

            if( !$selected ){
                // select the range containing maximum number of keywords
                $highest_range = null;
                for( $x=0; $x<count($this->ranges); $x++ ){
                    $r = &$this->ranges[$x];
                    if( is_null($highest_range) || (count($r->keywords) > count($highest_range->keywords)) ){
                        unset( $highest_range );
                        $highest_range = &$r;
                    }
                    unset( $r );
                }
                if( $highest_range ){
                    $highest_range->selected = 1;
                    $selected_ranges[] = $highest_range;
                }
            }
        }
    }

    ////
    class KRange{
        var $start = 0;
        var $end = 0;
        var $text;
        var $keywords = array(); // array of keyword objects pointing to this range
        var $selected = 0;

        function __construct( $start, $end, $text ){
            if ( $end < $start ) $end = $start;

            $this->start = $start;
            $this->end = $end;
            $this->text = substr( $text, $this->start, $this->end-$this->start );
        }

        function intersects( $start, $end ){
            if( (($start<$this->start) && ($end<$this->start)) || ($start>$this->end) ){
                return false;
            }
            return true;
        }

        function keyword_exists( $keyword ){
            foreach( $this->keywords as $kw ){
                if( $kw == $keyword ) return true;
            }
            return false;
        }

        function inflate( $start, $end, &$text ){
            $this->start = ($start < $this->start) ? $start : $this->start;
            $this->end = ($end > $this->end) ? $end : $this->end;
            $this->text = substr( $text, $this->start, $this->end-$this->start );
        }
    }
