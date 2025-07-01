function get_dashboard_box() {
      var url = 'Dashboard/get_newdashdata.php';
      var startdate = $("#startdatetime").val();
      var enddate = $("#enddatetime").val();
      console.log(enddate);
      console.log(startdate);
      $.ajax({
         type: "POST",
         dataType: 'JSON',
         url: url,
         cache: false,
         data: {
            'Startdate': startdate,
            'Enddate': enddate
         },
         success: function(data, textStatus, jqXHR) {
           
            // console.log(jqXHR);
            if (data.status === 'success' && textStatus == 'success') {
               var obj = data.info;
               var priority = data.priority;
               var category = data.category;
               var subcategory = data.subcategory;
               // for removing old data from charts
               $('#category').html('<canvas id="pieChart1" style="min-height:197px"></canvas>');
               $('#subcategory').html('<canvas id="pieChart2" style="min-height:197px"></canvas>');
               $('#priority').html('<canvas id="pieChart3" style="min-height:197px"></canvas>');
               $('#ageing').html('<canvas id="barchart3" style="min-height:197px"></canvas>');
               $('#socialmedia').html('<canvas id="barchart1" style="min-height:197px"></canvas>');
               // CRM
               $("#total_case_today").text(obj.total_case_today);
               $("#total_compalint").text(obj.total_compalint);
               $("#total_inquiries").text(obj.total_inquiries);
               $("#total_others").text(obj.total_others);
               $("#pending").text(obj.pending);
               $("#closed").text(obj.closed);
               $("#resolved").text(obj.resolved);
               $("#reopened").text(obj.reopened);
               $("#assigned").text(obj.assigned);
               $("#escalated").text(obj.escalated);
               //SOCIAL MEDIA AND CHANNELS
               $("#total_calls").text(obj.total_calls);
               $("#inbound_answered").text(obj.inbound_answered);
               $("#twitter_created").text(obj.twitter_created);
               $("#twitter_recieved").text(obj.twitter_recieved);
               $("#whatsapp_bot").text(obj.whatsapp_bot);
               $("#messenger_bot").text(obj.messenger_bot); //for messenger cound display[Aarti][21-08-2024]
               $("#chat_bot").text(obj.chat_bot);
               $("#facebook_recieved").text(obj.facebook_recieved);
               $("#sms_cnt").text(obj.sms_cnt);
               $("#email").text(obj.email);
               $("#live_agents").text(obj.live_agents);
               $("#live_backofficers").text(obj.live_backofficers);
               $("#twitter_read_cnt").text(obj.twitter_read_cnt);
               $("#twitter_unread_cnt").text(obj.twitter_unread_cnt);
               $("#whatsapp_read_cnt").text(obj.whatsapp_read_cnt);
               $("#whatsapp_unread_cnt").text(obj.whatsapp_unread_cnt);
               $("#facebook_read_cnt").text(obj.facebook_read_cnt);
               $("#facebook_unread_cnt").text(obj.facebook_unread_cnt);
               $("#email_read_cnt").text(obj.email_read_cnt);
               $("#email_unread_cnt").text(obj.email_unread_cnt);
               $("#sms_read_cnt").text(obj.sms_read_cnt);
               $("#sms_unread_cnt").text(obj.sms_unread_cnt);
               $("#chat_bot_read_cnt").text(obj.chat_bot_read_cnt);
               $("#chat_bot_unread_cnt").text(obj.chat_bot_unread_cnt);
               $("#messenger_bot_read").text(obj.messenger_bot_read);
               $("#messenger_bot_unread").text(obj.messenger_bot_unread);
               $("#instagram_recieved").text(obj.instagram_recieved); 
               $("#instagram_posts_recieved").text(obj.instagram_posts_recieved); 
               $("#instagram_bot").text(obj.instagram_bot_cnt); 
               $("#instagram_bot_read").text(obj.instagram_bot_read);
               $("#instagram_posts_read").text(obj.instagram_posts_read);
               $("#instagram_bot_unread").text(obj.instagram_bot_unread);
               $("#instagram_posts_unread").text(obj.instagram_posts_unread);//[vastvikta][17-12-2024]
               // TELEPHONY
               $("#inbound").text(obj.inbound);
               $("#outbound").text(obj.outbound);
               $("#missedcall").text(obj.missedcall);
               $("#voicemail").text(obj.voicemail);
               $("#abandon").text(obj.abandon);
               $("#total_talktime").text(obj.total_talktime);
               $("#average_talktime").text(obj.average_talktime);
               $("#average_duration").text(obj.average_duration);
               $("#longest_duration").text(obj.longest_duration);
               $("#average_wrapup").text(obj.average_wrapup);
               $("#wrapup").text(obj.wrapup);
               //overall dispositon list
               $("#disposition_data").html(data.dispostion);
               const num = obj.productive;
               const str_num = num.toString();
               const productive = Number(str_num.slice(0, 5));
               $("#productive").html('<canvas  class="ml-1 mt-1" data-type="radial-gauge" data-width="260" data-height="200" data-units="Percentage(' + productive + '%)" data-min-value="0" data-start-angle="90" data-ticks-angle="180" data-value-box="false" data-max-value="100" data-value="' + productive + '" data-major-ticks="0,10,20,30,40,50,60,70,80,90,100" data-minor-ticks="2" data-stroke-ticks="true" data-highlights="[{&quot;from&quot;: 0, &quot;to&quot;: 50, &quot;color&quot;: &quot;#E26866&quot;},{&quot;from&quot;: 50, &quot;to&quot;: 100, &quot;color&quot;: &quot;#AAC96B&quot;}]" data-color-plate="#fff" data-border-shadow-width="0" data-borders="false" data-needle-type="arrow" data-needle-width="2" data-needle-circle-size="7" data-needle-circle-outer="true" data-needle-circle-inner="false" data-animation-duration="1500" data-animation-rule="linear" data-color-value-box-shadow="false" data-value-box-stroke="0" data-color-value-box-background="false" data-value-int="2" data-value-dec="1" width="260" height="200" style="width: 260px; height: 200px;"></canvas>');
               // console.log(obj.category_name+' '+obj.category_count)
               //---------------------------
               //- BAR CHART - Social Media
               //---------------------------
               var barchartdata = {
                  labels: ['Twitter', 'Whatsapp', 'Facebook', 'Email', 'SMS', 'Live Chat','Messenger', 'Instagram','Instagram Post'],
                  datasets: [{
                        label: 'Recieved',
                        backgroundColor: 'green',
                        borderColor: 'rgba(60,141,188,0.8)',
                        pointRadius: false,
                        pointColor: '#3b8bba',
                        pointStrokeColor: 'rgba(60,141,188,1)',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data: [obj.twitter_recieved, obj.whatsapp_bot, obj.facebook_recieved, obj.email,obj.sms_cnt, obj.chat_bot,obj.messenger_bot, obj.instagram_recieved,obj.instagram_posts_recieved]
                     },
                     {
                        label: 'Created',
                        backgroundColor: 'crimson',
                        borderColor: 'rgba(210, 214, 222, 1)',
                        pointRadius: false,
                        pointColor: 'rgba(210, 214, 222, 1)',
                        pointStrokeColor: '#c1c7d1',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data: [obj.twitter_created, 0,obj.facebook_created, obj.email_created,obj.sms_created_cnt, obj.chat_created,obj.messenger_bot_cnt, obj.instagram_bot_cnt, 0]
                     },
                     {
                        label: 'Unread',
                        backgroundColor: '#FBD83E',
                        borderColor: 'rgba(210, 214, 222, 1)',
                        pointRadius: false,
                        pointColor: 'rgba(210, 214, 222, 1)',
                        pointStrokeColor: '#c1c7d1',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data: [obj.twitter_unread_cnt, obj.whatsapp_unread_cnt, obj.facebook_unread_cnt,obj.email_unread_cnt,obj.sms_read_cnt, obj.chat_bot_unread_cnt,obj.messenger_bot_unread, obj.instagram_bot_unread,obj.instagram_posts_unread]
                     },
                     {
                        label: 'Read',
                        backgroundColor: '#F79C29',
                        borderColor: 'rgba(210, 214, 222, 1)',
                        pointRadius: false,
                        pointColor: 'rgba(210, 214, 222, 1)',
                        pointStrokeColor: '#c1c7d1',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data: [obj.twitter_read_cnt, obj.whatsapp_read_cnt,obj.facebook_read_cnt, obj.email_read_cnt,obj.sms_unread_cnt, obj.chat_bot_read_cnt,obj.messenger_bot_read, obj.instagram_bot_read, obj.instagram_posts_read]
                     }
                     //,
                     // {
                     //    label: 'Deleted',
                     //    backgroundColor: '#7ABFF4',
                     //    borderColor: 'rgba(210, 214, 222, 1)',
                     //    pointRadius: false,
                     //    pointColor: 'rgba(210, 214, 222, 1)',
                     //    pointStrokeColor: '#c1c7d1',
                     //    pointHighlightFill: '#fff',
                     //    pointHighlightStroke: 'rgba(220,220,220,1)',
                     //    data: [obj.twitter_created, 0,obj.facebook_created, obj.email_created,obj.sms_created_cnt, obj.chat_created,obj.messenger_bot_cnt]
                     // }
                  ]
               }
               var barChartCanvas = $('#barchart1').get(0).getContext('2d')
               var barChartData = $.extend(true, {}, barchartdata)
               var temp0 = barchartdata.datasets[0]
               var temp1 = barchartdata.datasets[1]
               barChartData.datasets[0] = temp1
               barChartData.datasets[1] = temp0
               var barChartOptions = {
                  responsive: true,
                  maintainAspectRatio: false,
                  datasetFill: false
               }
               new Chart(barChartCanvas, {
                  type: 'bar',
                  data: barChartData,
                  options: barChartOptions
               })
               //-------------------------
               //- BAR CHART - Ageing
               //-------------------------
               var barchartdata = {
                  labels: ['Ageing'],
                  datasets: [{
                        label: '15 Days',
                        backgroundColor: 'crimson',
                        borderColor: 'rgba(60,141,188,0.8)',
                        pointRadius: false,
                        pointColor: '#3b8bba',
                        pointStrokeColor: 'rgba(60,141,188,1)',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data: [obj.ageing_15, 0, 0]
                     },
                     {
                        label: '7 Days',
                        backgroundColor: '#007bff',
                        borderColor: 'rgba(60,141,188,0.8)',
                        pointRadius: false,
                        pointColor: '#3b8bba',
                        pointStrokeColor: 'rgba(60,141,188,1)',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data: [obj.ageing_7, 0, 0]
                     },
                     {
                        label: '20 Days',
                        backgroundColor: '#63ebb9',
                        borderColor: 'rgba(210, 214, 222, 1)',
                        pointRadius: false,
                        pointColor: 'rgba(210, 214, 222, 1)',
                        pointStrokeColor: '#c1c7d1',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data: [obj.ageing_20, 0, 0]
                     },
                     {
                        label: '30 Days',
                        backgroundColor: '#17a2b8',
                        borderColor: 'rgba(210, 214, 222, 1)',
                        pointRadius: false,
                        pointColor: 'rgba(210, 214, 222, 1)',
                        pointStrokeColor: '#c1c7d1',
                        pointHighlightFill: '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data: [obj.ageing_30, 0, 0]
                     }
                  ]
               }
               var barChartCanvas = $('#barchart3').get(0).getContext('2d')
               var barChartData = $.extend(true, {}, barchartdata)
               var temp0 = barchartdata.datasets[0]
               var temp1 = barchartdata.datasets[1]
               barChartData.datasets[0] = temp1
               barChartData.datasets[1] = temp0
               var barChartOptions = {
                  responsive: true,
                  maintainAspectRatio: false,
                  datasetFill: false
               }
               new Chart(barChartCanvas, {
                  type: 'bar',
                  data: barChartData,
                  options: barChartOptions
               })
               //-----------------------
               //- PIE CHART - Category
               //-----------------------
               var piedata1 = {
                  //Name of Category
                  labels: obj.category_name,
                  datasets: [{
                     // Data of Category
                     data: obj.category_count,
                     backgroundColor: ['gainsboro', '#28a745', 'crimson', '#99c0fa', '#63ebb9', 'green', '#007bff', '#17a2b8'],
                  }]
               }
               var pieChartCanvas = $('#pieChart1').get(0).getContext('2d')
               var pieData = piedata1;
               var pieOptions = {
                  maintainAspectRatio: false,
                  responsive: true,
               }
               new Chart(pieChartCanvas, {
                  type: 'pie',
                  data: pieData,
                  options: pieOptions

               })
               //---------------------------
               //- PIE CHART - Sub Category
               //--------------------------
               var piedata2 = {
                  labels: obj.subcategory_name,
                  datasets: [{
                     data: obj.subcategory_count,
                     backgroundColor: ['gainsboro', '#28a745', 'crimson', '#99c0fa', '#63ebb9', 'green', '#007bff', '#17a2b8'],
                  }]
               }

               var pieChartCanvas = $('#pieChart2').get(0).getContext('2d')
               var pieData = piedata2;
               var pieOptions = {
                  maintainAspectRatio: false,
                  responsive: true,
               }
               new Chart(pieChartCanvas, {
                  type: 'pie',
                  data: pieData,
                  options: pieOptions
               })

               // Case Priority
               var piedata3 = {
                  labels: obj.priority_name,
                  datasets: [{
                     data: obj.priority_count,
                     backgroundColor: ['green', 'crimson', '#17a2b8'],
                  }]
               }

               var pieChartCanvas = $('#pieChart3').get(0).getContext('2d')
               var pieData = piedata3;
               var pieOptions = {
                  maintainAspectRatio: false,
                  responsive: true,
               }
               new Chart(pieChartCanvas, {
                  type: 'pie',
                  data: pieData,
                  options: pieOptions
               })
            } else {
               alert('No');
            }
         },
         error: function(jqXHR, textStatus, errorThrown) {
            //console.log('StatusERR ');
            //console.log('textStatusERR '+textStatus);
            //console.log(jqXHR);

            //if fails   

         }
      });
   }

   $(function() {
      // Hide columns based on checkboxes
      $("input:checkbox:not(:checked)").each(function() {
         var column = "table ." + $(this).attr("name");
         $(column).hide();
      });
   
      $("input:checkbox").click(function() {
         var column = "table ." + $(this).attr("name");
         $(column).toggle();
      });
   
      // Reset functionality
      $("#reset").click(function() {
         location.reload(true);
      });
   
      // Fetch dashboard data every 10 seconds
     
   });
   //Fetch dashboard data every 10 seconds [vastvikta][17-12-2024]
   function callDashboardBox() {
      get_dashboard_box();  // Call the function once after page load
      setTimeout(callDashboardBox, 10000);  // Set timeout to call the function every 10 seconds
   }
   
   window.onload = callDashboardBox; 
   $(document).ready(function() {
      var rand_no = Math.floor(Math.random() * 3) + 1; // Generate random number between 1 and 3
   
      $("#btnConvert").on('click', function() {
         html2canvas(document.getElementById("html-content")).then(function(canvas) {
            var anchorTag = document.createElement("a");
            document.body.appendChild(anchorTag);
   
            anchorTag.download = "dashboard_" + rand_no + ".jpg";
            anchorTag.href = canvas.toDataURL();
            anchorTag.target = '_blank';
            anchorTag.click();
         });
      });
   });