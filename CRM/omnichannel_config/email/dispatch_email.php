<?php
/**
 * EMAIL
 * Author: Aarti Ojha
 * Date: 30-12-2024
 * Description: Handles outgoing email responses, stores data in the database, and sends emails via cURL for each tenant in a multitenant setup.
 * 
 * Note: Please do not modify this file without permission.
 */

date_default_timezone_set('Asia/Kolkata');
require_once '/var/www/html/ensembler/PHPMailer-5.2.28/PHPMailerAutoload.php';
include_once("/var/www/html/ensembler/config/web_mysqlconnect.php"); //Database connection configuration

// Master database configuration
$masterdb = 'CampaignTracker';
global $configdbhost, $configdbuser, $configdbpass;

// Establish connection to the master database
$link = mysqli_connect($configdbhost, $configdbuser, $configdbpass);
if (!$link) {
    die('Failed to connect to CampaignTracker database.');
}

// Query to fetch the database names of all tenants
$query = "SELECT related_database_name FROM $masterdb.companies";
$stmts = mysqli_prepare($link, $query);
mysqli_stmt_execute($stmts);
$results = mysqli_stmt_get_result($stmts);

if (mysqli_num_rows($results) > 0) {
    while ($company = $results->fetch_assoc()) {
        $childdb = $company['related_database_name'];
        echo "############### Company Database Name: " . $childdb . "<br/>";
        
        // Process outgoing email for the tenant database
        Email_incoming_data($childdb, $link);
    }
} else {
    echo "No company databases found.";echo"<br/><br/>";
    die();
}

/**
 * Handles outgoing email for a tenant database.
 * 
 * @param string $childdb Name of the tenant's database.
 * @param object $link Master database connection object.
 */
function Email_incoming_data($childdb, $link) {
    // Query SMTP configuration for the tenant
    $sql_cdr = "SELECT * FROM $childdb.tbl_smtp_connection WHERE status=1";
    $query = mysqli_query($link, $sql_cdr);
    $config = mysqli_fetch_array($query);

    // SMTP configuration parameters
    $v_username = $config['v_username'];
    $v_password = $config['v_password'];
    $v_server = $config['v_server'];
    $i_tls = $config['i_tls'];
    $i_port = $config['i_port'];
    $ATTACHMENT_PATH = "/var/www/html/";

    // Query unsent emails
    $qry = "SELECT EMAIL_ID, v_toemail, v_subject, v_body, add_bcc, i_RetryCount, V_rule 
            FROM $childdb.web_email_information_out 
            WHERE email_type = 'OUT' AND I_Status = '1' AND expiry_flag='0'
            AND (
                  (schedule_flag = 1 AND scheduling_date <= NOW()) 
                  OR schedule_flag IS NULL 
                  OR schedule_flag = 0
            ) 
            AND v_toemail NOT IN ('rep-57@zra.org.zm', 'rep-71@zra.org.zm')";
    $iNomoreMail = 0;
    while ($iNomoreMail == 0) {
        $res = mysqli_query($link, $qry);
        if (!$res) {
            echo "Error in query [$qry].";echo"<br/><br/>";
            break;
        }

        $iRowCount = mysqli_num_rows($res);
        if ($iRowCount == 0) {
            echo "No more emails to send.\n";echo"<br/><br/>";
            break;
        }

        // Process each unsent email
        while ($row = mysqli_fetch_assoc($res)) {
            $MailID = $row['EMAIL_ID'];
            $toAddr = $row['v_toemail'];
            $subject = $row['v_subject'];
            $V_Body = $row['v_body'];
            $add_bcc = $row['add_bcc'];
            $fileNames = $row['V_rule'];
            $iRetry = $row['i_RetryCount'];

            // Set up PHPMailer
            $mail = new PHPMailer;
            $mail->IsSMTP();
            if ($i_tls == "1") {
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
            $mail->Host = $v_server;
            $mail->Port = $i_port;
            $mail->Username = $v_username;
            $mail->Password = $v_password;
            $mail->From = $v_username;
            $mail->FromName = $v_username;
            $mail->AddAddress($toAddr);
            if (!empty($add_bcc)) $mail->AddBCC($add_bcc);
            $mail->WordWrap = 50;
            $mail->IsHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $V_Body;

            // Handle attachments
            $filePath = $ATTACHMENT_PATH . str_replace("../", "", $fileNames);
            
            if (file_exists($filePath)) {
                $filename = basename($filePath);
                $mail->AddAttachment($filePath, $filename);
            } else {
                echo "Attachment not found: $filePath";echo"<br/><br/>";
            }

            // Send email
            $iRetry++;
            if ($mail->Send()) {
                echo "Mail sent to: $toAddr [ID= $MailID]\n";
                $qryUpdate = "UPDATE $childdb.web_email_information_out 
                              SET I_Status = 2, i_RetryCount = $iRetry, v_LastError='Success', d_RetryTime=NOW() 
                              WHERE EMAIL_ID = $MailID";
                mysqli_query($link, $qryUpdate);
            } else {
                echo "Mail not sent. Error: " . $mail->ErrorInfo . "\n";
                $error = addslashes($mail->ErrorInfo);
                $iStatus = ($iRetry > 3) ? 3 : 1;
                $qryUpdate = "UPDATE $childdb.web_email_information_out 
                              SET I_Status = $iStatus, i_RetryCount = $iRetry, v_LastError='$error', d_RetryTime=NOW() 
                              WHERE EMAIL_ID = $MailID";
                mysqli_query($link, $qryUpdate);
            }

        }
        mysqli_free_result($res);
        sleep(10); // Pause to avoid overwhelming the server
    }
}

/**
 * Retrieves CC email IDs for a tenant.
 * 
 * @return string|null The CC email ID.
 */
function get_cc_mail_id() {
    global $link, $db;
    $query = $link->query("SELECT cc_mail_id FROM $db.tbl_connection");
    $row = $query->fetch_assoc();
    return $row['cc_mail_id'] ?? null;
}
?>
