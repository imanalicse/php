<?php
/*
 * https://quickstream.westpac.com.au/docs/general/urls-and-ip-addresses/#urls-and-ip-addresses
 * https://quickstream.westpac.com.au/docs/quickweb/#parameters-for-secure-token-request
 */

//$base_url = url('');
$base_url = 'https://nsw-records.webmascot.com.au';

$postFields = array(
    "username" => "",
    "password" => "",
    "supplierBusinessCode" => "",
    "connectionType" => "QUICKWEB",
    "product" => "QUICKWEB",
    "principalAmount"=> 12,
    "paymentReference"=> 'nsw-'.time(),
    "customerReferenceNumber" => "CUSTOMER1",
    "returnUrl"=> $base_url.'/checkout/returnUrl',
    "serverReturnUrl"=> $base_url.'/api/checkout/serverReturnUrl',
    "errorEmailToAddress"=> 'iman@bitmascot.com',
);

echo '<pre>';
print_r($postFields);
echo '</pre>';

$postFieldsString = http_build_query( $postFields );
$curlHandle = curl_init();

curl_setopt( $curlHandle, CURLOPT_URL, "https://ws.support.qvalent.com/services/quickweb/CommunityTokenRequestServlet"); //Test

if( array_key_exists( "user", $_POST ) &&
    array_key_exists( "pwd", $_POST ) )
{
    curl_setopt( $curlHandle, CURLOPT_PROXY, "proxy.yourdomain.com.au:8080" );
    curl_setopt( $curlHandle, CURLOPT_PROXYUSERPWD, $_POST["user"].":".$_POST["pwd"] );
}

curl_setopt( $curlHandle, CURLOPT_POST, count( $postFields ) );
curl_setopt( $curlHandle, CURLOPT_POSTFIELDS, $postFieldsString );
curl_setopt( $curlHandle, CURLOPT_RETURNTRANSFER, 1 );
//curl_setopt( $curlHandle, CURLOPT_CAINFO, "PCA-3G5.pem" );
curl_setopt( $curlHandle, CURLINFO_HEADER_OUT, 1 );
$result = curl_exec( $curlHandle );

$token = str_replace('token=', '', $result);

?>
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
echo 'new token: '.$token;
?>
    <form action="https://quickweb.support.qvalent.com/OnlinePaymentServlet3" method="POST">
        <input type="hidden" name="token" value="<?php echo $token ?>"/>
        <input type="hidden" name="communityCode" value=""/>
        <input type="submit" value="Make Payment"/>
    </form>
<?php