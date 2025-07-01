
<?php
// include("webphone_mysqlconnect.php");
$session_extension=$_REQUEST['extension'];

$filename="/etc/asterisk/sip.conf";
$string_to_replace="register => $session_extension:";
$replace_with=";reg;ister => $session_extension:";
$content=file_get_contents($filename);
$content_chunks=explode($string_to_replace, $content);
$content=implode($replace_with, $content_chunks);
file_put_contents($filename, $content);
shell_exec('asterisk -rx "sip reload"');

 echo  '{"1":"$session_extension"}';

 // header("location:../web_logout.php");


?>