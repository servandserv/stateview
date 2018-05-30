<?php

namespace com\servandserv\stateview;

interface StateRepository
{
    public function getStateId();
    public function registerToken( TokenType $token );
    public function findToken( $id );
    public function delToken( $id );
    public function emptyTrash();
    public function setState( $url, Param $param );
    public function getState( $url );
    public function cleanState( $url = null );
}