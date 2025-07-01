<?php
include_once("../../../include/web_mysqlconnect.php");
include("/var/www/html/ensembler/logs/logs.php");

$sql_cdr= "SELECT * from $db.tbl_twitter_connection where status=1 and debug_status=1";
$query=mysqli_query($link,$sql_cdr);
$config = mysqli_fetch_array($query);

echo '================start twitter code======================';echo"<br/>";

$url = 'https://api.twitter.com/1.1/direct_messages/events/list.json';
$requestMethod = 'GET';
$keysetting = buildOauthKey($url,$requestMethod,$config);
$urlNew = $url.'?'.$keysetting;

$path = '/var/www/html/ensembler/CRM/omnichannel_config/';
$curl = curl_init();
  curl_setopt_array($curl, array(
  CURLOPT_URL => $urlNew,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET'
));
$response = curl_exec($curl);
curl_close($curl);
$item = json_decode($response,true);
echo "<pre>"; print_r($item); 

if($item["errors"][0]["message"] != ""){
  echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>".$item["errors"][0]["message"]."</em></p>";
  exit();
}
$msg="";
for($i=0;$i<count($item["events"]);$i++){
  $time_stamp=$item["events"][$i]["created_timestamp"];
  $timestampc=date("Y-m-d H:i:s", $time_stamp);
  $sender_id=$item["events"][$i]["message_create"]["sender_id"];
  $recipient_id=$item["events"][$i]["message_create"]["target"]["recipient_id"];

  $msg.="<br>ID::".$item["events"][$i]["id"]." Timestamp:: ".$timestampc;
  $msg.="Recip::".$recipient_id." Sender::".$sender_id;
  $msg.="message_data::".$item["events"][$i]["message_create"]["message_data"]["text"];
  $obj=$item["events"][$i]["message_create"]["target"];

  echo "<br>ID::".$item["events"][$i]["id"]." Timestamp:: ".$timestampc; echo"<br/>";
  echo "Recip::".$recipient_id." Sender::".$sender_id; echo"<br/>";
  echo "message_data::".$item["events"][$i]["message_create"]["message_data"]["text"]; echo"<br/>";

  $dm_id=$item["events"][$i]["id"];
  $source_app_id="";
  $message_data=$item["events"][$i]["message_create"]["message_data"]["text"];
  $att = $item["events"][$i]["message_create"]["message_data"]["attachment"];
  $fullpath = '';
  if(!empty($att)){
    $message_data = '';
    $image_url = $item["events"][$i]["message_create"]["message_data"]["attachment"]["media"]["media_url"];
    $path = '/ensembler/CRM/omnichannel_config/twitter/images/';
    $url_att = $image_url;
    $requestMethod = 'GET';
    $keysetting = buildOauthKey($url_att,$requestMethod,$config);
    $url_attact= $url_att.'?'.$keysetting;
      $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url_attact,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET'
      ));
      $response1 = curl_exec($curl);
      print_r($url_attact);echo"<br/>";
      curl_close($curl);
      $filename = rand().'.jpg';
      $fullpath = $path.$filename;
      file_put_contents($fullpath,$response1);
      // print_r($fullpath);echo"<br/>";
      // echo "<pre>"; print_r($response1); die;
  }

  $query = "SELECT * FROM $db.web_twitter_directmsg WHERE dm_id='".$item["events"][$i]["id"]."'";
  $res=mysqli_query($link,$query);
  $count = mysqli_num_rows($res);

	if($count == 0 ){
		  echo "Going to insert: $message_data";
	    mysqli_set_charset($link,'utf8');
	    $msgData = mysqli_real_escape_string($link, $message_data);
      $created_date = date("Y-m-d H:i:s");
      $msg_flag = 'IN';
	    $sql_insert_information="INSERT INTO $db.web_twitter_directmsg (dm_id,recipient_id,sender_id,source_app_id,message_data,time_stamp,created_date,msg_flag,attachment)
	    VALUES('". $dm_id."','".$recipient_id."','".$sender_id."','".$source_app_id."','".$msgData."','".$time_stamp."','".$created_date."','".$msg_flag."','".$fullpath."') ";
	    mysqli_query($link, $sql_insert_information)or die(mysqli_error());
	}else {
    echo "<br>......already inserted messages.........."; echo"<br/>";
  }
   echo"<br/>";echo '================End twitter code======================';echo"<br/>";
  
}//for loop


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
