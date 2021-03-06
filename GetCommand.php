<?php
include_once "BESCommand.php" ;

global $handlers ;

class GetCommand extends BESCommand
{
    function __construct( $cmd )
    {
        parent::__construct( $cmd ) ;
    }

    public function buildXML()
    {
	global $responseType ;

        // get dods for d return as ascii
        $arr = explode( " ", $this->cmd ) ;
        if( count( $arr ) != 4 && count( $arr ) != 7 )
        {
            return "malformed command $this->cmd" ;
        }
        if( $arr[2] != "for" )
        {
            return "malformed command $this->cmd" ;
        }
        if( count( $arr ) == 7 && ($arr[4] != "return" || $arr[5] != "as" ) )
        {
            return "malformed command $this->cmd" ;
        }
        $this->xml = "<get type=\"" . $arr[1] . "\" definition=\""
                     . $arr[3] . "\"" ;
        if( count( $arr ) == 7 )
        {
            $this->xml .= " returnAs=\"" . $arr[6] . "\"/>" ;
        }
        else
        {
            $this->xml .= "/>" ;
        }

	switch( $arr[1] )
	{
	    case "ddx":
	        $responseType = "xml" ;
		break ;
	    case "dods":
	        $responseType = "bin" ;
		break ;
	    default:
	        $responseType = "txt" ;
		break ;
	}
	if( isset( $arr[6] ) )
	{
	    switch( $arr[6] )
	    {
	        case "ascii":
		case "info":
		case "tab":
		case "flat":
		    $responseType = "txt" ;
		    break ;
	    }
	}

        return null ;
    }
}
$handlers["get"] = "GetCommand" ;
?>
