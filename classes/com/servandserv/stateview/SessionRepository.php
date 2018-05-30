<?php

namespace com\servandserv\stateview;

class SessionRepository implements StateRepository
{
    const SESSION_STATE_ID = "com.serandserv.stateview.sessionid";

    public function __construct()
    {
        if( !isset( $_SESSION[self::SESSION_STATE_ID] ) ) $_SESSION[self::SESSION_STATE_ID] = [ "states" => [], "tokens"=>[] ];
    }

    public function getStateId()
    {
        return session_id();
    }

    public function registerToken( TokenType $token )
    {
        $_SESSION[self::SESSION_STATE_ID]["tokens"][$token->getId()] = $token;
        
        return $token;
    }
    
    public function findToken( $id )
    {
        if( isset( $_SESSION[self::SESSION_STATE_ID]["tokens"][$id] ) ) {
            return $_SESSION[self::SESSION_STATE_ID]["tokens"][$id];
        } else {
            return NULL;
        }
    }
    
    public function delToken( $id )
    {
        if( $tokenId && isset( $_SESSION[self::SESSION_STATE_ID]["tokens"][$id] ) ) {
            unset( $_SESSION[self::SESSION_STATE_ID]["tokens"][$id] );
            return TRUE;
        }
        return FALSE;
    }
    
    public function emptyTrash()
    {
        foreach( $_SESSION[self::SESSION_STATE_ID]["tokens"] as $id => $token ) {
            if( !$token->getExpired() || time() > $token->getExpired() ) {
                unset( $_SESSION[self::SESSION_STATE_ID]["tokens"][$id] );
            }
        }
        return $this;
    }
    
    public function setState( $url, Param $param )
    {
        if( !isset( $_SESSION[self::SESSION_STATE_ID]["states"][$url] ) ) {
            $_SESSION[self::SESSION_STATE_ID]["states"][$url] = [];
        }
        if( $param->getValue() === "" ) {
            if( array_key_exists( $param->getName(), $_SESSION[self::SESSION_STATE_ID]["states"][$url] ) ) {
                unset( $_SESSION[self::SESSION_STATE_ID]["states"][$url][$param->getName()] );
            }
        } elseif( $param->getValue() !== NULL ) {
            $_SESSION[self::SESSION_STATE_ID]["states"][$url][$param->getName()] = $param->getValue();
        }
        return $this;
    }
    
    public function getState( $url )
    {
        $state = new State( $url );
        if( isset( $_SESSION[self::SESSION_STATE_ID]["states"][$url] ) ) {
            foreach( $_SESSION[self::SESSION_STATE_ID]["states"][$url] as $k=>$v )
            {
                $state->setParam( new Param( $k, $v ) );
            }
        }
        return $state;
    }
    
    public function cleanState( $url = NULL )
    {
        if( $url!==NULL && isset( $_SESSION[self::SESSION_STATE_ID]["states"][$url] ) ) {
            unset( $_SESSION[self::SESSION_STATE_ID]["states"][$url] );
        } elseif( isset( $_SESSION[self::SESSION_STATE_ID]["states"] ) ) {
            $_SESSION[self::SESSION_STATE_ID]["states"] = [];
        }
    }
}