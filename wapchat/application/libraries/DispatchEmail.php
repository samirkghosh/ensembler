<?
/***************************************************
FileName: dispatch_email.php 
Purpose: Reads outgoing email queue (web_email_information from master)
	 Sends all unsent mail (status=0). On success, it updates staus to 1
	 i_Status: 1: Pending, 2: Sent, 3: Aborted
	
Date: 15-06-202	[SG]
****************************************************/
require_once("/var/www/html/zra/include/web_mysqlconnect.php");
require_once("/var/www/html/zra/include/class-phpmailer.php");
	
define ("PORTNUM", "25");
define ("EMAIL_USER","zracallcenter@zra.com.zm");
define ("EMAIL_PWD","Lwsccs22020");
define ("EMAIL_SERVER",  "172.16.0.178");	
define ("EMAIL_SSL","0");


//define ("EMAIL_FROM_ADDR","xxxx");
//define ("EMAIL_FROM_NAME", "yyy");
//define ("ATTACHMENT_PATH","/var/www/html/uetcl/upload");



	/*************************************************/
	/* get all the mails not sent */
	 $qry="SELECT EMAIL_ID, v_toemail, v_fromemail,add_cc,add_bcc, v_subject, v_body, V_rule, d_email_date, email_type, module, size, subjectid ,ICASEID, original_subject,i_RetryCount FROM web_email_information WHERE email_type = 'OUT' and I_Status='1'  ORDER BY EMAIL_ID DESC
";

	$iNomoreMail = 0;
	
	while( $iNomoreMail == 0)
	{

		$res = mysql_query($qry);

		if ( $res == FALSE)
		{
			echo "Select error [$qry]. Error=". mysql_error();
			break;
		}	
		
		$iRowCount = mysql_num_rows($res);
		/* count if row count is zero then break  otherwise assign number of rows in the query to $iRowCount */
		if ( $iRowCount == 0)
		{
			echo "No more mails to send\n";
			break;
		}	

		for ( $i = 0; $i < $iRowCount; $i++ )
		{
			$iRetry =0;
			// Fetch the row
			$row = mysql_fetch_assoc($res);

	 		$MailID  = $row['EMAIL_ID']; //  email id
			$toAddr = $row['v_toemail'];
			$fromAddr = $row['v_fromemail'];
			$fromAddr = "zracallcenter@zra.com.zm";
			$subject = $row['v_subject'];
	 		$V_Body = $row['v_body'];
	 		//$fileNames = $row['V_rule'];
			$add_cc = $row['add_cc'];
			$add_bcc = $row['add_bcc'];
			
	 		$fileNames = $row['original_subject'];
			
			$iRetry = $row['i_RetryCount'];

			//print_r($row);
			//exit();
				
		############################################

			$mail = new PHPMailer();
			$mail->IsSMTP();

			if ( EMAIL_SSL == "1")
			{
				$mail->SMTPAuth = true;
				$mail->SMTPSecure = "ssl";
			}
			$mail->SMTPDebug = 1;

			$mail->Host = EMAIL_SERVER;
			$mail->Port = PORTNUM;
			$mail->Username = EMAIL_USER;
			$mail->Password = EMAIL_PWD;

			$mail->From = $fromAddr; 
			$mail->FromName = $fromAddr; //EMAIL_FROM_NAME;
	
			$mail->AddAddress($toAddr);
			$mail->AddCC($add_cc);
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

			$iRetry = $iRetry +1;
			$send=$mail->Send();
			if($send==1)
			{
				echo "<br>Mail send to: ".$toAddr."[ID= ".$MailID."]\n";
			 	$qry = "UPDATE web_email_information SET I_Status = 2, i_RetryCount = $iRetry, v_LastError='Success', d_RetryTime=NOW() WHERE EMAIL_ID = $MailID";
				mysql_query($qry);
			}
			else
			{
				echo "<br>Mail not sent. Error: ". $mail->ErrorInfo."\n";
				$error = addslashes($mail->ErrorInfo);
				if ( $iRetry > MAX_RETRY_SEND_MAIL)
					$iStatus =3;
				else
					$iStatus = 1;
			 	$qry = "UPDATE web_email_information SET I_Status = '$iStatus', i_RetryCount = $iRetry, v_LastError='$error', d_RetryTime=NOW()  WHERE EMAIL_ID = $MailID";
				if (mysql_query($qry) == 0)
					echo "Unable to Update $qry, errorcode:".mysql_error()."\n";
			 }

		}
		mysql_free_result($res);
		sleep(10);

	}
	mysql_close($link);
		
	?>
