<?php
/***
 * Auth: Vastvikta Nishad
 * Date:  10 Mar  2024
 * Description: To Display Web Email Complaint Data
 * 
*/

/* check license access or not for  this module*/
include_once("../../ensembler/function/classify_function.php"); 

include ("get_last_date_function.php");
$module_flag_customer = module_license('Email');
if($module_flag_customer !='1'){
    header("Location:web_admin_dashboard.php"); 
    exit();
}
/***END***/
$name= $_SESSION['logged'];
$iallstatus=(isset($_REQUEST['allstatus'])) ? $_REQUEST['allstatus'] : 4; ////Status
$msg=$_REQUEST['mg'];
$email=$_REQUEST['email'];
$mode=$_GET['mode'];

function datetimeformat($datetime)
{
    $cdate1=explode(' ',$datetime);  // to separate the date and time
    $cdate=$cdate1[0];  #######   date
    $ctime=$cdate1[1];  #######   time
    $cdateexplode=explode('-',$cdate);
    $cdateexp=$cdateexplode[2].'-'.$cdateexplode[1].'-'.$cdateexplode[0];
    //print $cdateexp;
    $cdatetime=$cdateexp.' '.$ctime;
    return($cdatetime);
}
function getStringBetween($str,$from,$to)
{
$sub = substr($str, stripos($str,$from)+strlen($from),strlen($str));
return substr($sub,0,stripos($sub,$to));
}
?>
<style>
    .flag-yellow {
        background:#f1a00bd4 !important; 
        color:#fff;
    }

    .flag-red{
        background:#e34234db !important; 
        color:#fff;
    }
    .flag-green{
        background:#228b22de !important; 
        color:#fff;
    }
    .new-customer .select-styl1 {
        width: 167px;
    }
  .fil_div
   {
      width:20px;
      height:20px;
      border:1px solid #999999;
      border-radius:5px;
      white-space: nowrap;
   }

   .read-container {
            margin-left: 400px; /* Adjust this value to control the gap */
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

</style>
<div class="col-sm-10 mt-3" style="padding-left:0">
<span class="breadcrumb_head" style="height:37px;padding:9px 16px">
    <div class="row">
        <div class="col-sm-12">
          <div class="row">
            <div class="col-sm-3">
                <div class="row">
                   <div class="col-sm-3">Email</div>
                   <div class="col-sm-5">
                    <select name="page" onchange="window.location=this.value">
                        <?php
                        $email_enquiry = base64_encode('email_enquiry');
                        $email_complaint = base64_encode('email_complaint');
                        ?>
                        <option value="omni_channel.php?token=<?php echo $email_complaint?>" selected>COMPLAINT</option>
                        <option value="omni_channel.php?token=<?php echo $email_enquiry?>">ENQUIRY</option>
                    </select>
                </div>
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
<div class="Reports-page#" style="display: block;border: #d4d4d4 1px solid;marginbottom:20px;min-height: 420px;margin-top:37px;background-color: #fff;">
    <form method="post" name="cfrm" id="post_complaint">
        <input type="hidden" name="id" value="<?php echo $_GET['id'];?>" id="inte_id">
        <div  style="margin-top:-1px;background-color: #fff;">
            <table class="tableview tableview-2 main-form new-customer">
                <tr>
                    <!-- <td width="95" class="left boder0-right">&nbsp;
                        
                    </td> -->
                    <td width="226" class="left boder0-right">
                    <label>Start Date </label>
                        <?php
                           // Determine start and end datetime parameters[vastvikta][17-03-2025]
                           
                        $dateRange =  get_date_email_complaint();

                        $startdatetime = (!empty($_REQUEST['sttartdatetime'])) 
                            ? $_REQUEST['sttartdatetime'] 
                            : date("d-m-Y H:i:s", strtotime($dateRange['start_date'] . " 00:00:00"));

                        $enddatetime = (!empty($_REQUEST['enddatetime'])) 
                            ? $_REQUEST['enddatetime'] 
                            : date("d-m-Y H:i:s", strtotime($dateRange['end_date'] . " 23:59:59"));
                            ?>
                        <span class="left boder0-left">
                            <input type="text" name="startdatetime" class="date_class dob1"  value="<?=$startdatetime?>" id="startdatetime">&nbsp;
                        </span>
                    </td>
                    <!-- <td width="112" class="left boder0-right"></td> -->
                    <td width="230" align="left" class="left boder0-right">
                    <label>End Date </label>
                        <span class="left boder0-left">
                            <input type="text" name="enddatetime" class="date_class dob1"  value="<?=$enddatetime?>" id="enddatetime">
                        </span>
                    </td>
                    <!-- <td width="180" class="left boder0-right">&nbsp;
                        
                    </td> -->
                    <td width="210" class="left boder0-right">
                    <label>Email ID</label>
                        <span class="left boder0-left">
                            <input type="text" class="select-styl1" id="email" name="email" value="<?=$email?>">
                        </span>
                    </td>
                    <td width="210" class="left boder0-right"> <label>Status</label>
                    
                        &nbsp;
                        <select name="allstatus" id="iallstatus" class="select-styl1">
                            <?php if($iallstatus == 3) { ?>
                            <option value="3" selected>New Case</option>
                            <?php } ?>
                            <option value="4" <?php if($iallstatus == 4) echo "selected"; ?>>ALL</option>
                            <option value="0" <?php if($iallstatus == 0) echo "selected"; ?>>Case Not Created</option>
                            <option value="1" <?php if($iallstatus == 1) echo "selected"; ?>>Case Created</option>
                            <!-- <option value="2" <?php if($iallstatus == 2) echo "selected"; ?>>Deleted</option> -->
                        </select>
    
                    </td>
                </tr>
                <tr>
                    <td>
                        <label>Classification</label>
                        <select name="serviceable" id="serviceable" class="select-styl1 classification">
                                <option value="">Select</option>
                                <option value="1">Serviceable</option>
                                <option value="2">Non servicable </option>
                                <option value="3">Spam</option>
                                <option value="4">Inquiry</option>
                        </select>
                    </td>
                        <td> <label>Sentiment</label>
                        <select name="sentiment" id="sentiment" class="select-styl1">
                                <option value="">Select</option>
                                <option value="negative">Negative</option>
                                <option value="positive">Positive</option>
                                <option value="neutral">Neutral</option>
                        </select>
                    </td>
                    <!--   * Author: Ritu Modi 
                           * Date: 02-09-2024  
                           * On the advice of ajay sir i have added one filters so that the data can be seached easily.-->
                    <td width="112"><label>Case ID</label>
                        <span class="left boder0-left" style="display: inline-block;">
                            <?php 
                            $query = mysqli_query($link, "SELECT ICASEID FROM $db.web_email_information WHERE ICASEID != '' GROUP BY ICASEID ASC ");
                            ?>                                    
                            <input type="number" id="searchBox" placeholder="Search Case ID and Select" onkeyup="filterFunction('ICASEID', 'searchBox', 'noCaseIdMessage')" class="select-styl1">

                            <div id="dropdownContainer" class="dropdown-content" style="width:170px; display: none; position: absolute; z-index: 1; background-color: white; border: 1px solid #ccc;">
                                <select name="ICASEID" id="ICASEID" class="select-styl1" size="5" style="width:100%; border: none; box-shadow: none; height: auto; margin: 0; display: none;" onchange="selectOption('ICASEID', 'searchBox')">
                                    <option value="">Select</option>
                                    <?php while ($row = mysqli_fetch_assoc($query)) {
                                        $name = $row['ICASEID'];
                                        $selected = ($name == $_POST['ICASEID']) ? "selected" : "";
                                    ?>
                                        <option value="<?= htmlspecialchars($name) ?>" <?= $selected ?>><?= htmlspecialchars($name) ?></option>
                                    <?php } ?>
                                </select>
                                <div id="noCaseIdMessage" style="display: none; color: red; font-size: 12px; padding: 5px;">No records found</div>
                            </div>
                        </span>
                    </td>
                    <td class="left boder0-left" colspan="2">
                        <input type='submit' name='sub1' value='Run Report' id="set_button_complaint" class="button-orange1 set_button" />
                        <input type='button' name='reset' value='Reset' id="reset_button_complaint" class="button-orange1 reset_button">
                    </td>
                </tr>
            </table>
        </div>
        <div class="table">
            <table class="tableview tableview-2" id="email_complaint_table">
                <thead>
                    <tr class="background">
                        <td align="left" valign="center" width="6%" >S.No.</td>
                        <td align="left" valign="center" width="13%" >Date </td>
                        <td align="left" valign="center" width="15%" >From Email </td>
                        <td align="left" valign="center" width="25%" >Subject </td>
                        <td align="left" valign="center" width="10%" >Case ID</td>
                        <td align="left" valign="center" width="10%" >Status</td>
                        <td align="left" valign="center" width="10%" >Sentiment</td>
                        <td align="left" valign="center" width="20%" >Classification</td>
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
        
        // Update the search box value with the selected option text
        searchBox.value = select.options[select.selectedIndex].text;
        
        // Hide the dropdown after an option is selected
        document.getElementById('dropdownContainer').style.display = "none";
    }

    // Hide the dropdown if clicking outside of it
    document.addEventListener('click', function(event) {
        var dropdownContainer = document.getElementById('dropdownContainer');
        var searchBox = document.getElementById('searchBoxId');
        if (!dropdownContainer.contains(event.target) && !searchBox.contains(event.target)) {
            dropdownContainer.style.display = 'none';
        }
    });
</script>