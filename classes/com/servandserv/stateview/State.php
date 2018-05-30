<?php
	namespace com\servandserv\stateview;
		
	/**
	 * Container for state parameters
	 * 
	 */
	class State
	{
			
		const NS = "urn:com:servandserv:data:stateview";
		const ROOT = "State";
		const PREF = NULL;
		protected $url;
		protected $params = [];

        public function __construct( $url )
        {
            $this->url = $url;
        }

		public function setParam( Param $val ) {
			$this->params[] = $val;
			return $this;
		}
		public function getParams() {
		    return $this->params;
		}
		
		public function toXmlWriter( \XMLWriter $xw, $xmlname = self::ROOT, $xmlns = self::NS )
		{
		    $xw->startElementNS( NULL, $xmlname, $xmlns );
		    $xw->writeAttribute( "url", $this->url );
		    foreach( $this->params as $param ) {
		        $param->toXmlWriter( $xw );
		    }
		    $xw->endElement();
		}
	}

