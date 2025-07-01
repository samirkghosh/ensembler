<?php
/**
 * Facebook outgoing
 * Author: Aarti Ojha
 * Date: 01-07-2024
 * Description: This file handles Fetting post and comment from Facebook and store in database This file run in cron 
 * Please do not modify this file without permission.
 * 
 **/
include_once("/var/www/html/ensembler/config/web_mysqlconnect.php"); // Include database connection file

/* facebook access token and url form database */
$sql_cdr= "SELECT * from $db.tbl_facebook_connection where status=1 and debug=1 ";
$query=mysqli_query($link,$sql_cdr);
$config = mysqli_fetch_array($query);

$access_token = $config['access_token']; // Replace with your access token
$global_url = $config['facebook_url']; // get facebook url from table
echo"<br/>";echo '================Start facebook code======================';echo"<br/>";

/* getting list of outgoint flag baises in database */
$sql_cdr = "SELECT * FROM $db.tbl_facebook WHERE msg_flag= 'OUT' and sent_flag='0'";
$query=mysqli_query($link,$sql_cdr);
$count = mysqli_num_rows($query);

if($count != 0 ){
  while($data=mysqli_fetch_assoc($query)){
  		$id = $data['id'];
  		if(empty($data['parent_comment_id'])){
  			$postid = $data['post_id']; // Replace with the actual post ID
	  	}else{
	  		$postid = $data['parent_comment_id']; // Replace with the actual post comment ID
	  	}
  		$comment_text = $data['comment']; // Your comment text here
  		$datajsonq = json_encode($datajson);
  		// $comment_text = str_replace(" ","%20", $message);
	    // $comment_text = str_replace("\n","%20", $comment_text);
  		echo"body---";print_r($datajson); echo"<pre>";
		$api_url = $global_url.$postid.'/comments';
		echo"url---";print_r($api_url); echo"<pre>";

		$fields = [
		    'message' => $comment_text,
		    'access_token' => $access_token,
		];
		print_r($fields);
	    $ch = curl_init($api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
		curl_setopt($ch, CURLOPT_POST, true);
		$response = curl_exec($ch);
		curl_close($ch);
		print_r($response);	
		// Handle the response from Facebook, typically a JSON response indicating the success or failure of the comment post.
		$response = json_decode($response,true);
		if($response["error"][0]["message"] = "") {
			// Comment posting failed, handle the error.
			echo "Error posting comment: " . $response;
		  	echo "<h3>Sorry, there was a problem.</h3><p>facebook returned the following error message:</p><p><em>".$response["error"][0]["message"]."</em></p>";
		  exit();
		}else{
			// Comment was posted successfully, and $response_data['id'] contains the comment ID.
			echo "Comment posted with ID: " . $response['id'];
			$comment_id = $response["id"];
		}

		
		if(!empty($comment_id)){
			$sent_date = date("Y-m-d H:i:s");
			/*send replay sucessfully*/
			$strQrytest ="update $db.tbl_facebook set comment_id='".$comment_id."',sent_date='".$sent_date."',sent_flag='1' where id=".$id; // store commnet_id and send date also send flag 1
			echo $strQrytest;
			mysqli_query($link, $strQrytest);
		}else{
			echo "<h3>Sorry, there was a problem.</h3><p>facebook returned the following error message:</p><p><em>".$response["error"][0]["message"]."</em></p>";
		   exit();
		}
		echo"<br/>";echo '================End facebook code======================';echo"<br/>";
	}
}

?>