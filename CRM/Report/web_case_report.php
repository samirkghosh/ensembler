<?php
/***
 * Custome case report page
 * Author: Ritu modi
 * Date: 09-02-2024
 * 
 * Description: This page is for generating a "Case Report".
**/?>

<form name="myform" method="post">
	<span class="breadcrumb_head" style="height:37px;padding:9px 16px">Case</span>
	<div class="style2-table ">
	<div class="table">
	<table class="tableview tableview-2 main-form new-customer">
		<tbody>
			<tr>
				<td class="left boder0-right">
					<label>Customer Name</label>
					<div class="log-case">
						<input type="text" class="select-styl1" name="fname" onkeypress="return isAlphabetKey(event)" id="fname" value="<?=$fname?>">
					</div>
				</td>
				<td width="50%" class="left  boder0-right">
					<label>Case Number</label>
					<div class="log-case">
						<input type="text" class="select-styl1" name="case" oninput="validateNumericInput(this)" id="casee" value="<?=$case?>">
					</div>
				</td>
			</tr>
			<tr>
				<td class="left boder0-right">
					<label>Agent Name</label>
					<div class="log-case">
						<?php
						// Fetching user profile data
              $result=uniuserprofile();
            ?>
						<select name="agent" id="agent" class="select-styl1" style="width:190px">
							<option value="">Select Agent</option>
						<?php 
							while($row=mysqli_fetch_array($result)) {
							$AtxUserID=$row['AtxUserID'];	
							$AtxUserName=$row['AtxUserName'];
							if($AtxUserID == $agent)
							{
							$sel = 'selected';
							}
							else
							{
							$sel = '';
							}
							?>
							<option value='<?=$AtxUserID?>' <?=$sel?>><?=$AtxUserName?></option>
							<? } ?>
						</select>
					</div>
				</td>
				<td width="50%" class="left  boder0-right">
					<?php
					// Determine start and end datetime parameters, defaulting to current month
					$dateRange = get_date_custom_case();

							$startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
								? $_REQUEST['sttartdatetime'] 
								: date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));
							
							$enddatetime = (!empty($_REQUEST['enddatetime'])) 
								? $_REQUEST['enddatetime'] 
								: date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));
							?>
				<label>From
				<input type="text" name="sttartdatetime" class="date_class dob1" value="<?=$startdatetime?>" id="sttartdatetime"></label>
				<label>To
					<input type="text" name="enddatetime" class="date_class dob1" value="<?=$enddatetime?>" id="enddatetime"></label>
				</td>
			</tr>
			<tr>
				<td width="50%" class="left  boder0-right">
					<label>Ticket Status</label>
					<div class="log-case">
						<select name="status" id="status" class="select-styl1" style="width:190px" onchange="if(this.value==3){ $('#closing_remarks_col').css('display','table-row'); }else{ $('#closing_remarks_col').css('display','none'); }">
							<option value="">Select Status</option>
							<?php
								$qry = get_status();
								while($res = mysqli_fetch_row($qry)){
								?>
								<option value="<?=$res[0]?>" <? if($_REQUEST['status']==$res[0]){ echo "selected"; } ?>><?=$res[1]?></option>
								<?php
								}
								?>
							?>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td class="left  boder0-right">
					<div class="log-case" id="Enq_pr" style="display: block;">
						<label>Category</label>
						<select name="category" id="category" class="select-styl1" style="width:190px;">
							<option value="">Select Category</option>
							<?php
								$sourceresult = get_category();
								while($row=mysqli_fetch_array($sourceresult)) {
									$SubI=$row['id'];	
									$subC=$row['category'];
								if($SubI == $category){
								  $sel = 'selected';
								}else{
								   $sel = '';
								}?>
							<option value='<?=$SubI?>'  <?=$sel?>>    <?=$subC?>  </option>
							<?php } ?>
						</select>
					</div>
				</td>
				<td class="left  boder0-right">
					<label>Sub Category</label>
					<select name="subcategory" id="subcategory" class="select-styl1" style="width:190px; ">
						<option value="">Select Sub Category</option>
						<?php
							$sourceresult = get_subcategory($link, $db, $category);
							while($row=mysqli_fetch_array($sourceresult)) {
								$SubI=$row['id'];	
								$subC=$row['subcategory'];
								if($SubI == $subcategory){
									$sel = 'selected';
								}else{
									$sel = '';
								}
								?>
							<option value='<?=$SubI?>'  <?=$sel?>>    <?=$subC?>  </option>
							<?php } ?>
					</select>
				</td>
			</tr>			
			<tr>
				<td class="left border0-left">
			  <label>Complaint Type</label>
			  <select name="comp" id="comp" class="select-styl1" style="width:180px">
					<option value="">Select Complaint Type</option>
				  <?php
					  	$comp_query = get_complaint_type();
					   	if (mysqli_num_rows($comp_query) > 0) {
						 while ($comp_res = mysqli_fetch_array($comp_query)){ ?>
					<option value="<?= $comp_res['id'] ?>" <? if ($comp_res['id'] == $comp) {
							echo "selected";
							} ?>><?= $comp_res['category'] ?></option>
					  	<?php }
					  } ?>
			   </select>
			 </td>	
			 <td class="left  boder0-right">
					<?php
					// Getting complaint source options
						$sourceresult=get_source();
					?>
					<label>Complaint Origin</label>
					<select name="source" id="source" class="select-styl1" style="width:190px">
						<option value="">Select Complaint Origin</option>
						<?php 
						while($row=mysqli_fetch_array($sourceresult)) { ?>
						<option value='<?=$row['id']?>' <?php echo ($source==$row['id']) ? 'selected' : '' ;?> ><?=$row['source']?>  </option>
						<? } ?>
					</select>
				</td>																		
			</tr>
			<tr>
				<td class="left  boder0-right" colspan="2">
					<center>
						<!-- Submit and reset buttons-->
                 <input name="submit" id="submit_Customecase" type="button" value="Run Report" class="button-orange1">
                  <input name="reset" id="reset_Customecase" type="button" value="RESET" class="button-orange1">
					</center>
				</td>
			</tr>
		</tbody>
	</table>
	</div>
	<div class="table">
	<div class="wrapper2">
		<div class="div2" style="width:1800px;">

<div class="table">
 <form name="frmService" method="post">
    <div class="wrapper6">
       <div>
          <table class="tableview tableview-2" id="Customecase_data">
				<thead>
					<tr style="font-size: 12px">
						<th >Case ID</th>
						<th >Created On</th>
						<th >Complaint Origin</th>
						<th >Complainant Type</th>
						<th >Category</th>
						<th >Sub Category</th>
						<th >Remark</th>
						<th >Root Cause</th>
						<th >Corrective Action</th>
						<th >Status</th>
						<th >Resolved On</th>					
					</tr>
				</thead>
				</table>
			</div>
		</div>
	</form>
</div>
</div></div>