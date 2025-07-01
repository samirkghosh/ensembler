<?php
include_once("../../../include/web_mysqlconnect.php");
$sql_cdr= "SELECT * from $db.tbl_twitter_connection where status=1 and debug_status=1";
$query=mysqli_query($link,$sql_cdr);
$config = mysqli_fetch_array($query);

echo '================start twitter code======================';echo"<br/>";
$url = "https://api.twitter.com/1.1/statuses/mentions_timeline.json";
$requestMethod = 'GET';
$keysetting = buildOauthKey($url,$requestMethod,$config);
$urlNew = $url.'?'.$keysetting;

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
$str = json_decode($response,true);
// echo "<pre>"; print_r($item); die;
$msg="";
if (count($str)){
  if ($str["errors"][0]["message"] != ""){
        echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>" . $str[errors][0]["message"] . "</em></p>";
       exit();
  }
  foreach ($str as $items){
        echo "<br /><hr />Time and Date of Tweet: " . $items['created_at'] . "<br /><hr />";
        echo "Tweet: " . $items['text'] . "<br />";
        echo "Tweeted by: " . $items['user']['name'] . "<br /><hr />";
        echo "Screen name: " . $items['user']['screen_name'] . "<br /><hr />";
        echo "Followers: " . $items['user']['followers_count'] . "<br /><hr />";
        echo "Friends: " . $items['user']['friends_count'] . "<br /><hr />";
        echo "Listed: " . $items['user']['listed_count'] . "<br /><hr />";
        echo "Tweet ID: " . $items['id_str'] . "<br /><hr />";
        echo "User ID: " . $items['user']['id'] . "<br /><hr />";

        ##########################   TEXT INSERT INTO DATABASE ########################
        $cur_Date = $items['created_at'];
        $screenname = $items['user']['screen_name'];
        $tname = $items['user']['name'];

        $timstamp = strtotime($cur_Date);
        $ddfdsfd = date("Y-m-d H:i:s", $timstamp);

        $sql = "SELECT * FROM $db.tbl_tweet WHERE i_TweetID='" . $items['id_str'] . "'";
        $tweets = mysqli_query($link,$sql);

        $count = mysqli_num_rows($tweets);
        if ($count == 0){
            $sql_insert_information = "insert into $db.tbl_tweet (v_TweeterDesc, d_TweetDateTime, v_Screenname, v_name, i_TweetID, in_reply_to_status_id, tuser_id  ) VALUES ('" . $items['text'] . "', '$ddfdsfd', '$screenname', '$tname', '" . $items['id_str'] . "', '" . $items['in_reply_to_status_id'] . "','" . $items['user']['id'] . "')";
            mysqli_query($link, $sql_insert_information)or die(mysqli_error());
        }        
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
