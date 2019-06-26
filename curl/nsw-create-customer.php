<?php

//The XML string that you want to send.
$xml = '<?xml version="1.0" encoding="ISO-8859-1"?>
<adlibXML>
    <recordList>
        <record priref="0">
            <priref>0</priref>
            <name>Red, Wilson</name>
            <forename>Red</forename>
            <surname>Wilson</surname>
            <delivery.address.line.one>Address Line 1</delivery.address.line.one>
            <delivery.address.line.two>Address Line 2</delivery.address.line.two>
            <delivery.suburb>Kingswood</delivery.suburb>
            <delivery.state>NSW</delivery.state>
            <delivery.postcode>2747</delivery.postcode>
            <delivery.country>Australia</delivery.country>
            <home.e-mail>webalive.srv@gmail.com</home.e-mail>
            <home.mobile>0404123456</home.mobile>
            <name.type>CONTACTS</name.type>
        </record>
    </recordList>
</adlibXML>';


//The URL that you want to send your XML to.
$url = 'http://axielloai.recordsnsw.com.au/Adlib_WriteApi/wwwopac.ashx?database=people&command=insertrecord';

//Your username.
$username = 'webalive';

//Your password.
$password = 'vhhUHG9WWT';

//Initiate cURL
$curl = curl_init($url);

//Set the Content-Type to text/xml.
$headers = array(
    'Content-Type: text/xml',
    //'Authorization: Basic '. base64_encode("$username:$password")
);

curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

//Set CURLOPT_POST to true to send a POST request.
curl_setopt($curl, CURLOPT_POST, true);

//Attach the XML string to the body of our request.
curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);

//Tell cURL that we want the response to be returned as
//a string instead of being dumped to the output.
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

//Disabled SSL Cert checks
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

// This is for authentication
curl_setopt($curl, CURLOPT_USERPWD, $username . ":" . $password);
//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

//Execute the POST request and send our XML.
$result = curl_exec($curl);

//Do some basic error checking.
if(curl_errno($curl)){
throw new Exception(curl_error($curl));
}

//Close the cURL handle.
curl_close($curl);

//Print out the response output.
echo $result;