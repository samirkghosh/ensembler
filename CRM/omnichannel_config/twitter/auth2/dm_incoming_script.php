<?php
/**
 * Twitter Incoming
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
$twitter_account_Id = $config['account_id'];
echo '================start twitter code======================';echo"<br/>";

// $consumer_key              = $config['consumer_key'];
// $consumer_secret           = $config['consumer_secret'];
// $oauth_access_token        = $config['access_token'];
// $oauth_access_token_secret = $config['access_token_secret'];


$consumer_key              = 'iiMI8WLS9qV70wbrwANJfD40y';
$consumer_secret           = 'xGpk1kNk7pVtjR4EYJRqh50ejdj2wyXxaI3ZD6p1oJ6q1pQC6Q';
$oauth_access_token        = '1717832469775351808-KJckLxGQ0KKCxNEM8OZqVrEquSOimp';
$oauth_access_token_secret = 'eGArzTvb99VuJP6Eo1bPaUVYJaGgEjsG2WFmXFK2IylRA';

// $url = 'https://api.twitter.com/2/dm_events?dm_event.fields=id,text,event_type,dm_conversation_id,created_at,sender_id,attachments,participant_ids,referenced_tweets&expansions=sender_id,referenced_tweets.id,attachments.media_keys,participant_ids';
$url = 'https://api.twitter.com/2/dm_events?dm_event.fields=id%2Ctext%2Cevent_type%2Cdm_conversation_id%2Ccreated_at%2Csender_id%2Cattachments%2Cparticipant_ids%2Creferenced_tweets&expansions=sender_id%2Creferenced_tweets.id%2Cattachments.media_keys%2Cparticipant_ids';
$requestMethod = 'GET';

$oauth = buildOauthNew($url,$requestMethod,$consumer_key,$consumer_secret,$oauth_access_token,$oauth_access_token_secret);
// $header = array(buildAuthorizationHeader($oauth));
$header = array('Content-Type: application/json',buildAuthorizationHeader($oauth));

echo"<br/>";echo"header Old : ";print_r($header);echo"<br/>";echo"<br/>";

$headernew = array(
    'Content-Type: application/json',
    'Authorization: OAuth oauth_consumer_key="'.$consumer_key.'",oauth_token="'.$oauth_access_token.'",oauth_signature_method="HMAC-SHA1",oauth_timestamp="1716889999",oauth_nonce="6655aa3ee328e",oauth_version="1.0",oauth_signature="NwbAIo0jBsGkVxkVD6%2BK07xNL3w%3D"'
  );
echo"<br/>";echo"header New : ";print_r($headernew);echo"<br/>";echo"<br/>";

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => $header
));
$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "HTTP Status Code: $http_code\n";echo"<br/>";echo"<br/>";
echo "Response: $response\n";echo"<br/>";echo"<br/>";

if ($http_code == 401) {
    echo "Unauthorized: Please check your OAuth credentials.\n";echo"<br/>";echo"<br/>";
}
curl_close($curl);
echo $response;
echo "<pre>"; print_r($response); 
die;
if(isset($item['title'])){
  echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>" . $item['detail'] . "</em></p>";
       exit();
}
if(isset($item["errors"]) and $item["errors"][0]["message"] != ""){
  echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>".$item["errors"][0]["message"]."</em></p>";
  exit();
}
$msg="";
for($i=0;$i<count($item["data"]);$i++){

  $time_stamp=$item["data"][$i]["created_at"];
  $timestampc=date("Y-m-d H:i:s", strtotime($time_stamp));
  $sender_id=$item["data"][$i]["sender_id"];
  $recipient_id = $twitter_account_Id;

  $msg.="<br>ID::".$item["data"][$i]["id"]." Timestamp:: ".$timestampc;
  $msg.="Recip::".$recipient_id." Sender::".$sender_id;
  $msg.="message_data::".$item["data"][$i]["text"];
  // $obj=$item["events"][$i]["message_create"]["target"];

  echo "<br>ID::".$item["data"][$i]["id"]." Timestamp:: ".$timestampc; echo"<br/>";
  echo "Recip::".$recipient_id." Sender::".$sender_id; echo"<br/>";
  echo "message_data::".$item["data"][$i]["text"]; echo"<br/>";

  $dm_id=$item["data"][$i]["id"];
  $source_app_id="";
  $message_data=$item["data"][$i]["text"];
  // $att = $item["data"][$i]["attachment"];
  $fullpath = '';
  
	$query = "SELECT * FROM $db.web_twitter_directmsg WHERE dm_id='".$item["data"][$i]["id"]."'";
  $res=mysqli_query($link,$query);
  $count = mysqli_num_rows($res);
  if($count == 0 ){
      echo "Going to insert: $message_data";
      mysqli_set_charset($link,'utf8');
      $msgData = mysqli_real_escape_string($link, $message_data);
      $created_date = date("Y-m-d H:i:s");
      $msg_flag = 'IN';
      $sql_insert_information="INSERT INTO $db.web_twitter_directmsg (dm_id,recipient_id,sender_id,source_app_id,message_data,time_stamp,created_date,msg_flag,attachment)
      VALUES('". $dm_id."','".$recipient_id."','".$sender_id."','".$source_app_id."','".$msgData."','".$timestampc."','".$created_date."','".$msg_flag."','".$fullpath."') ";
      mysqli_query($link, $sql_insert_information)or die(mysqli_error());
  }else {
    echo "<br>......already inserted messages.........."; echo"<br/>";
  }
   echo"<br/>";echo '================End twitter code======================';echo"<br/>";
  
 
}//for loop

function buildAuthorizationHeader($oauth) {
    $return = 'Authorization: OAuth ';
    $values = array();
    foreach($oauth as $key => $value){
        // if ($key == 'oauth_signature1') {
        //     // Do not URL-encode the oauth_signature
        //     $values[] = "$key=\"$value\"";
        // } else {
        //     $values[] = "$key=\"" . rawurlencode($value) . "\"";
        // }
        if (in_array($key, array('oauth_consumer_key', 'oauth_nonce', 'oauth_signature',
          'oauth_signature_method', 'oauth_timestamp', 'oauth_token', 'oauth_version'))){
              $values[] = "$key=\"" . rawurlencode($value) . "\"";    
        }
    }
    $return .= implode(', ', $values);
    return $return;
}

function buildOauthNew($url, $requestMethod, $consumer_key, $consumer_secret, $oauth_access_token, $oauth_access_token_secret) { 
    $oauth = array(
        'oauth_consumer_key' => $consumer_key,
        'oauth_token' => $oauth_access_token,
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_timestamp' => time(),
        'oauth_nonce' => generateNonce(),
        'oauth_version' => '1.0'
    );

    $base_info = buildBaseString1($url, $requestMethod, $oauth);
    $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
    $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
    $oauth['oauth_signature'] = $oauth_signature;
    // Debugging: Print base string and signature
    echo "Base String: $base_info\n";;echo"<br/>";echo"<br/>";
    echo "Signing Key: $composite_key\n";echo"<br/>";echo"<br/>";
    echo "OAuth Signature: $oauth_signature\n";echo"<br/>";echo"<br/>";

    return $oauth;
}

function buildBaseString1($baseURI, $method, $params) {
    $return = array();
    ksort($params);
    foreach($params as $key => $value) {
        $return[] = rawurlencode($key) . '=' . rawurlencode($value);
    }
    return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $return));
}
function generateNonce() {
    return bin2hex(random_bytes(16)); // More secure way to generate a nonce
}

function buildOauthNew11($url, $requestMethod,$consumer_key,$consumer_secret,$oauth_access_token,$oauth_access_token_secret){ 
      $oauth = array(
          'oauth_consumer_key' => $consumer_key,
          'oauth_token' => $oauth_access_token,
          'oauth_signature_method' => 'HMAC-SHA1',
          'oauth_timestamp' => time(),
          'oauth_nonce' => uniqid(),
          'oauth_version' => '1.0'
      );
      $base_info = buildBaseString1($url, $requestMethod, $oauth);
      $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
      $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
      $oauth['oauth_signature'] = $oauth_signature;
      return $oauth;
}
?>
