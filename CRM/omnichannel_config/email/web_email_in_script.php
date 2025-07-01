<?
/**************************************************************************************
* FileName: web_email_script_v2.php
* Purpose: Readed mails from server and added to the in-queue email list for forther processing
*		Uses only IMAP server
*		Reads only UNSEEN mail
*		Download all attchment and body
*		Store information in database
*		Mark it is as SEEN
*
*	[21-05-2020]	[SG]	Modified the existing code 
************************************************************************************************/

include_once("../../../config/web_mysqlconnect.php"); // Include database connection file 
echo "1\n";
require_once '/var/www/html/ensembler/PHPMailer-5.2.28/PHPMailerAutoload.php';
echo "2\n";

// include_once("include/class-phpmailer.php");
define ("DOWNLOADPATH", "/var/www/html/ensembler");
define ("DBG","1");

/************************************************************************************************/

/* open database for queue entry */
//$link = mysqli_connect("167.71.232.203","cron","1234");
$link = mysqli_connect("165.232.183.220","cron","1234");

//$link = mysqli_connect("139.59.10.9","root","All1@nc3@1986!");

mysqli_select_db($link, "ensembler");
$db = "ensembler";
$dbname = "ensembler";


/**********************************************************************
* define message types comes in header for processing
***************************************************************************/
$message = array();
$message["attachment"]["type"][0] = "text";
$message["attachment"]["type"][1] = "multipart";
$message["attachment"]["type"][2] = "message";
$message["attachment"]["type"][3] = "application";
$message["attachment"]["type"][4] = "audio";
$message["attachment"]["type"][5] = "image";
$message["attachment"]["type"][6] = "video";
$message["attachment"]["type"][7] = "other";

/************************************************************************************
* Get the IMAP credential from the table for accessing the mail server
***************************************************************************************/
$sql_connect_result="SELECT v_ipaddress,v_username,v_pasowrd FROM $dbname.tbl_imap_connection";
$result_connect_result=mysqli_query($link,$sql_connect_result);

/*********************************************************************************************
 Process for each email server
********************************************************************************************/
//while($row_result_connect=mysqli_fetch_array($result_connect_result))
{
	$hostname				=$row_result_connect['v_ipaddress'];
	//$hostname				='{imap.rediffmailpro.com:993/imap/ssl/novalidate-cert}INBOX';

	$username				=$row_result_connect['v_username'];
	$password				=$row_result_connect['v_pasowrd'];

	// $username				='uc2000helpdesk@gmail.com';
	//$password				='jfvrpwqegywqakpd';
//	$username				='rajdubey.alliance@gmail.com';
	//$password				='syepvwaknagahctq';
     
	 $hostname				='{imap.gmail.com:993/imap/ssl/novalidate-cert}INBOX';
	 $username				='allianceinfo91@gmail.com';
	  $password				='jkugmriynqbbqgpz';
     echo "<br>hostname ".$hostname;
      echo "<br>username ".$username;
      echo "<br>password ".$password;
	/* connect to mail server */
	$inbox = imap_open($hostname,$username,$password);
	print_r(imap_errors());
	
echo "Going to Download\n";

	/* grab emails */
	// $emails = imap_search($inbox,'ALL');
	$emails = imap_search($inbox,'UNSEEN');

	print_r($emails);

	//echo "Email Count - > ".count($emails)."<br>"; 

	/* if emails are returned, cycle through each... */
	if($emails) 
	{
		/* begin output var */
		$output = '';
	
		
		$total_inserted=0; 
		$total_already_inserted=0;
		rsort($emails);

		/* for every email... */
		foreach($emails as $email_number) 
		{

			$iUID = imap_UID($inbox, $email_number);
			print "Email no: $email_number, UID=$iUID\n";
			/************************************************************************
			* using  UID check if the mail is already there
			***********************************************************************************/
			$sql_qry = "SELECT i_UID FROM $dbname.web_email_information where i_UID = '".$iUID."'";
			$res = mysqli_query($link,$sql_qry);
			$numrow = mysqli_num_rows($res);
			if ( $numrow > 0)
			{ 
				$header = imap_headerinfo($inbox,$email_number,0); 
				print "*** Already downloaded.seqno: $email_number, UID=$iUID\n";
				print_r($header);
				print "*******************************************\n";
				$total_already_inserted++;

				continue;

			}
			else
				print "*** New Message: $iUID\n";

			$header = imap_headerinfo($inbox,$email_number,0); 
		
			if(DBG)
			{
				print_r($header);

			}

			$overview = imap_fetch_overview($inbox,$email_number,0); 
			$oMsg = "";
			//$message = nl2br(getMessageContent($inbox,$email_number, $oMsg)); //imap_fetchbody($inbox,$email_number,2);
			//[25-05-2020]	[SG] why nl2br????
			$message = getMessageContent($inbox,$email_number, $oMsg); //imap_fetchbody($inbox,$email_number,2);
			$entry_log="Orginalmessage".$oMsg;
		
			$from = $header->from[0]->personal;
			$from = (validateEmail($from)) ? $from : $header->from[0]->mailbox."@".$header->from[0]->host; 		

			$to   = $header->to[0]->mailbox."@".$header->to[0]->host;
			$date = date('Y-m-d H:i:s', strtotime($header->Date)); 

			/* output the email header information */
			$subject = $overview[0]->subject;
			/*
			$subject_t=imap_utf8($overview[0]->subject);
			if($part->type==0)
			{
				$subject = imap_utf8($overview[0]->subject);
			}
			else
			{
				$subject = $overview[0]->subject;
			}	
			*/
			//print_r($subject);

			$entry_log.="\n\nOrginal subject-".$subject."\nPart type-".$part->type."\n\n".$subject_t;
			//$subject =subject_decodestring($subject1);//new function added to remove utf8 on 17Jan20
		
			//$from = $overview[0]->from;
			$subject = addslashes($subject);
			$body 	 = addslashes($message);
			$undeliverablearray=explode(":",$subject);
			$undeliverable = $undeliverablearray[0];
			$entry_log = addslashes($entry_log);
		
			/* ***** */


			/******* */
			/* fetch attactment */
			$structure = imap_fetchstructure($inbox,$email_number);    

			$parts = $structure->parts; 

			$fpos=2; 
			$multifiles="";

			/* Save all the attachments in the download folder for this mail */
			for($ii = 1; $ii < count($parts); $ii++)
			{ 
				//$message["pid"][$ii] = ($ii); //echo "ii > ".$ii;
				$part = $parts[$ii]; 
				//$part->disposition="";

				if(strtolower($part->disposition) == "attachment") 
				{
									
					//$message["type"][$ii] = $part->type ; //. "/" . strtolower($part->subtype);
					//$message["subtype"][$ii] = strtolower($part->subtype);
					$ext=$part->subtype;
					$params = $part->dparameters; //print_r($params);

					/* Store the files in imap folder */
					$filename="imap/".$part->dparameters[0]->value;
					if ( strstr($filename,"UTF"))
					{
						
						$filePath = tempnam("/var/www/html/ensembler/imap","attach_");
						$filePath = $filePath.".$ext";
						$filename = strstr($filePath,"imap");
					}
					else
					{
						$filename = "$filename"; //echo 'type > '.$part->type.'<br>';
						$filePath= "/var/www/html/ensembler/".$filename;

						if ( file_exists($filePath) )
						{
							$filePath = tempnam("/var/www/html/ensembler/imap","attach_");
							$filePath = $filePath.".$ext";
							$filename = strstr($filePath,"imap");

						}
					}
														
					$mege="";
					$data="";

					$mege = imap_fetchbody($inbox,$email_number,$fpos);  


					$data = getdecodevalue($mege,$part->type);	

					print "FileName: $filePath\n";
					//exit();

					//$fp = fopen(DOWNLOADPATH."/".$filename,w);
					$fp = fopen($filePath, w);

					fputs($fp,$data);
					fclose($fp);

					$fpos+=1; 	
					
					$multifiles = ($multifiles=="") ? $filename : $multifiles.",".$filename;		
				}
	
			}
			/* end fetching attachment */
			/* get information specific to this email */
			//echo "<pre>";
		
			$total_inserted ++;

			/* insert the mail informatin in the mail queue */
			$sql_insert_information="INSERT INTO $dbname.web_email_information (v_toemail, v_fromemail, d_email_date, v_subject, Filter_bySubject, Filter_byFrom, V_rule, v_body, email_type, ICASEID, i_Update_status, orginal_subject, email_test, i_UID) VALUES ('$to', '$from', '$date', '$subject', '$subject', '$from', '$multifiles', '$body', 'IN', '', '', '$entry_log', 'email_script_page', $iUID)";  


			echo "<br><br>";

			$result_insert=mysqli_query($link,$sql_insert_information) ;
			if ( $result_insert == FALSE)
			{
				echo "Unable to insert:".$sql_insert_information."Error:";
				print_r($header);
				print("\n");
			exit();

			}
			else
			{	
				/* Set the Flag to SEEN */
				$status = imap_setflag_full($inbox, $email_number, "\\Seen \\Flagged"); 
				echo gettype($status);
				echo $satus. "\n";
				echo "Set Seen flag for:".$email_number."\n";
			}
			//break;
			//if ( $total_inserted == 10)
			//break;
		} // for ecach
	
		echo "Total Inserted -> ".$total_inserted."\n";
		echo "Already Inserted -> ".$total_already_inserted."\n";
		echo "*************************************************************\n";	
	}  // end if email

	/* close the connection */
imap_close($inbox);

} // end while

mysqli_close($link);

/***********************************************************************
* Note: all the functions used is defined below
*******************************************************************************/
function log1($msg)
{
	if (DBG)
	{
		file_put_contents("/tmp/imap.log", $msg, FILE_APPEND);
		file_put_contents("/tmp/imap.log","\n--\n", FILE_APPEND);
	}

}


/*****************************************/
function subject_decodestring($subject)
{

	$utf = substr($subject, 0, 10);
	if(strcasecmp($utf, "=?utf-8?B?") == '0')
	{
		// $subject = base64_decode(str_ireplace("=?UTF-8?", "",$subject));
		$d = str_ireplace("=?utf-8?B?", "",$subject);//echo  $d."<br>";
		return base64_decode($d);
	}
	return $subject ;

}

 
/* function */

function validateEmail($email)
{
    // SET INITIAL RETURN VARIABLES

        $emailIsValid = FALSE;

    // MAKE SURE AN EMPTY STRING WASN'T PASSED

        if (!empty($email))
        {
            // GET EMAIL PARTS

                $domain = ltrim(stristr($email, '@'), '@');
                $user   = stristr($email, '@', TRUE);

            // VALIDATE EMAIL ADDRESS

                if
                (
                    !empty($user) &&
                    !empty($domain) &&
                    checkdnsrr($domain)
                )
                {$emailIsValid = TRUE;}
        }

    // RETURN RESULT

        return $emailIsValid;
}

function getdecodevalue($message,$coding)
{
	//return quoted_printable_decode($message);
	switch($coding) {
	case 0:
		$message = quoted_printable_decode($message);
		break;
	case 1:
		$message = imap_8bit($message);
		break;
	case 2:
		$message = imap_binary($message);
		break;
	case 3:
		$message = imap_base64($message);
		break;
	case 5:
		$message = imap_base64($message);
		break;
	case 6:
	case 7:
		$message=imap_base64($message);
		break;
	case 4:
		$message = imap_qprint($message);
		break;
	}
	return $message;
}

function getMessageContent($mail,$id, &$orgMsg)
{
	
	// Get content of text message.
	
	$mid = $id;
	
	$struct = imap_fetchstructure($mail,$mid);
	
	$parts = $struct->parts;
	$i = 0;
	if (DBG == 1)
	{
		echo "Single Part\n";
		print_r($struct);
		echo "Single Part ends\n";
	}

//	log1("Parts:".print_r($parts));
	if (!$parts)
	{


		/* Simple message, only 1 piece */
		$attachment = array(); /* No attachments */
		$content = imap_body($mail, $mid);
		log1("Content:".$content);
	}
	else
	{
		/* Complicated message, multiple parts */
	
		$endwhile = false;
	
		$stack = array(); /* Stack while parsing message */
		$content = ""; /* Content of message */
		$attachment = array(); /* Attachments */
	
		while (!$endwhile)
		{
			if (!$parts[$i])
			{
				if (count($stack) > 0)
				{
					$parts = $stack[count($stack)-1]["p"];
					$i = $stack[count($stack)-1]["i"] + 1;
					array_pop($stack);
				}
				else
				{
					$endwhile = true;
				}
			}
	
			if (!$endwhile)
			{
				/* Create message part first (example '1.2.3') */
				$partstring = "";
				foreach ($stack as $s)
				{
					$partstring .= ($s["i"]+1) . ".";
				}
				$partstring .= ($i+1);
	
				if (strtoupper($parts[$i]->disposition) == "ATTACHMENT" && $parts[$i]->ifparameters != 0) 
				{ /* Attachment */
					if (DBG)
					{
						echo "******xxxxxxx********************[$i]\n";
						print_r($parts[$i]);
					}
					/* decode the file name */
					$x = imap_mime_header_decode($parts[$i]->parameters[0]->value);
					
					//$attachment[] = array("filename" => $parts[$i]->parameters[0]->value,
					$attachment[] = array("filename" => $x[0]->text,
									"filedata" => imap_fetchbody($mail, $mid, $partstring));
					if(DBG)
					{
						echo "**************************\n";
					}
				}
				elseif (strtoupper($parts[$i]->subtype) == "PLAIN")
				{ 
					/* Message */
					//$content .= imap_fetchbody($mail, $mid, $partstring);
					//[25-05-2020]	[SG] check for encoding and decode
					$msg = imap_fetchbody($mail, $mid, $partstring);
					log1("PLAIN:".$content);
					if ( $parts[$i]->encoding == 3)
						$msg = base64_decode($msg);
					$content .= $msg;
					log1("PLAIN1:".$content);
				}
			}
	
			if ($parts[$i]->parts)
			{
				$stack[] = array("p" => $parts, "i" => $i);
				$parts = $parts[$i]->parts;
				$i = 0;
			}
			else
			{
				$i++;
			}
		} /* while */
	} /* complicated message */
	
	// Convert quoted-printable characters in text content
 	$orgMsg = $content;
	if (DBG)
	{
		echo "[$content]";
	}
	$x = imap_mime_header_decode($content);
	$out="";
	for ( $i = 0; $i < count($x); $i++)
		$out=$out.' '. $x[$i]->text;
	//$Body_text = quoted_printable_decode($content);
	return $out;

}


/********************************************************************************
*
******************************************************************************************/
function is_base64_encoded($data)
{
        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
            return TRUE;
        } else {
            return FALSE;
        }
}


/***************************************************************************************
*********************************************************************************************/

function getStringBetween($str,$from,$to)
{
    $sub = substr($str, stripos($str,$from)+strlen($from),strlen($str));
    return substr($sub,0,stripos($sub,$to));
}

/* end function */
 
?>
