<?php 
/**
 * Social Media Channel
 * Author: Aarti Ojha
 * Date: 29-04-2024
 * Description: This file handles this script being used to update customer id if email and phone match or not
 * 
 * Please do not modify this file without permission.
 */
include_once("../../../config/web_mysqlconnect.php"); // Include database connection file 
$query = "select * from $db.web_accounts";  /* get customer id from web_account table */
$select = mysqli_query($link,$query);
echo"-STEP-1 fetching all customer data";echo"<br/>";
while($res = mysqli_fetch_array($select)){
	$email = $res['email'];
	$phone = $res['phone'];
	$AccountNumber = $res['AccountNumber'];
	$qdk = "select * from $db.interaction where ( email='" . $email . "' || mobile='".$phone."') and customer_id IS NULL  order by created_date desc"; 
	echo"-STEP-2 customerid blank in interaction then fecth data";echo"<br/>";
	/* check email and phone match or not */
	$ress = mysqli_query($link, $qdk);
    $num = mysqli_num_rows($ress);    
    if($num>0){
    	echo"<br/>";echo $qdk;echo"<br/>";
    	while ($rows = mysqli_fetch_array($ress)){
	    	$ids = $rows['id'];
	    	$update = "update $db.interaction set customer_id='".$AccountNumber."' where id=".$ids; 
	    	/* update customer id  */
			mysqli_query($link, $update);
			echo"-STEP-3 update customer id in interaction";echo"<br/>";
			echo $update;echo"<br/>";
			echo "update successfully..."; echo"<br/>";echo"<br/>";
		}
    }
}
?>