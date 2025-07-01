<?php
/***
 * ticket Report
 * Author: vastvikta nishad
 * Date: 18-12-2024
 * 
 * Description: This page is designed to generate and display the All deleted ticket Report.
**/

// function  file for updating date according to latest month record [vastvikta][18-03-2025]
include ("get_last_date_function.php");
$dateRange =  get_date_ticketdel();

$startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
? $_REQUEST['sttartdatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

$enddatetime = (!empty($_REQUEST['enddatetime'])) 
? $_REQUEST['enddatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));
?>
<div class="col-sm-10 mt-3" style="padding-left:0">
    <form name="myform" method="post">
    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Ticket Deletion Report</span>
    <div class="table">
    <table class="tableview tableview-2 main-form new-customer" style="background-color: #fff;">
        <tbody>
            <tr>
                <td  width="100" class="left ">&nbsp;
                     <label>Start Date </label>
                  
                <input type="text" name="sttartdatetime" class="dob1 date_class" value="<?=$startdatetime?>" id="startdatetime">&nbsp;
                </td>
                <td  class="left boder0-right">&nbsp;
                    <label>End Date </label>
                
                   
                 <input type="text" name="enddatetime" class="dob1 date_class" value="<?=$enddatetime?>" id="enddatetime">&nbsp;
             
                </td>                       
                <td  class="left">&nbsp;
                     <label>Case ID </label>
                
                        <span class="left boder0-left" style="display: inline-block;">
                            <?php 
                            $query = mysqli_query($link, "SELECT ticketid FROM $db.web_problemdefination_archive WHERE ticketid != '' GROUP BY ticketid ASC ");
                            ?>                                    
                            <input type="number" id="searchBox" placeholder="Search Case ID.. And Select." onkeyup="filterFunction('ticketid', 'searchBox', 'noCaseIdMessage')" style="width:170px; height:30px; margin-bottom: 0px;">

                            <div id="dropdownContainer" class="dropdown-content" style="width:170px; display: none; position: absolute; z-index: 1; background-color: white; border: 1px solid #ccc;">
                                <select name="ticketid" id="ticketid" class="select-styl1" size="5" style="width:100%; border: none; box-shadow: none; height: auto; margin: 0; display: none;" onchange="selectOption('ticketid', 'searchBox')">
                                    <option value="">Select</option>
                                    <?php while ($row = mysqli_fetch_assoc($query)) {
                                        $name = $row['ticketid'];
                                        $selected = ($name == $_POST['ticketid']) ? "selected" : "";
                                    ?>
                                        <option value="<?= htmlspecialchars($name) ?>" <?= $selected ?>><?= htmlspecialchars($name) ?></option>
                                    <?php } ?>
                                </select>
                                <div id="noCaseIdMessage" style="display: none; color: red; font-size: 12px; padding: 5px;">No records found</div>
                            </div>
                        </span>
                    </td>
                   
          
            <td class="left boder0-right">&nbsp;
                     <label>Deleted By</label>
                
                        &nbsp;<select name="i_CreatedBY" class="select-styl1" id="i_CreatedBY">
                            <?php
                            // Initialize the dropdown with a default option
                            echo '<option value="">Select Agent Name</option>';

                            // Fetch all names from the database
                            $query = "SELECT DISTINCT deleted_by FROM $db.web_problemdefination_archive";
                            $result = mysqli_query($link, $query);

                            // Check if the query executed successfully
                            if ($result) {
                                // Iterate over each i_CreatedBY and fetch the corresponding AtxUserName
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $createdBy = $row['deleted_by'];

                                    // Fetch AtxUserName from uniuserprofile table based on i_CreatedBY
                                    $userQuery = "SELECT AtxUserName FROM $db.uniuserprofile WHERE AtxUserID = '" . mysqli_real_escape_string($link, $createdBy) . "' ORDER BY AtxUserID";
                                    $userResult = mysqli_query($link, $userQuery);

                                    if ($userResult && mysqli_num_rows($userResult) > 0) {
                                        $userRow = mysqli_fetch_assoc($userResult);
                                        $userName = $userRow['AtxUserName'];

                                        // Add the option to the dropdown
                                        echo '<option value="' . htmlspecialchars($createdBy) . '">' . htmlspecialchars($userName) . '</option>';
                                    }
                                }
                            } else {
                                echo '<option value="">Error fetching data</option>';
                            }
                            ?>
                        </select>
                    </td>
                        </tr>
<tr>
                    <td width="226" class="left boder0-right">
                    <input name="submit" id="run_report" type="button" value="Run  Report" class="button-orange1">
                    <?php $ticket_deletion_report = base64_encode('ticket_deletion_report');?>
                    <input name="reset" id="reset_disposition" type="button" value="Reset" onclick="window.location.href='omni_channel.php?token=<?php echo $ticket_deletion_report; ?>';" 
                     class="button-orange1">
                </td>
            </tr>
        </tbody>
    </table>
</div>
    <!-- Start Display the filter data -->
    <div class="table">
            <div class="wrapper6">
               <div>
                    <table class="tableview tableview-2" id="ticket_deletion_report" >
                            <thead>
                            <tr style="font-size: 12px;">
                            <th>Case Id.</th>
                            <th>Customer Name</th>
                            <th>Phone Number</th>
                            <th>Created On</th>
                            <th>Deleted On</th>
                            <th>Deleted By</th>
                            <th>Reason of Delete</th>
                            <th>Status</th>
                            <th>Complaint Origin</th>
                           
                          
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Department</th>
                            <th>Priority/Non Priority</th>
                           
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="<?=$SiteURL?>public/js/ticket_deletion_script.js"></script><script type="text/javascript">
    function filterFunction(selectId, searchBoxId, messageDivId) {
        var input, filter, select, options, i;
        input = document.getElementById(searchBoxId);
        filter = input.value.toUpperCase();        
        select = document.getElementById(selectId);
        options = select.getElementsByTagName("option");
        var hasVisibleOptions = false;

        // Show the dropdown if the input has text
        var dropdownContainer = document.getElementById('dropdownContainer');
        if (filter.length > 0) {
            dropdownContainer.style.display = "block";
        } else {
            dropdownContainer.style.display = "none";
            return;
        }

        // Loop through all options and filter them
        for (i = 1; i < options.length; i++) { // Skip the first option
            var txtValue = options[i].textContent || options[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                options[i].style.display = "";
                hasVisibleOptions = true;
            } else {
                options[i].style.display = "none";
            }
        }

        // Handle "No records found" message
        var messageDiv = document.getElementById(messageDivId);
        if (!hasVisibleOptions && filter.length > 0) {
            select.style.display = "none";  // Hide the select dropdown
            messageDiv.style.display = "block"; // Show "No records found"
        } else {
            select.style.display = "block";  // Show the select dropdown
            messageDiv.style.display = "none"; // Hide "No records found"
        }
    }

    function selectOption(selectId, searchBoxId) {
        var select = document.getElementById(selectId);
        var searchBox = document.getElementById(searchBoxId);
        
        // Update the search box value with the selected option value
        searchBox.value = select.options[select.selectedIndex].value;

        // Hide the dropdown after an option is selected
        document.getElementById('dropdownContainer').style.display = "none";
    }

    // Hide the dropdown if clicking outside of it
    document.addEventListener('click', function(event) {
        var dropdownContainer = document.getElementById('dropdownContainer');
        var searchBox = document.getElementById('searchBox');
        if (!dropdownContainer.contains(event.target) && !searchBox.contains(event.target)) {
            dropdownContainer.style.display = 'none';
        }
    });
</script>
