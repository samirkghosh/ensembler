<?php
/**
 * Twitter Tweet Outgoing
 * Author: Aarti Ojha
 * Date: 01-07-2024
 * Description: This file handles Fetting post and comment from Facebook and store in database This file run in cron 
 * Please do not modify this file without permission.
 * 
 **/
include_once("../../../config/web_mysqlconnect.php"); // Include database connection file 
$sql_cdr= "SELECT * from $db.tbl_twitter_connection where status=1 and debug_status=1";
$query=mysqli_query($link,$sql_cdr);
$config = mysqli_fetch_array($query);
if(!empty($config['account_id'])){
  $twitter_account_Id = $config['account_id'];
}else{
  //$twitter_account_Id = '1468212258635333635';
  $twitter_account_Id = '28355465';
}
print_r($sql_cdr);echo"<br/>";
$bearer_token = $config['bearer_token'];
echo '================start twitter code======================';echo"<br/>";
$url = 'https://api.twitter.com/2/users/'.$twitter_account_Id.'/mentions';
echo "url tweet get--".$url; echo"<br/>";
$final_url = $url.'?media.fields=duration_ms,height,media_key,preview_image_url,type,url,width,public_metrics,non_public_metrics,organic_metrics,promoted_metrics,alt_text,variants&expansions=attachments.media_keys,author_id,edit_history_tweet_ids,entities.mentions.username,geo.place_id,in_reply_to_user_id,referenced_tweets.id,referenced_tweets.id.author_id&tweet.fields=attachments,author_id,conversation_id,created_at,entities,id,in_reply_to_user_id&max_results=100';
echo $final_url;

$curl = curl_init($url);
curl_setopt_array($curl, array(
  CURLOPT_URL => $final_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '.$bearer_token,
    'Cookie: guest_id=v1%3A168432144667831319; guest_id_ads=v1%3A168432144667831319; guest_id_marketing=v1%3A168432144667831319; personalization_id="v1_Kxgv90PxXbk+AHp85J9zMg=="'
  ),
));
$response = curl_exec($curl);
curl_close($curl);
$str = json_decode($response,true);
if(isset($str['title'])){
  echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>" . $str['detail'] . "</em></p>";
       exit();
}
echo"<pre>"; 
$msg="";
if (count($str)){
  if ($str["errors"][0]["message"] != ""){
        echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>" . $str['errors'][0]["message"] . "</em></p>";
       exit();
  }
  foreach ($str['data'] as $items){  
        foreach ($str['includes']['users'] as $key => $value) {        
          if($value['id'] == $items['author_id']){
            $name = $value['name'];
            $screen_name = $value['username'];
            break;
          }
        }
        print_r($items['author_id']);
        print_r($value['id']);
        echo "<br /><hr />Time and Date of Tweet: " . $items['created_at'] . "<br /><hr />";
        echo "Tweet: " . $items['text'] . "<br />";
        echo "Tweeted by: " . $name . "<br /><hr />";
        echo "Screen name: " . $screen_name . "<br /><hr />";
        echo "Tweet ID: " . $items['id'] . "<br /><hr />";
        echo "User ID: " . $items['author_id'] . "<br /><hr />";

        ##########################   TEXT INSERT INTO DATABASE ########################
        $cur_Date = $items['created_at'];
        $screenname = $screen_name;
        $tname = $name;
        $TweetId = $items['author_id'];
        $UserId = $items['author_id'];

        $timstamp = strtotime($cur_Date);
        $ddfdsfd = date("Y-m-d H:i:s", $timstamp);

        $sql = "SELECT * FROM $db.tbl_tweet WHERE i_TweetID='" . $items['id'] . "'";
        $tweets = mysqli_query($link,$sql);

        $count = mysqli_num_rows($tweets);
        if ($count == 0){
            $sql_insert_information = "insert into $db.tbl_tweet (v_TweeterDesc, d_TweetDateTime, v_Screenname, v_name, i_TweetID, in_reply_to_status_id, tuser_id  ) VALUES ('" . $items['text'] . "', '$ddfdsfd', '$screenname', '$tname', '" . $items['id'] . "', '" . $items['in_reply_to_user_id'] . "','" . $UserId . "')";
            echo $sql_insert_information;
            mysqli_query($link, $sql_insert_information)or die(mysqli_error());
        }       
  }
   echo"<br/>";echo '================End twitter code======================';echo"<br/>";
}//for loop
?>
