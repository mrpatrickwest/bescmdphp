<?php
include_once "ShowCommand.php" ;
include_once "SetCommand.php" ;
include_once "DefineCommand.php" ;
include_once "GetCommand.php" ;

function parseRequests( $requests, &$requestlist )
{
    global $handlers ;

    for( $r = 0; $r < count( $requests ); $r++ )
    {
        $request = $requests[$r] ;
        $space = strpos( $request, " " ) ;
        if( $space )
        {
            $cmd = substr( $request, 0, $space ) ;
            if( isset( $handlers[$cmd] ) )
            {
                $handler = new $handlers[$cmd]( $request ) ;
                $result = $handler->buildXML() ;
                if( $result != null )
                {
                    return "\"$cmd\" is malformed\n" . $result ;
                }
                array_push( $requestlist, $handler ) ;
            }
            else
            {
                return "\"$cmd\" is an invalid command" ;
            }
        }
        else
        {
            return "$request is an invalid command" ;
        }
    }
    return null ;
}

function parseCmd( $cmd, &$requests )
{
    $cmdarr = str_split( $cmd ) ;
    $isquote = false ;
    $start = 0 ;
    $request = "" ;
    for( $c = 0; $c < count( $cmdarr ); $c++ )
    {
        if( $cmdarr[$c] == "\"" )
        {
            if( $isquote )
            {
                $isquote = false ;
            }
            else
            {
                $isquote = true ;
            }
        }
        else if( $cmdarr[$c] == ";" && !$isquote )
        {
            $request = substr( $cmd, $start, $c - $start ) ;
            array_push( $requests, $request ) ;
            $start = $c + 1 ;
        }
        $request .= $cmdarr[$c] ;
    }

    if( $isquote )
    {
        return "Unbalanced quotes" ;
    }

    if( $start < count( $cmdarr ) )
    {
        return "Missing terminating semicolon\n" ;
    }
    return null ;
}

function getRequestDocument( $cmd, &$xml )
{
    $requests = array() ;
    $result = parseCmd( $cmd, $requests ) ;
    if( $result != null )
    {
        $msg = "The data access URL is malformed\n" ;
        $msg .= "$cmd\n" ;
        $msg .= "$result\n" ;
        return $msg ;
    }

    $requestHandlers = array() ;
    $result = parseRequests( $requests, $requestHandlers ) ;
    if( $result != null )
    {
        $msg = "The data access URL is malformed\n" ;
        $msg .= "$cmd\n" ;
        $msg .= "$result\n" ;
        return $msg ;
    }

    $xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n" ;
    $xml .= "<request reqID=\"some_unique_value\">\n" ;
    for( $r = 0; $r < count( $requestHandlers ); $r++ )
    {
        $xml .= $requestHandlers[$r]->getXML() . "\n" ;
    }
    $xml .= "</request>\n" ;

    return null ;
}
?>
