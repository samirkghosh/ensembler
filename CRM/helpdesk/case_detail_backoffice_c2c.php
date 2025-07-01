<?php 
/***
 * Backoffice c2c Page
 * Auth:  Aarti Ojha
 * Date:  08-07-2024
 * Description: this file use for fetch data and insert data in database
 * Admin all module Files added and common files added
 * 
*/
include("../web_function.php");
include('web_helpdesk_function.php'); // this file handle common function related ticket data

   
$_SESSION['c2c_url'] = $_SERVER['REQUEST_URI']; // for c2c
$name 		= $_SESSION['logged'];
$logedin_agent 	= 	$_SESSION['logged'];
$vuserid	=   $_SESSION['userid'];
$groupid 	=	$_SESSION['user_group'];
$reginoal_spoc	=	$_SESSION['reginoal_spoc'];// (agent , backoffice , master admin , supervisor): by farhan
$view_only =  false  ;
$queryString = '';
if($groupid=='070000'){
	$queryString =  'AND  id IN (1,2,3,8)'  ;
	$view_only =  true  ;
}
else if($groupid=='060000' || $groupid=='050000' || $groupid=='090000'){
	$queryString = ($groupid=='070000' || $groupid=='060000' || $groupid=='050000' || $groupid=='090000') ? 'AND  id IN (1,2,3,4,8)'  : '' ;
}

$id  =  $_GET['prob_id'];
$ticketid = $id ; 
$docket_no = $ticketid;
$ticket = $ticketid;
   
//This will get information before update for the pourpose of find out the update changes
$id  =  $_GET['prob_id'];
$ticketid = $id ; 
$docket_no = $ticketid;
$resonse = get_ticket_detail($docket_no); // function for fetch ticketid details query
$res = mysqli_fetch_assoc($resonse);
$case_id =    $res['iPID'];
$phone =    $res['phone'];
$mobile=    $res['mobile'];
$email 			= $res['email']; 
$age 			= $res['age_grp']; 
$gender 			= $res['gender']; 
$priority_user = $res['priority_user'];
$source=    $res['i_source'];
$iAssignTo 	=    $res['iAssignTo'];
$name =  explode(' ', $res['fname']);// Split fname into an array of two values seperated by space.
$first_name=$name[0];
$last_name =$name[1];
$address_1= $res['address'];
$address_2= $res['v_Location'];
$district=  $res['district'];
$villages = $res['v_Village'];
$type =     $res['vCaseType'];
$catid=     $res['vCategory'];
$subcatid=  $res['vSubCategory'];
$group=     $res['vProjectID'];

$comp =     $res['complaint_type'];
$remark=    $res['vRemarks'];
$status    =$res['iCaseStatus'];
$languagename=$res['language'];
$customer=$res['vCustomerID'];
$fbhandle=$res['fbhandle'];
$twitterhandle=$res['twitterhandle'];
$backoffice_remark=$res['b5_remark'];
$backoffice_last_remark=$res['b6_remark'];
$status_type_ =$res['iCaseStatus'];
$customer_id =    $res['vCustomerID'];
$source         	=$res['i_source'];
$ticketid_old_case  = $res['ticketid'];
$supervisor_remark  = $res['v_ActionSupervisor'];
$v_OverAllRemark  = $res['v_OverAllRemark'];
$account_no_  = $res['customer_account_no'];

/*new screen related changes-07-09-23*/
$fbhandle = $res['fbhandle'];
$twitterhandle = $res['twitterhandle'];
$business_number = $res['business_number'];
$register_tpin = $res['tpin'];
$passport_number = $res['passport_number'];
$area = $res['area'];
$town = $res['town'];
$whatsappnumber = $res['whatsapphandle'];
$sms_number = $res['smshandle'];
$nationality = $res['nationality'];
$taxtype_id = $res['tax_id'];
$company_name_case = $res['company_name'];
$company_registration = $res['company_registration'];
$root_cause = $res['root_cause'];
$corrective_measure = $res['corrective_measure'];
$regional = $res['regional'];
$customertype = $res['customertype'];
if(!empty($res['d_updateTime'])){
   $last_date = date("Y-m-d H:i:s",strtotime($res['d_updateTime']));
}
else{
   $last_date = 'N/A';
}

$subString = '' ;
$subString2 = "" ;
if ($status_type_ == '1'){   // pending
	$subString2 = "AND id IN (1, 8)";
	$subString =    "AND id NOT IN (3,5)";
}elseif ($status_type_ == '2'){   // drop
   $subString2 = "AND id IN (2)";
   $subString =    "";
}elseif ($status_type_ == '3'){   // close	
   $subString2 = "AND id IN (3,1)";
   $subString =    "";
}else if ($status_type_ == '4'){   // escalte 
   $subString2 = "AND id IN (4,8)";
   $subString  = "AND id NOT IN (3,5,1)";
}else if ($status_type_ == '5'){  // reopen
   $subString2 = "AND id IN (5,8)";
   $subString  = "AND id NOT IN (3,1)";
}else if ($status_type_ == '8'){   // resolved
   // $subString2 = "AND id IN (2,3,8)" ;
   $subString2 = "AND id IN (3,8)";
   $subString  = "AND id NOT IN (5)";
}	
$source = isset($_GET['mr'])  ? $_GET['mr'] : $source;
$category = category($catid); // for fetch category list
$subcategory = subcategory($subcatid); // for fetch sub category list
$mode_ticket = source($source); //for fetch source name
/*Dispostion start*/
// $languagename=$_GET['language'];
?>
	<div class="style2-table">
		<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Case Detail</span>
		<!-- table right panel start -->
		<form method="post" name="viewproblemfrm" id="viewproblemfrm" method="post" enctype="multipart/form-data">
			<input type="hidden" id="action" value="Update_Case" name="action">
			<input type="hidden" name="docket_no" id="docket_no" value="<?= $docket_no ?>">
         <input type="hidden" name="source" id="source" value="<?= $source ?>">
			<div class="table">
				<table width="100%" align="center" border="0" class="tableview tableview-2 main-form">
					<tbody>
							<tr>
                        <td colspan="2" id="show_ticket" style="color:#ffffff;  background:#004b8b82; padding:5px; border:1px solid #fff; text-align:center;display: none;">
                          
                         </td>
                     </tr>
                     <tr align="right">
                        <input type="hidden" name="steamid" id="steamid" value="">
                        <td><strong>
                              <font size="2" color="#003399">Ticket Status : </font>
                              <font size="2" color="#993300"><?= ticketstatus($status) ?></font>
                           </strong></td>
                        <td align="center">
                           <font size="2" color="#003399"><b>Ticket No:</b></font>
                           <font size="2" color="#993300"><b><?= $ticketid ?></b></font>
                        </td>
                        <td align="center">
                           <font size="2" color="#003399"><b>Category:</b></font>
                           <font size="2" color="#993300"><b><?= $category ?></b></font>
                        </td>
                        <td align="center">
                           <font size="2" color="#003399"><b>Sub Category:</b></font>
                           <font size="2" color="#993300"><b><?= $subcategory ?></b></font>
                        </td>
                        <td align="center">
                           <font size="2" color="#003399"><b>Company Name:</b></font>
                           <font size="2" color="#993300"><b><?= $company_name_case; ?></b></font>
                        </td>
                     </tr>
                     <tr>
                        <td align="center">
                           <font size="2" color="#003399"><b>Case Origin:</b></font>
                           <font size="2" color="#993300"><b><?= $mode_ticket ?></b></font>
                        </td>
                        <td align="center">
                           <font size="2" color="#003399"><b>Customer Name:</b></font>
                           <font size="2" color="#993300"><b><?= $res['fname'] ?></b></font>
                        </td>
                        <td align="center" >
                           <font size="2" color="#003399"><b>Last Action Date:</b></font>
                           <font size="2" color="#993300"><b><?= $last_date ?></b></font>
                        </td>
                        <td align="center" >
                           <font size="2" color="#003399"><b>Last Action By:</b></font>
                           <font size="2" color="#993300"><b><?=$row_interaction['created_by']?></b></font>
                        </td>
                        <td align="center">
                           <font size="2" color="#003399"><b>Creation Date and Time:</b></font>
                           <font size="2" color="#993300"><b><?= $res['d_createDate']; ?></b></font>
                        </td>
                     </tr>
					
					</tbody>
				</table>
				<style>
					.new-customer td{ padding:8px 10px !important;}
				</style>
				<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Customer Profile</span>
				<table cellpadding="3" cellspacing="2" width="90%" align="center" border="1" class="tableview tableview-2 main-form new-customer">
	            <tbody>
	               <!-- Start   end on line 1744-->
	               <!-- Modified Form Details by farhan on: 24 nov 2020  -->
	               <input type="hidden" name="customerid" id="customerid" value="<?= $customerid ?>">
				      <input type="hidden" name="PHP_SELF" id="PHP_SELF" value="<?= $_SERVER['PHP_SELF'] ?>">
				      <input type="hidden" name="query_string" id="query_string" value="<?= $query_string ?>">
				      <input type="hidden" name="ticketid" id="ticketid" value="<?= $ticketid ?>">
				      <input type="hidden" name="customerid" id="customerid" value="<?= $customerid ?>">
	               <tr>
	                  <td class="left boder0-left">
	                     <label>Register Number <em>*</em></label>
	                     <div class="log-case">
	                        <input name="phone" id="phone" type="text" value="<?=$phone?>" class="input-style1" onKeyUp="ChkIntOnly(this);NumOnly(this)" maxlength="12">
	                        <? if($_SESSION['VD_login']!="") 
	                                                  {?>
	                        <a href="javascript:void(0);" onClick="clickcall_c2c('<?=$phone?>','<?=$ticketid?>','1','<?=$_SESSION['VD_login']?>');">
	                           <img src="../../universus/img/call_1.png" border="0" style="height:15px;width:15px;margin-left: -18px;margin-top: 5px;"></a>
	                        <? }?>
	                     </div>
	                  </td>
	                  <td  class="left  boder0-left">
	                     <label>Alternate no</label>
	                     <input name="mobile" id="mobile" type="text" maxlength="10" value="<?=$mobile?>" class="input-style1 inputDisabled" onKeyUp="ChkIntOnly(this);NumOnly(this)">
	                  </td>
	               </tr>
	               <tr>
	                  <td class="left boder0-right">
	                     <label>Company Name</label>
	                     <input type="text" name="company_name" id="company_name" value="<?= $company_name_case ?>" class="input-style1">
	                  </td>
	                  <td class="left boder0-left">
	                     <label>Company Registration Number</label>
	                     <input type="text" name="company_registration" id="company_registration" value="<?= $company_registration ?>" class="input-style1">
	                  </td> 
	               </tr>
	               <tr>
	                  <td class="left boder0-right" >
	                     <label>First Name <em>*</em></label>
	                     <div class="log-case">
	                        <? $first_name=str_replace("singlequote", "'", $first_name); ?>
	                        <input name="first_name" id="first_name" maxlength="200" type="text" value="<?=$first_name?>" class="input-style1 inputDisabled">
	                     </div>
	                  </td>
	                  <td class="left boder0-right">
	                     <label>Last Name <em>*</em></label>
	                     <? $last_name=str_replace("singlequote", "'", $last_name); ?>
	                     <div class="log-case">
	                        <input name="last_name" id="last_name" type="text" maxlength="200" value="<?=$last_name?>" class="input-style1 inputDisabled">
	                     </div>
	                  </td>
	               </tr>
	               <tr>
	                  <td class="left boder0-left">
	                    <label>County</label>
	                        <div class="log-case">
	                          <select name="district" id="district" class="select-styl1" onchange="get_villages(this.value)" style="width:180px">
	                              <option value="0">Select County</option>
	                              <?php
	                              $city_query = city_list();
	                              while ($city_res = mysqli_fetch_array($city_query)){?>
	                                 <option value="<?= $city_res['id'] ?>" <? if ($city_res['id'] == $district) {
	                                 echo "selected";
	                                 } ?>><?= $city_res['city'] ?> </option>
	                              <?php } ?>
	                        </div>
	                  </td>
	                  <td class="left boder0-left">
	                  	<label>Sub County</label>
	                        <div class="log-case">
	                           <?php $villages_query = get_Village($district); ?>
	                           <select name="villages" id="villages" class="select-styl1" style="width:180px">
	                              <option value="0">Select Sub County</option>
	                              <?php
	                              if (mysqli_num_rows($villages_query) > 0) {
	                                 while ($villages_res = mysqli_fetch_array($villages_query)){?>
	                                    <option value="<?= $villages_res['id'] ?>" <? if ($villages_res['id'] == $villages) {
	                                          echo "selected";
	                                 } ?>><?= $villages_res['vVillage'] ?> </option>
	                              <?php } } ?>
	                           </select>
	                  		</div>
	                  </td>
	               </tr>
	               
	               <tr>
	                  <td class="left boder0-left">
	                      <label>Priority Customer <em>*</em></label>
	                        <div class="log-case">

	                           <span class="slug"> <input type="radio" name="priority_user" value="1" checked <? if ($priority_user == '1') {
	                           echo "checked";
	                           } ?>> Priority</span>
	                           <span class="slug"> <input type="radio" name="priority_user" value="0" <? if ($priority_user == '0') {
	                           echo "checked";
	                           } ?>> Non Priority</span>
	                        </div>
	                  </td>
	                  <td class="left boder0-left" >
	                     <label>Email</label>
	                     <input type="email" name="email" id="email" value="<?=$email?>" maxlength="100" class="input-style1">
	                  </td>
	               </tr>          
	               <tr>
	                  <td class="left boder0-left">
	                     <label>Nationality</label>
	                     <div class="log-case">
	                        <input name="nationality" id="nationality" type="text" value="<?= $nationality ?>" class="input-style1">
	                     </div>
	                  </td>
	              		<td class="left border0-left">
	                     <label>Gender <em>*</em></label>
	                     <select name="gender" id="gender" class="select-styl1" style="width:180px">
	                        <option value="0">Select Gender</option>
	                        <?php
	                         $gender_query = web_gender();
	                        if (mysqli_num_rows($gender_query) > 0) {
	                           while ($gender_res = mysqli_fetch_array($gender_query)){?>
	                           <option value="<?= $gender_res['value'] ?>" <? if ($gender_res['value'] == $gender) {
	                           echo "selected";
	                           } ?>><?= $gender_res['name'] ?> </option>
	                        <?php }
	                        } ?>
	                     </select>
	              		</td>
	               </tr>
	               <tr>
	               <td class="left boder0-left">
	                  <label>Facebook Handle</label>
	                  <input type="text" name="fbhandle" id="fbhandle" value="<?= $fbhandle ?>" class="input-style1">
	               </td>
	               <td class="left boder0-left">
	                  <label><img src = "../public/images/x-twitter.svg" alt="X" style="width:15px"> Handle</label>
	                  <input type="text" name="twitterhandle" id="twitterhandle" value="<?= $twitterhandle ?>" class="input-style1">
	               </td>
	            </tr>
	            <tr>
	            	<td class="left boder0-left">
	                  <label>SMS Number</label>
	                  <input type="text" name="smshandle" id="smshandle" value="<?= $sms_number ?>" class="input-style1">
	               </td>
	               <td class="left boder0-left">
	                  <label>Whatsapp Number</label>
	                  <input type="text" name="whatsapphandle" id="whatsapphandle" value="<?= $whatsappnumber ?>" class="input-style1">
	               </td>
	            </tr>
	            <tr>
	            	 <td class="left boder0-left">
	                  <label>Address</label>
	                  <textarea name="address_1" id="address_1" class="input-style1" ><?= $address_1 ?></textarea>
	               </td>
	            </tr>

	            </tbody>
	         </table>
			</div>
			<div class="old-customer-simple-table ">
            <span class="breadcrumb_head" style="height:37px;padding:9px 16px">CUSTOMER Information</span>
            <table class="tableview tableview-2 main-form new-customer">
               <tbody>
                  <!-- <tr class="background2">
                                 <th height="44" colspan="2" align="left">Channel Information</th>
                         </tr> -->
                   <tr>
                     <td class="left boder0-left">
                        <label>Complaint Origin<em>*</em></label>
                        <?php
                           $sourceresult = getComplaint();
                        ?>
                        <select name="source" id="source" class="select-styl1" style="width:190px">
                           <option value="0">Select Complaint Origin</option>
                           <? 
                             while($row=mysqli_fetch_array($sourceresult)) { ?>
                           <option value='<?=$row['id']?>' <?php echo ($source==$row['id']) ? 'selected' : '' ;?>>
                              <?=$row['source']?> </option>
                           <? } ?>
                        </select>
                     </td>

                     <td class="left boder0-right">
                        <label>Call <em>*</em></label>
                        <div class="log-case">
                           <span class="slug"> <input class="radio" type="radio" name="call_type" value="real" disabled="disabled" <? if ($call_type=='real' ){echo "checked" ;}?>> Real Call </span>
                           <span class="slug"> <input class="radio" type="radio" name="call_type" value="spam" disabled="disabled" <? if ($call_type=='spam' ){echo "checked" ;}?>> Spam Call </span>
                        </div>
                     </td>

                  </tr>
                 	<tr>
                  	 <td class="left boder0-left">
                        <label>Agent Name</label>
                        <input type="text" readonly="readonly" name="agent_name" id="agent_name" value="<?= $agent ?>" class="input-style1">
                  	</td>
                     <td class="left boder0-left">
                           <label>Language</label>
                           <?php
                            $langresult = getlanguage();  ?>
                           <select name="lang" id="lang" class="select-styl1" style="width:190px">
                              <?php
                              while($row=mysqli_fetch_array($langresult)) { 
                                 $sel = '';
                                 if($languagename == $row['id']):
                                 $sel = "selected";
                                 endif;
                                 ?>
                                 <option value='<?=$row['id']?>' <?=$sel?>>
                                 <?=$row['lang_Name']?> </option>

                              <? } ?>
                           </select>
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
			<div class="old-customer-simple-table ">
				<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Other Information</span>
				<table class="tableview tableview-2 main-form new-customer">
               <tbody>
                  <!-- <tr class="background2">
                                 <th height="44" colspan="2" align="left">Other Information</th>
                             </tr> -->
                  <tr>
                     <td class="left boder0-right">
                        <label>Reasons for calling <em>*</em></label>
                        <div class="log-case">
                           <?
                           $complaint_sql = complaint_type();
                        while ($rows = mysqli_fetch_array($complaint_sql)) {
                           if($rows['slug']=='none')break;
                           ?>
                           <span class="slug"> <input class="radio" type="radio" disabled="disabled" name="type" id="type<?=$rows['id']?>" value="<?=$rows['slug']?>" <?if ($type==$rows['slug']) {echo "checked" ;}?> >
                              <?=$rows['complaint_name']?></span>
                           <? }
                        ?>
                        </div>
                     </td>

                     <td class="left boder0-right">
                        <label>Priority of call <em>*</em></label>
                        <div class="log-case">
                           <span class="slug"> <input class="radio" type="radio" name="priority" value="high" disabled="disabled" <? if ($priority=='high' )
                                                 {echo "checked" ;}?>> High </span>
                           <span class="slug"> <input class="radio" type="radio" name="priority" value="mediam" disabled="disabled" <? if ($priority=='mediam' )
                                                 {echo "checked" ;}?>> Mediam </span>
                           <span class="slug"> <input class="radio" type="radio" name="priority" value="low" disabled="disabled" <? if ($priority=='low' )
                                                 {echo "checked" ;}?>> Low </span>
                        </div>
                     </td>
                  </tr>
                  <tr>
                     <td width="50%" class="left  boder0-left">
                        <label>Category <em>*</em></label>
                        <div class="log-case" id="category_div">
                           <select name="v_category" id="v_category" class="select-styl1 select2" style="width:180px" onChange="web_subcat(this.value);get_department(this.value)">
                              <option value="0">Select Category</option>
                          	<?php
                           $sourceresult=getwebcategory();
                           while($cat_res = mysqli_fetch_array($sourceresult)){                           
                           ?>
	                           <option value="<?=$cat_res['id']?>" <? if($cat_res['id']==$catid){ echo "selected" ; } ?>>
	                              <?=$cat_res['category']?>
	                           </option>
                           <?php } ?>
                           </select>
                        </div>
                     </td>
                     <!-- web_subcat('<?=$catid?>','<?=$subcatid?>'); -->
                     <td width="50%" class="left  boder0-left">
                        <label>Subcategory <em>*</em></label>
                        <div class="log-case" id="subcategory_div">
                           <select name="v_subcategory" id="v_subcategory" class="select-styl1 select2" style="width:180px">
                              <option value="0">Select Sub Category</option>
                              <?
                          $subcat_query = getwebsubcategory($catid);
                           if(mysqli_num_rows($subcat_query) > 0){
                           while($subcat_res = mysqli_fetch_array($subcat_query)){
                           ?>
                           <option value="<?=$subcat_res['id']?>" <? if($subcat_res['id']==$subcatid){ echo "selected" ; } ?>>
                              <?=$subcat_res['subcategory']?>
                           </option>
                              <?php } }
                           ?>
                           </select>
                        </div>
                     </td>
                  </tr>
                  <tr>                           
                     <td width="50%" class="left  boder0-right" >
                        <label>Status of Complaint <em>*</em></label>
                        <div class="log-case">
                           <? ///echo (isset($_GET['id']) && $_GET['id'] !='') ? 'disabled': ''
                        $status = !empty($status) ? $status : '1';
                        ?>
                           <input type="hidden" name="status_type_" value="<?php echo $status ?>">
                           <select name="status_type_2" class="select-styl1" <? echo
                                             (isset($status) && $status=='8' ) ? 'disabled' : 'disabled'
                                             ?> onchange="getfeedback(this.value)" style="width:180px;">
                              <option value="0">Select Status</option>
                              <?
                           $ticketstatus_query = mysqli_query($link,"select id,ticketstatus from $db.web_ticketstatus where status='1' $subString $subString2  ORDER BY ticketstatus ASC");   
                           
                              while($ticketstatus_res = mysqli_fetch_array($ticketstatus_query)):
                           
                           ?>
                              <option value="<?=$ticketstatus_res['id']?>" <? if($ticketstatus_res['id']==$status){ echo "selected" ; } ?>>
                                 <?=$ticketstatus_res['ticketstatus']?>
                              </option>
                              <?endwhile;?>
                           </select>
                        </div>
                     </td>
                     <td width="50%" class="left  boder0-right">
                        <label>Assign Department </label>
                        <div class="log-case">
                           <select name="group_assign" id="group_assign" class="select-styl1 select2" style="width:180px;">
                              <option value="0">Select Department </option>
                              <?
                           $group_query = mysqli_query($link,"select pId,vProjectName from $db.web_projects where i_Status='1' ");
                           while($group_res = mysqli_fetch_array($group_query)):
                           
                           ?>
                              <option value="<?=$group_res['pId']?>" <? if($group_res['pId']==$group){ echo "selected" ; } ?>>
                                 <?=$group_res['vProjectName']?>
                              </option>
                              <?endwhile;?>
                           </select>
                        </div>
                        <div id="show_emails"></div>
                     </td>
                  </tr>
                  <tr>
                     <td class="left border0-left">
                        <label>Root Cause</label>
                        <input type="text" name="root_cause" id="root_cause" value="<?= $root_cause ?>" class="input-style1">
                     </td>
                     <td class="left border0-left">
                        <label>Corrective measure</label>
                        <input type="text" name="corrective_measure" id="corrective_measure" value="<?= $corrective_measure ?>" class="input-style1">
                     </td>
                  </tr>
			 
               </tbody>
            </table>
			</div>	
			<!-- Agent Level View (FCR)-->    
			<?php  if($groupid=='070000' ){?>
			<div class="old-customer-simple-table ">
				<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Action taken by agent</span>
				<table class="tableview tableview-2 main-form new-customer">
					<tbody>
					<tr >
						<td  >
							<label>Agent Remarks <em>*</em> </label>
							<div class="log-case" id=""><br>
								<?=$remark?>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<? } ?>
			<!-- Back Office first Level View (BSL) -->
			<?php  if($groupid=='060000' ){?>
			<div class="old-customer-simple-table ">
			<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Action Taken by Branch Group Officer</span>
				<table class="tableview tableview-2 main-form new-customer">
					<tbody>
					<tr>
						<? //echo "select id,ticketstatus from $db.web_ticketstatus where status='1' $subString $subString2 ORDER BY ticketstatus ASC" ; ?>
						<td class="left   boder0-left" colspan="2" >
							<label>Status of complaint <em>*</em></label>
							<div class="log-case" >
								<select name="status_type_" id="inte_status_type_" class="select-styl1" style="width:180px;" onchange="change_new_status(this.value)">
								<option value="0">Select Status</option>
								<?	
									$ticketstatus_query = mysqli_query($link,"select id,ticketstatus from $db.web_ticketstatus where status='1' $subString $subString2 ORDER BY ticketstatus ASC");
									
									while($ticketstatus_res = mysqli_fetch_array($ticketstatus_query)):
									
									?>
								<option value="<?=$ticketstatus_res['id']?>" <? if($ticketstatus_res['id']==$status){ echo "selected"; } ?>> <?=$ticketstatus_res['ticketstatus']?></option>
								<?endwhile;?>
								</select>
							</div>
						</td>
					</tr>
					<tr>
						<td width="50%" class="left  boder0-right"  >
							<label>Agent Remarks <em>*</em> </label>
							<div class="log-case" id=""><?=$remark?>
							</div>
						</td>
						<td >
							<label>Branch Group Officer Remarks <em>*</em> </label>
							<div class="log-case" id="">
								<?if(!empty($backoffice_remark)): echo $backoffice_remark?>
								<? else: ?>
								<textarea name="backoffice_remark" id="backoffice_remark" type="text" <? echo !empty($backoffice_remark) ? 'readonly': '' ?>  style="margin: 0px;padding: 0.5rem;width: 260px;height: 50px;resize: none;"><?=$backoffice_remark?></textarea>
								<? endif; ?>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<?php } ?>
			<!-- Back Office LAST CALL (BLL)-->
			<?php  if($groupid=='090000' ){?>
			<div class="old-customer-simple-table ">
			<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Action Taken by Branch Manager</span>
				<table class="tableview tableview-2 main-form new-customer">
					<tbody>
			
					<tr>
						<td width="50%" class="left  boder0-right"  >
							<label><strong>Agent Remarks</strong> <em>*</em> </label>
							<div class="log-case" id=""><br><?=$remark?></div>
						</td>
						<td >
							<label><strong>Branch Group Officer Remarks</strong> <em>*</em> </label>
							<div class="log-case" id=""><br><?=$backoffice_remark?></div>
						</td>
					</tr>
					<tr>
						<td class="left   boder0-left" colspan="2" >
							<label>Status of complaint <em>*</em></label>
							<div class="log-case" >
								<select name="status_type_" id="inte_status_type_" class="select-styl1"    style="width:180px;" onchange="change_new_status(this.value)" <?=!empty($backoffice_last_remark)?'disabled':''?> >
								<option value="0">Select Status</option>
								<?	
									/*if($status_type_ == '8' || $status_type_ == '3'){
									$ticketstatus_query = mysqli_query($link,"select id,ticketstatus from $db.web_ticketstatus where status='1' ORDER BY ticketstatus ASC");
									}
									else{
										
									}*/
									
									$ticketstatus_query = mysqli_query($link,"select id,ticketstatus from $db.web_ticketstatus where status='1' $subString $subString2 ORDER BY ticketstatus ASC");
									
									
									while($ticketstatus_res = mysqli_fetch_array($ticketstatus_query)):
									
									?>
								<option value="<?=$ticketstatus_res['id']?>" <? if($ticketstatus_res['id']==$status){ echo "selected"; } ?>> <?=$ticketstatus_res['ticketstatus']?></option>
								<?endwhile;?>
								</select>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="left  boder0-left" >
							<label><strong>Branch Manager Remarks </strong><em>*</em> </label>
							<div class="log-case" id="">
								<?if(!empty($backoffice_last_remark)): echo $backoffice_last_remark?>
								<? else: ?>
								<textarea name="backoffice_last_remark" id="backoffice_last_remark" type="text" <? echo !empty($backoffice_last_remark) ? 'readonly': '' ?>  style="margin: 0px;padding: 0.5rem;width: 260px;height: 50px;resize: none;"><?=$backoffice_last_remark?></textarea>
								<? endif; ?>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<?php } ?>
			<?php  if($groupid=='080000' ){?>
			<div class="old-customer-simple-table ">
			<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Action Taken by Supervisor</span>
				<table class="tableview tableview-2 main-form new-customer">
					<tbody>
				
					<tr>
						<td >
							<label><strong>Back office remark</strong> <em>*</em> </label>
							<div class="log-case" id=""><br><?=$backoffice_remark?></div>
						</td>
						<td colspan="2" class="left  boder0-left" >
							<label><strong>Supervisor  Remarks </strong><em>*</em> </label>
							<div class="log-case" id="">
								<textarea name="supervisor_remark" id="supervisor_remark" type="text" style="margin: 0px;padding: 0.5rem;width: 260px;height: 50px;resize: none;"></textarea>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<?php } ?>
			<?php  if($groupid=='0000' ){?>
			<div class="old-customer-simple-table ">
			<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Action Taken by Administrator</span>
				<table class="tableview tableview-2 main-form new-customer">
					<tbody>
				
					<tr>
						<!--  <td width="50%" class="left  boder0-right"  >
							<label><strong>Agent Remarks</strong> <em>*</em> </label>
							<div class="log-case" id=""><br><?=$remark?></div>
							</td> -->
						<td >
							<label><strong>Remark</strong> <em>*</em> </label>
							<div class="log-case" id=""><br><?=$v_OverAllRemark?></div>
						</td>
						<td colspan="2" class="left  boder0-left" >
							<label><strong>  Remarks </strong><em>*</em> </label>
							<div class="log-case" id="">
								<textarea name="v_OverAllRemark" id="v_OverAllRemark" type="text" style="margin: 0px;padding: 0.5rem;width: 260px;height: 50px;resize: none;"></textarea>
							</div>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<? } ?>
			<!-- Feedback from cutstomer in case case resolved -->
			<center>
				<input type="hidden" name="Action" value="update" />
				<input name="Update" id="update_btn" type="submit" value="Update" class="button-orange1" style="float:inherit;" onclick="return validate_existing()"  />&nbsp;
				<input type="button" value="Print Case" onclick="printdiv('rightpanels');" class="button-orange1" style="float:none;">
			</center>
		</form>
		<input type="hidden" id="save_current_status" value="<?=$status_type_?>">
		<!-- ######################        ADD MORE INTERACTION SCETION FORM SART        ############################ -->     		
		<form name="addInteraction_form" id="addInteraction_form"   enctype="multipart/form-data" method="post" style="display: none" >
			<input type="hidden" name="action" id="ajax_addInteraction" value="ajax_addInteraction">
			<input type="hidden" name="docket_no" id="docket_no" value="<?=$ticketid_old_case?>">
			<input type="hidden" name="rowid" id="rowid" value="0">
			<input type="hidden" name="source_id" id="source_id" value="<?=$source?>">
			<input type="hidden" name="customer_id" id="customer_id" value="<?=$customer_id?>">
			<input type="hidden" name="c_mobile" id="c_mobile" value="<?=$phone?>">
			<input type="hidden" name="c_full_name" id="c_full_name" value="<?=$first_name.' '.$last_name?>">
			<input type="hidden" name="caller_id" value="<?=$phone?>">
			<input type="hidden" name="lead_id" id="lead_id" value="<?=$_GET['lead_id']?>">
			<input type="hidden" name="vendor_lead_code" id="vendor_lead_code" value="<?=$_GET['vendor_lead_code']?>">
			<input type="hidden" name="file" id="file" value="<?=$_GET['file']?>">
			<!-- <input type="hidden" name="status" value="<?=$status_type_?>"> -->

			 <!-- Modifed by farhan on 27-06-2024 -->
         <input type="hidden" name="recording_file" value="<?=$_GET['file']?>">
         <input type="hidden" name="vendor_lead_code" value="<?=$_GET['vendor_lead_code']?>">
         <input type="hidden" name="lead_id" value="<?=$_GET['id']?>">
         <input type="hidden" name="lang" value="<?=$_GET['language']?>">
         <input type="hidden" name="caller_id" value="<?=$_GET['phone_number']?>">
         <!-- End -->

			<div class="old-customer-simple-table">
			<span class="breadcrumb_head" style="height:37px;padding:9px 16px">
				<span style="float:left">Add New remark</span>
				<span style="float:right"><button type="button" onclick="$('#addInteraction_form').hide();" class="button-orange1" style="float: right; padding: 0px;">close</button></span>
		    </span>
				<table class="tableview tableview-2 main-form new-customer">
				
					<tr>
					<? 
						$ticketstatus_query = mysqli_query($link,"select id, ticketstatus from $db.web_ticketstatus where status='1' $subString $subString2 ORDER BY ticketstatus ASC");
						
						
						
						?> 
					<td class="left  boder0-left" colspan="3" >
						<label>Status of complaint <em>*</em></label>
						<div class="log-case" >
							<select name="inte_status_type_" id="inte_status_type_" class="select-styl1"    style="width:180px;" onchange="change_new_status(this.value)">
								<option value="0">Select Status</option>
								<?	
								while($ticketstatus_res = mysqli_fetch_array($ticketstatus_query)):
								
								?>
								<option value="<?=$ticketstatus_res['id']?>" <? if($ticketstatus_res['id']==$status_type_){ echo "selected"; } ?>> <?=$ticketstatus_res['ticketstatus']?></option>
								<?endwhile;?>
							</select>
						</div>
					</td>
					</tr>
					<tr>
					<td colspan="3">
						<? if($groupid == '070000'): ?>
						<label>Remarks/Feedback<em>*</em> </label>
						<? elseif($groupid =='090000'): ?>
						<label>Backoffice Last Level Remarks/Feedback<em>*</em> </label>
						<? elseif($groupid =='060000'): ?>
						<label>Backoffice Remarks/Feedback<em>*</em> </label>
						<? elseif($groupid =='0000'): ?>
						<label>Remarks/Feedback<em>*</em> </label>
						<? endif; ?>
						<div class="log-case">
							<textarea name="interaction_remark" id="interaction_remark" type="text"  style="margin: 0px;padding: 0.5rem;width: 533px;height: 50px;resize: none;"><?=$customer_remark?></textarea>
						</div>
					</td>
					</tr>
					<tr>
					<td colspan="3"> 
						<input type="submit" class="button-orange1" name="remark_button" id="remark_button" value="Save New Remark"> 
						<span id="responseMessage2"></span>
					</td>
					</tr>
				</table>
			</div>
		</form>
		<!-- ######################        ADD MORE INTERACTION SCETION FORM CLOSE        ############################ -->     		
		<!-- ######################        FEEDBACK SCETION FORM SART        ############################ -->     		
		<form name="feedback_form" id="<?=($groupid=="070000" ? 'feedback_form' : 'feedback_form_notallow')  ?>"   enctype="multipart/form-data" method="post" style="display: none;"  >
			<input type="hidden" name="ticket_no" id="ticket_no" value="<?=$ticketid?>">
			<input type="hidden" name="feed_source_id" id="source_id" value="<?=$source?>">
			<input type="hidden" name="feed_customer_id" id="customer_id" value="<?=$customer_id?>">
			<input type="hidden" name="feed_email" id="feed_email" value="<?=$email?>">
			<input type="hidden" name="status_type_" id="feed_current_status_type_" value="<?=$status_type_?>">
			<input type="hidden" name="caller_id" value="<?=$phone?>">
			<input type="hidden" name="lead_id" id="lead_id" value="<?=$_GET['lead_id']?>">
			<input type="hidden" name="vendor_lead_code" id="vendor_lead_code" value="<?=$_GET['vendor_lead_code']?>">
			<input type="hidden" name="file" id="file" value="<?=$_GET['file']?>">
			<div class="old-customer-simple-table">
			<span class="breadcrumb_head" style="height:37px;padding:9px 16px">
				<span style="float:left">Customer Feedback</span>
				<span style="float:right"><button type="button" onclick="$('#feedback_form').hide();" class="button-orange1" style="float: right; padding: 0px;">close</button></span>
		    </span>
				<table class="tableview tableview-2 main-form new-customer">
					
					<tr>
					<td colspan="2">
						<label><input type="radio" name="feedback" value="1"  onchange="set_status_values('1')" > Satisfied </label>
						<label><input type="radio" name="feedback"  value="0" onchange="set_status_values('0')"> No Satisfied </label>
						<label><input type="radio" name="feedback" checked value="2" onchange="set_status_values('2')" > No Response </label>
					</td>
					<td class="left  boder0-right" >
						<label>Status of complaint <em>*</em></label>
						<div class="log-case" >
							<select name="status_type_" id="current_status_type_" class="select-styl1"  disabled="disabled"  style="width:180px;">
								<option value="0">Select Status</option>
								<?
								$ticketstatus_query = mysqli_query($link,"select id, ticketstatus from $db.web_ticketstatus where status='1' AND id NOT IN (1,4) ORDER BY ticketstatus ASC");
								while($ticketstatus_res = mysqli_fetch_array($ticketstatus_query)):
								
								?>
								<option value="<?=$ticketstatus_res['id']?>" <? if($ticketstatus_res['id']==$status){ echo "selected"; } ?>> <?=$ticketstatus_res['ticketstatus']?></option>
								<?endwhile;?>
							</select>
						</div>
					</td>
					</tr>
					<tr>
					<td colspan="3">
						<label>Customer Feedback Remarks <em>*</em> </label>
						<div class="log-case">
							<textarea name="customer_remark" id="customer_remark" type="text"  style="margin: 0px;padding: 0.5rem;width: 533px;height: 50px;resize: none;"><?=$customer_remark?></textarea>
						</div>
					</td>
					</tr>
					<tr>
					<td colspan="3"> 
						<input type="submit" class="button-orange1" name="feedback_button" id="feedback_button" value="Save Feedback"> 
						<span id="responseMessage"></span>
					</td>
					</tr>
				</table>
			</div>
		</form>
			<!-- ######################        FEEDBACK SCETION FORM CLOSE        ############################ -->     		
			<br>					
			<?if(isset($docket_no)): 
				$whr_cond= " ticketid='$key' ";
			
			?>
			<div id="ticket_history_docket" style="max-height: 300px;overflow: scroll;" >
				<?php 
					if($customer_id){
						$ticket_query = gethistory($customer_id);
      				$count = mysqli_num_rows($ticket_query);
					?>
					<table class="tableview tableview-2 main-form new-customer"  >
						<thead>
							<tr class="background2">
							<th align="center" class="boder0-right">Ticket</th>
							<th align="center" class="boder0-right">Type</th>
							<th align="center" class="boder0-right">SubCategory</th>
							<th align="center" class="boder0-right">Status</th>
							<th align="center" class="boder0-right">Created On</th>
							<th align="center" class="boder0-right">Township</th>
							<th align="center" class="boder0-right">View</th>
							<th align="center" class="boder0-right">Action</th>
							</tr>
						</thead>
						<tbody  >
							<? while($ticket_res = mysqli_fetch_array($ticket_query)){?>
							<tr style="background: ; color: ;">
							<td align="center"><?=$ticket_res['ticketid']?></td>
							<!-- <td align="center"><?=getTypeName($ticket_res['vCaseType'])?></td> -->
							<td align="center"><?=$ticket_res['vCaseType']?></td>
							<td align="center"><?=category($ticket_res['vCategory'])?></td>
							<td align="center"><?=ticketstatus($ticket_res['iCaseStatus'])?></td>
							<td align="center"><?=date("d-m-Y H:i:s", strtotime($ticket_res['d_createDate']))?></td>
							<td align="center"><?=township($ticket_res['vProjectID'])?></td>
							<td align="center"><a href="helpdesk/interaction_view.php?docketid=<?=$ticket_res['ticketid']?>" class="ico-interaction2">view</a></td>
							<td align="center">
								<? if($ticket_res['ticketid'] == $ticketid_old_case && $status !='3'){?>
								<button type="button" class="button-orange1" onclick="addmore_interaction('<?=$ticket_res['ticketid']?>', '<?=$ticket_res['vCustomerID']?>', '<?=$ticket_res['i_source']?>', '<?=$ticket_res['iPID']?>')"> <a href="#addInteraction_form"> Add Remark</a></button>
								<? } ;?>
							</td>
							</tr>
							<? } ?>
						</tbody>
					</table>
				<?php } ?>
			</div>
		<?endif;?>	
			<!----START---->
			<? $displayy="none"; if($_POST['callbk']=="1"){ $displayy=""; }else{ $displayy="none"; } ?>
			<div class="old-customer-simple-table ">
				<form method="POST" name="disposeAction" id="disposeAction">
				<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Disposition</span>
					<table class="tableview tableview-2 main-form new-customer">
						<tbody>
						<tr>
							<td width="50%" class="left  boder0-right" >
								<label>Disposition </label>
								<? 
									$qdispo="select V_DISPO,V_DISPOSITION from $db_asterisk.tbl_disposition where I_Status=1 order by V_DISPOSITION asc ;";
									$q=mysqli_query($link,$qdispo); ?>
								<div class="log-case">
									<select class="select-styl1" name="disposition" id="disposition">
									<option value="0">Select Disposition <em>*</em> </option>
									<? while($qq=mysqli_fetch_array($q)) { ?>
									<option value="<?=$qq['V_DISPO']?>" <? if($qq['V_DISPO']==$_POST['disposition']){ echo "selected"; }?> ><?=strtolower($qq['V_DISPOSITION'])?></option>
									<? } ?>
									</select>
									
								</div>
							</td>
							<td width="50%" class="left  boder0-right" colspan="2">
								<label>Remarks <em>*</em> </label>
								<div class="log-case" id="">
									<textarea name="dispose_remark" id="dispose_remark" class="input-style1" style="margin: 0px; width: 552px; height: 90px;"><?=$dispose_remark?></textarea>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="left boder0-right">
								<? //print_r($_SESSION); 
								if($agent=="") $agent=$_SESSION['VD_login'];
								
								//if(isset($ticketid12) && $ticketid12!='') { 
									if($customer_id!="")
									{
										$customerid=$customer_id;
									}else if($customerid!="")
									{
										$customerid=$customer_id;
									}
									?>
								<span style="display:block; text-align:center;" >
								&nbsp;
								<input name="btnDispose" type="submit" value="Dispose" class="button-orange1" onclick="return validate_dispose()" style="float:inherit;"  />
								<input type="hidden" name="caller_id" value="<?=$phone?>">
								<input type="hidden" name="action" value="Dispose_Submit"/>
								<input type="hidden" name="agent" value="<?=$agent?>"/>
								<input type="hidden" name="list_id" id="list_id" value="<?=$_GET['list_id']?>">
								<input type="hidden" name="docket_no" id="docket_no" value="<?=$docket_no?>">
								<input type="hidden" name="campaignForPop" id="campaignForPop" value="<?=$_GET['campaignForPop']?>">
								<input type="hidden" name="lead_id" id="lead_id" value="<?=$_GET['lead_id']?>">
								<input type="hidden" name="vendor_lead_code" id="vendor_lead_code" value="<?=$_GET['vendor_lead_code']?>">
								<input type="hidden" name="file" id="file" value="<?=$_GET['file']?>">
								<input type="hidden" name="customerid" id="customerid_new" value="<?=$customerid?>">
								<input type="hidden" name="file" id="file" value="<?= $_GET['file'] ?>">
                        <input type="hidden" name="docket_no_new" id="docket_no_new" value="">
                        <input type="hidden" name="email" id="email" value="<?= $email ?>">
								</span>
								<!-- Modified by farhan on 27-06-2024 -->
                        <input type="hidden" name="fname" id="fname" value="<?= $first_name?>">
                        <input type="hidden" name="lname" id="lname" value="<?= $last_name ?>">
                        <!-- End -->
								<? // } ?>
							</td>
						</tr>
						</tbody>
					</table>
				</form>
			</div>
			<!--END---------->
			<div class="old-customer-table">
				<h6>Disposition</h6>
				<?
					$qdk_incoming="select * from $db.web_wrapcall where ticketid like '%$ticketid%' order by id desc";
					$ress_incoming=mysqli_query($link,$qdk_incoming);
					$numeqdk_incoming=mysqli_num_rows($ress_incoming);
										?>	
				<div style="height: 100px; overflow-y: auto; width:100% !important; ">
					<table width="685" class="tableview tableview-2 main-form">
						<tbody>
						<tr bgcolor="#dddddd" class="background">
							<td width="10%" align="center">
								<div align="left"><b>S No</b></div>
							</td>
							<td width="21%" align="center">
								<div align="left"><b>Disposition</b></div>
							</td>
							<td width="30%" align="center">
								<div align="left"><b>Start Time</b></div>
							</td>
							<td width="25%" align="center">
								<div align="left"><b>Remarks</b></div>
							</td>
							<td width="14%" align="center">
								<div align="left"><b>Recording</b></div>
							</td>
						</tr>
						<?
							$sno=0;
							while($rowincoming=mysqli_fetch_array($ress_incoming))
							{
								$disposition=$rowincoming['disposition']; 		
								$entry_date=$rowincoming['entry_date']; 			
								$remarks=$rowincoming['remarks'];
								$filename=$rowincoming['filename'];		
								$org_filename=getFileName($filename);
								$filenamed = "http://".$unique_ip.$org_filename;
									
								$sno++;
							?>
						<tr >
							<td width="10%" align="center">
								<div align="left"><b><?=$sno?></b></div>
							</td>
							<td width="21%" align="center">
								<div align="left"><b><?=$disposition?></b></div>
							</td>
							<td width="30%" align="center">
								<div align="left"><b><?=$entry_date?></b></div>
							</td>
							<td width="25%" align="center">
								<div align="left"><b><?=$remarks?></b></div>
							</td>
							<td width="14%" align="center">
								<?php
									if($filename!=""){?>
								<a download="" href="<?=$filenamed?>" target="_blank">
									<!--  <a href="javascript:void(0)" onClick="cl12('<?=$filenamed?>','<?=$sno?>')" > -->
									<img src="images/playsound.png" border="0" align="absmiddle" width="22"/>
								</a>
								<?php }?>	
							</td>
						</tr>
						<? }?>
						<? if($numeqdk_incoming<=0){ ?>
						<tr >
							<td colspan="8" align="center">No record found !!</td>
						</tr>
						<? }?>
						</tbody>
					</table>
				</div>
			</div>
	</div>
<!-- Modal content -->

<div id="processModel" class="modalprocess">
      <div class="modal-content">
      <div class="modal-body">
         <center>
            <h3><img src="<?=$SiteURL?>public/images/loader.gif" style="height:30px;width: 30px;">&nbsp;&nbsp;Processing... Please wait...</h3>
         </center>
      </div>
   </div>
</div>
<script src="<?=$SiteURL?>public/js/backoffice.js"></script>
<script language="javascript" src="<?=$SiteURL?>public/js/new_vision.js"></script>
