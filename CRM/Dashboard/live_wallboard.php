<?php
/*
 * Auth: FARHAN AKHTAR
 * Date: 05-07-24
 * page: Live Wallboard
 * purpose: For monitoring current day data of CRM and Telephony 
*/
?>
<link rel="stylesheet" href="<?=$SiteURL?>public/css/wallboard.css">
<body>
   <div class="col-sm-12" style="background-color: white !important;">
      <?php
      // Set default start and end dates if not submitted
      $startdate = $_POST['startdatetime'] ?? date("d-m-Y 00:00:00");
      $enddate = $_POST['enddatetime'] ?? date("d-m-Y 23:59:59");
      ?>

      <input type="hidden" name="startdatetime" value="<?= $startdate ?>">
      <input type="hidden" name="enddatetime" value="<?= $enddate ?>">
      <!-- Dashboard Section -->
      <div class="col-sm-12">
         <div class="d-flex align-items-center justify-content-between">
            <div class="box">
               <p class="title">Total Cases</p>
               <h3 class="rate-percentage text-danger"><span id="total_cases_e"></span></h3>
            </div>
            <div class="box">
               <p class="title">Complaint Cases</p>
               <h3 class="rate-percentage text-danger"><span id="complaints_e"></span></h3>
            </div>
            <div class="box">
               <p class="title">Inquiries Cases</p>
               <h3 class="rate-percentage text-danger"><span id="inquiries_e"></span></h3>
            </div>
            <div class="box">
               <p class="title">Other Cases</p>
               <h3 class="rate-percentage text-danger"><span id="others_e"></span></h3>
            </div>
            <div class="box">
               <p class="title">Pending</p>
               <h3 class="rate-percentage text-danger"><span id="pending_e"></span></span></h3>
            </div>
            <div class="box">
               <p class="title">In Progress</p>
               <h3 class="rate-percentage text-danger"><span id="inprogress_e"></span></h3>
            </div>
            <div class="box">
               <p class="title">Closed</p>
               <h3 class="rate-percentage text-danger"><span id="closed_e"></span></h3>
            </div>
            <div class="box">
               <p class="title">Assigned</p>
               <h3 class="rate-percentage text-danger"><span id="assigned_e"></span></h3>
            </div>
            <div class="box">
               <p class="title">Escalated</p>
               <h3 class="rate-percentage text-danger"><span id="escalated_e"></span></h3>
            </div>

         </div>
      </div>

      <table class="tableview tableview-2">
         <tbody>
            <tr class="bg-danger text-white">

               <td>
                  Highest Call Answer -
                  <span id="longCallAnsName_e"></span>(<span id="longCallAns_e"></span>)
               </td>

               <td>
                  Highest Talk Time -
                  <span id="highestTalkTimeName_e"></span>(<span id="highestTalkTime_e"></span>)
               </td>

               <td>
                  Highest Login Time -
                  <span id="highestLoginTimeName_e"></span>(<span id="highestLoginTime_e"></span>)

               </td>

               <td colspan="2">Percentage of Answered Call -
                  <span id="percentage_answeredcall_e"></span>

               </td>
            </tr>
         </tbody>
      </table>

      <div class="col-sm-12">
         <div class="d-flex align-items-center justify-content-between">
            <div class="box">
               <p class="title">Inbound</p>
               <h3 class="rate-percentage text-info"><span id="inbound_t">0</span></h3>
            </div>
            <div class="box">
               <p class="title">Outbound</p>
               <h3 class="rate-percentage text-info"><span id="outbound_t">0</span></h3>
            </div>
            <div class="box">
               <p class="title">Missed Call</p>
               <h3 class="rate-percentage text-info"><span id="miscall_t">0</span></h3>
            </div>
            <div class="box">
               <p class="title">Voice Mail</p>
               <h3 class="rate-percentage text-info"><span id="voicemail_t">0</span></h3>
            </div>
            <div class="box">
               <p class="title">Abandon</p>
               <h3 class="rate-percentage text-info"><span id="abandon_t">0</span></h3>
            </div>
            <div class="box">
               <p class="title">Talktime</p>
               <h3 class="rate-percentage text-info"><span id="talktime_t">00:00:00</span></h3>
            </div>
            <div class="box">
               <p class="title">Average Talktime</p>
               <h3 class="rate-percentage text-info"><span id="avgtalktime_t">00:00:00</span></h3>
            </div>
            <div class="box">
               <p class="title">In Wrapup</p>
               <h3 class="rate-percentage text-info"><span id="wrapup_t">00:00:00</span></h3>
            </div>
            <div class="box">
               <p class="title">Overall Calls</p>
               <h3 class="rate-percentage text-info"><span id="overall_t">0</span></h3>
            </div>

         </div>
      </div>

      <table class="tableview tableview-2">
         <thead>
            <tr class="bg-success text-white">
               <td>
                  Twitter - <span id="twitter_r">0</span>
               </td>
               <td>
                  Email - <span id="email_r">0</span>
               </td>
               <td>
                  SMS - <span id="sms_r">0</span>
               </td>
               <td>
                  Live Chat - <span id="chat_r">0</span>
               </td>
               <td>
                  Facebook - <span id="facebook_r">0</span>
               </td>
               <td>
                  whatsapp - <span id="whatsapp_r">0</span>
               </td>
            </tr>
         </thead>
      </table>
      <div class="col-sm-12">
         <div class="row">
            <div class="col-sm-4">
               <div id="category_e" class="charts_div"></div>
            </div>
            <div class="col-sm-4">
               <div id="subcategory_e" class="charts_div"></div>
            </div>
            <div class="col-sm-4">
               <div id="language_e" class="charts_div"></div>
            </div>
         </div>
         <div class="row">
            <div class="col-sm-6">
               <div id="socialmedia_e" class="charts_div"></div>
            </div>
            <div class="col-sm-6">

            <div class="row">
               <div class="col-sm-12 mb-2">
                  <center><span class="live_agents">Live Agents - <span id="live_agents_count"></span></span></center>
               </div>
               <div class="col-sm-12">
                  <div class="marquee">
                     <div class="marquee__group">
                           <!-- <span class="liveusers"> 
                                    <img src="../public/images/agent.png" alt="User Image" class="liveuserimage">
                                       vastiviktanishad<br>
                                    <span class="custom-badge bg-primary text-white mx-3">READY </span>
                           </span>
                           <span class="liveusers"> 
                                    <img src="../public/images/agent.png" alt="User Image" class="liveuserimage">
                                      pawansethi<br>
                                    <span class="custom-badge bg-warning text-white mx-3">PAUSED </span>
                           </span> -->       
                     </div>
                  </div>
               </div>
            </div>
 

            </div>
         </div>
      </div>
   </div>
</body>
<!-- js files  -->
<script type="text/javascript" src="<?= $SiteURL ?>public/js/jquery.canvasjs.min.js"></script>
<script type="text/javascript" src="<?= $SiteURL ?>public/js/jquery.min.js"></script>

</html>