<?php

date_default_timezone_set('Asia/Kolkata');

require_once '/var/www/html/ensembler/PHPMailer-5.2.28/PHPMailerAutoload.php';


//********** alliance GMAIL
define ("PORTNUM", '587');
define ("EMAIL_USER", 'rajdubey.alliance@gmail.com');
define ("EMAIL_PWD", 'syepvwaknagahctq');
define ("EMAIL_SERVER", 'smtp.gmail.com');


	
// define ("EMAIL_SERVER", 'outlook.office365.com');	
// define ("EMAIL_USER", 'complaints@ensembler.org.zm');
// define ("EMAIL_PWD", 'ensembler123456$');
 define ("EMAIL_TLS", '1');
	


// define ("EMAIL_SERVER", 'outlook.office365.com');	
// define ("EMAIL_USER", 'kewal.singh@ensembler.com');
// define ("EMAIL_PWD", 'Alliance@123');
// define ("EMAIL_TLS", '1');

define ("EMAIL_FROM_ADDR","singhkewal954@gmail.com");
define ("EMAIL_TO_ADDR","kewal.singh@ensembler.com");
define ("EMAIL_CC","");
define ("EMAIL_BCC","");

	/*************************************************/

		$toAddr = EMAIL_TO_ADDR;

		$fromAddr = EMAIL_FROM_ADDR;
		$subject = "Test Mail";
	 	$V_Body = "This is Body part of Test message";

	 	$fileNames = '';

		$add_cc = EMAIL_CC;
		$add_bcc = EMAIL_BCC;
			
	 	$fileNames = "";
			
				
		############################################
		
			$mail = new PHPMailer;
			$mail->IsSMTP();
			

			if ( EMAIL_TLS == "1")
			{
				$mail->SMTPAuth = true;
				$mail->SMTPSecure = "tls";
			}
			$mail->SMTPDebug = 2;

			$mail->SMTPOptions = array(
				'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
				)
			);
			$mail->Auth_type = 'LOGIN';

			$mail->Host = EMAIL_SERVER;
			$mail->Port = PORTNUM;
			$mail->Username = EMAIL_USER;
			$mail->Password = EMAIL_PWD;
			$mail->From = $fromAddr; 
			$mail->FromName = $fromAddr; //EMAIL_FROM_NAME;
	
			$mail->AddAddress($toAddr);

			if ( !empty($add_cc))
				$mail->AddCC($add_cc);
			if ( !empty($add_bcc))
				$mail->AddBCC($add_bcc);
						
			$mail->WordWrap = 50;
			$mail->IsHTML(true);

			$mail->Subject  =  $subject;

			//$mail->AddAttachment(ATTACHMENT_PATH . '/'.$filename1, $filename);
			if ( file_exists($fileNames) )
			{
				$filename = basename($fileNames);
				$mail->AddAttachment($fileNames, $filename);
			}

			$mail->Body     =  $V_Body;

			$send=$mail->Send();

		
			if($send==1)
			{
				echo "<br>Mail send to: ".$toAddr."]\n";
			}
			else
			{
				echo "<br>Mail not sent. Error: ". $mail->ErrorInfo."\n";
			 }

?>
