<!--
Document: Voice Mails Form with DataTables
Author: Ritu Modi
Date: 22-03-2024

This form displays voice mail records with the ability to filter by start and end datetime and read/unread status.
-->
<!-- added style for background changing on read and unread [vastvikta][28-01-2025]-->
<style>
  table tbody tr.unread-row {
    background-color:#f0f1f0 !important; /* Light grey background */
    font-weight: bold; /* Bold text */
    color: #000; /* Black text */
}

table thead th {
        height: 10px; /* Set the height explicitly */
        line-height: 40px; /* Center text vertically */
        padding: 0; /* Remove default padding */
        text-align: center; /* Center text horizontally */
    }

</style>
<form name="myform" method="post">
	<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Voice Mails</span>
		<div class="style2-table ">
			<div class="table">
				<table class="tableview tableview-2 main-form new-customer">
					<tbody>			
						<tr>
							<td width="50%" class="left  boder0-right">
								<?php
									$startdatetime= ($_REQUEST['sttartdatetime']!='') ? ($_REQUEST['sttartdatetime']) : date("01-m-Y 00:00:00");
									$enddatetime = ($_REQUEST['enddatetime']!='') ? ($_REQUEST['enddatetime'])  : date("d-m-Y 23:59:59");		
									?>
								<label>From
								<input type="text" name="sttartdatetime" class="date_class dob1" id="startdatetime" value="<?=$startdatetime?>"></label>
								<label>To
								<input type="text" name="enddatetime" class="date_class dob1" id="enddatetime" value="<?=$enddatetime?>"></label>
							</td>							
							<!-- <td width="50%" class="left  boder0-right">
								<label>Read / Unread</label>
								<div class="log-case">
									<select name="status" id="status" class="select-styl1" style="width:190px">
									<option value="">Select</option>
									<option value="1" <?=($status == '1') ? 'selected' : ''?>>Read</option>
									<option value="0" <?=($status == '0') ? 'selected' : ''?>>Unread</option>
									</select>
								</div>
							</td> -->
							
							<td class="left  boder0-right">
									<center>
										<input type='submit' name='sub1' value='Submit' class="button-orange1" style="float:inherit;" onclick="dosubmit(1)">
										<?php 
										$VoiceMail_report = base64_encode('VoiceMail_report');
										?>
										<input type="button" 
											class="button-orange1" 
											value="Reset" 
											onclick="window.location.href='VoiceMail_index.php?token=<?php echo $VoiceMail_report; ?>';" 
											style="float:inherit; color:#222; text-decoration:none; " />
									</center>
								</td>
								<td class="left  boder0-right">
									<audio id="player"  controls>
									  <source id="ogg_src" type="audio/mpeg">
									</audio>
								</td>
						</tr>
						<!-- <tr>
						</tr> -->
					</tbody>
				</table>
			</div>
			<div class="table">
				<div class="wrapper2">
					<div class="div2">
					    <?php
						
							$query = view_voicemailreport();
    						$total=mysqli_num_rows($query);
						?>
					<div id="display-error" style="margin-top:5px;">Total Records Found - <?=$total?> </div>
					<div class="box-body table-responsive">
						<table class="tableview tableview-2 table-bordered" style="font-size: 12px">
							<thead class="table-primary">
								<tr>
									<th>S No.</th>
									<th>Caller ID</th>
									<th>Client Name</th>
									<th>Voicemail Time</th>
									<!-- <th>Read/Unread</th> -->
									<!-- <th>Case</th> -->
									<th>Play</th>
								</tr>
							</thead>
							<tbody>
									<?php
									$count = 0;
									if ($total > 0) {
										while ($ticket_res = mysqli_fetch_array($query)) {
											$newFileName = substr($ticket_res['recordingname'], 0, strrpos($ticket_res['recordingname'], "."));
											$file = "/var/www/html/voicemail/IVR/DROP/" . $newFileName . ".WAV";

											if (file_exists($file) && file_exists(SmartFileName_voice($newFileName))) {
												$count++;
												$rowClass = ($ticket_res['flag1'] == '0') ? 'unread-row' : ''; // Add class for status 0
												$web_case_detail = base64_encode('web_case_detail');
												$idd = base64_encode($ICASEID);
												$new_case_manual = base64_encode('new_case_manual');
												$phone = $ticket_res['callerid'];
												?>
												<tr class="<?= $rowClass ?>">
													<td style="text-align: center;"><?= $count ?></td>
													<td>
														<?php if ($_SESSION['VD_login'] != "") { ?>
															<a href="javascript:void(0);" onClick="clickcall_c2c('<?=$ticket_res['callerid']?>', '<?=$count?>', '1', '<?=$_SESSION['VD_login']?>');" style="color:#203f78">
																<?=$ticket_res['callerid']?>
															</a>
														<?php } else { ?>
															<?=$ticket_res['callerid']?>
														<?php } ?>
													</td>
													<td style="text-align: center;"><?= $ticket_res['client_name'] ?></td>
													<td style="text-align: center;"><?= $ticket_res['voicemailtime'] ?></td>

													<!-- <td>
														<?= ($ticket_res['case_id'] == '') 
															? '<a href="helpdesk_index.php?token=' . $new_case_manual . '&voicemailid=' . $ticket_res['id'] . '&phone_number=' . $ticket_res['callerid'] .'&mr=10" target="_blank">Create Case</a>' 
															: '<a href="case_detail_backoffice.php?id=' . $ticket_res['case_id'] . '">' . $ticket_res['case_id'] . '</a>' ?>
													</td> -->
													<td style="text-align: center;">
														<a href="#" class="play" id="<?= $ticket_res['id'] ?>" 
														onClick="onPlayWithStatusUpdate(this.id, '<?= SmartFileName_voice($newFileName) ?>')"
														style="color:#254988">
														Play
														</a> 
													</td>
												</tr>
												<?php
											}
										}
									} else {
										?>
										<tr>
											<td colspan="6">
												<center>No record found</center>
											</td>
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
         $('.date_class').datetimepicker();
        //  function onPlay(id,sourceUrl)
		function onPlay(id,sourceUrl)
         {
			// alert(sourceUrl)
            console.log("called onplay");
            //alert(id+' '+file);
            var audio = $("#player");      
            $("#ogg_src").attr("src", sourceUrl);
            /****************/
            audio[0].pause();
            audio[0].load();//suspends and restores all audio element
            //audio[0].play(); changed based on Sprachprofi's comment below
            audio[0].oncanplaythrough = audio[0].play();
            $.ajax({
                method:"POST",
                url: "VoiceMail/get_voicemail_play.php",
                data:{'id':id},
                dataType:'json',
                success : function(data)
                {
                    console.log('success');
                    console.log(data);
                    if(data.status == 'success')
                    { 
                       console.log('read');
                       //window.location.href="voicemail_report.php";
                    } 
                },
                error : function(error)
                {
                    console.log('error')
                    console.log(error)
                }
            });
         }
        //  added code to update status to read on playing voicemail [vastvikta ][27-01-2025]
         function updateStatus(id) {
            console.log('called1');
                $.ajax({
                    method: "POST",
                    url: "Report/report_function.php",  // PHP file that updates the status
                    data: { 'id': id , 'action' : 'updateStatus'},  // Send the voicemail ID
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == 'success') {
                            console.log('Status updated to 0');
                            // Optionally, you can update the UI here, e.g., change the "Read/Unread" label
                            $('#status_' + id).text('Unread');
                        } else {
                            console.log('Error: ' + data.message);
                        }
                    },
                    error: function(error) {
                        console.log('Error updating status');
                        console.log(error);
                    }
                });
            }
            // [vastvikta ][27-01-2025]
        function onPlayWithStatusUpdate(id, sourceUrl) {
            console.log(id);
            updateStatus(id);

        
            onPlay(id, sourceUrl);
            
        }
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
