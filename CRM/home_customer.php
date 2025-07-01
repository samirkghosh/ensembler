<?php
/**
 * Customer Ticket Page
 * Author: Aarti Ojha
 * Date: 17-12-2024
 * This file handles the display of customer ticket information and provides a live chat option.
 */

// Include database connection file
include("../../config/web_mysqlconnect.php");

// Include common functions file
include("web_function.php");

// Check if the customer is logged in
if (!isset($_SESSION['AccountNumber'])) {
   // Redirect to login page if not logged in
   header("Location:../customer_login.php");
   exit();
}

// Get the logged-in customer's account number
$uid = $_SESSION['AccountNumber'];

// URL for the live chatbox
$url = $base_path.'chatbox?uid=' . $uid;
?>
<style type="text/css">
   /* Styling for the live chat button */
   .chat{
      float: right !important;
      margin-bottom: 10px;
   }
</style>
<body>
   <div class="wrapper">
      <div class="container-fluid">
         <div class="row" style="min-height:90vh">
            <div class="col-sm-2" style="padding-left:0">
                <?php include("includes/sidebar.php"); ?> <!-- Side menu file include -->
            </div>
            <!-- Main Content Area -->
            <div class="col-sm-10 mt-3" style="padding-left:0">
               <div class="rightpanels">
                  <div class="style2-table">
                     <!-- Live Chatbox Button -->
                     <a id="anchorID" href="<?php echo $url;?>" target="_blank" class="chat button-orange1">Live Chatbox</a></li>

                     <!-- Logout Button -->
                     <input name="New" type="button" style="float:right" value="Logout" class="button-orange1" onclick="logoutcall('../web_logout_customer.php')">
                         
                      <div class="table" >
                        <div id="changeservice" style="display: none;">
                           <div class="old-customer-table">
                              <span class="breadcrumb_head" style="height:37px;padding:9px 16px;background:#f5f5f5">
                                 <span class="float:left">
                                    Your Profile
                                 </span>
                                 <span class="float:right">
                                 <form name="frmviewcustomer" id="frmviewcustomer" action="web_customer_update.php"
                                    method="post" novalidate="novalidate">
                                    <input name="customerid" id="customerid" type="hidden" value="<?=$uid?>" class="input-style1" readonly="">
                                    <input name="custedit" id="custedit" type="submit" value="Edit" class="button-orange1 float-right" style="margin-top:-25px">
                                    
                                </form>
                                 </span>
                              </span>

                               <!-- Profile Table -->
                              <table class="table table-stripped table-bordered">
                                 <thead>
                                    <tr>                                      
                                       <th>Name</th>
                                       <th>Email ID</th>
                                       <th>Mobile No</th>
                                       <th>Created Date</th>
                                    </tr>

                                 </thead>
                                 <?php
                                 // Fetch customer details
                                 $query = "SELECT * FROM $db.web_accounts WHERE AccountNumber='$uid'";
                                 $result = mysqli_query($link, $query) or die("Error: " . mysqli_error($link));

                                 $row = mysqli_fetch_assoc($result);
                                 ?>
                                 <tbody>
                                    <tr>
                                       <td><?=$row['fname'] . " " . $row['lname'] ?></td>
                                       <td>
                                          <li><?= $row['email'] ?> 
                                       </td>
                                       <td><?= $row['phone'] ?></td>
                                       <td><?= ($row['createddate'] != '') ? date('d M y - H:i A', strtotime($row['createddate'])) : '' ?></td>
                                    </tr>

                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <!-- Ticket Cases Section -->
                        <form name="home" id="home" action="<?=$_SERVER['PHP_SELF']?>" method="post" class="explain">
                           <span class="breadcrumb_head" style="height:37px;padding:9px 16px;background:#f5f5f5">Cases
                           </span>
                           <table class="table table-striped table-bordered example">
                              <thead>
                                 <tr>
                                    <th>Ticket No.</th>
                                    <th>Category</th>
                                    <th>Subcategory</th>
                                    <th>Department</th>
                                    <th>Mode</th>
                                    <th>Status</th>
                                    <th>Created On</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <tr>
                                 <?php
                                 if (!empty($_SESSION['AccountNumber'])) {
                                       $id = $_SESSION['AccountNumber'];

                                       $condition .= "and vCustomerID='$id'";
                                       $qq = "select * from $db.web_problemdefination where ticketid!='' $condition order by iPID desc";
                                       $total = mysqli_num_rows(mysqli_query($link, $qq));
                                       $q = mysqli_query($link, "$qq") or die(mysqli_error($link));
                                    }
                                    $count = 0;
                                    if ($total > 0) {
                                       while ($ticket_res = mysqli_fetch_array($q)){
                                          $count++; ?>
                                          <tr>
                                             <td><?= $ticket_res['ticketid'] ?></td>
                                             <td><?= category($ticket_res['vCategory'])?></td>
                                             <td><?= subcategory($ticket_res['vSubCategory'])?></td>
                                             <td><?= department($ticket_res['vProjectID'])?></td>
                                             <td><?= source($ticket_res['i_source'])?></td>
                                             <td><?= ticketstatus($ticket_res['iCaseStatus'])?></td>
                                             <td><?= $ticket_res['d_createDate'] ?></td>
                                          </tr>
                                       <?php }
                                    } else { ?>
                                       <tr>
                                          <td colspan="7">
                                             <center>
                                                No record found
                                             </center>
                                          </td>
                                       </tr>
                                 <? } ?>
                              </tr>
                              </tbody>
                           </table>
                        </form>
                     </div>
                     <!-- End Right panel -->
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="footer">
         <? include("includes/web_footer.php"); ?>
      </div>
   </div>
</body>