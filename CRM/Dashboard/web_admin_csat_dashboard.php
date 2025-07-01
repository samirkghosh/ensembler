<?php 
/**
* Auth: Ritu modi 
* Date: 05/10/2024
* Description: This file displays the CSAT (Customer Satisfaction) and DSAT (Dissatisfaction) dashboard for agents. It allows users to view CSAT, DSAT, NPS (Net Promoter Score), and customer effort reports within a specified date range.
*/
$web_admin_dashboard = base64_encode('web_admin_dashboard');
// $json_score_cat = json_encode($str_score_cat);
// $json_tollfree = json_encode($str_tollfree);
// $json_email = json_encode($str_email);
// $json_NPS = json_encode($str_NPS);
// $json_NPSall = json_encode($str_NPSall);
// $json_consumer = json_encode($str_consumer);
?>
<form name="frmagentdashboard" action="" method="post">
   <div class="style2-table">
      <div class="style-title">
         <div class="row">
            <div class="col-sm-6">
              <h3>CSAT DSAT Dashboard</h3>
            </div>
            <div class="col-sm-6 d-flex flex-row-reverse bd-highlight">
                  <a href="dashboard_index.php?token=<?php echo $web_admin_dashboard;?>">Admin Dashboard</a> 
            </div>
         </div>
      </div>
      <div class="style-title2 st-title2-wth-lable main-form">
         <?php
            $startdate = isset($_POST['startdatetime']) ? $_POST['startdatetime'] : date("01-m-Y 00:00:00");
            $enddate = isset($_POST['enddatetime']) ? $_POST['enddatetime'] : date("d-m-Y 23:59:59");
            ?>
         From : <input type="text" name="startdatetime" class="date_class dob1"  value="<?=$startdate?>" id="startdatetime" autocomplete="off">&nbsp;&nbsp;
         To : <input type="text" name="enddatetime" class="date_class dob1"  value="<?=$enddate?>" id="enddatetime"  autocomplete="off">&nbsp;&nbsp;
         Type :
         <input type="radio" name="type_v" id="type-1" value="1" <? if($_REQUEST['type_v']==1) echo "checked"; ?> onclick="showDiv(this.value)" checked > All
         <input type="radio" name="type_v" id="type-2" value="2" <? if($_REQUEST['type_v']==2) echo "checked"; ?> onclick="showDiv(this.value)" /> Agent
         <input type='submit' name='sub1' value='GO' class="button-orange1" style="margin:0 !important;">
         <label>Agent Name</label>
         <div class="log-case">
            <?php
            $result=uniuserprofile();
            ?>
            <select name="agent" id="agent" class="select-styl1" style="width:190px">
            <option value="">Select Agent</option>
            <?php 
               while($row=mysqli_fetch_array($result)) {
               $AtxUserID=$row['AtxUserID']; 
               $AtxUserName=$row['AtxUserName'];
               if($AtxUserName == $_POST['agent'])
               {$sel = 'selected'; }
               else { $sel = ''; }
               ?>
            <option value='<?=$AtxUserName?>' <?=$sel?>><?=$AtxUserName?></option>
            <? } ?>
            </select>
         </div>
      </div>
       <!-- CSAT and DSAT charts -->
      <div class="table" id="SRallview">
            <?php
            $startdatetime = ($_POST['startdatetime']!='') ? date("Y-m-d 00:00:00",strtotime($_POST['startdatetime'])) : date("Y-m-01 00:00:00");
            $enddatetime = ($_POST['enddatetime']!='') ? date("Y-m-d",strtotime($_POST['enddatetime'])).date(" 23:59:59") : date("Y-m-d H:i:s");
            $query_str = " and (createddate between '$startdatetime' and '$enddatetime')";
            $query_str_sat = " and (Connect_time between '$startdatetime' and '$enddatetime')";
            $query_str_nps = " and (created_date between '$startdatetime' and '$enddatetime')";
            $query_str_customer = " (created_date between '$startdatetime' and '$enddatetime')"
            ?>       
         <table width="100%" border="0">
            <tbody>
               <tr class="background3 agent_wise" style="<?=$show_ag?>">
                  <td align="center" colspan="2">
                     <?php
                        ################ CSAT & D-SAT Score cases
                     // Fetching CSAT and DSAT data
                     $Fetch_satis = fetchSatisfactionData($query_str_sat);
                     $CSATVAL_C = $Fetch_satis['GOOD'] ?? 0;
                     $DSATVAL_C = $Fetch_satis['BAD'] ?? 0;
                     $Total_Calls = $Fetch_satis['Total_Calls'] ?? 0;
                     
                     $str_score_cat = "";
                     
                     if ($Total_Calls > 0) {    
                         $TOTAL_CSAT_PER_C = round(($CSATVAL_C * 100 / $Total_Calls), 0);
                         $TOTAL_DSAT_PER_C = round(($DSATVAL_C * 100 / $Total_Calls), 0);
                     
                         $str_score_cat_array = [
                             '{y: ' . $CSATVAL_C . ', legendText: "C SAT", indexLabel: "' . $CSATVAL_C . '", label: "CSAT"}',
                             '{y: ' . $DSATVAL_C . ', legendText: "D SAT", indexLabel: "' . $DSATVAL_C . '", label: "DSAT"}',
                             '{y: ' . $TOTAL_CSAT_PER_C . ', legendText: "C SAT%", indexLabel: "' . $TOTAL_CSAT_PER_C . '", label: "CSAT%"}',
                             '{y: ' . $TOTAL_DSAT_PER_C . ', legendText: "D SAT%", indexLabel: "' . $TOTAL_DSAT_PER_C . '", label: "DSAT%"}'
                         ];
                     
                         // Convert array to comma-separated string
                         $str_score_cat = implode(",", $str_score_cat_array);
                     }
                     ?>
                     <div id="chartContainer" class="charts_div" style="width:100%; height:300px; float:left;"></div>
                  </td>
               </tr>
               <!-----------------------CSAT-DSAT Score-------------------------->
               <tr class="background3 agent_wise" style="<?=$show_ag?>">
                  <td align="center">
                     <?php
                        $str_tollfree="";
                        ################ Toll Free Number 
                        if(!empty($_POST['agent'])){
                           $agent = $_POST['agent'];
                           $agentquery = " and AgentName like '%".$agent."%'";
                        }
                        $Fetch_satis2=fetchSatisfactionData2($startdatetime,$enddatetime,$agentquery);            
                        $CSATVAL_C2=$Fetch_satis2['GOOD'];
                        $DSATVAL_C2=$Fetch_satis2['BAD'];
                        $CSATVAL_C2=($CSATVAL_C2!='')?$CSATVAL_C2:0;
                        $DSATVAL_C2=($DSATVAL_C2!='')?$DSATVAL_C2:0;
                        if($Fetch_satis2['Total_Calls2']>0){
                           $TOTAL_CSAT_PER_P2=round(($CSATVAL_C2*100/$Fetch_satis2['Total_Calls2']),0);
                           $TOTAL_DSAT_PER_P2=round(($DSATVAL_C2*100/$Fetch_satis2['Total_Calls2']),0);
                           $TOTAL_HAPPY=$Fetch_satis2['HAPPY_C'];
                           $TOTAL_HAPPY=($TOTAL_HAPPY!='')?$TOTAL_HAPPY:0;
                           $TOTAL_Satisfied=$Fetch_satis2['Satisfied_C'];
                           $TOTAL_Satisfied=($TOTAL_Satisfied!='')?$TOTAL_Satisfied:0;
                           $TOTAL_Neutral=$Fetch_satis2['Neutral_C'];
                           $TOTAL_Neutral=($TOTAL_Neutral!='')?$TOTAL_Neutral:0;
                           $TOTAL_Unsatisfied=$Fetch_satis2['Unsatisfied_C'];
                           $TOTAL_Unsatisfied=($TOTAL_Unsatisfied!='')?$TOTAL_Unsatisfied:0;
                           $TOTAL_Demotivated=$Fetch_satis2['Demotivated_C'];
                           $TOTAL_Demotivated=($TOTAL_Demotivated)?$TOTAL_Demotivated:0;                        
                           $str_tollfree .='{y: '.$TOTAL_HAPPY.', legendText: "Very Satisfied" ,indexLabel:"'.$TOTAL_HAPPY.'" , label: "Very Satisfied"},';
                           $str_tollfree .='{y: '.$TOTAL_Satisfied.', legendText: "Satisfied" ,indexLabel:"'.$TOTAL_Satisfied.'" ,label: "Satisfied"},';
                           $str_tollfree .='{y: '.$TOTAL_Neutral.', legendText: "Neutral" , indexLabel:"'.$TOTAL_Neutral.'" , label: "Neutral"},';
                           $str_tollfree .='{y: '.$TOTAL_Unsatisfied.', legendText: "Unsatisfied" ,indexLabel:"'.$TOTAL_Unsatisfied.'" ,label: "Unsatisfied"},';
                           $str_tollfree .='{y: '.$TOTAL_Demotivated.', legendText: "Very Unsatisfied" ,indexLabel:"'.$TOTAL_Demotivated.'" ,label: "Very Unsatisfied"},';
                           $str_tollfree = substr($str_tollfree, 0,-1);
                        }                        
                        ?>
                     <div id="chartContainer_tollfree" class="charts_div" style="width:100%; height:300px; float:left;"></div>
                  </td>
               </tr>
               <!---Toll free------->
               <tr class="background3 agent_wise" style="<?=$show_ag?>;display:none">
                  <td align="center" colspan="2">
                     <?php
                        $str_email="";
                        if(!empty($_POST['agent'])){
                           $agent = $_POST['agent'];
                           $agentquery = ' and Agent_id=$agent';
                        }
                        $query_str_satq = " and (Connect_time between '$startdatetime' and '$enddatetime') $agentquery";
                        ################ Email
                        $Fetch_satis3=fetchSatisfactionData3($query_str_satq);
                        $CSATVAL_C3=$Fetch_satis3['GOOD'];
                        $CSATVAL_C3=($CSATVAL_C3)?$CSATVAL_C3:0;
                        $DSATVAL_C3=$Fetch_satis3['BAD'];
                        $DSATVAL_C3=($DSATVAL_C3)?$DSATVAL_C3:0;                        
                        if($Fetch_satis3['Total_Calls3']>0){
                           $TOTAL_CSAT_PER_P3=round(($CSATVAL_C3*100/$Fetch_satis3['Total_Calls3']),0);
                           $TOTAL_CSAT_PER_P3=($TOTAL_CSAT_PER_P3)?$TOTAL_CSAT_PER_P3:0;
                           $TOTAL_DSAT_PER_P3=round(($DSATVAL_C3*100/$Fetch_satis3['Total_Calls3']),0);
                           $TOTAL_DSAT_PER_P3=($TOTAL_DSAT_PER_P3)?$TOTAL_DSAT_PER_P3:0;
                           $TOTAL_HAPPY1=$Fetch_satis3['HAPPY_C'];
                           $TOTAL_HAPPY1=($TOTAL_HAPPY1)?$TOTAL_HAPPY1:0;
                           $TOTAL_Satisfied1=$Fetch_satis3['Satisfied_C'];
                           $TOTAL_Satisfied1=($TOTAL_Satisfied1)?$TOTAL_Satisfied1:0;
                           $TOTAL_Neutral1=$Fetch_satis3['Neutral_C'];
                           $TOTAL_Neutral1=($TOTAL_Neutral1)?$TOTAL_Neutral1:0;
                           $TOTAL_Unsatisfied1=$Fetch_satis3['Unsatisfied_C'];
                           $TOTAL_Unsatisfied1=($TOTAL_Unsatisfied1)?$TOTAL_Unsatisfied1:0;
                           $TOTAL_Demotivated1=$Fetch_satis3['Demotivated_C'];
                           $TOTAL_Demotivated1=($TOTAL_Demotivated1)?$TOTAL_Demotivated1:0;
                           $str_email .='{y: '.$TOTAL_HAPPY1.', legendText: "Very Satisfied" , indexLabel: "'.$TOTAL_HAPPY1.'" , label: "Very Satisfied"},';
                           $str_email .='{y: '.$TOTAL_Satisfied1.', legendText: "Satisfied" , indexLabel: "'.$TOTAL_Satisfied1.'" , label: "Satisfied"},';
                              $str_email .='{y: '.$TOTAL_Neutral1.', legendText: "Neutral" , indexLabel: "'.$TOTAL_Neutral1.'" , label: "Neutral"},';
                           $str_email .='{y: '.$TOTAL_Unsatisfied1.', legendText: "Unsatisfied" , indexLabel: "'.$TOTAL_Unsatisfied1.'" , label: "Unsatisfied"},';
                           $str_email .='{y: '.$TOTAL_Demotivated1.', legendText: "Very Unsatisfied" , indexLabel: "'.$TOTAL_Demotivated1.'" , label: "Very Unsatisfied"},';
                            $str_email = substr($str_email, 0,-1);
                           }         
                                    ?>
                     <div id="chartContainer_email" class="charts_div" style="width:100%; height:300px; float:left;"></div>
                  </td>
               </tr>
               <!-- NPS Report code -->
               <tr class="background3 agent_wise" style="display: none">
                  <td align="center">
                      <?php
                        $str_NPS="";
                        ################ Email
                        $Fetch_satisnps=fetchNpsData($query_str_nps);
                        $TOTAL_Detractors=$Fetch_satisnps['Detractors'];
                        $TOTAL_Detractors=($TOTAL_Detractors)?$TOTAL_Detractors:0;
                        $TOTAL_Passives=$Fetch_satisnps['Passives'];
                        $TOTAL_Passives=($TOTAL_Passives)?$TOTAL_Passives:0;
                        $TOTAL_Promoters=$Fetch_satisnps['Promoters'];
                        $TOTAL_Promoters=($TOTAL_Promoters)?$TOTAL_Promoters:0;
                        $NPS = ( $TOTAL_Promoters - $TOTAL_Detractors) / $Fetch_satisnps['Total_Calls3'];
                        $NPS_Total = round($NPS*100);
                        $str_NPS .='{y: '.$NPS_Total.', legendText: "NPS" , indexLabel: "'.$NPS_Total.'%" , label: "NPS"},';
                         $str_NPS = substr($str_NPS, 0,-1);
                           ?>
                           <div id="chartContainer_NPS" class="charts_div" style="width:100%; height:300px; float:left;"></div>
                  </td>
                  <td align="center">
                     <?php
                     $str_NPSall = "";
                        $subcatgeory_cnt_per=($TOTAL_Detractors/$Fetch_satisnps['Total_Calls3'])*100;
                        $str_NPSall .='{y: '.$TOTAL_Detractors.', indexLabel: "Detractors - '.$TOTAL_Detractors.'('.round($subcatgeory_cnt_per,2).')%" , label: "Detractors",color: "red"},';
                        $cnt_per=($TOTAL_Passives/$Fetch_satisnps['Total_Calls3'])*100;
                        $str_NPSall .='{y: '.$TOTAL_Passives.', legendText: "Passives" , indexLabel: "Passives - '.$TOTAL_Passives.'('.round($cnt_per,2).')%" , label: "Passives",color:"#f5aa36"},';
                        $cnt_per_2=($TOTAL_Promoters/$Fetch_satisnps['Total_Calls3'])*100;
                           $str_NPSall .='{y: '.$TOTAL_Promoters.' , indexLabel: "Promoters - '.$TOTAL_Promoters.'('.round($cnt_per_2,2).')%" , label: "Promoters",color: "Promoters",color:"#23bfaa"},';
                         $str_NPSall = substr($str_NPSall, 0,-1);
                         ?>
                     <div id="chartContainer_npsall" class="charts_div" style="width:100%; height:350px; float:left;"></div>
                  </td>
               </tr>
               <!-- customer effort report -->
               <tr class="background3 agent_wise" style="display: none">
                  <td align="center" colspan="2">
                     <?php
                        if(!empty($_POST['agent'])){
                           $agent = ' agent_id='.$_POST['agent'].' and ';
                        }
                        $str_consumer="";
                        $Fetch_satis5=fetchCustomerEffortData($query_str_customer);
                        if($Fetch_satis5['Total_Calls']>0){
                           
                           $Very_good=$Fetch_satis5['Very_good'];
                           $Very_good=($Very_good)?$Very_good:0;
                           $Low_effort=$Fetch_satis5['Low_effort'];
                           $Low_effort=($Low_effort)?$Low_effort:0;
                           $Neutral=$Fetch_satis5['Neutral'];
                           $Neutral=($Neutral)?$Neutral:0;
                           $high_effort=$Fetch_satis5['high_effort'];
                           $high_effort=($high_effort)?$high_effort:0;
                           $very_high_effort=$Fetch_satis5['very_high_effort'];
                           $very_high_effort=($very_high_effort)?$very_high_effort:0;
                           $str_consumer .='{y: '.$Very_good.', legendText: "Very Low effort" , indexLabel: "'.$Very_good.'" , label: "Very Low effort"},';
                           $str_consumer .='{y: '.$Low_effort.', legendText: "Low Effort" , indexLabel: "'.$Low_effort.'" , label: "Low Effort"},';
                              $str_consumer .='{y: '.$Neutral.', legendText: "Neutral" , indexLabel: "'.$Neutral.'" , label: "Neutral"},';
                           $str_consumer .='{y: '.$high_effort.', legendText: "High effort" , indexLabel: "'.$high_effort.'" , label: "High effort"},';
                           $str_consumer .='{y: '.$very_high_effort.', legendText: "Very High Effort" , indexLabel: "'.$very_high_effort.'" , label: "Very High"},';
                            $str_consumer = substr($str_consumer, 0,-1);
                           }
                        ?>
                     <div id="chartContainer_Customer" class="charts_div" style="width:100%; height:300px; float:left;"></div>
                  </td>
               </tr>
            </tbody>
         </table>
         <table class="tableview tableview-2">
            <tbody>
            </tbody>
         </table>
      </div>
      <!-- ADD START-->      
         <table width="100%">
            <tbody>

            </tbody>
         </table>
      <!-- ADD END-->
   </div>
</form>
<script type="text/javascript">
   window.onload = function () {
   
   CanvasJS.addColorSet("Shades1",
   [//colorSet Array
   
   "#0000FF",
   "#FF4500",
   "#808080",
   "#ffdb58"               
   ]);
   
   
   <?php 
   if($str_score_cat != ''){
      ?> 
     var chart1 = new CanvasJS.Chart("chartContainer",
     {
       colorSet: "Shades1",
       title:{
         text: "C-SAT & D-SAT Agent Score"    
       },
       animationEnabled: true,
       exportEnabled: true,
       axisY: {
         title: "Score"
       },
       legend: {
         verticalAlign: "bottom",
         horizontalAlign: "center"
       },
       theme: "theme1",
       data: [
   
       {        
         click: function(e){
   
   window.location.href="web_customer_satis_report.php?for=agent&type=all&val="+e.dataPoint.label+"&sttartdatetime=<?=$startdatetime?>&enddatetime=<?=$enddatetime?>";
   },
         type: "column",  
         showInLegend: true, 
         legendMarkerColor: "grey",
         legendText: "C-SAT & D-SAT Score",
         dataPoints: [      
         <?=$str_score_cat?>       
         ]
       }   
       ]
     });
   
     chart1.render();
   
   <?php }?>
   /*------------------------------------------------------*/
     CanvasJS.addColorSet("Shades2",
   [//colorSet Array
   
   "#0000FF",
   "#FF4500",
   "#808080",
   "#ffdb58",
   "#1E90FF",
   "#9ACD32",
   "#000080",
   "#B22222",
   "#0000CD"
   
   ]);
   <?php 
   if($str_tollfree != ''){?>
   var chart2 = new CanvasJS.Chart("chartContainer_tollfree",
     {
       colorSet: "Shades2",
       title:{
         text: "C-SAT & D-SAT Agent Score received by the customers for Toll Free No."    
       },
       animationEnabled: true,
       exportEnabled: true,
       axisY: {
         title: "Score"
       },
       legend: {
         verticalAlign: "bottom",
         horizontalAlign: "center"
       },
       theme: "theme1",
       data: [
   
       {       
         click: function(e){
   //alert(  "dataSeries Event => Type: "+ e.dataSeries.type+ ", dataPoint { x:" + e.dataPoint.name + ", y: "+ e.dataPoint.y + " }" );
   //window.location.href="web_detail_dashboard.php?type="+e.dataPoint.name;
   window.location.href="web_customer_satis_report.php?for=agent&type=1&val="+e.dataPoint.label+"&sttartdatetime=<?=$startdatetime?>&enddatetime=<?=$enddatetime?>";
   }, 
         type: "column",  
         showInLegend: true, 
         legendMarkerColor: "grey",
         legendText: "C-SAT %",
         dataPoints: [      
         <?=$str_tollfree?>       
         ]
       }   
       ]
     });
   
     chart2.render();
   <?php }?>
     /*------------------------------------------------------*/
   /*------------------------------------------------------*/
   <?php 
   if($str_email != ''){?>
     var chart3 = new CanvasJS.Chart("chartContainer_email",
     {
       colorSet: "Shades2",
       title:{
         text: "C-SAT & D-SAT Agent Score By Email"    
       },
       animationEnabled: true,
       exportEnabled: true,
       axisY: {
         title: "Score"
       },
       legend: {
         verticalAlign: "bottom",
         horizontalAlign: "center"
       },
       theme: "theme1",
       data: [
   
       {        
         click: function(e){
   window.location.href="web_customer_satis_report.php?for=agent&type=2&val="+e.dataPoint.label+"&sttartdatetime=<?=$startdatetime?>&enddatetime=<?=$enddatetime?>";
   },
         type: "column",  
         showInLegend: true, 
         legendMarkerColor: "grey",
         legendText: "NPS Count & C-SAT %",
         dataPoints: [      
         <?=$str_email?>       
         ]
       }   
       ]
     });
   
      chart3.render();
      <?php }?>
   /*------------------------------------------------------*/

   /*------------------------------------------------------*/
   <?php 
   if($str_NPS != ''){
   ?>
     var chart4 = new CanvasJS.Chart("chartContainer_NPS",
     {
       colorSet: "Shades2",
       title:{
         text: "Net Promoter Score"    
       },
       animationEnabled: true,
       exportEnabled: true,
       axisY: {
         title: "Score"
       },
       legend: {
         verticalAlign: "bottom",
         horizontalAlign: "center"
       },
       theme: "theme1",
       data: [
   
       {        
         type: "column",  
         showInLegend: true, 
         legendMarkerColor: "grey",
         legendText: "NPS Count",
         dataPoints: [      
         <?=$str_NPS?>       
         ]
       }   
       ]
     });
   
      chart4.render();
   <?php }?>
   /*------------------------------------------------------*/
   /*-----------------------NPS PIE CHART-------------------------------*/
    <?php 
   if($str_NPSall != ''){
       ?>
         var chartNps = new CanvasJS.Chart("chartContainer_npsall", {
            title: {
               text: "NPS",
               fontFamily: "arial black",
               fontColor: "#695A42",
               fontSize:15,
            },
            animationEnabled: true,
            exportEnabled: true,
            axisY: {
                  title: "Case Count"
            },
            legend: {
                  verticalAlign: "bottom",
                  horizontalAlign: "center"
            },
            //theme: "theme1",
            data: [
      
                  {
                     type: "pie",
                     //showInLegend: true,
                     bevelEnabled: true, 
                     legendMarkerColor: "grey",
                     //legendText: "",
                     dataPoints: [ <?=$str_NPSall?>]
                  }
            ]
         });
      
         chartNps.render();
    <?php }?>
    /*------------------------------------------------------*/
   <?php 
   if($str_consumer != ''){?>
     var chart3 = new CanvasJS.Chart("chartContainer_Customer",
     {
       colorSet: "Shades2",
       title:{
         text: "Customer Effort"    
       },
       animationEnabled: true,
       exportEnabled: true,
       axisY: {
         title: "Score"
       },
       legend: {
         verticalAlign: "bottom",
         horizontalAlign: "center"
       },
       theme: "theme1",
       data: [
   
       {        
         type: "column",  
         showInLegend: true, 
         legendMarkerColor: "grey",
         legendText: "Customer Effort",
         dataPoints: [      
         <?=$str_consumer?>       
         ]
       }   
       ]
     });
   
      chart3.render();
      <?php }?>
   /*------------------------------------------------------*/
   }
</script>

<script>
function goBack()
{
 window.location.href='web_admin_dashboard.php';
}


function showDiv(val) {
   if(val==1)
   {
      // alert('Hi');
      $(".agent_wise").css("display","table-row");
      $(".product_wise").css("display","table-row");
   }else if(val==2)
   {
      // alert('Hello');
      // $(".agent_wise").css("display","table-row");
      // $(".product_wise").css("display","none");
   }else if(val==3)
   {
      // alert('Handsome');
      
      $(".agent_wise").css("display","none");
      $(".product_wise").css("display","table-row");
   }
}

</script> 
<!-- <script type="text/javascript" src="<?=$SiteURL?>public/js/dashboard_CsatDsat.js" ></script> -->

