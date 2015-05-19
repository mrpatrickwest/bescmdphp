<?php
$handlers = array() ;
global $handlers ;

class BESCommand
{
    protected $cmd ;
    protected $xml ;

    protected function __construct( $cmd )
    {
        $this->cmd = $cmd ;
    }

    public function getXML()
    {
        return $this->xml ;
    }
}
?>
