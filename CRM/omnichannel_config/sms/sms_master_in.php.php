<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include_once("../../../include/web_mysqlconnect.php");
include_once("sms_incoming.php");
$xml = simplexml_load_string($xmlstring, "SimpleXMLElement", LIBXML_NOCDATA);
$json = json_encode($xml);
$array = json_decode($json,TRUE);


$sql_cdr= "SELECT * from $db.tbl_sms_connection where status=1 and debug_status=1 limit 1";
$query=mysqli_query($link,$sql_cdr);
$config = mysqli_fetch_array($query);
echo '================start sms code======================';echo"<br/>";
echo "<br>sms type::".$config['sms_type']; echo"<br/>";
if($config['sms_type'] == 'onfonmedia'){
		onfonmedia_send($config);
}else if($config['sms_type'] == 'url_based'){
		url_based_send($config);
}else if($config['sms_type'] == 'exotel'){
		exotel_send($config);
}
echo '================End sms code======================';echo"<br/>";
?>