<?php
include_once("../../../include/web_mysqlconnect.php");
include("/var/www/html/ensembler/logs/logs.php");
// include_once("../../priority.php");
// $phone= '767868';
// $priority = priority_user($phone);
// print_r($priority); die;

$sql_cdr= "SELECT * from $db.tbl_twitter_connection where status=1 and debug_status=1";
$query=mysqli_query($link,$sql_cdr);
$config = mysqli_fetch_array($query);

echo '================start twitter code======================';echo"<br/>";

$url = 'https://api.twitter.com/1.1/direct_messages/events/new.json';
$requestMethod = 'POST';
$keysetting = buildOauthKey($url,$requestMethod,$config);
$urlNew = $url.'?'.$keysetting;

$consumer_key              = $config['consumer_key'];
$consumer_secret           = $config['consumer_secret'];
$oauth_access_token        = $config['access_token'];
$oauth_access_token_secret = $config['access_token_secret'];

$header_access_token = 'oauth_access_token:'.$oauth_access_token;
$header_access_token_secret = 'oauth_access_token_secret:'.$oauth_access_token_secret;
$header_consumer_key = 'consumer_key:'.$consumer_key;
$header_consumer_secret = 'consumer_secret:'.$consumer_secret;

$header = array($header_access_token,$header_access_token_secret,$header_consumer_key,$header_consumer_secret,
        'Content-Type: application/json'
);
$sql_cdr = "SELECT * FROM $db.web_twitter_directmsg WHERE msg_flag= 'OUT' and sent_flag='0'";
$query=mysqli_query($link,$sql_cdr);
$count = mysqli_num_rows($query);
if($count != 0 ){
  while($data=mysqli_fetch_assoc($query)){
    $postfields =array(
        "event" => array(
            "type" => "message_create",
            "message_create" => array(
                "target" => array(
                    "recipient_id" => $data['recipient_id']
                ),
                "message_data" => array(
                    "text" => $data['message_data']
                )
            )
        )
      );

    $datajson=json_encode($postfields);
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $urlNew,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>$datajson,
    CURLOPT_HTTPHEADER => $header
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $json_arry = json_decode($response,true);

    if($json_arry["errors"][0]["message"] != "") {
      echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>".$json_arry["errors"][0]["message"]."</em></p>";
        if(__DBG__)
        {
        $szMsg=__FILE__." Erorr Code:: ".$json_arry["errors"][0]["message"]." Msg:".$json_arry["errors"][0]["message"];
        DbgLog(_LOG_INFO,__LINE__, "Twitter CRON Listing :", $szMsg);
        }
      exit();
    }

      $obj=$json_arry["event"]["message_create"]["target"];
      $dm_id=$json_arry["event"]["id"];
      $recipient_id=$obj["recipient_id"];
      $sender_id=$json_arry["event"]["message_create"]["sender_id"];
      $time_stamp=$json_arry["event"]["created_timestamp"];
      $message_data=$json_arry["event"]["message_create"]["message_data"]["text"];

      echo "<br>ID::".$item["events"]["id"]; echo "<br/>";
      echo "Recip::".$recipient_id." Sender::".$sender_id;echo "<br/>";
      echo "message_data::".$item["events"][$i]["message_create"]["message_data"]["text"]; echo "<br/>";

  	  echo"<br/>";echo "Going to insert: $message_data"; echo"<br/>";
      $sent_date = date("Y-m-d H:i:s");
      $sent_flag = '1';
      $id = $data['id'];
      $strQrytest ="update $db.web_twitter_directmsg set dm_id='".$dm_id."',sender_id='".$sender_id."',time_stamp='".$time_stamp."',sent_date='".$sent_date."',sent_flag='".$sent_flag."' where id=".$id;
      mysqli_query($link, $strQrytest);
      echo"<br/>";echo '================End twitter code======================';echo"<br/>";
  }//for loop
}else{
  echo "<br>......already inserted messages.........."; echo"<br/>";
}
function buildOauthKey($url, $requestMethod,$settings){
    $consumer_key              = $settings['consumer_key'];
    $consumer_secret           = $settings['consumer_secret'];
    $oauth_access_token        = $settings['access_token'];
    $oauth_access_token_secret = $settings['access_token_secret'];

    $oauth = array(
        'oauth_consumer_key' => $consumer_key,
        'oauth_nonce' => time(),
        'oauth_signature_method' => 'HMAC-SHA1',
        'oauth_token' => $oauth_access_token,
        'oauth_timestamp' => time(),
        'oauth_version' => '1.0'
    );
    $base_info = buildBaseString($url, $requestMethod, $oauth);
    $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
    $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
    $oauth['oauth_signature'] = $oauth_signature;

    foreach($oauth as $key => $value){
        if (in_array($key, array('oauth_consumer_key', 'oauth_nonce', 'oauth_signature',
            'oauth_signature_method', 'oauth_timestamp', 'oauth_token', 'oauth_version'))) {
            $values[] = "$key=" . rawurlencode($value);
        }
    }
    $return .= implode('&', $values);
    return $return;
   
}
function buildBaseString($baseURI, $method, $params){
    $return = array();
    ksort($params);
    foreach($params as $key => $value)
    {
        $return[] = rawurlencode($key) . '=' . rawurlencode($value);
    }
    return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $return));
}
?>
