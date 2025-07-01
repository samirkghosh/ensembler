<?php
/*
* Date: 16-11-2024
* Auth: Aarti ojha
* Purpose: Fetch unseen emails from an IMAP server using OAuth2 (or password for testing).
*          Download attachments, process email body, store information in a database, and mark them as SEEN.
* Note: For production, replace the password with an OAuth2 token for security.
*/
// phpinfo(); die;
include_once("/var/www/html/ensembler/config/web_mysqlconnect.php"); // Include database connection file
require_once '/var/www/html/ensembler/PHPMailer-5.2.28/PHPMailerAutoload.php';

$log_prefix = "[IMAP Script]";

// Master database configuration
$masterdb = 'CampaignTracker';
global $configdbhost, $configdbuser, $configdbpass;

// Establish connection to the master database
$link = mysqli_connect($configdbhost, $configdbuser, $configdbpass);
if (!$link) {
    die('Failed to connect to CampaignTracker database.');
}

// Query to fetch the database names of all tenants
$query = "SELECT related_database_name,company_id FROM $masterdb.companies";
$stmts = mysqli_prepare($link, $query);
mysqli_stmt_execute($stmts);
$results = mysqli_stmt_get_result($stmts);

if (mysqli_num_rows($results) > 0) {
    while ($company = $results->fetch_assoc()) {
        $childdb = $company['related_database_name'];
        $companyId = $company['company_id'];
        echo "############### Company Database Name: " . $childdb . "<br/>";
        
        // Process outgoing email for the tenant database
        Fetching_Imap_Mails($childdb, $link,$companyId);
    }
} else {
    echo "No company databases found.";echo"<br/><br/>";
    die();
}


/**
 * Handles Incoming email for a tenant database.
 * 
 * @param string $childdb Name of the tenant's database.
 * @param object $link Master database connection object.
 */

function Fetching_Imap_Mails($childdb, $link,$companyId){
    global $link, $db,$passwordIMP,$usernameIMP;

    /************************************************************************************
    * Get the IMAP credential from the table for accessing the mail server
    ***************************************************************************************/
    $sql_connect_result="SELECT i_port,v_username,v_password FROM $childdb.tbl_imap_connection where status='1'";
    $result_connect_result=mysqli_query($link,$sql_connect_result);
    $row_result_connect=mysqli_fetch_array($result_connect_result);

    // IMAP server configuration
    $i_port =$row_result_connect['i_port'];
    $hostname = '{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
    $username =$row_result_connect['v_username'];
    $password =$row_result_connect['v_password'];

    print_r($row_result_connect);

    // Open IMAP connection
    $inbox = imap_open($hostname, $username, $password);

    if (!$inbox) {
        $error = 'IMAP connection failed: ' . imap_last_error();
        echo $error;echo "<br><br>";
        // Send the error email[Aarti][24-09-2024]
        // sendErrorEmail($error);
    } else {
        echo 'Connected to IMAP successfully!';echo "<br><br>";
    }

    // Fetch all unseen emails
    /* grab emails */
     // \Log::info("$log_prefix Connecting to IMAP server...");
    $emails = imap_search($inbox,'ALL');
    // $emails = imap_search($inbox,'UNSEEN');
    echo "Email Count - > ".count($emails)."<br>"; echo "<br><br>";
    // \Log::info("$log_prefix Fetched " . count($emails) . " emails.");

    if ($emails) {
        //Sort emails in descending order (most recent first)
        rsort($emails);

        // Limit to the first 10 emails
        $emails = array_slice($emails, 0, 10);
        foreach ($emails as $email_number) {
            $overview = imap_fetch_overview($inbox, $email_number, 0); // Get email overview
            $message = imap_fetchbody($inbox, $email_number, 2); // Get email body (part 2)

            echo "<br/><br/>From: " . $overview[0]->from;
            echo "<br/>Subject: " . $overview[0]->subject;
            // echo "<br/>Message: " . $message;

            $iUID = imap_uid($inbox, $email_number); // Get unique ID of the email
            print "Email no: $email_number, UID=$iUID\n";

            // Check if the email is already in the database
            $sql_qry = "SELECT i_UID FROM $childdb.web_email_information WHERE i_UID = '$iUID'";
            $res = mysqli_query($link, $sql_qry);
            $numrow = mysqli_num_rows($res);

            if ($numrow > 0) {
                echo "*** Already downloaded: UID=$iUID\n";
                // \Log::info("$log_prefix Already downloaded.seqno: UID=$iUID\n");
                continue; // Skip this email if it's already processed
            } else {
                echo"<br/><br/>";echo "*** New Message: UID=$iUID\n";

                $header = imap_headerinfo($inbox,$email_number,0); 

                // Fetch email content and attachments
                $dataall = getMessageContent($inbox, $email_number, $oMsg,$companyId);

                $message = $dataall['message'];
                $multifiles = $dataall['multifiles'];
                echo"<br/>";
                print_r($dataall);
                echo"<br/>";
                $from = $header->from[0]->mailbox."@".$header->from[0]->host;
                // echo"<pre>";print_r($header);

                $to = $header->to[0]->mailbox."@".$header->to[0]->host;
                $date = date('Y-m-d H:i:s', strtotime($overview[0]->date));
                $subject = addslashes($overview[0]->subject);
                $body = addslashes($message);

                 echo "<br/>To: " . $to;
                 echo "<br/>From: " . $from;
                 $queue_type = 'complain';
                if(!empty($to)){
                    // Insert email information into the database as spam if already classified as spam [vastvikta][09-06-2025]
                    $result = get_spam_email($from,$childdb);

                    if ($result > 0) {
                        // If spam (classification = 3)
                        $sql_insert = "INSERT INTO {$childdb}.web_email_information 
                        (v_toemail, v_fromemail, d_email_date, v_subject, v_body, i_UID, email_type, queue_type, V_rule, ICASEID, classification) 
                        VALUES ('$to', '$from', '$date', '$subject', '$body', '$iUID', 'IN', '$queue_type', '$multifiles', '', '3')";
                
                    } else {
                        // If not spam
                        $sql_insert = "INSERT INTO {$childdb}.web_email_information 
                        (v_toemail, v_fromemail, d_email_date, v_subject, v_body, i_UID, email_type, queue_type, V_rule, ICASEID) 
                        VALUES ('$to', '$from', '$date', '$subject', '$body', '$iUID', 'IN', '$queue_type', '$multifiles', '')";
                    }
                    echo $sql_insert;
                    if (!mysqli_query($link, $sql_insert)) {
                        echo "Unable to insert:" .$sql_insert. "Error:";
                        print("\n");
                        // \Log::error("$log_prefix Failed to insert email data for UID $iUID: " . mysqli_error($link));
                        $targetFolder = 'Old Mail';
                        $result = imap_mail_move($inbox, $email_number, $targetFolder);

                        if ($result) {
                            echo "Email #{$emailNumber} moved to the 'Old' folder successfully.<br>";
                        } else {
                            echo "Failed to move email #{$emailNumber} to the 'Old' folder: " . imap_last_error() . "<br>";
                        }
                        continue;
                    }else{
                        // added code for  email insertion incoming [vastvikta][16-04-2025]
                        $interact_id = mysqli_insert_id($link);
                    
                        $isexist = get_user_list($from,$childdb);
                        if (!empty($isexist)) {
                            $customerid = $isexist['AccountNumber']; // Get customer_id from the result
                            $sql_new = "INSERT INTO $childdb.interaction (
                                        caseid, intraction_type, email, mobile, name, interact_id, customer_id, remarks, filename, created_date, type
                                    ) VALUES (
                                        '', 'email', '', '$from', '', '$interact_id', '$customerid', '$subject', '', NOW(), 'IN'
                                    )";
                            $result_mess = mysqli_query($link, $sql_new) or die("Error In Query of interaction insertion " . mysqli_error($link));
                        }
                        // Mark the email as seen
                        $status = imap_setflag_full($inbox, $email_number, "\\Seen");
                        if ($status) {
                            echo "Set Seen flag for email number: $email_number\n";
                        } else {
                            echo "Failed to set Seen flag for email number: $email_number\n";
                        }
                    }
                }
            }
        }
        imap_close($inbox);
    } else {
        echo "No new emails found.\n";
    }
}
// Function to find if email is spam or not; if spam then return 1 [vastvikta][09-06-2025]
function get_spam_email($from,$childdb) {
    global $db, $link;

    // Escape the input to prevent SQL injection
    $from_escaped = mysqli_real_escape_string($link, $from);

    $sql = "SELECT * FROM {$childdb}.web_email_information 
            WHERE v_fromemail = '$from' AND classification = '3'";

   
    $result = mysqli_query($link, $sql);

    if ($result) {
        // Debug: Show number of rows found
        $count = mysqli_num_rows($result);
        echo "count of rows".$count;
        if (mysqli_num_rows($result) > 0) {
            return 1;
        } else {
            return 0;
        }
    } else {
        // Debug: Show SQL error
        return 0;
    }
}




function get_user_list($send_from,$childdb){
    global $link;
    $send_from = mysqli_real_escape_string($link, $send_from);
   
    $sql = "SELECT * FROM $childdb.web_accounts 
    WHERE email = '$send_from' LIMIT 1";

    $result = mysqli_query($link, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
    return mysqli_fetch_assoc($result); // Return the first matching row
    } else {
    return null; // No match found
    }
}
// Functio
/**
 * Validate the email address format
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Decode email content based on encoding type
 * @param string $message
 * @param int $coding
 * @return string
 */
function getdecodevalue($message, $coding) {
    switch ($coding) {
        case 0: return quoted_printable_decode($message);
        case 1: return imap_8bit($message);
        case 2: return imap_binary($message);
        case 3: return base64_decode($message);
        case 4: return imap_qprint($message);
        default: return $message;
    }
}

/**
 * Fetch the email content and handle attachments
 * @param resource $mail - IMAP connection
 * @param int $id - Email ID
 * @param string $orgMsg - Reference for the original message
 * @return string
 */
function getMessageContent($mail, $id, &$orgMsg,$companyId) {
    $struct = imap_fetchstructure($mail, $id);
    $parts = $struct->parts;
    $content = "";

    $multifiles="";

    // If it's a simple message (no multiple parts)
    if (!$parts) {
        $content = imap_body($mail, $id);
    } else {
        // Handle multipart messages (attachments, etc.)
        foreach ($parts as $i => $part) {
            print_r($part->disposition);
            if (strtoupper($part->disposition) === "ATTACHMENT") {
                // Handle attachments here
                $filename = $part->parameters[0]->value;
                $filedata = imap_fetchbody($mail, $id, $i+1);
                echo "Attachment found: $filename\n";

                /* Store the files in imap folder */
                // $filename="imap/".$filename;
                if ( strstr($filename,"UTF")){
                    
                    
                    $company_path =  "unistorage/" . $companyId;
                    $filePath = tempnam("/var/www/html/".$company_path."/imap","attach_");
                    $filePath = $filePath.".$ext";
                    $filename = strstr($filePath,"imap");

                }else{
                    $company_path =  "unistorage/" . $companyId;
                    
                    $filename = "$filename"; echo 'type > '.$part->type.'<br>';

                    $filePath= "/var/www/html/".$company_path."/imap/".$filename;

                    print_r($filePath); 

                    if ( file_exists($filePath)){

                        $filePath = tempnam("/var/www/html/".$company_path."/imap","attach_");
                        $filePath = $filePath.".$ext";
                        $filename = strstr($filePath,"imap");

                    }
                }
                $multifiles = ($multifiles=="") ? $filename : $multifiles.",".$filename;
                $content = imap_body($mail, $id);
            } elseif (strtoupper($part->subtype) === "PLAIN") {
                // Handle plain text body
                $msg = imap_fetchbody($mail, $id, $i+1);
                if ($part->encoding == 3) $msg = base64_decode($msg);
                $content .= $msg;
            }
        }
    }

    // Decode the content
    $orgMsg = quoted_printable_decode($content);
    $data['multifiles'] = $multifiles;
    $data['message'] = $content;
    return $data;
}

// for facing error send mail[Aarti][25-09-2024]
function sendErrorEmail($errorMessage) {
    try {
        $recipientEmails = ['Aarti.Ojha@ensembler.com']; // Add more emails as needed
        $mail = new PHPMailer(true);
        define ("PORTNUM", '587');
        define ("EMAIL_USER", 'rajdubey.alliance@gmail.com');
        define ("EMAIL_PWD", 'syepvwaknagahctq');
        define ("EMAIL_SERVER", 'smtp.gmail.com');  
        define ("EMAIL_TLS", '1');
        $fromAddr = 'rajdubey.alliance@gmail.com';

        $mail = new PHPMailer;
        $mail->IsSMTP();
        if ( EMAIL_TLS == "1"){
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
        $mail->Host = EMAIL_SERVER;
        $mail->Port = PORTNUM;
        $mail->Username = EMAIL_USER;
        $mail->Password = EMAIL_PWD;

        // $mail->From = $fromAddr; 
        $mail->FromName = $fromAddr;

        // Loop through the recipient emails array and add them
        foreach ($recipientEmails as $toAddr) {
            $mail->addAddress($toAddr); // Add each email address
        }  

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = 'Alliane Live IMAP Script Error Notification';
        $mail->Body    = '<b>Error:</b> ' . $errorMessage;
        $send=$mail->Send();

        if($send==1){ 
          $msg = "successfully mail send.";
        }else{
          $msg = "something went wrong.";
        }
    } catch (Exception $e) {
        echo "Error email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
