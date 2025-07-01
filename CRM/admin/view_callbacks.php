<?php
/***
 * Auth: Vastvikta Nishad
 * Date:  16 Feb 2024
 * Description: To Display Callbacks
 * 
*/
?><div class="style2-table">
    <div class="style-title">
        <h3>Callbacks</h3>
    </div>
    <div id="success"></div>
    <form name="frmService" action="" method="post">
        <div class="table" id="SRallview"> 
            <div class="">
                <div class="div2">
                    <!-- Start of the table displaying callback records -->
                    <table class="tableview tableview-2" id="admin_table">
                        <thead>
                            <tr class="background">
                                <td align="left">S.No.</td>
                                <td align="left">User</td>
                                <td align="left">Recipient</td>
                                <td align="left">Callback Date::Time</td>
                                <td align="left">Callback Alert Time</td>
                                <td align="left">Callback Remark</td>
                                <td align="left">Created On</td>
                                <td align="left">Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                // Retrieve callback records from the database
                                $res = getCallBacks();
                                // Get the number of rows in the result
                                $numrow = mysqli_num_rows($res);
                                
                                if ($numrow == 0) 
                                {
                                    // Display message if no records found
                                    $message = "No record Found!";
                                    echo '<tr><td colspan=6 align="right" class="contentred"><img src=images/no_records.jpg>&nbsp;'.$message.'</td></tr>';
                                } else {
                                    // Loop through each callback record
                                    while ($row_doc = mysqli_fetch_array($res))
                                    {
                                        // Increment counter for S.No.
                                        $count = $count + 1;
                                        // Encode values for URL parameters
                                        $new_callbacks = base64_encode('new_callbacks');
                                        $id = base64_encode($row_doc['callback_id']);
                            ?>
                            <!-- Display each callback record in a table row -->
                            <tr>
                                <td align="left"><?php echo $count?></td>
                                <td align="left"><?php echo $row_doc['user']?></td>
                                <td align="left"><?php echo $row_doc['recipient']?></td>
                                <td align="left"><?php echo $row_doc['callback_time']?></td>
                                <td align="left"><?php echo $row_doc['callback_alert_time']?></td>
                                <td align="left"><?php echo $row_doc['remark']?></td>
                                <td align="left"><?php echo $row_doc['entry_time']?></td>                           
                                <td>
                                    <!-- Create a link with encoded parameters for callback actions -->
                                    <a href='javascript:void(0)' onclick="window.location.href='admin_index.php?token=<?php echo $new_callbacks ;?>&id=<?php echo $id ?>';">
                                        <!-- Display an edit icon for the action -->
                                        <img src="<?php echo $SiteURL?>public/images/edit-icon.png" border="0" alt="Edit" />
                                    </a>
                                </td>
                            </tr>
                            <?php 
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                    <!-- End of the table displaying callback records -->
                </div>
            </div>
        </div>
    </form>
</div>
