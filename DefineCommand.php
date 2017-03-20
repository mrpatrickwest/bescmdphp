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
            $constraintsStr = $arr[5];
            $inQuotes = false;
            $done = false;
            $constraint = "";
            $constraintList = array();
            for($i = 0; $i < strlen($constraintsStr) && !$done; $i++) {
                $constraint .= $constraintsStr[$i];
                if($constraintsStr[$i] == '"') {
                    if(!$inQuotes) {
                        $inQuotes = true;
                    } else if($i == strlen($constraintsStr) - 1 || $constraintsStr[$i + 1] == ',' || $constraintsStr[$i + 1] == ';') {
                        array_push($constraintList, $constraint);
                        $constraint = "";
                        $inQuotes = false;
                        $i++;
                    } else {
                        return "Unbalanced quotes in $this->cmd";
                        $done = true;;
                    }
                }
            }
            $constraints = null;
            for($i = 0; $i < sizeof($constraintList); $i++) {
                $constraint = $constraintList[$i];
                $dot = strpos($constraint, '.');
                if(!$dot) {
                    return "missing dot in $this->cmd";
                    break;
                }
                $containerName = substr($constraint, 0, $dot);
                $equal = strpos($constraint, '=', $i);
                if(!$dot) {
                    return "missing equal in $this->cmd";
                    break;
                }
                if(!in_array($containerName, $containers)) {
                    return "no container named $containerName in $this->cmd";
                    break;
                }
                $constraintStr = substr($constraint, $dot+1, $equal-$dot-1);
                if($constraintStr != "constraint") {
                    return "should say \"constraint\" after container name in $this->cmd";
                    break;
                }
                $theConstraint = substr($constraint, $equal+2, strlen($constraint)-$equal-3);
                $constraints[$containerName] = $theConstraint;
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
