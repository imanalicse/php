<?php

//The XML string that you want to send.
$xml = '<adlibXML>
  <recordList>
    <record priref="0">
	  <priref>0</priref>
	  <description>Reading room request</description>
	  <completion.date>2019-04-11</completion.date>
	  <completion.time>17:00</completion.time>
	  <request.details>Test record created via API v4</request.details>
	  <client.name.lref>57413</client.name.lref>
	  <dataType>FolderData</dataType>
	  <request.date.received>2019-04-08</request.date.received>
	  <request.from.name></request.from.name>
	  <status>Started</status>
	  <recordType>Folder</recordType>
	  <api_object_number>NRS-15051-1-7-[368]</api_object_number>
	  <api_object_qty>1</api_object_qty>
	  <api_object_type>Digital</api_object_type>
	  <api_object_note>This is a note about the first object</api_object_note>
	  <api_object_number>NRS-15051-1-33-[SAMS1][DUP19]</api_object_number>
	  <api_object_qty>5</api_object_qty>
	  <api_object_type>HardCopy</api_object_type>
	  <api_object_note>Second Note</api_object_note>
	  <assigned_to></assigned_to>
	  <priority></priority>
	  <topNode>x</topNode>
	  <notes>Test record for Ian Mirfin</notes>
	  <job_type>Reading room request</job_type>
	  <end.date_predicted>2019-04-08</end.date_predicted>
    </record>
  </recordList>
</adlibXML>';


//The URL that you want to send your XML to.
$url = 'http://axielloai.recordsnsw.com.au/Adlib_WriteApi/wwwopac.ashx?database=workflow&command=insertrecord';
//$url = 'http://google.com';

//Your username.
$username = 'webalive';

//Your password.
$password = 'vhhUHG9WWT';

//Initiate cURL
$curl = curl_init($url);

//Set the Content-Type to text/xml.
curl_setopt ($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));

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