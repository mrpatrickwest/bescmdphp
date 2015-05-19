<?php
include_once "BESCommand.php" ;

global $handlers ;

class DefineCommand extends BESCommand
{
    function __construct( $cmd )
    {
        parent::__construct( $cmd ) ;
    }

    public function buildXML()
    {
        // define d as c with c.constraint="something"
        $arr = explode( " ", $this->cmd ) ;
        if( count( $arr ) != 4 && count( $arr ) != 6 )
        {
            return "malformed command $this->cmd" ;
        }
        if( $arr[2] != "as" )
        {
            return "malformed command $this->cmd" ;
        }

        $containersStr = $arr[3] ;
        $containers = explode( ",", $containersStr ) ;

        /* not all containers have to have constraints but all
         * constraints must have a corresponding container
         */
        $constraints = null ;
        if( count( $arr ) == 6 )
        {
            if( $arr[4] != "with" )
            {
                return "malformed command $this->cmd" ;
            }
            $constraintsStr = $arr[5] ;
            $constraintsFull = explode( ",", $constraintsStr ) ;
            for( $c = 0; $c < count( $constraintsFull ); $c++ )
            {
                $keyValue = explode( ".", $constraintsFull[$c] ) ;
                if( count( $keyValue ) != 2 )
                {
                    return "malformed command $this->cmd" ;
                }
                if( !in_array( $keyValue[0], $containers ) )
                {
                    return "no container for constraint, malformed command $this->cmd" ;
                }
                $keyValueArray = explode( "=", $keyValue[1] ) ;
                if( count( $keyValueArray ) != 2 )
                {
                    return "malformed command $this->cmd" ;
                }
                $value = str_replace( "\"", "", $keyValueArray[1] ) ;
                $constraints[$keyValue[0]] = $value ;
            }
        }
        $this->xml = "<define name=\"" . $arr[1] . "\">" ;
        for( $c = 0; $c < count( $containers ); $c++ )
        {
            $this->xml .= "<container name=\"" . $containers[$c] . "\"" ;
            if( isset( $constraints[$containers[$c]] ) )
            {
                $this->xml .= "><constraint>" .  $constraints[$containers[$c]]
                              . "</constraint></container>" ;
            }
            else
            {
                $this->xml .= "/>" ;
            }
        }
        $this->xml .= "</define>" ;
        return null ;
    }
}
$handlers["define"] = "DefineCommand" ;
?>
