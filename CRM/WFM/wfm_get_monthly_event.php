<?php
?>
<!-- <script type="text/javascript" src="templates/js/jquery-ui.min2.js"></script> -->
<!-- <script type="text/javascript" src="templates/js/jquery-ui-timepicker-addon2.js"></script>
<link href="templates/fullcalendar/fullcalendar.css" rel="stylesheet" />
<link href="templates/fullcalendar/fullcalendar.print.css" rel="stylesheet" media="print" />
<script src="templates/fullcalendar/moment.min.js"></script>
<script src="templates/fullcalendar/fullcalendar.min.js"></script> -->

 <script>  
  $(document).ready(function() {
  var agent_id='<?=$_REQUEST['agent_list'];?>';
  var sched_id='<?=$_REQUEST['sch_id'];?>';

  get_calender(agent_id,sched_id);
  function get_calender(agent_id,sched_id){
    var url_l="get_agents_val="+agent_id+"&sch_id="+sched_id;
    var calendar = $('#calendar').fullCalendar({
      editable:false,
      events:'',
      header:{
      left:'prev,next today',
      center:'title',
      right:'month,agendaWeek,agendaDay'
    },
    events: 'WFM/wfm_load_events.php?'+url_l,
    selectable:true,
    selectHelper:true,
    
    editable:false,  
   });

      //window.location="wfm_agent_monthly_event_detail.php?"+url_l;
    }

  });
   
  </script>
  <style>
    .fc-time
    {
      display: none !important;
    }
    .fc-title
    {
      text-align: middle !important;
    }
    .fc-agendaWeek-button
    {
      display: none !important;
    }
    .fc-agendaDay-button
    {
      display: none !important;
    }
    .fc-leave
    {
      background-color: #F0F0F0 !important;
      color:#FF0000;
      border-color: #FF0000;
    }
    .fc-noleave
    {
      background-color: #2196F3 !important;
      color:#fff;
      border-color: #2196F3;
    }

  </style>
  <br />

  <br />
  <!-- <div class="container"> -->
   <div id="calendar"></div>
  <!-- </div> -->
 