<?php
/*
 * https://quickstream.westpac.com.au/docs/general/urls-and-ip-addresses/#urls-and-ip-addresses
 */


//define('BASE_URL', 'https://quickweb.westpac.com.au');

define('MODE', 'LIVE');
//define('BASE_URL', 'https://quickstream.support.qvalent.com');
define("USERNAME", "");
define("PASSWORD", "");
define("supplierBusinessCode", "");

$postFields = array(
    "username" => USERNAME,
    "password" => PASSWORD,
    "customerReferenceNumber" => "CUSTOMER1"
);

$postFieldsString = http_build_query( $postFields );
$curlHandle = curl_init();

curl_setopt( $curlHandle, CURLOPT_URL, "https://ws.support.qvalent.com/services/quickweb/CommunityTokenRequestServlet" ); //Test
//curl_setopt( $curlHandle, CURLOPT_URL, "https://ws.qvalent.com/services/quickweb/CommunityTokenRequestServlet" ); //LIVE


if( array_key_exists( "user", $_POST ) &&
    array_key_exists( "pwd", $_POST ) )
{
    curl_setopt( $curlHandle, CURLOPT_PROXY, "proxy.yourdomain.com.au:8080" );
    curl_setopt( $curlHandle, CURLOPT_PROXYUSERPWD, $_POST["user"].":".$_POST["pwd"] );
}
curl_setopt( $curlHandle, CURLOPT_POST, count( $postFields ) );
curl_setopt( $curlHandle, CURLOPT_POSTFIELDS, $postFieldsString );
curl_setopt( $curlHandle, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt( $curlHandle, CURLOPT_CAINFO, "PCA-3G5.pem" );
curl_setopt( $curlHandle, CURLINFO_HEADER_OUT, 1 );
$result = curl_exec( $curlHandle );
?>
    <html>
    <head>
    </head>
    <body>
    <p> Header: <?php echo curl_getinfo( $curlHandle, CURLINFO_HEADER_OUT ); ?> </p>
    <?php
    if( curl_errno( $curlHandle ) )
    {
        ?>
        <p> Error: <?php echo curl_error( $curlHandle ); ?> </p>
        <?php
    }
    else
    {
        ?>
        <p> Token: <?php echo $result; ?> </p>
        <?php
    }
    ?>
    </body>
    </html>
<?php
curl_close( $curlHandle );
?>