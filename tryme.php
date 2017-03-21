<?php

include_once "BESRequestBuilder.php" ;

//$cmd = "show version;" ;
//$cmd = "set context var to val;" ;
//$cmd = "set container values c,fnoc1.nc;" ;
//$cmd = "set container values c,fnoc1.nc,nc;" ;
//$cmd = "set container in catalog values c,fnoc1.nc,nc;" ;
//$cmd = "define d as mfp920504a;" ;
//$cmd = "get das for d;" ;
$cmd = "define d as c1,c2 with c1.constraint=\"x,y,z\",c2.constraint=\"a,b,c\";get dods for d return as ascii;" ;
//$cmd = "define d as c with c.constraint=\"x,y,z\";get dods for d return as ascii;" ;

$xml = "" ;

$result = getRequestDocument( $cmd, $xml ) ;
if( $result != null )
{
    echo "$result" ;
    exit( 1 ) ;
}

echo "$xml" ;

?>
