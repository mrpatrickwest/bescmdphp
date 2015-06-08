<?php

include_once "BESRequestBuilder.php" ;
include_once "BESExecute.php" ;
include_once "pptphpapi/PPTClient.php" ;

function usage()
{
print "
php bescmdln.php: the following flags are available:
    -h <host> - specifies a host for TCP/IP connection
    -p <port> - specifies a port for TCP/IP connection
    -u <unixSocket> - specifies a unix socket for connection.
    -x <command> - specifies a command for the server to execute
    -i <inputFile> - specifies a file name for a sequence of input commands
    -f <outputFile> - specifies a file name to output the results
    -t <timeoutVal> - specifies an optional timeout value in seconds
    -d - sets the optional debug flag for the client session
    -r <num> - repeat the command(s) num times
    -? - display this list of flags
" ;

exit( 1 ) ;
}

$options = getopt("h::p::x::i::f::d::r::");

$host = "localhost" ;
$port = 10022 ;
$cmd = null ;
$inFile = null ;
$outFile = null ;
$repeat = 1 ;

foreach( $options as $option => $value )
{
    switch( $option )
    {
        case 'h':
            $host = $value ;
            break ;
        case 'p':
            $port = $value ;
            break ;
        case 'x':
            $cmd = $value ;
            break ;
        case 'i':
            $inFile = $value ;
            break ;
        case 'f':
            $outFile = $value ;
            break ;
        case 'r':
            $repeat = $value ;
            break ;
        default:
            usage() ;
            break ;
    }
}

if( $cmd != null && $inFile != null )
{
    print( "You can only specify one command with -x or -i\n" ) ;
    usage() ;
}

if( $inFile != null && !file_exists( $inFile ) )
{
    print( "Input file you specified $inFile does not exist\n" ) ;
    usage() ;
}

$outDescript = null ;
if( $outFile != null )
{
    $outDescript = fopen( $outFile, "w" ) ;
    if( !$outDescript )
    {
        print( "Unable to open the output file $outFile\n" ) ;
        usage() ;
    }
}
else
{
    $outDescript = fopen('php://stdout', 'w');
    if( !$outDescript )
    {
        print( "Unable to create stdout descriptor\n" ) ;
        usage() ;
    }
}

$result = null ;

if( $cmd != null )
{
    $result = executeCommand( $host, $port, $cmd, $repeat, $outDescript ) ;
}
else if( $inFile != null )
{
    $result = executeFromFile( $host, $port, $inFile, $repeat, $outDescript ) ;
}
else
{
    executeCommands( $host, $port, $outDescript ) ;
}

fclose( $outDescript ) ;

if( $result != null )
{
    print( "Failed to execute the command\n" ) ;
    print( $result ) ;
}

// start with taking --execute from the command line
/* EXAMPLES
//$cmd = "show version;" ;
//$cmd = "set context var to val;" ;
//$cmd = "set container values c,fnoc1.nc;" ;
//$cmd = "set container values c,fnoc1.nc,nc;" ;
//$cmd = "set container in catalog values c,fnoc1.nc,nc;" ;
//$cmd = "define d as mfp920504a;" ;
//$cmd = "get das for d;" ;
$cmd = "define d as c with c.constraint=\"x;y;z\";get dods for d return as ascii;" ;

$xml = "" ;

$result = getRequestDocument( $cmd, $xml ) ;
if( $result != null )
{
    echo "$result" ;
    exit( 1 ) ;
}

echo "$xml" ;
*/

?>
