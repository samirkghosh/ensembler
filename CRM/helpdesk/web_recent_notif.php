<?php
/***
 * Recent Case 
 * Author: Ritu modi
 * Date: 04-04-2024
 *  This code is used in a web application to display recent cases and their details for users to review or manage.
-->
 **/
include_once("../include/web_mysqlconnect.php");
include_once("live_chat_connection.php");

$date1 = date('Y-m-d');
$date12 = date('Y-m-d H:i:s');

$prev_date = date('Y-m-d 00:00:0', strtotime($date1 .' -29 day'));
$next_date = date('Y-m-d H:i:s');

$today_date   = date('Y-m-d ').'00:00:00';
$to_date      = date('Y-m-d ').'23:59:59';
?>
<div id='latestData'>
<table class="table table-bordered table-sm" style="width:82%">
 <tr>
	<td>&nbsp;<img src="images/facebooklogo.png" width="40" height="40"></td>
	<td>
				    <strong><a href="facebook.php"><span id="fbcount">0</span></a></strong>
					&nbsp;<code>new posts</code>
	</td>
 </tr>
 <tr>
		<td>&nbsp;<img src="images/maillogo.png" width="40" height="40"></td>
	<td>
				    <strong><a href="web_queue.php"><span id="mailcount">0</span></a></strong>
					&nbsp;<code>new mail</code>
	</td>
 </tr>
 <tr>
	<td >&nbsp;<img src="images/tweetlogo.png" width="40" height="40"></td>
	<td>
				 <strong><a href="admin-twitter_requests1.php"><span id="tweetcount">0</span></a></strong>
					 &nbsp;<code>new tweets</code></td>
 </tr> 
 <tr>
	<td >&nbsp;<img src="images/chatlogo.png" width="40" height="40"></td>
	<td>
					 <strong><a href="admin-Chat_requests1.php"><span id="chatcount">0</span></a></strong>
					 &nbsp;<code>new chat</code></td>
 </tr> 
 <tr>
	<td >&nbsp;<img src="images/whatsapp.png" width="40" height="40"></td>
	<td>
					 <strong><a href="admin-whatsapp_request1.php"><span id="#">0</span></a></strong>
					 &nbsp;<code>new message</code></td>
 </tr> 
 <tr>
	<td >&nbsp;<img src="images/sms.png" width="40" height="40"></td>
	<td>
					 <strong><a href="admin-sms_request1.php"><span id="#">0</span></a></strong>
					 &nbsp;<code>new sms</code></td>
 </tr> 

</tbody></table>
</div>
