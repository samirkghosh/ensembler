<?php
/***
 * Auth: Vastvikta Nishad
 * Date:  21 Mar  2024
 * Description: To Display data of Twitter  To Create update or Delete Case and reply to the DM's
 * 
*/
 /* check license access or not for  this module
 adding this file makes the page blank with no table and data and with only the sidebar */
include_once("../../ensembler/function/classify_function.php"); 
  $module_flag_customer = module_license('Twitter');
  if($module_flag_customer !='1'){
    header("Location:web_admin_dashboard.php"); 
    exit();
  }
/***END***/
include ("../web_function.php");

// function  file for updating date according to latest month record [vastvikta][18-03-2025]
include ("get_last_date_function.php");
//functions for fetching data and other

$name = $_SESSION['logged'];
$todaysdate = $selection1 = $_POST['selection'];
if ($_GET['seltype'])
{
    $selection1 = $_GET['seltype'];
}
include ("date.php");
mysqli_query($link, "alter table $dbname.tbl_tweet add column i_reminder int(1) default 0;");

$msg = $_POST['mg'];
$phone = $_POST['phone'];

$mode = $_GET['mode'];

$dateRange =  get_date_twitter();

$startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
? $_REQUEST['sttartdatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

$enddatetime = (!empty($_REQUEST['enddatetime'])) 
? $_REQUEST['enddatetime'] 
: date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));

###########################################################################


$yest = date("Y-m-d", $yesterday);
$iallstatus = $_POST['allstatus']; ////              Status
$iallstatus1 = setStatusTwitter($iallstatus);

$iallstatus = (isset($_REQUEST['allstatus'])) ? $_REQUEST['allstatus'] : 4; ////              Status
?> 
<style type="text/css">
    .fil_div{
        width:20px;
        height:20px;
        border:1px solid #999999;
        border-radius:5px;
        white-space: nowrap;
    }
    .Unread{
        background:crimson;
        margin-left: -50px;
        display: inline-block;
    }

   .Read{
        background:#e1ba02;
        margin-left: -73px;
    }
    .read-container {
        margin-left: 400px; /* Adjust this value to control the gap */
    }

    .flag-yellow {
        background:#f1a00bd4 !important; 
        color:#fff;
    }

    .flag-red {
        background:#e34234db !important; 
        color:#fff;
    }

    .flag-green {
    background:#228b22de !important; 
    color:#fff;
    }

</style>

         <div class="col-sm-10 mt-3" style="padding-left:0">
            <span class="breadcrumb_head" style="height:37px;padding:9px 16px">
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="row">
                        <div class="col-sm-2">
                            <div class="row">
                               <div class="col-sm-7">x(Twitter)</div>
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
        <div class="Reports-page#" style="display: block;padding: 15px;border: #d4d4d4 1px solid;marginbottom:20px;min-height: 420px;margin-top:37px">
            <form method="post" name="cfrm" id="post_twitter">
                <div  style="margin-top:-1px;;background-color: #fff;">
                    <table class="tableview tableview-2 main-form new-customer">
                        <tr>
                            <td width="95" class="left boder0-right">&nbsp;
                                <label>Start Date </label>
                            </td>
                            <td width="226" class="left boder0-right">
                               
                                <span class="left boder0-left">
                                <input type="text" name="startdatetime" class="date_class dob1"
                                value="<?=$startdatetime?>" id="startdatetime">&nbsp;
                                </span>
                            </td>
                            <td width="112" class="left boder0-right"><label>End Date </label>
                            </td>
                            <td width="230" align="left" class="left boder0-right">
                                <span class="left boder0-left">
                                <input type="text" name="enddatetime" class="date_class dob1"
                                value="<?=$enddatetime?>" id="enddatetime">
                                </span>
                            </td>
                            <td width="112" class="left boder0-right"><label>From</label>
                            </td>
                            <td width="230" align="left" class="left boder0-right">
                                <span class="left boder0-left">
                                <?php 
                                 $query = fetchTweetId();
                                 ?>
                                <select name="v_Screenname" id="v_Screenname" class="select-styl1" style="width:180px">
                                <option value="">Select</option>

                                <? while ($row = mysqli_fetch_assoc($query)) {
                                $name = $row['v_Screenname'];
                                $selected = "";

                                if($name == $_POST['v_Screenname']) 
                                {
                                    $selected = "selected";
                                }else
                                {
                                    $selected = "";
                                }
                                ?>
                                <option value="<?=$name?>" <?=$selected?>><?=$name?></option>
                                
                                <?}?>
                                </select>
                                
                                </span>
                            </td>  
                        </tr>
                        <tr>
                        </td>
                            <td width="112" class="left boder0-right"><label>Name</label>
                            </td>
                          <!-- added filters n the basis of name and username [vastvikta][13-12-2024] -->
                                <td width="230" align="left" class="left boder0-right">
                                <span class="left boder0-left">
                                <?php 
                                    // Query to fetch distinct v_name
                                    $query = "SELECT DISTINCT v_name FROM $db.tbl_tweet";
                                    $result = mysqli_query($link, $query);

                                    if (!$result) {
                                        die("Query failed: " . mysqli_error($link));
                                    }
                                ?>
                                <select name="v_name" id="v_name" class="select-styl1" style="width:180px">
                                <option value="">Select</option>

                                <? while ($row = mysqli_fetch_assoc($result)) {
                                $name = $row['v_name'];
                                $selected = "";

                                if($name == $_POST['v_name']) 
                                {
                                    $selected = "selected";
                                }else
                                {
                                    $selected = "";
                                }
                                ?>
                                <option value="<?=$name?>" <?=$selected?>><?=$name?></option>
                                
                                <?}?>
                                </select>
                                
                                </span>
                            </td>  
                            <td width="112" class="left boder0-right"><label>Case Id</label></td>
                            <td width="150" align="left" class="left boder0-right">
                                <span class="left boder0-left">
                                    <?php 
                                    $query = mysqli_query($link, "SELECT ICASEID FROM $db.tbl_tweet WHERE ICASEID != '' GROUP BY ICASEID ASC ");
                                    ?>
                                    
                                    <input type="text" id="searchBox" placeholder="Search Case ID..And Select." onkeyup="filterFunction('ICASEID', 'searchBox', 'noCaseIdMessage')" style="width:150px; height:30px; margin-bottom: 5px;">
                                    
                                    <div id="dropdownContainer" class="dropdown-content" style="width:150px; display: none; position: absolute; z-index: 1; background-color: white; border: 1px solid #ccc;">
                                        <select name="ICASEID" id="ICASEID" class="select-styl1" size="5" style="width:100%; border: none; box-shadow: none; height: auto; margin: 0;" onchange="selectOption('ICASEID', 'searchBox')">
                                            <option value="0">Select</option>
                                            <?php while ($row = mysqli_fetch_assoc($query)) {
                                                $name = $row['ICASEID'];
                                                $selected = ($name == $_POST['ICASEID']) ? "selected" : "";
                                            ?>
                                                <option value="<?= $name ?>" <?= $selected ?>><?= $name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <!-- "No records found" message now above the dropdown -->
                                    <div id="noCaseIdMessage" style="display: none; color: red; font-size: 12px; padding: 5px;">No records found</div>
                                </span>
                            </td>                      
                            <td class="left  boder0-left" colspan="4">
                                <input name="sub1"
                                value="Run Report" id="post_twitter" class="button-orange1 set_button" type="submit">   
                                <?php
                                $twitter = base64_encode('twitter');
                                
                                ?>                         
                          <input name="sub1"
                                value="Reset" class="button-orange1 set_button"  onclick="window.location.href='omni_channel.php?token=<?php echo $twitter; ?>';"  id='reset_twitter' type="Reset">
                            </td>
                        </tr>        
                    </table>
                    </div>
                    <div class="table">
                    <table width="100%" align="center" border="0" class="tableview tableview-2"  id="twitter_table">
                        <thead>
                            <tr class="background">
                                <td align="center" valign="middle" width="6%"> S.No.</td>
                                <td align="center" valign="middle" width="15%">Date </td>
                                <td align="center" valign="middle" width="14%">From </td>
                                <td align="center" valign="middle" width="14%">Name</td>
                                <td align="center" valign="middle" width="24%">Description</td>
                                <td align="center" valign="middle" width="8%">Case</td>
                                <td align="center" valign="middle" width="8%">Status</td>
                               <td align="center" valign="middle" width="8%">Action</td> 
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
</div>
<script type="text/javascript">
    function filterFunction(selectId, searchBoxId, messageDivId) {
        var input, filter, select, options, i, visibleOptionsCount;
        
        // Get the input element and its value
        input = document.getElementById(searchBoxId);
        filter = input.value.toUpperCase();
        
        // Get the select element and its options
        select = document.getElementById(selectId);
        options = select.getElementsByTagName("option");
        visibleOptionsCount = 0;

        // Show the dropdown if the input has text
        var dropdownContainer = document.getElementById('dropdownContainer');
        if (filter.length > 0) {
            dropdownContainer.style.display = "block";
        }

        // Loop through all options and check if they match the filter text
        var hasVisibleOptions = false;
        for (i = 1; i < options.length; i++) { // Start from 1 to skip the "Select" option
            if (options[i].value === "") continue;
            txtValue = options[i].textContent || options[i].innerText;
            
            // If the option text matches the filter, display it; otherwise, hide it
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                options[i].style.display = "";
                hasVisibleOptions = true;
            } else {
                options[i].style.display = "none";
            }
        }

        // Get the message div for "No records found"
        var messageDiv = document.getElementById(messageDivId);
        
        // Show or hide the "No records found" message based on the visibility of options
        if (!hasVisibleOptions && filter.length > 0) {
            dropdownContainer.style.display = "none"; // Hide dropdown
            messageDiv.style.display = "block";  // Show "No records found" message
        } else {
            dropdownContainer.style.display = "block";  // Show dropdown
            messageDiv.style.display = "none";  // Hide "No records found" message
        }

        // Hide dropdown if input is empty
        if (filter.length === 0) {
            dropdownContainer.style.display = "none";
            messageDiv.style.display = "none"; // Hide "No records found" message
        }
    }

    function selectOption(selectId, searchBoxId) {
        var select = document.getElementById(selectId);
        var searchBox = document.getElementById(searchBoxId);
        
        // Update the search box value with the selected option text
        searchBox.value = select.options[select.selectedIndex].text;
        
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