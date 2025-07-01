<?php
/**
 * Twitter Outgoing
 * Author: Aarti Ojha
 * Date: 01-07-2024
 * Description: This file handles Fetting post and comment from Facebook and store in database This file run in cron 
 * Please do not modify this file without permission.
 * 
 **/
include_once("../../../config/web_mysqlconnect.php"); // Include database connection file 
$sql_cdr= "SELECT * from $db.tbl_twitter_connection where status=1 and debug_status=1 ";
$query=mysqli_query($link,$sql_cdr);
$config = mysqli_fetch_array($query);

$acoount_id = $config['account_id'];
$consumer_key              = $config['consumer_key'];
$consumer_secret           = $config['consumer_secret'];
$oauth_access_token        = $config['access_token'];
$oauth_access_token_secret = $config['access_token_secret'];

$sql="select * from $db.web_twitter_directmsg where sent_flag='0'";
$query=mysqli_query($link,$sql);
$numrec=mysqli_num_rows($query);

$oauth = array(
'oauth_consumer_key' => $consumer_key,
'oauth_nonce' => time(),
'oauth_signature_method' => 'HMAC-SHA1',
'oauth_token' => $oauth_access_token,
'oauth_timestamp' => time(),
'oauth_version' => '1.0'
);

if($numrec>0){
	while($row=mysqli_fetch_assoc($query)){
		$return = '';
		$values= array();
		$dm_text = array();
		$recipient_id = $row['recipient_id'];
		$id = $row['id'];
		$url = 'https://api.twitter.com/2/dm_conversations/'.$recipient_id.'-'.$acoount_id.'/messages';
		echo"<br/>";echo"url: ".($url);echo"<br/>";

		$requestMethod = 'POST';
		$oauth = buildOauthNew($url,$requestMethod,$consumer_key,$consumer_secret,$oauth_access_token,$oauth_access_token_secret);
		$header = array(buildAuthorizationHeader($oauth),'Content-Type: application/json');
		$dm_text['text'] = $row['message_data'];
		$dm_text = json_encode($dm_text);
		echo"<br/>";echo"header: ";print_r($headers);echo"<br/>";
		echo"<br/>";echo"dm_text: ";print_r($dm_text);echo"<br/>";
		if(!empty($header)){
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS =>$dm_text,
			  CURLOPT_HTTPHEADER => $header,
			));
			$response = curl_exec($curl);
			curl_close($curl);
			$data = json_decode($response,true); 
			echo"<br/><br/>"; print_r($data);
			if(isset($data['data'])){
				$dm_event_id = $data['data']['dm_event_id'];
				$strQrytest = "update $db.web_twitter_directmsg set sent_flag='1',dm_id='$dm_event_id' where id=".$id;
				mysqli_query($link, $strQrytest);
				echo"<br/>";echo"----------------------------------------";echo"<br/>";
				echo"<br/>";echo "message sucessfully send!!";
			}else{
				$error = $data['title'];
				$strQrytest = "update $db.web_twitter_directmsg set sent_flag='0',error_message='$error',dm_id='$dm_event_id' where id=".$id;
				mysqli_query($link, $strQrytest);
				echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>" . $data['title']. "</em></p>";
			}
		}
		sleep(2);
	}
	
}else{
	echo"<br/>";echo"----------------------------------------";echo"<br/>";
	echo"<br/>";echo "message already sended!!";echo"<br/>";
}
function buildOauthNew($url, $requestMethod,$consumer_key,$consumer_secret,$oauth_access_token,$oauth_access_token_secret){ 
      $oauth = array( 
          'oauth_consumer_key' => $consumer_key,
          'oauth_nonce' => time(),
          'oauth_signature_method' => 'HMAC-SHA1',
          'oauth_token' => $oauth_access_token,
          'oauth_timestamp' => time(),
          'oauth_version' => '1.0'
      );
      $base_info = buildBaseString1($url, $requestMethod, $oauth);
      $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
      $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
      $oauth['oauth_signature'] = $oauth_signature;

      return $oauth;
}
function buildBaseString1($baseURI, $method, $params){
    $return = array();
    ksort($params);
    foreach($params as $key => $value)
    {
        $return[] = rawurlencode($key) . '=' . rawurlencode($value);
    }
    return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $return));
}
function buildAuthorizationHeader($oauth) {
    $return = 'Authorization: OAuth ';
    $values = array();
    
    foreach($oauth as $key => $value)
    {
        $values[] = "$key=\"" . rawurlencode($value) . "\"";
    }
    
    $return .= implode(', ', $values);
    return $return;
}
?>