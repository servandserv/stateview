<?php
	namespace com\servandserv\stateview;
		
	class Param {
			
		const NS = "urn:com:servandserv:data:stateview";
		const ROOT = "Param";
		const PREF = NULL;

		protected $name = null;
		protected $value = null;
        
        public function __construct( $name, $value ) 
        {
            $this->name = $name;
            $this->value = $value;
        }
		public function setName ( $val ) {
			$this->name = $val;
			return $this;
		}
		public function setValue ( $val ) {
			$this->value = $val;
			return $this;
		}
		public function getName() {
			return $this->name;
		}
		public function getValue() {
			return $this->value;
		}
		
		public function toXmlWriter( \XMLWriter $xw, $xmlname = self::ROOT, $xmlns = self::NS )
		{
		    $xw->startElementNS( NULL, $xmlname, $xmlns );
		    $xw->writeAttribute( "name", $this->getName() );
		    $xw->text( $this->getValue() );
		    $xw->endElement();
		}
	}
