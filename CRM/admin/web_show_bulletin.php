<?php 
/**
 * Bulletin list Page
 * Author: Ritu modi
 * Date: 12-03-2024
 * This page is used for managing sub bulletins. It displays a list of existing sub bulletins in a table format. Users, particularly administrators, can create new sub bulletins, edit existing ones, or delete them.
 */
// Encode 'Edit_Bulletin' to base64 for token generation
$Edit_Bulletin = base64_encode('Edit_Bulletin');
// Include common functions
include_once("common_function.php");
?>
<!-- Bulletin Form -->
<form name="frmService" action="" method="post">
    <!-- Bulletin Section -->
    <div class="style-table">
        <div class="style-title">
            <h3>Sub Bulletin </h3><!-- Title -->
            <p><!-- Button for creating a new bulletin -->
                &nbsp;
                <a href="admin_index.php?token=<?php echo $Edit_Bulletin;?>" class="button-orange1">New Bulletin</a>
            </p>
        </div>
        <table class="tableview tableview-2 table_bulletin" id="admin_table"><!-- Bulletin Table -->
              <thead>
                <tr class="background">
                   <td align="left">S.No.</td>
                   <td align="left">Message</td>
                   <td align="left">Start Date</td>
                   <td align="left">End Date</td>
                   <td align="left">Created By</td>
                   <td align="left">Msg Type</td>
                   <td align="left">Action</td>
                </tr>
                </thead>
                <tbody>
                <?php 
                    $Bulletins = new common_function; // Get all bulletins
                    $res = $Bulletins->gell_all_bulletin();// Get the number of rows returned       
                    $numrow=mysqli_num_rows($res);// Set style for displaying table based on number of rows
                    $show = 'display:none'; // Check if there are bulletins
                    if($numrow==0){
                        $show = 'display:';
                    }else{
                      while($row_doc=mysqli_fetch_array($res)){       // Loop through each bulletin
                        // Extract bulletin details
                         $bulletin_id  = $row_doc['id'];
                         $Message      = $row_doc['Message'];
                         $d_startDate  = $row_doc['d_startDate'];
                         $d_endDate    = $row_doc['d_endDate'];

                         $new_Bulletin = base64_encode('new_Bulletin');
                         // Encode bulletin id for URL
                         $id = base64_encode($row_doc['id']);

                        if($row_doc['v_createdBy'] == '1'){
                          $v_createdBy = 'Admin';
                        }
                        if($row_doc['i_msgType'] == '0'){
                          $i_msgType = 'Normal';
                        }else{
                          $i_msgType = 'Important';
                        }
                        #background color
                        $count=$count+1;
                        if($count%2==0){$bgcolor='white';}else{$bgcolor='efefef';}
                        #end of background color
                        ?><!-- Bulletin Row -->
                        <tr class="row_bulletin" id="<?=$row_doc['id']?>">
                           <td align="left"><?php echo $count ?></td>
                           <td align="left" class="message_text"><?php echo $Message?></td>
                           <td align="left"><?php echo $d_startDate?></td>
                           <td align="left"><?php echo $d_endDate?></td>
                           <td align="left"><?php echo $v_createdBy?></td>
                           <td align="left"><?php echo $i_msgType?></td>
                           <td> <!-- Edit, delete Bulletin Action -->
                              <a href="javascript:void(0)" onclick="window.location.href='admin_index.php?token=<?php echo $Edit_Bulletin;?>&id=<?php echo $id ?>';"> <img src="<?php echo $SiteURL?>public/images/edit-icon.png" border="0" alt="Edit">
                              </a>
                              <a href='javascript:void(0);' class="delete_bulletin" data-id="<?php echo $row_doc['id']?>"><img src="<?php echo  $SiteURL ?>public/images/delete-icon.png" border="0" alt="delete"></a>
                           </td>
                        </tr>
                      <?php 
                    } 
                  }
                  ?>
                 </tbody>
            </table>
    </div>
</form>
<script src="<?=$SiteURL?>/public/js/master_service.js"></script>
