<?php
/***
* Login Script
* Author: Aarti
* Date: 29-02-2024
* Description: This file uses the reply option on mail send user.
**/
include_once("../../config/web_mysqlconnect.php"); // Database file incluede
include("../web_function.php"); // handling common function

$caseid = $_REQUEST['caseid'];
$customerid = $_REQUEST['customerid'];
//get value
$view=$_GET['view'];
$cemail=$_GET['cemail'];
$uid=$_GET['uid'];
$subject="(ID:".$uid.")";	
$addbtn_sms=$_REQUEST['addbtn_sms'];
$ticketid=$_REQUEST['iid']; 
$replyid = $_REQUEST['replyid'];
$reginoal_spoc = ($_SESSION['reginoal_spoc']!='') ? $_SESSION['reginoal_spoc'] : 3;
$reply_to = (isset($_REQUEST['reply_to'])) ? $_REQUEST['reply_to'] : "";
$v_cc_email=$_REQUEST['v_cc_email'];//cc

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    // Get the value of the hidden input containing the JSON string for attachments
    $liValuesJson = $_POST['liValues'];
    // Decode the JSON string to get the array of li values
    $liValues = json_decode($liValuesJson);
    if(count($liValues) > 1){
        // Path to save the zip file
        $zipFilePath = "Attachments/attachments_" .date("Y-m-d_H-i-s").".zip";
        // Create a zip archive
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            // Add files to the zip archive
            foreach ($liValues as $filename) {
                $filePath = 'Attachments/'.$filename;
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, $filename);
                }
            }
            $zip->close();
        } else {
            $error = "Failed to create the zip file.";
        }
        $targetPath = "CRM/".$zipFilePath;
    } else if(count($liValues) == 1){
        $uploadedFile = $liValues[0];
        $targetPath = "CRM/Attachments/".$uploadedFile;
    } else {
        $targetPath = "";
    }   
    
    $Mail = $_POST['V_EmailId']; // to email id
    $V_Content = $link->real_escape_string($_POST['V_Content']);//$_POST['V_Content']; For new line insertion
    $userfile = $_POST['attachments']; // attachment in email
    $V_Subject = $_POST['V_Subject']; // subject email
    $view = $_POST['view'];
    $AccountName = $_POST['AccountName'];
    $v_queue_type = $_POST['v_queue_type'];
    if($_POST['i_templateid'] != ''){
        $temp_arr = explode("|", $_POST['i_templateid']);
        $i_templateid = $temp_arr[0];
    } else {
        $i_templateid = 0;
    }

    $pos = stripos($V_Subject, "[TICKET - ".$_REQUEST['iid']."] ");
    $name = $_SESSION['logged'];
    $V_Campaign_mode = $_GET['V_Campaign_mode'];
    $subjectid = $_REQUEST['subjectid'];
    $todaytime = date("Y-m-d H:i:s");
    
    // end function part code for mail image Embedding.
    if(isset($_REQUEST['forward'])){
        // changed  from email from session to ardcoded email[vastvikta][03-05-2025]
        $from = $from_email;
    } else {
        $from = from_email;
    }
    // updated the conditon as it was  adding subject when ticket wasn't there [vastvikta][17-04-2025]
    $V_Subject = is_int($pos) ? $V_Subject : (is_numeric($_REQUEST['iid']) ? "[TICKET -" . $_REQUEST['iid'] . "] " . $V_Subject : $V_Subject);

    $uploadedFiles = $targetPath;
    if($v_queue_type == 'inquiry'){
        $queue_type = 'inquiry';
    } else {
        $queue_type = 'complain';            
    }
    $data_email=array();
    $data_email['Mail'] = $Mail;
    $data_email['from']= $from ;
    $data_email['V_Subject']=$V_Subject;
    $data_email['V_Content']=$V_Content;
    $data_email['V_rule']=($uploadedFiles == 1) ? '' :$uploadedFiles;
    $data_email['todaytime']=$todaytime;
    $data_email['view']=$view;
    $data_email['userfile_size']=$userfile_size;
    $data_email['subjectid']=$subjectid;
    $data_email['ICASEID']=$_POST['iid'];
    $data_email['i_templateid']=$i_templateid;
    $data_email['v_cc_email']=$v_cc_email;
    $data_email['error_mail']=$error_mail;
    $data_email['queue_type']=$queue_type;
    $data_email['caseid']=$caseid;
    $data_email['customerid']=$customerid;
    insert_emailinformationout($data_email);
    //code for updating read flag for the  email reply [vastvikta][17-12-2024]
    mysqli_query($link,"update $db.web_email_information set Flag='1' where EMAIL_ID='$replyid'");
?>
    <script type="text/javascript">
        setTimeout(function(){
            window.top.close();
        },2000);
    </script>
<?php
    $error = 'Message has been sent to '.$Mail.''; 
}

?>
<!-- Jquery and css code start -->
<html xmlns="http://www.w3.org/1999/xhtml">
<TITLE></TITLE>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<!-- <link rel="stylesheet" type="text/css" href="home_page/css/style.css" /> -->
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/email_template_css.css" />
<script src="<?=$SiteURL?>public/js/jquery.min.js"></script>
<!-- jquery code start File upload -->
<style type="text/css"> .myclass{ display: block !important;  } </style>
<!-- CKeditor new code added 21-02-2024 -->
<script src="https://cdn.ckeditor.com/ckeditor5/37.0.0/classic/ckeditor.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>CRM/omnichannel_config/css/web_send_email_reply.css">
<style>
input[type=text]{ border:1px solid #A9A6A6; border-radius:2px; padding:4px; width:99%; }
body {
    padding: 20px;
}
.btn-file {
    position: relative;
    overflow: hidden;
}
.btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
}	
ul {
    list-style-type: none;
}
li {
    font-family: Arial, sans-serif;
    font-size: 14px;
}
a {
    color: purple;
    text-decoration: underline;
}
/*ckeditor__editable*/
.ck.ck-content.ck-editor__editable.ck-rounded-corners.ck-editor__editable_inline.ck-blurred {
    height: 200px;
}
 /* Full-screen background blur and loader */
#loaderOverlay {
    display: none; /* Initially hidden */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8); /* Semi-transparent white background */
    z-index: 9999;
    backdrop-filter: blur(5px); /* Apply blur */
}

/* Loader in the center */
#loader {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}    
</style>
<script language="JavaScript">
	function validate(){
		if(document.frmums.V_EmailId.value == ''){
			alert("Please Enter Email!");
			document.frmums.V_EmailId.focus();
			return false;
		}
		if(document.frmums.V_Subject.value == ''){
			alert("Please Enter Subject!");
			document.frmums.V_Subject.focus();
			return false;
		}else{
			document.frmums.submit();
		}
	}	
</script>
<!-- Farhan Akhtar (16-10-2024) -->

<!-- Html code start -->
</head>
<body>
    
<!-- Farhan Akhtar (16-10-2024) -->
    <? if(!empty($action)) :?>
        <!-- <div class="seven">
            <h1>Content Messaging</h1>
        </div> -->
    <? endif; ?>
    <span id="closeButton" style="position: absolute; top: 10px; right: 15px; font-size: 20px; cursor: pointer;">Close</span>

	<form id="fileupload" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="replyid" id="replyid" value="<?=$replyid?>">
	<table align='center' width="90%" border="0" cellspacing="0">
		<tr class='mail-pagination'>
			<td valign=middle align='left' class='normaltextabhi'>
				<input type="submit" name="addbtn_email" id="compose" class="compose" value="Send" onclick='javascript:return validate();' 
				style='float:left; width:auto; margin-bottom:4px;'>

			<?php
			$q_reply=mysqli_query($link,"select *  from $dbname.web_email_information where EMAIL_ID='$replyid' ;");
			while($row_reply=mysqli_fetch_array($q_reply)){
				$d_email_date=$row_reply['d_email_date'];
				$reply_body="<br><br><br><hr>".$row_reply['v_body'];
				$reply_body= nl2br(quoted_printable_decode($reply_body));
				$reply_subject=$row_reply['v_subject'];
				$v_cc_email	=$row_reply['v_cc_email'];
				$multi_attach = explode(",", $row_reply['V_rule']);
				$v_queue_type = $row_reply['queue_type'];
			}
			$signatureimages='<img src="images/'.$dbheadlogo.'" title="zra" alt="zra" style="width:100px;height:40px;margin-top:10px;">';
			?>
			</td>
		</tr>
	</table>
	<div id="myDiv" style="position:relative;top:50px;left:1px;background-color:white;width:504px;display:none;"></div>
	<!-- Dropdown Menu-->
	<table width="90%" BORDER='0' align="center" cellpadding='0' cellspacing='0'>
		<? if($error!=''){?>
		<tr>
		<td style="color:#ffffff; width:100%; background:#00CC66; padding:5px; border:1px solid #02944b; text-align:center;"><?=$error?></td>
		</tr>
		<? } ?>
		<tr>
			<td style='border-bottom:1px solid #DBE1F1;'><font face="verdana" color=""><b>&nbsp;Compose Mail</b></font></td>
		</tr>
		<tr>
			<td class="contentRed" colspan="2" align="center">&nbsp;</td>
		</tr>
		<tr>
			<td>
				<style type="text/css">.tbl td{ padding: 4px; border-color: #e3e2e2;  }</style>
				<table width="100%" border="1" cellpadding="4" cellspacing="1" align="center" class="tbl" style='border-collapse: collapse;' bordercolor='#e3e2e2'>
                <!-- readonly in case when there is value [vastvikta][03-05-2025] -->
                    <tr>
                        <td valign="top">
                            <font color="#000000" face="verdana" size="2">To Email-Id<span class="orangeTxt">*</span></font>
                        </td>
                        <td>
                            <input type="text" name="V_EmailId" id="V_EmailId" 
                                value="<?= htmlspecialchars($reply_to) ?>" 
                                class="textbox" size="40" 
                                style="<?= $dis ?> margin-top: 2px;" 
                                placeholder="To :"
                                <?= empty($reply_to) ? '' : 'readonly' ?>>
                        </td>
                    </tr>

					<tr>
						<td><font color="#000000" face=verdana size=2>Subject<span class="orangeTxt">*</span></font></td>
						<td><?php
                                $reply_subject = $reply_subject; 
                            ?>
                            <input 
                                type="text" 
                                name="V_Subject" 
                                class="textbox" 
                                size="40" 
                                value="<?= htmlspecialchars($reply_subject) ?>" 
                                <?= !empty($reply_subject) ? 'readonly' : '' ?>>
                            <input type="hidden" name="customerid" id="customerid"  value="<?=$customerid?>">
                            <input type="hidden" name="caseid" id="caseid"  value="<?=$caseid?>">
						</td> 
					</tr>

					<tr>
						<td ><font color="#000000" face=verdana size=2>Created on</font></td>
						<td >
                            <!-- <font face="verdana" size=2><?=date("d-m-Y H:i:s",strtotime($d_email_date))?></font> changed date code [vastvikta][03-05-2025] -->
						<font face="verdana" size=2><?=date("d-m-Y H:i:s")?></font>
						</td>
					</tr>

					<tr>
						<td>
						<font color="#000000" face=verdana size=2>Template<span class="orangeTxt">*</span>
						</td>

						<td>
                        <!-- changed the table from email_templates to tbl_mailformats[vastvikta][11-03-2025] -->
						<?php $query = $link->query("SELECT * from $db.`tbl_mailformats` WHERE `MailTemplateName`='reply_mail' AND `MailStatus`=1 "); ?>
						<select name="template" id="template">
							<option value="">Select template</option>
							<?php 
							$count = 0;
                           while($row = $query->fetch_assoc()) :
                            
                            $mailformat = $row['MailSubject'].$row['MailGreeting'].$row['MailBody'].$row['MailSignature'];
							
								$template_name = $link->real_escape_string($mailformat);
								$count ++;
							?>
							<option value="<?=$template_name?>">Template <?=$count?></option>
							<?
							endwhile;
							?>
						</select>
						</td>
					</tr>
					<tr>
                        <? if($action == "") {?>
                            <td ><font color="#000000" face=verdana size=2>Content<span class="orangeTxt">*</span></font></td>
                            <td ><font color="#000000" face=verdana size=2 style="vertical-align:super">Reply with AI </font><a href="javascript:void(0)" id="generateReply"><img src="<?=$SiteURL?>public/images/magic.png" alt="AI" style="height: 25px;"></a></td>
                        <? }else { ?>
                            <td colspan='2'><font color="#000000" face=verdana size=2>Content<span class="orangeTxt">*</span></font></td>
                        <? } ?>
                    </tr>
					<tr>
						<td colspan='2'>
						<textarea name="V_Content" rows="8" cols="40" style="width:100%; height:300px" id="V_Content" >
						</textarea>
						</td>
					</tr>	
					<tr>
						<td><font color="#000000" face=verdana size=2>Attachment</font></td>
						<td>
							<div class="row files" id="files">
							<span class="btn btn-default btn-file">
								Browse  <input type="file" name="attachments[]" id="attachment"  multiple />
							</span>
							<br />
							<ul class="fileList"></ul>
							<input type="hidden" name="liValues" id="liValues">
						</div>
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center">		
							<input type="hidden" name="v_queue_type" value="<?=$v_queue_type?>" />
							<input type="hidden" name="AccountName" value="<?=$AccountName?>" />
							<input type="hidden" name="view" value="<?=$view?>" />
							<input type="hidden" name="name" value="<?=$name?>" />
							<input type="hidden" name="iid" value="<?=$_REQUEST['iid']?>" /> <!--uid hidden field -->
						</td>
					</tr>

				</table>

			</td>
		</tr>
	</table>
	</form>
<div id="loaderOverlay">
<div id="loader">
    <img src="<?=$SiteURL?>public/images/galaxy_ai.gif" alt="Loading...">
</div>
</div>
<script src="<?=$SiteURL?>CRM/omnichannel_config/js/omni_config.js"></script>   
<script>
    // Close window on cross icon click
    document.getElementById("closeButton").addEventListener("click", function () {
        window.close();
    });
</script> 