<?php
include("../../config/web_mysqlconnect.php");

//print_r($_REQUEST);
$queueid=$_REQUEST['queueid'];
$agent=$_REQUEST['agent'];
$caseid=$_REQUEST['caseid'];
$date_today=date("Y-m-d H:i:s");

$msg="";
$pre_case_id=$_REQUEST['pre_case_id'];
$new_caseid=$_REQUEST['new_caseid'];
$remark=$_REQUEST['remark'];
//print_r($_REQUEST);
$sql="select count(*) as c from $db.web_problemdefination  where ticketid='".$new_caseid."' ";
$q=mysqli_query($link,$sql);
$fetch_t=mysqli_fetch_array($q);
$err=0;
if($fetch_t['c']==0 && $new_caseid!=""){

		$msg="Incorrect Case ID!!!!!";$err=1;
}else{
	   $err=0;
}

if(isset($_REQUEST['btnsubmit']))
{
	if($remark=="")
{
	$err=1;
	$msg="Please enter remark";
}
	if($err==0)
	{
		$remark=addslashes($remark)."\n Updated Previous Ticket :[".$pre_case_id ."] TO [".$new_caseid."]";
		$q= "update $dbname.web_email_information set ICASEID='$new_caseid',email_test='clearCaseID' where EMAIL_ID='".$queueid."' ; ";
	 	
	    mysqli_query($link,$q);
	    $ins="INSERT INTO $dbname.web_queue_history(agentName,caseid,queue_id,created_date,remark)VALUES('".$agent."','".$pre_case_id."','".$queueid."','".$date_today."','".$remark."')";
	    mysqli_query($link,$ins);
	    //echo "<br>".$q."<br>".$ins;	
	    $msg="Sucessfully updated";?>
			<script type="text/javascript"> window.parent.location.reload(); 
			window.location='';
		</script>
	   
	     <?
	 }
	
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> Alliance Infotech Private Limited, India </title>
<link rel="stylesheet" type="text/css" href="home_page/css/style.css" />
<link rel="stylesheet" type="text/css" href="templates/css/styles.css" />
</head>
<body >
<form  method="post" name="frmedit" id="quickcreatenc" >
<table width="100%" border="0"  align="left" cellpadding="0" cellspacing="0" class="main-form tableview tableview-2">
<tr class="background">
    <td colspan="2">Edit Case </td>
  </tr>
   <? if(!empty($msg)){?>
  <tr id="display-success">
    <td height="20" colspan="2" align="center" style="text-align:center; text-transform:capitalize;">
       <?=$msg?>
       <script language="javascript" type="text/javascript">
	  /* function refreshParent(){
			   window.parent.location.reload();
		}setInterval ( "refreshParent()", 2000 );*/
	 </script> 
       </td>
  </tr>
  <? }?>
  
	
 
    <tr>
  <td>Case ID </td>
	<td><input type="text" name="new_caseid" id="new_caseid" class="input-style1" size="40"  />
    <input type="hidden" name="queueid" value="<?=$_REQUEST['queueid']?>" />
    <input type="hidden" name="agent" value="<?=$_REQUEST['agent']?>" />
    <input type="hidden" name="pre_case_id" value="<?=$_REQUEST['caseid']?>" />


</td>
</tr>

<tr><td>&nbsp;</td></tr>
 
<tr>
  <td>Remark<em>*</em></font>&nbsp;&nbsp;</b></td>
	<td><textarea name="remark" id="remark" class="text-area1" rows="5" cols="23" ></textarea><br />

</td>
</tr>
 </table>
 <div class="button-all2" style="clear:both;">
	<input type="submit" onClick="" value="Submit"  name="btnsubmit" class="button-orange1" style="float:inherit;"> 
    </div>
</form>
</body>
</html>