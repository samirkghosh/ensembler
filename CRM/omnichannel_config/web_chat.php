 <?php
/***
 * Chat Listing page
 * Auth: Vastvikta Nishad
 * Date:  26 Mar  2024
 * Description: Handling Chat Listing Data Also Create Case and Case details 
 * 
*/
include_once("../../config/web_mysqlconnect.php"); // Database connection files
include_once("../function/classify_function.php"); // Some Common File code handling
// function  file for updating date according to latest month record [vastvikta][18-03-2025]
include ("get_last_date_function.php");
$dateRange =  get_date_chat();

$startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
? $_REQUEST['sttartdatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

$enddatetime = (!empty($_REQUEST['enddatetime'])) 
? $_REQUEST['enddatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));

$iallstatus = $_POST['allstatus'];
$iallstatus1 = getStatusQuery($iallstatus);

// Checking module license 
$module_flag_customer = module_license('chat');
$id = $_POST['id'];

if($module_flag_customer != '1'){
   // Redirecting to dashboard if module license is not valid
   header("Location:web_admin_dashboard.php"); 
   exit();
}


?><style>
.flag-yellow {
    background:#f1a00bd4 !important; 
    color:#fff;
}

.flag-red{
    background:#e34234db !important; 
    color:#fff;
}
.fil_div{
        width:20px;
        height:20px;
        border:1px solid #999999;
        border-radius:5px;
        white-space: nowrap;
    }
     /*read and unread color flow changes [Aarti][06-01-2024]*/
     .Unread{
      background:#f1f1f1;
      margin-left: -50px;
      display: inline-block;
    }

    .Read{
      background:#ffffff;
      margin-left: -73px;
    }

    .mail-row {
      cursor: pointer;
      font-weight: bold; /* Bold for unread emails */
      background-color: #f1f1f1 !important; /* Light blue background for unread emails */      
    }
    .tableview tr td a{
        color: #555;
    }
    .mail-row td a{
        color:#3974aa;    
    }

    /*END -06-01-2024*/
</style>
<!-- HTML content starts here -->
<div class="col-sm-10 mt-3" style="padding-left:0">
   <span class="breadcrumb_head" style="height:37px;padding:9px 16px"><div class="row">
      <div class="col-sm-12">
         <div class="row">
         <div class="col-sm-2">
               <div class="row">
                  <div class="col-sm-7">Chat</div>
               </div>
         </div>
         <div class="col-sm-6 read-container"></div>
            <div class="col-sm-2 read-container">
                     <div class="row">
                        <div class="col-sm-7 ">Unread</div>
                        <div class="col-sm-5"><div class="fil_div Unread" ></div></div>
                     </div> 
                  </div>                      
                  <div class="col-sm-2 ">
                     <div class="row">
                        <div class="col-sm-7">Read</div>
                        <div class="col-sm-5"><div class="fil_div Read" ></div></div>
                     </div> 
               </div>
            </div>
         </div>
      </div>
   </span>
   <div class="Reports-page#" style="display: block; border: #d4d4d4 1px solid; margin-bottom:20px; min-height: 420px; margin-top:37px">
      <!-- Form for filtering chat sessions -->
      <form method="post" name="cfrm" id="post_web_chat">
         <!-- Filter -->
         <div style="margin-top:-1px;">
            <table class="tableview tableview-2 main-form new-customer">
               <tr style="height:40px;">
               <input type="hidden" name="id"id = 'id' value="<?php echo htmlspecialchars($id); ?>">
                  <td width="95" class="left boder0-right">
                     <label>Start Date</label>
                  </td>
                  <td width="226" class="left boder0-right">
                     <span class="left boder0-left">
                        <input type="text" name="sttartdatetime" class="date_class dob1" value="<?=$startdatetime?>" id="startdatetime">
                     </span>
                  </td>
                  <td width="112" class="left boder0-right">
                     <label>End Date</label>
                  </td>
                  <td width="230" class="left boder0-right" align="left">
                     <span class="left boder0-left">
                        <input type="text" name="enddatetime" class="date_class dob1" value="<?=$enddatetime?>" id="enddatetime">
                     </span>
                  </td>
                  <td width="211" class="left boder0-right">
                     <label>Phone</label>
                  </td>
                  <td width="192" class="left boder0-right">
                     <span class="left boder0-left">
                        <input type="text" class="input-style1" id="phone" name="phone" value="<?=$phone?>" style="background:#fff">
                     </span>
                  </td>
               </tr>
               <tr>
                  <td width="95" class="left boder0-right">
                     <label>Chat type</label>
                  </td>
                  <td width="230" align="left" class="left boder0-right">
                                <span class="left boder0-left">
                                <select name="chat_type" id="chat_type" class="select-styl1" style="width:180px">
                                <option value="">Select</option>
                                <option value="1" >Missed Chat</option>
                                </select>
                                </span>
                            </td> 
                           <!--   * Author: Ritu Modi 
                           * Date: 03-09-2024  
                           * On the advice of ajay sir i have added one filters so that the data can be seached easily.-->

                           <td width="211" class="left boder0-right">&nbsp;
                               <label>Case Id</label>
                           </td>
                           <?php 
                           // Database query to fetch distinct Case IDs
                           $query = mysqli_query($link, "SELECT caseid FROM $db.`overall_bot_chat_session` WHERE caseid != '' ORDER BY caseid ASC");

                           $caseIdList = [];
                           while ($row = mysqli_fetch_assoc($query)) {
                               $caseIdList[] = $row['caseid'];
                           }
                           ?>
                           <td width="192" class="left boder0-right">
                               <span class="left boder0-left">
                                   <input type="number" class="input-style1" id="caseid" name="caseid" placeholder="Search Case Id..." 
                                          onkeyup="filterFunction('caseid', 'caseSuggestions', 'noCaseIdMessage')" 
                                          style="background:#fff; width:180px; height:30px; margin-bottom: 5px;">
                                   
                                   <!-- Suggestions Div -->
                                   <div id="caseSuggestions" style="border: 1px solid #ccc; max-height: 150px; overflow-y: auto; position: absolute; width: 180px; display: none; background: #fff; z-index: 1000;"></div>
                                   
                                   <!-- No Records Found Message Div -->
                                   <div id="noCaseIdMessage" style="display: none; color: red; font-size: 12px;">No records found</div>
                               </span>
                           </td>
                           <td class="left boder0-right"> <label>Status</label>
                        </td>
                        <td>
                    &nbsp;
                    <select name="allstatus" id="iallstatus" class="select-styl1">
                        <?php if($iallstatus == 3) { ?>
                        <option value="3" selected>New Case</option>
                        <?php } ?>
                        <option value="4" <?php if($iallstatus == 4) echo "selected"; ?>>ALL</option>
                        <option value="0" <?php if($iallstatus == 2) echo "selected"; ?>>Case Not Created</option>
                        <option value="1" <?php if($iallstatus == 1) echo "selected"; ?>>Case Created</option>
                    </select>

                </td>
               </tr>
                <tr>
                <td class="left boder0-right">
                <label>Sender Name</label>
                    </td>
                        <td class="left boder0-right">
                            &nbsp;<select name="send_name" class="select-styl1" id="send_name">
                                <?php
                                // Initialize the dropdown with a default option
                                echo '<option value="">Select Name</option>';

                                // Fetch all names from the database
                                $result = mysqli_query($link, "SELECT distinct(name) FROM $db.overall_bot_chat_session ; ");

                                if ($result) {
                                    // Loop through the result set to populate the dropdown
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $name = $row['name'];
                                        // Output each name as a dropdown option
                                        echo '<option value="' . $name . '">' . htmlspecialchars($name) . '</option>';
                                    }
                                } else {
                                    // Handle errors or empty result set
                                    echo '<option value="">No names available</option>';
                                }
                                ?>
                    </select>
                </td>  
                           <!-- Hidden Input to Store Case ID List -->
                           <input type="hidden" id="caseIdList" value='<?= json_encode($caseIdList) ?>'>
                  <td class="left boder0-right" colspan="4">
                     <input type='submit' name='sub1' value='Run Report' class="button-orange1" />
                     <input type='reset' name='reset' value='Reset' class="button-orange1" id="reset_button_chat" />
                  </td>
               </tr>
            </table>
         </div>
         <!-- Data -->
         <div class="wrapper2">
            <div class="box-body table-responsive">
              <div class="table">
                 <table class="tableview tableview-2" id = 'chat_table'>
                    <thead>
                       <tr class="" style="font-size: 12px">
                          <th>S.No</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th>Chat</th>
                          <th>Case</th>
                          <th>Date</th>
                          <th>Disposition</th>
                       </tr>
                    </thead>
                    <tbody>
                  </tbody>
                 </table>
              </div>
            </div>
         </div>
      </form>
   </div>
</div>
<script>
function data_delete(id) {
    // Send AJAX request to delete the data
    $.ajax({
        url: 'omnichannel_config/omnichannel_function.php', // Server-side script to handle deletion
        method: 'POST',
        data: { id: id, action:'data_delete'},
        success: function(response) {
            // Handle success response
            alert('Data deleted successfully');
            // Optionally, you can update the UI here
            // Reload the page after 2 seconds
            setTimeout(function() {
                location.reload(); // Reload the page
            }, 1000); // 1000 milliseconds = 1 second
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.error('Error deleting data:', error);
        }
    });
}
</script>
<script type="text/javascript">
   /**
     * Author: Ritu Modi 
     * Date: 03-09-2024 
     * Function to filter the options in a dropdown based on the text input.
     */
function filterFunction(searchBoxId, suggestionsDivId, messageDivId) {
    var input, filter, suggestionsDiv, messageDiv, i, matched;

    // Get the input element and its value
    input = document.getElementById(searchBoxId);
    filter = input.value.toUpperCase();
    
    // Get the suggestions container and message div
    suggestionsDiv = document.getElementById(suggestionsDivId);
    messageDiv = document.getElementById(messageDivId);
    
    // Retrieve data from the hidden input field
    var caseIdList = JSON.parse(document.getElementById('caseIdList').value);
    
    // Clear existing suggestions
    suggestionsDiv.innerHTML = '';

    // Check if any suggestion matches the filter
    matched = false;
    for (i = 0; i < caseIdList.length; i++) {
        if (caseIdList[i].toUpperCase().indexOf(filter) > -1) {
            matched = true;
            var suggestion = document.createElement('div');
            suggestion.textContent = caseIdList[i];
            suggestion.style.padding = '5px';
            suggestion.style.cursor = 'pointer';
            suggestion.style.borderBottom = '1px solid #ddd';
            
            // Event listener for selecting the suggestion
            suggestion.addEventListener('click', function() {
                selectSuggestion(this.textContent, searchBoxId, suggestionsDivId);
            });

            suggestionsDiv.appendChild(suggestion);
        }
    }

    // Show or hide suggestions and message
    if (matched) {
        suggestionsDiv.style.display = 'block';
        messageDiv.style.display = 'none';
    } else if (filter.length > 0) {
        suggestionsDiv.style.display = 'none';
        messageDiv.style.display = 'block';
    } else {
        suggestionsDiv.style.display = 'none';
        messageDiv.style.display = 'none';
    }

    // Hide suggestions if Backspace is pressed and input is empty
    if (input.value === '') {
        suggestionsDiv.style.display = 'none';
    }
}

function selectSuggestion(text, searchBoxId, suggestionsDivId) {
    var searchBox = document.getElementById(searchBoxId);
    var suggestionsDiv = document.getElementById(suggestionsDivId);
    
    // Update the search box value
    searchBox.value = text;
    
    // Hide the suggestions
    suggestionsDiv.style.display = 'none';
}

// Event listener for handling Backspace and clearing the dropdown
document.getElementById('caseid').addEventListener('keydown', function(event) {
    if (event.key === 'Backspace') {
        var suggestionsDiv = document.getElementById('caseSuggestions');
        
        // If search box is empty after Backspace, hide the dropdown
        if (this.value === '') {
            suggestionsDiv.style.display = 'none';
        }
    }
});

// Hide the suggestions dropdown when clicking outside of it
document.addEventListener('click', function(event) {
    var suggestionsDiv = document.getElementById('caseSuggestions');
    var searchBox = document.getElementById('caseid');
    
    if (event.target !== searchBox && event.target !== suggestionsDiv) {
        suggestionsDiv.style.display = 'none';
    }
});

</script>