<?php
/**
 * Account Number Dropdown for Case Creation Page
 * Author: Aarti
 * Date: 09-04-2024
 * 
 * This script retrieves account numbers associated with a provided phone number (RMN) 
 * from the database and displays them in a dropdown menu on the case creation page.
 */
include("web_mysqlconnect.php"); // Database connection

// Retrieve the phone number (RMN) from the POST request
$val = $_POST['val'];

// Query the database for account numbers linked to the provided RMN
$account_no_res = mysqli_query($link,"SELECT account_no from $db.web_account_mapping WHERE rmn_no = '$val'");
?>
<!-- Dropdown label for account numbers -->
<label>Account No<em>*</em></label>
 <select name="account_no_" id="account_no_" class="select-styl1" style="width:180px" onchange="getaccountinput(this.value)">
     <option value="">Select Account No</option>  
        <? 
			while ( $res_=mysqli_fetch_array($account_no_res)) { 
                $account_no_=$res_['account_no'];
				?>

            <option value='<?=$account_no_?>'><?=$account_no_?>  </option>
            <? } ?>
            <option value="0">NA</option>
            <option value="other">Other</option>

    </select>