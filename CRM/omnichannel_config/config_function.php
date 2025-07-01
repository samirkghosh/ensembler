<?php 
include_once("../../config/web_mysqlconnect.php");
class OMNI_CLASS
{	
	function __construct() {
		global $link,$db;
		$this->connect = $link;
		if($_POST['action'] == 'submit_config'){
			$this->save_config();
		}
		if($_POST['action'] == 'test_channel'){
			$this->Smtp_Debug_channel();
		}
		if($_POST['action'] == 'ChannelDelete'){
			$this->ChannelDelete();
		}
		if($_POST['action'] == 'edit_config'){
			$this->EditChannelDetails();
		}
		if($_POST['action'] == 'ImapTesting'){
			$this->ImapTesting();
		}
		if($_POST['action'] == 'twitter_debug_channel'){
			$this->twitter_debug_channel();
		}
		if($_POST['action'] == 'sms_debug_channel'){
			$this->sms_debug_channel();
		}
		if($_POST['action'] == 'facebook_debug_channel'){
			$this->facebook_debug_channel();
		}
		if($_POST['action'] == 'whatsapp_debug_channel'){
			$this->whatsapp_debug_channel();
		}
	}
	
	function get_config_list($id=''){
		global $db;
		$where = '';
		if(!empty($id)){
			$where = " where id = ".$id;
		}
	  	$sql_cdr= "SELECT * from $db.omni_configuration ".$where;
  		$query=mysqli_query($this->connect,$sql_cdr);
	  	return $query;
	}
	//function to get imap data list 
	function get_imap_list($id=''){
		global $db;
		$where = '';
		if(!empty($id)){
			$where = " where I_ID = ".$id;
		}
		$sql_cdr= "SELECT * from $db.tbl_connection".$where;
  		$query=mysqli_query($this->connect,$sql_cdr);
	  	return $query;
	}
	//function to get  smtp data list 
	function get_smt_list($id=''){
		global $db;
		$where = '';
		if(!empty($id)){
			$where = " where id = ".$id;
		}
		$sql_cdr= "SELECT * from $db.tbl_smtp_connection".$where;
  		$query=mysqli_query($this->connect,$sql_cdr);
	  	return $query;
	}
	//function to get  twitter data list 
	function get_twitter_list($id=''){
		global $db;
		$where = '';
		if(!empty($id)){
			$where = " where id = ".$id;
		}
		$sql_cdr= "SELECT * from $db.tbl_twitter_connection".$where;
  		$query=mysqli_query($this->connect,$sql_cdr);
	  	return $query;
	}
	//function to get facebook data list 
	function get_facebook_list($id=''){
		global $db;
		$where = '';
		if(!empty($id)){
			$where = " where id = ".$id;
		}
		$sql_cdr= "SELECT * from $db.tbl_facebook_connection".$where;
  		$query=mysqli_query($this->connect,$sql_cdr);
	  	return $query;
	}
	//function to get whatsapp data list 
	//[Ritu][1-07-2024]
	function get_whatsapp_list($id=''){
		global $db;
		$where = '';
		if(!empty($id)){
			$where = " where id = ".$id;
		}
		$sql_cdr= "SELECT * from $db.tbl_whatsapp_connection".$where;
		//echo $sql_cdr;
  		$query=mysqli_query($this->connect,$sql_cdr);
	  	return $query;
	}
	//function to get messenger data list 
	//[Ritu][08-07-2024]
	function get_messenger_list($id=''){
		global $db;
		$where = '';
		if(!empty($id)){
			$where = " where id = ".$id;
		}
		$sql_cdr= "SELECT * from $db.tbl_messenger_connection".$where;
		//echo $sql_cdr;
  		$query=mysqli_query($this->connect,$sql_cdr);
	  	return $query;
	}
	//function to get instagram data list 
	//[Ritu][19-11-2024]
	function get_instagram_list($id=''){
		global $db;
		$where = '';
		if(!empty($id)){
			$where = " where id = ".$id;
		}
		$sql_cdr= "SELECT * from $db.tbl_instagram_connection".$where;
		//echo $sql_cdr;
  		$query=mysqli_query($this->connect,$sql_cdr);
	  	return $query;
	}
	//function to get sms data list 
	function get_sms_list($id=''){
		global $db;
		$where = '';
		if(!empty($id)){
			$where = " where id = ".$id;
		}
		$sql_cdr= "SELECT * from $db.tbl_sms_connection".$where;
  		$query=mysqli_query($this->connect,$sql_cdr);
	  	return $query;
	}
	//function to add  data  in the channel on the basis of the  channel  name 
	function save_config(){
		global $db,$link;
	    $name = $_POST['name'];
	    $serverip = $_POST['serverip'];
	    $port = $_POST['port'];
	    $userid = $_POST['userid'];
	    $password = $_POST['password'];
	    $tls = $_POST['tls'];	   
	    $date = date('Y-m-d h:i:m');
	    $channel_name = $_POST['channel_name'];
	    $token_expire_date= date('Y-m-d',strtotime($_POST['startdatetime']));

	    if($channel_name == 'Smtp'){
	    	$status = $_POST['smtp_status'];
	    	$strQrytest ="insert INTO $db.tbl_smtp_connection (v_name,channel_name,i_port,v_username,v_password,v_server,i_tls,i_status,created_date,updated_date) VALUES 
	    	('{$name}','{$channel_name}','{$port}','{$userid}','{$password}','{$serverip}','{$tls}','{$status}','{$date}','')";
    		mysqli_query($this->connect, $strQrytest);
        	echo "success";
	    }else if($channel_name == 'Imap'){
	    	$imap_type = $_POST['type_auth'];
	    	$client_secret = $_POST['clientsecret'];
    		$tenant = $_POST['tenant'];
	    	$clientId = $_POST['clientId'];
	    	$status = $_POST['imap_status'];
	    	$name = $_POST['imap_name'];
	    	$serverip = $_POST['imap_serverip']; 
	    	$imap_userid = $_POST['imap_userid'];
	    	$password = $_POST['imap_password'];
	    	$debug = '0';
	    	$strQrytest ="insert INTO $db.tbl_connection (v_connectionname,v_ipaddress,v_username,v_pasowrd,v_type,v_client_id,v_client_secret,v_tenant,v_debug,channel_name,status,created_date,updated_date,token_expire_date) VALUES ('{$name}','{$serverip}','{$imap_userid}','{$password}','{$imap_type}','{$clientId}','{$client_secret}','{$tenant}','{$debug}','{$channel_name}','{$status}','{$date}','','{$token_expire_date}')";
       		 mysqli_query($this->connect, $strQrytest);
        	echo "success";
	    }else if($channel_name == 'Twitter'){
	    	$oauth_type = $_POST['oauth_type'];
	    	$access_token = $_POST['access_token'];
    		$access_token_secret = $_POST['access_token_secret'];
	    	$consumer_key = $_POST['consumer_key'];
	    	$consumer_secret = $_POST['consumer_secret'];
	    	$status = $_POST['twitter_status'];
	    	$name = $_POST['facebook_name'];
	    	$bearer_token = $_POST['bearer_token'];
	    	$account_name = $_POST['account_name'];
	    	$debug = '0';
	    	
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://api.twitter.com/2/users/by?usernames='.$account_name.'&user.fields=created_at&expansions=pinned_tweet_id&tweet.fields=author_id%2Ccreated_at',
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
			$resp1= json_decode($response,true);
			if($resp1['data']){
				$account_id = $resp1['data'][0]['id'];
			}else{
				$account_id = '';
			}
	    	$strQrytest ="insert INTO $db.tbl_twitter_connection (channel_name,name,access_token,access_token_secret,consumer_key,consumer_secret,oauth_type,status,created_date,updated_date,bearer_token,account_name,account_id,token_expire_date) VALUES ('{$channel_name}','{$name}','{$access_token}','{$access_token_secret}','{$consumer_key}','{$consumer_secret}','{$oauth_type}','{$status}','{$date}','','{$bearer_token}','{$account_name}','{$account_id}','{$token_expire_date}')";
       		 mysqli_query($this->connect, $strQrytest);
	    }else if($channel_name == 'SMS'){
	    	$name = $_POST['sms_name'];
	    	$client_id = $_POST['client_id'];
	    	$api_key = $_POST['api_key'];
	    	$senderId = $_POST['senderId'];
	    	$status = $_POST['sms_status'];
	    	$debug = '0';
	    	$sms_type = $_POST['sms_type'];
	    	$userid = $_POST['userid'];
			$v_prefix = $_POST['prefixInput'];
	    	$sms_password = $_POST['sms_password'];
	    	$sms_apitoken = $_POST['sms_apitoken'];
	    	if($sms_type == 'smszambia'){
	    		$domainname = $_POST['domainname'];
	    	}else{
    			$domainname = $_POST['sms_domain'];
	    	}
	    	if($sms_type == 'exotel'){
	    		$api_key = $_POST['sms_apikey'];
	    	}
	    	$sms_sid = $_POST['sms_sid'];
	    	$strQrytest ="insert INTO $db.tbl_sms_connection (name,channel_name,senderId,apikey,clientId,sms_type,domain_name,userid,password,api_token,sid,status,debug_status,created_date,updated_date,v_prefix) VALUES ('{$name}','{$channel_name}','{$senderId}','{$api_key}','{$client_id}','{$sms_type}','{$domainname}','{$userid}','{$sms_password}','{$sms_apitoken}','{$sms_sid}','{$status}','{$debug}','{$date}','','{$v_prefix}')";
       		 mysqli_query($this->connect, $strQrytest);
	    }else if($channel_name == 'Whatsapp'){
	    	$name = $_POST['name'];
	    	$app_id = $_POST['app_id'];
	    	$app_token = $_POST['app_token'];
    		$whatsapp_url = $_POST['whatsapp_url'];
	    	$STD = $_POST['STD'];;
	    	$status = $_POST['whatsapp_status'];
	    	$debug = '0';
	    	$strQrytest ="insert INTO $db.tbl_whatsapp_connection (name,channel_name,app_id,app_token,whatsapp_url, STD,status,debug,created_date,updated_date,token_expire_date) VALUES ('{$name}','{$channel_name}','{$app_id}','{$app_token}','{$whatsapp_url}','{$STD}','{$status}','{$debug}','{$date}','','{$token_expire_date}')";
       		 mysqli_query($this->connect, $strQrytest);

	    }else if($channel_name == 'Messenger'){
	    	$name = $_POST['name'];
	    	$app_id = $_POST['app_id'];
	    	$access_token = $_POST['access_token'];
    		$facebook_url = $_POST['facebook_url'];
	    	$status = $_POST['messenger_status'];
	    	$debug = '0';
	    	$strQrytest ="insert INTO $db.tbl_messenger_connection (name,channel_name,app_id,access_token,facebook_url, status,debug,token_expire_date) VALUES ('{$name}','{$channel_name}','{$app_id}','{$access_token}','{$facebook_url}','{$status}','{$debug}','{$token_expire_date}')";
       		 mysqli_query($this->connect, $strQrytest);

	    }else if($channel_name == 'Instagram'){
	    	$name = $_POST['name'];
	    	$app_id = $_POST['app_id'];
	    	$access_token = $_POST['access_token'];
    		$instagram_url = $_POST['instagram_url'];
	    	$status = $_POST['instagram_status'];
	    	$debug = '0';
	    	$strQrytest ="insert INTO $db.tbl_instagram_connection (name,channel_name,app_id,access_token,instagram_url, status,debug,token_expire_date) VALUES ('{$name}','{$channel_name}','{$app_id}','{$access_token}','{$instagram_url}','{$status}','{$debug}','{$token_expire_date}')";
       		 mysqli_query($this->connect, $strQrytest);

	    }else if($channel_name == 'Facebook'){
	    	$name = $_POST['facebook_name'];
	    	$app_id = $_POST['app_id'];
	    	$app_token = $_POST['app_token'];
    		$app_secret = $_POST['app_secret'];
	    	$access_token = $_POST['access_token_facebook'];;
	    	$status = $_POST['facebook_status'];
	    	$debug = '0';
	    	$strQrytest ="insert INTO $db.tbl_facebook_connection (name,channel_name,app_id,app_token,app_secret,access_token,status,debug,created_date,updated_date,token_expire_date) VALUES ('{$name}','{$channel_name}','{$app_id}','{$app_token}','{$app_secret}','{$access_token}','{$status}','{$debug}','{$date}','','{$token_expire_date}')";
       		 mysqli_query($this->connect, $strQrytest);
	    }
		$result = mysqli_query($link, $strQrytest); 
		if (!$result){
			if (_DBGLOG_){
					DbgLog(_LOG_ERROR,__LINE__, __FILE__,"Data insertion error in configuration file  error : $strQrytest". mysqli_error($link));
			}
			$response['error'] = TRUE;
			$response['error_msg'] = "OMNICHANNEL  CONFIGURATION PANEL Database error";
			echo json_encode($response);
			exit();
		}
    	
	}
	//function  to delete the data in particular channel   from the database
	function ChannelDelete(){
		global $link,$db;
		$id = $_POST['id'];
		$channel = $_POST['channel'];
		$response = 'not sucessful'; 
		if($channel == 'Smtp'){
			$del = "delete from $db.tbl_smtp_connection where id=".$id;
			$query = mysqli_query($this->connect, $del);
			$response = "delete successfully";
			echo json_encode(['message' => $response]);
		} else if($channel == 'Imap'){
			$del = "delete from $db.tbl_connection where I_ID=".$id;
			$query = mysqli_query($this->connect, $del);
			$response = "delete successfully";
			echo json_encode(['message' => $response]);
		} else if($channel == 'Twitter'){
			$del = "delete from $db.tbl_twitter_connection where id=".$id;
			$query = mysqli_query($this->connect, $del);
			$response = "delete successfully";
			echo json_encode(['message' => $response]);
		} else if($channel == 'SMS'){
			$del = "delete from $db.tbl_sms_connection where id=".$id;
			$query = mysqli_query($this->connect, $del);
			$response = "delete successfully";
			echo json_encode(['message' => $response]);
		} else if($channel == 'Facebook'){
			$del = "delete from $db.tbl_facebook_connection where id=".$id;
			$query = mysqli_query($this->connect, $del);
			$response = "delete successfully";
			echo json_encode(['message' => $response]);
		}else if($channel == 'Whatsapp'){
			$del = "delete from $db.tbl_whatsapp_connection where id=".$id;
			$query = mysqli_query($this->connect, $del);
			$response = "delete successfully";
			echo json_encode(['message' => $response]);
		}else if($channel == 'Messenger'){
			$del = "delete from $db.tbl_messenger_connection where id=".$id;
			$query = mysqli_query($this->connect, $del);
			$response = "delete successfully";
			echo json_encode(['message' => $response]);
		}else if($channel == 'Instagram'){
			$del = "delete from $db.tbl_instagram_connection where id=".$id;
			$query = mysqli_query($this->connect, $del);
			$response = "delete successfully";
			echo json_encode(['message' => $response]);
		}
		$result = mysqli_query($link, $del); 
		if (!$result){
			if (_DBGLOG_){
					DbgLog(_LOG_ERROR,__LINE__, __FILE__,"Data Deletion error in configuration file  error : $del". mysqli_error($link));
			}
			$response['error'] = TRUE;
			$response['error_msg'] = "OMNICHANNEL  CONFIGURATION PANEL Database error";
			echo json_encode($response);
			exit();
		}
		
	}
	//function to edit the details  on the basis of the channel and id 
	function EditChannelDetails(){
		global $db,$link;
		$id = $_POST['id'];
	    $channel_name = $_POST['channel_name'];	    
	    $date = date('Y-m-d h:i:m');
	    $token_expire_date= date('Y-m-d',strtotime($_POST['startdatetime']));
	    if($channel_name == 'Facebook'){
	    	$name = $_POST['facebook_name'];
	    	$app_id = $_POST['app_id'];
	    	$app_token = $_POST['app_token'];
    		$app_secret = $_POST['app_secret'];
	    	$access_token = $_POST['access_token_facebook'];;
	    	$status = $_POST['facebook_status'];
	    	$debug = '0';
	    	$strQrytest ="update  $db.tbl_facebook_connection set name='{$name}',channel_name='{$channel_name}',app_id='{$app_id}',app_token='{$app_token}',app_secret='{$app_secret}',access_token='{$access_token}',status='{$status}',updated_date='{$date}',token_expire_date='{$token_expire_date}' where id='{$id}'";
    		mysqli_query($this->connect, $strQrytest);
        	echo 'update successfully';
	    }if($channel_name == 'Whatsapp'){
	    	$name = $_POST['name'];
	    	$app_id = $_POST['app_id'];
	    	$app_token = $_POST['app_token'];
    		$whatsapp_url = $_POST['whatsapp_url'];
	    	$STD = $_POST['STD'];;
	    	$status = $_POST['whatsapp_status'];
	    	$debug = '0';
	    	$strQrytest ="update  $db.tbl_whatsapp_connection set name='{$name}',channel_name='{$channel_name}',app_id='{$app_id}',app_token='{$app_token}',whatsapp_url='{$whatsapp_url}',STD='{$STD}',status='{$status}',updated_date='{$date}',token_expire_date='{$token_expire_date}' where id='{$id}'";
    		mysqli_query($this->connect, $strQrytest);
        	echo 'update successfully';
	    }if($channel_name == 'Messenger'){
	    	$name = $_POST['name'];
	    	$app_id = $_POST['app_id'];
	    	$access_token = $_POST['access_token'];
    		$facebook_url = $_POST['facebook_url'];
	    	$status = $_POST['messenger_status'];
	    	$debug = '0';
	    	$strQrytest ="update  $db.tbl_messenger_connection set name='{$name}',channel_name='{$channel_name}',app_id='{$app_id}',access_token='{$access_token}',facebook_url='{$facebook_url}',status='{$status}',token_expire_date='{$token_expire_date}' where id='{$id}'";
    		mysqli_query($this->connect, $strQrytest);
        	echo 'update successfully';
	    }if($channel_name == 'Instagram'){
	    	$name = $_POST['name'];
	    	$app_id = $_POST['app_id'];
	    	$access_token = $_POST['access_token'];
    		$instagram_url = $_POST['instagram_url'];
	    	$status = $_POST['instagram_status'];
	    	$debug = '0';
	    	$strQrytest ="update  $db.tbl_instagram_connection set name='{$name}',channel_name='{$channel_name}',app_id='{$app_id}',access_token='{$access_token}',instagram_url='{$instagram_url}',status='{$status}',token_expire_date='{$token_expire_date}' where id='{$id}'";
    		mysqli_query($this->connect, $strQrytest);
        	echo 'update successfully';
	    }
	    else if($channel_name == 'SMS'){
	    	$name = $_POST['sms_name'];
	    	$client_id = $_POST['client_id'];
	    	$api_key = $_POST['api_key'];
	    	$senderId = $_POST['senderId'];
	    	$status = $_POST['sms_status'];
	    	$v_prefix = $_POST['prefixInput'];

	    	$sms_type = $_POST['sms_type'];
	    	$userid = $_POST['userid'];
	    	$sms_password = $_POST['sms_password'];
	    	$sms_apitoken = $_POST['sms_apitoken'];
	    	if($sms_type == 'smszambia'){
	    		$domainname = $_POST['domainname'];
	    	}else{
    			$domainname = $_POST['sms_domain'];
	    	}
	    	$sms_sid = $_POST['sms_sid'];
	    	if($sms_type == 'exotel'){
	    		$api_key = $_POST['sms_apikey'];
	    	}
	    	$strQrytest ="update  $db.tbl_sms_connection set name='{$name}',channel_name='{$channel_name}',senderId='{$senderId}',apikey='{$api_key}',clientId='{$client_id}',sms_type='{$sms_type}',domain_name='{$domainname}',userid='{$userid}',password='{$sms_password}',api_token='{$sms_apitoken}',sid='{$sms_sid}',status='{$status}',updated_date='{$date}',v_prefix='{$v_prefix}' where id='{$id}'";
    		mysqli_query($this->connect, $strQrytest);
        	echo 'update successfully';
	    }else if($channel_name == 'Twitter'){
	    	$oauth_type = $_POST['oauth_type'];
	    	$access_token = $_POST['access_token'];
    		$access_token_secret = $_POST['access_token_secret'];
	    	$consumer_key = $_POST['consumer_key'];
	    	$consumer_secret = $_POST['consumer_secret'];
	    	$status = $_POST['twitter_status'];
	    	$name = $_POST['facebook_name'];
	    	$bearer_token = $_POST['bearer_token'];
	    	$account_name = $_POST['account_name'];
	    	$strQrytest ="update  $db.tbl_twitter_connection set name='{$name}',channel_name='{$channel_name}',access_token='{$access_token}',access_token_secret='{$access_token_secret}',consumer_key='{$consumer_key}',consumer_secret='{$consumer_secret}',oauth_type='{$oauth_type}',status='{$status}',updated_date='{$date}',bearer_token='{$bearer_token}',account_name='{$account_name}',token_expire_date='{$token_expire_date}' where id='{$id}'";
    		mysqli_query($this->connect, $strQrytest);
        	echo 'update successfully';
	    }else if($channel_name == 'Imap'){
	    	$imap_type = $_POST['type_auth'];
	    	$client_secret = $_POST['clientsecret'];
    		$tenant = $_POST['tenant'];
	    	$clientId = $_POST['clientId'];
	    	$status = $_POST['imap_status'];
	    	$name = $_POST['imap_name'];
	    	$serverip = $_POST['imap_serverip'];
	    	$imap_userid = $_POST['imap_userid'];
	    	$password = $_POST['imap_password'];

	    	$strQrytest ="update  $db.tbl_connection set v_connectionname='{$name}',channel_name='{$channel_name}',v_ipaddress='{$serverip}',v_username='{$imap_userid}',v_pasowrd='{$password}',v_type='{$imap_type}',v_client_id='{$clientId}',v_client_secret='{$client_secret}',v_tenant='{$tenant}',status='{$status}',updated_date='{$date}',token_expire_date='{$token_expire_date}' where I_ID='{$id}'";
    		mysqli_query($this->connect, $strQrytest);
        	echo 'update successfully';
	    }else if($channel_name == 'Smtp'){
	    	$status = $_POST['smtp_status'];
	    	$name = $_POST['name'];
		    $serverip = $_POST['serverip'];
		    $port = $_POST['port'];
		    $userid = $_POST['userid'];
		    $password = $_POST['password'];
		    $tls = $_POST['tls'];
		    $strQrytest ="update  $db.tbl_smtp_connection set v_name='{$name}',channel_name='{$channel_name}',i_port='{$port}',v_username='{$userid}',v_password='{$password}',v_server='{$serverip}',i_tls='{$tls}',i_status='{$status}',updated_date='{$date}' where id='{$id}'";
    		mysqli_query($this->connect, $strQrytest);
        	echo 'update successfully';	
	    }
    	$result = mysqli_query($link, $strQrytest); 
		if (!$result){
			if (_DBGLOG_){
					DbgLog(_LOG_ERROR,__LINE__, __FILE__,"Data updation error in configuration file  error : $strQrytest". mysqli_error($link));
			}
			$response['error'] = TRUE;
			$response['error_msg'] = "OMNICHANNEL  CONFIGURATION PANEL Database error";
			echo json_encode($response);
			exit();
		}
	}
	//fucntion to handle the   debugging of the imap  channel 
	function ImapTesting(){
		global $db;
		$id = $_POST['id'];
		$sql_cdr= "SELECT * from $db.omni_configuration where id=".$id;
  		$query=mysqli_query($this->connect,$sql_cdr);
  		$config = mysqli_fetch_array($query);

  		if($config['imap_type'] == 'OAuth'){
	  		$hostname=$config['server_ip'];
			$username=$config['userid'];
			$password=$config['password'];

			/* connect to mail server */
			$inbox = imap_open($hostname,$username,$password);
			if(imap_errors()){
				print_r(imap_errors());
			}else{

			}
			/*grab emails */
			$emails = imap_search($inbox,'ALL');
			print_r($emails);
			echo "Email Count - > ".count($emails)."<br>"; 
			/*close the connection */
			imap_close($inbox);
		}
	}
	//fucntion to handle the debug request of the smtp channel 
	function Smtp_Debug_channel(){
		global $db;
		require_once '/var/www/html/ensembler/PHPMailer-5.2.28/PHPMailerAutoload.php';
		$id = $_POST['id'];
		$sql_cdr= "SELECT * from $db.tbl_smtp_connection where id=".$id;
  		$query=mysqli_query($this->connect,$sql_cdr);
  		$config = mysqli_fetch_array($query);
 		$fromAddr  = $config['v_username'];
		$toAddr = $_POST['email_to'];
		if($_POST['body'] != ''){
			$V_Body =  $_POST['body'];
		}else{
			$V_Body = "Dear Customer,\n Test messages from omni channel team, \n Thank you";
		} 	
		if($_POST['subject'] != ''){
			$subject =  $_POST['subject'];
		}else{
			$subject = "Configuration Mail Test";
		}	
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$add_cc= '';
		$add_bcc='';
		if ($config['i_tls'] == "1"){
			$mail->SMTPAuth = true;
			$mail->SMTPSecure = "tls";
		}
		$mail->SMTPDebug = 0;
		$mail->SMTPOptions = array(
			'ssl' => array(
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
			)
		);
		$mail->Host = $config['v_server'];
		$mail->Port = $config['i_port'];
		$mail->Username = $config['v_username'];;
		$mail->Password = $config['v_password'];

		$mail->AddAddress($toAddr);
		if ( !empty($config['add_cc']))
				$add_cc = $row['add_cc'];
				$mail->AddCC($add_cc);
		if ( !empty($configs['add_bcc']))
			$add_bcc = $row['add_bcc'];
			$mail->AddBCC($add_bcc);

		$mail->From = $fromAddr; 
		$mail->FromName = $fromAddr; //EMAIL_FROM_NAME;		
		$mail->WordWrap = 50;
		$mail->IsHTML(true);
		$mail->Subject  =  $subject;
		$mail->Body     =  $V_Body;
		$send=$mail->Send();
		if($send==1){
			$strQrytest ="update  $db.tbl_smtp_connection set i_debug='1' where id=".$id;
			mysqli_query($this->connect, $strQrytest);
			echo "Mail successfully send to: "."[ID= ".$toAddr."]\n";		 	
		}else{
			$strQrytest ="update  $db.tbl_smtp_connection set i_debug='0',i_status='0' where id=".$id;
			mysqli_query($this->connect, $strQrytest);
			echo "Mail not sent. Error: ". $mail->ErrorInfo."\n";
			// $error = addslashes($mail->ErrorInfo);
		}
		$result = mysqli_query($link, $strQrytest); 
		if (!$result){
			if (_DBGLOG_){
					DbgLog(_LOG_ERROR,__LINE__, __FILE__,"Data Debugging error in configuration SMTP file  error : $strQrytest". mysqli_error($link));
			}
			$response['error'] = TRUE;
			$response['error_msg'] = "OMNICHANNEL  CONFIGURATION PANEL Database error";
			echo json_encode($response);
			exit();
		}
	}
	//FUNCTION TO DEBUG THE TWITTER CHANNEL 
	function twitter_debug_channel(){
		global $link,$db;
		$id = $_POST['id'];
		$recipient_id = $_POST['recipient_id'];
		$message = $_POST['message'];
		$sql_cdr= "SELECT * from $db.tbl_twitter_connection where id=".$id;
  		$query=mysqli_query($this->connect,$sql_cdr);
  		$config = mysqli_fetch_array($query);
  		// auth v2 configuration
  		if($config['oauth_type'] == '0'){
  			$bearer_token = $config['bearer_token'];
  			$account_name = $config['account_name'];

  			$curl_1 = curl_init();
  			curl_setopt_array($curl_1, array(
			  CURLOPT_URL => 'https://api.twitter.com/2/users/by?usernames='.$account_name.'&user.fields=created_at&expansions=pinned_tweet_id&tweet.fields=author_id%2Ccreated_at',
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
			$response_1 = curl_exec($curl_1);
			curl_close($curl_1);
			$resp1= json_decode($response_1,true);			
			if($resp1['data']){
				$account_id = $resp1['data'][0]['id'];
			}else{
				$account_id = '';
			}
  			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://api.twitter.com/2/users/1468212258635333635/mentions?media.fields=duration_ms%2Cheight%2Cmedia_key%2Cpreview_image_url%2Ctype%2Curl%2Cwidth%2Cpublic_metrics%2Cnon_public_metrics%2Corganic_metrics%2Cpromoted_metrics%2Calt_text%2Cvariants&expansions=attachments.media_keys%2Cauthor_id%2Cedit_history_tweet_ids%2Centities.mentions.username%2Cgeo.place_id%2Cin_reply_to_user_id%2Creferenced_tweets.id%2Creferenced_tweets.id.author_id&tweet.fields=attachments%2Cauthor_id%2Cconversation_id%2Ccreated_at%2Centities%2Cid%2Cin_reply_to_user_id',
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
			$resp = json_decode($response,true);
		    if ($resp["errors"][0]["message"] != ""){
				$strQrytest ="update  $db.tbl_twitter_connection set debug_status='0',status='0' where id=".$id;
				mysqli_query($this->connect, $strQrytest);	
				$response = "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>" . $resp[errors][0]["message"] . "</em></p>";	
			}else if(isset($resp['data'])){			
				$strQrytest ="update  $db.tbl_twitter_connection set debug_status='1' , account_id = $account_id where id=".$id;
				echo $strQrytest;
				mysqli_query($this->connect, $strQrytest);
				$count = count($resp['data']);
				$response = "<p>Message Get sucessfully Total Count = <em>".$count."</em></p>";
			}
  		}else{ // auth v1 configuration
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
	        if($_POST['method'] == 'POST'){
	        	$url = 'https://api.twitter.com/1.1/direct_messages/events/new.json';
	        	$requestMethod = 'POST';
	        }else{
        		$url = 'https://api.twitter.com/1.1/direct_messages/events/list.json';
        		$requestMethod = 'GET';
	        }
	        $keysetting = $this->buildOauthKey($url,$requestMethod,$config);
	      	$urlNew = $url.'?'.$keysetting;
	      	
	  		if($_POST['method'] == 'POST'){
		  		$postfields =array(
		            "event" => array(
		                "type" => "message_create",
		                "message_create" => array(
		                    "target" => array(
		                        "recipient_id" =>$recipient_id
		                    ),
		                    "message_data" => array(
		                        "text" => $message
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
	        }else{
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
	  		}
		    $response = curl_exec($curl);
		    curl_close($curl);
		    $resp = json_decode($response,true);
		    if ($resp["errors"][0]["message"] != ""){
				$strQrytest ="update  $db.tbl_twitter_connection set debug_status='0',status='0' where id=".$id;
				mysqli_query($this->connect, $strQrytest);	
				$response = "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>" . $resp[errors][0]["message"] . "</em></p>";	 	
			}else if(isset($resp['event'])){
				$strQrytest ="update  $db.tbl_twitter_connection set debug_status='1' where id=".$id;
				mysqli_query($this->connect, $strQrytest);
				$response = "<p><strong>Message sent sucessfully</strong></p>";
			}else if(isset($resp['events'])){
				$strQrytest ="update  $db.tbl_twitter_connection set debug_status='1' where id=".$id;
				mysqli_query($this->connect, $strQrytest);
				$count = count($resp['events']);
				$response = "<p>Message Get sucessfully Total Count = <em>".$count."</em></p>";
			}
		}
		$result = mysqli_query($link, $strQrytest); 
		if (!$result){
			if (_DBGLOG_){
					DbgLog(_LOG_ERROR,__LINE__, __FILE__,"Data Debugging error in configuration Twitter file  error : $strQrytest". mysqli_error($link));
			}
			$response['error'] = TRUE;
			$response['error_msg'] = "OMNICHANNEL  CONFIGURATION PANEL Database error";
			echo json_encode($response);
			exit();
		}
	    echo $response;
	}
	//Function to build  authorization key for the  twitter channel 
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

        $base_info = $this->buildBaseString1($url, $requestMethod, $oauth);
        $composite_key = rawurlencode($consumer_secret) . '&' . rawurlencode($oauth_access_token_secret);
        $oauth_signature = base64_encode(hash_hmac('sha1', $base_info, $composite_key, true));
        $oauth['oauth_signature'] = $oauth_signature;

        foreach($oauth as $key => $value){
            if (in_array($key, array('oauth_consumer_key', 'oauth_nonce', 'oauth_signature',
                'oauth_signature_method', 'oauth_timestamp', 'oauth_token', 'oauth_version'))) {
                $values[] = "$key=" . rawurlencode($value);
            }
        }
        $return = implode('&', $values);
        return $return;
    }
    function buildBaseString1($baseURI, $method, $params){
        $return = array();
        ksort($params);
        foreach($params as $key => $value)
        {
            $return[] = rawurlencode($key) . '=' . rawurlencode($value);
        }
        return $method . "&" . rawurlencode($baseURI) . '&' . rawurlencode(implode('&', $return));
    }
	//function to debug the sms  channel 
    function sms_debug_channel(){
		global $link,$db;
		$id = $_POST['id'];
		$number = $_POST['number'];
		$message = $_POST['sms_message'];
		$sql_cdr= "SELECT * from $db.tbl_sms_connection where id=".$id;
  		$query=mysqli_query($this->connect,$sql_cdr);
  		$config = mysqli_fetch_array($query);

  		if($config['sms_type'] == 'onfonmedia'){
  			$auth['SenderId'] = $config['senderId'];
	        $auth['ApiKey'] = $config['apikey'];
	        $auth['ClientId'] = $config['clientId'];
	        $auth['MessageParameters'][0]['Number'] = $number;
	        $auth['MessageParameters'][0]['Text'] = $message;
	        $json_data = json_encode($auth);

	        $urlNew = 'https://api.onfonmedia.co.ke/v1/sms/SendBulkSMS';
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
		      CURLOPT_POSTFIELDS =>$json_data
		    ));
  		}else if($config['sms_type'] == 'url_based'){
  			$WebSMSUserID = $config['userid'];
  			$WebSMSPwd = $config['password'];
  			$WebSMSSender = $config['senderId'];
  			$parts = $this->getParts($message, 512);

  			for ($i = 0; $i < count($parts); $i++){
			   	/* Replace space with + */
			   	$msg = str_replace(" ","+", $parts[$i]);
			   	$msg = str_replace("\n","+", $msg);
	  			$URL="smsservice/httpapi?username={$WebSMSUserID}&password={$WebSMSPwd}&sender_id={$WebSMSSender}&phone=%s&msg=%s";
		        $DomainName= $config['domain_name'];
		        $MyURL = sprintf($URL, $number, $msg);
		        $urlNew =  $DomainName."/".$MyURL;

		      	$options = array(
			        CURLOPT_RETURNTRANSFER => true,   // return web page
			        CURLOPT_HEADER         => false,  // don't return headers
			        CURLOPT_FOLLOWLOCATION => true,   // follow redirects
			        CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
			        CURLOPT_ENCODING       => "",     // handle compressed
			        CURLOPT_USERAGENT      => "test", // name of client
			        CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
			        CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
			        CURLOPT_TIMEOUT        => 120,    // time-out on response
				CURLOPT_HTTPGET => true,
			    );
		      	$curl = curl_init($urlNew);
	    		curl_setopt_array($curl, $options);
	    	}
  		}else if($config['sms_type'] == 'exotel'){
  			$from = '09513886363';
			$post_data = array(
			    'From'     => $from,
			    'To'       => $number,
			    'Body' => $message ,
			    'accept'=> "application/json"
			);
			$api_key    = $config['apikey']; 
			$api_token  = $config['api_token']; 
			$exotel_sid = $config['sid'];  
			$exotel_sid = $config['sid'];
			#Replace <subdomain> with the region of your account
			#<subdomain> of Singapore cluster is @api.exotel.com
			#<subdomain> of Mumbai cluster is @api.in.exotel.com
			$url    = "https://" . $api_key . ":" . $api_token ."@api.exotel.in/v1/Accounts/" . $exotel_sid ."/Sms/send";  
			$json_data = json_encode($post_data);
			$curl = curl_init();
		      curl_setopt_array($curl, array(
		      CURLOPT_URL => $url,
		      CURLOPT_RETURNTRANSFER => true,
		      CURLOPT_ENCODING => '',
		      CURLOPT_MAXREDIRS => 10,
		      CURLOPT_TIMEOUT => 0,
		      CURLOPT_FOLLOWLOCATION => true,
		      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		      CURLOPT_CUSTOMREQUEST => 'POST',
		      CURLOPT_POSTFIELDS =>$post_data
		    ));
		 //    $http_result = curl_exec($curl);
			// curl_close($curl); 
  		}       
	    $response = curl_exec($curl);
	    curl_close($curl);
	    if($config['sms_type'] == 'url_based'){
    		if($response == ''){
				$strQrytest ="update  $db.tbl_sms_connection set debug_status='0',status='0' where id=".$id;
				mysqli_query($this->connect, $strQrytest);	 				
			}else{
				$strQrytest ="update  $db.tbl_sms_connection set debug_status='1',status='1' where id=".$id;
				mysqli_query($this->connect, $strQrytest);
				$response = "<p><strong>Message sent sucessfully</strong></p>";	
			}
		}else if($config['sms_type'] == 'exotel'){
			if($response == ''){
				$strQrytest ="update  $db.tbl_sms_connection set debug_status='0',status='0' where id=".$id;
				mysqli_query($this->connect, $strQrytest);	 				
			}else{
				$strQrytest ="update  $db.tbl_sms_connection set debug_status='1',status='1' where id=".$id;
				mysqli_query($this->connect, $strQrytest);
				$response = "<p><strong>Message sent sucessfully</strong></p>";	
			}
	    }else{
		    $resp = json_decode($response,true);
		    if($resp['Data'][0]['MessageErrorCode'] != '0'){
				$strQrytest ="update  $db.tbl_sms_connection set debug_status='0',status='0' where id=".$id;
				mysqli_query($this->connect, $strQrytest);	
				$response = "<h3>Sorry, there was a problem.</h3><p>SMS returned the following error message:</p><p><em>" . $resp["Data"][0]["MessageErrorDescription"] . "</em></p>"; 	
			}else if($resp['Data'][0]['MessageErrorCode'] == '0'){
				$strQrytest ="update  $db.tbl_sms_connection set debug_status='1',status='1' where id=".$id;
				mysqli_query($this->connect, $strQrytest);
				$response = "<p><strong>Message sent sucessfully</strong></p>";	
			}
		}
		$result = mysqli_query($link, $strQrytest); 
		if (!$result){
			if (_DBGLOG_){
					DbgLog(_LOG_ERROR,__LINE__, __FILE__,"Data Debugging error in configuration SMS file  error : $strQrytest". mysqli_error($link));
			}
			$response['error'] = TRUE;
			$response['error_msg'] = "OMNICHANNEL  CONFIGURATION PANEL Database error";
			echo json_encode($response);
			exit();
		}
	    echo $response;
	}
	function getParts($str, $len){
		$parts= array();
		$n = strlen($str)/$len;
		for ($i=0; $i < $n; $i++)
		{
			$parts[] = substr($str,0,$len);
			$str = substr($str,$len);
		}
		return $parts;
	}
	//function to debug the facebook channel 
	function facebook_debug_channel(){
		global $db;
		$id = $_POST['id'];
		$postid = $_POST['postid'];
		$sql_cdr= "SELECT * from $db.tbl_facebook_connection where id=".$id;
  		$query=mysqli_query($this->connect,$sql_cdr);
  		$config = mysqli_fetch_array($query);

        $app_id	 = $config['app_id'];
        $app_token = $config['app_token'];
        $app_secret = $config['app_secret'];
        $access_token = $config['access_token'];

        $urlNew = 'https://graph.facebook.com/v16.0/me/posts?&access_token='.$access_token;
      	$curl = curl_init();
	      curl_setopt_array($curl, array(
	      CURLOPT_URL => $urlNew,
	      CURLOPT_RETURNTRANSFER => true,
	      CURLOPT_ENCODING => '',
	      CURLOPT_MAXREDIRS => 10,
	      CURLOPT_TIMEOUT => 0,
	      CURLOPT_FOLLOWLOCATION => true,
	      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
	    ));
	    $response = curl_exec($curl);
	    curl_close($curl);
	    $resp = json_decode($response,true);
	    if(isset($resp['error'])){
			$strQrytest ="update  $db.tbl_facebook_connection set debug='0', status='0' where id=".$id;
			mysqli_query($this->connect, $strQrytest);	 	
			$response = "<p><strong>".$resp['error']['message']."</strong></p>";	
		}else if(isset($resp['data'])){
			$strQrytest ="update  $db.tbl_facebook_connection set debug='1' where id=".$id;
			mysqli_query($this->connect, $strQrytest);
			$count = count($resp['data']);	
			$response = "<p>Facebook Posts Get sucessfully Total Count = <em>".$count."</em></p>";
		}
	    echo $response;
	}
	//function to debug the whatsapp channel 
	function whatsapp_debug_channel(){
		global $db;
		$id = $_POST['id'];
		$postid = $_POST['postid'];
		$sql_cdr= "SELECT * from $db.tbl_whatsapp_connection where id=".$id;
  		$query=mysqli_query($this->connect,$sql_cdr);
  		$config = mysqli_fetch_array($query);

        $app_id	 = $config['app_id'];
        $app_token = $config['app_token'];
        $app_secret = $config['app_secret'];
        $access_token = $config['access_token'];

        $urlNew = 'https://graph.whatsapp.com/v16.0/me/posts?&access_token='.$access_token;
      	$curl = curl_init();
	      curl_setopt_array($curl, array(
	      CURLOPT_URL => $urlNew,
	      CURLOPT_RETURNTRANSFER => true,
	      CURLOPT_ENCODING => '',
	      CURLOPT_MAXREDIRS => 10,
	      CURLOPT_TIMEOUT => 0,
	      CURLOPT_FOLLOWLOCATION => true,
	      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
	    ));
	    $response = curl_exec($curl);
	    curl_close($curl);
	    $resp = json_decode($response,true);
	    if(isset($resp['error'])){
			$strQrytest ="update  $db.tbl_whatsapp_connection set debug='0', status='0' where id=".$id;
			mysqli_query($this->connect, $strQrytest);	 	
			$response = "<p><strong>".$resp['error']['message']."</strong></p>";	
		}else if(isset($resp['data'])){
			$strQrytest ="update  $db.tbl_whatsapp_connection set debug='1' where id=".$id;
			mysqli_query($this->connect, $strQrytest);
			$count = count($resp['data']);	
			$response = "<p>Whatsapp Posts Get sucessfully Total Count = <em>".$count."</em></p>";
		}
	    echo $response;
	}
}
$controller = new OMNI_CLASS();
?>