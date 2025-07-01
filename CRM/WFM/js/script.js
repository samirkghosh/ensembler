/*--------- real time adherence wfm code 
date : 16-03-2023 -----*/
$(function() {
	$('#main-menu').smartmenus({
	  mainMenuSubOffsetX: -1,
	  subMenusSubOffsetX: 10,
	  subMenusSubOffsetY: 0
	});
});
function clearText(field){
    if (field.defaultValue == field.value) field.value = '';
    else if (field.value == '') field.value = field.defaultValue;
}

$(function(){
    $(".wrapper1").scroll(function(){
        $(".wrapper2")
            .scrollLeft($(".wrapper1").scrollLeft());
    });
    $(".wrapper2").scroll(function(){
        $(".wrapper1")
            .scrollLeft($(".wrapper2").scrollLeft());
    });
  
  
  $(".wrapper3").scroll(function(){
        $(".wrapper4")
            .scrollLeft($(".wrapper3").scrollLeft());
    });
    $(".wrapper4").scroll(function(){
        $(".wrapper3")
            .scrollLeft($(".wrapper4").scrollLeft());
    });
  
  $(".wrapper5").scroll(function(){
        $(".wrapper6")
            .scrollLeft($(".wrapper5").scrollLeft());
    });
    $(".wrapper6").scroll(function(){
        $(".wrapper5")
            .scrollLeft($(".wrapper6").scrollLeft());
    });
  
  $(".wrapper7").scroll(function(){
        $(".wrapper8")
            .scrollLeft($(".wrapper7").scrollLeft());
    });
    $(".wrapper8").scroll(function(){
        $(".wrapper7")
            .scrollLeft($(".wrapper8").scrollLeft());
    });
});

$(document).ready(function(){
    //Examples of how to assign the Colorbox event to elements
    $(".ico-setting").colorbox({iframe:true, innerWidth:410, innerHeight:200});
    $(".ico-display").colorbox({iframe:true, innerWidth:800, innerHeight:85});
    $(".kno-display").colorbox({iframe:true, width:"50%", height:"80%"});
    $(".supportsection").colorbox({iframe:true, innerWidth:550, innerHeight:400});
    $(".newdocument").colorbox({iframe:true, innerWidth:550, innerHeight:390});
    $(".group3").colorbox({rel:'group3', transition:"none", width:"75%", height:"75%"});
    $(".group4").colorbox({rel:'group4', slideshow:true});
    $(".ajax").colorbox();
    $(".form-ele").colorbox({iframe:true, innerWidth:250, innerHeight:390});
    $(".vimeo").colorbox({iframe:true, innerWidth:500, innerHeight:409});
    $(".iframe").colorbox({iframe:true, width:"80%", height:"80%"});
    $(".inline").colorbox({inline:true, width:"450"});
    $("#inline_service_click").colorbox({inline:true, width:450, innerHeight:420});
    $(".inline_service_click2").colorbox({inline:true, width:450, innerHeight:420});
    $(".inline2").colorbox({inline:true, width:"450", height:"80%"});
    $(".callbacks").colorbox({
      onOpen:function(){ alert('onOpen: colorbox is about to open'); },
      onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
      onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
      onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
      onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
    });

    $('.non-retina').colorbox({rel:'group5', transition:'none'})
    $('.retina').colorbox({rel:'group5', transition:'none', retinaImage:true, retinaUrl:true});
    
    //Example of preserving a JavaScript event for inline calls.
    $("#click").click(function(){ 
      $('#click').css({"background-color":"#f00", "color":"#fff", "cursor":"inherit"}).text("Open this window again and this message will still be here.");
      return false;
    });
  });

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
  $('#dateto').datepicker();
  $('#datefrom').datepicker();
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

$(window).load(function(){
$('.regular-link a').click(function () {
  // alert ("Hello");
    $('a').removeClass('active');
    $('a').children('div').removeClass('in');
    $(this).addClass('active');
    $(this).children('div').addClass('in');
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


});
/*--------- end real time adherence wfm code -----*/