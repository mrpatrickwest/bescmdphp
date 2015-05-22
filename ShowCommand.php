<?php
include_once "BESCommand.php" ;

global $handlers ;

class ShowCommand extends BESCommand
{
    function __construct( $cmd )
    {
        parent::__construct( $cmd ) ;
    }

    public function buildXML()
    {
        $arr = explode( " ", $this->cmd ) ;
        if( count( $arr ) != 2 )
        {
            return "malformed command $this->cmd" ;
        }
        $this->xml = "<" . $arr[0] . ucfirst( $arr[1] ) . "/>" ;
        return null ;
    }
}
$handlers["show"] = "ShowCommand" ;
?>
