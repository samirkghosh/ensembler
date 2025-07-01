<?php
/**
 * SMS Outgoing
 * Author: Aarti Ojha
 * Date: 01-07-2024
 * Description: This file handles Social Media API Data for Outgoing and Incoming Response Store in the database.
 * 
 * Please do not modify this file without permission.
 ********************************Currentry not used this file***********************************
 */
include_once("../../../config/web_mysqlconnect.php"); // Include database connection file
define ("SMS_URL","https://api.onfonmedia.co.ke/v1/sms/SendBulkSMS");

$sql_cdr= "SELECT * from $db.tbl_sms_connection where status=1 and debug=1 ";
$query=mysqli_query($link,$sql_cdr);
$config = mysqli_fetch_array($query);

$userid = $config['userid']; // Replace with your userid
$password = $config['password']; // get password url from table
$senderId = $config['senderId']; // get senderId from table
$domain_name = $config['domain_name'];

$days = 10;
$date = strtotime("-$days day");
$date_time = date("Y-m-d H:i:s", $date);
$qu="select i_ID,v_mobileNo,v_smsString from  $db.tbl_smsmessages where i_status='1' and d_timeStamp >= '$date_time'"; ############  SMS table 
	$resu=mysqli_query($link, $qu)or die(mysqli_error($link));
	$num=mysqli_num_rows($resu);
	$k = 0;
	while($ress=mysqli_fetch_array($resu)){	
		$Smsarray = array();
		$IID=$ress['i_ID'];
		$mobile=$ress['v_mobileNo'];
		// $mstring = substr($mobile, -9);
		$mobile= "+260".$mobile;
		// print_r($mobile);
		// $mobile=$mstring;
 		$vsmsString=$ress['v_smsString'];
		//$vsmsString=str_replace(" ","%20", $vsmsString);
		
		
	#################################### SENDING SMS CURL ###############################


 		$msgArray = splitString($vsmsString, 160);
 		for ($i = 0; $i < count($msgArray); $i++)
		{	
		 	$msg = $msgArray[$i];
			$msg = str_replace("\r"," ",$msg);
			$msg = str_replace("\n"," ",$msg);
			// $msg = str_replace(" ", "%20", $msg);
		}	

		if(!empty($vsmsString)){	 		
	 		$json_data['auth']['username'] = $userid;
			$json_data['auth']['password'] = $password;
			$json_data['auth']['sender_id'] = $senderId;
			$json_data['messages'][0]['phone'] = $mobile;
			$json_data['messages'][0]['message'] = $msg;
			$json_encode = json_encode($json_data);
			echo $json_encode;
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $domain_name,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => $json_encode,
			  CURLOPT_HTTPHEADER => array(
			    'Content-Type: application/json',
			    'Cookie: MUTUMIKI=aj7klh6ggvfo4hl2t1qegbl07g'
			  ),
			));
			$response = curl_exec($curl);
			curl_close($curl);
			echo $response;
			$response = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			echo "<br>Result=";print_r($r);
			$errors = curl_error($curl);
			$response=json_decode($response);	
			curl_close ($curl);
			if ($errors) {
				echo "cURL Error #:" . $errors;
			} else {
				echo $response;
			}
		}
		$sqlmit="UPDATE $db.tbl_smsmessages  SET i_status = '0',d_lastTriedAt=NOW(), i_retries=i_retries+1, log='$response' WHERE i_ID ='$IID'";
		$resultt = mysqli_query($link, $sqlmit) or die(mysqli_error($link));
		$k++;
				
	}
	if($num == 0)
	{
		echo "No record!!"; 
	}



/***************************************************************/

function splitString($string, $len)
{
 	$strsz = strlen($string);
	$str = $string;
	
	if ( $strsz <= $len)
		$arr = array($string);
	else
	{
		$arr = array();
		while($strsz > $len )
		{
			for ($i = $len,$j= $len; $i > 0; $i--, $j++)
			{
				if ( !ctype_alpha($str[$i-1]))
				{
					$point = $i;
					break;
				}
				else if(!ctype_alpha($str[$j-1]))
				{
					$point = $j;	
				}
			}
			$string1 = substr($str, 0, $point);
			array_push($arr,$string1);
			$str = substr($str, $point);
			$strsz = $strsz - strlen($string1);
			if ( strlen($str) <= $len)
				array_push($arr, $str);
		}
	}
	return $arr;
}

?>
