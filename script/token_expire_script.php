<?php
/**
 * Social Media Channel
 * Author: Aarti Ojha
 * Date: 17-07-2024
 * Description: This file handles Social Media Token Expire Notification Send
 * 
 * Please do not modify this file without permission.
 */
include_once("../config/web_mysqlconnect.php"); // Include database connection file 
Token_Expire_Notification();
/**
 * Fetches token expiration data and sends notification if necessary
 */
function Token_Expire_Notification(){
    global $db, $link,$from_email;
    echo"<br/>";echo '================Start Token_Expire_Notification code======================';echo"<br/>";

    	$channel_data = array();

    	// Check Imap Table Token Expire Date
	    $imap_q = "SELECT * FROM $db.tbl_connection WHERE status=1 AND v_debug=1";
	    $imap_query = mysqli_query($link, $imap_q);
	    if ($imap_query) {
	        while ($imap_config = mysqli_fetch_array($imap_query)) {
	            $imap_token_expire_date = $imap_config['token_expire_date'];
	            $imap_days = fetch_date_diff($imap_token_expire_date);
	            if ($imap_days <= 5) {
	                $channel_data[] = ['Channel_name' => 'Imap', 'days' => $imap_days];
	            }
	        }
	    }

		// Check Twitter Table Token Expire Date
	    $twitter_q = "SELECT * FROM $db.tbl_twitter_connection WHERE status=1 AND debug_status=1";
	    $twitter_query = mysqli_query($link, $twitter_q);
	    if ($twitter_query) {
	        while ($twitter_config = mysqli_fetch_array($twitter_query)) {
	            $twitter_token_expire_date = $twitter_config['token_expire_date'];
	            $twitter_days = fetch_date_diff($twitter_token_expire_date);
	            if ($twitter_days <= 5) {
	                $channel_data[] = ['Channel_name' => 'Twitter', 'days' => $twitter_days];
	            }
	        }
	    }

		// Check Facebook Table Token Expire Date
	    $facebook_q = "SELECT * FROM $db.tbl_facebook_connection WHERE status=1 AND debug=1";
	    $facebook_query = mysqli_query($link, $facebook_q);
	    if ($facebook_query) {
	        while ($facebook_config = mysqli_fetch_array($facebook_query)) {
	            $facebook_token_expire_date = $facebook_config['token_expire_date'];
	            $facebook_days = fetch_date_diff($facebook_token_expire_date);
	            if ($facebook_days <= 5) {
	                $channel_data[] = ['Channel_name' => 'Facebook', 'days' => $facebook_days];
	            }
	        }
	    }

		// Check Whatsapp Table Token Expire Date
	    $whatsapp_q = "SELECT * FROM $db.tbl_whatsapp_connection WHERE status=1 AND debug=1";
	    $whatsapp_query = mysqli_query($link, $whatsapp_q);
	    if ($whatsapp_query) {
	        while ($whatsapp_config = mysqli_fetch_array($whatsapp_query)) {
	            $whatsapp_token_expire_date = $whatsapp_config['token_expire_date'];
	            $whatsapp_days = fetch_date_diff($whatsapp_token_expire_date);
	            if ($whatsapp_days <= 5) {
	                $channel_data[] = ['Channel_name' => 'Whatsapp', 'days' => $whatsapp_days];
	            }
	        }
	    }
	    echo"<pre>";print_r($channel_data); 
		// Send Mail if any token is expiring soon
	    if (!empty($channel_data)) {
	        foreach ($channel_data as $data) {
	            $channel_name = $data['Channel_name'];
	            $days_remaining = $data['days'];

	            // Check Whatsapp Table Token Expire Date
			    $expire_emails = "SELECT * FROM $db.token_expire_emails";
			    $queryexpire_emails = mysqli_query($link, $expire_emails);
		        while ($expire_emails_config = mysqli_fetch_array($queryexpire_emails)) {
		        	if($expire_emails_config['type'] == '1'){
		        		$email[] = $expire_emails_config['email'];
		        	}
		        	if($expire_emails_config['type'] == '2'){
		        		$phones[] = $expire_emails_config['phone'];
		        	}
		        }
		        $v_toemail = implode(",",$email);// Replace with actual admin email
	            // $V_MobileNo = implode(",",$phones);;

	            $subject = "Token Expiration Alert for $channel_name";
	            $message = "The token for $channel_name will expire in $days_remaining days. Please renew it as soon as possible.";
	            echo"<br/><br/><br/>";echo $message;echo"<br/><br/><br/>";
	            $sql_email="insert into $db.web_email_information_out(v_toemail,v_fromemail,v_subject, v_body,email_type,module, ICASEID,i_expiry) values ('$v_toemail', '$from_email', '$subject', '$message', 'OUT', 'Notification Token','','')";
		         mysqli_query($link,$sql_email) or die("Notification mail web_email_information_out ".mysqli_error($link));
	        }
	    }
	echo"<br/>";echo '================ End Token_Expire_Notification code ======================';echo"<br/>";
}
/**
 * Calculates the difference in days between the current date and the given token expiration date
 * Returns 0 if the expiration date is the same as the current date
 */
function fetch_date_diff($token_expire_date){
	
	// Get the current date with no time component
    $currentDate = new DateTime();
    $currentDate->setTime(0, 0, 0);

    // Get the token expiration date with no time component
    $expireDate = new DateTime($token_expire_date);
    $expireDate->setTime(0, 0, 0);

     // Calculate the difference in days
    $interval = $currentDate->diff($expireDate);
    return $interval->days;
}
?>