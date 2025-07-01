jQuery(function ($){
    var Startdate = jQuery("input[name='startdatetime']").val();
    var Enddate = jQuery("input[name='enddatetime']").val();
    var Dashboard = {
        init: function (){
            jQuery("body").on('click','#filter_record',this.updateDashboard.bind(this)); // Bind to maintain context
            this.startPeriodicUpdate(); // Start periodic update
        },
        startPeriodicUpdate: function() {
            // Get initial values of Startdate and Enddate
            this.Startdate = Startdate;
            this.Enddate = Enddate;
            this.updateDashboard(this.Startdate, this.Enddate); // Initial call to updateDashboard

            // Use an anonymous function to pass the parameters
            setInterval(() => {
                this.updateDashboard(this.Startdate, this.Enddate);
            }, 15000);
        },
        updateDashboard: function(Startdate,Enddate) {
            $.ajax({
                url: '../script/api_dashboard.php', // Replace with the path to your PHP file
                method: 'POST', // or 'POST' if your PHP file expects POST requests
                data: {'Startdate': Startdate,'Enddate':Enddate,'action':'dashboard'},
                dataType: 'json',
                success: function(data) {
                    var obj = data.info;
                  //   console.log(obj);

                    if (data.status === 'success') {        
                     /* Data For Dashboard Fields */ 
                        getData();

                     /* Graphs For Category, Subcategory and Language */ 
               
                        // Category
                        try {
                           // Parse the JSON string
                           var category_data = JSON.parse(data.cat);
                        
                           var chart = new CanvasJS.Chart("category_e", {
                              animationEnabled: true,
                              title: {
                                 text: "Category",
                                 fontFamily: "arial black",
                                 fontColor: "#4F81BC",
                                 fontSize: 12
                              },
                              legend: {
                                 verticalAlign: "bottom",
                                 horizontalAlign: "center"
                              },
                              data: [{
                                 type: "pie",
                                 indexLabelFontColor: "#4F81BC",
                                 indexLabel: "{label} - {y} (#percent%)", // 👈 This line is key
                                 cursor: 'pointer',
                                 bevelEnabled: true,
                                 dataPoints: category_data
                              }]
                           });
                        
                           chart.render();
                        
                        } catch (err) {
                           console.log(err + 'Category');
                        }
                        
               
                        // Sub category
                        try{
                        
                           var subcategory_data = JSON.parse(data.subcat);
                           var chart = new CanvasJS.Chart("subcategory_e", {
                              title: {
                                 text: "Sub Category",
                                 fontFamily: "arial black",
                                 fontColor: "#4F81BC",
                                 fontSize: 12,
                              },
                              animationEnabled: true,
                              axisY: {
                                 title: "Case Count"
                              },
                              legend: {
                                 verticalAlign: "bottom",
                                 horizontalAlign: "center"
                              },
                              data: [{
                                 type: "pie",
                                 indexLabelFontColor: "#4F81BC",
                                 cursor: 'pointer',
                                 bevelEnabled: true,
                                 theme: 'theme1',
                                 dataPoints: subcategory_data
                              }]
                           });
                     
                           chart.render();
                     
                        }catch(err){
                           console.log(err+'Subcategory');
                        }
               
                        // Language
                        try {
                           // Parse the JSON string
                           //console.log(data.lang)
                           var language_data = JSON.parse(data.lang);
               
                           // Create the chart
                           var chart = new CanvasJS.Chart("language_e", {
                              animationEnabled: true,
                              title: {
                                    text: "Language",
                                    fontFamily: "arial black",
                                    fontColor: "#4F81BC",
                                    fontSize: 12
                              },
                              axisX: {
                                    labelAngle: 170
                              },
                              axisY: {
                                    title: "No of cases",
                                    includeZero: true,
                                    titleFontColor: "#4F81BC",
                                    lineColor: "#4F81BC",
                                    labelFontColor: "#4F81BC",
                                    tickColor: "#4F81BC"
                              },
                              data: [{
                                    type: 'column',
                                    indexLabelFontColor: "#4F81BC",
                                    showInLegend: false,
                                    cursor: 'pointer',
                                    bevelEnabled: true,
                                    legend: {
                                       verticalAlign: 'bottom',
                                       horizontalAlign: 'center'
                                    },
                                    theme: 'theme1',
                                    dataPoints: language_data
                              }]
                           });
               
                           // Render the chart
                           chart.render();
               
                        } catch (err) {
                           console.log(err + 'Language');
                        }

                        // Social Media
                        try {
                           // Create the chart
                           var chart = new CanvasJS.Chart("socialmedia_e", {
                           animationEnabled: true,
                           title:{
                              text: "Social Media",
                              fontFamily: "arial black",
                              fontColor: "#4F81BC",
                              fontSize: 12
                           },	
                           axisY: {
                              title: "No of Counts",
                              titleFontColor: "#4F81BC",
                              lineColor: "#4F81BC",
                              labelFontColor: "#4F81BC",
                              tickColor: "#4F81BC"
                           },	
                           toolTip: {
                              shared: true
                           },
                           legend: {
                              cursor:"pointer",
                              itemclick: toggleDataSeries
                           },
                           data: [{
                              type: "column",
                              name: "Recieved",
                              bevelEnabled: true,
                              dataPoints:[
                                 { label: "Twitter", y: obj.twitter_r },
                                 { label: "Email", y: obj.email_r },
                                 { label: "SMS", y: obj.sms_r },
                                 { label: "Chat", y: obj.chat_r },
                                 { label: "Facebook", y: obj.facebook_r },
                                 { label: "Whatsapp", y: obj.whatsapp_r }
                              ]
                           },
                           {
                              type: "column",	
                              name: "Case Created",
                              axisYType: "secondary",
                              bevelEnabled: true,
                              dataPoints:[
                                 { label: "Twitter", y: obj.twitter_c },
                                 { label: "Email", y: obj.email_c },
                                 { label: "SMS", y: obj.sms_c },
                                 { label: "Chat", y: obj.chat_c },
                                 { label: "Facebook", y: obj.facebook_c },
                                 { label: "Whatsapp", y: obj.whatsapp_c }
                              ]
                           }]
                        });
                        chart.render();

                        function toggleDataSeries(e) {
                           if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                              e.dataSeries.visible = false;
                           }
                           else {
                              e.dataSeries.visible = true;
                           }
                           chart.render();
                        }
                                       
                        } catch (err) {
                           console.log(err + 'Social Media');
                        }
               
                    }

                   function getData() {

                     /* CRM */
                      $("#total_cases_e").text(obj.total_cases);
                      $("#complaints_e").text(obj.complaints);
                      $("#inquiries_e").text(obj.inquiries);
                      $("#others_e").text(obj.others);
                      $("#pending_e").text(obj.pending);
                      $("#inprogress_e").text(obj.inprogress);
                      $("#closed_e").text(obj.closed);
                      $("#assigned_e").text(obj.assigned);
                      $("#escalated_e").text(obj.escalated);
                      $("#resolution_rate_e").text(obj.resolution_rate);

                      /* OMNICHANNELS */
                      $("#twitter_r").text(obj.twitter_r);
                      $("#email_r").text(obj.email_r);
                      $("#sms_r").text(obj.sms_r);
                      $("#chat_r").text(obj.chat_r);
                      $("#facebook_r").text(obj.facebook_r);
                      $("#whatsapp_r").text(obj.whatsapp_r);

                      /* TELEPHONY */
                      $("#longCallAnsName_e").text(obj.longCallAnsName);
                      $("#longCallAns_e").text(obj.longCallAns);
                      $("#highestTalkTimeName_e").text(obj.highestTalkTimeName);
                      $("#highestTalkTime_e").text(obj.highestTalkTime);
                      $("#highestLoginTimeName_e").text(obj.highestLoginTimeName);
                      $("#highestLoginTime_e").text(obj.highestLoginTime);
                      $("#percentage_answeredcall_e").text(obj.percentage_answeredcall);
                      $("#overall_t").text(obj.total_calls);
                      $("#inbound_t").text(obj.total_inbound);
                      $("#outbound_t").text(obj.total_outbound);
                      $("#talktime_t").text(obj.talktime);
                      $("#avgtalktime_t").text(obj.average_talktime);
                      $("#wrapup_t").text(obj.wrapuptime);
                      $("#voicemail_t").text(obj.voicemail);
                      $("#abandon_t").text(obj.abandoned);
                      $("#miscall_t").text(obj.missedcall);

                     //  console.log(data.live_agents)

                      /* LIVE AGENTS*/
                      $("#live_agents_count").text(obj.live_agents_count);
                      $(".marquee__group").html(data.live_agents);
 
                   }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching dashboard data: " + error);
                }
            });
        }
    };

    $(document).ready(function() {
        Dashboard.init();
    });
});
