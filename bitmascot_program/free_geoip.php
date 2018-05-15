 <?php
	/* 
	http://freegeoip.net/{format}/{ip_or_hostname}
	Where format is one of the available formats (csv, xml or json - lower case). IP or host name is optional. It will search your IP if one is not provided.

	JSON callbacks are supported by adding the callback argument to the query string:
	*/

	$url ="http://freegeoip.net/xml/google.com?callback=show";
	$result = simplexml_load_file($url);
	print"<pre>";
	print_r($result);
	print"<pre>";
?>