/**
 * Auth: Vastivkta Nishad 
 * Date: 17 MAY 20204
 * Description: This file contains functions related to wfm
 */
//shifted from real_time_adherence.php and wfm_adherence_report.php
window.setInterval(function(){
    get_adherence();
}, 5000);
  // }, 10000)
function get_adherence()
{
    // alert("hi");
    var sch_id=$('#sch_id').val();
    if(sch_id!=0)
    var date_val=$('#startdatetime').val();
    // alert(date_val);
    $.post("WFM/get_real_time_adherence.php",
    {
    get_dat: date_val,sched_id:sch_id,
    },
    function(data, status){
    // alert(data);
    $("#div_adherence").html(data);
    console.log("real time adherence");
    
    });
}
//shifted from  wfm_adherence_report.php
// window.setInterval(function(){
//   get_adherence();
// }, 2000);

function get_adherence()
{
   //alert("hi");
    var sch_id=$('#sch_id').val();
    // if(sch_id!=0)
    var start_date_val=$('#startdatetime').val();
  var end_date_val=$('#enddatetime').val();
  var agent_id=$('#agent_list').val();
    // alert(start_date_val);
    $.post("WFM/get_adherence_report.php",
    {
      s_dat: start_date_val,e_dat: end_date_val,sched_id:sch_id,agent_id:agent_id,
    },
    function(data, status){
      // alert(data);
      $("#div_adherence_monthly").html(data);
      console.log("monthly adherence");   
    });
}

function export_excel(){ 
    document.frmagentdashboardd.action="export_wfm_adherence_report.php";
    document.frmagentdashboardd.submit(); 
}
//shifted from agent_shifts.php
$(function() {
    // var dt_cur=$('#startdatetime').val();
    var currentdate = new Date();
    var cuyear = currentdate.getFullYear();
    var cumonth = currentdate.getMonth();
    var cudate = currentdate.getDay();
        // alert(cuyear+" "+cumonth+" "+cudate);
    //maxDate:new Date(cuyear-18, cumonth, cudate, currentdate.getHours(), currentdate.getMinutes()),
    $('#startdatetime').datetimepicker({
    minDate:new Date(cuyear, cumonth, 1, 1, 30),
    maxDate:new Date(cuyear, cumonth+8, cudate, currentdate.getHours(), currentdate.getMinutes()),
    changeYear: true,
    changeMonth: true,
    yearRange:'1920:-0'
    });

    $('#from_time').timepicker();
    $('#to_time').timepicker();
    $('#from_break_time').datetimepicker();
    $('#to_break_time').datetimepicker();
    $('#from_leave_time').datetimepicker();
    $('#to_leave_time').datetimepicker();
    $('#date_time').datetimepicker();
    $('#date_edit_time').datetimepicker();  
    $('#from_reassign_time').datetimepicker();
    $('#to_reassign_time').datetimepicker();
    $('#from_swap_time').datetimepicker();
    $('#to_swap_time').datetimepicker();
    // $('#from_move_time').datetimepicker();
    $('#to_move_time').datetimepicker();

    // Start and End Date validation
    var from_move_time = $('#from_move_time');
        //startDateTextBox.datetimepicker('setDate', (new Date()) );
    from_move_time.datetimepicker({ 
    // timeFormat: 'HH:mm:ss', 
    onClose: function(dateText, inst) {
        if (endDateTextBox.val() != '') {
        var testStartDate = from_move_time.datetimepicker('getDate');
        var testEndDate = endDateTextBox.datetimepicker('getDate');
        if (testStartDate > testEndDate)
            endDateTextBox.datetimepicker('setDate', testStartDate);
        }
        else {
        endDateTextBox.val(dateText);
        }
    },
    onSelect: function (selectedDateTime){
        from_move_time.datetimepicker('option', 'minDate', from_move_time.datetimepicker('getDate') );
        from_move_time.datetimepicker('option', 'maxDate+6', from_move_time.datetimepicker('getDate') );

    }
    });
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
        startDateTextBox.datetimepicker('option', 'maxDate+6', startDateTextBox.datetimepicker('getDate') );

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
    });

});

function modal_popup(agentid,shiftid,agent_name,sched_id)
{
  $.post("WFM/wfm_get_value.php",
    {
      agent_details_id: agentid,shift_details_id: shiftid,sched_detail_id: sched_id,
    },
    function(data, status){
      // alert(data);
      var arr_data=data.split("|");
      $('#agent_name').val(agent_name);
      $('#agent_id').val(agentid);
      $('#shift_id').val(shiftid);
      $('#sched_id').val(sched_id);
      $('#from_time').val(arr_data[0]);
      $('#to_time').val(arr_data[1]);
      $('#shiftnames').text(arr_data[3]);
      $('select[name^="break_list"]').find($('option')).attr('selected',false);
      $('select[name^="break_list"]').val(arr_data[2].split(','));
      $("#myModal").css("display","block");
    
    });
  
}

function close_modal()
{

  $("#myModal").css("display","none");
}
function get_break_list()
   {
     // alert("Hi");
     var break_value="";
       $.post("WFM/wfm_get_value.php",
       {
         get_break_list: break_value,
       },
       function(data, status){
         // alert(data);
        // var data='<option>1</option>';
         $('select[name^="break_list"]').html(data);
       });
   }
   function get_agents_val(agentid,shiftid,fromdate,todate,sched_id,cond)
   {
     // alert("Hi");
       $.post("WFM/wfm_get_value.php",
       {
         get_agents_val: agentid, shift_id: shiftid, fromdate: fromdate, todate: todate, sched_id: sched_id,cond: cond,
       },
       function(data, status){
         // alert(data);
        // var data='<option>1</option>';
         $('#agent_list').html(data);
         $('#agent_list_swap').html(data);
       });
   }
   function get_agents_schud_val(agentid,shiftid,fromdate,todate,sched_id,cond){
       $.post("WFM/wfm_get_value.php",
       {
         get_agents_val_sch: agentid, shift_id: shiftid, fromdate: fromdate, todate: todate,cond: cond,
       },
       function(data, status){
         $('#schd_list_move').html(data);
       });
   }
   $(document).ready(function() {
     $("#chk_leave").click(function () {
             if ($(this).is(":checked")) {
                 $("#from_leave_time").attr('disabled',false);
                 $("#to_leave_time").attr('disabled',false); 
                 $("#from_leave_time").css('background-color',"#fff");
                 $("#to_leave_time").css('background-color',"#fff");   
             }
             else {
                 $("#from_leave_time").attr('disabled',true);
                 $("#to_leave_time").attr('disabled',true);
                 $("#from_leave_time").css('background-color',"#ccc");
                 $("#to_leave_time").css('background-color',"#ccc");
             }
         });
     $("#chk_reassign").click(function () {
             if ($(this).is(":checked")) {
                 $("#agent_list").attr('disabled',false);
                 $("#from_reassign_time").attr('disabled',false);
                 $("#to_reassign_time").attr('disabled',false); 
                 $("#agent_list").css('background-color',"#fff");
                 $("#from_reassign_time").css('background-color',"#fff");
                 $("#to_reassign_time").css('background-color',"#fff");   
             }
             else {
                 $("#agent_list").attr('disabled',true);
                 $("#from_reassign_time").attr('disabled',true);
                 $("#to_reassign_time").attr('disabled',true);
                 $("#agent_list").css('background-color',"#ccc");
                 $("#from_reassign_time").css('background-color',"#ccc");
                 $("#to_reassign_time").css('background-color',"#ccc");
             }
         });
     $("#chk_swap").click(function () {
             if ($(this).is(":checked")) {
                $("#agent_list").attr('disabled',true);
                $("#agent_list").css('background-color',"#ccc");
                 $("#from_reassign_time").attr('disabled',true);
                 $("#to_reassign_time").attr('disabled',true);
                 $("#from_leave_time").attr('disabled',true);
                 $("#to_leave_time").attr('disabled',true);
                 $("#chk_leave").attr('disabled',true);
                 $("#chk_reassign").attr('disabled',true);
                 $("#from_leave_time").css('background-color',"#ccc");
                 $("#to_leave_time").css('background-color',"#ccc");
                 $("#chk_leave").prop("checked", false);
                 $("#chk_reassign").prop("checked", false);
                 $("#from_reassign_time").css('background-color',"#ccc");
                 $("#to_reassign_time").css('background-color',"#ccc");
                 $("#from_time").attr('disabled',true);
                 $("#to_time").attr('disabled',true);
                 $("#break_list").attr('disabled',true);
                 $("#from_time").css('background-color',"#ccc");
                 $("#to_time").css('background-color',"#ccc");
                 $("#break_list").css('background-color',"#ccc");
                 $("#from_swap_time").attr('disabled',false);
                 $("#to_swap_time").attr('disabled',false);
                 $("#from_swap_time").css('background-color',"#fff");
                 $("#to_swap_time").css('background-color',"#fff");
                 $("#agent_list_swap").attr('disabled',false);
                 $("#agent_list_swap").css('background-color',"#fff");
                 $("#from_leave_time").val("");
                 $("#to_leave_time").val("");
                 $("#from_reassign_time").val("");
                 $("#to_reassign_time").val("");

                 $("#chk_move").attr('disabled',true);
                 $("#from_move_time").val("");
                 $("#to_move_time").val("");
                  
             }
             else 
             {
                   $("#chk_leave").attr('disabled',false);
                   $("#chk_reassign").attr('disabled',false);
                   $("#from_time").attr('disabled',false);
                   $("#to_time").attr('disabled',false);
                   $("#break_list").attr('disabled',false);
                   $("#from_time").css('background-color',"#fff");
                   $("#to_time").css('background-color',"#fff");
                   $("#break_list").css('background-color',"#fff");
                   $("#agent_list").attr('disabled',true);
                   $("#agent_list").css('background-color',"#ccc");
                   $("#from_swap_time").attr('disabled',false);
                   $("#from_swap_time").attr('disabled',true);
                   $("#to_swap_time").attr('disabled',true);
                   $("#from_swap_time").css('background-color',"#ccc");
                   $("#to_swap_time").css('background-color',"#ccc");
                   $("#agent_list_swap").attr('disabled',true);
                   $("#agent_list_swap").css('background-color',"#ccc");

                   $("#chk_move").attr('disabled',true);
                   $("#from_move_time").val("");
                  $("#to_move_time").val("");
             }
         });
     $("#btn_Cancel").click(function () {
             close_modal();
         });
     $("#to_reassign_time").on("change",function () {
             var agent_id=$("#agent_id").val();
             var shift_id=$("#shift_id").val();
             var sched_id=$("#sch_id").val();
             var from_date=$("#from_reassign_time").val();
             var to_date=$("#to_reassign_time").val();
             if(from_date!="" && to_date!="")
             {
               get_agents_val(agent_id,shift_id,from_date,to_date,sched_id,1);  
             }   
         });
     $("#to_swap_time").on("blur",function () {
             var agent_id=$("#agent_id").val();
             var shift_id=$("#shift_id").val();
             var sched_id=$("#sch_id").val();
             var from_date=$("#from_swap_time").val();
             var to_date=$("#to_swap_time").val();
             if(from_date!="" && to_date!="")
             {
               get_agents_val(agent_id,shift_id,from_date,to_date,sched_id,2);  
             }   
         });
     $("#chk_move").click(function () {
        if ($(this).is(":checked")) {
            $("#agent_list").attr('disabled',true);
            $("#agent_list").css('background-color',"#ccc");
             $("#from_reassign_time").attr('disabled',true);
             $("#to_reassign_time").attr('disabled',true);
             $("#from_leave_time").attr('disabled',true);
             $("#to_leave_time").attr('disabled',true);
             $("#chk_leave").attr('disabled',true);
             $("#chk_reassign").attr('disabled',true);
             $("#from_leave_time").css('background-color',"#ccc");
             $("#to_leave_time").css('background-color',"#ccc");
             $("#chk_leave").prop("checked", false);
             $("#chk_reassign").prop("checked", false);
             $("#from_reassign_time").css('background-color',"#ccc");
             $("#to_reassign_time").css('background-color',"#ccc");
             $("#from_time").attr('disabled',true);
             $("#to_time").attr('disabled',true);
             $("#break_list").attr('disabled',true);
             $("#from_time").css('background-color',"#ccc");
             $("#to_time").css('background-color',"#ccc");
             $("#break_list").css('background-color',"#ccc");
             $("#from_swap_time").attr('disabled',true);
             $("#to_swap_time").attr('disabled',true);
             $("#from_move_time").attr('disabled',false);
             $("#to_move_time").attr('disabled',false);
             $("#from_move_time").css('background-color',"#fff");
             $("#to_move_time").css('background-color',"#fff");
             $("#to_swap_time").css('background-color',"#ccc");
             $("#from_swap_time").css('background-color',"#ccc");
             $("#agent_list_swap").attr('disabled',true);
             $("#agent_list_swap").css('background-color',"#ccc");
             $("#schd_list_move").attr('disabled',false);
             $("#schd_list_move").css('background-color',"#fff");

             $("#chk_swap").attr('disabled',true);

             $("#from_leave_time").val("");
             $("#to_leave_time").val("");
             $("#from_reassign_time").val("");
             $("#to_reassign_time").val("");
             $("#from_swap_time").val("");
             $("#from_swap_time").val("")
         }else{
               $("#chk_leave").attr('disabled',false);
               $("#chk_reassign").attr('disabled',false);
               $("#from_time").attr('disabled',false);
               $("#to_time").attr('disabled',false);
               $("#break_list").attr('disabled',false);
               $("#from_time").css('background-color',"#fff");
               $("#to_time").css('background-color',"#fff");
               $("#break_list").css('background-color',"#fff");
               $("#agent_list").attr('disabled',true);
               $("#agent_list").css('background-color',"#ccc");
               $("#from_swap_time").attr('disabled',false);
               $("#from_swap_time").attr('disabled',true);
               $("#to_swap_time").attr('disabled',true);
               $("#from_swap_time").css('background-color',"#ccc");
               $("#to_swap_time").css('background-color',"#ccc");
               $("#agent_list_swap").attr('disabled',true);
               $("#agent_list_swap").css('background-color',"#ccc");
               $("#from_move_time").attr('disabled',true);
               $("#to_move_time").attr('disabled',true);
               $("#from_move_time").css('background-color',"#ccc");
               $("#to_move_time").css('background-color',"#ccc");
               $("#schd_list_move").attr('disabled',true);
               $("#schd_list_move").css('background-color',"#ccc");

               $("#to_move_time").attr('disabled',true);
               $("#from_move_time").attr('disabled',true);
               $("#from_move_time").css('background-color',"#cc");
               $("#to_move_time").css('background-color',"#cc");
               $("#chk_swap").attr('disabled',false);
         }
     });
     $("#to_move_time").on("blur",function () {
         var agent_id=$("#agent_id").val();
         var shift_id=$("#shift_id").val();
         var sched_id=$("#sch_id").val();
         var from_date=$("#from_move_time").val();
         var to_date=$("#to_move_time").val();
         if(from_date!="" && to_date!="")
         {
           get_agents_schud_val(agent_id,shift_id,from_date,to_date,sched_id,3);  
         }   
     });
   });
//code shifted from web_shift_assignment_report.php
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
//function shifted from web_shift_assignment_report.php

function dosubmit_shift(nval) {
    if (nval == 1) {
        document.myform.action = window.location.href; 
        document.myform.target = "_self";
        document.myform.submit();
    }
    if (nval == 2) {
        document.myform.action = "WFM/report/web_shift_assignment_export.php?export=1";
        document.myform.target = "_self";
        document.myform.submit();
    }
    if (nval == 3) {
        document.myform.action = "WFM/report/web_shift_assignment_export.php?print=";
        document.myform.target = "_blank";
        document.myform.submit();
    }
}

function Print(){ 
    window.print(); 
    setTimeout('window.close()', 10); 
  } 
//function shifted web_agentwise_assignment_report.php

function dosubmit_agentwise(nval) {
    if (nval == 1) {
        document.myform.action = window.location.href; 
        document.myform.target = "_self";
        document.myform.submit();
    }
    if (nval == 2) {
        document.myform.action = "WFM/report/web_agentwise_assignment_export.php?export=1";
        document.myform.target = "_self";
        document.myform.submit();
    }
    if (nval == 3) {
        document.myform.action = "WFM/report/web_agentwise_assignment_export.php?print=";
        document.myform.target = "_blank";
        document.myform.submit();
    }
}

//function shifted from web_schedule_adherence_report.php

function dosubmit_schedule(nval) {
    console.log("function triggered");
    
    if (nval == 1) {
        document.myform.action = window.location.href; 
        document.myform.target = "_self";
        document.myform.submit();
    }
    if (nval == 2) {
        document.myform.action = "WFM/report/web_schedule_adherence_export.php?export=";
        document.myform.target = "_self";
        document.myform.submit();
    }
    if (nval == 3) {
        document.myform.action = "WFM/report/web_schedule_adherence_export.php?print=";
        document.myform.target = "_blank";
        document.myform.submit();
    }
}

function show_dispo(val)
{
// alert(val);
	if(val==3)
	{
		// $('#dispo2').css('display','block');
		// $('#dispo1').css('display','none');
	}
	else
	{
		// $('#dispo2').css('display','none');
		// $('#dispo1').css('display','block');
	}
}
//function shifted from web_shift_assignment_report_hist.php
function dosubmit_shift_hist(nval)
{
	if(nval==1)
	{
		document.myform.action=window.location.href;
		document.myform.target="_self";
		document.myform.submit();
	}
	if(nval==2)
	{
		document.myform.action="WFM/report/web_shift_assignment_export_hist.php?export=1";
		document.myform.target="_self";
		document.myform.submit();
	}
	if(nval==3)
	{
		document.myform.action="WFM/report/web_shift_assignment_export_hist.php?print=";
		document.myform.target="_blank";
		document.myform.submit();
	}
}
//function shifted from web_agentwise_assignment_report_hist.php
function dosubmit_agent_hist(nval)
{
	if(nval==1)
	{
		document.myform.action=window.location.href;
		document.myform.target="_self";
		document.myform.submit();
	}
	if(nval==2)
	{
		document.myform.action="WFM/report/web_agentwise_assignment_export_hist.php?export=1";
		document.myform.target="_self";
		document.myform.submit();
	}
	if(nval==3)
	{
		document.myform.action="WFM/report/web_agentwise_assignment_export_hist.php?print=";
		document.myform.target="_blank";
		document.myform.submit();
	}
}
//function shifted from web_schedule_adherence_report_hist.php
function dosubmit_schedule_hist(nval){
	if(nval==1)
	{
		document.myform.action=window.location.href;
		document.myform.target="_self";
		document.myform.submit();
	}
	if(nval==2){
		document.myform.action="WFM/report/web_schedule_adherence_export_hist.php?export=1";
		document.myform.target="_self";
		document.myform.submit();
	}
	if(nval==3)
	{
		document.myform.action="WFM/report/web_schedule_adherence_export_hist.php?print=";
		document.myform.target="_blank";
		document.myform.submit();
	}
}
//datatable code for wfm reports
$(document).ready(function() {
    console.log("Document is ready");
    shift_assignment_datatable();
    
    function shift_assignment_datatable(i_procSchedID = '') {
       
        var dataTable = $('#shift_report').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 25, // Set default number of records per page to 30
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "searching": false,
            "ajax": {
                url: "WFM/report/wfm_fetch_data.php",
                type: "POST",
                data: {
                    i_procSchedID: i_procSchedID,
                    action: 'shift_report'
                }
            }
        });
    }    
    $('#shift_form').submit(function(e) {
        e.preventDefault();
        var i_procSchedID = $('#i_procSchedID').val();
       
        // Destroy DataTable instance to reinitialize with new data
        $('#shift_report').DataTable().destroy();
    
        // Call fill_datatables function with appropriate filter parameters
        shift_assignment_datatable(i_procSchedID);
        console.log(i_procSchedID);
    });

    agentwise_assignment_datatable();
    function agentwise_assignment_datatable(AtxUserID = ''){
      
        var datatable = $('#agentwise_report').DataTable({
            
            "processing": true ,
            "serverSide": true , 
            "order": [],
            "pageLenght": 25,
            "lenghtMenu": [ [10, 25, 50, 100,  -1], [10, 25, 50,100, "All"] ],
            "searching" : false,
            "ajax": {
                url:"WFM/report/wfm_fetch_data.php",
                type: "POST",
                data:{
                    AtxUserID:AtxUserID,
                    action:'agentwise_report'
                }
            }});
    }

    $('#agentwise_form').submit(function(e) {
        e.preventDefault();
        var AtxUserID = $('#AtxUserID').val();
       
        // Destroy DataTable instance to reinitialize with new data
        $('#agentwise_report').DataTable().destroy();
    
        // Call fill_datatables function with appropriate filter parameters
        agentwise_assignment_datatable(AtxUserID);
        console.log(AtxUserID);
    });

    schedule_adherence_datatables();
    function schedule_adherence_datatables(startdatetime = ''){

        var datatable = $('#adherence_report').DataTable({
            
            "processing": true ,
            "serverSide": true , 
            "order": [],
            "pageLenght": 25,
            "lenghtMenu": [ [10, 25, 50, 100,  -1], [10, 25, 50,100, "All"] ],
            "searching" : false,
            "ajax": {
                url:"WFM/report/wfm_fetch_data.php",
                type: "POST",
                data:{
                    startdatetime:startdatetime,
                    action:'adherence_report'
                }
            }});
    }
    $('#adherence_form').submit(function(e) {
        e.preventDefault();
        var startdatetime = $('#startdatetime').val();
       
        // Destroy DataTable instance to reinitialize with new data
        $('#adherence_report').DataTable().destroy();
    
        // Call fill_datatables function with appropriate filter parameters
        schedule_adherence_datatables(startdatetime);
        console.log(startdatetime);
    });

    shift_assignment_hist_datatable();
    function shift_assignment_hist_datatable(i_procSchedID = '') {
        
        var dataTable = $('#shift_report_hist').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 25, // Set default number of records per page to 30
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "searching": false,
            "ajax": {
                url: "WFM/report/wfm_fetch_data.php",
                type: "POST",
                data: {
                    i_procSchedID: i_procSchedID,
                    action: 'shift_report_hist'
                }
            }
        });
    }
    $('#shift_hist_form').submit(function(e) {
        e.preventDefault();
        var i_procSchedID = $('#i_procSchedID').val();
       
        // Destroy DataTable instance to reinitialize with new data
        $('#shift_report_hist').DataTable().destroy();
    
        // Call fill_datatables function with appropriate filter parameters
        shift_assignment_hist_datatable(i_procSchedID);
        console.log(i_procSchedID);
    });
    agentwise_assignment_hist_datatable();
    function agentwise_assignment_hist_datatable(AtxUserID = ''){
        var dataTable = $('#agentwise_report_hist').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 25, // Set default number of records per page to 30
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "searching": false,
            "ajax": {
                url: "WFM/report/wfm_fetch_data.php",
                type: "POST",
                data: {
                    AtxUserID:AtxUserID,
                    action: 'agentwise_report_hist'
                }
            }
        }); 
    }
    $('#agentwise_hist_form').submit(function(e) {
        e.preventDefault();
        var AtxUserID = $('#AtxUserID').val();
       
        // Destroy DataTable instance to reinitialize with new data
        $('#agentwise_report_hist').DataTable().destroy();
    
        // Call fill_datatables function with appropriate filter parameters
        agentwise_assignment_hist_datatable(AtxUserID);
        console.log(AtxUserID);
    });
    schedule_adherence_hist_datatables();
    function schedule_adherence_hist_datatables(startdatetime = ''){
        var dataTable = $('#schedule_report_hist').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 25, // Set default number of records per page to 30
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "searching": false,
            "ajax": {
                url: "WFM/report/wfm_fetch_data.php",
                type: "POST",
                data: {
                    startdatetime:startdatetime,
                    action: 'schedule_report_hist'
                }
            }
        }); 
    }
    $('#schedule_hist_form').submit(function(e) {
        e.preventDefault();
        var startdatetime = $('#startdatetime').val();
       
        // Destroy DataTable instance to reinitialize with new data
        $('#agentwise_report_hist').DataTable().destroy();
    
        // Call fill_datatables function with appropriate filter parameters
        schedule_adherence_hist_datatables(startdatetime);
        console.log(startdatetime);
    });
});
