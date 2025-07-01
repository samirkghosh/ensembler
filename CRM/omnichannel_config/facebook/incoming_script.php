<?php
/*Developer:Aarti
Fetting post and comment from Facebook and store in database
This file run in cron 
*/
include_once("/var/www/html/ensembler/config/web_mysqlconnect.php");// Include database connection file

include_once('/var/www/html/ensembler/CRM/omnichannel_config/script_common_file.php'); // for mail send and curl hit common fun addedand curl hit common fun added

global $link,$db;

/* getting token fron tbl_facebook_connection table*/
// Master database
$masterdb = 'CampaignTracker';
global $configdbhost, $configdbuser, $configdbpass;

// Establish connection to the master database
$link = mysqli_connect($configdbhost, $configdbuser, $configdbpass);
if (!$link) {
    die('Failed to connect to CampaignTracker database.');
}

// Query to get the related database name
$query = "SELECT related_database_name FROM $masterdb.companies";
$stmts = mysqli_prepare($link, $query);
mysqli_stmt_execute($stmts);
$results = mysqli_stmt_get_result($stmts);

if (mysqli_num_rows($results) > 0){
    while ($company = $results->fetch_assoc()) {
        $db = $company['related_database_name'];

        echo "<br/>"; echo "<br/>"; echo " ############### Company Database Name : ".$db; echo"<br/>";

        facebook_incoming_data($db); //For outgoing Data 
    }
}else{
    echo "No company databases found."; die;
}

function facebook_incoming_data($db){
	global $link;
	$sql_cdr= "SELECT * from $db.tbl_facebook_connection where status=1 and debug=1 ";
	$query=mysqli_query($link,$sql_cdr);
	$config = mysqli_fetch_array($query);
	$totalRecords = mysqli_num_rows($query);

	if($totalRecords == 0){
		echo"<br/>";
		echo "***** FACEBOOK details not exits in table tbl_facebook_connection ***** ";
		echo"<br/>";
		return false;
	}
	$access_token = $config['access_token']; /* Main part: access token */
	$global_url = $config['facebook_url']; // Get Facebook URL from table

	// Calculate the date range for fetching data (current day and previous day)
	$current_date = date("Y-m-d H:i:s");
	$startDate = time();
	$previous_date = date('Y-m-d H:i:s', strtotime('-1 day', $startDate));


	// $previous_date = $config['last_fetch_date'];

	$limit = 20; // Facebook API default limit is 25

	/* Step-1 Fetting all post */
	echo "##################### Fetchinhg all post ##################"; echo"<br/>";

	$urlNew = "{$global_url}me/posts?fields=attachments%2Cid%2Cmessage%2Cfrom%2Cadmin_creator%2Ccreated_time&limit={$limit}&access_token={$access_token}";
	echo "<br/>";print_r($urlNew);echo "<br/>"; 
	do {

		// For fetching post data using curl hit 
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $urlNew,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		));
		$response = curl_exec($curl);
		curl_close($curl);
		$response = json_decode($response,true);

		// if error getting send mail with details code - [Aarti][28-02-2025]
		if(!empty($response['error']) && isset($response['error'])){
			echo"<br/>";echo"<br/>";
			print "*** FACEBOOK POST return this error : =". $response['error']['message'];echo"<br/>";
			print "*******************************************\n"; echo"<br/>";echo"<br/>";

			$responseError['error'] = TRUE;
    		$responseError['error_message'] = $response['error']['message'];
    		$responseError['error_code'] = $response['error']['code'];
    		$responseError['error_type'] = $response['error']['type'];

    		$type = 'FacebookPost';
        	sendErrorEmail(json_encode($responseError),$type); // for send error mail -script_common.php
			break;
		}

		echo "<pre>";
		echo '<br>message counts:'.$count = count($response['data']); echo"<br/>";

		// If no more data, break the loop
		if($count == 0){
			break;
		}
		
		for($i=0 ; $i<$count ; $i++){
			/* Step-2 store all post in table */
			
			$post_date = str_replace("T"," ",$response['data'][$i]['created_time']);
			$post_date = str_replace("+0000"," ",$post_date);

			print_r('previous date '.$previous_date); echo"<br/>";
			print_r('post date '.$post_date); echo"<br/>"; 
			// if ($post_date >= $previous_date && $post_date <= $current_date) {
				if(!empty($response['data'])){
					$name_post = addslashes($response['data'][$i]['from']['name']);
					$userid_post = addslashes($response['data'][$i]['from']['id']);

					if(empty($response['data'][$i]['from'])){
						$msg_flag_post = 'IN';
					}else{
						$msg_flag_post = 'OUT';
					}
					$post_id_22 = $response['data'][$i]['id'];
					echo "----user id --------------------------".$userid_post; echo"<br/>";
					echo "----post id --------------------------".$post_id_22; echo"<br/>";
					$date_post = str_replace("T"," ",$response['data'][$i]['created_time']);
					$date_post = str_replace("+0000"," ",$date_post);

					$comment_post = addslashes($response['data'][$i]['message']);
					echo "<br><br>";
					$attachment_post = '';

					if(isset($response['data'][$i]['attachments'])){
						$attachment_post = $response['data'][$i]['attachments']['data'][$i]['media']['image']['src'];
					}
					echo "POST User id ::".$post_id_22."----userid:".$userid_post." Date:".$date_post." Post:".$comment_post; echo"<br/>";

					$sql_1 = "SELECT * from $db.tbl_facebook where post_id='".$post_id_22."'";
					$query_11=mysqli_query($link,$sql_1);
					$fb_count_1 = mysqli_num_rows($query_11);

					if($fb_count_1 == 0){
						$insert_post = "insert into $db.tbl_facebook ( name , comment , createddate , post_id , comment_id , post , userid ,msg_flag,attachment) values( '$name_post' , '' , '$date_post' , '$post_id_22' , '' , '$comment_post' , '$userid_post','$msg_flag_post','$attachment_post')";
						mysqli_query($link,$insert_post);
						echo $insert_post;
					}else{
						print "*** Already downloaded.seqno: postId=$post_id_22\n";
						print "*******************************************\n";
					}
				}
				$post_id = $response['data'][$i]['id'];
				echo"<br/>";echo "##################### Get comment from post ##################"; echo"<br/>";
				echo"<br/>";print_r($post_id);echo"<br/>";
				/* Step-3 get comment from post */
				$commentsWithReplies = getCommentsWithReplies($post_id, $access_token,$global_url); // Now $commentsWithReplies contains top-level comments with their replies
				$comment_id = '';
				if(!empty($commentsWithReplies)){
					/* storing comments */
					// Loop through top-level comments and insert them
					/* Step-4 get comment from comment */
					foreach ($commentsWithReplies as $comment) {
						insertCommentAndReplies($comment,$post_id,$comment_id);
					}
				}
			// } else {			
		 //        // Exit the loop if the post date is older than the previous date
		 //        break 2; // Break out of both the for loop and the do-while loop
		 //    }

		    $urlNew = isset($response['paging']['next']) ? $response['paging']['next'] : null;
		    echo"<br/>";echo "Start Fect Data - ".$i; echo"<br/>";echo"<br/>";
		}

	}while($urlNew);

}

/* Step-5: Update the last fetching date in the database */
// $update_fetch_date = date('Y-m-d H:i:s');
// $sql_update = "UPDATE $db.tbl_facebook_connection SET last_fetch_date='$update_fetch_date' WHERE status=1 and debug=1";
// mysqli_query($link, $sql_update);

/* Step-6 Store comment from post & store comment from comment */
function insertCommentAndReplies($comment, $postId,$parent_comment_id) {
    // Insert the comment into the comments table
	global $link,$db;
	
    $name_comment = addslashes($comment['from']['name']);
	$userid_comment = addslashes($comment['from']['id']);

	if(empty($comment['from'])){
		$msg_flag_comment = 'IN';
	}else{
		$msg_flag_comment = 'OUT';
	}
	$comment_id = $comment['id'];
	echo "----userid_comment--------------------------".$userid_comment; echo"<br/>";
	echo "----comments id --------------------------".$comment_id; echo"<br/>";

	$date = str_replace("T"," ",$comment['created_time']);
	$date = str_replace("+0000"," ",$date);
	$comment_message = addslashes($comment['message']);

	echo "<br><br>";
	$attachment = '';
	if(isset($comment['attachment'])){
		$attachment_commnets = $comment['attachment']['media']['image']['src'];
	}
    $sql = "SELECT * from $db.tbl_facebook where comment_id='".$comment_id."'";
	$query_1=mysqli_query($link,$sql);
	$fb_count = mysqli_num_rows($query_1);
	if($fb_count == 0){
		$insert = "insert into $db.tbl_facebook ( name , comment , createddate , post_id , comment_id ,parent_comment_id, post , userid ,msg_flag,attachment) values( '$name' , '$comment_message' , '$date' , '$postId' , '$comment_id','$parent_comment_id', '$msg' , '','$msg_flag_comment','$attachment_commnets')";
		mysqli_query($link,$insert);
		echo $insert;
	}else{
		print "*** Already downloaded.seqno: Comment = $comment_id\n";
		print "*******************************************\n";
	}
    // If there are replies to this comment, insert them recursively
    if (isset($comment['replies']['data']) && !empty($comment['replies']['data'])) {
        foreach ($comment['replies']['data'] as $reply) {
            insertCommentAndReplies($reply, $postId,$comment_id);
        }
    }
}

/* Step-7 Fetting comment inside post */
function getCommentsWithReplies($post_id, $access_token,$global_url) {
	$limit = 100; // Facebook API default limit is 25
    $top_level_comments = [];
    $url = $global_url.$post_id.'/comments?fields=attachment%2Cid%2Cmessage%2Cfrom%2Ccreated_time&limit='.$limit.'&access_token='.$access_token;

    echo"<pre/>";echo"Comment Url: ".$url;echo "<br/>";

    $response = file_get_contents($url);
    $comments = json_decode($response, true);
    echo"<pre/>";echo "<br/>";

    foreach ($comments['data'] as $comment) {
        $comment['replies'] = fetchReplies($comment['id'], $access_token);
        $top_level_comments[] = $comment;

        // If there are replies to this comment, fetch them recursively
        if (isset($comment['replies']['data']) && !empty($comment['replies']['data'])) {
            $comment['replies']['data'] = fetchReplies($comment['id'], $access_token);
        }
    }
    return $top_level_comments;
}

/* Step-8 Fetting comment inside Comment */
function fetchReplies($comment_id, $access_token) {
	global $global_url;
    $url = $global_url.$comment_id.'/comments?fields=attachment%2Cid%2Cmessage%2Cfrom%2Ccreated_time&access_token='.$access_token;
    $response = file_get_contents($url);
    $replies = json_decode($response, true);
    return $replies;
}

?>
