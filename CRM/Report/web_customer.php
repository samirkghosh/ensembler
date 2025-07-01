<?php
/***
 * Customer Report Page
 * Author: Ritu modi
 *This form is used to generate a report of customer data based on various filter criteria such as sub-county, contact number, and date range.
**/?>

<!-- added this code for increasing the width size  of the created date field [vastvikta][11-02-2025]-->
<style>
    th.created-date {
        width: 150px;  /* Adjust width as needed */
        min-width: 150px;
    }
</style>
<form name="frmcustomer" method="post">
<div class="style2-table" >
	<div class="table">
		<span class="breadcrumb_head" style="height:37px;padding:9px 16px">CUSTOMERS</span>
			<table class="tableview tableview-2 main-form new-customer">
				<tbody>
					<tr>
						<td class="left  boder0-right">
				        <label>County</label>
						<select name="district" id="district" class="select-styl1" style="width:190px;">
							<option value="">Select County</option>
							<?php
								$sourceresult = get_city();
								while($row=mysqli_fetch_array($sourceresult)) {
									$SubI=$row['id'];	
									$subC=$row['city'];
									if($SubI == $district){
										$sel = 'selected';
									}else{
										$sel = '';
									}
								?>
							<option value='<?=$SubI?>'  <?=$sel?>>    <?=$subC?>  </option>
							<?php } ?>
						</select>
					</td>
					<!--	<td class="left  boder0-right">
							<label>Sub County</label>
						<select name="village" id="village" class="select-styl1" style="width:190px;">
							<option value="">Select Sub County</option>
							<?php
							$villages_query = get_Village($district);
                                while($villages_res = mysqli_fetch_array($villages_query)){ ?>
                                 <option value="<?=$villages_res['id']?>" <?=( $villages_res['id'] == $village) ? 'selected' : '' ?>><?=$villages_res['vVillage']?> </option>
                                 <?php }  ?>
						</select>

						</td> 
					</tr>
					<tr> -->
							<?php
							$dateRange = get_date_customer();

							$sttartdatetime = (!empty($_REQUEST['sttartdatetime'])) 
								? $_REQUEST['sttartdatetime'] 
								: date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));
							
							$enddatetime = (!empty($_REQUEST['enddatetime'])) 
								? $_REQUEST['enddatetime'] 
								: date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));
							?>
						<td class="left boder0-left ">
							<label>From
							<input type="text" name="sttartdatetime" class="date_class dob1" value="<?=$sttartdatetime?>" id="sttartdatetime"></label>
							<label>To
							<input type="text" name="enddatetime" class="date_class dob1" value="<?=$enddatetime?>" id="enddatetime"></label>
						</td>
					</tr>
					<tr>
						<td class="left  boder0-left"><label>Contact Number</label>
							<span class="left boder0-left">
							<input name="phone" id="phone" type="text" value="<?=$phone?>" class="input-style1" oninput="validateNumericInput(this)" size="15" >
							</span>
						</td>
						
						<td class="left boder0-left">
						   <center>
						   	    <input name="submit_cust" id="submit_cust" type="button" value="Run Report" class="button-orange1">
                  				<input name="reset_cust" id="reset_cust" type="button" value="RESET" class="button-orange1">
   						   </center>
   						</td>  
					</tr>
				</tbody>
			</table>
		</div>
		<div class="table">
         	<form name="frmService" method="post">
            	<div class="wrapper6">
               	<div>
                  	<table class="tableview tableview-2" id="customer_data">
			 		<thead>
					<tr class="" style="font-size: 12px">
			   			<th >S.No.</th>
			   			<th >Name</th>
			   			<th >Contact No.</th>
			   			<th >Alternate No.</th>
			   			<th >Email</th>
			   			<th >Plot/House No.</th>
			   			<th >Street</th>
			   			<th >County</th>
			   			<th>Facebook Handle</th>
			   			<th >Twitter Handle</th>
			   			<th class="created-date">Created Date</th>
					</tr>
			 		</thead>
			  	</table>
			</div>
		</div>
	</form>
</div>
		 	