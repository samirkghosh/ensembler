 <?/***
 * Recent Case section
 * Author: Ritu modi
 * Date: 04-04-2024
 *  This code is used in a web application to display recent cases and their details for users to review or manage.
-->
 **/
?>
<table class="tableview tableview-2">
   <thead>
      <tr>
         <td><strong>Case Id</strong></td>
         <td><strong> Name</strong></td>
      </tr>
   </thead>
   <tbody>
      <?php
              $groupid=$_SESSION['user_group'];
         $rspoc=$_SESSION['reginoal_spoc'];
         $vuserid        =   $_SESSION['userid'];
         if(($groupid=='00000') || ($groupid=='0000')) {
         	$sql = "select p.iPID,a.fname,p.ticketid from $db.web_accounts a , $db.web_problemdefination p where a.AccountNumber=p.vCustomerID order by p.iPID desc limit 0,5; " ; 
         }else if($groupid=='060000') { 	/* When BAck Office  Is Logedin */
         	$sql = "select p.iPID,a.fname,p.ticketid from $db.web_accounts a , $db.web_problemdefination p,  $db.web_project_assigne pa where a.AccountNumber=p.vCustomerID  AND p.vProjectID IN (SELECT project_id FROM $db.web_project_assigne AS pas WHERE pas.user_id  ='".$_SESSION['userid']."' ) GROUP BY p.ticketid order by p.iPID desc limit 0,5 " ;
         }else if($groupid=='090000') { 	/* When BAck Office  Is Logedin */
         	$sql = "select p.iPID,a.fname,p.ticketid from $db.web_accounts a , $db.web_problemdefination p,  $db.web_project_assigne pa where a.AccountNumber=p.vCustomerID  AND p.iAssignTo IN ($vuserid) GROUP BY p.ticketid order by p.iPID desc limit 0,5 " ;
         }else{	
         	$sql = "select p.iPID,a.fname,p.ticketid  from $db.web_accounts a , $db.web_problemdefination p where a.AccountNumber=p.vCustomerID  order by p.iPID desc limit 0,5; " ;
         }
         $q = mysqli_query($link,$sql);
		  $count = mysqli_num_rows($q);
         if($count > 0 ){
            while($res = mysqli_fetch_array($q)){
               $ticket = $res['ticketid'];
               $id = $res['iPID'];
   				?>
   			 <tr>
   				<td><a href="javascript:void(0);" onclick="return check_working_status('<?php echo $ticket; ?>', '<?php echo $id; ?>')" style="color: <?php echo $color_code; ?>; font-weight: 700;"><?php echo $ticket; ?></a>

               </td>
   				<td><?=$res['fname']?></td>
   			 </tr>
   			 <?php }
         }else{ ?>
			 <tr>
				<td colspan='2'>No record found</td>
			 </tr>
			<?php }?>
   </tbody>
</table>
<!-- Action Due By-->
<!-- Action Due By-->