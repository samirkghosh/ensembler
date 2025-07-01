<?php
/***
 * Report -> Agent
 * Author: Aarti
 * Date: 
 * 
 * This page is designed for generating an agent report. Users can select specific agents, a date range, and a time period for the report. After submitting the form, the report displays details such as agent name, login time, logout time, and IP address.
**/include_once "../../config/web_mysqlconnect.php";

?>
<!-- updated the code related to save filter data [vastvikta][26-03-2025] -->
<!-- CSS for Modal Styling -->
<style>
  .no-min-width {
    min-width: unset !important; /* Override the default min-width */
    display: inline-flex; /* Ensures alignment */
    align-items: center; /* Keeps text aligned with radio buttons */
  }

  .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.4);
  }
  .modal-content {
      background-color: white;
      margin: 5% auto;
      padding: 10px;
      border-radius: 10px;
      width: 40%;
      text-align: center;
      position: relative;
  }

  /* FLEXBOX to align everything in one row */
  .filter-row {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px; /* Space between elements */
  }

  /* Styling for input field */
  .input-style11 {
      width: 150px;
      height: 30px;
      border: 1px solid #cbcbcb;
      border-radius: 0;
      padding: 5px;
      outline: none;
  }

  /* Close button styling */
  .close {
      position: absolute;
      right: 15px;
      top: 10px;
      font-size: 20px;
      cursor: pointer;
  }
  .filter-container {
    display: flex;
    align-items: center; /* Align items vertically */
    gap: 10px; /* Add space between elements */
  }

  #edit_filter, #delete_filter , #restore_filter{
      display: flex;
      align-items: center;
      justify-content: center;
      width: 30px; /* Ensure uniform width */
      height: 30px;
      cursor: pointer;
  }
</style>

<title>agent</title>
<form name="myform" id="myform" method="post">
  <div class="style2-table">
    <div class="table">
    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">LOGIN USER REPORT</span>
    <!-- Main form table -->
    <table class="tableview tableview-2 main-form new-customer">
        <tbody>
          <tr>
            <td class="left boder0-right">
              <label>User</label>
              <div class="log-case">
                <?php
                    // Retrieve agent profiles
                    $result = uniuserprofile();
                ?>
                <select name="agent_n" id="agent_n" class="select-styl1">
                  <option value="">Select User</option>
                  <?php 
                    // Loop through each agent profile
                    while($row=mysqli_fetch_array($result)) {
                      $AtxUserID=$row['AtxUserID']; 
                      $AtxUserName=$row['AtxUserName'];
                      if($AtxUserName == $_POST['agent_n']){
                        $sel = 'selected';
                      }else{
                        $sel = '';
                      }
                  ?>
                    <option value='<?=$AtxUserName?>' <?=$sel?>><?=$AtxUserName?></option>
                  <? } ?>
                </select>
              </div>
            </td>
            <td class="left boder0-left boder0-right"></td>
              <?php
              // Determine start and end datetime parameters[vastvikta][17-03-2025]
                                
              $dateRange = get_date_login_user();

              $startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
                  ? $_REQUEST['sttartdatetime'] 
                  : date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

              $enddatetime = (!empty($_REQUEST['enddatetime'])) 
                  ? $_REQUEST['enddatetime'] 
                  : date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));
              ?>
            <td class="left boder0-left boder0-right ">
              <label> From 
              <input type="text" name="sttartdatetime" class="date_class dob1" value="<?=$startdatetime?>" id="sttartdatetime"></label>
              <label>
              To <input type="text" name="enddatetime" class="date_class dob1" value="<?=$enddatetime?>" id="enddatetime"></label>
            </td>
          </tr>
          <tr>
            <td  class="left boder0-left boder0-right" colspan="2">
              <label>Time Period</label>
                <span class="left boder0-left">
                  <select name="timeperiod" id="timeperiod" class="select-styl1">
                    <option value="">Select a Period</option>
                    <option value="1" <? if($_POST['timeperiod']==1){ echo "selected"; } ?>>This Month</option>
                    <option value="2" <? if($_POST['timeperiod']==2){ echo "selected"; } ?>>Previous Month</option>
                    <option value="3" <? if($_POST['timeperiod']==3){ echo "selected"; } ?>>This Financial Year</option>
                  </select>
                </span>
            </td>
            <td class="left boder0-left boder0-right" colspan="2">
            <form name="myform2" id="myform2" method="post">
              <input type="hidden" name="status" id="status" value="<?php echo isset($_POST['status']) ? $_POST['status'] : '1'; ?>">
              <div class="filter-container">
                <label class="no-min-width">Saved Filters</label>
                <!-- <input type="radio" id="active" name="filter" value="1"
                  <?php if(!isset($_POST['status']) || $_POST['status'] == "1") echo "checked"; ?> onchange="toggleFilterButtons()">
                  Active
                <input type="radio" id="inactive" name="filter" value="0"
                  <?php if(isset($_POST['status']) && $_POST['status'] == "0") echo "checked"; ?> onchange="toggleFilterButtons()">
                  Inactive -->

                <select name="saved_filters" id="saved_filters" class="select-styl1">
                  <option value="">Select Saved Filter</option>
                  <?php
                    global $db, $link;
                    $agent_id = $_SESSION['userid'];

                    function get_filter_list($status, $agent_id) {
                      global $db, $link;
                        if ($agent_id == '1') {
                          $sql = "SELECT * FROM $db.user_filters WHERE filter_page = 'login_user_report' AND status = '$status'";
                        } else {
                          $sql = "SELECT * FROM $db.user_filters WHERE filter_page = 'login_user_report' AND created_by = '$agent_id' AND status = '$status'"; 
                        }
                      return mysqli_query($link, $sql);
                    }

                    $result = get_filter_list(isset($_POST['status']) ? $_POST['status'] : '1', $agent_id);
                    while ($row = mysqli_fetch_array($result)) {
                      echo "<option value='" . $row['id'] . "'>" . $row['filter_name'] . "</option>";
                    }
                  ?>
                </select>
                <div id="edit_filter" style="margin-top:12px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 512 512">
                        <path fill="#6283bc" d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160L0 416c0 53 43 96 96 96l256 0c53 0 96-43 96-96l0-96c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 96c0 17.7-14.3 32-32 32L96 448c-17.7 0-32-14.3-32-32l0-256c0-17.7 14.3-32 32-32l96 0c17.7 0 32-14.3 32-32s-14.3-32-32-32L96 64z"/>
                    </svg>
                </div>
                <!-- <div id="delete_filter" style="margin-top:12px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 448 512">
                        <path fill="#6283bc" d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z"/>
                    </svg>
                </div> -->
                <!-- <div id="restore_filter" style="margin-top:12px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 512 512">
                        <path fill="#6283bc" d="M0 224c0 17.7 14.3 32 32 32s32-14.3 32-32c0-53 43-96 96-96l160 0 0 32c0 12.9 7.8 24.6 19.8 29.6s25.7 2.2 34.9-6.9l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-9.2-9.2-22.9-11.9-34.9-6.9S320 19.1 320 32l0 32L160 64C71.6 64 0 135.6 0 224zm512 64c0-17.7-14.3-32-32-32s-32 14.3-32 32c0 53-43 96-96 96l-160 0 0-32c0-12.9-7.8-24.6-19.8-29.6s-25.7-2.2-34.9 6.9l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c9.2 9.2 22.9 11.9 34.9 6.9s19.8-16.6 19.8-29.6l0-32 160 0c88.4 0 160-71.6 160-160z"/>
                    </svg>
                </div> -->
              </div>
            </form>
          </td>
        </tr>
        <tr>
          <td class="left boder0-right">
            <center>
              <input name="submit" id="submit" type="button" value="Run Report" class="button-orange1">
              <input name="reset" id="reset" type="button" value="Reset" class="button-orange1">
              <input name="save_filters" id="save_filters" type="button" value="Save Filter" class="button-orange1">
              <input name="update_filters" id="update_filters" type="button" value="Update Filter" class="button-orange1" style="display: none;">
            </center>
          </td>
        </tr>
      </tbody>
    </table>
</form><!-- Modal Structure -->
<div id="filterModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h4>Save Filter</h4> 
    <!-- Flex container for aligning everything in one row -->
    <div class="filter-row">
      <label for="filter_name">Filter Name:</label>
      <input type="text" id="filter_name" name="filter_name" class="input-style11" placeholder="Enter filter name">
      
      <!-- Hidden field for filter page -->
      <input type="hidden" id="filter_page" name="filter_page" value="login_user_report">
      
      <!-- Save Button -->
      <input name="save_filters1" id="save_filters1" type="button" value="Save Filter" class="button-orange1">
    </div>
  </div>
</div>
<div id="filterModal2" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h4>Update Filter</h4> 
    <!-- Flex container for aligning everything in one row -->
    <div class="filter-row">
        <label for="filter_name">Filter Name:</label>
        <input type="text" id="filter_name_update" name="filter_name_update" class="input-style11" placeholder="Enter filter name">
        
        <!-- Hidden field for filter page -->
        <input type="hidden" id="filter_page" name="filter_page" value="login_user_report">
        
        <!-- Save Button -->
        <input name="save_filters2" id="save_filters2" type="button" value="Save Filter" class="button-orange1">
      </div>
    </div>
  </div>
</div>
<div class="table">
  <form name="frmService" method="post">
    <div class="wrapper6"> 
        <table class="tableview tableview-2" id="agent_data">
          <thead>
            <tr class="background" style="font-size: 12px">
                <th align="center">S.No.</th>
                <th align="center">Username</th>
                <th align="center">Login</th>
                <th align="center">Logout </th>
                <th align="center">Login Duration </th>
                <th align="center">IP Address</th>
              </tr>
          </thead>
        </table>
      </div>
    </div>
  </form>
</div>
<!-- JavaScript to Show Filter Name Input -->
<script>
// change the icons display on the basis of the active inactive filter status 
function toggleFilterButtons() {
    let isActive = document.getElementById('active').checked;
    document.getElementById('delete_filter').style.display = isActive ? 'block' : 'none';
    document.getElementById('edit_filter').style.display = isActive ? 'block' : 'none';
    document.getElementById('restore_filter').style.display = isActive ? 'none' : 'block';
}
document.addEventListener("DOMContentLoaded", function () {
    // Call the function to set the correct display of buttons on page load
    toggleFilterButtons();

    // Select all input elements with name 'filter' (radio buttons or checkboxes)
    document.querySelectorAll("input[name='filter']").forEach(input => {
        // Add an event listener to each filter input to detect changes
        input.addEventListener("change", function () {
            // Update the visibility of filter-related buttons based on the selection
            toggleFilterButtons();

            // Get the value of the selected filter (e.g., Active/Inactive)
            let selectedStatus = this.value;

            // Call the function to update filters dynamically via AJAX
            updateFilters(selectedStatus);
        });
    });
});

function updateFilters(status) {
    // Create a payload object containing the action type and selected status
    let payload = {
        action: "update_dropdown", // Specifies the action to be performed
        status: status // Holds the selected filter status (e.g., Active/Inactive)
    };

    // Perform an AJAX request to update the filters dynamically
    $.ajax({
        url: "Report/save_filter.php", // The server-side script that handles filter updates
        type: "POST", // Send data using the POST method
        contentType: "application/json", // Specify the content type as JSON
        data: JSON.stringify(payload), // Convert the payload object to a JSON string

        success: function (response) {
            // Log the server response for debugging purposes
            console.log(response);

            // If the response status is "success", update the dropdown options
            if (response.status === "success") {
                $("#saved_filters").html('<option value="">Select Saved Filter</option>' + response.filters);
            } else {
                // Show an alert if there is an error in the response
                alert("Error: " + response.message);
            }
        },

        error: function (xhr, status, error) {
            // Log any AJAX errors to the console
            console.error("AJAX Error:", status, error);
            // Show an alert if the request fails
            alert("Error fetching filters. Please try again.");
        }
    });
}
document.addEventListener("DOMContentLoaded", function () {
    // Handle clicking the edit filter button
    document.getElementById('edit_filter').addEventListener('click', function () {
        // Get the selected filter ID from the dropdown
        const selectedFilterId = document.getElementById('saved_filters').value;
        const updateButton = document.getElementById('update_filters');
        const saveButton = document.getElementById('save_filters');

        // If no filter is selected, show an alert and exit the function
        if (!selectedFilterId) {
            alert("Please select a filter to edit.");
            return;
        }

        // Show the "Update Filter" button and hide the "Save Filter" button
        updateButton.style.display = 'inline-block'; // Show "Update Filter" button
        saveButton.style.display = 'none'; // Hide "Save Filter" button

        // Store the selected filter ID in the "Update Filter" button for later use
        updateButton.setAttribute('data-filter-id', selectedFilterId);
    });

    // Open the modal and prefill the filter name when "Update Filter" is clicked
    document.getElementById('update_filters').addEventListener('click', function () {
        // Retrieve the filter ID stored in the "Update Filter" button
        const filterId = this.getAttribute('data-filter-id');
        
        // If a filter ID is present, proceed with opening the modal
        if (filterId) {
            const modal = document.getElementById('filterModal2');
            modal.style.display = 'block'; // Display the modal

            // Fetch the selected filter's name from the dropdown based on its value
            const selectedFilterText = document.querySelector(`#saved_filters option[value='${filterId}']`).textContent;
            
            // Populate the modal input field with the selected filter name
            document.getElementById('filter_name_update').value = selectedFilterText;
        }
    });

    // Ensure the Save Filter button opens the correct modal
    document.getElementById("save_filters").addEventListener("click", function () {
        document.getElementById("filterModal").style.display = "block"; // Open the Save Filter modal
    });

    // Close modals when clicking the close button
    document.querySelectorAll('.close').forEach((closeBtn) => {
        closeBtn.addEventListener('click', function () {
            this.closest('.modal').style.display = 'none'; // Hide the modal
        });
    });

    // Hide modals if clicking outside of them
    window.addEventListener('click', function (event) {
        if (event.target === document.getElementById('filterModal')) {
            document.getElementById('filterModal').style.display = 'none'; // Hide Save Filter modal
        }
        if (event.target === document.getElementById('filterModal2')) {
            document.getElementById('filterModal2').style.display = 'none'; // Hide Update Filter modal
        }
    });
});
</script>


