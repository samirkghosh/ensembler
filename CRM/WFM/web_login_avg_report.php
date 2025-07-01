<?php
include("../web_mysqlconnect.php");
$groupid=$_SESSION['user_group'];
$rspoc=$_SESSION['reginoal_spoc'];
?>

</head>
<body onload="get_login_avg()">
<form name="frmagentdashboardd" action="" method="post">
  <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Agent Average Report</span>
    <form name="frmagentdashboardd" action="" method="post">
      <table class="tableview tableview-2 main-form new-customer">
        <tbody>
          <tr>
            <td class="left boder0-right">
              <label for="">Select Agent</label>
              <select style="width:190px;" class="select-styl1" id="agent_list" name="agent_list">
                  <option value="">Select</option>
                  <?php   
                    $agent_query = mysqli_query($link,"select AtxUserID,AtxDisplayName from $db.uniuserprofile where AtxDisplayName!='' and AtxUserStatus=1 and AtxDesignation='Agent'");
                    while($agent_name_res = mysqli_fetch_array($agent_query)){
                    ?>
                      <option value='<?=$agent_name_res["AtxUserID"]?>' <? if($_REQUEST['agent_list']==$agent_name_res["AtxUserID"]){ echo 'selected'; } ?>><?=$agent_name_res["AtxDisplayName"]?></option><?
                    }
                   ?>
                </select>
            </td>
            
          </tr>
          <tr>
              <td class="left boder0-right">
                <label for="">From Date</label>
              <input type="text" name="sttartdatetime" class="date_class dob1 select-styl1" style="width:190px;" value="<?=$_REQUEST['sttartdatetime']?>" id="startdatetime" autocomplete="off" style="width:150px;">
              </td>
              <td class="left boder0-right">
                <label for="">To Date</label>
                <input type="text" name="enddatetime" class="date_class dob1 select-styl1" style="width:190px;" value="<?=$_REQUEST['enddatetime']?>" id="enddatetime" autocomplete="off" style="width:150px;">
              </td>
              <td class="left boder0-right">
                <input type='submit' name='sub1' value='Show' class="button-orange1 submit_wfm">
                <!-- <input type='submit' name='exp1' value='Export' onClick="export_excel();" class="submit_wfm button-orange1"> -->
              </td>
          </tr>
        </tbody>
      </table>            
      <div class="row <?=$rval?>">
        <div class="col-5">
        </div>
        <div class="col-15"><b>Agent Name</b>
        </div>
        <div class="col-15"><b>Avg. Logged-in</b>
        </div>
        <div class="col-15"><b>Avg. Talktime</b>
        </div>
        <div class="col-15"><b>Occupancy (%)</b>
        </div>
        <div class="col-15"><b>Adherence (%)</b>
        </div>
      </div>
      <div  id="div_login_monthly">
      </div>
    </form>
  <!-- End Right panel -->
</form>             
</body>
<script type="text/javascript">
$(function() {
  
  var currentdate = new Date();
  var cuyear = currentdate.getFullYear();
  var cumonth = currentdate.getMonth();
  var cudate = currentdate.getDay();
  //maxDate:new Date(cuyear-18, cumonth, cudate, currentdate.getHours(), currentdate.getMinutes()),
  $('#dob').datepicker({
  minDate:new Date(1920, 01, 01, 1, 30),
  maxDate:new Date(cuyear, cumonth, cudate, currentdate.getHours(), currentdate.getMinutes()),
  changeYear: true,
  changeMonth: true,
  yearRange:'1920:-0'
  });
  $('#startdatetime').datepicker();
  $('#enddatetime').datepicker();
  $('#agentdateto').datepicker();
  $('#agentdatefrom').datepicker();
  
// Start and End Date validation
  var startDateTextBox = $('#startdatetime');
    //startDateTextBox.datetimepicker('setDate', (new Date()) );
    startDateTextBox.datetimepicker({ 
      timeFormat: 'HH:mm:ss', 
      onClose: function(dateText, inst) {
        if (endDateTextBox.val() != '') {
          var testStartDate = startDateTextBox.datetimepicker('getDate');
          var testEndDate = endDateTextBox.datetimepicker('getDate');
          if (testStartDate > testEndDate)
            endDateTextBox.datetimepicker('setDate', testStartDate);
        }
        else {
          endDateTextBox.val(dateText);
        }
      },
      onSelect: function (selectedDateTime){
        endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
      }
    });
  
  
  var endDateTextBox = $('#enddatetime');
    endDateTextBox.datetimepicker({ 
      timeFormat: 'HH:mm:ss',
      onClose: function(dateText, inst) {
        if (startDateTextBox.val() != '') {
          var testStartDate = startDateTextBox.datetimepicker('getDate');
          var testEndDate = endDateTextBox.datetimepicker('getDate');
          if (testStartDate > testEndDate)
            startDateTextBox.datetimepicker('setDate', testEndDate);
        }
        else {
          startDateTextBox.val(dateText);
        }
      },
      onSelect: function (selectedDateTime){
        startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate') );
      }
    });
  
 
var ex13 = $('#startdatetime');
$('#startdatetime').click(function(){
  //ex13.datetimepicker('setDate', (new Date()) );
});
});

$(document).ready(function(){ 
$('#cssmenu > ul > li ul').each(function(index, element){
  var count = $(element).find('li').length;
  // var content = '<span class="cnt">' + count + '</span>';
  var content = '<span class="cnt"> </span>';
  //alert('hello');
  $(element).closest('li').children('a').append(content);
});

// $('#cssmenu ul ul li:odd').addClass('odd');
// $('#cssmenu ul ul li:even').addClass('even');

$('#cssmenu > ul > li > a').click(function() {
  var checkElement = $(this).next();
  $('#cssmenu li').removeClass('active');
  $(this).closest('li').addClass('active'); 

  if((checkElement.is('ul')) && (checkElement.is(':visible'))) {
    $(this).closest('li').removeClass('active');
    checkElement.slideUp('normal');
  }
  if((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
    $('#cssmenu ul ul:visible').slideUp('normal');
    checkElement.slideDown('normal');
  }

  if($(this).closest('li').find('ul').children().length == 0) {
    return true;
  } else {
    return false; 
  }
});

$('#cssmenu > ul > li li a').click(function() {
  var checkElement = $(this).next();
    $('#cssmenu li li').removeClass('active');
    $(this).closest('li li').addClass('active');
  
  if((checkElement.is('ul li li ul')) && (checkElement.is(':visible'))) {
    $(this).closest('li').removeClass('active');
    checkElement.slideUp('normal');
  }
  if((checkElement.is('ul li li ul')) && (!checkElement.is(':visible'))) {
    $('#cssmenu ul ul ul:visible').slideUp('normal');
    checkElement.slideDown('normal');
  }
  
  if($(this).closest('li').find('ul').children().length == 0) {
    return true;
  } else {
    return false; 
  }
 
});

});
// window.setInterval(function(){
//   get_adherence();
// }, 2000);
function get_login_avg()
{
  var start_date_val=$('#startdatetime').val();
  var end_date_val=$('#enddatetime').val();
  var agent_id=$('#agent_list').val();
    // alert(start_date_val);
    $.post("WFM/get_avglogin_monthly_report.php",
    {
      s_dat: start_date_val,e_dat: end_date_val,agent_id:agent_id,
    },
    function(data, status){
      // alert(data);
      $("#div_login_monthly").html(data);
      console.log("monthly login average.");
    
    });
}
function export_excel(){ 
      // alert("fdgfdgf");
    document.frmagentdashboard.action="export_web_login_avg_report.php?agent_list=<?=$_REQUEST['agent_list']?>&sttartdatetime=<?=$_REQUEST['sttartdatetime']?>&enddatetime=<?=$_REQUEST['enddatetime']?>&";
    document.frmagentdashboard.submit();
  
}
</script>