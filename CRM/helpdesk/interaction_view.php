<?php 
/***
 * Intraction Customer Details
 * Author: Aarti
 * Date: 09-04-2024
 *  This code is used in a web application to  Intraction Customer Details Display list
-->
 **/
include("../../config/web_mysqlconnect.php"); //  Connection to database // Please do not remove this
// fetch user details
include("../web_function.php");
$docketid=$_GET['docketid'];
 $mode = $_GET['mode'];
 $sql="select  a.fname as custname,p.vCustomerID from $db.web_accounts a , $db.web_problemdefination p where a.AccountNumber=p.vCustomerID
  AND ticketid	='".$docketid."' ";
$q=mysqli_query($link,$sql);
$rowC=mysqli_fetch_row($q);
$customerName=$rowC['0'];
 $query="SELECT * from $db.web_case_interaction where caseID ='$docketid' ORDER BY id desc ";
 $result = mysqli_query($link,$query);
 $rec_count = mysqli_num_rows($result);


 include("../includes/head.php");
?> 
<body>
	<div class="container-fluid">
		<h4 class="text-center">Tracking/Interactions (<?=$docketid?>)[<?=ucwords($customerName)?>] </h4>
		<div id="rec" style="text-align:right; height:40px; width:70%; margin:auto;"></div>
	<table class="tableview tableview-2 main-form new-customer">
	
		<tbody>
		<tr class="background">
			<td>Sr.No</td>
			<td>Date::Time</td>
			<td>ActionBy</td>
			<td>Action</td>
            <td>Remark</td>		
			<td>Status</td>
			<td>Mode</td>
			<td>Callback Date::Time</td>
			<? if($mode == 1):?>
			<td>Recording</td>
			<?endif;?>
			</tr>
<?php
if ($rec_count != 0) {
    while ($data = mysqli_fetch_assoc($result)) { 
        $remark = $data['remark'];
        $entry_date = $data['created_date'];

        // Fetch callback time
        $sql2 = "SELECT callback_time FROM $db_asterisk.autodial_callbacks WHERE remark = '$remark' AND entry_time = '$entry_date'";
        $result2 = mysqli_query($link, $sql2);

        $callback_time = 'No Callback'; // Default message
        if ($result2 && mysqli_num_rows($result2) > 0) {
            $data2 = mysqli_fetch_assoc($result2);
            $callback_time = !empty($data2['callback_time']) ? $data2['callback_time'] : 'No Callback'; 
        }

        $sno++;
?>
        <tr>
            <td><?php echo $sno; ?></td>
            <td><?= makeDateInddmmyyyy($data['created_date']) ?></td>
            <td><?= ucfirst($data['created_by']) ?></td>
            <td><?= $data['action'] ?></td>
            <td><?= wordwrap(ucfirst($data['remark']), 25, "<br>\n") ?></td>
            <td><?= ticketstatus($data['current_status']) ?></td>
            <td><?= source($data['mode_of_interaction']) ?></td>
            <td><?= $callback_time ?></td> <!-- Fixed callback display -->

            <?php if ($mode == 1): ?>
                <td>
                    <?php
                    $recording_filename = $data['recording_filename'];
                    $org_filename = getFileName($recording_filename);
                    $filename_r = $base_path . $ip . $org_filename;

                    if (!empty($recording_filename)) {
                    ?>
                        <a download="" href="<?= $filename_r ?>" target="_blank">
                            <img src='../../public/images/playsound.png'>
                        </a>
                    <?php } ?>
                </td>
            <?php endif; ?>
        </tr>
<?php 
    } 
} else { ?>
    <tr>
        <td colspan="8">No Data</td>
    </tr>
<?php } ?>

</tbody>
</table>
</div>
<script language="javascript">

			function cl12(nval,val){ 
				document.getElementById('rec').innerHTML="<embed height='40' width='100%' src='"+nval+"' type='audio/mpeg'>"; 
				return false;                 
			}
		</script> 
</body>
</html>