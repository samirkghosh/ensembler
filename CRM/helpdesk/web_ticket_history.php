<?php
/***
 * Ticket History 
 * Author: Aarti
 * Date: 04-04-2024
 *  This code is used in a web application to display Ticket History  and their details for users to review or manage.
-->
 **/
include_once ("../../config/web_mysqlconnect.php");
include("../web_function.php");
$key = $_POST['key'];
$nkey = $_POST['nkey'];
$url = $_POST['url'];
// Get All Cases of the customer 
if(isset($_POST['QUERY_STRING']) && $_POST['QUERY_STRING'] =='getdata'){
	if($nkey=='docket_no'){
	 	$whr_cond= " ticketid='$key' ";
	 	$query = "select vCustomerID from $db.web_problemdefination where $whr_cond  " ;
		$res = mysqli_fetch_row(mysqli_query($link,$query));
		$customerid=$res[0];
		$name=getcustomers($customerid)[0]['fname'];	// get name of customer
	}
	if($nkey=='phone'){
		 $query = "select AccountNumber, fname from $db.web_accounts where phone='$key' " ;
		$res = mysqli_fetch_row(mysqli_query($link,$query));
		$customerid=$res[0];
		$name=$res[1];
	}
	$date = date('Y-m-d').' 00:00:00' ;
	$whr_cond= " vCustomerID='$customerid'  AND d_createDate >= DATE_SUB('".$date."', INTERVAL 60 DAY) ";  // last 60 days record 
	 $sql_query = "select iPID,d_createDate, ticketid, vCategory, iCaseStatus from $db.web_problemdefination where $whr_cond order by d_createDate desc limit 10" ;/*By farhan on 09-04-2021*/
	$ticket_query = mysqli_query($link,$sql_query);
	$arr = [];	
	if(mysqli_num_rows($ticket_query) > 0){
		$loop = 0 ; 
		$token =  base64_encode('web_case_detail');
        
		while($ticket_res = mysqli_fetch_assoc($ticket_query)){
			$mid = base64_encode($ticket_res['ticketid']);
			$tickethtml = '<a href="helpdesk_index.php?token='.$token.'&id='.$mid.'"  target="_blank">'.$ticket_res['ticketid'] .'</a>';

			//print_r($ticket_res);
			$arr[$loop]['iPID'] 	= $ticket_res['iPID'];
			$arr[$loop]['date'] 	= date("d-m-Y H:i:s", strtotime($ticket_res['d_createDate'])) ; 
			$arr[$loop]['name'] 	= wordwrap($name, 12, "<br>\n") ; 
			$arr[$loop]['ticketid'] = $tickethtml; 
			$arr[$loop]['caetgory'] = category($ticket_res['vCategory']) ; 
			$arr[$loop]['status'] 	= ticketstatus($ticket_res['iCaseStatus']);
			$loop++; 
			// print_r($ticket_res);
		}	
	}
	echo json_encode($arr);die();	
}
?>
<link href="<?=$SiteURL?>public/css/colorbox.css" rel="stylesheet" type="text/css"/>
<script src="<?=$SiteURL?>public/js/jquery.colorbox.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$(".ico-interaction2").colorbox({iframe:true, innerWidth:800, innerHeight:600});
	});
</script>
<?php
$QUERY_STRING = unserialize($_POST['QUERY_STRING']);
if($nkey=='docket_no'){
	 $whr_cond= " ticketid='$key' ";
}else{
	if($nkey=='customer_id'){
		$whr_cond= " vCustomerID='$key' ";
	}
	if($nkey=='phone'){
		$query = "select AccountNumber from $db.web_accounts where phone='$key' " ;
		$res = mysqli_fetch_row(mysqli_query($link,$query));
		$customerid=$res[0];
		$whr_cond= " vCustomerID='$customerid' ";
	}
}
if(!empty($nkey) && !isset($_POST['left']) ){
	$ticket_query = mysqli_query($link,"select * from $db.web_problemdefination where $whr_cond order by d_createDate desc limit 15; ");
	if($nkey=='docket_no'){
		$ticket_query = mysqli_query($link,"select * from $db.web_problemdefination where $whr_cond order by d_createDate desc limit 15; ");
	}else{
		$ress = mysqli_fetch_array($ticket_query);		 		 
		$customerid=$ress['vCustomerID'];
		$ticket_query = mysqli_query($link,"select * from $db.web_problemdefination where vCustomerID='$customerid' order by d_createDate desc limit 15; ");
	}	
	if(mysqli_num_rows($ticket_query) > 0){?>
		<div style="max-height: 300px; overflow: scroll;">
			<table class="tableview tableview-2 main-form new-customer" >
				<tbody>
				  <tr class="background">
					<td align="center" class="boder0-right">Case Id</td>
					<td align="center" class="boder0-right">Type</td>
					<td align="center" class="boder0-right">SubCategory</td>
					<td align="center" class="boder0-right">Status</td>
					<td align="center" class="boder0-right">Created On</td>
					<td align="center" class="boder0-right">Township</td>
					<td align="center" class="boder0-right">Interaction</td>
					<td align="center" class="boder0-right">Action</td>
				</tr>			
				<?php
				$token =  base64_encode('web_case_detail');
				while($ticket_res = mysqli_fetch_array($ticket_query)){
					$mid = base64_encode($ticket_res['ticketid']);
					$tickethtml = '<a href="helpdesk_index.php?token='.$token.'&id='.$mid.'"  target="_blank">'.$ticket_res['ticketid'] .'</a>';
					?>
					<tr style="background:; color:;">			  
						<td align="center" ><?=$tickethtml?></td>
						<td align="center" ><?=$ticket_res['vCaseType'];?></td>
						<td align="center" ><?=subcategory($ticket_res['vSubCategory'])?></td>
						<td align="center" ><?=ticketstatus($ticket_res['iCaseStatus'])?></td>
						<td align="center" ><?=date("d-m-Y H:i:s", strtotime($ticket_res['d_createDate']))?></td>
						<td align="center" ><?=township($ticket_res['vProjectID'])?></td>
						<td align="center" ><a href="helpdesk/interaction_view.php?docketid=<?=$ticket_res['ticketid']?>" class="ico-interaction2">view</a></td>
						<td align="center">
			        		<?php if($ticket_res['ticketid'] == $key && $nkey=='docket_no' && $ticket_res['iCaseStatus'] !=3 ){?>
			        		<button type="button" class="button-orange1" onclick="addmore_interaction('<?=$ticket_res['ticketid']?>', '<?=$ticket_res['vCustomerID']?>', '<?=$ticket_res['i_source']?>', '<?=$ticket_res['iPID']?>')" style="color: #222; text-decoration: none;">Add Remark</button>
			        		<?php }?>
			        	</td>
					</tr>
					<?php }?>			
				</tbody>
			</table>
		</div>
	<?php 
	}
}		
?>
<?php if(isset($_POST['left'])){
	$sql = "select * from $db.web_problemdefination where $whr_cond order by d_createDate desc limit 5; " ;
	$ticket_query1 = mysqli_query($link,$sql);
	if($nkey=='docket_no' || $nkey=='customer_id'  ){
		$ress1 = mysqli_fetch_array($ticket_query1);		 		 
		$customerid1=$ress1['vCustomerID'];
		$ticket_query2 = mysqli_query($link,"select * from $db.web_problemdefination where vCustomerID='$customerid1' order by d_createDate desc limit 5; ");
	}		 		 
 ?>
	<table class="tableview tableview-2 main-form new-customer" >
		<tbody>
		  <tr class="background2">
			<th align="center" class="boder0-right">Ticket</th>
			<th align="center" class="boder0-right">Status</th>
			<th align="center" class="boder0-right">&nbsp;</th>
		</tr>
		<?php
		while($ticket_res1 = mysqli_fetch_array($ticket_query2)){?>
			<tr style="background:; color:;">			  
				<td align="center" ><?=$ticket_res1['ticketid']?></td>
				<td align="center" ><?=ticketstatus($ticket_res1['iCaseStatus'])?></td>
				<td align="center">
	        		<?php if($ticket_res1['ticketid'] == $key && $nkey=='docket_no' && $ticket_res1['iCaseStatus'] !=3 ){?>
	        		<!-- <button type="button" class="button-orange1" onclick="addmore_interaction('<?=$ticket_res1['ticketid']?>', '<?=$ticket_res1['vCustomerID']?>', '<?=$ticket_res1['i_source']?>', '<?=$ticket_res1['iPID']?>')">Add More </button> -->
	        		<a href="#new_remark" style="cursor:pointer" onclick="addmore_interaction('<?=$ticket_res1['ticketid']?>', '<?=$ticket_res1['vCustomerID']?>', '<?=$ticket_res1['i_source']?>', '<?=$ticket_res1['iPID']?>')" data-toggle="tooltip" title="Add Remark"><i class="fa fa-plus" aria-hidden="true"></i></a>
	        		<?php }?>
	        	</td>
			</tr>
			
			<?php
		}
		?>	
		</tbody>
	</table>
<?php }?>
