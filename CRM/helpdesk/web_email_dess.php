<?php
/***
 * Document Upload
 * Author: Aarti
 * Date: 09-04-2024
 *  This code is used in a web application to email details display 
-->
 **/
   include("../../config/web_mysqlconnect.php"); //  Connection to database // Please do not remove this
   $ID=$_GET['id'];
   $iid=$_GET['iid'];

   if($_GET['type'] = 'out'){
      $sql_email_details = "select * from $db.web_email_information_out where EMAIL_ID='$ID'";
   }else{
      $sql_email_details = "select * from $db.web_email_information where EMAIL_ID='$ID'";
   }
   $fetch2 = mysqli_query($link,$sql_email_details);
   while($row=mysqli_fetch_array($fetch2))
   {
   $V_Emailto=$row['v_toemail'];
   $V_EmailFrom=$row['v_fromemail'];
   $v_subject=$row['v_subject'];
   $v_cc_email=$row['v_cc_email'];
   /* [05-06-2020]	[SG]	Added nl2br for new line in display */
   $V_BodyMessage = $row['v_body'];
   if ( !strstr($V_BodyMessage,"<br"))
   	$V_BodyMessage = nl2br($V_BodyMessage);
   $multi_attach = explode(",", $row['V_rule']);
   $d_email_date=$row['d_email_date'];
   $emailtype=$row['email_type'];
   }
   
   ?>
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
      <title>Untitled Document</title>
      <script language="javascript" type="text/javascript" src="<?=$SiteURL?>public/jscripts/tiny_mce/tiny_mce.js"></script>
      <script language="javascript" type="text/javascript" src="<?=$SiteURL?>public/jscripts/general.js"></script>
      <script language="javascript" type="text/javascript">
         tinyMCE.init({
         	mode : "exact",
         	elements : "V_Content",
         	theme : "advanced",
         	plugins : "table,advhr,advimage,advlink,flash,paste,fullscreen,noneditable,contextmenu",
         	theme_advanced_buttons1_add_before : "newdocument,separator",
         	theme_advanced_buttons1_add : "fontselect,fontsizeselect",
         	theme_advanced_buttons2_add : "separator,forecolor,backcolor,liststyle",
         	theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,",
         	theme_advanced_buttons3_add_before : "tablecontrols,separator",
         	theme_advanced_buttons3_add : "flash,advhr,separator,fullscreen",
         	theme_advanced_toolbar_location : "top",
         	theme_advanced_toolbar_align : "left",
         	extended_valid_elements : "hr[class|width|size|noshade]",
         	file_browser_callback : "ajaxfilemanager",
         	paste_use_dialog : false,
         	theme_advanced_resizing : true,
         	theme_advanced_resize_horizontal : true,
         	apply_source_formatting : true,
         	force_br_newlines : true,
         	force_p_newlines : false,	
         	relative_urls : true
         });
         
         function ajaxfilemanager(field_name, url, type, win) {
         	var ajaxfilemanagerurl = "<?=$SiteURL?>public/jscripts/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php";
         	switch (type) {
         		case "image":
         			break;
         		case "media":
         			break;
         		case "flash": 
         			break;
         		case "file":
         			break;
         		default:
         			return false;
         	}
         	var fileBrowserWindow = new Array();
         	fileBrowserWindow["file"] = ajaxfilemanagerurl;
         	fileBrowserWindow["title"] = "Ajax File Manager";
         	fileBrowserWindow["width"] = "100";
         	fileBrowserWindow["height"] = "50";
         	fileBrowserWindow["close_previous"] = "no";
         	tinyMCE.openWindow(fileBrowserWindow, {
         	  window : win,
         	  input : field_name,
         	  resizable : "yes",
         	  inline : "yes",
         	  editor_id : tinyMCE.getWindowArg("editor_id")
         	});
         	
         	return false;
         }
      </script>
      <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/style.css" />
      <link rel="stylesheet" type="text/css" href="<?=$SiteURL?>public/css/email_template_css.css" />
   </head>
   <body>
      <br>
      <table align='center' width="90%" border="0" cellspacing="0">
      <FORM NAME="frmums_details" method="post" enctype="multipart/form-data">
         <?php if(($groupid=='070000') && ($V_EmailFrom==$centralspoc)) { ?>
         <tr class="mail-pagination">
            <td valign="middle" align="left" class="normaltextabhi" colspan="2">
               <input type="button" name="addbtn_email" id="compose" class="compose" value="Reply" onClick="window.location.href='web_send_email_reply.php?replyid=<?=$ID?>&iid=<?=$iid?>';" style="float:left; width:auto; margin-bottom:4px;">
            </td>
         </tr>
         <?php } ?>
      </FORM>
      <style type="text/css">.tbl td{ padding: 4px; border-color: #e3e2e2;  }</style>
      <table width="90%" border="1" cellpadding="4" cellspacing="1" align="center" class="tbl" style='border-collapse: collapse;' bordercolor='#e3e2e2'>
         <td width="20%" ><font color="#000000" face=verdana size=2>To Email-Id</font></td>
         <td width="80%" ><font face="verdana" color="#CC0000" size=2><?=$V_Emailto?></font></td>
         </tr>
			<tr>
				<td ><font color="#000000" face=verdana size=2>From Email-Id</font></td>
				<td >
				<font face="verdana" color="#CC0000" size=2><?=$V_EmailFrom?></font>
				</td>
			</tr>
			<!-- <tr>
				<td><font color="#000000" face=verdana size=2>CC</font></td>
				<td><font face="verdana" color="#CC0000" size=2><?=$v_cc_email?></font></td>
			</tr> -->
			<tr>
				<td ><font color="#000000" face=verdana size=2>Subject</font></td>
				<td >
				<font face="verdana" size=2><?=$v_subject?></font>
				</td>
			</tr>
			<tr>
				<td ><font color="#000000" face=verdana size=2>Created on</font></td>
				<td >
				<font face="verdana" size=2><?=date("d-m-Y H:i:s",strtotime($d_email_date))?></font>
				</td>
			</tr>
			<tr>
				<td><font color="#000000" face=verdana size=2>Attachment</font></td>
				<td align=left bgcolor=<?=$rowcol?>>&nbsp;<font face=verdana size=1>
				<?php $ii=0;
					foreach($multi_attach as $attach)
					{ 
							
						if($attach=='../lead_doc/' || $attach=='' || $attach=='../uploaded/') { }else{ 
							$i++;
							if(strpos($attach,"imap/")!==false){ $attach = "../".$attach; }
						?>
				<a href='javascript:void(0)' onClick="JavaScript:window.open('../../../<?=$attach?>','_blank','height=350, width=600,scrollbars=0')" class='cptext'>Attachment <?=$i?>
				</a>&nbsp; &nbsp;&nbsp;
				<?php } 
					}
					?>
				&nbsp;
				</td>
			</tr>
			<tr>
				<td>
				<font color="#000000" face=verdana size=2>Content</font>
				</td>
				<td>
				<textarea name="V_Content" rows="8" cols="40" style="width:100%; height:350px" id="V_Content" ><?=$V_BodyMessage?></textarea>
				</td>
			</tr>
         </form>
      </table>
   </body>
</html>