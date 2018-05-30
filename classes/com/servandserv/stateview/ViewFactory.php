<?php

namespace com\servandserv\stateview;

use \com\servandserv\data\DataAdaptor;
use \com\servandserv\data\stateview\Errors;
use \com\servandserv\data\stateview\Error;
use \com\servandserv\httpclient\Router;

class ViewFactory implements Router
{

    private $rep;
    private $referrerId;
    private $referrer;
    private $callbackId;
    private $callback;
    private $onCallbackFn;
    private $query;
    private $token;
    private $env;

    public function __construct( StateRepository $rep, array $env = [] )
    {
        $this->rep = $rep;
        $this->referrerId = filter_input( INPUT_GET, "__referrer__" );
        $this->referrer = $rep->findToken( $this->referrerId );
        $this->callbackId = filter_input( INPUT_GET, "__callback__" );
        $this->callback = $rep->findToken( $this->callbackId );
        $this->env = new Env();
        foreach( $env as $k => $v ) {
            $this->env->setParam( new Param( $k, $v ) );
        }
        
    }

    public function getReferrer()
    {
        return $this->referrer;
    }

    public function getCallback()
    {
        return $this->callback;
    }
    
    public function createView( TokenType $token, array $state = [], callable $cb = null )
    {
        // clean old tokens
        // можно удалять продухшиетокены только при перестроении view
        // иначе можно получить ссылку на несуществующий токен при отправке формы которая была открыта давно
        $this->emptyTrash();
        $sn = filter_input( INPUT_SERVER, "SCRIPT_NAME" );
        $query = new Query( $sn );
        // all query params
        foreach( $_GET as $k => $v ) {
            $query->setParam( new Param( $k, $v ) );
        }
        foreach( $state as $k=>$v ) {
            $this->rep->setState( $query->getUrl(), new Param( $k, $v ) );
        }
        $token->setId( self::createTokenId( $sn ) )
            ->setQuery( $query );
        try {
            $this->rep->registerToken( $token );
            $view = ( new View() )
                ->setSessionId( $this->rep->getStateId() )
                ->setToken( $token )
                ->setCallback( $this->callback )
                ->setReferrer( $this->referrer )
                ->setEnv( $this->env )
                ->setState( $this->rep->getState( $query->getUrl() ) );
        } catch( \Exception $e ) {
            $view = ( new View() )
                ->setSessionId( $this->rep->getStateId() )
                ->setError( ( new Error )->setDescription( "Access denied" ) )
                ->setEnv( $this->env )
                ->setState( $this->rep->getState() );
        }
        
        if( $cb !== NULL ) $cb( $view );
        
        return $view;
    }
    
    public static function createTokenId( $salt )
    {
        return hash_hmac( "md5", microtime( true ), $salt );
    }

    public function del( $tokenId )
    {
        if( $tokenId ) {
            $this->rep->delToken( $id );
        }
    }
    
    public function emptyTrash()
    {
        $this->rep->emptyTrash();
    }
    

    public function createToken( $referrerId )
    {
        return $this->rep->findToken( $referrerId );
    }

    public function redirect( DataAdaptor $request = NULL, DataAdaptor $response = NULL )
    {
        if( $this->referrer ) {
            $this->referrer->setRequest( $request );
            $this->referrer->setResponse( $response );
            $this->rep->registerToken( $this->referrer );
            if( method_exists( $this->referrer, "onCallback" ) ) {
                return $this->referrer->onCallback( $request, $response );
            }
        }
        return FALSE;
    }
}
