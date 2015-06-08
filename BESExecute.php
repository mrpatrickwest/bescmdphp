<?php

include_once( "BESRequestBuilder.php" ) ;
include_once( "pptphpapi/PPTClient.php" ) ;

/** Function that takes a string command, translates to XML then executes
 *
 * @param string $host host that the BES is running on
 * @param string $port tcp port that the BES is running on
 * @param string $cmd string command to be converted to XML
 * @param int $repeat number of times to repeat the document
 * @param string $outDescript file to dump the output to
 * @return string error message if error or null if successful
 */
function executeCommand( $host, $port, $cmd, $repeat, $outDescript )
{
    $xml = "" ;

    $result = getRequestDocument( $cmd, $xml ) ;
    if( $result == null )
    {
        $result = executeXMLCommand( $host, $port, $xml, $repeat, $outDescript);
    }

    return $result ;
}

/** Function that reads an xml request document from file and executes it
 *
 * @param string $host host that the BES is running on
 * @param string $port tcp port that the BES is running on
 * @param string $inFile string input file to read the xml request from
 * @param int $repeat number of times to repeat the document
 * @param string $outDescript file to dump the output to
 * @return string error message if error or null if successful
 */
function executeFromFile( $host, $port, $inFile, $repeat, $outDescript )
{
    $xml = file_get_contents( $inFile, false, NULL, -1, 2048 ) ;
    if( $xml === false )
    {
        $error = error_get_last();
        $result = "Failed to read input file $inFile: " ;
        $result .= $error ;
    }
    else
    {
        $result = executeXMLCommand( $host, $port, $xml, $repeat, $outDescript);
    }

    return $result ;
}

/** Function that taks an xml request document and executes it
 *
 * @param string $host host that the BES is running on
 * @param string $port tcp port that the BES is running on
 * @param string $xml string request document
 * @param int $repeat number of times to repeat the document
 * @param string $outDescript file to dump the output to
 * @return string error message if error or null if successful
 */
function executeXMLCommand( $host, $port, $xml, $repeat, $outDescript )
{
    $client = new PPTClient() ;
    $result = $client->initTCPConnection( $host, $port, 0 ) ;

    if( $result == null )
    {
        $result = executeXMLCommandOnce( $client, $xml, $outDescript ) ;
    }

    $client->closeConnection() ;

    return $result ;
}

/** Function that executs an xml command given an already open client
 *
 * @param PPTClient $client host BES Client to use to execute command
 * @param string $xml string request document
 * @param int $repeat number of times to repeat the document
 * @param string $outDescript file to dump the output to
 * @return string error message if error or null if successful
 */
function executeXMLCommandOnce( $client, $xml, $outDescript )
{
    $result = $client->sendRequest( $xml ) ;
    if( $result == null )
    {
        $data = "" ;
        $done = false ;
        while( !$done )
        {
            $extensions = array() ;
            $result = $client->receiveChunk( $data, $extensions ) ;
            if( $result != null )
            {
                if( $result == "done" )
                {
                    $result = null ;
                    $done = true ;
                }
                else
                {
                    break ;
                }
            }
            else
            {
                fprintf( $outDescript, "$data" ) ;
            }
        }
    }
}

/** Function that reads commands from stdin and executes them in one session
 *
 * @param string $host host that the BES is running on
 * @param string $port tcp port that the BES is running on
 * @param string $outDescript file to dump the output to
 * @return string error message if error or null if successful
 */
function executeCommands( $host, $port, $outDescript )
{
    // open the ppt client
    $client = new PPTClient() ;
    $result = $client->initTCPConnection( $host, $port, 0 ) ;

    if( $result == null )
    {
        print( "BESClient> " ) ;
        while( $line = fgets( STDIN ) )
        {
            $cmd = trim( preg_replace( '/\s+/', ' ', $line ) );
            if( $cmd != "" )
            {
                if( $cmd == "exit" || $cmd == "exit;" )
                {
                    break ;
                }
                $xml = "" ;
                $result = getRequestDocument( $cmd, $xml ) ;
                if( $result == null )
                {
                    $result = executeXMLCommandOnce( $client, $xml, $outDescript ) ;
                }

            }
            print( "BESClient> " ) ;
        }
    }

    $client->closeConnection() ;

    return $result ;
}

?>
