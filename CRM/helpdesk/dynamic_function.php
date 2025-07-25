<?php
session_start();
include "../../config/web_mysqlconnect.php"; //  Connection to database // Please do not remove this

// Load configuration
$config = require 'helb_config.php';

// fetch user details
include "../web_function.php"; // For common function access

/**
 * Dynamics 365 CTI Integration - API Handler
 * Author: Aarti Ojha
 * Date: 16-01-2024
 *
 * Description:
 * Handles incoming call events from the dialer.
 * Retrieves or creates a Dynamics 365 contact record based on the caller ID
 * and opens the corresponding contact form (pre-filled or blank) in an CRM.
 *
 * This script is invoked via AJAX from the Case interface.
 */

// Handle AJAX requests
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'Submit_Ticket':
            Dynamics_Case_Create($config);
            break;
        case 'search_customer':
            search_customer($config);
            break;
    }
}


/**
 * Fetch customer data from Dynamics 365 filtered by phone number.
 */
function search_customer($config){
    $phone = htmlspecialchars($_POST['key']);

    // Build OData filter for telephone1
    $filter = urlencode("contains(telephone1,'$phone')");
    $url = $config['resource'] . "/api/data/v9.2/contacts?\$filter=$filter";

    // Get OAuth access token
    $accessToken = getAccessToken($config);
    if (!$accessToken) {
        echo '<tr><td colspan="2">Authentication Failed!</td></tr>';
        return;
    }
    // Set headers
    $headers = [
        "Authorization: Bearer $accessToken",
        "Accept: application/json",
        "User-Agent: php-curl"
    ];

    // Perform CURL request
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => $headers]);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    // Check if result is found
    if (!empty($data['value']) && isset($data['value'][0])) {
        $rest = $data['value'][0];

        // Prepare customer data
        $customer_array = [];
        $customer_array[] = $rest['firstname'] ?? '';
        $customer_array[] = $rest['lastname'] ?? '';
        $customer_array[] = $rest['telephone1'] ?? '';
        $customer_array[] = $rest['emailaddress1'] ?? '';

        // Implode for JS function input
        $js_data = implode("||", array_map('htmlspecialchars', $customer_array));

        // Build result row
        $htmls = '<tr style="cursor: pointer;" onclick="showCustomerDetails(\'' . $js_data . '\')">
                    <td>' . htmlspecialchars($rest['firstname']) . '</td>
                    <td>' . htmlspecialchars($rest['telephone1']) . '</td>
                  </tr>';
    } else {
        $htmls = '<tr><td colspan="2">No Record!</td></tr>';
    }
    echo $htmls;
}

/**
 * Create a case in Dynamics 365. using web apis
 */
function Dynamics_Case_Create($config){
    $data = json_decode(file_get_contents("php://input"), true);

    // Get access token
    $token = getAccessToken($config);

    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'Access token failed']);
        exit;
    }

    $contactId = createOrGetCustomer($token, $config);

    if (!$contactId) {
        http_response_code(400);
        echo json_encode(['error' => 'Customer creation failed']);
        exit;
    }

    $result = createCase($token, $config, $contactId);
    $insert_database_flag = '1';

    // if($result && $insert_database_flag = '1'){
    	// Insert or update customer details in the database
    	Ajax_Submit_Ticket($result['case_id']); 
    // }
    
    if (isset($result['success']) && $result['success']) {
        echo json_encode([
            'success' => true,
            'case_id' => $result['case_id'],
            'title' => $result['title']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => $result['error'] ?? 'Unknown error',
            'response' => $result['response'] ?? null
        ]);
    }
    exit;
}

/* get token using dynamic login details*/ 
/**
 * Obtain OAuth access token.
 */
function getAccessToken($config){
    $tenantId = $config['tenant_id'];
    $clientId = $config['client_id'];
    $clientSecret = $config['client_secret'];
    $orgUrl = $config['orgUrl'];

    $tokenUrl = "https://login.microsoftonline.com/$tenantId/oauth2/v2.0/token";

    $data = http_build_query([
        'grant_type' => 'client_credentials',
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'scope' => "$orgUrl/.default"
    ]);

    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);
    // ... existing code
    $tokenData = json_decode($response, true);
    if (!isset($tokenData['access_token'])) {
        error_log("Token retrieval failed: " . $response);
        return null;
    }
    return $tokenData['access_token'];
}

/**
 * Create or retrieve a customer in Dynamics 365.
 */
function createOrGetCustomer($accessToken, $config) {
    $phone = $_POST['phone'];

    $phone = preg_replace('/^91/', '', $phone);

    $filter = urlencode("contains(telephone1,'$phone')");
    $url = $config['resource'] . "/api/data/v9.2/contacts?\$filter=$filter";

    $headers = [
        "Authorization: Bearer $accessToken",
        "Accept: application/json",
        "User-Agent: php-curl"
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => $headers]);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);


    if (!empty($data['value']) && isset($data['value'][0]['contactid'])) {
        // Found existing contact
        return $data['value'][0]['contactid'];
    }

    // Create new contact if not found
    $url = $config['resource'] . "/api/data/v9.2/contacts";
    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json",
        "OData-MaxVersion: 4.0",
        "OData-Version: 4.0",
        "Prefer: return=representation"
    ];

    $payload = json_encode([
        "firstname" => $_POST['first_name'],
        "lastname" => $_POST['last_name'],
        "emailaddress1" => $_POST['email'],
        "telephone1" => $_POST['phone'],
        "mobilephone" => $_POST['phone'],
        "statecode" => 0,        // Active
        "statuscode" => 1,        // Active
        "ownerid@odata.bind" => "/systemusers(fb973ba0-1323-f011-8c4d-6045bdceacc4)"  // Replace this with actual user ID - kewal singh
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload
    ]);
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    if ($info['http_code'] == 201) {
        $responseData = json_decode($response, true);
        return $responseData['contactid'] ?? null;
    }

    return null;
}

/**
 * Create a case in Dynamics 365.
 */
function createCase($accessToken, $config, $customerId) {
    $url = $config['resource'] . "/api/data/v9.2/incidents";
    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json",
        "OData-MaxVersion: 4.0",
        "OData-Version: 4.0",
        "Prefer: return=representation"
    ];

    $payload = [
        "title" => $_POST['case_titile'],
        "description" => $_POST['v_remark_type'],
        "customerid_contact@odata.bind" => "/contacts($customerId)"
        // "ownerid@odata.bind" => "/systemusers({$_POST['ownerid']})"
    ];

    if (!empty($_POST['subjectid'])) {
        // $payload["subjectid@odata.bind"] = "/subjects({$_POST['subjectid']})";
    }

    if (!empty($_POST['contactid'])) {
        $payload["primarycontactid@odata.bind"] = "/contacts({$_POST['contactid']})";
    }

    if (!empty($_POST['casetypecode'])) {
        $payload["casetypecode"] = (int) $_POST['casetypecode'];
    }

    if($_POST['source'] == '1'){ //phone
        $source = '1';
    }else if($_POST['source'] == '6'){ //email
        $source = '2';
    }else if($_POST['source'] == '14'){ //facebook
        $source = '4';
    }else if($_POST['source'] == '3'){ //twitter
        $source = '5';
    }else{
        $source = '6'; //lot
    }

    if (!empty($_POST['source'])) {
        $payload["caseorigincode"] = (int) $source;
    }
    if (!empty($_POST['productsid'])) {
        $payload["productid@odata.bind"] = "/products({$_POST['productsid']})";
    }
    if (!empty($_POST['priority'])) {
        // $payload["prioritycode"] = (int) $_POST['priority'];
    }

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
      CURLOPT_POSTFIELDS => json_encode($payload),
      CURLOPT_HTTPHEADER => $headers
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        return ['error' => "cURL Error: $err"];
    }

    $data = json_decode($response, true);

    if ($httpCode >= 200 && $httpCode < 300 && isset($data['ticketnumber'])) {
        return [
            'success' => true,
            'case_id' => $data['ticketnumber'],
            'title' => $data['title']
        ];
    } else {
        return [
            'error' => "Failed to create case. Status: $httpCode",
            'response' => $response
        ];
    }
}

/*** FOR Insert/Update Customer details and Create Ticket Code Start****/ 
/**
 * Create ticket using multiple channels like SMS, EMAIL, TWITTER, FACEBOOK, WHATSAPP, IVR, MANUAL
 *
 * Step-1: Validation
 * Step-2: Create user and create ticket flow
 * Step-3: Send Ticket Mails
 */
function Ajax_Submit_Ticket($case_id){
	global $link,$db;
    $vuserid        =  $_SESSION['userid'];
    /** Customer details **/
    // Collect and sanitize input
    $todaydate = date("Y-m-d H:i:s");
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_no = $_POST['phone'];
    $customer_id = $_POST['customerid'];

    $smshandle = $_POST['smshandle'];
    $whatsapphandle = $_POST['whatsapp_number'];
    $messengerhandle = $_POST['messengerhandle'];
    $instagramhandle = $_POST['instagramhandle']; 
    $address_1 = $_POST['address_1'];
    $fbhandle = $_POST['fbhandle'];
    $twitterhandle = $_POST['twitterhandle'];
    $gender = $_POST['gender'];
    $priority_user = $_POST['priority_user'];
    $currentdate = date("Y-m-d H:i:s");
    $fname = trim($first_name . " " . $last_name);
    
    // Check if customer already exists
    $get_chat_query = "select AccountNumber,email, phone from $db.web_accounts where (phone = '$phone_no' and phone!='') || (email = '$email' and email!='') ";; 
    $getchat_res111 = mysqli_query($link, $get_chat_query);

    if (mysqli_num_rows($getchat_res111) > 0) {
        $res1 = mysqli_fetch_assoc($getchat_res111);
    }

    $rows = $res1;
    $customerid = $res1['AccountNumber'] ?? 0;

    /** if the customer already exists then update otherwise insert a new record **/
    if (count($rows) <= 0) {

        // Insert new customer
        $insert_customer_query = "insert into $db.web_accounts(fname, createddate, address, phone, mobile,fbhandle, twitterhandle, email,smshandle,whatsapphandle,messengerhandle,instagramhandle) values('$fname','$todaydate','"  . addslashes($address_1) . "','$phone_no','$phone_no','$fbhandle','$twitterhandle','$email','$smshandle','$whatsapphandle','$messengerhandle','$instagramhandle')  ";
        mysqli_query($link, $insert_customer_query) or die("Error In Query2 " . mysqli_error($link));
        $customerid = mysqli_insert_id($link);
    }else{
        /*** update customer information if exist  ***/
        $update_customer = "update $db.web_accounts set phone='$phone_no' ,fname='$fname' , address='" . addslashes($address_1) . "',mobile='$phone_no',updatedate=NOW(),twitterhandle='$twitterhandle',fbhandle='$fbhandle', email='$email',gender='$gender',priority_user='$priority_user',smshandle='$smshandle',whatsapphandle='$whatsapphandle',messengerhandle='$messengerhandle',instagramhandle='$instagramhandle' where AccountNumber='$customerid'";
        mysqli_query($link, $update_customer) or die("Error In Query2 " . mysqli_error($link));
    }

    /*** Customer create and update after satrt ticket create code ***/ 
    // Proceed to ticket creation
    if ($customerid > 0) {
        $ticketid = getticket(); // this function for get ticket id

        /** Case details **/ 

        $type = $_POST['type'];
        $status_type_ = '1';
        $priority = $_POST['priority'];
        $call_type = $_POST['call_type'];
        $v_remark_type = $_POST['v_remark_type'];
        $source = $_POST['source'];
        $department = $_POST['department'];  //store in vAssignDepartname
        

        $enquire = $_POST['enquire']; // store in v_Enq_Complaint
        $product = $_POST['product']; // store in vCategory
        $request = $_POST['request']; //store in vSubCategory
        $case_titile = $_POST['case_titile']; // store in organization
        $last_case_id = $case_id; // store in vNinNumber

        // Insert code for ticket details
        $sql = "insert into $db.web_problemdefination(vCustomerID, vCaseType, iCaseStatus, vRemarks, ticketid, d_createDate, d_updateTime,i_source, i_CreatedBY, call_type, priority,vAssignDepartname,v_Enq_Complaint,vCategory,vSubCategory,organization,vNinNumber) values('$customerid','$type','$status_type_', '" . $v_remark_type . "', '$ticketid', '$currentdate', '$currentdate', '$source','$vuserid', '$call_type', '$priority','$department','$enquire','$product','$request','$case_titile','$last_case_id')";

        mysqli_query($link, $sql) or die("Error In Query2 " . mysqli_error($link));
        $ticket = mysqli_insert_id($link);

        // Update email, Twitter, voicemail, Facebook, SMS with case ID
        if (isset($_POST['chatid']) && isset($_POST['mr'])) {
           $chat_id = $_POST['chatid'];
           $sql_update = "UPDATE overall_bot_chat_session SET caseid='$ticketid' WHERE chat_session='$chat_id'";
           mysqli_query($link, $sql_update) or die("Error In overall_bot_chat_session" . mysqli_error($link));

           $sql_update_chat = "UPDATE in_out_data SET caseid='$ticketid' WHERE chat_session_id='$chat_id'";
           mysqli_query($link, $sql_update_chat) or die("Error In in_out_data" . mysqli_error($link));

          //interaction data store in interaction table[Aarti][07-01-2025]
          // for any interactio coming with any channel when insert data in interactio table
           // [Aarti][07-01-2025]
            $intraction_type = 'webchat';
            interaction_history_insert($ticketid,$chat_id,$intraction_type,$customerid);
        
        }
        $email_type = '';
        // update email with case ID
        if ($_POST['emailid'] && isset($_POST['mr']) && $_POST['mr'] == '6') {
           // status = '2'   means case created
           $chat_id = $_POST['emailid'];
           //  email_test='$orginal_subject'
           $sql_update = "UPDATE $db.web_email_information SET ICASEID='$ticketid', i_Update_status='1' ,i_flag=1 , i_reminder=0 ,case_open_time='$todaydate' WHERE  EMAIL_ID =$chat_id  ";
           mysqli_query($link, $sql_update) or die("Error In web_email_information " . mysqli_error($link));
           $email_type = 'By email';

           // for any interactio coming with any channel when insert data in interactio table
           // [Aarti][07-01-2025]
            $intraction_type = 'email';
            interaction_history_insert($ticketid,$chat_id,$intraction_type,$customerid);

        }
        // update Twitter with case ID
        if (isset($_POST['twitterid']) && $_POST['mr'] == '3') {
           // status = '2'   means case created
           $chat_id = $_POST['twitterid'];
           //  email_test='$orginal_subject'
           $update_tweet = "UPDATE $db.tbl_tweet SET ICASEID='$ticketid', i_Update_status='1', i_reminder=0 ,case_open_time='$todaydate' WHERE  i_ID =$chat_id  ";
           mysqli_query($link, $update_tweet) or die("Error In tbl_tweet " . mysqli_error($link));

           // for any interactio coming with any channel when insert data in interactio table
           // [Aarti][07-01-2025]
            $intraction_type = 'twitter';
            interaction_history_insert($ticketid,$chat_id,$intraction_type,$customerid);
        }
        //update voicemail with case ID
        if (isset($_POST['voicemailid']) && isset($_POST['mr']) && $_POST['mr'] == '10') {
           // status = '2'   means case created
           $voice_id = $_POST['voicemailid'];
           //  email_test='$orginal_subject'
           $update_voicemail = "UPDATE $db_asterisk.tbl_cc_voicemails SET case_id='$ticketid' WHERE id =$voice_id  ";
           mysqli_query($link, $update_voicemail) or die("Error In tbl_cc_voicemails " . mysqli_error($link));

            // for any interactio coming with any channel when insert data in interactio table
           // [Aarti][07-01-2025]
            $intraction_type = 'voicecall';
            interaction_history_insert($ticketid,$voice_id,$intraction_type,$customerid);
        }                   
        //update facebook with case ID
        $facebook_type  = '';
        if (isset($_POST['facebookid']) && isset($_POST['mr']) && $_POST['mr'] == '4') {
           // status = '2'   means case created
           $facebook_id = $_POST['facebookid'];
           //  email_test='$orginal_subject'
           $update_facebook= "UPDATE $db.tbl_facebook SET ICASEID='$ticketid' WHERE id =$facebook_id  ";
           mysqli_query($link, $update_facebook) or die("Error In tbl_facebook " . mysqli_error($link));
           $facebook_type = 'By facebook';
        }
        //update sms with case ID
        if (isset($_POST['smsid']) && isset($_POST['mr']) && $_POST['mr'] == '13') {
           // status = '2'   means case created
           $smsid = $_POST['smsid'];
           //  email_test='$orginal_subject'
           $update_sms= "UPDATE $db.tbl_smsmessagesin SET ICASEID='$ticketid', flag=1 WHERE i_id =$smsid  ";
           mysqli_query($link, $update_sms) or die("Error In tbl_smsmessagesin " . mysqli_error($link));
           // for any interactio coming with any channel when insert data in interactio table
           // [Aarti][07-01-2025]
            $intraction_type = 'SMS';
            interaction_history_insert($ticketid,$smsid,$intraction_type,$customerid);
        }
         // update Twitter with case ID
        if (isset($_REQUEST['whatsappid']) && isset($_POST['mr']) && $_POST['mr'] == '8') {
           // status = '2'   means case created
           $whatsappid = $_POST['whatsappid'];
           //  email_test='$orginal_subject'
           $update_tweet = "UPDATE $db.whatsapp_in_queue SET ICASEID='$ticketid' WHERE  id =$whatsappid  ";
           mysqli_query($link, $update_tweet) or die("Error In whatsapp_in_queue " . mysqli_error($link));

           // for any interactio coming with any channel when insert data in interactio table
           // [Aarti][07-01-2025]
            $intraction_type = 'Whatsapp';
            interaction_history_insert($ticketid,$whatsappid,$intraction_type,$customerid);
        }

         //update sms with case ID
         if (isset($_POST['messengerid']) && isset($_POST['mr']) && $_POST['mr'] == '14') {
            $messengerid = $_POST['messengerid'];       
            $update_messenger= "UPDATE $db.messenger_in_queue SET ICASEID='$ticketid', flag=1 WHERE id =$messengerid";
            mysqli_query($link, $update_messenger) or die("Error In messenger_in_queue " . mysqli_error($link));

            // for update customer id in sendfrom field messenger table
           $update_tweet = "UPDATE $db.messenger_in_queue SET customer_id='$customerid' WHERE  id = '$messengerid'";
           mysqli_query($link, $update_tweet) or die("Error In messenger_in_queue " . mysqli_error($link));

           // for any interactio coming with any channel when insert data in interactio table
           // [Aarti][07-01-2025]
            $intraction_type = 'messenger';
            interaction_history_insert($ticketid,$messengerid,$intraction_type,$customerid);
         }
          //update sms with case ID handling instagram [Aarti][19-11-2024]
         if (isset($_POST['instagramid']) && isset($_POST['mr']) && $_POST['mr'] == '15') {
            $instagramid = $_POST['instagramid'];       
            $update_messenger= "UPDATE $db.instagram_in_queue SET ICASEID='$ticketid', flag=1 WHERE id =$instagramid";
            mysqli_query($link, $update_messenger) or die("Error In instagram_in_queue " . mysqli_error($link));

            // for update customer id in sendfrom field messenger table
           $update_tweet = "UPDATE $db.instagram_in_queue SET customer_id='$customerid' WHERE  send_from =$instagramhandle  ";
           mysqli_query($link, $update_tweet) or die("Error In instagram_in_queue " . mysqli_error($link));

            // for any interactio coming with any channel when insert data in interactio table
           // [Aarti][07-01-2025]
            $intraction_type = 'instagram';
            interaction_history_insert($ticketid,$instagramid,$intraction_type,$customerid);
         }

        // Function For Sending mail customer to inform case Status
        TicketEmail_Send($type,$email,$ticketid,$fname,$phone_no);
    }
}

/**
 * TicketEmail_Send Function
 * This function sends emails and SMS notifications related to ticket status changes.
 */
 function TicketEmail_Send($type,$email,$ticketid,$fname,$phone_no){
    global $from_email,$db,$link,$SiteURL;
   /** For check email id and send mail **/ 
   if (!empty($email)) {
    $case_type = ($type === 'complaint') ? 'com_new_case' : (($type === 'Inquiry') ? 'inquiry_new_case' : 'new_case');

     // Generate email template
     $res = mail_template($ticketid, $case_type, $data = []);
     $subject = $res['sub'];
     $message = $res['msg'];
     $expiry = $res['expiry'];

     /*Aarti-23-11-23
     insert data in web_email_information_out table and 
     replce the insert code and add new function for insert code*/
     $data_email=array();
     $data_email['Mail'] = $email;
     $data_email['from']= $from_email ;
     $data_email['V_Subject']=$subject;
     $data_email['V_Content']=$message;
     $data_email['ICASEID']=$ticketid;
     $data_email['i_expiry']=$expiry;
     $data_email['V_rule']=$path;
     $data_email['view']='New Case Manual';
     // Insert data into web_email_information_out table
     insert_emailinformationout($data_email);
   }
   // Check if phone number is not empty
    if(!empty($phone_no)){
         $sms_type = 'new_case';
         $customer_name=ucwords($fname);
         $data_arr = ["name" => $customer_name];
         $res_sms = sms_template($ticketid,$sms_type,$data_arr);
         $message = $res_sms['msg'];
         $expiry = $res_sms['expiry'];

          /*insert data in tbl_smsmessages table*/
           $data_sms=array();
           $data_sms['v_mobileNo'] = $phone_no;
           $data_sms['v_smsString']= $message;
           $data_sms['V_Type']='Sms';
           $data_sms['V_AccountName']=$fname;
           $data_sms['V_CreatedBY']='';
           $data_sms['i_status']='1';
           $data_sms['i_expiry']=$expiry;  
           $data_sms['ICASEID']=$ticketid;
           // Insert data into tbl_smsmessages table              
           insert_smsmessages($data_sms);
           /*end - web_email_information_out*/
    }
 } 
?>