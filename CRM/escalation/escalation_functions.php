<?php

/***
 * Author : farhan akhtar
 * LastModified on :: 08-05-2025 
 * Description: This file handles escalation functions.
*/


include "/var/www/html/ensembler/config/web_mysqlconnect.php"; // Include database connection file
include "/var/www/html/ensembler/CRM/web_function.php";


$today_date = date('Y-m-d H:i:s');
$to_date = date('Y-m-d H:i:s');
$from = $from_email; // define in common file
$holidays = ["2023-12-25"]; // holiday dates add here comma seperated

function get_subcat_data($link, $db, $resolution){

    $query = $link->query("SELECT `id`, `subcategory`, `$resolution`,`level1_users`,`level2_users`,`level3_users` FROM $db.`web_subcategory` WHERE `status` IN (1) AND `$resolution` NOT IN ('')");
    $data = [];
    while($row = $query->fetch_assoc()):
        $data[] = $row;
    endwhile;

    return $data;
}

function user_info($id){
    global $db,$link;
	$query=$link->query("select AtxEmail,AtxHomePhone,AtxDisplayName from $db.uniuserprofile where AtxUserID='$id'");
    $fetch = $query->fetch_assoc();
    return $fetch;
}
function getHoursfromdates($from, $today_date){
	$hours  ='' ;
	$timestamp1 = strtotime($today_date);	// convert into seconds 
	$timestamp2 = strtotime($from);	// convert into seconds 
	$hours = round(abs($timestamp1 - $timestamp2)/(60*60));	// convert into Hours
	return $hours ;
}

function get_pending_cases($link, $db, $to_date, $level) {
   
    $sql = "SELECT 
                a.fname, a.email, a.phone,
                p.iPID, p.vCustomerID, p.vCaseType, p.vCategory, p.iCaseStatus,
                p.vSubCategory, p.vProjectID, p.ticketid, p.d_createDate
            FROM 
                `$db`.web_accounts AS a
            INNER JOIN 
                `$db`.web_problemdefination AS p
                ON a.AccountNumber = p.vCustomerID
            WHERE 
                p.d_createDate <= '$to_date'
                AND p.vSubCategory != 0
        ";

    if ($level == 1) {
        $sql .= " AND p.escalate_status = 0 AND p.iCaseStatus IN (1, 8)";
    } elseif (in_array($level, [2, 3])) {
        $sql .= " AND p.escalate_status = 1 AND p.escalation_level = " . ($level - 1);
    }

    $query = $link->query($sql);
    if (!$query) {
        error_log("Query failed: {$link->error}");
        return [];
    }

    $data = [];
    while ($row = $query->fetch_assoc()) {
        $data[] = $row;
    }

    return $data;
}

function get_escalation_users($level){

    global $db,$link;
	$query= $link->query("select * from $db.escalationdays where level='$level'");
    $fetch = $query->fetch_assoc();
    return $fetch;
}

function get_user_department($id) {
    global $link,$db;
    $query = $link->query("SELECT user_id FROM $db.`web_project_assigne` WHERE `project_id` IN ('$id')");
    $data = [];
    while ($row = $query->fetch_assoc()) :
        $data[] = $row; 
    endwhile;

    return $data;
}
function escalate_case($link, $db, $level, $case_info, $fetch_esc, $users_subcat){

    $id = $case_info['id'];
    $name = $case_info['name'];
    $email = $case_info['email'];
    $phone = $case_info['phone'];
    $type = $case_info['type'];
    $status = $case_info['status'];
    $category = $case_info['category'];
    $subcategory = $case_info['subcategory'];
    $department = $case_info['department'];
    $ticketid = $case_info['ticketid'];
    $createddate = $case_info['createddate'];
    $resolution_time_in_hour = $case_info['resolution_time_in_hour'];

    //sms
    $data_arr = ["name"=>$name,"level"=>$level,"hour"=>$resolution_time_in_hour];
    $res_sms = sms_template($ticketid,'escalation',$data_arr);
    $message = $res_sms['msg'];
    $expiry_sms = $res_sms['expiry'];

    //email
    $data_mail = ['name' => $name, 'mobile' => $phone, 'email' => $email, 'category' => category($category), 'subcategory' =>  subcategory($subcategory),'ticketid'=>$ticketid,'createddate'=>$createddate,'status'=>ticketstatus($status),'level'=>$level,"hour"=>$resolution_time_in_hour];
    $res_email = mail_template($ticketid,'escalation', $data_mail);
    $subject = $res_email['sub'];
    $body = $res_email['msg'];
    $expiry_mail = $res_email['expiry'];

    // escalation list users
    $users = explode(",",$fetch_esc['escalation_list']);
    // echo "<br>";
    // subcategory users list
    $subcat_users = explode(",",$users_subcat);
    
    // print_r($subcat_users);
    // get users from departments
    $user_department = get_user_department($department);

   
   if($fetch_esc['escalation_to'] == 1) // if escalation to customer 
   {

        if($fetch_esc['escalation_media'] == 1) // email
        {
            send_email($email,$subject,$body,$expiry_mail,$ticketid); // to customer

        }else if($fetch_esc['escalation_media'] == 2) // sms
        {
           send_sms($ticketid,$phone,$message,$name,$expiry_sms); // to customer

        }else if($fetch_esc['escalation_media'] == 3) // both
        {
            send_email($email,$subject,$body,$expiry_mail,$ticketid); // to customer
            send_sms($ticketid,$phone,$message,$name,$expiry_sms); // to customer
        }

   }else if($fetch_esc['escalation_to'] == 2) //else if escalation to department 
   {
        if($fetch_esc['escalation_media'] == 1) // email
        {
            foreach ($user_department as $user) {
                $user_info = user_info($user['user_id']);
                send_email($user_info['AtxEmail'],$subject,$body,$expiry_mail,$ticketid); // to department
            }

        }else if($fetch_esc['escalation_media'] == 2) // sms
        {
            foreach ($user_department as $user) {
                $user_info = user_info($user['user_id']);
                send_sms($ticketid,$user_info['AtxHomePhone'],$message,$user_info['AtxDisplayName'],$expiry_sms); // to department
            }

        }else if($fetch_esc['escalation_media'] == 3) // both
        {
            foreach ($user_department as $user) {
                $user_info = user_info($user['user_id']);
                send_email($user_info['AtxEmail'],$subject,$body,$expiry_mail,$ticketid); // to department
                send_sms($ticketid,$user_info['AtxHomePhone'],$message,$user_info['AtxDisplayName'],$expiry_sms); // to department
            }
           
        }

   }else if($fetch_esc['escalation_to'] == 3) //else both customer and department 
   {
    // echo "here";
    // echo "<br>";
    // echo $fetch_esc['escalation_media'];

        if($fetch_esc['escalation_media'] == 1) // email
        {
            send_email($email,$subject,$body,$expiry_mail,$ticketid); // to customer

            foreach ($user_department as $user) {
                $user_info = user_info($user['user_id']);
                send_email($user_info['AtxEmail'],$subject,$body,$expiry_mail,$ticketid); // to department
            }

        }else if($fetch_esc['escalation_media'] == 2) // sms
        {
            send_sms($ticketid,$phone,$message,$name,$expiry_sms); // to customer

            foreach ($user_department as $user) {
                $user_info = user_info($user['user_id']);
                send_sms($ticketid,$user_info['AtxHomePhone'],$message,$user_info['AtxDisplayName'],$expiry_sms); // to department
            }

        }else if($fetch_esc['escalation_media'] == 3) // both
        {
            send_email($email,$subject,$body,$expiry_mail,$ticketid); // to customer
            send_sms($ticketid,$phone,$message,$name,$expiry_sms); // to customer

            foreach ($user_department as $user) {
                $user_info = user_info($user['user_id']);
                send_email($user_info['AtxEmail'],$subject,$body,$expiry_mail,$ticketid); // to department
                send_sms($ticketid,$user_info['AtxHomePhone'],$message,$user_info['AtxDisplayName'],$expiry_sms); // to department
            }
        }
     
      
   }

//    echo $fetch_esc['escalation_media'];


   // for escalation list users (working fine)

   if($fetch_esc['escalation_media'] == 1) // email
   {
       		
		    foreach ($users as $value) {  // send mail to users of escalation list
                
                $user_info = user_info($value);
                send_email($user_info['AtxEmail'],$subject,$body,$expiry_mail,$ticketid);
         
            }

		    foreach ($subcat_users as $subcat) {  // send mail to users of subcategory users list
                
                $user_info = user_info($subcat);
                send_email($user_info['AtxEmail'],$subject,$body,$expiry_mail,$ticketid);
         
            }

   }else if($fetch_esc['escalation_media'] == 2) // sms
   {
        foreach ($users as $value) {  // send sms to users of escalation list
                    
            $user_info = user_info($value);
            send_sms($ticketid,$user_info['AtxHomePhone'],$message,$user_info['AtxDisplayName'],$expiry_sms);
 
       }

        foreach ($subcat_users as $subcat) {  // send sms to users of subcategory users list
                    
            $user_info = user_info($subcat);
            send_sms($ticketid,$user_info['AtxHomePhone'],$message,$user_info['AtxDisplayName'],$expiry_sms);
 
       }

   }else if($fetch_esc['escalation_media'] == 3) // both
   {
        foreach ($users as $value) {  // send mail and sms to users of escalation list
                        
            $user_info = user_info($value);
            
            send_email($user_info['AtxEmail'],$subject,$body,$expiry_mail,$ticketid); 
            send_sms($ticketid,$user_info['AtxHomePhone'],$message,$user_info['AtxDisplayName'],$expiry_sms);
    
        }

        foreach ($subcat_users as $subcat) {  // send mail and sms to users of subacategory users list
                        
            $user_info = user_info($subcat);
            
            send_email($user_info['AtxEmail'],$subject,$body,$expiry_mail,$ticketid); 
            send_sms($ticketid,$user_info['AtxHomePhone'],$message,$user_info['AtxDisplayName'],$expiry_sms);
    
        }
   }

   $cond = ($level == 1) ? "SET escalate_status= 1, escalation_hours= '$resolution_time_in_hour',escalation_level='$level',escalation_date=NOW()" : "SET escalation_hours= '$resolution_time_in_hour',escalation_level='$level',escalation_date=NOW()";
    $query_update = $link->query("UPDATE $db.web_problemdefination $cond WHERE iPID ='$id'");
    if($query_update == true) 
    {
        return true;
    }

}

function send_email($to,$subject,$body,$expiry,$ticketid){
    global $link,$dbname,$from;

    if(empty($to))
    {
        return;
    }
    //create pdf
    include_once 'helpdesk/dom_pdf.php'; 
    // $path = "../CRM/pdf/$ticketid.pdf";
    $path = BasePathStorage.'/pdf/'.$ticketid.'.pdf';
    $sql_email = "insert into $dbname.web_email_information_out(v_toemail,v_fromemail,v_subject, v_body,email_type,i_expiry,ICASEID,V_rule) values ('$to', '$from', '$subject', '$body', 'OUT','$expiry','$ticketid','$path')";
    // added code interactionn history [vastvikta][13-05-2025]
    $res = $link->query($sql_email);
    $interact_id = mysqli_insert_id($link);

    $sql_find_email = "SELECT AccountNumber FROM $db.web_accounts WHERE email = '$to'";
	$result = mysqli_query($link, $sql_find_email);

	
	if ($result && mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		if(empty($customerid)){
			$customerid = $row['AccountNumber'];
		}
	}
    $intraction_type = 'email';
    $agentid = $_SESSION['userid'];
    // SQL query to insert interaction details into the `interaction` table.
    $sql = "INSERT INTO $db.interaction (caseid,intraction_type,email,mobile,name,interact_id,customer_id,remarks,filename,created_date,type,created_by
            ) VALUES ('$ticketid','$intraction_type','$to','','','$interact_id','$customerid','$subject','',NOW(),'OUT','$agentid')";
    $result_sms= mysqli_query($link,$sql) or die("Error In Query24 ".mysqli_error($link));

    if($res == true) 
    {
        return true; 
    }
}
function send_sms($ticketid,$phone,$message,$name,$expiry){
    global $link,$db;

    $todayTime = date("Y-m-d H:i:s");
    $send_from = 'Ensembler'; // Default sender ID.
    $send_to = '00266'.$phone;
    
    // Define the SMS message and status.
    $status = '0'; // Initial status for new SMS entries.0
    // SQL query to insert SMS details into the `sms_out_queue` table.
    $sql = "INSERT INTO $db.sms_out_queue (send_to,send_from,message,create_date,status,ICASEID,created_by
                ) VALUES ('$send_to','$send_from','$message','$todayTime','$status','','$name'
                )";
    $result_sms= $link->query($sql);
    if($result_sms == true) 
    {
        return true; 
    }
}
function calculateEscalationDate($caseCreationDate, $resolutionTimeInHours, $holidays) {
    // Convert case creation date to a DateTime object
    $date = new DateTime($caseCreationDate);

    // Add resolution time to the creation date
    $date->modify("+$resolutionTimeInHours hours");

    // Loop until we find a valid business day
    while (in_array($date->format('Y-m-d'), $holidays) || $date->format('N') >= 6) {
        // If the date falls on a holiday or weekend, move to the next day
        $date->modify('+1 day');
    }

    return $date->format('Y-m-d H:i:s');
}


?>