<?php
/***
 * Auth: Aarti Ojha
 * Date: 19-06-2024
 * Description: this file handling license function 
 * 
**/
include_once("../config/web_mysqlconnect.php"); // Connection to database // Please do not remove this
include_once("../../companyTrackerConnection.php"); // Connection to database // Please do not remove this

$user = $_REQUEST['user'];
$type = $_REQUEST['type'];
$action = $_REQUEST['action'];
$companyid = $_REQUEST['companyid'];

$db = get_dbname($companyid);


$link = mysqli_connect($configdbhost, $configdbuser, $configdbpass); // Establish database connection
mysqli_select_db($link, $db); // Select database
function get_dbname($companyid) {
    global $CampTrackConn;
    $query = $CampTrackConn->query("SELECT related_database_name FROM companies WHERE company_id ='$companyid'");
    $row = $query->fetch_row();
    return $row[0];
}

function get_userid($user){
    global $link,$db;
    $query = $link->query("SELECT AtxUserID FROM $db.uniuserprofile WHERE AtxUserName ='$user' ");
    $row = $query->fetch_row();
    return $row[0];
}

function check_concurrent_user($type)
{
    global $link,$db;  
    // $query = "SELECT count(*) as total FROM `concurrent_users` WHERE `user_type`='$type' AND `status` = '1' ";
    $query = "SELECT count(*) as total FROM $db.`concurrent_users` WHERE `status` = '1' ";
    $fetch = $link->query($query)->fetch_assoc();
    if($fetch == null) 
    {
        $count = '';
    }else
    {
        $count = $fetch['total'];
    }
    return $count;
}

function check_duplicate_user($user,$type)
{
    global $link,$db;  
    $query = "SELECT id FROM $db.`concurrent_users` WHERE `user` = '$user' AND `user_type` = '$type'  AND `status` = '1' ";
    $fetch = $link->query($query)->fetch_assoc();
    if($fetch == null) 
    {
        $id = '';
    }else
    {
        $id = $fetch['id'];
    }
    
    return $id;
}

function update_concurrent_user($user)
{
    global $link,$db;  
    $query = "UPDATE $db.`concurrent_users` SET `timestamp` = NOW() WHERE `user` = '$user' ";
    $result = $link->query($query);
    return $result;

}

function insert_concurrent_user($user,$type) 
{
    global $link,$db;  
    $query = "INSERT INTO $db.`concurrent_users` (`user`,`user_type`) VALUES ('$user','$type') ";
    $result = $link->query($query);
    return $result;
}

function get_max_license_count($type, &$licenseType) 
{

    global $link,$db;  
    $query = "SELECT license_count,license_type FROM $db.`tbl_license` WHERE `user_type`='$type' ";
    $fetch = $link->query($query)->fetch_assoc();
    if($fetch == null) 
    {
        $count = 0;
        $licenseType ='';
    }else
    {
        $count = $fetch['license_count'];
        $licenseType = $fetch['license_type'];
    }
    return $count;
    
   
}

function get_classify_agent($user)
{
    global $db,$link;
    $query = "SELECT i_classify FROM $db.`uniuserprofile` WHERE `AtxUserName`='$user' ";
    $fetch = $link->query($query)->fetch_assoc();
    $classify = $fetch['i_classify'];
    return $classify;

}

function check_already_login($user){
    global $db,$link;
    $response = '';
    $sql_name="SELECT login_status,login_datetime,id FROM $db.uniuserprofile WHERE AtxUserName='$user' AND AtxUserStatus = 1";
    $res_name=mysqli_query($link,$sql_name);
    $num_user_login=mysqli_num_rows($res_name);
    if($num_user_login>0){
       $row_name=mysqli_fetch_array($res_name);
       if($row_name['id'] != '1'){
          $user_status=$row_name['login_status'];
          if($user_status == 'online'){
            $response = 1;
          } else {
            $response = 0;
          } 
       }  
    }
    return $response;
}

$response = array();

if($action == 'check') {
    $licenseType ='';
   $licenseCount = get_max_license_count($type, $licenseType);

   if ($licenseType == 'concurrent')
   {
    
        $duplicate_id = check_duplicate_user($user,$type);
        
        if(!empty($duplicate_id)) 
        {
        
            $result = 0;
            $msg = 'User is already Logged-in';
        
        }else 
        {
            $chk_concurr = check_concurrent_user($type);
            
            if($chk_concurr >= $licenseCount) 
            {
                //not able to login
                $result = 0;
                $msg = 'You have exceeded the number of concurrent logins!!';
            }else
            {
            
                    $insert_con = insert_concurrent_user($user,$type);  
            
                    if($insert_con == true ) 
                    {
                        $result = 1;
                        $msg = 'success';
                    }
            
            }
        
        }
   }
   else{
        $result = 1;
        $msg = "success";
   }

    if ( $result == 1)
    {
        $response['result']='success';
        $response['msg']= $msg;
    }
    else{
        $response['result']='failed';
        $response['msg']= $msg;
    }

    echo json_encode($response,true);
    exit();

}else if($action == 'remove') {

    if(!empty($user))  
    {
        //delete concurrent user 
        $c_q=mysqli_query($link,"DELETE FROM $db.`concurrent_users` WHERE `user`='$user'");

        $userid = get_userid($user);
        /* Code for updating telephony flag from tbl_mst_user_company table */
        $link->query("UPDATE $db.`tbl_mst_user_company` SET telephony_flag = 0 WHERE I_UserID = '$userid' ");
    
        if ($c_q == true)
        {
            $response['result']='success';
            $response['msg']='successfully removed';
        }
        else{
            $response['result']='failed';
            $response['msg']='failed to remove';
        }
    
    
        echo json_encode($response,true);
        exit();

    }else 
    {
        $response['result']='failed';
        $response['msg']='Undefined User';
        echo json_encode($response,true);
        exit();

    }


}else if($action == 'update') {

    if(!empty($user)) 
    {
        $update = update_concurrent_user($user);
    }


}else if($action == 'refresh') {

$query_1 = "DELETE FROM $db.`concurrent_users` WHERE `timestamp` < (NOW() - INTERVAL 1 MINUTE) ";
$c_q_1=mysqli_query($link,$query_1);

$query_2 = "DELETE FROM $db_asterisk.`autodial_live_agents` WHERE `last_update_time` < (NOW() - INTERVAL 1 MINUTE) ";
$c_q_2=mysqli_query($link,$query_2);

/* Code for updating telephony flag from tbl_mst_user_company table */
$link->query("UPDATE $db.`tbl_mst_user_company` SET telephony_flag = 0 WHERE `last_attemped_time` < (NOW() - INTERVAL 1 MINUTE) ");


}else if($action == 'classify') {

$classify = get_classify_agent($user);

if($classify == 1)
{
    $result = 0;
    $msg = "This agent doesn't have rights to use telephony, Please contact administrator";
}else
{
    $result = 1;
    $msg = 'success';
}

 if ( $result == 1)
 {
     $response['result']='success';
     $response['msg']= $msg;
 }
 else{
     $response['result']='failed';
     $response['msg']= $msg;
 }

 echo json_encode($response,true);
 exit();

} else if($action == 'userState') {

    $login_check = check_already_login($user);
    if ($login_check == 1) {
        $response['result']='failed';
        $response['msg']= "Some one is already loggedin, Loggout first before login.";
    } else {
        $response['result']='success';
        $response['msg']= "success";
    }

    echo json_encode($response,true);
    exit();

}

?>

