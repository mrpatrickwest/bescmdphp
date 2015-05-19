<?php
include_once( "BESCommand.php" ) ;

global $handlers ;

class SetCommand extends BESCommand
{
    function __construct( $cmd )
    {
        parent::__construct( $cmd ) ;
    }

    private function buildContainerNoCatalog( $arr )
    {
        if( $arr[2] != "values" )
        {
            return "malformed command $this->cmd" ;
        }
        $values = explode( ",", $arr[3] ) ;
        if( count( $values ) != 2 && count( $values ) != 3 )
        {
            return "malformed command $this->cmd" ;
        }
        $this->xml = "<setContainer name=\"" . $values[0] . "\"" ;
        if( count( $values ) == 3 )
        {
            $this->xml .= " type=\"" . $values[2] . "\">" ;
        }
        else
        {
            $this->xml .= ">" ;
        }
        $this->xml .= $values[1] . "</setContainer>" ;
        return null ;
    }

    private function buildContainerWithCatalog( $arr )
    {
        if( $arr[2] != "in" || $arr[4] != "values" )
        {
            return "malformed command $this->cmd" ;
        }
        $values = explode( ",", $arr[5] ) ;
        if( count( $values ) != 2 && count( $values ) != 3 )
        {
            return "malformed command $this->cmd" ;
        }
        $this->xml = "<setContainer name=\"" . $values[0] . "\"" ;
        $this->xml .= " space=\"" . $arr[3] . "\"" ;
        if( count( $values ) == 3 )
        {
            $this->xml .= " type=\"" . $values[2] . "\">" ;
        }
        else
        {
            $this->xml .= ">" ;
        }
        $this->xml .= $values[1] . "</setContainer>" ;
        return null ;
    }

    private function buildContainer( $arr )
    {
        if( count( $arr ) != 4 && count( $arr ) != 6 )
        {
            return "malformed command $this->cmd" ;
        }
        if( count( $arr ) == 4 )
        {
            return $this->buildContainerNoCatalog( $arr ) ;
        }
        else
        {
            return $this->buildContainerWithCatalog( $arr ) ;
        }
    }

    private function buildContext( $arr )
    {
        if( count( $arr ) != 5 )
        {
            return "malformed command $this->cmd" ;
        }
        if( $arr[3] != "to" )
        {
            return "malformed command $this->cmd" ;
        }
        $this->xml = "<setContext name=\"" . $arr[2] . "\">" . $arr[4]
                     .  "</setContext>" ;
    }

    public function buildXML()
    {
        $arr = explode( " ", $this->cmd ) ;
        if( $arr[1] == "container" )
        {
            return $this->buildContainer( $arr ) ;
        }
        else if( $arr[1] == "context" )
        {
            return $this->buildContext( $arr ) ;
        }
        else
        {
            return "malformed command $this->cmd" ;
        }
    }
}
$handlers["set"] = "SetCommand" ;
?>
