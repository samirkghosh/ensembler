<?php
/**
 * This example shows making an SMTP connection without using authentication.
 */

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

require_once '../PHPMailerAutoload.php';

//Create a new PHPMailer instance
$mail = new PHPMailer;

//Tell PHPMailer to use SMTP
$mail->isSMTP();


//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 2;

//Ask for HTML-friendly debug output
//$mail->Debugoutput = 'html';

//Set the hostname of the mail server
$mail->Host = "192.168.0.26";

//Set the SMTP port number - likely to be 25, 465 or 587
$mail->Port =25 ;
//$mail->Port =587 ;


//Whether to use SMTP authentication
$mail->SMTPAuth = true;
//Set who the message is to be sent from
$mail->Username = 'crm@ensembler.zm';
$mail->Password = 'CustomerRm@2o22';
//$mail->SMTPSecure = 'ssl';
$mail->SMTPSecure = 'tls';


$mail->SMTPOptions = array(
'ssl' => array(
'verify_peer' => false,
'verify_peer_name' => false,
'allow_self_signed' => true
)
);


$mail->setFrom('crm@ensembler.zm', 'First Last');

//Set an alternative reply-to address

$mail->addReplyTo('crm@ensembler.zm', 'First Last');
//Set who the message is to be sent to
//$mail->addAddress('crm@ensembler.zm', 'Test');
$mail->addAddress('samkghosh@yahoo.co.uk', 'ZICTA');
//Set the subject line

$mail->Subject = 'PHPMailer SMTP without auth test';
$mail->Body='Test';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';
//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
