jQuery(function ($){
    var Admin = {
        init: function (){
            jQuery("body").on('click', '.break_delete', this.HandleBreakDelete);//function to handle break delete request
            jQuery("body").on('click', '#submit', this.HandleBreaksSubmit); //function to handle the  inserting new break request
            jQuery("body").on('click', '#update', this.HandleBreaksSubmit); //function to handle the updating  existing break request 
            jQuery("body").on('click', '#insert', this.HandleShiftSubmit); //function to handle the inserting  new shift
            jQuery("body").on('click', '.schedule_delete', this.HandleScheduleDelete); //function to delete schedule 
            jQuery("body").on('click', '.update_schedule', this.HandleScheduleSubmit); //function to handle the updating  existing schedule 
            jQuery("body").on('click', '.submit_schedule', this.HandleScheduleSubmit); //function to handle the inserting  new schedule

        },
        HandleBreaksSubmit: function (e) {
            e.preventDefault(); // Define the event parameter
            // Extract necessary data from the form
            var breakId = $('#break_id').val();
            var breakName = $('#break_name').val();
            var fromTime = $('#from_timebreak').val();
            var toTime = $('#to_timebreak').val();
            var breakType = $('#break_type').val();
            
            // Send data to the server
            $.ajax({
                method: 'POST',
                url: 'WFM/wfm_function.php',
                data: {
                    'breakId': breakId,
                    'break_name': breakName,
                    'from_time': fromTime,
                    'to_time': toTime,
                    'break_type': breakType,
                    'action': (breakId !== '') ? 'update_breaks' : 'submit_breaks' 
                },
                success: function (response) {
                    $('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Break ' + (breakId !== '' ? 'updated' : 'inserted') + ' successfully</div>');
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                  
                }
            });
        },
        HandleScheduleSubmit: function (e) {
          e.preventDefault();
          console.log("schedule submit triggered");
      
          // Retrieve form values
           // Retrieve form values
    var scheduleName = $('#txt_schedule_name').val();
    var numberOfShifts = $('#txt_shift').val();
    var shifts = [];
    var agents = [];
    var breaks = [];
    var fromDate = $('#from_time').val();
    var toDate = $('#to_time').val();

    // Retrieve selected break values
    var selectedBreaks = $('#break_list1 :selected').map(function () {
        return $(this).val();
    }).get();

    // Retrieve shift details based on the number of shifts
    for (var i = 1; i <= numberOfShifts; i++) {
        shifts.push($('#ddl_shift' + i).val());
        agents.push($('#txt_agent' + i).val());
        breaks.push($('#break_list' + i).val());
    }

    console.table({
        'Schedule Name': scheduleName,
        'Number of Shifts': numberOfShifts,
        'Shifts': shifts,
        'Agents': agents,
        'Breaks': selectedBreaks, // Use selectedBreaks instead of breaks
        'From Date': fromDate,
        'To Date': toDate
    });

          
          // Perform AJAX request
          $.ajax({
              method: 'POST',
              url: 'WFM/wfm_function.php',
              data: {
                  'action': 'insert_or_update_schedule',
                  'scheduleName': scheduleName,
                  'numberOfShifts': numberOfShifts,
                  'shifts': shifts,
                  'agents': agents,
                  'breaks': breaks,
                  'fromDate': fromDate,
                  'toDate': toDate
              },
             
          });
      },
      
      
        
        HandleBreakDelete: function (e) {
            e.preventDefault();
            var breakId = $(this).data('id');
            console.log('Break ID:', breakId);
            if (confirm("Are you sure to delete?")) {
                $.ajax({
                    method: 'POST',
                    url: 'WFM/wfm_function.php',

                    data: { 'breakId': breakId, 'action': 'break_delete' },
                    success: function (response) {
                        $('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Break Deleted Successfully</div>');
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                      
                    }
                });
            }
        },
        HandleScheduleDelete: function(e){
          e.preventDefault();
          console.log("scheduleDelete triggered");
          
          var i_procSchedID = $(this).data('id');
          console.log(i_procSchedID);
          if (confirm("Are you sure to delete?")) {
            $.ajax({
                method: 'POST',
                url: 'WFM/wfm_function.php',
                data: { 'i_procSchedID': i_procSchedID, 'action': 'schedule_delete' },
                success: function (response) {
                    $('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Break Deleted Successfully</div>');
                    setTimeout(function () {
                        location.reload();
                    }, 2000);
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                  
                }
            });
        }

        },
        HandleShiftSubmit:function(e){
          e.preventDefault();
          var shiftName = $('#shift_name').val();
          var daysOfWeek = [];
          $('input[name="chk_days[]"]:checked').each(function () {
              daysOfWeek.push(parseInt($(this).val()));
          });
          
          var fromTime = $('#from_timebreak').val();
          var toTime = $('#to_timebreak').val();
          if (daysOfWeek.length === 0) {
            alert('Please select at least one day.');
            return; // Stop further execution if validation fails
        }
          $.ajax({
            method: 'POST',
            url: 'WFM/wfm_function.php',
            data: {
              'shift_name': shiftName,
              'chk_days': daysOfWeek,
              'from_time': fromTime,
              'to_time': toTime,
              'action': 'submit_shift' 
          },
          success: function (response) {
            $('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Shift Inserted  Successfully</div>');
            setTimeout(function () {
                location.reload();
            }, 5000);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
              
            }
          });
        }
    }
    Admin.init();
});
// close

//javascript code shifted from create_breaks.php 

function clearText(field){
    if (field.defaultValue == field.value) field.value = '';
    else if (field.value == '') field.value = field.defaultValue;
}

//code for select date time in wfm_adherence_report.php 
$(function() {
  var currentdate = new Date();
  var cuyear = currentdate.getFullYear();
  var cumonth = currentdate.getMonth();
  var cudate = currentdate.getDay();
  //maxDate:new Date(cuyear-18, cumonth, cudate, currentdate.getHours(), currentdate.getMinutes()),
  $('#dob').datepicker({
  minDate:new Date(1920, 1, 1, 1, 30),
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
//code for timepickeer in create_break

$(function() {
  var currentdate = new Date();
  var cuyear = currentdate.getFullYear();
  var cumonth = currentdate.getMonth();
  var cudate = currentdate.getDay();
  //maxDate:new Date(cuyear-18, cumonth, cudate, currentdate.getHours(), currentdate.getMinutes()),
  $('#dob').datepicker({
  minDate:new Date(1920, 1, 1, 1, 30),
  maxDate:new Date(cuyear, cumonth, cudate, currentdate.getHours(), currentdate.getMinutes()),
  changeYear: true,
  changeMonth: true,
  yearRange:'1920:-0'
  });
  $('#from_timebreak').timepicker();
  $('#to_timebreak').timepicker();
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
//code for date picker in create_schedule 
$(function() {
	
	var currentdate = new Date();
	var cuyear = currentdate.getFullYear();
	var cumonth = currentdate.getMonth();
	var cudate = currentdate.getDay();
	//maxDate:new Date(cuyear-18, cumonth, cudate, currentdate.getHours(), currentdate.getMinutes()),
	$('#dob').datepicker({
	minDate:new Date(1920, 1, 1, 1, 30),
	maxDate:new Date(cuyear, cumonth, cudate, currentdate.getHours(), currentdate.getMinutes()),
	changeYear: true,
	changeMonth: true,
	yearRange:'1920:-0'
	});
	$('#from_time').datepicker();
	$('#to_time').datepicker();
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

$(window).load(function(){
$('.regular-link a').click(function () {
// alert ("Hello");
  $('a').removeClass('active');
  $('a').children('div').removeClass('in');
  $(this).addClass('active');
  $(this).children('div').addClass('in');
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

$(window).load(function(){
$('.regular-link a').click(function () {
// alert ("Hello");
  $('a').removeClass('active');
  $('a').children('div').removeClass('in');
  $(this).addClass('active');
  $(this).children('div').addClass('in');
});
});

$(document).ready(function() {
  $("#break_type").change(function() {
    // alert($(this).val());
      if ($(this).val()!=1) {
        $("#from_time").val("");
        $("#to_time").val("");
          $(".class_schedule").css("display","none");
         
      } else {
          $(".class_schedule").css("display","block");
      }
  });
});

function getBreak(brk_id) {
    console.log("Sending request with brk_id: " + brk_id);
    
    $.post("WFM/wfm_get_value.php", {
        brk_id: brk_id,
    }, function(data, status) {
        console.log("Received data from server: " + data);

        if (data.trim() === "") {
            console.log("Error: Empty response from server.");
            return;
        }

        var brk_details = data.split('|');
        console.log("Break details:", brk_details);

        $('#break_id').val(brk_id);
        $('#break_type').val(brk_details[0]);
        $('#break_name').val(brk_details[1]);
        $('#from_time').val(brk_details[2]);
        $('#to_time').val(brk_details[3]);

        $('#submit').css('display', 'none');
        $("#update").css('display', 'block');
    });
}
//code for create_shift to select days date 27:02:2024
//To select checkboxes related to days
$(document).ready(function() {
  $("#chk_days1").change(function() {
      if (this.checked) {
          $(".chk_class").each(function() {
              this.checked=true;
          });
      } else {
          $(".chk_class").each(function() {
              this.checked=false;
          });
      }
  });

  $(".chk_class").click(function () {
      if ($(this).is(":checked")) {
          var isAllChecked = 0;

          $(".chk_class").each(function() {
              if (!this.checked)
                  isAllChecked = 1;
          });

          if (isAllChecked == 0) {
              $("#chk_days1").prop("checked", true);
          }     
      }
      else {
          $("#chk_days1").prop("checked", false);
      }
  });
});
//javascript code shifted from create schedule 
function getSchedDetails(sch_id,shift_id,cnt){
	$.post("WFM/wfm_get_value.php",
    {
      sched_id_details:sch_id,shift_id:shift_id,
    },
    function(data, status){

	var sched_details_list=data.split('|');
       if(cnt==0)
       {
       		$("#txt_agent1").val(sched_details_list[2]);
       		$('#break_list1').val(sched_details_list[3].split(','));
       }
       if(cnt==1)
       {
       		$("#txt_agent2").val(sched_details_list[2]);
       		$("#break_list2").val(sched_details_list[3].split(','));
       }
       if(cnt==2)
       {
       		$("#txt_agent3").val(sched_details_list[2]);
       		$("#break_list3").val(sched_details_list[3].split(','));
       }
      // var sched_details=data.split('|');
      // // $("#div_adherence").html(data);
      // console.log("get schedule list details");
    
    });
}
function getSched(sch_id){
    $.post("WFM/wfm_get_value.php",
    {
      sched_id:sch_id,
    },
    function(data, status){
       // alert(data);
      var sched_details=data.split('|');
      $("#txt_schedule_name").val(sched_details[0]);
      $("#txt_shift").val(sched_details[1]);
      var shift_list=sched_details[2];
      $("#from_time").val(sched_details[3]);
      $("#to_time").val(sched_details[4]);
      var shift_arr=shift_list.split(',');
      if(sched_details[1]==1)
      {
      	$("#ddl_shift1").attr('disabled',false);
      	$("#ddl_shift1").val(shift_arr[0]);
      	$("#txt_agent1").attr('disabled',false);
      	$("#break_list1").attr('disabled',false);
      }
      else if(sched_details[1]==2)
      {
      	$("#ddl_shift1").attr('disabled',false);
      	$("#ddl_shift1").val(shift_arr[0]);
      	$("#ddl_shift2").attr('disabled',false);
      	$("#ddl_shift2").val(shift_arr[1]);
      	$("#txt_agent1").attr('disabled',false);
      	$("#txt_agent2").attr('disabled',false);
      	$("#break_list1").attr('disabled',false);
      	$("#break_list2").attr('disabled',false);

      }
      else if(sched_details[1]==3)
      {
      	$("#ddl_shift1").attr('disabled',false);
      	$("#ddl_shift1").val(shift_arr[0]);
      	$("#ddl_shift2").attr('disabled',false);
      	$("#ddl_shift2").val(shift_arr[1]);
      	$("#ddl_shift3").attr('disabled',false);
      	$("#ddl_shift3").val(shift_arr[2]);
      	$("#txt_agent1").attr('disabled',false);
      	$("#txt_agent2").attr('disabled',false);
      	$("#txt_agent3").attr('disabled',false);
      	$("#break_list1").attr('disabled',false);
      	$("#break_list2").attr('disabled',false);
      	$("#break_list3").attr('disabled',false);
      }

      for(var i=0;i<shift_arr.length;i++)
      {
      	// alert(shift_arr[i]);
      	// var list_details=getSchedDetails(sch_id,shift_arr[i]);
      	getSchedDetails(sch_id,shift_arr[i],i);

      }

      // getSchedDetails(sch_id);
      // $("#div_adherence").html(data);
      $("#update").css('display','block');
      console.log("get schedule details");
    
    });
} 


function chk_val(){

	var shift_val=$("#txt_shift").val();
	if(shift_val>3 || shift_val<1)
	{
		alert("Please enter value between 1 and 3");
		$("#txt_shift").val("1");
		$("#ddl_shift1").prop("disabled",false);
		$("#ddl_shift2").prop("disabled",true);
		$("#ddl_shift3").prop("disabled",true);

		$("#txt_agent1").prop("disabled",false);
		$("#txt_agent2").prop("disabled",true);
		$("#txt_agent3").prop("disabled",true);

		$("#break_list1").prop("disabled",false);
		$("#break_list2").prop("disabled",true);
		$("#break_list3").prop("disabled",true);	

				
	}
	else
	{
		if(shift_val<=1)
		{
			$("#ddl_shift1").prop("disabled",false);
			$("#ddl_shift2").prop("disabled",true);
			$("#ddl_shift3").prop("disabled",true);

			$("#txt_agent1").prop("disabled",false);
			$("#txt_agent2").prop("disabled",true);
			$("#txt_agent3").prop("disabled",true);

			$("#break_list1").prop("disabled",false);
			$("#break_list2").prop("disabled",true);
			$("#break_list3").prop("disabled",true);	

		}
		else if(shift_val==2)
		{
			$("#ddl_shift1").prop("disabled",false);
			$("#ddl_shift2").prop("disabled",false);
			$("#ddl_shift3").prop("disabled",true);

			$("#txt_agent1").prop("disabled",false);
			$("#txt_agent2").prop("disabled",false);
			$("#txt_agent3").prop("disabled",true);

			$("#break_list1").prop("disabled",false);
			$("#break_list2").prop("disabled",false);
			$("#break_list3").prop("disabled",true);	
		}
		else if(shift_val==3)
		{
			$("#ddl_shift1").prop("disabled",false);
			$("#ddl_shift2").prop("disabled",false);
			$("#ddl_shift3").prop("disabled",false);

			$("#txt_agent1").prop("disabled",false);
			$("#txt_agent2").prop("disabled",false);
			$("#txt_agent3").prop("disabled",false);

			$("#break_list1").prop("disabled",false);
			$("#break_list2").prop("disabled",false);
			$("#break_list3").prop("disabled",false);	
		}
		else
		{
			$("#ddl_shift1").prop("disabled",true);
			$("#ddl_shift2").prop("disabled",true);
			$("#ddl_shift3").prop("disabled",true);

			$("#txt_agent1").prop("disabled",true);
			$("#txt_agent2").prop("disabled",true);
			$("#txt_agent3").prop("disabled",true);

			$("#break_list1").prop("disabled",true);
			$("#break_list2").prop("disabled",true);
			$("#break_list3").prop("disabled",true);	
		}
	}
}
$(document).ready(function(){
  $(".btn_gen_sched").on("click",function(){
    var val=$(this).prev().html();
    console.log('wfm_create_schedule_test');
    $.post("WFM/wfm_create_schedule_test.php",
      {
        get_schedule_data: val,
      },
      function(data, status){
        alert(data);     	
    });
  });
});
