<!--
Document: Misscall Form with DataTables
Author: Ritu Modi
Date: 03-04-2024

This form displays missed call records with the ability to filter by start and end datetime, phone number, and status.
-->
<form name="myform" method="post">
<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Misscall</span>
	<div class="style2-table ">
		<div class="table">
        <table class="tableview tableview-2 main-form new-customer">
            <tbody>
                <tr>
                    <td width="50%" class="left boder0-right">
                        <?php 
                            // Setting default start and end datetime if not provided in the request
                            $startdatetime = isset($_REQUEST['sttartdatetime']) && $_REQUEST['sttartdatetime'] != '' 
                                            ? $_REQUEST['sttartdatetime'] 
                                            : date("01-m-Y 00:00:00");
                            $enddatetime = isset($_REQUEST['enddatetime']) && $_REQUEST['enddatetime'] != '' 
                                        ? $_REQUEST['enddatetime'] 
                                        : date("d-m-Y 23:59:59");
                            // Capturing submitted status value
                            $status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
                        ?>
                        <label>From
                            <input type="text" name="sttartdatetime" class="date_class dob1" id="startdatetime" value="<?=$startdatetime?>">
                        </label>
                        <label>To
                            <input type="text" name="enddatetime" class="date_class dob1" id="enddatetime" value="<?=$enddatetime?>">
                        </label>
                    </td>
                    <td width="25%" class="left boder0-right">
                        <label>Call From</label>
                        <div class="log-case">
                            <input type="text" name="phone_number" id="phone_number" class="select-styl1" value="<?=isset($_REQUEST['phone_number']) ? $_REQUEST['phone_number'] : ''?>">
                        </div>
                    </td>
                    <td width="50%" class="left boder0-right">
                        <label>Status</label>
                        <div class="log-case">
                            <select name="status" id="status" class="select-styl1">
                                <option value="">Select Status</option>
                                <option value="AGENT DROP" <?= $status == 'AGENT DROP' ? "selected" : "" ?>>AGENT DROP</option>
                                <option value="DROP" <?= $status == 'DROP' ? "selected" : "" ?>>SYSTEM QUEUE DROP</option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" name="sub1" value="Submit" class="button-orange1">
                        <?php $misscall_report = base64_encode('misscall_report'); ?>
                        <input type="reset" name="reset" value="Reset" onclick="window.location.href='VoiceMail_index.php?token=<?=$misscall_report?>';" class="button-orange1" id="reset_button">
                    </td>
                </tr>
            </tbody>
        </table>
<!-- updated the code for   displaying status in the same row as other table column instead of the dropdown [vastvikta][16-12-2024] -->
                </div>
                <div class="table">		
                    <div class="wrapper2">
                        <div class="div2">
                            <?php
                            // Fetch missed calls data
                            $result = fetchMissedCalls();
                            $total=mysqli_num_rows($result); // Counting the number of records
                            ?>
                        <!-- Display total records found -->
                        <div id="display-error" style="margin-top:5px;">Total Records Found - <?=$total?> </div>				
                        <div class="box-body table-responsive">
            <table class="table table-striped table-bordered example">
                <thead>
                    <tr style="font-size: 12px">
                        <th>S.No.</th>
                        <th>Phone No</th>
                        <th>DID Name</th>
                        <th>Start DateTime</th>
                        <th>Status</th>
                        <th style="width:1%;"></th> <!-- Added Agent Status Column -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 0;
                    if ($total > 0) {
                        while ($ticket_res = mysqli_fetch_array($result)) {
                            $count++;
                    ?>
                            <tr id="row_<?=$count?>"> <!-- Assign an ID to each row -->
                                <td><?=$count?></td>
                                <td>
                                    <?php if ($_SESSION['VD_login'] != "") { ?>
                                        <a href="javascript:void(0);" onClick="clickcall_c2c('<?=$ticket_res['phone_number']?>', '<?=$count?>', '1', '<?=$_SESSION['VD_login']?>');" style="color:#203f78">
                                            <?=$ticket_res['phone_number']."<code> (".$ticket_res['misscalls'].")</code>"?>
                                        </a>
                                    <?php } else { ?>
                                        <?=$ticket_res['phone_number']."<code> (".$ticket_res['misscalls'].")</code>"?>
                                    <?php } ?>
                                </td>
                                <td><?=$ticket_res['did_name']?></td>
                                <td><?=$ticket_res['call_date']?></td>
                                <td><?=($ticket_res['status'] == 'DROP') ? 'SYSTEM QUEUE DROP' : $ticket_res['status']?></td>
                            
                            </tr>
                    <?php }
                    } else { ?>
                        <tr>
                            <td colspan="8" align="center">No record found !!</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        </div>
			</div>
		</div>
	</div>
</form>

<script type="text/javascript">
    // Function to handle click to call functionality
   function clickcall_c2c(mobile,cid,trunk,agent)
    {
        // get agent status from autodial_live_agent starts
        $.ajax({
            url: "../../../../universus/checkAgentStatus.php",
            type: "POST",
            data: {'user':agent,
            },
            async:true,
            crossDomain:true,
            success: function(result){
            console.log('Agent status:'+result);
            // Check if agent is not in READY or REST mode
            if (result!="READY" && result!="REST") {
            var userWidth = screen.availWidth;
            var userHeight = screen.availHeight;
            var popW;
            var popH;
            var leftPos;
            var topPos;
            popW = 500;
            popH = 260;
            settings = 'modal,scrollBars=no,resizable=no,toolbar=no,menubar=no,location=no,directories=no,';
            leftPos = (userWidth - popW) / 2,
            topPos = (userHeight - popH) / 2;
            settings += 'left=' + leftPos + ',top=' + topPos + ',width=' + popW + ', height=' + popH + '';  
            // Open click to call popup     
            window.open("../../../universus/clicktocall_popup.php?V_Dial_Number="+mobile+"&cid="+cid+"&trunk="+trunk+"&agent="+agent, "Clicktocall", settings, 'true'); 
            }else{
                alert("You can't make call in READY mode or in Rest Break\n\nPlease chose Preview mode  to initiate a Dial Out");
            }
            }
            // get agent status from autodial_live_agent ends
        });
    }
</script>
