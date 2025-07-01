<? error_reporting(0);
/**/
                   

function add_audit_log($user_id, $actionlog, $ticketid, $comments, $db, $types='')
{
   global $link;
   $created_on=date("Y-m-d H:i:s");
   $ip = getenv('REMOTE_ADDR');
    $sql_ins1="INSERT INTO $db.web_audit_history(user_id, action, created_on, ticket_id, comments, ip_address, case_process_type)
   VALUES('$user_id', '$actionlog', NOW(), '$ticketid', '$comments' , '$ip', '$types')";
   mysqli_query($link,$sql_ins1)or die(mysqli_error().'Err web_audit_history');
}

function assignto($assignto)
{
 global $db,$link;
   $res=mysqli_fetch_array(mysqli_query($link,"select AtxUserName from $db.uniuserprofile where AtxUserID='$assignto' ; "));
   return $res['AtxUserName'];
}

function assignfor($assignfor)
{
 global $db,$link;
   $res=mysqli_fetch_array(mysqli_query($link,"select assignfor from $db.web_assignfor where id='$assignfor' ; "));
   return $res['assignfor'];
}

function project($projectid)
{
 global $db,$link;
   $res=mysqli_fetch_array(mysqli_query($link,"select vProjectName from $db. web_projects where pId='$projectid' ; "));
   return $res['vProjectName'];
}


function insert_record($table_name,$field_name,$field_value,$database_name) 
{
   global $link;
   mysqli_select_db($database_name);
   $sql_prjname="insert into $table_name $field_name values ('$field_value')";
   $res_prjname=mysqli_query($link,$sql_prjname);
   $row_prjname=mysqli_fetch_array($res_prjname);
   $record_name=$row_prjname[$field_name];
   return($record_name);
}
function fetch_record($table_name,$field_name,$id_field,$id_value) 
{
   global $link;
   $sql_prjname="select $field_name from $table_name where $id_field='$id_value'";
   $res_prjname=mysqli_query($link,$sql_prjname);
   $row_prjname=mysqli_fetch_array($res_prjname);
   $record_name=$row_prjname[$field_name];
   return($record_name);
}
function maximum_id($table_name,$field_name)
{
   global $link;
$sql_id = "select max($field_name) as max_id from $table_name";
$Fetch_Id=mysqli_query($link,$sql_id);
if($row=mysqli_fetch_array($Fetch_Id))
{
return $max_id=$row['max_id']+1;
}
else
{
return $max_id=1;
}
}
function newmaxid()
{
   global $link;
$sql_custid = "select max(I_CompanyID) as maxcid from tbl_mst_company";
$Fetch_CompanyId=mysqli_query($link,$sql_custid);
$row=mysqli_fetch_array($Fetch_CompanyId);
return $CompanyId=$row['maxcid']+1;
}
function newmaxbranchid()
{
   global $link;
$sql_branchid = "select max(Branch_Id) as maxbranchid from tbl_mst_branch";
$Fetch_BranchId=mysqli_query($link,$sql_branchid);
$row=mysqli_fetch_array($Fetch_BranchId);
return $BranchId=$row['maxbranchid']+1;
}
function newmaxuserid($dbname)
{
   global $link;
$sql_userid = "select max(I_UserID) as maxuserid from $dbname.tbl_mst_user_company  order by I_UserID asc ";
//print "sql=".$sql_userid;
$res_userid=mysqli_query($link,$sql_userid);
$row_user=mysqli_fetch_array($res_userid);
$v_userid=$row_user['maxuserid']+1;
return $v_userid;
}

function mailcompose($to,$subject,$body,$headers,$from)
{
   $headers= "MIME-Version: 1.0\r\n";
   $headers.= "Content-type: text/html; charset=iso-8859-1\r\n";
   $headers.= "X-Priority: 1\r\n";
   $headers.= "X-MSMail-Priority: High\r\n";
   $headers.="From: ".$from."\r\n";
   mail($to,$subject,$body, $headers,$from);
}

function getregion_display($region)
{
global $db,$link;
$sql1="select Region_Name from $db.tbl_mst_region  where Region_ID='$region' and I_Status='1'";
//print $sql1;
$res1=mysqli_query($link,$sql1);
$row1 = mysqli_fetch_array($res1);
$regionname = $row1['Region_Name'];
return $regionname;
}
   
/************FUNCTION TO GET THE COMPANYID OF USERID************************/
function company_id($userid)
{
global $link;
$sql1="select I_CompanyID from tbl_mst_user_company  where I_UserID='$userid'";
//print $sql1;
$res1=mysqli_query($link,$sql1);
$row1 = mysqli_fetch_array($res1);
$company_id = $row1['I_CompanyID'];
return $company_id;
}
/**************END OF THE FUNCTION************************/

############## CODE TO FIND THE COMPANY NAME  ################33
   ############### AUTHOR :: SUSHILA
function company_name($company_id)
{  
   global $link;
   $sql_company="select V_CompanyName from tbl_mst_company  where I_CompanyID='$company_id'";
   //print $sql_company;
   $res_company=mysqli_query($link,$sql_company)or (die(mysqli_connect()));
   $row_company=mysqli_fetch_array($res_company);
   $company_name=$row_company['V_CompanyName'];
   return $company_name;
}  
   ################### END OF CODE #############################3

/************FUNCTION TO GET THE DATABASE OF PARTICULAR COMPANY ID************************/
function database_name($company_id)
{
global $link;
$sql_database="select a.V_Company_DBName,a.I_CompanyID,b.I_CompanyID  from tbl_mst_company a,tbl_mst_user_company b where (a.I_CompanyID=b.I_CompanyID) and a.I_CompanyID='$company_id'";

$res_database=mysqli_query($link,$sql_database);
if ($res_database == FALSE)
{
   echo "Error in query, errorcode:".mysqli_error();
   exit();
}
else
{
   $row_database=mysqli_fetch_array($res_database);
   $database_name=$row_database['V_Company_DBName'];
   $database_name=strtolower($database_name);
}
return $database_name;
}
/**************END OF THE FUNCTION************************/

function failed_login($table_name,$usname,$reason_failure,$database_name)
{
global $link;
   mysqli_select_db($database_name);
   $modificationtime    =  date("Y-m-d G:i:s");
   $ip =getenv('REMOTE_ADDR');
  
   $id=maximum_id($table_name,'SNo');
   if(empty($reason_failure)){ $reason_failure="login"; }
   $sql="insert into $table_name(SNo,IP,AccessedAt,UserName,Reason) values('$id','$ip','$modificationtime','$usname','$reason_failure')";

   $res=mysqli_query($link,$sql) or die(mysqli_error("error")) ;
}



//mysqli_close($link);

function date_difff($llogin,$clogin)
{
	$llogin=explode("-",$llogin);
   	$clogin=explode("-",$clogin);
   	$cloginstr=mktime(0,0,0,$clogin[1],$clogin[2],$clogin[0]);
   	$lloginstr=mktime(0,0,0,$llogin[1],$llogin[2],$llogin[0]);
   	$diffseconds = $cloginstr-$lloginstr;
   	$days=$diffseconds/(60*60*24);
   	return ceil($days);
}

/*  comment by sush due to dupliacay of function name as already exists in function_doc.php 
function StoreDateIntoDataBase($date0)
{
   $date1=explode("-",$date0);
   $date2=$date1[2]."-".$date1[1]."-".$date1[0];
   return $date2;

}*/

function F_Count_User($companyid,$dbname,$db,$usertype)
{
   global $link;
$sql_user_ID = "SELECT count(u.AtxDesignation) as ID FROM  $dbname.tbl_mst_user_company as tmuc, $db.uniuserprofile as u where 
tmuc.V_EmailID = u.AtxEmail AND u.AtxDesignation = '$usertype' AND tmuc.I_CompanyID='$companyid' AND u.AtxUserStatus = '1' ";
$Fetch_USERID=mysqli_query($link,$sql_user_ID);
$row=mysqli_fetch_array($Fetch_USERID);
return $USERID=$row['ID'];
}

function F_Count_Admin_User($companyid,$dbname,$db)
{
   global $link;
$sql_user_ID = "SELECT count(u.AtxDesignation) as ID FROM  $dbname.tbl_mst_user_company as tmuc, $db.uniuserprofile as u where 
tmuc.V_EmailID = u.AtxEmail AND u.AtxDesignation IN ('CRM Admin') AND tmuc.I_CompanyID='$companyid' AND u.AtxUserStatus = '1' ";
$Fetch_USERID=mysqli_query($link,$sql_user_ID);
$row=mysqli_fetch_array($Fetch_USERID);
return $USERID=$row['ID'];
}

function F_Count_Agent_User($companyid,$dbname,$db)
{
   global $link;
$sql_user_ID = "SELECT count(u.AtxDesignation) as ID FROM  $dbname.tbl_mst_user_company as tmuc, $db.uniuserprofile as u where 
tmuc.V_EmailID = u.AtxEmail AND u.AtxDesignation IN ('Agent') AND tmuc.I_CompanyID='$companyid' AND u.AtxUserStatus = '1' ";
$Fetch_USERID=mysqli_query($link,$sql_user_ID);
$row=mysqli_fetch_array($Fetch_USERID);
return $USERID=$row['ID'];
}

function F_Count_UserID($companyid,$dbname,$db)
{
   global $link;
//$sql_user_ID = "SELECT count(u.AtxDesignation) as ID FROM  $dbname.tbl_mst_user_company as tmuc, $db.uniuserprofile as u where tmuc.V_EmailID = u.AtxEmail AND u.AtxDesignation IN ('EWSA Admin','Administrator','Agent') AND tmuc.I_CompanyID='$companyid' AND u.AtxUserStatus = '1' ";
$sql_user_ID = "SELECT count(u.AtxDesignation) as ID FROM  $dbname.tbl_mst_user_company as tmuc, $db.uniuserprofile as u where tmuc.V_EmailID = u.AtxEmail AND u.AtxDesignation IN ('CRM Admin','Agent') AND tmuc.I_CompanyID='$companyid' AND u.AtxUserStatus = '1' ";
$Fetch_USERID=mysqli_query($link,$sql_user_ID);
$row=mysqli_fetch_array($Fetch_USERID);
return $USERID=$row['ID'];
}

function F_CompanyUserLicense($companyid,$dbname,$db)
{
   global $link;
   $sql_user_lience="select I_NUSERS from $dbname.tb_mst_user_licence where I_organistionID='$companyid' ";
   //$sql_user_lience="SELECT u.AtxDesignation, tmul.I_NUSERS FROM $dbname.tb_mst_user_licence as tmul, $dbname.tbl_mst_user_company as tmuc, $db.uniuserprofile as u where tmuc.V_EmailID = u.AtxEmail AND tmul.I_organistionID=tmuc.I_CompanyID AND u.AtxDesignation IN ('Team Lead','Administrator','Agent') AND tmul.I_organistionID='$companyid' ";
   $res_user_lience=mysqli_query($link,$sql_user_lience) or die(mysqli_error());
   while ($fetch_result=mysqli_fetch_array($res_user_lience)) {

      $I_NUSERS=$fetch_result['I_NUSERS'];
   }
   
   return $I_NUSERS;
}


function totaluseravialable()
{
   global $link;
$companyid  =     $_SESSION['companyid'];
$sql_total_user = "select count(I_CompanyID) as countcid from tbl_mst_user_company where I_UserStatus=1 and I_CompanyID='$companyid'";
$Fetch_CompanyId=mysqli_query($link,$sql_total_user);
$row=mysqli_fetch_array($Fetch_CompanyId);
$I_CompanyId1=$row['countcid'];
return $I_CompanyId1;

}


##### function to check if the ip address is valid or not ############
function ip_check($val,$current_ip,$ip_address,$userid,$company_id,$usname,$database_name)
{
 if (($current_ip==$ip_address) ||($ip_address=='')) ##check for ip address
 {
   if($val==1)
   {
      //header('Location:check_database.php?userid='.$userid);
      //echo "<script>document.location.href='check_database.php?userid=".$userid."'</script>";
      ?>
      <form name="frm" id="frm" method="post" action="check_database.php">
      <input type="hidden" name="userid" id="userid"  value='<?=$userid?>'> 
      <input type="hidden" name="company_id" id="company_id"  value='<?=$company_id?>'>
      <input type="hidden" name="check" id="check" value='1'> 
      </form>
      <script language="javascript">document.frm.submit();</script>
      <? 
   }
   else if($val==2)
   {
      //echo $company_id;
      //exit();
      header("Location:changepass.php?userid=".$userid."&comp=".$company_id);
   }
  }
 else
 {
       $reason_failure="Invalid IP Address";
      $insert_query=failed_login('failed_login',$usname,$reason_failure,$database_name);
      header("Location:stafflogin.php?ip_invalid=1&userid=$userid");
      
      
  }
            
}

## essilor 

##### function to check if the ip address is valid or not ############
function web_ip_check($val,$current_ip,$ip_address,$userid,$company_id,$usname,$database_name)
{
   // print_r($current_ip); echo "<br/>";
   // print_r($ip_address); die;

   // if (($current_ip==$ip_address) ||($ip_address=='')) ##check for ip address
 // {
   if($val==1)
   {
      //header('Location:check_database.php?userid='.$userid);
      //echo "<script>document.location.href='check_database.php?userid=".$userid."'</script>";
      ?>
      <form name="frm" id="frm" method="post" action="web_check_database.php">
      <input type="hidden" name="userid" id="userid"  value='<?=$userid?>'> 
      <input type="hidden" name="company_id" id="company_id"  value='<?=$company_id?>'>
      <input type="hidden" name="check" id="check" value='1'> 
      </form>
      <script language="javascript">document.frm.submit();</script>
      <? 
   }
   else if($val==2)
   {
      //echo $company_id;
      //exit();
      header("Location:web_changepass.php?userid=".$userid."&comp=".$company_id);
   }
 //  }
 // else
 // {
 //       $reason_failure="Invalid IP Address";
 //      $insert_query=failed_login('failed_login',$usname,$reason_failure,$database_name);
 //      header("Location:stafflogin.php?ip_invalid=1&userid=$userid");
      
      
 //  }
            
}


#====================================================================================================
#  Function Name     :  fillArrayCombo
#----------------------------------------------------------------------------------------------------
function fillArrayCombo($arrName, $selected='')
{
   $strHTML = "";
   reset($arrName);
    while(list($key,$val) = each($arrName))
    {
      $strHTML .= "<option value=\"". $key. "\"";
      if($selected == $key)
         $strHTML .= " selected ";
      $strHTML .= ">".$val. "</option>";
    }
   return $strHTML;
}
#====================================================================================================
#  Function Name     :  fillDbCombo
#----------------------------------------------------------------------------------------------------
function fillDbCombo($arrName, $key, $val, $selected='')
{
   global $db2;
   $strHTML = "";
   $i = 0;
    while($i < $db2->num_rows())
    {
      $db2->next_record();
         $strHTML .= "<option value=\"". $db2->f($key). "\"";
      if($selected == $db2->f($key))
         $strHTML .= " selected ";
      if(is_array($val))
         $strHTML .= ">".$db2->f($val[0])." ".$db2->f($val[1])." </option>";
      
      $strHTML .= ">".$db2->f($val). "</option>";
      $i++;
   }
   $db2->free();
   //echo $strHTML;
   return $strHTML;
}

function fillDbMultiple($arrName, $key, $val)
{
   global $db,$link;
   $strHTML = "";
   $i = 0;
   //echo $db->num_rows();
    while($i < $db->num_rows())
    {
      $db->next_record();
      $strHTML .= "<option value=\"". $db->f($key). "\"";
      if(in_array($db->f($key),$arrName)){
         $strHTML .= " selected ";
      }
      
      $strHTML .= ">".$db->f($val). "</option>";
      $i++;
   }
   $db->free();
   //echo $strHTML;
   return $strHTML;
}

###################################  neeti ##########################################################

function getStatus($selectedvalue,$nval)
{
   global $db,$link;
   
   if($nval=='dropdown')
   {
   $q="select I_CategoryID , V_CategoryName from $db.tbl_mst_taskstatus where I_CategoryStatus='1'";
   $res=mysqli_query($link,$q);
   $output="";
      while($rs=mysqli_fetch_array($res))
      {
         $catid=$rs['I_CategoryID'];
         $catname=$rs['V_CategoryName'];
         
         $output .="<option value='".$catid."' ";
         if($selectedvalue==$catid)
         {
            $output .=" selected";
         }
         $output .=">".$catname."</option>";
      }
   }
   else
   {
   $q="select V_CategoryName from $db.tbl_mst_taskstatus where I_CategoryStatus='1' and I_CategoryID='$selectedvalue'";
   $res=mysqli_query($link,$q);
   $rs=mysqli_fetch_array($res);
   $output=$rs['V_CategoryName'];
   }
   
   return $output;
}

function getonlyAgent($selectedvalue,$nval)
{
   global $db,$link;
   $type=$_SESSION['user_group'];
   if(($_SESSION['user_group']=='0000') || ($_SESSION['user_group']=='080000'))
      {
         $reportedby="";
         //echo "ADMIN USER";
      }
      elseif($_SESSION['user_group']=='070000')
      {
         $reportedby="";
         //echo "AGENT USER";
      }
      else
      {
         $allusers = allsubordinates($_SESSION['userid']);
         //print_r($allusers);
         $result1 = array_unique($allusers);
         $result = array_values(array_diff($result1, array($_SESSION['userid'])));
         $bosss=implode(",",$result);
         //print_r($result);
         //$reportedby='and (problemdefination.ReportedBy='."'".$_SESSION['userid']."'".'|| problemdefination.i_TechStaffId='."'".$_SESSION['userid']."')";
         //echo "OTHER USER";
         $reportedby=" AND AtxUserID IN (".$bosss.") ";
      }
   
   //$q="select AtxUserID , AtxUserName from $db.uniuserprofile where AtxUserStatus='1' AND AtxUserID != '1' $reportedby ";
   $q= "SELECT * FROM $db.uniuserprofile as u , $db.unigroupdetails as ud WHERE u.AtxUserID = ud.ugdContactID"
         ." AND u.AtxUserStatus='1' AND atxGid IN ($type) $reportedby ORDER BY AtxDisplayName ASC";
   $res=mysqli_query($link,$q);
   $output="";
   if($nval=='dropdown')
   {
      while($rs=mysqli_fetch_array($res))
      {
         $AtxUserID=$rs['AtxUserID'];
         $AtxUserName=$rs['AtxUserName'];
         
         $output .="<option value='".$AtxUserID."' ";
         if($selectedvalue==$AtxUserID)
         {
            $output .=" selected";
         }
         $output .=">".$AtxUserName."</option>";
      }
   }
   
   return $output;
}

function getAgent($selectedvalue,$nval)
{
   global $db,$link;
   
   if(($_SESSION['user_group']=='0000') || ($_SESSION['user_group']=='080000'))
      {
         $reportedby="";
         //echo "ADMIN USER";
      }
      elseif($_SESSION['user_group']=='070000')
      {
         $reportedby="";
         //echo "AGENT USER";
      }
      else
      {
         $allusers = allsubordinates($_SESSION['userid']);
         //print_r($allusers);
         $result1 = array_unique($allusers);
         $result = array_values(array_diff($result1, array($_SESSION['userid'])));
         $bosss=implode(",",$result);
         //print_r($result);
         //$reportedby='and (problemdefination.ReportedBy='."'".$_SESSION['userid']."'".'|| problemdefination.i_TechStaffId='."'".$_SESSION['userid']."')";
         //echo "OTHER USER";
         $reportedby=" AND AtxUserID IN (".$bosss.") ";
      }
      
   $q="select AtxUserID , AtxUserName from $db.uniuserprofile where AtxUserStatus='1' AND AtxUserID != '1' $reportedby";
   $res=mysqli_query($link,$q);
   $output="";
   if($nval=='dropdown')
   {
      while($rs=mysqli_fetch_array($res))
      {
         $AtxUserID=$rs['AtxUserID'];
         $AtxUserName=$rs['AtxUserName'];
         
         $output .="<option value='".$AtxUserID."' ";
         if($selectedvalue==$AtxUserID)
         {
            $output .=" selected";
         }
         $output .=">".$AtxUserName."</option>";
      }
   }
   
   return $output;
}

function getEwsaAgent($selectedvalue,$nval)
{
   global $db,$link;
   $q="select u.AtxUserID,u.AtxUserName from uniuserprofile u , unigroupdetails uu where u.AtxUserID=uu.ugdContactID and uu.atxGid='060000' and u.AtxUserStatus='1'";
   $res=mysqli_query($link,$q) or die(mysqli_error());
   $output="";
   if($nval=='dropdown')
   {
      while($rs=mysqli_fetch_array($res))
      {
         $AtxUserID=$rs['AtxUserID'];
         $AtxUserName=$rs['AtxUserName'];
         
         $sqlpecom="select count(IID) as pecount FROM problemdefination WHERE DStatus='1' and ComplainHandled='0' AND i_TechStaffId = '$AtxUserID'";
         $respecom=mysqli_query($link,$sqlpecom)or die(mysqli_error());
         $rspecom=mysqli_fetch_array($respecom);
         $pecount=$rspecom['pecount'];
         
         
         $output .="<option value='".$AtxUserID."' ";
         if($selectedvalue==$AtxUserID)
         {
            $output .=" selected";
         }
         $output .=">".$AtxUserName."(".$pecount.")</option>";
      }
   }
   
   return $output;
}

function getAllContactInfo($accno)
{
   global $db,$link;
   $q="select * from $db.accounts where AccountNumber='$accno'";
   $res=mysqli_query($link,$q);
   $result=mysqli_fetch_array($res);
   return $result;
}

function getAllContactone($accno,$val)
{
   global $db,$link;
   $q="select $val from $db.crmcontactid where AccountID='$accno'";
   $res=mysqli_query($link,$q);
   $result=mysqli_fetch_array($res);
   return $result[$val];
}

function getproblemOfCust($id,$val)
{
   global $db,$link;
   $q="select $val from $db.problemdefination where IID='$id' and DStatus='1' and ComplainHandled='0'";
   $res=mysqli_query($link,$q);
   $rs=mysqli_fetch_array($res);
   $output=$rs[$val];
   return $output;
}


function getServiceName($id)
{
   global $db,$link;
   $q="select V_ServiceName from $db.tbl_mst_services where I_ServiceID='$id'";
   $res=mysqli_query($link,$q);
   $rs=mysqli_fetch_array($res);
   $output=$rs['V_ServiceName'];
   return $output;
}

function getServiceCat($id,$val,$tid)
{
   global $db,$link;
   $q="select $val from $db.problemdefination where DStatus='1' and ProjectID='$id' AND IID = '$tid'";
   $res=mysqli_query($link,$q);
   $rs=mysqli_fetch_array($res);
   $out=$rs[$val];
   
   if($val=='ProblemType')
   {
   $qq="select V_CategoryName from $db.tbl_mst_category where I_CategoryID='$out'";
   $resq=mysqli_query($link,$qq);
   $rsq=mysqli_fetch_array($resq);
   $output=$rsq['V_CategoryName'];
   }
   else if($val=='SubProblemType')
   {
   $qq="select V_SubCategoryName from $db.tbl_mst_subcategory where I_SubCategoryID='$out'";
   $resq=mysqli_query($link,$qq);
   $rsq=mysqli_fetch_array($resq);
   $output=$rsq['V_SubCategoryName'];
   }
   /*else if($val=='ProblemDescription')
   {
   $qq="select V_SubCategoryName from $db.tbl_mst_subcategory where I_SubCategoryID='$out'";
   $resq=mysqli_query($link,$qq);
   $rsq=mysqli_fetch_array($resq);
   $output=$rsq['V_SubCategoryName'];
   }*/
   
   return $output;
}

function getService($selectedvalue,$nval)
{
   global $db,$link;
   $q="select I_ServiceID,V_ServiceName  from $db.tbl_mst_services where DStatus='1'";
   $res=mysqli_query($link,$q);
   $output="";
   if($nval=='dropdown')
   {
      while($rs=mysqli_fetch_array($res))
      {
         $I_ServiceID=$rs['I_ServiceID'];
         $V_ServiceName=$rs['V_ServiceName'];
         
         $output .="<option value='".$I_ServiceID."' ";
         if($selectedvalue==$I_ServiceID)
         {
            $output .=" selected";
         }
         $output .=">".$V_ServiceName."</option>";
      }
   }
   
   return $output;
}


###############################    Get the value of drop down Category  #####################

function getCategory_dk($selectedvalue,$nval)
{
   global $db,$link;
   
   $serviceid=$_SESSION['tk_service'];
   
   $q="select c.I_CategoryID,c.V_CategoryName  from $db.tbl_mst_category c , $db.tbl_assoc_subcategory_category s 
where c.I_CategoryID=s.I_CategoryID and c.I_CategoryStatus='1' and s.I_ServiceID='$serviceid' group by c.I_CategoryID ";
   $res=mysqli_query($link,$q);
   $output="";
   if($nval=='dropdown')
   {
      while($rs=mysqli_fetch_array($res))
      {
         $ICategoryID=$rs['I_CategoryID'];
         $VCategoryName=$rs['V_CategoryName'];
         
         $output .="<option value='".$ICategoryID."' ";
         if($selectedvalue==$ICategoryID)
         {
            $output .=" selected";
         }
         $output .=">".$VCategoryName."</option>";
      }
   }
   
   return $output;
}






###############################    Get the value of drop down  Sub Category  #####################


function getCategory_dk1($selectedvalue,$nval)
{
   global $db,$link;
   
   $serviceid=$_SESSION['tk_service'];
   $categoryid=$_SESSION['tk_cat'];
   
   $q="select c.I_SubCategoryID,c.V_SubCategoryName,s.*  from $db.tbl_mst_subcategory c , $db.tbl_assoc_subcategory_category s
where c.I_SubCategoryID=s.I_SubCategoryID and c.I_SubCategoryStatus='1' and s.I_ServiceID='$serviceid' and s.I_CategoryID='$categoryid'";

   $res=mysqli_query($link,$q);
   $output="";
   if($nval=='dropdown')
   {
      while($rs=mysqli_fetch_array($res))
      {
         $ISubCategoryID=$rs['I_SubCategoryID'];
         $VSubCategoryName=$rs['V_SubCategoryName'];
         
         $output .="<option value='".$ISubCategoryID."' ";
         if($selectedvalue==$ISubCategoryID)
         {
            $output .=" selected";
         }
         $output .=">".$VSubCategoryName."</option>";
      }
   }
   
   return $output;
}


####################################
function getTicketID($id)
{
   global $db,$link;
   $q="select IID from $db.problemdefination where i_wrapcallid='$id'";
   $res=mysqli_query($link,$q);
   $rs=mysqli_fetch_array($res);
   $output=$rs['IID'];
   return $output;
}

function getCustomerName($id)
{
   global $db,$link;
   $q="select AccountName from $db.accounts where AccountNumber='$id'";
   $res=mysqli_query($link,$q);
   $rs=mysqli_fetch_array($res);
   $output=$rs['AccountName'];
   return $output;
}

function getUserID($name)
{
   global $db,$link;
   $q="select AtxUserID from $db.uniuserprofile where AtxUserName='$name'";
   $res=mysqli_query($link,$q);
   $rs=mysqli_fetch_array($res);
   $output=$rs['AtxUserID'];
   return $output;
}

function getCategory($id)
{
   global $db,$link;
   $q="select V_CategoryName from $db.tbl_mst_category where I_CategoryID='$id'";
   $res=mysqli_query($link,$q);
   $rs=mysqli_fetch_array($res);
   $output=$rs['V_CategoryName'];
   return $output;
}

function getSubCategory($id)
{
   global $db,$link;
   $q="select V_SubCategoryName from $db.tbl_mst_subcategory where I_SubCategoryID='$id'";
   $res=mysqli_query($link,$q);
   $rs=mysqli_fetch_array($res);
   $output=$rs['V_SubCategoryName'];
   return $output;
}

function getTypeOfCall($id)
{
   global $db,$link;
   $q="select V_CALL_STATUS from $db.tbl_mst_call_status where I_CALL_STATUS='$id'";
   $res=mysqli_query($link,$q);
   $rs=mysqli_fetch_array($res);
   $output=$rs['V_CALL_STATUS'];
   return $output;
}

function GetTaskType_Name($id)
{
   global $db,$link; 

   $q="select V_CategoryName from $db.tbl_mst_tasktype where I_CategoryID='$id'";
   $res=mysqli_query($link,$q);
   $rs=mysqli_fetch_array($res);
   $output=$rs['V_CategoryName'];
   return $output;
}

function getUserName($id)
{
   global $db,$link;
   $q="select AtxUserName from $db.uniuserprofile where AtxUserID='$id'";
   $res=mysqli_query($link,$q);
   $rs=mysqli_fetch_array($res);
   $output=$rs['AtxUserName'];
   return $output;
}

function getSRrecord($var,$count)
{
   global $db,$link;
   
   $arr=explode(",",$var);
   $status=$arr[0];
   $agent=$arr[1];
   $service=$arr[2];
   $cat=$arr[6];
   $subcat=$arr[7];
      
   $out="";
   
   if($status!=''){ $out .=" and I_SRStatus='$status' "; }
   if($agent!=''){ $out .=" and "; }
   if($service!=''){ $out .=" and I_ServiceID='$service' "; }
   if($cat!=''){ $out .=" and I_CategoryID='$cat' "; }
   if($subcat!=''){ $out .=" and I_SubCategoryID='$subcat' "; }
   
   if($arr[3]!="" || !empty($arr[3]))
   {
      $from=date('Y-m-d',strtotime(getFromDate('from',$arr[3])));
      $to=date('Y-m-d',strtotime(getFromDate('to',$arr[3])));
      
       $out .=" and TimeStamp>='$from 00:00:00' and TimeStamp<='$to 23:59:59'  "; 
   }
   else if($arr[4]!='' && $arr[5]!='')
   {
      $from=date('Y-m-d',strtotime($arr[4]));
      $to=date('Y-m-d',strtotime($arr[5]));
      
      $out .=" and TimeStamp>='$from 00:00:00' and TimeStamp<='$to 23:59:59'  "; 
   }
   
   
      
   //$q="select * from $db.tbl_cust_wrapcalldetails where I_WRAPCALLSTATUS='1' $out";
   
   //p.IID,p.ProjectID, p.Subject, p.ComplainHandled, p.ReportedDate, p.ProblemType, p.AssignedTo, p.ProblemDescription,
   $q="SELECT a.BillingCity, a.Phone, a.AccountName, A.resion, w.* FROM $db.tbl_cust_wrapcalldetails w LEFT JOIN $db.accounts a ON a.AccountNumber = w.V_CustId WHERE w.I_WRAPCALLSTATUS='1' $out";
   
   $ress=mysqli_query($link,$q);
   $num=mysqli_num_rows($ress);
   
   $_SESSION['total_record'] = $num ;
   
   if($_SESSION['page_size'] >= $_SESSION['total_record'])
   {
      $_SESSION['start_record'] = 0;
   }
   
   $sql= "SELECT a.BillingCity, a.Phone, a.AccountName, A.resion, w.* FROM $db.tbl_cust_wrapcalldetails w LEFT JOIN $db.accounts a ON a.AccountNumber = w.V_CustId WHERE w.I_WRAPCALLSTATUS='1' $out ORDER BY I_WrapID DESC "
   ." LIMIT ". $_SESSION['start_record']. ", ". $_SESSION['page_size'];
   $res=mysqli_query($link,$sql);
   
   $output="";
   if($count=='count')
   {
      return $output=$num;
   }
   else
   {
   while($rs=mysqli_fetch_array($res))
   {
      
   $ticketid=$rs['I_WrapID'];
   //$name=getCustomerName($rs['V_CustId']);
   $name=$rs['AccountName'];        // Customer Name
   $BillingCity=$rs['BillingCity']; // Location 
   $Phone=$rs['Phone'];          // Mobile No.
   $resion=getregion_display($rs['resion']);          // Branch
   $subject=$rs['V_Subject'];
   $V_Remarks=$rs['V_Remarks'];
   $Interaction_Remarks=$rs['Interaction_Remarks'];
   $category=getCategory($rs['I_CategoryID']);
   $subcategory=getSubCategory($rs['I_SubCategoryID']);
   $status=getStatus($rs['I_SRStatus'],'');
   
   
   if(!empty($rs['TimeStamp'])){
   $opendatetime=date('d-m-Y h:i:s',strtotime($rs['TimeStamp']));
   }else{ $opendatetime='';}
   
   if(!empty($rs['D_OpenDate'])){
   $opendate=date('d-m-Y',strtotime($rs['D_OpenDate']));
   }else{ $opendate='';}
   
   if(!empty($rs['D_CloseDate'])){
   $closedate=date('d-m-Y',strtotime($rs['D_CloseDate']));
   }else{ $closedate='';}
   
   $type=GetTaskType_Name($rs['I_TypeOfCall']);
   $assignedtoagent=getUserName($rs['V_SRAssignedTo']);
   $AssignedTo =  $rs['AssignedTo'];
   
   $output .='<tr >
        <td align="center"><a href="servicesrequest.php?I_WrapID='.$ticketid.'&Action=View">'.$ticketid.'</a></td>
        <td align="center">'.$name.'</td>
        <td align="center">'.$category.'</td>
        <td align="center">'.$subcategory.'</td>
        
        <td align="center">'.$Phone.'</td>
        <td align="center">'.$subject.'</td>
        <td align="center">'.$V_Remarks.'</td>
        <td align="center">'.$assignedtoagent.'</td>
        <td align="center">'.$opendatetime.'</td>
      </tr>';
      
      /*<!--
        <td align="center">'.$BillingCity.'</td>
        <td align="center">'.$AssignedTo.'</td>
        <td align="center">'.$resion.'</td>
        
        
        <td align="center">'.$status.'</td>-->
        
        <!--<td align="center">'.$closedate.'</td>
        <td align="center">'.$type.'</td>-->
      */
   }
   if($num<=0){ $output='<tr ><td colspan="10" align="center">No record found !!</td></tr>'; }
   }
      
   return $output;
}


function getProblemTicket($numofrecords)
{
   global $db,$link;
   
   $sql= "select p.IID,p.ComplainDate from $db.problemdefination p where p.DStatus='1' and (p.ComplainHandled='0' or p.ComplainHandled='2') ";
   $res=mysqli_query($link,$sql);
   $numm=mysqli_num_rows($res);
   
   $sqll= $sql." order by p.IID desc limit 0,$numofrecords ";
   $resl=mysqli_query($link,$sqll);
   $num=mysqli_num_rows($resl);
   
   $output="";
   
   while($rs=mysqli_fetch_array($resl))
   {
   $ticketid=$rs['IID'];
   $date=($rs['ComplainDate']=="0000-00-00") ? "" : date('d-m-Y',strtotime($rs['ComplainDate']));
      
   $type=getTypeOfCall($rs['I_TypeOfCall']);
   $assignedto=getUserName($rs['V_SRAssignedTo']);
   
   $output .='<tr >
        <td align="center"><a href="admin-Appointment2.php?tid='.$ticketid.'">'.$ticketid.'</a></td>
        <td align="center">'.$date.'</td>
       </tr>';
   
   }
   if($num<=0){ $output='<tr ><td colspan="9" align="center">No record found !!</td></tr>'; } else { 
   
   if($num<$numm){
      
   $output .='<tr >
        <td align="right" colspan="2">
        <form name="form1" method="post">
         <input type="hidden" name="count" id="count" value="'.$numofrecords.'">
        <a href="javascript:void(0)" onclick="submitThis()">view more..</a>
        </form>
        </td></tr>'; 
        }
   }
      
   return $output;
}

function getLog($var,$count)
{
   global $db,$link;
   
   $arr=explode(",",$var);
   $agent=$arr[0];
         
   $out="";
   
   if($agent!=''){ $agentid=getUserName($agent); $out .=" and UserName='$agentid' "; }
   
   if($arr[1]!="" || !empty($arr[1]))
   {
      $from=date('Y-m-d',strtotime(getFromDate('from',$arr[1])));
      $to=date('Y-m-d',strtotime(getFromDate('to',$arr[1])));
      
       $out .=" and AccessedAt>='$from 00:00:00' and AccessedAt<='$to 23:59:59'  "; 
   }
   else if($arr[2]!='' && $arr[3]!='')
   {
      $from=date('Y-m-d',strtotime($arr[2]));
      $to=date('Y-m-d',strtotime($arr[3]));
      
      $out .=" and AccessedAt>='$from 00:00:00' and AccessedAt<='$to 23:59:59'  "; 
   }
   else
   {
      if($arr[2]!='')
      {
         $from=date('Y-m-d',strtotime($arr[2]));
         
         $out .=" and AccessedAt>='$from 00:00:00'  "; 
      }
      else if($arr[3]!='')
      {
         $to=date('Y-m-d',strtotime($arr[3]));
         
         $out .=" and AccessedAt<='$to 23:59:59'  "; 
      }
   }
   
   
   $q="select * from $db.logip where UserName!='' $out";
   $ress=mysqli_query($link,$q);
   $num=mysqli_num_rows($ress);
   
   $_SESSION['total_record'] = $num ;
   
   if($_SESSION['page_size'] >= $_SESSION['total_record'])
   {
      $_SESSION['start_record'] = 0;
   }
   
   $sql= "select * from $db.logip where UserName!='' $out"
   ." LIMIT ". $_SESSION['start_record']. ", ". $_SESSION['page_size'];
   $res=mysqli_query($link,$sql);
   
   $output="";
   if($count=='count')
   {
      return $output=$num;
   }
   else
   {
   while($rs=mysqli_fetch_array($res))
   {
      
   $login=$rs['AccessedAt'];
   $logout=$rs['TimePeriod'];
   $UserName=$rs['UserName'];
   $ip=$rs['IP'];
   
   $userid=getUserID($UserName); 
   /*<a href="userdetailview.php?id='.$userid.'">'.$UserName.'</a>*/
   $output .='<tr >
        <td align="center">'.$UserName.'</td>
        <td align="center">'.$login.'</td>
        <td align="center">'.$logout.'</td>
        <td align="center">'.$ip.'</td>
      </tr>';
   }
   if($num<=0){ $output='<tr ><td colspan="3" align="center">No record found !!</td></tr>'; }
   }
   
   
   
   
   return $output;
}

# Set page size under cookie
if(($_POST['page_size']) || ($_GET['page_size']))
   setcookie('page_size', $_REQUEST['page_size'], time()+24*3600);
   
if(($_POST['user_page_size']) || ($_GET['user_page_size']))
   setcookie('user_page_size', $_REQUEST['user_page_size'], time()+24*3600);

$lang['PageSize_List'] = array(  '1'      => '1',
                     '5'      => '5',
                     '10'  => '10',
                     '15'  => '15',
                     '30'  => '30',
                     '50'  => '50',
                     '100'    => '100',
                     );
$lang['time'] = array(  '1' => '09:00-10:00' , 
                        '2' => '10:00-11:00' , 
                        '3' => '11:00-12:00' , 
                        '4' => '12:00-13:00' , 
                        '5' => '13:00-14:00' , 
                        '6' => '14:00-15:00' , 
                        '7' => '15:00-16:00' , 
                        '8' => '16:00-17:00' , 
                        '9' => '17:00-18:00' , 
                        '10' => '18:00-19:00' , 
                        '11' => '19:00-20:00' , 
                     );

function showPagination($num_items, $add_prevnext_text = TRUE)
{
global $lang;
$path_parts = pathinfo($_SERVER['SCRIPT_FILENAME']);
$base_url = $path_parts["basename"] . "?" . substr($_SERVER['QUERY_STRING'], 0, strpos($_SERVER['QUERY_STRING'],"&start")===false?strlen($_SERVER['QUERY_STRING']):strpos($_SERVER['QUERY_STRING'],"&start"));
$total_pages = ceil($num_items/$_SESSION['page_size']);

if ( $total_pages == 1 )
return '';
$on_page = floor($_SESSION['start_record'] / $_SESSION['page_size']) + 1;
$page_string = '';
if ( $total_pages > 10 )
{
$init_page_max = ( $total_pages > 3 ) ? 3 : $total_pages;
for($i = 1; $i < $init_page_max + 1; $i++)
{
   $page_string .= ( $i == $on_page ) ? '<font class=activePage>' . $i . '</font>' : '<a class=pageLink href="'. $base_url. "&amp;start=" . ( ( $i - 1 ) * $_SESSION['page_size'] )  . '" >' . $i . '</a>';
//          $page_string .= ( $i == $on_page ) ? '<font class=activePage>' . $i . '</font>' : '<a class=pageLink href="javascript: document.forms[0].action=\'' . $base_url. "&amp;start=" . ( ( $i - 1 ) * $_SESSION['page_size'] )  . '\';document.forms[0].submit();" >' . $i . '</a>';
   if ( $i <  $init_page_max )
      $page_string .= ' ';
}
if ( $on_page > 1  && $on_page < $total_pages )
{
   $page_string .= ( $on_page > 5 ) ? ' ... ' : ', ';
   $init_page_min = ( $on_page > 4 ) ? $on_page : 5;
   $init_page_max = ( $on_page < $total_pages - 4 ) ? $on_page : $total_pages - 4;

   for($i = $init_page_min - 1; $i < $init_page_max + 2; $i++)
   {
      $page_string .= ($i == $on_page) ? '<font class=activePage>' . $i . '</font>' : '<a class=pageLink href="'. $base_url . "&amp;start=" . ( ( $i - 1 ) * $_SESSION['page_size'] )  . '" >' . $i . '</a>';
//             $page_string .= ($i == $on_page) ? '<font class=activePage>' . $i . '</font>' : '<a class=pageLink href="javascript: document.forms[0].action=\'' . $base_url . "&amp;start=" . ( ( $i - 1 ) * $_SESSION['page_size'] )  . '\'; document.forms[0].submit();">' . $i . '</a>';
      if ( $i <  $init_page_max + 1 )
         $page_string .= ' ';
   }
   $page_string .= ( $on_page < $total_pages - 4 ) ? ' ... ' : ', ';
}
else
   $page_string .= ' ... ';

for($i = $total_pages - 2; $i < $total_pages + 1; $i++)
{
   $page_string .= ( $i == $on_page ) ? '<font class=activePage>' . $i . '</font>'  : '<a class=pageLink href="' . $base_url . "&amp;start=" . ( ( $i - 1 ) * $_SESSION['page_size'] ) . '">' . $i . '</a>';
//          $page_string .= ( $i == $on_page ) ? '<font class=activePage>' . $i . '</font>'  : '<a class=pageLink href="javascript: document.forms[0].action=\'' . $base_url . "&amp;start=" . ( ( $i - 1 ) * $_SESSION['page_size'] ) . '\';document.forms[0].submit();">' . $i . '</a>';
   if( $i <  $total_pages )
      $page_string .= " ";
}
}
else
{
for($i = 1; $i < $total_pages + 1; $i++)
{
   $page_string .= ( $i == $on_page ) ? '<font class=activePage>' . $i . '</font>' : '<a class=pageLink href="' . $base_url . "&amp;start=" . ( ( $i - 1 ) * $_SESSION['page_size'] ) . '">' . $i . '</a>';
//          $page_string .= ( $i == $on_page ) ? '<font class=activePage>' . $i . '</font>' : '<a class=pageLink href="javascript: document.forms[0].action=\'' . $base_url . "&amp;start=" . ( ( $i - 1 ) * $_SESSION['page_size'] ) . '\';document.forms[0].submit();">' . $i . '</a>';
   if ( $i <  $total_pages )
         $page_string .= " ";
}
}
if ( $add_prevnext_text )
{
if ( $on_page > 1 )
//          $page_string = ' <a class=pageLink href="javascript: document.forms[0].action=\'' . $base_url . "&amp;start=" . ( ( $on_page - 2 ) * $_SESSION['page_size'] ) . '\'; document.forms[0].submit();">' . "Previous" . '</a>&nbsp;&nbsp;' . $page_string;
   $page_string = ' <a class=form-prev href="' . $base_url . "&amp;start=" . ( ( $on_page - 2 ) * $_SESSION['page_size'] ) . '">' . "Previous" . '</a>&nbsp;&nbsp;' . $page_string;
else
   $page_string = '&nbsp;<font class="form-prev disabledText">Previous</font>&nbsp;' . $page_string;
if ( $on_page < $total_pages )
//          $page_string .= '&nbsp;&nbsp;<a class=pageLink href="javascript: document.forms[0].action=\'' . $base_url . "&amp;start=" . ( $on_page * $_SESSION['page_size'] ) . '\';document.forms[0].submit();">' . "Next" . '</a>';
   $page_string .= '&nbsp;&nbsp;<a class=form-next href="' . $base_url . "&amp;start=" . ( $on_page * $_SESSION['page_size'] ) . '">' . "Next" . '</a>';
else
   $page_string .= '&nbsp;<font class="form-next disabledText">Next</font>&nbsp;';
}
return $page_string;
}
         
function getFromDate($key,$val)
{
   $cdt=date('m-Y');
   $dt=explode("-",$cdt);
   $current_year=$dt[1];
   $current_month=$dt[0];
   
   if($key=='from')
   {
   
      if($val==1)
      {
         $output="01-".$cdt;
      }
      
      if($val==2)
      {
         $cdt=date("m-Y", strtotime("-1 months"));
         $output="01-".$cdt;
      }
      
      if($val==3)
      {
         $output="01-04-".($current_year);
      }
      
      if($val==4)
      {
         $output="01-04-".($current_year-1);
      }
   
   }
   else if($key=='to')
   {
   
      if($val==1)
      {
         $output=date('d')."-".$cdt;
      }
      
      if($val==2)
      {
         $cdt=date("m-Y", strtotime("-1 months"));
         $output=date('d', strtotime('last day of previous month'))."-".$cdt;
      }
      
      if($val==3)
      {
         $output="31-03-".($current_year+1);
      }
      
      if($val==4)
      {
         $output="31-03-".($current_year);
      }

   
   }
   return $output;
}

/* function used in admin-Appointment2.php page */
/* fetch email id for a any employee who is in our database */

function getemailid($id,$db)
{
   global $link;
   $q="select AtxEmail from $db.uniuserprofile where AtxUserStatus='1' and AtxUserID='$id' ";
   $rs=mysqli_query($link,$q) or die("Error in query11 ".mysqli_error());
   $ress=mysqli_fetch_array($rs);
   $email=$ress['AtxEmail'];
   return $email;
}

function getuid($id,$db)
{
   global $link;
   $q="select I_UserID from $db.tbl_mst_user_company where V_EmailID='$id'";
   $rs=mysqli_query($link,$q) or die("Error in query to get I_UserID using V_EmailID in tbl_mst_user_company".mysqli_error());
   $ress=mysqli_fetch_array($rs);
   $userid=$ress['I_UserID'];
   return $userid;

}

/* function used in admin-Appointment2.php page */
/* fetch mobile no for a any employee who is in our database */
function getphoneno($id,$db)
{
   global $link;
   $q="select AtxHomePhone from $db.uniuserprofile where AtxUserStatus='1' and AtxUserID='$id' ";
   $rs=mysqli_query($link,$q) or die("Error in query11 ".mysqli_error());
   $ress=mysqli_fetch_array($rs);
   $AtxHomePhone=$ress['AtxHomePhone'];
   return $AtxHomePhone;
}

/* function used in admin-Appointment2.php page */
/* fetch ewsa official name*/
function getewsaname($id,$db)
{
   global $link;
   $q="select AtxUserName from $db.uniuserprofile where AtxUserStatus='1' and AtxUserID='$id' ";
   $rs=mysqli_query($link,$q) or die("Error in query11 ".mysqli_error());
   $ress=mysqli_fetch_array($rs);
   $AtxUserName=$ress['AtxUserName'];
   return $AtxUserName;
}

/* function used in admin-Appointment2.php page */
function getcustomeremailid($id,$db)
{
   global $link;
   // $id => Ticket id
   $q="SELECT p.ProjectID, a.AccountName, a.resion, c.Mail, a.Phone, a.BillingStreet, a.BillingStreet2,
   a.BillingCity, a.BillingState, a.BillingPostalCode FROM $db.problemdefination p LEFT JOIN $db.accounts a
   ON a.AccountNumber = p.ProjectID LEFT JOIN $db.crmcontactid c ON a.AccountNumber = c.AccountID WHERE p.IID = '$id'";
   $rs=mysqli_query($link,$q) or die("Error in query11 ".mysqli_error());
   $ress=mysqli_fetch_array($rs);
   return $ress;
   //$email=$ress['Mail'];
   //return $email;
   
}

/* function used in admin-Appointment2.php page */
function getemailtemplate($etempid,$db,$fields)
{
   global $link;
   //if($fields=='All'){ $fields = "*"; }
   $emailq="select $fields from $db.email_template where email_id ='$etempid'";
   $emailres=mysqli_query($link,$emailq);
   $emailrs=mysqli_fetch_array($emailres);
   return $emailrs;
}

function findDateIntoDataBase($date0)
{
   $date1=explode("-",$date0);
   $date2=$date1[2]."-".$date1[1]."-".$date1[0];
   return $date2;

}

function getip()
{
      if (!empty($_SERVER['HTTP_CLIENT_IP'])){
         $IPAddress = $_SERVER['HTTP_CLIENT_IP'];
      } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
         $IPAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } else {
         $IPAddress = $_SERVER['REMOTE_ADDR'];
      }
      return $IPAddress;
}


$lang['timeperiod'] = array(  '1'      => 'This Month',
                     '2'      => 'Previous Month',
                     '3'   => 'This Financial Year',
                     '4'   => 'Previous Financial Year',
                     );
                     

$arrfeedback=array();
$arrfeedback['0'] = "Select Your Feedback";
$arrfeedback['1'] = "Satisfactory";
$arrfeedback['2'] = "Unsatisfactory";
$arrfeedback['3'] = "Good";
$arrfeedback['4'] = "Very Good";             

/*
// These three functions are not used 
//chkassignments
//delassignments
*/

function chkassignments($boss1,$uid)
{
   global $db,$link;
   //check if they already assigned
      $sqlm="SELECT TeamMembers FROM $db.uniuserhierarchy WHERE AtxUserID = '$boss1'";
      $resm=mysqli_query($link,$sqlm) or die(mysqli_error());
      if(mysqli_num_rows($resm)>0)
      {
      
            $sqlm2="SELECT TeamMembers FROM $db.uniuserhierarchy WHERE FIND_IN_SET('$uid',TeamMembers) AND AtxUserID = '$boss1'";
            $resm2=mysqli_query($link,$sqlm2) or die(mysqli_error());
            if(mysqli_num_rows($resm2)>0)
            {
               // Nothing to do here
            }
            else
            {
               $rowm=mysqli_fetch_array($resm);
               $TeamMembers=$rowm['TeamMembers'];
               //exit();
               $New_TeamMembers = $TeamMembers.",".$uid;
               $sqlmu="UPDATE $db.uniuserhierarchy SET TeamMembers = '$New_TeamMembers' WHERE AtxUserID = '$boss1'";
               mysqli_query($link,$sqlmu) or die(mysqli_error());
            }
      }
      else
      {
         $sqlm="insert into $db.uniuserhierarchy(AtxUserID,TeamMembers)values('$boss1','$uid')";
         $resm=mysqli_query($link,$sqlm) or die(mysqli_error());
      }
}


function delassignments($boss1,$uid)
{
   global $db,$link;
   //check if they already assigned
      $sqlm="SELECT TeamMembers FROM $db.uniuserhierarchy WHERE AtxUserID = '$boss1'";
      $resm=mysqli_query($link,$sqlm) or die(mysqli_error());
      if(mysqli_num_rows($resm)>0)
      {
            $rowm=mysqli_fetch_array($resm);
            $TeamMembers=$rowm['TeamMembers'];
            
/*          
            $sqlm2="SELECT TeamMembers FROM $db.uniuserhierarchy WHERE FIND_IN_SET('$uid',TeamMembers) AND AtxUserID = '$boss1'";
            $resm2=mysqli_query($link,$sqlm2) or die(mysqli_error());
            if(mysqli_num_rows($resm2)>0)
            {
               // Nothing to do here
            }
            else
            {
               
               //exit();
               $New_TeamMembers = $TeamMembers.",".$uid;
               $sqlmu="UPDATE $db.uniuserhierarchy SET TeamMembers = '$New_TeamMembers' WHERE AtxUserID = '$boss1'";
               mysqli_query($link,$sqlmu) or die(mysqli_error());
            }
*/          
      }
      else
      {
         //$sqlm="insert into $db.uniuserhierarchy(AtxUserID,TeamMembers)values('$boss1','$uid')";
         //$resm=mysqli_query($link,$sqlm) or die(mysqli_error());
      }
}

function removeFromString($str, $item) {
    $parts = explode(',', $str);

    while(($i = array_search($item, $parts)) !== false) {
        unset($parts[$i]);
    }

    return implode(',', $parts);
}

// Be care full this written three places one here 
// Another in innercommon.php function Name is changed (allsubordinatesduplicate) so please if change any thing here cmake change for these fucntion are also make
// Another in function_define_dk.php
$rwscategory_path = array();
$allids=array();
function allsubordinates($id)
{
   //
   //uniuserreportedto
   //I_UserId
   //I_ReportedTo
   //$name ='';
   //
   global $db,$link, $rwscategory_path, $allids;
   $allids[]=$id;
   
   $sql = "SELECT I_UserId, I_ReportedTo FROM $db.uniuserreportedto WHERE I_ReportedTo ='$id'";
   //echo "<br>";
   $result = mysqli_query($link,$sql) or die(mysqli_error());
   $total = mysqli_num_rows($result);
   
   
   if($total>0)
   {
      while($row = mysqli_fetch_array($result))
      {
         //$name = $row['name'];
         $category_id = $row['I_UserId'];
         //$allids.=$category_id.", ";
         //echo "<br>";
         $rwscategory_path['name_'.$category_id] = $category_id;
         allsubordinates($category_id);
      }
   }
   //return $name;
   return $allids;
}

function chkassignmentsunik($boss1,$uid)
{
   global $db,$link;
   
   //check if they already assigned
   $sqlm="SELECT I_UserId FROM $db.uniuserreportedto WHERE I_ReportedTo = '$boss1' AND I_UserId = '$uid'";
   $resm=mysqli_query($link,$sqlm) or die(mysqli_error());
   if(mysqli_num_rows($resm)>0)
   {
         // Nothing to do here
   }
   else
   {
      $sqlmi="insert into $db.uniuserreportedto(I_ReportedTo,I_UserId)values('$boss1','$uid')";
      $resm=mysqli_query($link,$sqlmi) or die(mysqli_error());
   }
}

function chkunassignmentsunik($uid, $boss)
{
   global $db,$link;
   
   $bosss=implode(",",$boss); if($bosss=='') $bosss="''";
   $sqlm2="SELECT I_Row_Id, I_ReportedTo FROM $db.uniuserreportedto WHERE I_UserId = '$uid' AND I_ReportedTo NOT IN ($bosss)";
   $resm2=mysqli_query($link,$sqlm2) or die(mysqli_error());
   if(mysqli_num_rows($resm2)>0)
   {
      // Delete From table
      while($rowm2=mysqli_fetch_array($resm2))
      {
         $I_boss=$rowm2['I_ReportedTo'];
         $sqlmu="DELETE FROM $db.uniuserreportedto WHERE I_UserId = '$uid' AND I_ReportedTo = '$I_boss'";
         mysqli_query($link,$sqlmu) or die(mysqli_error());
      }
   }
}

function RegComp($complainttype, $naturecomplaint, $areaid, $housetype, $houseno,  $locationid, $zoneid, $source, $notifytype, $notifyno, $i_SPCode, $filepath, $compdesc, $timeslot)

{  
   



global $link;
global $ProDatabasename,$db,$configdbname,$USERID;
   
   if(!empty($db)){ 
   //Created By Vivek Pratap Singh Created Date 10-24-2016;
   // Because provider is blank
   $ProDatabasename = finduserdatabase($i_SPCode);

   //$ProDatabasename=$db;
   }

   //echo $compdesc;
   //exit();
   $strsql="SELECT * FROM $ProDatabasename.accounts WHERE (v_Locationid = '$locationid' || Phone = '$locationid' )"; 
   $szFnName = __FILE__.":"."', '','$strsql',''";




$rscomp=mysqli_query($link,$strsql)or die(mysqli_error());
   $rowcomp=mysqli_fetch_array($rscomp);
   $v_LocationId=$rowcomp['v_Locationid'];
   $AccountNumber=$rowcomp['AccountNumber'];
   $CustomerAccount=$rowcomp['CustomerAccount'];
   $v_RMN = $rowcomp['Phone'];
   
if(empty($notifyno)){ $notifyno = $v_RMN; }
   
   


$V_SubCategoryName='NotOthers';  

$strcat="select sc.V_SubCategoryName As V_SubCategoryName,sc.I_SubCategoryID As I_SubCategoryID from $ProDatabasename.tbl_mst_subcategory sc

join $ProDatabasename.tbl_assoc_subcategory_category  a on a.I_SubCategoryID=sc.I_SubCategoryID

where a.I_CategoryID='$complainttype' and  sc.I_SubCategoryID='$naturecomplaint' ";


$rscat=mysqli_query($link,$strcat)or die(mysqli_error());
         

                     if($numrows=mysqli_num_rows($rscat)>0)
                            {
            
                              $rowcomp=mysqli_fetch_array($rscat);
                              $V_SubCategoryName = $rowcomp['V_SubCategoryName'];

                             }




     $allow_multiple ='0';
         
      $strchk="select i_allow_multiple from $configdbname.tbl_mst_company where I_CompanyID='$i_SPCode' and i_allow_multiple='1'";
          $chkcomp=mysqli_query($link,$strchk)or die(mysqli_error());

              if($numrows=mysqli_num_rows($chkcomp)>0)
                            {
            
                              $rowcomp1=mysqli_fetch_array($chkcomp);
                              $allow_multiple = $rowcomp1['i_allow_multiple'];

                             } 



$strsqlvalidate = "SELECT IID as I_DocketNo FROM $ProDatabasename.problemdefination WHERE v_LocationID = '$locationid' AND ProblemType = '$complainttype' AND SubProblemType ='$naturecomplaint' AND ComplainHandled = '0'"; 
   

$rscomp=mysqli_query($link,$strsqlvalidate)or die(mysqli_error());





   if($numrows=mysqli_num_rows($rscomp)>0 && $V_SubCategoryName!='Others' && $allow_multiple !='1' )
   {
      $counter = 1;
      $rowcomp=mysqli_fetch_array($rscomp);
      $i_docketno=$rowcomp['I_DocketNo'];
   }
   else
   {
      $counter = 0;
   
   //exit();
   /*if($counter==0)
   {
      $URL="docketid.php?add=1&dID=".$i_docketno;
   }
   else
   {
      $URL="docketid.php?add=0&dID=".$i_docketno;
   }  */
   
   // Add default Time "$timeframe='30';" if database have no default time
   // Made change on 08-07-2015 by abhishek
   $timeframe='30';
   //$strsqltimeframe = "SELECT i_TimeFrame as i_timeframe FROM $ProDatabasename.tbl_mst_subcategory WHERE i_comptype = '$complainttype' AND I_SubCategoryID = '$naturecomplaint' ";
   $strsqltimeframe="SELECT ts.i_TimeFrame as i_timeframe FROM $ProDatabasename.tbl_mst_category c,"
      ." $ProDatabasename.tbl_mst_services s, $ProDatabasename.tbl_assoc_subcategory_category t, $ProDatabasename.tbl_mst_subcategory ts WHERE"
      ." c.I_CategoryID=t.I_CategoryID and s.I_ServiceID=t.I_ServiceID and ts.I_SubCategoryStatus=1 and"
      ." ts.I_SubCategoryID=t.I_SubCategoryID AND ts.I_SubCategoryID='$naturecomplaint' AND t.I_CategoryID= '$complainttype' ";



   $rstimeframe=mysqli_query($link,$strsqltimeframe)or die(mysqli_error());
   $rowtimeframe=mysqli_fetch_array($rstimeframe);
      if(!empty($rowtimeframe['i_timeframe']))
      {     
         $timeframe=$rowtimeframe['i_timeframe'];
      }
   
   
   // we are converted timeframe minute to seconds for recalculate the time frame
   // Made changes on 08-07-2015 by abhishek
   //echo "Time Frame<br>";
   if($dayname <> "Saturday")
   {
      $timeframe = ($timeframe*60);
   }
   else
   {
      //echo 'TT'.$timeframe = $timeframe + 1440;
      $timeframe = ($timeframe*60) + 86400;
   }
   
   
   //echo "Time Frame First <br>";
   //$strsqltimestamp = "SELECT UNIX_TIMESTAMP()";
   //echo "Time Frame First <br>";
   $strsqltimestamp = "SELECT UNIX_TIMESTAMP() +".$timeframe." as c ";
   //echo "Time STAMPS<br>";
   $rstimestamp=mysqli_query($link,$strsqltimestamp)or die(mysqli_error());
   $rowtimestamp=mysqli_fetch_array($rstimestamp);
   $currenttimeframe=$rowtimestamp['c'];
   //echo "AFTER ALL Change Time Frame<br>";
   //exit();
   if(($complainttype <> "") && ($naturecomplaint <> ""))
   {
      $cgi=0;
      $strsqlgroupid = " SELECT i_CompGroupID FROM $ProDatabasename.tbl_compgroupassign WHERE i_CompTypeID = '$complainttype' AND i_NatureOfComp = '$naturecomplaint' ";


      $rsgroup=mysqli_query($link,$strsqlgroupid)or die(mysqli_error('HI'));
      $rowgroup=mysqli_fetch_array($rsgroup);
      $groupid=$rowgroup['i_CompGroupID'];
      $i_CompGroupID[$cgi]=$rowgroup['i_CompGroupID'];
      $cgi++;
   }
   
   if(($zoneid <> "") && ($areaid <> ""))
   {
      $ci=0;
      $strsql="SELECT i_CircleID FROM $ProDatabasename.tbl_areacircleassign WHERE i_ZoneID = '$zoneid' AND i_AreaID = '$areaid'";

  
      $rscomp=mysqli_query($link,$strsql)or die(mysqli_error('TEST'));
      if($numrows=mysqli_num_rows($rscomp)>0)
      {
         $rowcomp=mysqli_fetch_array($rscomp);
         $circleid=$rowcomp['i_CircleID'];
         $i_CircleID[$ci]=$rowcomp['i_CircleID'];
         $ci++;
      }
   
   }
   
   

   /*
   if(($circleid <> "") && ($groupid <> ""))
   {
      $strsqltechstaffid="SELECT i_TechStaffID FROM $ProDatabasename.tbl_techstaffassign WHERE i_CircleID = '$circleid' AND i_CompGroupID = '$groupid'";
//AND i_Level = '0'
      $rscomp=mysqli_query($link,$strsqltechstaffid);
      if($numrows=mysqli_num_rows($rscomp)>0)
      {
         $rowcomp=mysqli_fetch_array($rscomp);
         $techstaffid=$rowcomp['i_TechStaffID'];



      }
   
   }
   */

   foreach($i_CircleID as $keyci => $valci)
   {
   
      foreach($i_CompGroupID as $keycgi => $valcgi)
      {
         //echo $sql68="SELECT i_TechStaffID FROM $db.tbl_techstaffassign WHERE i_CircleID='$i_CircleID' AND i_CompGroupID = '$i_CompGroupID' ";

         

if(empty($i_TechStaffID)){
         
$sql668="SELECT i_TechStaffID FROM $ProDatabasename.tbl_techstaffassign WHERE i_CircleID='$valci' AND i_CompGroupID = '$valcgi'";

    

   //AND i_Level = '0'
         

            $res668=mysqli_query($link,$sql668);
            if(mysqli_num_rows($res668)>0)
            {
               $row668=mysqli_fetch_array($res668);
               $techstaffid=$row668['i_TechStaffID'];


               break;
            }
         }

      }
   
   }
      
   $sqllock="LOCK TABLES $ProDatabasename.problemdefination";
   mysqli_query($link,$sqllock);
   $strsqldocketid = "select max(IID) as d from $ProDatabasename.problemdefination ";
   $rscomp=mysqli_query($link,$strsqldocketid);
   if($numrows=mysqli_num_rows($rscomp)>0)
   {
      $rowcomp=mysqli_fetch_array($rscomp);
      $i_docketno=$rowcomp['d'];
   }
   
   if(!empty($i_docketno))
   {
      $i_docketno = $i_docketno + 1;
   }
   else
   {
      $i_docketno = 1;
   }
      
   
   if(empty($notifytype)){ $notifytype=0; }
   if(empty($techstaffid)){ $techstaffid=0; }

   if(empty($housetype)){ $housetype=0; }
   $sessiondocketno = $i_docketno;
   
   
   
   $sessionid = $sessiondocketno.$currenttimeframe;
      //d_CompletedAt, '0000-00-00 00:00:00',
   $curretdate = date('Y-m-d');  
   $currettime = date('H:i:s');
   $curretdatetime = $curretdate.' '.$currettime;
   //$deadlinedate = date('Y-m-d');
   $deadlinedate=date('Y-m-d', strtotime('+1 day', strtotime($curretdate)) ); 
   $deadlinetime = date('H:i:s');
   $timestamp=strtotime(date("Y-m-d H:i:s"));
   $urgency    = 1; //$_POST['I_PriorityID'];   //$urgency
   $StatusID      = 0;
   $type       = 1;
   
   $v_mobileNo   = getTechPhone($techstaffid);

    


   $AreaName     = getAreaName($areaid);
   
   $CompTypeName = getCompType($complainttype);
   
   $CompNature   = getCompnature($naturecomplaint, $complainttype);


   $subject   = $CompTypeName;
   
   $branch       = '1459'; //$i_SPCode it must be 1459
   $I_ServiceID  = 1;
   //$timeslot   = 1;

   if(empty($timeslot)){ $timeslot=1; }
      

   if(empty($techstaffid)){ $pendingreason = '6'; }else{ $pendingreason = '0'; }
   //$strsqlinsert = "insert into $ProDatabasename.problemdefination(IID, ID,  V_sessionid, v_CallerId, d_CreatedAt, i_CompletedBy, i_Status, i_HouseType, i_HouseNo, i_AreaId, i_AccomId, i_CompTypeId,i_NatureOfComp, i_NewDocketNo, i_CheckingTime, i_AlertCount, v_LocationID, i_TechStaffId, i_ZoneID,i_CompSourceId,i_NotifType,i_NotifNo) values ('$i_docketno', '$i_docketno', '$sessionid', '', '$curretdatetime', '', '1', '$housetype', '$houseno', '$areaid', '0', '$complainttype', '$naturecomplaint', '0', '$currenttimeframe', '1', '$locationid', '$techstaffid', '$zoneid', '$source', '$notifytype', '$notifyno' )"; 

   $strsqlinsert="insert into $ProDatabasename.problemdefination(IID, ID, ReportedBy, ComplainDate, ComplainTime, ProblemDescription, ProblemType,
 SubProblemType, Urgency, Deadline, ComplainHandled, ProjectID, Type, Subject, BranchID, 
 ReportedDate, ReportedTime, I_ServiceID, DeadLineTime, AssignedTo, i_TechStaffId, v_Reason, I_SourceID,
 I_HouseType, I_HouseNo, I_AreaId, I_ZoneId, v_LocationID, i_CheckingTime, i_TimeSlot,I_Pending_Reason,I_Pending_Datetime,V_Pending_Description) values 
 ('$i_docketno', '$i_docketno', '$userid', '$curretdate', '$currettime', '$compdesc', '$complainttype', '$naturecomplaint', '$urgency', '$deadlinedate',
 '$StatusID', '$AccountNumber', '$type', '$subject', '$branch', '$curretdate', '$currettime', '$I_ServiceID',
 '$deadlinetime',  '$techstaffid', '$techstaffid', '', '$source', '$housetype','$houseno','$areaid','$zoneid',
 '$locationid', '$timestamp', '$timeslot','$pendingreason','$curretdatetime','$compdesc')";
 // '$notifytype', '$notifyno'
   mysqli_query($link,$strsqlinsert)or die(mysqli_error());
   
   $sqlunlock="UNLOCK TABLES $ProDatabasename";
   mysqli_query($link,$sqlunlock);
   //if(empty($techstaffid)){ $pendingreason = '9'; }else{ $pendingreason = '0'; }
   //$strdtl="insert into $ProDatabasename.tbl_usercompdesc (I_DocketNo, V_UserCompDesc, CHandledDateTime, CHandledBy, ComplaintStatus, I_Pending_Reason, I_Pending_Datetime  ) values ('$i_docketno', '$compdesc', '0000-00-00 00:00:00', '$techstaffid', '1', '$pendingreason', '$curretdatetime' ) ";
   //mysqli_query($link,$strdtl);
   //I_SRStatus
   //echo '<br />';
   $strsqlinsertwrap="INSERT INTO $ProDatabasename.tbl_cust_wrapcalldetails (I_WrapID, V_CustId, I_ServiceID, I_CategoryID, I_SubCategoryID,I_SubSubCategoryID, V_Remarks, D_OpenDate,
 V_CreatedBy, TimeStamp, I_TypeOfCall, I_PriorityID, V_Subject,I_SRStatus, recorded_filename, V_VoicePath, salesref, V_SRAssignedTo, D_DOA_SRToSalesAgent, I_TicketID)
 values ('0', '$AccountNumber','$I_ServiceID', '$complainttype', '$naturecomplaint' , '$naturecomplaint' ,  '$compdesc' , '$curretdate' , '$techstaffid' , now(),
 '$type' , '$urgency', '$subject', '$StatusID' , '','','', '$techstaffid', '$curretdate', '$i_docketno')";
   mysqli_query($link,$strsqlinsertwrap)or die(mysqli_error());
   
   $I_WrapID = mysqli_insert_id();
   //echo '<br />'; //ComplaintStatus $StatusID
   $sql_c1="insert into $ProDatabasename.complainthandled (ComplaintID,CSolution,CHandledDate,CHandledTime,CHandledBy,V_Datetime,ComplaintStatus)values('$i_docketno','$compdesc','$curretdate','$currettime','$techstaffid','$curretdatetime','$StatusID')";
   $res_c1=mysqli_query($link,$sql_c1)or die(mysqli_error());//or die(mysqli_error());
   
   /*$sql5 = "SELECT I_WrapID FROM $ProDatabasename.tbl_cust_wrapcalldetails WHERE I_TicketID = '$i_docketno'";
   $res=mysqli_query($link,$sql5);
   $row=mysqli_fetch_array($res);
   $I_WrapID=$row['I_WrapID'];*/

   $sql_c="UPDATE $ProDatabasename.problemdefination SET i_wrapcallid = '$I_WrapID' WHERE IID = '$i_docketno'";
   $res_c=mysqli_query($link,$sql_c)or die(mysqli_error());


   
   //if(!empty(mysqli_insert_id))
   //{
   /* Inserting into SMS Tbl */
   if($source!='6')
   { 
     $strsqlinsertimg = "insert into $ProDatabasename.compqueue_images(id, I_DocketNo, Image_file) values ('0', '$i_docketno','$filepath')"; 



     mysqli_query($link,$strsqlinsertimg)or die(mysqli_error());
   }
   
   //$CompTypeName $CompNature

   $strsqlmasterinsert = "replace into $configdbname.tbl_usercompqueue( I_DocketNo, i_SPCode, i_ServiceId, i_UserId, v_Category, v_SubCategory,
   d_CreatedAt, i_Status,i_NotifNo,i_CompSourceId ) values ('$i_docketno', '$i_SPCode', '1', '$CustomerAccount', '$CompTypeName', '$CompNature', '$curretdatetime', '$StatusID', '$notifyno', '$source')";



   mysqli_query($link,$strsqlmasterinsert)or die(mysqli_error());
   
   

   //$inidata = parse_ini_file('icms.ini');
   global $inidata;
   $ALERTMODE=$inidata[ALERTMODE];
   //exit();
   if($ALERTMODE!='0')
   {
   $v_smsString = "New Complaint: DktNO=".$i_docketno.", LOCATION NO=".$locationid.", AREA=".$AreaName.", Bldg=".$houseno." ,COMP TYPE=".$CompTypeName.", NATURE TYPE=".$CompNature; 
   $v_usersmsString = "Your complaint lodged successfully, DktNo is ".$i_docketno; 
   //$strsqlinsert = "Insert into $ProDatabasename.tbl_smsmessages(v_mobileNo, v_smsString, d_timeStamp, d_lastTriedAt, i_retries, i_MaxRetries, i_status, v_category) values ('$v_mobileNo', '$v_smsString', now(), now(), '0', '5', '1', 'web')";   
   //mysqli_query($link,$strsqlinsert);
   



$strmassqlinsert = "Insert into $configdbname.tbl_smsmessages(v_mobileNo, v_smsString, d_timeStamp, d_lastTriedAt, i_retries, i_MaxRetries, i_status, v_category) values ('$v_mobileNo', '$v_smsString', '$curretdatetime', '$curretdatetime', '0', '5', '1', '$ProDatabasename')"; 
   mysqli_query($link,$strmassqlinsert);



   $strmasusersqlinsert = "Insert into $configdbname.tbl_smsmessages(v_mobileNo, v_smsString, d_timeStamp, d_lastTriedAt, i_retries, i_MaxRetries, i_status, v_category) values ('$notifyno', '$v_usersmsString', '$curretdatetime', '$curretdatetime', '0', '5', '1', '$ProDatabasename')"; 
   mysqli_query($link,$strmasusersqlinsert);
   }
   //exit();
   
   }

   return $RegComp = $i_docketno;
   //}
   
   
}





function RegComp1($complainttype, $naturecomplaint, $areaid, $housetype, $houseno,  $locationid, $zoneid, $source, $notifytype, $notifyno, $i_SPCode, $filepath, $compdesc, $timeslot)

{  
   



global $link;
global $ProDatabasename,$db,$configdbname,$USERID;
   
   if(!empty($db)){ 
   //Created By Vivek Pratap Singh Created Date 10-24-2016;
   // Because provider is blank
   $ProDatabasename = finduserdatabase($i_SPCode);

   //$ProDatabasename=$db;
   }

   //echo $compdesc;
   //exit();
   $strsql="SELECT * FROM $ProDatabasename.accounts WHERE (v_Locationid  = '$locationid' || Phone = '$locationid' )"; 
   $szFnName = __FILE__.":"."', '','$strsql',''";




$rscomp=mysqli_query($link,$strsql)or die(mysqli_error());
   $rowcomp=mysqli_fetch_array($rscomp);
   $v_LocationId=$rowcomp['v_Locationid'];
   $AccountNumber=$rowcomp['AccountNumber'];
   $CustomerAccount=$rowcomp['CustomerAccount'];
   $v_RMN = $rowcomp['Phone'];
   
if(empty($notifyno)){ $notifyno = $v_RMN; }
   
   


$V_SubCategoryName='NotOthers';  

$strcat="select sc.V_SubCategoryName As V_SubCategoryName,sc.I_SubCategoryID As I_SubCategoryID from $ProDatabasename.tbl_mst_subcategory sc

join $ProDatabasename.tbl_assoc_subcategory_category  a on a.I_SubCategoryID=sc.I_SubCategoryID

where a.I_CategoryID='$complainttype' and  sc.I_SubCategoryID='$naturecomplaint' ";


$rscat=mysqli_query($link,$strcat)or die(mysqli_error());
         

                     if($numrows=mysqli_num_rows($rscat)>0)
                            {
            
                              $rowcomp=mysqli_fetch_array($rscat);
                              $V_SubCategoryName = $rowcomp['V_SubCategoryName'];

                             }




     $allow_multiple ='0';
         
      $strchk="select i_allow_multiple from $configdbname.tbl_mst_company where I_CompanyID='$i_SPCode' and i_allow_multiple='1'";
          $chkcomp=mysqli_query($link,$strchk)or die(mysqli_error());

              if($numrows=mysqli_num_rows($chkcomp)>0)
                            {
            
                              $rowcomp1=mysqli_fetch_array($chkcomp);
                              $allow_multiple = $rowcomp1['i_allow_multiple'];

                             } 



$strsqlvalidate = "SELECT IID as I_DocketNo FROM $ProDatabasename.problemdefination WHERE v_LocationID = '$locationid' AND ProblemType = '$complainttype' AND SubProblemType ='$naturecomplaint' AND ComplainHandled = '0'"; 
   

$rscomp=mysqli_query($link,$strsqlvalidate)or die(mysqli_error());





   if($numrows=mysqli_num_rows($rscomp)>0 && $V_SubCategoryName!='Others' && $allow_multiple !='1' )
   {
      $counter = 1;
      $rowcomp=mysqli_fetch_array($rscomp);
      $i_docketno=$rowcomp['I_DocketNo'];
   }
   else
   {
      $counter = 0;
   
   //exit();
   /*if($counter==0)
   {
      $URL="docketid.php?add=1&dID=".$i_docketno;
   }
   else
   {
      $URL="docketid.php?add=0&dID=".$i_docketno;
   }  */
   
   // Add default Time "$timeframe='30';" if database have no default time
   // Made change on 08-07-2015 by abhishek
   $timeframe='30';
   //$strsqltimeframe = "SELECT i_TimeFrame as i_timeframe FROM $ProDatabasename.tbl_mst_subcategory WHERE i_comptype = '$complainttype' AND I_SubCategoryID = '$naturecomplaint' ";
   $strsqltimeframe="SELECT ts.i_TimeFrame as i_timeframe FROM $ProDatabasename.tbl_mst_category c,"
      ." $ProDatabasename.tbl_mst_services s, $ProDatabasename.tbl_assoc_subcategory_category t, $ProDatabasename.tbl_mst_subcategory ts WHERE"
      ." c.I_CategoryID=t.I_CategoryID and s.I_ServiceID=t.I_ServiceID and ts.I_SubCategoryStatus=1 and"
      ." ts.I_SubCategoryID=t.I_SubCategoryID AND ts.I_SubCategoryID='$naturecomplaint' AND t.I_CategoryID= '$complainttype' ";



   $rstimeframe=mysqli_query($link,$strsqltimeframe)or die(mysqli_error());
   $rowtimeframe=mysqli_fetch_array($rstimeframe);
      if(!empty($rowtimeframe['i_timeframe']))
      {     
         $timeframe=$rowtimeframe['i_timeframe'];
      }
   
   
   // we are converted timeframe minute to seconds for recalculate the time frame
   // Made changes on 08-07-2015 by abhishek
   //echo "Time Frame<br>";
   if($dayname <> "Saturday")
   {
      $timeframe = ($timeframe*60);
   }
   else
   {
      //echo 'TT'.$timeframe = $timeframe + 1440;
      $timeframe = ($timeframe*60) + 86400;
   }
   
   
   //echo "Time Frame First <br>";
   //$strsqltimestamp = "SELECT UNIX_TIMESTAMP()";
   //echo "Time Frame First <br>";
   $strsqltimestamp = "SELECT UNIX_TIMESTAMP() +".$timeframe." as c ";
   //echo "Time STAMPS<br>";
   $rstimestamp=mysqli_query($link,$strsqltimestamp)or die(mysqli_error());
   $rowtimestamp=mysqli_fetch_array($rstimestamp);
   $currenttimeframe=$rowtimestamp['c'];
   //echo "AFTER ALL Change Time Frame<br>";
   //exit();
   if(($complainttype <> "") && ($naturecomplaint <> ""))
   {
      $cgi=0;
      $strsqlgroupid = " SELECT i_CompGroupID FROM $ProDatabasename.tbl_compgroupassign WHERE i_CompTypeID = '$complainttype' AND i_NatureOfComp = '$naturecomplaint' ";


      $rsgroup=mysqli_query($link,$strsqlgroupid)or die(mysqli_error('HI'));
      $rowgroup=mysqli_fetch_array($rsgroup);
      $groupid=$rowgroup['i_CompGroupID'];
      $i_CompGroupID[$cgi]=$rowgroup['i_CompGroupID'];
      $cgi++;
   }
   
   if(($zoneid <> "") && ($areaid <> ""))
   {
      $ci=0;
      $strsql="SELECT i_CircleID FROM $ProDatabasename.tbl_areacircleassign WHERE i_ZoneID = '$zoneid' AND i_AreaID = '$areaid'";

  
      $rscomp=mysqli_query($link,$strsql)or die(mysqli_error('TEST'));
      if($numrows=mysqli_num_rows($rscomp)>0)
      {
         $rowcomp=mysqli_fetch_array($rscomp);
         $circleid=$rowcomp['i_CircleID'];
         $i_CircleID[$ci]=$rowcomp['i_CircleID'];
         $ci++;
      }
   
   }
   
   

   /*
   if(($circleid <> "") && ($groupid <> ""))
   {
      $strsqltechstaffid="SELECT i_TechStaffID FROM $ProDatabasename.tbl_techstaffassign WHERE i_CircleID = '$circleid' AND i_CompGroupID = '$groupid'";
//AND i_Level = '0'
      $rscomp=mysqli_query($link,$strsqltechstaffid);
      if($numrows=mysqli_num_rows($rscomp)>0)
      {
         $rowcomp=mysqli_fetch_array($rscomp);
         $techstaffid=$rowcomp['i_TechStaffID'];



      }
   
   }
   */

   foreach($i_CircleID as $keyci => $valci)
   {
   
      foreach($i_CompGroupID as $keycgi => $valcgi)
      {
         //echo $sql68="SELECT i_TechStaffID FROM $db.tbl_techstaffassign WHERE i_CircleID='$i_CircleID' AND i_CompGroupID = '$i_CompGroupID' ";

         

if(empty($i_TechStaffID)){
         
$sql668="SELECT i_TechStaffID FROM $ProDatabasename.tbl_techstaffassign WHERE i_CircleID='$valci' AND i_CompGroupID = '$valcgi'";

    

   //AND i_Level = '0'
         

            $res668=mysqli_query($link,$sql668);
            if(mysqli_num_rows($res668)>0)
            {
               $row668=mysqli_fetch_array($res668);
               $techstaffid=$row668['i_TechStaffID'];


               break;
            }
         }

      }
   
   }
      
        
   $sqllock="LOCK TABLES $ProDatabasename.tbl_auto_serialno";
   mysqli_query($link,$sqllock);

   $strsqldocketid = "select max(i_auto_docketno) as d from $ProDatabasename.tbl_auto_serialno where datetime=now()";


   $rscomp=mysqli_query($link,$strsqldocketid);

   if($numrows=mysqli_num_rows($rscomp)>0)
   {
      $rowcomp=mysqli_fetch_array($rscomp);
      $i_docketno=$rowcomp['d'];
 
   }
    

   if(!empty($i_docketno))
   {
      
         $i_docketno = $i_docketno + 1;
 
          $strupdate="update $ProDatabasename.tbl_auto_serialno set i_auto_docketno='$i_docketno'";
                mysqli_query($link,$strupdate);
        
   
         }
   else
   {
      
          
        $i_docketno =1;
             
         $i_docketno = date('Ymd').$i_docketno;

   $strsqlinsert = "replace into $ProDatabasename.tbl_auto_serialno(i_auto_docketno, datetime) values ('$i_docketno', now())";

   mysqli_query($link,$strsqlinsert)or die(mysqli_error());
     


   }
      
   
   if(empty($notifytype)){ $notifytype=0; }
   if(empty($techstaffid)){ $techstaffid=0; }

   if(empty($housetype)){ $housetype=0; }
   $sessiondocketno = $i_docketno;
   
   
   
   $sessionid = $sessiondocketno.$currenttimeframe;
      //d_CompletedAt, '0000-00-00 00:00:00',
   $curretdate = date('Y-m-d');  
   $currettime = date('H:i:s');
   $curretdatetime = $curretdate.' '.$currettime;
   //$deadlinedate = date('Y-m-d');
   $deadlinedate=date('Y-m-d', strtotime('+1 day', strtotime($curretdate)) ); 
   $deadlinetime = date('H:i:s');
   $timestamp=strtotime(date("Y-m-d H:i:s"));
   $urgency    = 1; //$_POST['I_PriorityID'];   //$urgency
   $StatusID      = 0;
   $type       = 1;
   
   $v_mobileNo   = getTechPhone($techstaffid);

    


   $AreaName     = getAreaName($areaid);
   
   $CompTypeName = getCompType($complainttype);
   
   $CompNature   = getCompnature($naturecomplaint, $complainttype);


   $subject   = $CompTypeName;
   
   $branch       = '1459'; //$i_SPCode it must be 1459
   $I_ServiceID  = 1;
   //$timeslot   = 1;

   if(empty($timeslot)){ $timeslot=1; }
      

   if(empty($techstaffid)){ $pendingreason = '6'; }else{ $pendingreason = '0'; }
   //$strsqlinsert = "insert into $ProDatabasename.problemdefination(IID, ID,  V_sessionid, v_CallerId, d_CreatedAt, i_CompletedBy, i_Status, i_HouseType, i_HouseNo, i_AreaId, i_AccomId, i_CompTypeId,i_NatureOfComp, i_NewDocketNo, i_CheckingTime, i_AlertCount, v_LocationID, i_TechStaffId, i_ZoneID,i_CompSourceId,i_NotifType,i_NotifNo) values ('$i_docketno', '$i_docketno', '$sessionid', '', '$curretdatetime', '', '1', '$housetype', '$houseno', '$areaid', '0', '$complainttype', '$naturecomplaint', '0', '$currenttimeframe', '1', '$locationid', '$techstaffid', '$zoneid', '$source', '$notifytype', '$notifyno' )"; 

   $strsqlinsert="insert into $ProDatabasename.problemdefination(IID, ID, ReportedBy, ComplainDate, ComplainTime, ProblemDescription, ProblemType,
 SubProblemType, Urgency, Deadline, ComplainHandled, ProjectID, Type, Subject, BranchID, 
 ReportedDate, ReportedTime, I_ServiceID, DeadLineTime, AssignedTo, i_TechStaffId, v_Reason, I_SourceID,
 I_HouseType, I_HouseNo, I_AreaId, I_ZoneId, v_LocationID, i_CheckingTime, i_TimeSlot,I_Pending_Reason,I_Pending_Datetime,V_Pending_Description) values 
 ('$i_docketno', '$i_docketno', '$userid', '$curretdate', '$currettime', '$compdesc', '$complainttype', '$naturecomplaint', '$urgency', '$deadlinedate',
 '$StatusID', '$AccountNumber', '$type', '$subject', '$branch', '$curretdate', '$currettime', '$I_ServiceID',
 '$deadlinetime',  '$techstaffid', '$techstaffid', '', '$source', '$housetype','$houseno','$areaid','$zoneid',
 '$locationid', '$timestamp', '$timeslot','$pendingreason','$curretdatetime','$compdesc')";
 // '$notifytype', '$notifyno'
   mysqli_query($link,$strsqlinsert)or die(mysqli_error());
   
   $sqlunlock="UNLOCK TABLES $ProDatabasename";
   mysqli_query($link,$sqlunlock);
   //if(empty($techstaffid)){ $pendingreason = '9'; }else{ $pendingreason = '0'; }
   //$strdtl="insert into $ProDatabasename.tbl_usercompdesc (I_DocketNo, V_UserCompDesc, CHandledDateTime, CHandledBy, ComplaintStatus, I_Pending_Reason, I_Pending_Datetime  ) values ('$i_docketno', '$compdesc', '0000-00-00 00:00:00', '$techstaffid', '1', '$pendingreason', '$curretdatetime' ) ";
   //mysqli_query($link,$strdtl);
   //I_SRStatus
   //echo '<br />';
   $strsqlinsertwrap="INSERT INTO $ProDatabasename.tbl_cust_wrapcalldetails (I_WrapID, V_CustId, I_ServiceID, I_CategoryID, I_SubCategoryID,I_SubSubCategoryID, V_Remarks, D_OpenDate,
 V_CreatedBy, TimeStamp, I_TypeOfCall, I_PriorityID, V_Subject,I_SRStatus, recorded_filename, V_VoicePath, salesref, V_SRAssignedTo, D_DOA_SRToSalesAgent, I_TicketID)
 values ('0', '$AccountNumber','$I_ServiceID', '$complainttype', '$naturecomplaint' , '$naturecomplaint' ,  '$compdesc' , '$curretdate' , '$techstaffid' , now(),
 '$type' , '$urgency', '$subject', '$StatusID' , '','','', '$techstaffid', '$curretdate', '$i_docketno')";
   mysqli_query($link,$strsqlinsertwrap)or die(mysqli_error());
   
   $I_WrapID = mysqli_insert_id();
   //echo '<br />'; //ComplaintStatus $StatusID
   $sql_c1="insert into $ProDatabasename.complainthandled (ComplaintID,CSolution,CHandledDate,CHandledTime,CHandledBy,V_Datetime,ComplaintStatus)values('$i_docketno','$compdesc','$curretdate','$currettime','$techstaffid','$curretdatetime','$StatusID')";
   $res_c1=mysqli_query($link,$sql_c1)or die(mysqli_error());//or die(mysqli_error());
   
   /*$sql5 = "SELECT I_WrapID FROM $ProDatabasename.tbl_cust_wrapcalldetails WHERE I_TicketID = '$i_docketno'";
   $res=mysqli_query($link,$sql5);
   $row=mysqli_fetch_array($res);
   $I_WrapID=$row['I_WrapID'];*/

   $sql_c="UPDATE $ProDatabasename.problemdefination SET i_wrapcallid = '$I_WrapID' WHERE IID = '$i_docketno'";
   $res_c=mysqli_query($link,$sql_c)or die(mysqli_error());


   
   //if(!empty(mysqli_insert_id))
   //{
   /* Inserting into SMS Tbl */
   if($source!='6')
   { 
     $strsqlinsertimg = "insert into $ProDatabasename.compqueue_images(id, I_DocketNo, Image_file) values ('0', '$i_docketno','$filepath')"; 



     mysqli_query($link,$strsqlinsertimg)or die(mysqli_error());
   }
   
   //$CompTypeName $CompNature

   $strsqlmasterinsert = "replace into $configdbname.tbl_usercompqueue( I_DocketNo, i_SPCode, i_ServiceId, i_UserId, v_Category, v_SubCategory,
   d_CreatedAt, i_Status,i_NotifNo,i_CompSourceId ) values ('$i_docketno', '$i_SPCode', '1', '$CustomerAccount', '$CompTypeName', '$CompNature', '$curretdatetime', '$StatusID', '$notifyno', '$source')";



   mysqli_query($link,$strsqlmasterinsert)or die(mysqli_error());
   
   

   //$inidata = parse_ini_file('icms.ini');
   global $inidata;
   $ALERTMODE=$inidata[ALERTMODE];
   //exit();
   if($ALERTMODE!='0')
   {
   $v_smsString = "New Complaint: DktNO=".$i_docketno.", LOCATION NO=".$locationid.", AREA=".$AreaName.", Bldg=".$houseno." ,COMP TYPE=".$CompTypeName.", NATURE TYPE=".$CompNature; 
   $v_usersmsString = "Your complaint lodged successfully, DktNo is ".$i_docketno; 
   //$strsqlinsert = "Insert into $ProDatabasename.tbl_smsmessages(v_mobileNo, v_smsString, d_timeStamp, d_lastTriedAt, i_retries, i_MaxRetries, i_status, v_category) values ('$v_mobileNo', '$v_smsString', now(), now(), '0', '5', '1', 'web')";   
   //mysqli_query($link,$strsqlinsert);
   



$strmassqlinsert = "Insert into $configdbname.tbl_smsmessages(v_mobileNo, v_smsString, d_timeStamp, d_lastTriedAt, i_retries, i_MaxRetries, i_status, v_category) values ('$v_mobileNo', '$v_smsString', '$curretdatetime', '$curretdatetime', '0', '5', '1', '$ProDatabasename')"; 
   mysqli_query($link,$strmassqlinsert);



   $strmasusersqlinsert = "Insert into $configdbname.tbl_smsmessages(v_mobileNo, v_smsString, d_timeStamp, d_lastTriedAt, i_retries, i_MaxRetries, i_status, v_category) values ('$notifyno', '$v_usersmsString', '$curretdatetime', '$curretdatetime', '0', '5', '1', '$ProDatabasename')"; 
   mysqli_query($link,$strmasusersqlinsert);
   }
   //exit();
   
   }

   return $RegComp = $i_docketno;
   //}
   
   
}




################################
## Find The Service Provider Database Name Here
##
################################
function finduserdatabase($i_SPCode)
{
   global $link;
      /*       tbl_databaseinfo i_SPCode v_DIDNumber v_DatabaseName v_WorkingFolder i_Status v_IPAddress       */
      $strsqltechstaffid="SELECT V_Company_DBName FROM tbl_mst_company WHERE I_CompanyID = '$i_SPCode' AND I_Company_Status = '1' ";
      $rscomp=mysqli_query($link,$strsqltechstaffid);
      if($numrows=mysqli_num_rows($rscomp)>0)
      {
         $rowcomp=mysqli_fetch_array($rscomp);
         return $v_DatabaseName=$rowcomp['V_Company_DBName'];
         
      }
      else
      {
         return false;  
      }
}


function findProvideName($i_SPCode)
{
   global $link;
      /*       tbl_databaseinfo i_SPCode v_DIDNumber v_DatabaseName v_WorkingFolder i_Status v_IPAddress       */ 

      $strsqltechstaffid="SELECT V_CompanyName as V_SPName FROM tbl_mst_company WHERE I_CompanyID = '$i_SPCode' AND I_Company_Status = '1' ";
      $rscomp=mysqli_query($link,$strsqltechstaffid);
      if($numrows=mysqli_num_rows($rscomp)>0)
      {
         $rowcomp=mysqli_fetch_array($rscomp);
         return $V_SPName=$rowcomp['V_SPName'];
         
      }
      else
      {
         return false;  
      }
}

function findProvideLogo($i_SPCode)
{
   global $link;
      /*       tbl_databaseinfo i_SPCode v_DIDNumber v_DatabaseName v_WorkingFolder i_Status v_IPAddress       */ 
      $strsqltechstaffid="SELECT v_SPImage FROM tbl_providerregistration WHERE i_SPCode = '$i_SPCode' AND i_Status = '1' ";
      $rscomp=mysqli_query($link,$strsqltechstaffid);
      if($numrows=mysqli_num_rows($rscomp)>0)
      {
         $rowcomp=mysqli_fetch_array($rscomp);
         return $v_SPImage=$rowcomp['v_SPImage'];
         
      }
      else
      {
         return false;  
      }
}

function areaname($Areaid,$zoneid)
{
   global $link;
   global $ProDatabasename;
   $strsql="SELECT v_AreaName FROM $ProDatabasename.tbl_area WHERE i_AreaID = '".$Areaid."' AND i_ZoneID = '".$zoneid."'";
   $rs=mysqli_query($link,$strsql);
   $numrows=mysqli_num_rows($rs);
   $row=mysqli_fetch_array($rs);
   $v_AreaName=$row['v_AreaName'];
   return $v_AreaName;
}

function password($password,$confirmpassword)
{
   if($password==$confirmpassword)
   {
      return true;
   }
   else
   {
      return false;
   }
 } 
 
/*$arr_status = array();
//$arr_status[0] = '';
$arr_status[1] = 'Pending';
//$arr_status[2] = 'Aborted';
$arr_status[3] = 'Cancelled';
$arr_status[4] = 'Completed';
$arr_status[5] = 'Partially Completed';
$arr_status[6] = 'Relodged Complaint';
$arr_status[7] = 'Stock Not Available';
$arr_status[8] = 'Work Sanction Required';
$arr_status[9] = 'Complaint Not Assigned';
$arr_status[10] = 'Complaint Re-open';
*/
$arr_status = array();
$arr_status[0] = 'Pending';
$arr_status[1] = 'Closed';
$arr_status[2] = 'Cancelled';
$arr_status[3] = 'Resolved';
$arr_status[4] = 'Reopened';
/*$arr_status[4] = 'Completed';
$arr_status[5] = 'Partially Completed';
$arr_status[6] = 'Relodged Complaint';
$arr_status[7] = 'Stock Not Available';
$arr_status[8] = 'Work Sanction Required';
$arr_status[9] = 'Complaint Not Assigned';
$arr_status[10] = 'Complaint Re-open';*/

function PendingReason($preason)
{
   global $link;
   global $ProDatabasename,$db;
   if(!empty($db)){ $ProDatabasename=$db; }
   $strsql="SELECT V_CategoryName FROM $ProDatabasename.tbl_mst_pending_reason WHERE I_CategoryID = '$preason' AND I_CategoryStatus ='1' ";
   $rscomp=mysqli_query($link,$strsql);
   if($numrows=mysqli_num_rows($rscomp)>0)
   {
      $rowcomp=mysqli_fetch_array($rscomp);
      return $pendingreason=$rowcomp['V_CategoryName'];
   }
}

function icmddateformat($datetime)
{
   $timestamp=strtotime($datetime);
   $str = date('d M Y H:i', $timestamp);
   return($str);
}

function icmddatetimeformat($date,$time)
{
   $fulldatetime=$date.' '.$time;
   $timestamp=strtotime($fulldatetime);
   $str = date('d M Y H:i', $timestamp);
   return($str);
}

function getTechPhone($techID)
{
        global $ProDatabasename,$db;
   if(!empty($db)){ $ProDatabasename=$db; }
   
   $strsql="SELECT AtxMobile FROM $ProDatabasename.uniuserprofile WHERE AtxUserID = '$techID' ";
   $rscomp=mysqli_query($link,$strsql);




   if($numrows=mysqli_num_rows($rscomp)>0)
   {
      $rowcomp=mysqli_fetch_array($rscomp);
      return $strTechPhone=$rowcomp['AtxMobile'];
   }
   else
   {
      //$inidata = parse_ini_file('icms.ini');
      global $DefaultMobileNo;
      //return $strTechPhone=$inidata[DefaultMobileNo];



      return $strTechPhone=$DefaultMobileNo;
   }
}

function getAreaName($AreaID)
{
   global $link;
   global $ProDatabasename;
   $strsql="SELECT v_AreaName FROM $ProDatabasename.tbl_area WHERE i_AreaId = '$AreaID' ";
   $rscomp=mysqli_query($link,$strsql);
   if($numrows=mysqli_num_rows($rscomp)>0)
   {
      $rowcomp=mysqli_fetch_array($rscomp);
      return $getAreaName=$rowcomp['v_AreaName'];
   }
}

function getCompType($CompID)
{
   global $link;
   global $ProDatabasename,$db;
   if(!empty($db)){ 
   //$ProDatabasename=$db; 
   
   
   }
   $strsql="SELECT V_CategoryName FROM $ProDatabasename.tbl_mst_category WHERE I_CategoryID = '$CompID' ";
   $rscomp=mysqli_query($link,$strsql);
   if($numrows=mysqli_num_rows($rscomp)>0)
   {
      $rowcomp=mysqli_fetch_array($rscomp);
      return $getCompType=$rowcomp['V_CategoryName'];
   }
}

function getCompnature($CompNatureID,$CompType)
{
   global $link;
   global $ProDatabasename,$db;
   if(!empty($db)){
 //$ProDatabasename=$db; 
   }
// $strsql="SELECT V_SubCategoryName FROM $ProDatabasename.tbl_mst_subcategory WHERE I_SubCategoryID = '$CompNatureID' AND i_CompType = '$CompType'";
   $strsql="SELECT ts.V_SubCategoryName FROM $ProDatabasename.tbl_mst_category c,"
      ." $ProDatabasename.tbl_mst_services s, $ProDatabasename.tbl_assoc_subcategory_category t, $ProDatabasename.tbl_mst_subcategory ts WHERE"
      ." c.I_CategoryID=t.I_CategoryID and s.I_ServiceID=t.I_ServiceID and ts.I_SubCategoryStatus=1 and"
      ." ts.I_SubCategoryID=t.I_SubCategoryID AND ts.I_SubCategoryID='$CompNatureID' AND t.I_CategoryID= '$CompType'";
   $rscomp=mysqli_query($link,$strsql);
   if($numrows=mysqli_num_rows($rscomp)>0)
   {
      $rowcomp=mysqli_fetch_array($rscomp);
      return $getCompnature=$rowcomp['V_SubCategoryName'];
   }
}




function getcomplaintcategorylist()
{
   global $link;
   global $ProDatabasename;
   $strsql="SELECT I_CategoryID as i_CompTypeId, V_CategoryName as v_CompTypeDesc  FROM $ProDatabasename.tbl_mst_category where I_CategoryStatus='1' ORDER BY I_CategoryID ";
   //$strsql="SELECT i_CompTypeId, v_CompTypeDesc  FROM $ProDatabasename.tbl_comptype WHERE i_WebFlag = '1' ORDER BY i_WebOrder ";
   return $rscomp=mysqli_query($link,$strsql);
}


function getcomplaintsubcategorylist($compid)
{
   global $link;
   global $ProDatabasename;
   //$strsubsql="SELECT i_NatureOfComp, v_NatureDesc  FROM $ProDatabasename.tbl_compnature WHERE i_CompType = '$compid' AND i_WebFlag = '1' ORDER BY i_WebOrder ";
   $strsubsql="SELECT ts.V_SubCategoryName as v_NatureDesc, ts.I_SubCategoryID as i_NatureOfComp FROM $ProDatabasename.tbl_mst_category c,"
      ." $ProDatabasename.tbl_mst_services s, $ProDatabasename.tbl_assoc_subcategory_category t, $ProDatabasename.tbl_mst_subcategory ts WHERE"
      ." c.I_CategoryID=t.I_CategoryID and s.I_ServiceID=t.I_ServiceID and ts.I_SubCategoryStatus=1 and"
      ." ts.I_SubCategoryID=t.I_SubCategoryID AND t.I_CategoryID= '$compid'";
   return $rscomp=mysqli_query($link,$strsubsql);
}

function findRegisteredProvideList($findUserRegProvideList)
{
   global $link;
   global $configdbname;
   /*       tbl_providerregistration i_SPCode V_SPName i_Status v_SPImage     */ 
   if($findUserRegProvideList){ $profiler = " AND I_CompanyID IN ($findUserRegProvideList) "; }
   //$strsqltechstaffid="SELECT i_SPCode, V_SPName FROM $configdbname.tbl_providerregistration WHERE i_Status = '1' $profiler ";
   $strsqltechstaffid="SELECT I_CompanyID as i_SPCode, V_CompanyName as V_SPName,v_SPImage  FROM $configdbname.tbl_mst_company WHERE I_Company_Status = '1' $profiler ";
   return $rscomp=mysqli_query($link,$strsqltechstaffid);
}

function findUserRegProvideList($USERID)
{
   global $link;
   global $configdbname;
   /*       tbl_providerregistration i_SPCode V_SPName i_Status v_SPImage     */ 
   $sqluserpro="SELECT v_SPCode FROM $configdbname.`tbl_customerdetails` WHERE `i_UserId` = '$USERID'  ";
   $rsuserpro=mysqli_query($link,$sqluserpro) or die(mysqli_error());
   $row=mysqli_fetch_array($rsuserpro);
   return $row['v_SPCode'];
}

function findDuplicateUser($email,$phone)
{
   global $link;
   global $configdbname;
   /*       tbl_providerregistration i_SPCode V_SPName i_Status v_SPImage     */ 
   $sql = "SELECT * FROM $configdbname.tbl_customerdetails WHERE ( v_EmailId = '$email'  OR  v_MobileNo = '$phone' ) ";
   $stm = mysqli_query($link,$sql);
   return $rows=mysqli_num_rows($stm);
}


function ComplaintsDesc($tid)
{
   global $link;
   global $ProDatabasename;
   $sqldesc="SELECT * FROM $ProDatabasename.`complainthandled` WHERE `ComplaintID` = '$tid'  ";
   $valdesc=mysqli_query($link,$sqldesc) or die(mysqli_error());
   $rowdesc=mysqli_fetch_array($valdesc);
   return $V_UserCompDesc=$rowdesc['CSolution'];
   
}


function getTechStaffName($techID)
{
   global $link;
   global $ProDatabasename;
   $v_TechStaffName='';
   $strsql="SELECT AtxUserName FROM $ProDatabasename.uniuserprofile WHERE AtxUserID = '$techID' ";
   $rscomp=mysqli_query($link,$strsql);
   if($numrows=mysqli_num_rows($rscomp)>0)
   {
      $rowcomp=mysqli_fetch_array($rscomp);
      $AtxUserName=$rowcomp['AtxUserName'];
   }
   else
   {
      $AtxUserName='Not Assigned';
   }
   return $AtxUserName;
}

function getTechStaffList()
{
   global $link;
   global $ProDatabasename;
   $strsql="SELECT AtxUserName as v_TechStaffName FROM $ProDatabasename.uniuserprofile  WHERE AtxUserStatus = '1'";
   return $rscomp=mysqli_query($link,$strsql);
}


function ComplaintsImage($tid)
{
   global $link;
   global $ProDatabasename;
   $sqlimg="SELECT * FROM $ProDatabasename.`compqueue_images` WHERE `I_DocketNo` = '$tid'  ";
   $valimg=mysqli_query($link,$sqlimg) or die(mysqli_error());
   $rowimg=mysqli_fetch_array($valimg);
   return $Image_file=$rowimg['Image_file'];
   
}

function findstringvalue($data)
{
   $data=trim($data);
   $data=stripslashes($data);
   $data=htmlspecialchars($data);
   return($data);
}

function ComplaintsSource($i_CompSourceId)
{
   global $link;
   global $configdbname;
   $sql="select v_SourceDesc from $configdbname.`tbl_compsource` WHERE i_CompSourceId='$i_CompSourceId'";
   $result=mysqli_query($link,$sql);
   $row=mysqli_fetch_array($result);
   return $row['v_SourceDesc'];
   
}

function ComplaintsSourceagent($i_CompSourceId)
{
   global $link;
   global $configdbname;
   $sql="select v_SourceDesc from $configdbname.`tbl_compsource` WHERE i_CompSourceId='$i_CompSourceId'";
   $result=mysqli_query($link,$sql);
   $row=mysqli_fetch_array($result);
   return $row['v_SourceDesc'];
   
}
 

function gettimeslotlist()
{
   global $link;
   global $ProDatabasename;
   $strtimesql="SELECT I_TimeslotID, V_TimeslotName FROM $ProDatabasename.tbl_mst_timeslot WHERE I_CategoryStatus = '1' ORDER BY I_TimeslotID ";
   return $rstimecomp=mysqli_query($link,$strtimesql);
} 

##__________________________________________________________
/*
THIS IS THE  VIEW COMPLAIN LISTING OF CUSTOMER ON DASHBOARD
___________________________________________________________## 
*/
function Complaintstatuslist($i_UserId,$i_Status)
{
   global $link;
   global $configdbname;
   //$i_UserId=$row['i_UserId'];
   $sql="select COUNT(i_UserId) as comcount FROM $configdbname.`tbl_usercompqueue` WHERE i_Status ='$i_Status' AND i_UserId = '$i_UserId'";
   $result=mysqli_query($link,$sql);
   $rows=mysqli_fetch_array($result);
   return $rows['comcount'];

}

function personalinfoname($i_UserId)
{
   global $link;
   global $configdbname;
   $sql="SELECT v_UserName FROM  $configdbname.`tbl_customerdetails` WHERE i_UserId='$i_UserId'";
   $result=mysqli_query($link,$sql);
   $rows=mysqli_fetch_array($result);
   return $rows['v_UserName'];
}
function personalinfomobile($i_UserId)
{
   global $link;
   global $configdbname;
   $sql="SELECT v_MobileNO FROM  $configdbname.`tbl_customerdetails` WHERE i_UserId='$i_UserId'";
   $result=mysqli_query($link,$sql);
   $rows=mysqli_fetch_array($result);
   return $rows['v_MobileNO'];
}
function personalinfoaddress($i_UserId)
{
   global $link;
   global $configdbname;
   $sql="SELECT v_Address1, v_Address2, v_Address3, i_City, i_State , v_pin  FROM  $configdbname.`tbl_customerdetails` WHERE i_UserId='$i_UserId'";
   $result=mysqli_query($link,$sql);
   $rows=mysqli_fetch_array($result);
   return $rows['v_Address1'].' '.$rows['v_Address2'].' '.$rows['v_Address3'].' '.$rows['v_pin'];
   //return $rows['v_Address2'];
}

function personalinfoemailid($i_UserId)
{
   global $link;
   global $configdbname;
   $sql="SELECT v_EmailId  FROM  $configdbname.`tbl_customerdetails` WHERE i_UserId='$i_UserId'";
   $result=mysqli_query($link,$sql);
   $rows=mysqli_fetch_array($result);
   return $rows['v_EmailId'];
}

function accountnumberlist($i_UserId)
{
   global $link;
   global $configdbname;
   $sql="SELECT i_UserId ,`CustomerAccount` FROM $configdbname.`tbl_customerdetails` INNER JOIN  `icmsdesign`.`accounts` ON $configdbname.`tbl_customerdetails`. i_UserId=`icmsdesign`.`accounts`.AccountNumber";
   $result=mysqli_query($link,$sql);
   $rows=mysqli_fetch_array($result);
   return $rows['CustomerAccount']; 
}

$arr_color_chart=array();
$arr_color_chart['0']='#4661EE';
$arr_color_chart['1']='#EC5657';
$arr_color_chart['2']='#1BCDD1';
$arr_color_chart['3']='#8FAABB';
$arr_color_chart['4']='#B08BEB';
$arr_color_chart['5']='#3EA0DD';
$arr_color_chart['6']='#F5A52A';
$arr_color_chart['7']='#23BFAA';
$arr_color_chart['8']='#FAA586';
$arr_color_chart['9']='#EB8CC6';

$arr_color_chart_font=array();
$arr_color_chart_font['0']='#FFF';
$arr_color_chart_font['1']='#000';
$arr_color_chart_font['2']='#FFF';
$arr_color_chart_font['3']='#FFF';
$arr_color_chart_font['4']='#FFF';
$arr_color_chart_font['5']='#FFF';
$arr_color_chart_font['6']='#FFF';
$arr_color_chart_font['7']='#FFF';
$arr_color_chart_font['8']='#FFF';
$arr_color_chart_font['9']='#FFF';


function findRegisteredProvideNOTList($findUserRegProvideList)
{
   global $link;
   global $configdbname;
   /*       tbl_providerregistration i_SPCode V_SPName i_Status v_SPImage     */ 
   if($findUserRegProvideList){ $profiler = " AND I_CompanyID NOT IN ($findUserRegProvideList) "; }
   //$strsqltechstaffid="SELECT i_SPCode, V_SPName FROM $configdbname.tbl_providerregistration WHERE i_Status = '1' $profiler ";
   $strsqltechstaffid="SELECT I_CompanyID as i_SPCode, V_CompanyName as V_SPName,v_SPImage  FROM $configdbname.tbl_mst_company WHERE I_Company_Status = '1' $profiler ";
   return $rscomp=mysqli_query($link,$strsqltechstaffid);
}

$uc_ip="165.232.183.220";

function get_web_page($url) {
    $options = array(
        CURLOPT_RETURNTRANSFER => true,   // return web page
        CURLOPT_HEADER         => false,  // don't return headers
        CURLOPT_FOLLOWLOCATION => true,   // follow redirects
        CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
        CURLOPT_ENCODING       => "",     // handle compressed
        CURLOPT_USERAGENT      => "test", // name of client
        CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
        CURLOPT_TIMEOUT        => 120,    // time-out on response
    ); 

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);

    $content  = curl_exec($ch);

    curl_close($ch);

    return $content;
}

function getjobTitle($id,$db)
{
   global $link;
   $q="select AtxDesignation from $db.uniuserprofile where AtxUserStatus='1' and AtxUserID='$id' ";
   $rs=mysqli_query($link,$q) or die("Error in query11 ".mysqli_error());
   $ress=mysqli_fetch_array($rs);
   $AtxDesignation=$ress['AtxDesignation'];
   return $AtxDesignation;
}

/* Get Display Name using atxGid(jobtitle) - 10-09-2021 :: Farhan*/ 

function get_display_atx($atx)
{
 global $db,$link;
 $sql_atx = "SELECT DisplayName FROM $db.unigroupid WHERE atxGid =$atx";
 $result_atx = mysqli_query($link,$sql_atx);
 $row_atx = mysqli_fetch_assoc($result_atx);
 return $row_atx['DisplayName'];

}
// [21-08-2023] Changes has to be done for New Email and Sms Templates

function include_mail_template($ticketid, $case_type, $data_arr=[]){
   // print_r($data_arr);
   global $db,$link;
   
   if($case_type!="" && $ticketid!="")
   {
      $sql= $link->query("SELECT * FROM $db.tbl_mailformats WHERE MailStatus=1 AND MailTemplateName='$case_type'");
      $row = $sql->fetch_assoc();
      $subject =$row['MailSubject'];
      $greeting =$row['MailGreeting'];
      $body=$row['MailBody'];
      $signature=$row['MailSignature'];
      $expiry=$row['MailExpiry'];

      $name = $data_arr['name'];
      $mobile = $data_arr['mobile'];
      $email = $data_arr['email'];
      $category = $data_arr['category'];
      $depertment = $data_arr['sub_category'];
      $cef_link = $data_arr['cef_link'];
      $nps_link = $data_arr['nps_link'];
      $feedback_link = $data_arr['feedback_link'];
      $chat_feedback_link = $data_arr['chat_feedback_link'];

      $subject = str_replace("%ticketno%", $ticketid, $subject);
      $body= str_replace("%ticketno%", $ticketid, $body);
      $body= str_replace("%name%", $name, $body);
      $body= str_replace("%mobile%", $mobile, $body);
      $body= str_replace("%email%", $email, $body);
      $body= str_replace("%category%", $category, $body);
      $body= str_replace("%depertment%", $depertment, $body);
      $body= str_replace("%cef_link%", $cef_link, $body);
      $body= str_replace("%nps_link%", $nps_link, $body);
      $body= str_replace("%feedback_link%", $feedback_link, $body);
      $body= str_replace("%chat_feedback_link%", $chat_feedback_link, $body);

      $password_user = $data_arr['password_user'];
      $login_link = $data_arr['login_link'];
      $body= str_replace("%password_user%", $password_user, $body);
      $body= str_replace("%login_link%", $login_link, $body);
      $greeting= str_replace("%name%", $name, $greeting);

      $content = $greeting.$body.$signature;
      
      return array('sub' => $subject, 'msg' => $content, 'expiry'=>$expiry) ;
   }
}

function include_sms_template($ticketid, $case_type, $data_arr=[]){
   global $db,$link;
   if($case_type!="" && $ticketid!=""){

      $sql= $link->query("SELECT * FROM $db.tbl_smsformat WHERE smsstatus=1 AND smstemplatename='$case_type'");
      $row = $sql->fetch_assoc();
      $header =$row['smsheader'];
      $footer =$row['smsfooter'];
      $body=$row['smsbody'];
      $expiry=$row['smsexpiry'];

      $name = $data_arr['name'];
      $cef_link = $data_arr['cef_link'];
      $nps_link = $data_arr['nps_link'];
      $feedback_link = $data_arr['feedback_link'];
      $chat_feedback_link = $data_arr['chat_feedback_link'];
      
      $header = str_replace("%name%", $name, $header);
      $header = str_replace("%customer%", $name, $header);
      $body = str_replace("%nps_link%", $nps_link, $body);
      $body = str_replace("%feedback_link%", $feedback_link, $body);
      $body = str_replace("%chat_feedback_link%", $chat_feedback_link, $body);
      $body = str_replace("%cef_link%", $cef_link, $body);
      $body = str_replace("%docket_no%", $ticketid, $body);
      
      $password_user = $data_arr['password_user'];
      $email = $data_arr['email'];
      $login_link = $data_arr['login_link'];
      $body= str_replace("%password_user%", $password_user, $body);
      $body= str_replace("%email%", $email, $body);
      $body= str_replace("%login_link%", $login_link, $body);

      $content=$header.','.$body.$footer;
      
      return array('msg' => $content, 'expiry'=> $expiry ) ;
      
   }//end of casetype
}
/*Aarti-23-11-23
this function for insert email out record in web_email_information_out*/
function insert_emailinformationout($data){
   global $link,$dbname;
   $todaytime=date("Y-m-d H:i:s");
   $Mail = $data['Mail'];
   $from = $data['from'];
   $V_Subject = $data['V_Subject'];
   $V_Content = $data['V_Content'];
   $d_email_date = $data['d_email_date'];
   $todaytime = $todaytime;
   $view = $data['view'];
   $userfile_size = $data['userfile_size'];
   $subjectid = $data['subjectid'];
   $ICASEID = $data['ICASEID'];
   $i_templateid = $data['i_templateid'];
   $v_cc_email = $data['v_cc_email'];
   $error_mail = $data['error_mail'];
   $I_Status = $data['I_Status'];
   $queue_type = $data['queue_type'];
   $sql_sms="insert into $dbname.web_email_information_out(v_toemail, v_fromemail, v_subject,v_body, V_rule,d_email_date, email_type,module, size,subjectid, ICASEID, i_templateid,v_cc_email,v_LastError,I_Status,queue_type) values ('$Mail', '$from', '$V_Subject', '$V_Content', '$d_email_date', '$todaytime', 'OUT', '$view','$userfile_size','$subjectid','$ICASEID','$i_templateid','$v_cc_email','$error_mail','1','$queue_type')";
   $result_sms= mysqli_query($link,$sql_sms) or die("Error in Query".mysqli_error($link));
}
/*
* Date: 06-12-2024
* Author: Aarti Ojha
* Purpose: Send SMS using SMPP service, insert SMS into the database, and fetch/update SMS list.
* Note: Centralized functions for insert/update/delete to maintain consistency and ease of future changes.
*/
function insert_smsmessages($data){
   global $link,$db;
   $caller_id = $data['v_mobileNo'];
   $fname = $data['V_AccountName'];
   $createdBy = $_SESSION['userid'];
   $expiry = $data['i_expiry'];
   $message_text = $data['v_smsString'];
   // Get the current timestamp for the SMS creation time.
    $todayTime = date("Y-m-d H:i:s");
    $send_from = 'LEC'; // Default sender ID.
    $send_to = '00266'.$caller_id;
    // Define the SMS message and status.
    $message = mysqli_real_escape_string($link, $message_text);
    $status = '0'; // Initial status for new SMS entries.0
    // SQL query to insert SMS details into the `sms_out_queue` table.
    $sql = "INSERT INTO $db.sms_out_queue (send_to,send_from,message,create_date,status,ICASEID,created_by,AccountName
            ) VALUES ('$send_to','$send_from','$message','$todayTime','$status','$docket_no','$createdBy'
            ,'$fname')";
    $result_sms= mysqli_query($link,$sql) or die("Error In Query24 ".mysqli_error($link));

}
?>
