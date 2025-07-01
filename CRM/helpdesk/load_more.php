<?php
/**
 * Display Customer Cases for Last 60 Days
 * Author: Aarti
 * Date: 09-04-2024
 * 
 * This script retrieves and displays the last 60 days of cases for a specific customer.
 * If "revert" is set, it limits the display to the most recent 10 cases; otherwise, it shows all records.
 */
include_once("../../config/web_mysqlconnect.php"); // Include database connection
include("../web_function.php"); // Include necessary functions

$id = $_POST['id'];
$customer_id = $_POST['customerid'];
$revert = $_POST['revert'];

// Check if 'revert' has a value to determine query limitations
if($revert!=''){
	$date = date('Y-m-').'01 00:00:00' ;
	$whr_cond= " AND d_createDate >= DATE_SUB('".$date."', INTERVAL 60 DAY) ";  // last 60 days record 
	$sql_cases = "select * from $db.web_problemdefination where vCustomerID='$customer_id' $whr_cond order by d_createDate desc limit 10" ;
	$ticket_query = mysqli_query($link,$sql_cases);
	$ticket_arr = [];
	while($ticket_res = mysqli_fetch_array($ticket_query)){
		$ticket_arr[] =$ticket_res ;
	}
	  if(count($ticket_arr)>0 ): 
		$cnt = 0;
	   foreach ($ticket_arr as $key => $ticket_res): $cnt++;?>				        
	    <tr id="row_<?=$ticket_res['iPID']?>">
	        <td align="center"><?=$cnt?></td>
	        <td align="center"><?=date("d-m-Y H:i:s", strtotime($ticket_res['d_createDate']))?></td>
	        <td align="center"><?=getfname($customer_id)?></td>
	        <td align="center"><a style="text-decoration: none;" href="interaction_view.php?docketid=<?=$ticket_res['ticketid']?>" class="ico-interaction"><?=$ticket_res['ticketid']?></a></td>
	        <td align="center"><?=category($ticket_res['vCategory'])?></td>
	        <td align="center"><?=ticketstatus($ticket_res['iCaseStatus'])?></td>
	    </tr>
	   
	<? endforeach;
	    $id=$ticket_res['iPID'];
	        
	    ?>
	    <tr id="remove_row">
	    	<td colspan="6">
	    		<button id="load_more" data-item="<?php echo $id;?>" class="button-orange1">Load More</button>
	    	</td>
	    </tr>

	<? else: ?>
		<tr style="background: ; color: ;">
	        <td align="center" colspan="6">No Record Found</td>
	    </tr>
	<?endif;?>
                  

<?}else{
$date = date('Y-m-').'01 00:00:00' ;
$whr_cond= " AND d_createDate >= DATE_SUB('".$date."', INTERVAL 60 DAY) ";  // last 60 days record 
$sql_cases = "select * from $db.web_problemdefination where vCustomerID='$customer_id' $whr_cond order by d_createDate desc" ;
$ticket_query = mysqli_query($link,$sql_cases);
$ticket_arr = [];
	while($ticket_res = mysqli_fetch_array($ticket_query)){
		$ticket_arr[] =$ticket_res ;
	}
	if(count($ticket_arr)>0 ):  
		$cnt = 0;
	foreach ($ticket_arr as $key => $ticket_res): $cnt++;?>
	        <tr id="row_<?=$ticket_res['iPID']?>">
                <td align="center"><?=$cnt?></td>
	            <td align="center"><?=date("d-m-Y H:i:s", strtotime($ticket_res['d_createDate']))?></td>
	            <td align="center"><?=getfname($customer_id)?></td>
	            <td align="center"><a style="text-decoration: none;" href="helpdesk/interaction_view.php?docketid=<?=$ticket_res['ticketid']?>" class="ico-interaction"><?=$ticket_res['ticketid']?></a></td>
	            <td align="center"><?=category($ticket_res['vCategory'])?></td>
	            <td align="center"><?=ticketstatus($ticket_res['iCaseStatus'])?></td>
	        </tr>
		       
    <? endforeach;
        $id=$ticket_res['iPID'];
            
        ?>
        <tr id="remove_row">
        	<td colspan="6">
        		<button id="load_less" data-item="<?php echo $id;?>" class="button-orange1">Load Less</button>
        	</td>
        </tr>

    <? else: ?>
    	<tr style="background: ; color: ;">
            <td align="center" colspan="6">No Record Found</td>
        </tr>
    <?endif;?>
              
<?}?>
<!-- Including additional CSS and JS files for UI enhancements -->
<link href="<?=$SiteURL?>public/css/colorbox.css" rel="stylesheet" type="text/css"/>
<script src="<?=$SiteURL?>public/js/jquery.colorbox.js"></script>

<script type="text/javascript">
    $(document).ready(function(){
        // Initialize Colorbox for interaction links in the table
        $(".ico-interaction").colorbox({iframe:true, innerWidth:800, innerHeight:600});
    });
</script>