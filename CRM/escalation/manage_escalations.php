<?php
/***
 * Author : farhan akhtar
 * LastModified on :: 08-05-2025 
 * Description: This file handles escalation details in the database.
*/

require "escalation_functions.php";

if(isset($_REQUEST['escalation_level']) && ($_REQUEST['escalation_level'] == 1)){

    $subcat_array = get_subcat_data($link,$db,'resolution_time_in_hours'); // fn to subcategory and resolution time in hour 
    $case_array = get_pending_cases($link,$db,$to_date,1); // fn to get pending created cases for first level escalation
 
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
                                $send = escalate_case($link,$db,1,$case_info,get_escalation_users(1),$subcat_value['level1_users']);

                                // echo $send;
                                $check_val = ($send == 1) ? true : false;
                            } else {
                                $check_val = false; 
                            }
                            if($send== 1){
                                echo "Escalation Level 1 Updated Successfully--".$case_value['ticketid'];
                                echo"<br/>";
                            }
                        }
                    }
                }

        }

    switch ($check_val) {
        case true:
            echo "Escalation Level 1 Updated Successfully";
            break;
        default:
            echo "No One Case to Escalate For Level 1";
            break;
    }

    exit();

}else if(isset($_REQUEST['escalation_level']) && ($_REQUEST['escalation_level'] == 2)){

    $subcat_array = get_subcat_data($link,$db,'second_resolution_time'); // fn to subcategory and resolution time in hour 
    $case_array = get_pending_cases($link,$db,$to_date,2); // fn to get pending created cases for second level escalation
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
                                $send = escalate_case($link,$db, 2,$case_info,get_escalation_users(2),$subcat_value['level2_users']);
                                $check_val = ($send == 1) ? true : false;
                            } else {
                                $check_val = false; 
                            }
                            if($send== 1){
                                echo "Escalation Level 2 Updated Successfully--".$case_value['ticketid'];
                                echo"<br/>";
                            }
                        }
                    }
                }
        }
    switch ($check_val) {
        case true:
            echo "Escalation Level 2 Updated Successfully";
            break;
        default:
            echo "No One Case to Escalate For Level 2";
            break;
    }

}else if(isset($_REQUEST['escalation_level']) && ($_REQUEST['escalation_level'] == 3)){

    $subcat_array = get_subcat_data($link,$db,'third_resolution_time'); // fn to subcategory and resolution time in hour 
    $case_array = get_pending_cases($link,$db,$to_date,3); // fn to get pending created cases for third level escalation
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
                                $send = escalate_case($link, $db,3,$case_info,get_escalation_users(3),$subcat_value['level3_users']);
                                $check_val = ($send == 1) ? true : false;
                            } else {
                                $check_val = false; 
                            }
                            if($send== 1){
                               echo "Escalation Level 3 Updated Successfully--".$case_value['ticketid'];
                                echo"<br/>";
                            }
                        }
                    }
                }
        }

    switch ($check_val) {
        case true:
            echo "Escalation Level 3 Updated Successfully";
            break;
        default:
            echo "No One Case to Escalate For Level 3";
            break;
    }    
}else{
    $check_val = false;
    echo "No Data";
}

