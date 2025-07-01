   window.onload = function () {
   
   CanvasJS.addColorSet("Shades1",
   [//colorSet Array
   
   "#0000FF",
   "#FF4500",
   "#808080",
   "#ffdb58"               
   ]);
   
   
   if($str_score_cat != ''){
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
         dataPoints:  <?php echo $json_score_cat;?>
       }   
       ]
     });
   
     chart1.render();
   
    }
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
   if($str_tollfree != ''){
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
         dataPoints: <?php echo $json_tollfree; ?> 
       }   
       ]
     });
   
     chart2.render();
   }
     /*------------------------------------------------------*/
   /*------------------------------------------------------*/
    
   if($str_email != ''){
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
         dataPoints: <?php echo $json_email; ?>       
         ]
       }   
       ]
     });
   
      chart3.render();
       }
   /*------------------------------------------------------*/

   /*------------------------------------------------------*/
   if($str_NPS != ''){
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
         dataPoints: <?php echo $json_NPS; ?>      
         ]
       }   
       ]
     });
   
      chart4.render();
   }
   /*------------------------------------------------------*/
   /*-----------------------NPS PIE CHART-------------------------------*/
   if($str_NPSall != ''){
       
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
                     dataPoints: <?php echo $json_NPSall; ?>
                  }
            ]
         });
      
         chartNps.render();
     }
    /*------------------------------------------------------*/ 
   if($str_consumer != ''){
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
         dataPoints: <?php echo $json_consumer;?>
       }   
       ]
     });
   
      chart3.render();
      }
   /*------------------------------------------------------*/
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
    $(".agent_wise").css("display","none");
    $(".product_wise").css("display","table-row");
  }
}
