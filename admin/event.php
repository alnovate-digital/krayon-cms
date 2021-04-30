<?php

    class EventDispatcher{

        var $listeners = array();
        var $sorted = array();

        function dispatch( $event_name, $args=array() ){

            if( !isset($this->listeners[$event_name]) ){
                return;
            }

            return $this->_do_dispatch( $this->get_listeners($event_name), $args );

        }

        function get_listeners( $event_name = null ){
            if( null !== $event_name ){
                if( !isset($this->sorted[$event_name]) ){
                    $this->_sort_listeners($event_name);
                }

                return $this->sorted[$event_name];
            }

            foreach( array_keys($this->listeners) as $event_name ){
                if( !isset($this->sorted[$event_name]) ){
                    $this->_sort_listeners( $event_name );
                }
            }

            return $this->sorted;
        }

        function has_listeners( $event_name = null ){
            return (bool) count( $this->get_listeners($event_name) );
        }

        function add_listener( $event_name, $listener, $priority = 0 ){
            $this->listeners[$event_name][$priority][] = $listener;
            unset( $this->sorted[$event_name] );
        }

        function remove_listener( $event_name, $listener ){
            if( !isset($this->listeners[$event_name]) ){
                return;
            }

            foreach( $this->listeners[$event_name] as $priority => $listeners ){
                if( false !== ($key = array_search($listener, $listeners, true)) ){
                    unset( $this->listeners[$event_name][$priority][$key], $this->sorted[$event_name] );
                }
            }
        }

        function has_listener( $event_name, $listener ){
            if( !isset($this->listeners[$event_name]) ){
                return false;
            }

            foreach( $this->listeners[$event_name] as $priority => $listeners ){
                if( false !== ($key = array_search($listener, $listeners, true)) ){
                    return true;
                }
            }

            return false;
        }

        function _do_dispatch( $listeners, &$args ){
            foreach( $listeners as $listener ){
                $stop_propogation = call_user_func_array( $listener, $args );
                if( $stop_propogation ){
                    return true;
                }
            }
        }

        function _sort_listeners( $event_name ){
            $this->sorted[$event_name] = array();

            if( isset($this->listeners[$event_name]) ){
                krsort( $this->listeners[$event_name] );
                $this->sorted[$event_name] = call_user_func_array( 'array_merge', $this->listeners[$event_name] );
            }
        }
    }// end class
