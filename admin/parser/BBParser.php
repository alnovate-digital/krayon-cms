<?php


    if ( !defined('K_ENGINE_DIR') ) die(); // cannot be loaded directly

    class KBBNode extends KHTMLNode{
        var $is_closed = 0;

        function __construct( $type, $name='', $str_attr='', $text='', $is_self_closing=0, $is_end_tag=0 ){
            $this->type = $type;
            $this->name = strtolower( trim($name) );
            $str_attr = trim( $str_attr );
            if( $type==K_NODE_TYPE_CODE && !$is_end_tag && strlen($str_attr) ){

                // NOTE: the following sanatization will munge dangerous words in attributes. However quotes and lt, gt tags still remain.
                // The shortcode handler routine should use 'htmlspecialchars($str, ENT_QUOTES, K_CHARSET)' if using the attributes in tricky places.
                $this->attributes = $this->parse_attr( $this->sanitize( $this->normalize_entities($str_attr) ) );
            }
            $this->text = $text;
            $this->is_self_closing = $is_self_closing;
            $this->is_end_tag = $is_end_tag;
        }

        function get_HTML( $level=0 ){
            global $FUNCS;

            switch( $this->type ){
                case K_NODE_TYPE_ROOT:
                    foreach( $this->children as $node ){
                        $html .= $node->get_HTML( $level );
                    }
                    break;
                case K_NODE_TYPE_TEXT:
                    $html = $this->text;
                    break;
                case K_NODE_TYPE_CODE:

                    // first call children
                    $content = null;
                    if( !$this->is_self_closing ){
                        foreach( $this->children as $node ){
                            $content .= $node->get_HTML( $level+1 );
                        }
                    }

                    // then invoke handler for self
                    $params = $this->attributes;
                    $html = call_user_func( $FUNCS->shortcodes[$this->name], $params, $content, $this->name );
                    break;
            }

            return $html;
        }

        function get_info( $level=0 ){
            for( $x=0; $x<$level*3; $x++ ){
                $lead .= '&nbsp;';
            }

            switch( $this->type ){
                case K_NODE_TYPE_ROOT:
                    foreach( $this->children as $node ){
                        $html .= $node->get_info( $level );
                    }
                    break;
                case K_NODE_TYPE_TEXT:
                    return;
                case K_NODE_TYPE_CODE:
                    $opening_tag = '[';
                    $closing_tag = ']';

                    $html = $lead . $opening_tag . $this->name;
                    foreach( $this->attributes as $attr ){
                        $html .= ' {';
                        if( $attr['lhs'] ){
                            $html .= $attr['lhs'] . '=';
                        }
                        $html .= '"' . $attr['rhs'] . '"}';
                    }

                    if( $this->is_self_closing ){
                        $html .= ' /'.$closing_tag;
                    }
                    else{
                        $html .= $closing_tag . '<br>';
                    }

                    foreach( $this->children as $node ){
                        $html .= $node->get_info( $level+1 );
                    }
                    $html .= $this->is_self_closing ? '' : $lead . $opening_tag.'/' . $this->name . $closing_tag;
                    $html = $html . '<br>';
                    break;
                }


            return $html;
        }

        function parse_attr( $str_attr ){
            $attrs = array();
            $regex = '/(\w+)\s*=\s*(["\'])(.*?)\2(?:$|\s)|(\w+)\s*=\s*([^\s"\']+)(?:$|\s)|(["\'])(.*?)\6(?:$|\s)|(\S+)(?:$|\s)/is';
            // [footag  gender="fe'm"ale" sex='m'a"le' color=red "r'e"d" 'r'e"d' red?@]
            $cnt = preg_match_all( $regex, $str_attr, $matches, PREG_SET_ORDER );
            if( $cnt ){
                foreach( $matches as $match ){
                    $attr = array();

                    if( isset($match[8]) ){
                        $attr['rhs'] = $match[8];
                    }
                    elseif( isset($match[7]) ){
                        $attr['rhs'] = $match[7];
                    }
                    elseif( isset($match[5]) ){
                        $attr['lhs'] = $match[4];
                        $attr['rhs'] = $match[5];
                    }
                    elseif( isset($match[3]) ){
                        $attr['lhs'] = $match[1];
                        $attr['rhs'] = $match[3];
                    }
                    if( count($attr) ) $attrs[] = $attr;
                }
            }

            return $attrs;
        }

    }// end class KBBNode

    class KBBParser extends KHTMLParser{
        function __construct( $str ){
            global $FUNCS;

            $this->str = $str;
            $this->state = K_STATE_TEXT;
            $this->stack = array();
            $this->curr_node = new KBBNode( K_NODE_TYPE_ROOT );
            $this->DOM = &$this->curr_node;
            $this->pattern = null;

            $tags = $FUNCS->shortcodes; //array( 'foo', 'bar', 'baz', 'b', 'i', 'code', 'img' );
            if( count($tags) ){
                $str_tags = join( '|', array_map('preg_quote', array_keys($tags)) );
                $this->pattern = '/\[('.$str_tags.')\b([^\[\]]*?)(\/)\]|\[(\/)\s*('.$str_tags.')\b([^\[\]]*?)\]|\[('.$str_tags.')\b([^\[\]]*?)\]/is';
                // NOTE: limitation - cannot have square brackets anywhere in the tag
            }
        }

        function &get_DOM(){
            if( !$this->parsed ){
                $this->pos = 0;
                $tag = null;

                while( 1==1 ){
                    switch( $this->state ){
                        case K_STATE_TEXT:
                            $start = $this->pos;
                            $tag = &$this->get_next_tag();
                            if( !$tag ){
                                $text = substr( $this->str, $start, strlen($this->str)-$start );
                                $this->add_child( new KBBNode( K_NODE_TYPE_TEXT, '', '', $this->remove_pees($text) ) );
                                break 2;
                            }
                            $text = substr( $this->str, $start, $tag->char_num-$start );
                            $this->add_child( new KBBNode( K_NODE_TYPE_TEXT, '', '', $this->remove_pees($text) ) );

                            if( $tag->is_end_tag ){
                                $this->state = K_STATE_TAG_CLOSE;
                            }
                            else{
                                $this->state = K_STATE_TAG_OPEN;
                            }
                            break;

                        case K_STATE_TAG_OPEN:
                            if( $tag->name ){
                                if( $tag->is_self_closing ){
                                    $this->add_child( $tag );
                                }
                                else{
                                    $this->add_child( $tag );
                                    $this->push( $tag );
                                }
                            }
                            $this->state = K_STATE_TEXT;
                            break;

                        case K_STATE_TAG_CLOSE:
                            if( $tag->name!=$this->curr_node->name ){
                                $pos = $this->find( $tag->name );
                                if( $pos!== false ){

                                    // Move children of the unclosed tags to this tag
                                    $real_parent = &$this->stack[$pos];

                                    // Unclosed tags are on the stack above this tag's opening counterpart..
                                    for( $x=$pos+1; $x<count($this->stack); $x++ ){
                                    $false_parent = &$this->stack[$x];
                                    $this->move_children( $real_parent, $false_parent );
                                    unset( $false_parent );
                                    }
                                    // .. and the last is curr_node.
                                    $this->move_children( $real_parent, $this->curr_node );

                                    // normalize stack
                                    $cnt_pop = count($this->stack)-$pos;
                                    for( $x=0; $x<$cnt_pop; $x++ ){
                                        $this->pop();
                                    }
                                    $this->pop();
                                }
                            }
                            else{
                                $this->curr_node->is_closed = 1;
                                $this->pop();
                            }
                            $this->state = K_STATE_TEXT;
                            break;
                    }

                }
                if( $this->state != K_STATE_TEXT ){
                    // echo "Parsing ended in an invalid state";
                }
                if( count($this->stack) ){
                    // Unclosed tags.. move children to root.
                    for( $x=1; $x<count($this->stack); $x++ ){
                        $false_parent = &$this->stack[$x];
                        $this->move_children( $this->DOM, $false_parent );
                        unset( $false_parent );
                    }
                    // .. and the last is curr_node.
                    $this->move_children( $this->DOM, $this->curr_node );
                }
                $this->parsed = true;
            }
            return $this->DOM;
        }

        function get_info(){
            $DOM = &$this->get_DOM();
            return $DOM->get_info();
        }

        function &get_next_tag(){
            if( is_null($this->pattern) ) return false;

            while( 1==1 ){
                $res = preg_match( $this->pattern, $this->str, $matches, PREG_OFFSET_CAPTURE, $this->pos ); //We'll now require php 4.3.3
                if( !$res ) return false;

                $match = $matches[0];
                $is_self_closing = 0;
                $is_end_tag = 0;

                if( !empty($matches[1][0]) ){ /* [foo attr /] */
                    $tag = $matches[1][0];
                    $attr = $matches[2][0];
                    $is_self_closing = 1;
                }
                elseif( !empty($matches[4][0]) ){  /* [/foo attr] */
                    $tag = $matches[5][0];
                    $attr = $matches[6][0];
                    $is_end_tag = 1;
                }
                elseif( !empty($matches[7][0]) ){  /* [foo attr] */
                    $tag = $matches[7][0];
                    $attr = $matches[8][0];
                }

                $tag = strtolower( trim($tag) );
                $starts = $match[1];
                $len = strlen( $match[0] );
                $this->pos = $starts + $len;

                $node = new KBBNode( K_NODE_TYPE_CODE, $tag, $attr, '', $is_self_closing, $is_end_tag );
                $node->char_num = $starts;

                return $node;
            }
        }

        // removes <P> added by CKEditor around shortcodes
        function remove_pees( $html ){
            $html = preg_replace('/^\s*<\/p>/is', ' ', $html); // remove any closing </P> at the very beginning of string
            $html = preg_replace('/\s*<p[^>]*>\s*$/is', ' ', $html); // remove any opening <P> at the very end of string
            return $html;
        }

        function move_children( &$to, &$from ){
            $count = count($from->children);
            for( $x=0; $x<$count; $x++ ){
                $to->children[] = &$from->children[$x];
                unset( $from->children[$x] );
            }
            $from->is_self_closing = 1;
        }

    }// end class KBBParser
