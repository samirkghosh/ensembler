<?php
/***
 * Search Customer Ticket
 * Author: Aarti
 * Date: 09-04-2024
 *  This code is used in a web application to Search Customer Ticket
-->
 **/
include_once ("../../config/web_mysqlconnect.php");
$key = $_POST['key'];
$nkey = $_POST['nkey'];
$url = $_POST['url'];
$QUERY_STRING = unserialize($_POST['QUERY_STRING']);
$qs = "";
foreach ($QUERY_STRING as $keyy => $value) {
	$qs .="&$keyy=$value";
}
if($nkey=='customer'){

	$res = mysqli_query($link,"select AccountNumber,fname,phone,email,mobile,country,address,v_Location,language,district,v_Village,twitterhandle,fbhandle,age_grp,gender,priority_user,tpin,company_name, company_registration, customertype, smshandle,whatsapphandle, messengerhandle,instagramhandle ,regional, nationality from $db.web_accounts where fname like '%$key%' or phone like '%$key%' or email like '%$key%'");
	?>
	<!-- close button  -->
	<span class="sticky right" onclick="close_pin()"><img src="<?=$SiteURL?>public/images/close.gif" style="height:15px;width:15px;cursor: pointer;"></span>
	<!-- close button  -->
	<table width="100%" border="1" style="border-collapse: collapse; border-color: #c7c7c7;">
		<?php
		while($rest = mysqli_fetch_array($res)){
			$customer_array = array();
			$customer_array[] = $rest['AccountNumber'];//0
			$name =  explode(' ', $rest['fname']);
		    $first_name=$name[0];
		    $last_name =$name[1];
			$customer_array[] = $first_name;//1
			$customer_array[] = $rest['phone'];//2
			$customer_array[] = $rest['mobile'];//3
			$customer_array[] = $last_name;//4
			$customer_array[] = $rest['email'];//5
			$customer_array[] = $rest['address'];//6
			$customer_array[] = $rest['v_Location'];//7
			$customer_array[] = $rest['district'];//8
			$customer_array[] = $rest['country'];//9
			$customer_array[] = $rest['language'];//10
			$customer_array[] = $rest['fbhandle'];//11
			$customer_array[] = $rest['twitterhandle']; //12
			$customer_array[] = $rest['v_Village'];//13
			$customer_array[] = $rest['age_grp'];//14
			$customer_array[] = $rest['gender'];//15
			$customer_array[] = $rest['priority_user'];//16
			$customer_array[] = $rest['tpin'];//17
			$customer_array[] = $rest['company_name'];//18
			$customer_array[] = $rest['company_registration'];//19
			$customer_array[] = $rest['smshandle'];//20
			$customer_array[] = $rest['regional'];//21
			$customer_array[] = $rest['nationality'];//22
			$customer_array[] = $rest['customertype'];//23
			$customer_array[] = $rest['whatsapphandle'];//24
			$customer_array[] = $rest['messengerhandle'];//25
			$customer_array[] = $rest['instagramhandle'];//26
			$customer_array = implode("||", $customer_array);
			?>
			<tr style="cursor: pointer;" onclick="getCustomerDetails('<?=$customer_array?>');getCustomerTicketHistory('<?=$rest['phone']?>','phone');">
				<td><?=$rest['fname']?></td>
				<td><?=$rest['phone']?></td>
			</tr>
		<?php }
		if(mysqli_num_rows($res)<=0){?>
				<tr><td>No Record!</td></tr>
		<?php } ?>
	</table>

<?php }

if($nkey=='city'){
	$res = mysqli_query($link,"select id,city from $db.web_city where city like '%$key%' and status='1' ; ");?>
	<table width="100%" border="1" style="border-collapse: collapse; border-color: #c7c7c7;">
		<?php
		while($rest = mysqli_fetch_array($res)){ ?>
				<tr style="cursor: pointer;" onclick="$('#city').val('<?=$rest['id']?>'); $('#cityname').val('<?=$rest['city']?>'); $('#search_result_city').css('display','none'); "><td><?=$rest['city']?></td></tr>
		<?php } ?>
	</table>

<?php } ?>

<script>
	/*to close  dialog box :*/
	function close_pin(){
		$("#search_result").css("display","none");
	}
	/* End function */
</script>