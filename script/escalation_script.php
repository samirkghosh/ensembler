<?php
include_once "../config/web_mysqlconnect.php";
include_once "../CRM/web_function.php";
$today_date = date('Y-m-d H:i:s');
$to_date = date('Y-m-d').' 23:59:59';

$holidays = ["2024-12-25","2025-01-01"]; // holiday dates add here comma seperated

$from = getsmtp_mail();
function get_subcat_data($resolution){
    global $link,$db;
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

function get_pending_cases($level){
    global $link,$db,$to_date;
    if($level == 1){
        $query = $link->query("SELECT web_accounts.fname,web_accounts.email,web_accounts.phone, web_problemdefination.iPID,web_problemdefination.vCustomerID,web_problemdefination.vCaseType,web_problemdefination.vCategory,web_problemdefination.iCaseStatus,web_problemdefination.vSubCategory,web_problemdefination.vProjectID,web_problemdefination.ticketid,web_problemdefination.d_createDate FROM $db.web_accounts INNER JOIN $db.web_problemdefination ON web_accounts.AccountNumber = web_problemdefination.vCustomerID WHERE web_problemdefination.escalate_status IN (0) AND web_problemdefination.iCaseStatus IN (1) AND web_problemdefination.escalation_level IN (0) AND web_problemdefination.d_createDate <='$to_date' AND web_problemdefination.vSubCategory NOT IN (0)");
    } else if($level == 2) {
        $query = $link->query("SELECT web_accounts.fname,web_accounts.email,web_accounts.phone, web_problemdefination.iPID,web_problemdefination.vCustomerID,web_problemdefination.vCaseType,web_problemdefination.vCategory,web_problemdefination.iCaseStatus,web_problemdefination.vSubCategory,web_problemdefination.vProjectID,web_problemdefination.ticketid,web_problemdefination.d_createDate FROM $db.web_accounts INNER JOIN $db.web_problemdefination ON web_accounts.AccountNumber = web_problemdefination.vCustomerID WHERE web_problemdefination.escalate_status IN (1) AND web_problemdefination.iCaseStatus IN (4) AND web_problemdefination.escalation_level IN (1) AND web_problemdefination.d_createDate <='$to_date' AND web_problemdefination.vSubCategory NOT IN (0)");
    }else if($level == 3) {
        $query = $link->query("SELECT web_accounts.fname,web_accounts.email,web_accounts.phone, web_problemdefination.iPID,web_problemdefination.vCustomerID,web_problemdefination.vCaseType,web_problemdefination.vCategory,web_problemdefination.iCaseStatus,web_problemdefination.vSubCategory,web_problemdefination.vProjectID,web_problemdefination.ticketid,web_problemdefination.d_createDate FROM $db.web_accounts INNER JOIN $db.web_problemdefination ON web_accounts.AccountNumber = web_problemdefination.vCustomerID WHERE web_problemdefination.escalate_status IN (1) AND web_problemdefination.iCaseStatus IN (4) AND web_problemdefination.escalation_level IN (2) AND web_problemdefination.d_createDate <='$to_date' AND web_problemdefination.vSubCategory NOT IN (0)");
    }
    $data = [];
    while($row = $query->fetch_assoc()):
        $data[] = $row;
    endwhile;

    return $data;
 
}

function get_escalation_users($level){
    global $db,$link;
	$query=$link->query("select * from $db.escalationdays where level='$level'");
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

function escalate_case($level,$case_info,$fetch_esc,$users_subcat){
    global $link,$db;

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

    // subcategory users list
    $subcat_users = explode(",",$users_subcat);

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

   $cond = ($level == 1) ? "SET iCaseStatus='4', escalate_status= 1, escalation_hours= '$resolution_time_in_hour',escalation_level='$level',escalation_date=NOW()" : "SET escalation_hours= '$resolution_time_in_hour',escalation_level='$level',escalation_date=NOW()";

    $query_update = $link->query("UPDATE $db.web_problemdefination $cond WHERE iPID ='$id'");
    if($query_update == true) 
    {
        return true;
    }

}

function send_email($to,$subject,$body,$expiry,$ticketid){
  global $link,$dbname,$from;

  if(!empty($to))
  {
    $sql_email = "insert into $dbname.web_email_information_out(v_toemail,v_fromemail,v_subject, v_body,email_type,i_expiry,ICASEID) values ('$to', '$from', '$subject', '$body', 'OUT','$expiry','$ticketid')";
  
    $res = $link->query($sql_email);
  
    if($res == true) 
    {
        return true; 
    }
  }

}

function send_sms($ticketid,$phone,$message,$name,$expiry){
    global $link,$db;
    if(!empty($phone)){   
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
        if($result_sms == true) {
            return true; 
        }       
    }
}

function calculateEscalationDate($caseCreationDate, $resolutionTimeInHours, $holidays = []) {
    // Convert the case creation date to a DateTime object
    $currentDate = new DateTime($caseCreationDate);
    
    // Calculate the target date
    $currentDate->modify("+".$resolutionTimeInHours." hours");
    
    // Loop until the target date is reached
    while ($resolutionTimeInHours > 0) {
        // Check if the current date falls on a weekend or a holiday
        $currentDayOfWeek = $currentDate->format('N'); // 1 (Monday) to 7 (Sunday)
        $currentDateFormatted = $currentDate->format('Y-m-d');// current date in ymd format
       //if $currentDayOfWeek is saturday(6),sunday(7) or any holiday from an array $holidays
        if ($currentDayOfWeek >= 6 || in_array($currentDateFormatted, $holidays)) { 
            // Increment the date by one day
            $currentDate->modify("+1 day");
        } else {
            // Decrement the remaining resolution time by one hour
            $resolutionTimeInHours--;
            $currentDate->modify("+1 hour");
        }
    }

    // Return the escalation date in ymd format
    return $currentDate->format('Y-m-d H:i:s');
}

if(isset($_REQUEST['escalation_level']) && ($_REQUEST['escalation_level'] == 1)){

    $subcat_array = get_subcat_data('resolution_time_in_hours'); // fn to subcategory and resolution time in hour 
    $case_array = get_pending_cases(1); // fn to get pending created cases for first level escalation
    $check_val = false; 
        foreach ($subcat_array as $subcat_value){ // sub category loop 	

                if($subcat_value['resolution_time_in_hours'] == '0')continue;  // if resolution time in hour is 0 then skip

                foreach ($case_array as $case_value){ // pending case loop		   

                    if($subcat_value['id'] == $case_value['vSubCategory']) // subcategory comparsion 
                    {
                        $no_of_hours = getHoursfromdates($case_value['d_createDate'], $today_date); // total no of hours b/w case creation and today date

                        if($no_of_hours >= $subcat_value['resolution_time_in_hours']){

                            // array for case information along with customer information
                            $case_info = ["id"=>$case_value['iPID'],"name"=>$case_value['fname'],"email"=>$case_value['email'],"phone"=>$case_value['phone'],"type"=>$case_value['vCaseType'],"status"=>$case_value['iCaseStatus'],"category"=>$case_value['vCategory'],"subcategory"=>$case_value['vSubCategory'],"department"=>$case_value['vProjectID'],"ticketid"=>$case_value['ticketid'],"createddate"=>$case_value['d_createDate'],"resolution_time_in_hour"=>$subcat_value['resolution_time_in_hours']];

                            $caseCreationDate = $case_value['d_createDate'];
                            $resolutionTimeInHours = $subcat_value['resolution_time_in_hours'];
                            // get escalation date after skiping weekend and holidays 
                            $escalationDate = calculateEscalationDate($caseCreationDate, $resolutionTimeInHours, $holidays);

                            if ($escalationDate <= $today_date) {
                                // Escalate the case
                                $send = escalate_case(1,$case_info,get_escalation_users(1),$subcat_value['level1_users']);
                                $check_val = ($send == true) ? true : false;
                            } else {
                                $check_val = false; 
                            }
                        }
                    }
                }
        }
   
    if($check_val== true){
        echo "Escalation Level 1 Updated Successfully" ;
    }
    else{
        echo "No One Case to Escalate For Level 1" ;	
    }
}else if(isset($_REQUEST['escalation_level']) && ($_REQUEST['escalation_level'] == 2)){

    $subcat_array = get_subcat_data('second_resolution_time'); // fn to subcategory and resolution time in hour 
    $case_array = get_pending_cases(2); // fn to get pending created cases for second level escalation
    $check_val = false; 
        foreach ($subcat_array as $subcat_value){ // sub category loop 	

                if($subcat_value['second_resolution_time'] == '0')continue;  // if resolution time in hour is 0 then skip

                foreach ($case_array as $case_value){ // pending case loop		   

                    if($subcat_value['id'] == $case_value['vSubCategory']) // subcategory comparsion
                    {
                        $no_of_hours = getHoursfromdates($case_value['d_createDate'], $today_date); // total no of hours b/w case creation and today date

                        if($no_of_hours >= $subcat_value['second_resolution_time']){

                            // array for case information along with customer information
                            $case_info = ["id"=>$case_value['iPID'],"name"=>$case_value['fname'],"email"=>$case_value['email'],"phone"=>$case_value['phone'],"type"=>$case_value['vCaseType'],"status"=>$case_value['iCaseStatus'],"category"=>$case_value['vCategory'],"subcategory"=>$case_value['vSubCategory'],"department"=>$case_value['vProjectID'],"ticketid"=>$case_value['ticketid'],"createddate"=>$case_value['d_createDate'],"resolution_time_in_hour"=>$subcat_value['second_resolution_time']];

                            $caseCreationDate = $case_value['d_createDate'];
                            $resolutionTimeInHours = $subcat_value['second_resolution_time'];
                            // get escalation date after skiping weekend and holidays
                            $escalationDate = calculateEscalationDate($caseCreationDate, $resolutionTimeInHours, $holidays);

                            if ($escalationDate <= $today_date) {
                                // Escalate the case
                                $send = escalate_case(2,$case_info,get_escalation_users(2),$subcat_value['level2_users']);
                                $check_val = ($send == true) ? true : false;
                            } else {
                                $check_val = false; 
                            }
                        }
                    }
                }
        }

    if($check_val== true){
        echo "Escalation Level 2 Updated Successfully" ;
    }
    else{
        echo "No One Case to Escalate For Level 2" ;	
    }
}else if(isset($_REQUEST['escalation_level']) && ($_REQUEST['escalation_level'] == 3)){

    $subcat_array = get_subcat_data('third_resolution_time'); // fn to subcategory and resolution time in hour 
    $case_array = get_pending_cases(3); // fn to get pending created cases for third level escalation
    $check_val = false; 
        foreach ($subcat_array as $subcat_value){ // sub category loop 	

                if($subcat_value['third_resolution_time'] == '0')continue;  // if resolution time in hour is 0 then skip

                foreach ($case_array as $case_value){ // pending case loop		   

                    if($subcat_value['id'] == $case_value['vSubCategory']) // subcategory comparsion
                    {
                        $no_of_hours = getHoursfromdates($case_value['d_createDate'], $today_date); // total no of hours b/w case creation and today date

                        if($no_of_hours >= $subcat_value['third_resolution_time']){

                            // array for case information along with customer information
                            $case_info = ["id"=>$case_value['iPID'],"name"=>$case_value['fname'],"email"=>$case_value['email'],"phone"=>$case_value['phone'],"type"=>$case_value['vCaseType'],"status"=>$case_value['iCaseStatus'],"category"=>$case_value['vCategory'],"subcategory"=>$case_value['vSubCategory'],"department"=>$case_value['vProjectID'],"ticketid"=>$case_value['ticketid'],"createddate"=>$case_value['d_createDate'],"resolution_time_in_hour"=>$subcat_value['third_resolution_time']];

                            $caseCreationDate = $case_value['d_createDate'];
                            $resolutionTimeInHours = $subcat_value['third_resolution_time'];
                            // get escalation date after skiping weekend and holidays
                            $escalationDate = calculateEscalationDate($caseCreationDate, $resolutionTimeInHours, $holidays);

                            if ($escalationDate <= $today_date) {
                                // Escalate the case
                                $send = escalate_case(3,$case_info,get_escalation_users(3),$subcat_value['level3_users']);
                                $check_val = ($send == true) ? true : false;
                            } else {
                                $check_val = false; 
                            }
                        }
                    }
                }
        }

    if($check_val== true){
        echo "Escalation Level 3 Updated Successfully" ;
    }
    else{
        echo "No One Case to Escalate For Level 3" ;	
    }    
}else{
    $check_val = false;
    echo "No Data";
}



