<?php
/* check license access or not for  this module*/ 

include("../../config/web_mysqlconnect.php");

// function  file for updating date according to latest month record [vastvikta][18-03-2025]
include ("get_last_date_function.php");
$dateRange =  get_date_whatsapp();

$startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
? $_REQUEST['sttartdatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

$enddatetime = (!empty($_REQUEST['enddatetime'])) 
? $_REQUEST['enddatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));

$module_flag_customer = module_license('WhatsApp');
if($module_flag_customer !='1'){
  header("Location:web_admin_dashboard.php"); 
  exit();
}
/***END***/
$name= $_SESSION['logged'];
?>
  <!--  CSS code -->
<link href="<?=$SiteURL?>public/css/channel_all_style.css" rel="stylesheet" type="text/css" />
<div class="col-sm-10 mt-3" style="padding-left:0">
  <span class="breadcrumb_head" style="height:37px;padding:9px 16px">
        <div class="row">
          <div class="col-sm-12">
            <div class="row">
              <div class="col-sm-2">
                  <div class="row">
                     <div class="col-sm-7">Whatsapp</div>
                   </div>
              </div>
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
  <div class="Reports-page#"
     style="display: block;border: #d4d4d4 1px solid;marginbottom:20px;min-height: 420px;margin-top:37px">
        <form method="post" name="cfrm" id="post_whatsapp">
           <!-- Filter -->
            <div style="margin-top:-1px;">
                <table class="tableview tableview-2 main-form new-customer">
                   <tr style="height:40px;">
                      <td width="95" class="left boder0-right">&nbsp;
                         <label>Start Date </label>
                      </td>
                      <td width="226" class="left boder0-right">
                           
                         <span class="left boder0-left">
                            <input type="text" name="sttartdatetime" class="date_class dob1"
                               value="<?=$startdatetime?>" id="startdatetime">&nbsp;
                         </span>
                      </td>
                      <td width="112" class="left boder0-right"><label>End Date </label></td>
                      <td width="230" class="left boder0-right" align="left">
                         <span class="left boder0-left">
                            <!-- <input type="text" name="enddatetime" class="dob1"  value="<?=$enddatetime?>" id="enddatetime"> -->
                            <input type="text" name="enddatetime" class="date_class dob1"
                               value="<?=$enddatetime?>" id="enddatetime">
                         </span>
                      </td>
                      <td width="211" class="left boder0-right">&nbsp;
                         <label>Phone</label>
                      </td>
                      <td width="192" class="left boder0-right">
                         <span class="left boder0-left">
                         <input type="text" class="input-style1" id="phone" name="phone"
                            value="<?=$phone?>" style="background:#fff">
                         </span>
                      </td>
                   </tr>
                     <!--   * Author: Ritu Modi 
                        * Date: 26-08-2024  
                        * On the advice of ajay sir i have added two filters so that the data can be seached easily.-->
                    <td width="211" class="left boder0-right"><label>Case ID</label></td>
                    <td width="192" align="left" class="left boder0-right">
                        <span class="left boder0-left">
                            <?php $query = mysqli_query($link, "SELECT ICASEID FROM $db.whatsapp_in_queue WHERE ICASEID != '' GROUP BY ICASEID ASC "); ?>
                            
                            <input type="number" id="searchBox" placeholder="Search Case ID..." onkeyup="filterFunction('ICASEID', 'searchBox', 'noCaseIdMessage')" style="width:155px; height:30px; margin-bottom: 5px;">
                            
                            <select name="ICASEID" id="ICASEID" class="select-styl1" size="5" style="width:155px; display: none;" onchange="selectOption('ICASEID', 'searchBox')">
                                <option value="">Select</option>
                                <?php while ($row = mysqli_fetch_assoc($query)) {
                                    $name = $row['ICASEID'];
                                    $selected = ($name == $_POST['ICASEID']) ? "selected" : "";
                                ?>
                                    <option value="<?= $name ?>" <?= $selected ?>><?= $name ?></option>
                                <?php } ?>
                            </select>
                            <div id="noCaseIdMessage" style="display: none; color: red; font-size: 12px;">No records found</div> <!-- Message for Case ID -->
                        </span>
                    </td>

                    <td width="112" class="left boder0-right"><label>Sender</label></td>
                    <td width="230" align="left" class="left boder0-right">
                        <span class="left boder0-left">
                            <?php $query = mysqli_query($link, "SELECT send_from FROM $db.whatsapp_in_queue WHERE send_from != '' GROUP BY send_from ASC "); ?>
                            
                            <input type="number" id="searchSenderBox" placeholder="Search Sender..." onkeyup="filterFunction('send_from', 'searchSenderBox', 'noSenderMessage')" style="width:180px; height:30px; margin-bottom: 5px;">
                            
                            <select name="send_from" id="send_from" class="select-styl1" size="5" style="width:180px; display: none;" onchange="selectOption('send_from', 'searchSenderBox')">
                                <option value="">Select</option>
                                <?php while ($row = mysqli_fetch_assoc($query)) {
                                    $name = $row['send_from'];
                                    $selected = ($name == $_POST['send_from']) ? "selected" : "";
                                ?>
                                    <option value="<?= $name ?>" <?= $selected ?>><?= $name ?></option>
                                <?php } ?>
                            </select>
                            <div id="noSenderMessage" style="display: none; color: red; font-size: 12px;">No records found</div> <!-- Message for Sender -->
                        </span>
                    </td>
                    <td class="left boder0-right">
                <label>Sender Name</label>
                    </td>
                        <td class="left boder0-right">
                            &nbsp;<select name="send_name" class="select-styl1" id="send_name">
                                <?php
                                // Initialize the dropdown with a default option
                                echo '<option value="">Select Sender Name</option>';

                                // Fetch all names from the database
                                $result = mysqli_query($link, "SELECT fname, lname ,whatsapphandle FROM $db.web_accounts where whatsapphandle!='' ");

                                if ($result) {
                                    // Loop through the result set to populate the dropdown
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $fname = $row['fname'];
                                        $lname = $row['lname'];
                                        $whatsapphandle= $row['whatsapphandle'];
                                        $fullname = $fname . ' ' . $lname;

                                        // Output each name as a dropdown option
                                        echo '<option value="' . $whatsapphandle . '">' . htmlspecialchars($fullname) . '</option>';
                                    }
                                } else {
                                    // Handle errors or empty result set
                                    echo '<option value="">No names available</option>';
                                }
                                ?>
                    </select>
                </td>       
                            </tr>
                            <tr>
                   <td class="left  boder0-left" colspan="4">
                      <input type='submit' name='sub1' value='Run Report' class="button-orange1 set_button" />
                      <?php $whatsapp = base64_encode('whatsapp');?>
                      <input type="button" 
                    class="button-orange1" 
                    value="Reset" 
                    onclick="window.location.href='omni_channel.php?token=<?php echo $whatsapp; ?>';" 
                    style="float:inherit; color:#222; text-decoration:none; " />
                  </td>   
                </table>
            </div>
           <br>
            <div class="table" id="facebook">
               <table class="tableview tableview-2 " id = 'whatsapp_table'>
                  <thead> 
                  <tr class="background">
                        <td align="center" valign="middle" width="5%">S.No.</td>
                        <td align="center" valign="middle" width="15%">Date</td>
                        <td align="center" valign="middle" width="12%">Sender</td>
                        <td align="center" valign="middle" width="12%">Recipient</td>
                        <td align="center" valign="middle" width="18%">Message</td>
                        <td align="center" valign="middle" width="10%">Attachment</td>
                        <td align="center" valign="middle" width="8%">Case</td>
                        <td align="center" valign="middle" width="8%">Status</td>
                        <td align="center" valign="middle" width="7%">Action</td>
                    </tr>
                  </thead>
                        <tbody>

                        </tbody>
               </table> 
            </div>                        
        </form>
     </div>
  </div>
</div>
<script type="text/javascript">
  setInterval(function() {  
         get_notification();
    }, 6000); 
    function get_notification(){
      $.ajax({
        url: 'common_function.php',
        type: 'post',
        dataType: 'json',
        data: {'action': 'get_whatsapp_notification'},
        success: function(data) {
          console.log('whatsapp notification');
          $.each(data, function(index, value){
            $('.whatsa_'+index).text(value);
          });
        }
      });
    }
</script>
<script type="text/javascript">
    /**
     * Author: Ritu Modi 
     * Date: 26-08-2024 
     * Function to filter the options in a dropdown based on the text input.
     * selectId - The ID of the <select> element.
     * searchBoxId - The ID of the <input> element used for searching.
     * messageDivId - The ID of the <div> element that displays the "No records found" message.
     */
function filterFunction(selectId, searchBoxId, messageDivId) {
    var input, filter, select, options, i, visibleOptionsCount;
    
    // Get the input element and its value
    input = document.getElementById(searchBoxId);
    filter = input.value.toUpperCase();
    
    // Get the select element and its options
    select = document.getElementById(selectId);
    options = select.getElementsByTagName("option");
    visibleOptionsCount = 0;

    // Loop through all options and check if they match the filter text
    for (i = 0; i < options.length; i++) {
        if (options[i].value == "") continue;  // Skip the "Select" option
        txtValue = options[i].textContent || options[i].innerText;
        
        // If the option text matches the filter, display it; otherwise, hide it
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            options[i].style.display = "";
            visibleOptionsCount++;
        } else {
            options[i].style.display = "none";
        }
    }

    // Get the message div for "No records found"
    var messageDiv = document.getElementById(messageDivId);
    
    // If no matching records are found and the filter is not empty, hide the dropdown and show the message
    if (visibleOptionsCount == 0 && filter.length > 0) {
        select.style.display = "none";  // Hide the dropdown
        messageDiv.style.display = "block";  // Show the "No records found" message
    } else {
        select.style.display = "block";  // Show the dropdown
        messageDiv.style.display = "none";  // Hide the "No records found" message
    }
    
    // If Backspace is pressed and search box is empty, hide the dropdown
    if (filter === "") {
        select.style.display = "none";
    }
}

/**
 * Function to update the search box with the selected option from the dropdown.
 * selectId - The ID of the <select> element.
 * searchBoxId - The ID of the <input> element used for searching.
 */
function selectOption(selectId, searchBoxId) {
    var select = document.getElementById(selectId);
    var searchBox = document.getElementById(searchBoxId);
    
    // Update the search box value with the selected option text
    searchBox.value = select.options[select.selectedIndex].text;
    
    // Hide the dropdown after an option is selected
    select.style.display = "none";
}

// Event listener to detect Backspace key press
document.getElementById('searchBox').addEventListener('keydown', function(event) {
    if (event.key === 'Backspace' && this.value === '') {
        document.getElementById('ICASEID').style.display = 'none';  // Hide the Case ID dropdown
    }
});

document.getElementById('searchSenderBox').addEventListener('keydown', function(event) {
    if (event.key === 'Backspace' && this.value === '') {
        document.getElementById('send_from').style.display = 'none';  // Hide the Sender dropdown
    }
});

// Reset button functionality
document.getElementById('reset_button_whatsapp').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent form submission to handle reset functionality

    // Clear the search box for Case ID and hide its dropdown
    document.getElementById('searchBox').value = '';  // Clear Case ID search box
    document.getElementById('ICASEID').style.display = 'none';  // Hide Case ID dropdown
    document.getElementById('noCaseIdMessage').style.display = 'none'; // Hide "No records found" message

    // Clear the search box for Sender and hide its dropdown
    document.getElementById('searchSenderBox').value = '';  // Clear Sender search box
    document.getElementById('send_from').style.display = 'none';  // Hide Sender dropdown
    document.getElementById('noSenderMessage').style.display = 'none'; // Hide "No records found" message

    // Optionally reset other fields (if needed)
    document.getElementById('startdatetime').value = '';  // Clear start date
    document.getElementById('enddatetime').value = '';  // Clear end date
    document.getElementById('phone').value = '';  // Clear phone
});

</script>