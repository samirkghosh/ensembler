<?php 
	if(!isset($_SESSION)) { 
		session_start(); 
	} 
	include_once '../config/web_mysqlconnect.php';
	
	function subject_decode_string($subject){
		$utf = substr($subject, 0, 10);
		if(strcasecmp($utf, "=?utf-8?B?") == '0')
		{
			// $subject = base64_decode(str_ireplace("=?UTF-8?", "",$subject));
			$d = str_ireplace("=?utf-8?B?", "",$subject);//echo  $d."<br>";
			return base64_decode($d);
		}
		return $subject ;
	}

	function decode_text_check($textVal){
		$x = substr($textVal,0, 50);
		$y = strpos($x, ' ');
		if($y == "")
		{
			return 1;
		}else{
			return 0;
		}

	}

	function getCaseID_Subject($subject){
		$subject=string_between_two_string($subject,"[","]");   // TICKET # 200056
		// if (strpos($subject,'LWSC/') !== false)    // this will work for another formate so dont remove it. : 29-12-2020 vijay
		if (strpos($subject,'TICKET #') !== false) {
			$spos=strpos($subject, "#");
			$epos=strlen($subject);
			//echo $subject."<br>poc is avail  --Start pos".$spos." end pos". $epos."<br>";
			$caseID = substr($subject, $spos+1,$epos); 
		}else{
			$caseID = "";
		}
		return $caseID;
	}
	function string_between_two_string($str, $starting_word, $ending_word){ 
		$arr = explode($starting_word, $str); 
		if (isset($arr[1])){ 
			$arr = explode($ending_word, $arr[1]); 
			return $arr[0]; 
		} 
		return ''; 
	}



	if(isset($_POST['id']) && (isset($_POST['action']) && $_POST['action'] == 'view_mail' )) {

		$Mid = $_POST['id'];

		// update flag for read unread
		$link->query("UPDATE $dbname.web_email_information SET Flag='1' WHERE EMAIL_ID='$Mid'; "); 

		// get email info
		$query_info = $link->query("SELECT v_subject,v_fromemail,v_body,ICASEID,i_DeletedStatus,V_rule,i_reminder,d_email_date FROM $dbname.web_email_information WHERE EMAIL_ID='$Mid'");
		$query5 = $query_info->fetch_assoc();
		$vsubject=$query5['v_subject'];
		$vsubject = subject_decode_string($vsubject);
		$vfromemail=$query5['v_fromemail'];
		$i_reminder=$query5['i_reminder'];//some one is working condition
		$v_body=$query5['v_body'];
		$emaildatetime=$query5['d_email_date'];
		// Convert to Unix timestamp
		$timestamp = strtotime($emaildatetime);

		// Format the date
		$formattedDate = date('M j, g A', $timestamp);

		switch ($vsubject) {
			case 'Service Request Form':
				break;
			default:
				$v_body = nl2br($v_body);
				break;
		}
		$ICASEID=$query5['ICASEID'];
		$i_DeletedStatus=$query5['i_DeletedStatus'];

		// get account info
		$acc_info = $link->query("SELECT AccountNumber FROM $db.web_accounts WHERE (email like '%$vfromemail%')");
		$q= $acc_info->fetch_row();
		$customerid = $q[0];

		$param1 = ($customerid != '') ? "new_case_manual.php?customerid=$customerid&emailid=$Mid&mr=6" : "new_case_manual.php?email=$vfromemail&emailid=$Mid&mr=6";
		$param2 = $Mid;

		$checkdecodeVal=decode_text_check($v_body);
		if($checkdecodeVal==1) {
			$x=str_replace('\n',"", $v_body);
			$x=str_replace('\r',"", $x);
	
		}

		$multi_attach = explode(",", $query5['V_rule']);
		$i=0;
		foreach($multi_attach as $attach) { 
			if($attach !='../lead_doc/' || $attach !='' || $attach !='../uploaded/') {  
				$i++;
				if(strpos($attach,"imap/")!==false) { 
					$attach = "../$attach"; 
					$attach = "../imap/$attach";
					$attachment = "<a href='javascript:void(0)' onClick=\"JavaScript:window.open('{$attach}','_blank','height=350,width=600,scrollbars=0')\" class='text-primary'><i class='fas fa-paperclip'></i> Attachment {$i} </a>";
				}
				
		    } 
		}

		if(($groupid=='080000') || ($groupid=='0000') || ($groupid=='070000')) {
			if($ICASEID!='' && $ICASEID!='0') { 
				$caseinfo = "<b><code>Case is created with this case ID: $ICASEID</code></b>";
			}else if($i_DeletedStatus!=2 ) {
				$caseinfo = "<input type=\"button\" name=\"sub1\" class=\"btn btn-success btn-sm\" value=\"Create Case\" id=\"createcase\" data-param1=\"$param1\" data-param2=\"$param2\">";
			}

			if(empty($ICASEID)) {
			    $ICASEID=getCaseID_Subject($vsubject);
				$ICASEID=trim($ICASEID);
				if(!empty($ICASEID)) {
				  $where_c=" ticketid ='$ICASEID'";
				}
			}
			$where_c=" ticketid ='$ICASEID'";
            $status='';

			if(!empty($ICASEID)) {
			    $qq= "SELECT iCaseStatus, ticketid, iPID, vCustomerID  FROM $db.web_problemdefination WHERE ( $where_c) ";
				$q1= $link->query($qq);
				$numRows= $q1->num_rows;
				$fetch1= $q1->fetch_assoc();
				$case_id= $fetch1['iPID'];
				$caseee= $fetch1['ticketid'];
				$Customerid= $fetch1['vCustomerID'];

				$custv = $link->query("SELECT email FROM $db.web_accounts WHERE AccountNumber='$Customerid'");
				$qcust= $custv->fetch_assoc();

				if( ($qcust['email']==$vfromemail)  && $qcust['email']!='') {
				   $q= "UPDATE $dbname.web_email_information SET ICASEID='".$caseee."',email_test='web_queue_1' WHERE EMAIL_ID='".$vfromemail."' ; ";
				   $link->query($q);
				}
				$ticket_query = $link->query("SELECT iCaseStatus,ticketid FROM $db.web_problemdefination WHERE (iPID='$case_id') ");
				$ress = $ticket_query->fetch_assoc();

				$ticket_status = $link->query("SELECT ticketstatus FROM $db.web_ticketstatus WHERE id='".$ress['iCaseStatus']."'");

			 	$rest= $ticket_status->fetch_assoc();
			  	$status = $rest['ticketstatus'];
			}

			$reply_btn = ($ICASEID != '' && $case_id != '') 
			? 
			   "<input type=\"button\" value=\"Reply\" class=\"btn btn-success btn-sm\" onClick=\"JavaScript:window.open('web_send_email_reply.php?replyid=$Mid&amp;iid=$caseee&amp;reply_to=$vfromemail','_blank','height=550, width=900,scrollbars=0')\">" 
			: 
			   "<input type=\"button\" value=\"Reply\" class=\"btn btn-success btn-sm\" onClick=\"JavaScript:window.open('web_send_email_reply.php?replyid=$Mid&amp;iid=ensembler&amp;reply_to=$vfromemail','_blank','height=550, width=900,scrollbars=0')\">"
			;

			if($i_DeletedStatus == 2) { 
				$deletedinfo = "Email is already deleted!"; 
			}

		

		}

		echo json_encode(["status"=>"success","subject"=>$vsubject,"content"=>$v_body,"emailid"=>$vfromemail,"emaildatetime"=>$formattedDate,"attachments"=>$attachment,"caseinfo"=>$caseinfo,"replybtn"=>$reply_btn,"deletedinfo"=>$deletedinfo,"mid"=>$Mid]);
		exit();

	}
