<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- Header start -->
<?php  $this->load->view('layout/header'); ?>
<!-- Header End  -->
<style>
  .info-box{
    min-height: 0;
    padding: 7px;
    height:50px;
  }

  .info-box-text{
    font-size:smaller;
  }
  .info-box .info-box-icon {
    width: 36px;
  }
  .info-box .info-box-number {
    font-weight: 700;
    font-size: 13px;
  }
  .bg-green2{
    background-color:#12e094a8;
    color:#fff;
  }
  .bg-purple2{
    background-color:#99c0fa;
    color:#fff;
  }
  .fa, .fas ,.fab ,.far {
    font-size:15px;
  }
</style>
  
    <!-- Content Header (Page header) -->
    <?php $this->load->view('breadcrumb') ?>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

                <!-- Row 1 -->
                  <div class="row">
                
                    <div class="col-sm-12 card" style="padding-top: 15px;background: floralwhite;border: 0.5px solid #2222;">
                      <span>Today's record</span>
                      <div class="row">

                        <div class="col-md-2">
                          <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="far fa-comment fa-xs"></i></span>

                            <div class="info-box-content">
                            <div class="row">
                              <div class="col-sm-12">
                                  <span class="info-box-text">SMS Sent</span>
                                </div>
                                
                                <div class="col-sm-12">
                                  <span class="info-box-number" id="sms_sent_today">0</span>
                                </div>
                              </div>
                            </div>
                            <!-- /.info-box-content -->
                          </div>
                          <!-- /.info-box -->
                        </div>

                        <div class="col-md-2">
                          <div class="info-box">
                            <span class="info-box-icon bg-secondary"><i class="far fa-comments fa-xs"></i></span>

                            <div class="info-box-content">
                            <div class="row">
                              <div class="col-sm-12"><span class="info-box-text">SMS Recieved</span></div>
                              <div class="col-sm-12"><span class="info-box-number" id="sms_received_today">0</span></div>
                            </div>
                          
                            </div>
                            <!-- /.info-box-content -->
                          </div>
                          <!-- /.info-box -->
                        </div>

                        <div class="col-md-2">
                          <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fab fa-whatsapp fa-xs"></i></span>

                            <div class="info-box-content">
                            <div class="row">
                              <div class="col-sm-12"><span class="info-box-text">Whatsapp Sent</span></div>
                              <div class="col-sm-12">  <span class="info-box-number" id="whatsapp_sent_today"></span></div>
                            </div>
                            </div>
                            <!-- /.info-box-content -->
                          </div>
                          <!-- /.info-box -->
                        </div>

                        <div class="col-md-2">
                          <div class="info-box">
                            <span class="info-box-icon bg-green2"><i class="fab fa-whatsapp fa-xs"></i></span>

                            <div class="info-box-content">
                            <div class="row">
                              <div class="col-sm-12"><span class="info-box-text">Whatsapp Recieved</span></div>
                              <div class="col-sm-12"><span class="info-box-number" id="whatsapp_received_today">0</span></div>
                            </div>
                              
                            </div>
                            <!-- /.info-box-content -->
                          </div>
                          <!-- /.info-box -->
                        </div>
                
                        <div class="col-md-2">
                          <div class="info-box">
                            <span class="info-box-icon bg-purple2"><i class="far fa-comments fa-xs"></i></span>

                            <div class="info-box-content">
                            <div class="row">
                              <div class="col-sm-12"><span class="info-box-text">Quota Utilized</span></div>
                              <div class="col-sm-12"> <span class="info-box-number" id="sms_received_today">0</span></div>
                            </div>            
                            </div>
                            <!-- /.info-box-content -->
                          </div>
                          <!-- /.info-box -->
                        </div>

                        <div class="col-md-2">
                          <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fas fa-robot fa-xs"></i></span>

                            <div class="info-box-content">
                            <div class="row">
                              <div class="col-sm-12"><span class="info-box-text">Bot Sessions</span></div>
                              <div class="col-sm-12"> <span class="info-box-number" id="today_active_bot">0</span></div>
                            </div>
                
                            </div>
                            <!-- /.info-box-content -->
                          </div>
                          <!-- /.info-box -->
                        </div>
                      
                      </div>

                    </div>

                  </div>

                <!-- Row 2 -->
                  <div class="row">
                
                    <div class="col-sm-12 card" style="padding-top: 15px;background: aliceblue;border: 0.5px solid #2222;">
                      <span>Overall record</span>
                      <div class="row">

                        <div class="col-md-2">
                          <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="far fa-comment fa-xs"></i></span>

                            <div class="info-box-content">
                            <div class="row">
                              <div class="col-sm-12">
                                  <span class="info-box-text">SMS Sent</span>
                                </div>
                                
                                <div class="col-sm-12">
                                  <span class="info-box-number" id="sms_sent_till_now">0</span>
                                </div>
                              </div>
                            </div>
                            <!-- /.info-box-content -->
                          </div>
                          <!-- /.info-box -->
                        </div>

                        <div class="col-md-2">
                          <div class="info-box">
                            <span class="info-box-icon bg-secondary"><i class="far fa-comments fa-xs"></i></span>

                            <div class="info-box-content">
                            <div class="row">
                              <div class="col-sm-12"><span class="info-box-text">SMS Recieved</span></div>
                              <div class="col-sm-12"><span class="info-box-number" id="sms_received_till_now">0</span></div>
                            </div>
                          
                            </div>
                            <!-- /.info-box-content -->
                          </div>
                          <!-- /.info-box -->
                        </div>

                        <div class="col-md-2">
                          <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fab fa-whatsapp fa-xs"></i></span>

                            <div class="info-box-content">
                            <div class="row">
                              <div class="col-sm-12"><span class="info-box-text">Whatsapp Sent</span></div>
                              <div class="col-sm-12">  <span class="info-box-number" id="whatsapp_sent_till_now">0</span></div>
                            </div>
                            </div>
                            <!-- /.info-box-content -->
                          </div>
                          <!-- /.info-box -->
                        </div>

                        <div class="col-md-2">
                          <div class="info-box">
                            <span class="info-box-icon bg-green2"><i class="fab fa-whatsapp fa-xs"></i></span>

                            <div class="info-box-content">
                            <div class="row">
                              <div class="col-sm-12"><span class="info-box-text">Whatsapp Recieved</span></div>
                              <div class="col-sm-12"><span class="info-box-number" id="whatsapp_received_till_now">0</span></div>
                            </div>
                              
                            </div>
                            <!-- /.info-box-content -->
                          </div>
                          <!-- /.info-box -->
                        </div>

                        <div class="col-md-2">
                          <div class="info-box">
                            <span class="info-box-icon bg-purple2"><i class="far fa-comments fa-xs"></i></span>

                            <div class="info-box-content">
                            <div class="row">
                              <div class="col-sm-12"><span class="info-box-text">Quota Utilized</span></div>
                              <div class="col-sm-12"> <span class="info-box-number" id="sms_received_today">0</span></div>
                            </div>            
                            </div>
                            <!-- /.info-box-content -->
                          </div>
                          <!-- /.info-box -->
                        </div>

                        <div class="col-md-2">
                          <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fas fa-robot fa-xs"></i></span>

                            <div class="info-box-content">
                            <div class="row">
                              <div class="col-sm-12"><span class="info-box-text">Bot Sessions</span></div>
                              <div class="col-sm-12"> <span class="info-box-number" id="today_active_bot_till_now">0</span></div>
                            </div>

                            </div>
                            <!-- /.info-box-content -->
                          </div>
                          <!-- /.info-box -->
                        </div>
                      
                      </div>

                    </div>
                  </div>

                <!-- Row 3 -->
                <div class="row">
                
                  <div class="col-sm-4">
                    <!-- PIE CHART -->
                    <div class="card card-info card-outline">
                        <div class="card-header">
                          <h3 class="card-title">Today's SMS</h3>
                          <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                            </button>
                          </div>
                        </div>

                        <div class="card-body">
                          <canvas id="pieChart1" style="min-height: 180px; height: 180px; max-height: 180px; max-width: 100%;"></canvas>
                        </div>
                        <!-- /.card-body -->
                      </div>
                      <!-- /.card -->
                  </div>

                  <div class="col-sm-8">
                      <!-- DONUT CHART -->
                    <div class="card card-info card-outline">
                      <div class="card-header">
                        <h3 class="card-title">Last 5 days SMS</h3>
                        <div class="card-tools">
                          <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                          </button>
                        </div>
                      </div>
                      <div class="card-body">
                        <canvas id="barchart1" style="min-height: 180px; height: 180px; max-height: 180px; max-width: 100%;"></canvas>
                      </div>
                      <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                  </div>

                  <div class="col-sm-3">
                      
                    <!-- PIE CHART -->
                    <div class="card card-info card-outline card-outline">
                      <div class="card-header">
                        <h3 class="card-title">Today's Whatsapp</h3>

                        <div class="card-tools">
                          <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                          </button>
                        </div>
                      </div>
                      <div class="card-body">
                        <canvas id="pieChart2" style="min-height: 180px; height: 180px; max-height: 180px; max-width: 100%;"></canvas>
                      </div>
                      <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                  </div>

                  <div class="col-sm-3">
                      
                      <!-- PIE CHART -->
                      <div class="card card-info card-outline card-outline">
                        <div class="card-header">
                          <h3 class="card-title">Overall Whatsapp</h3>
  
                          <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                            </button>
                          </div>
                        </div>
                        <div class="card-body">
                          <canvas id="pieChart3" style="min-height: 180px; height: 180px; max-height: 180px; max-width: 100%;"></canvas>
                        </div>
                        <!-- /.card-body -->
                      </div>
                      <!-- /.card -->
                  </div>

                  <div class="col-sm-6">
                      <!-- LINE CHART -->
                    <div class="card card-info card-outline">
                      <div class="card-header">
                        <h3 class="card-title">Last 5 days Whatsapp</h3>

                        <div class="card-tools">
                          <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                          </button>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="chart">
                          <canvas id="barchart2" style="min-height: 180px; height: 180px; max-height: 180px; max-width: 100%;"></canvas>
                        </div>
                      </div>
                      <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                  </div>

                  <div class="col-sm-12">
                    <!-- BAR CHART -->
                    <div class="card card-info card-outline card-outline">
                      <div class="card-header">
                        <h3 class="card-title">Whatsapp Chatbot</h3>

                        <div class="card-tools">
                          <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                          </button>
                        </div>
                      </div>
                      <div class="card-body">
                        <div class="chart">
                          <canvas id="areaChart" style="min-height: 180px; height: 180px; max-height: 180px; max-width: 100%;"></canvas>
                        </div>
                      </div>
                      <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                  </div>
                </div>

        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer --> 
  <?php $this->load->view('layout/footer');?>
  <!-- End of footer -->

<!-- REQUIRED SCRIPTS -->

<!-- Chart SCRIPTS -->
<script src="<?php echo base_url() ?>assets/plugins/chart.js/Chart.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo base_url() ?>assets/dist/js/pages/dashboard3.js"></script>

<script type="text/javascript">
  
  


  function get_dashboard_box(){
    var img_path = "<?php echo base_url() . '/assets/images/loading.gif' ?>";
    $("[class$='_error']").html("");
    $(".custom_loader").html('<img src="' + img_path + '">');
    // var url = $(this).attr('action'); // the script where you handle the form input.

    var url = '<?php echo site_url('dashboard/get_dashboard_box') ?>' // the script where you handle the form input.
    $.ajax({
        type: "POST",
        dataType: 'JSON',
        url: url,
        data: {'dash' : '1'}, // serializes the form's elements.
        success: function (data, textStatus, jqXHR){
          
          // console.log('textStatus '+textStatus);
           console.log(data);
          // console.log(jqXHR);
         
          if (data.status === 'success' && textStatus=='success') {
            var obj = data.info ;
            $("#sms_sent_today").text(obj.sms_sent_today);
            $("#sms_sent_till_now").text(obj.sms_sent_till_now);
            $("#whatsapp_sent_today").text(obj.whatsapp_sent_today);
            $("#whatsapp_sent_till_now").text(obj.whatsapp_sent_till_now);
            $("#whatsapp_received_today").text(obj.whatsapp_received_today);
            $("#whatsapp_received_till_now").text(obj.whatsapp_received_till_now);
            $("#sms_received_today").text(obj.sms_received_today);
            $("#sms_received_till_now").text(obj.sms_received_till_now);
            $("#quota_utilized_today").text(obj.quota_utilized_today);
            $("#quota_utilized_till_now").text(obj.quota_utilized_till_now);
            $("#live_conversion").text(obj.live_conversion);
            $("#monthly_active_whatsapp").text(obj.monthly_active_whatsapp);
            $("#today_active_bot").text(obj.active_bot);
            $("#today_active_bot_till_now").text(obj.active_bot_till_now);
            $("#templates").text(obj.templates);

   

          //-------------------------
         // Farhan :: 25-06-2021 
        //------------------------

                  //-------------------------
                 // Today's SMS- PIE CHART 
                //------------------------
                var todaysms =[obj.sms_submitted_today,obj.sms_queue_today,obj.sms_delivered_today,obj.sms_undelivered_today];
                console.log('('+todaysms+')Today sms');
                  var datasms = todaysms;
                  var piedata1  =
                    {
                      labels: [
                          'Submitted',
                          'Queue',
                          'Delivered',
                          'Undelivered'
                      ],
                      datasets: [
                        {
                          data: datasms,
                          backgroundColor : ['#6c757d', '#17a2b8', '#28a745','crimson'],
                        }
                      ]
                    }
                    // Get context with jQuery - using jQuery's .get() method.
                    var pieChartCanvas = $('#pieChart1').get(0).getContext('2d')
                    var pieData        = piedata1;
                    var pieOptions     = {
                      maintainAspectRatio : false,
                      responsive : true,
                    }
                    //Create pie chart
                    new Chart(pieChartCanvas, {
                      type: 'pie',
                      data: pieData,
                      options: pieOptions
                    })

                  //--------------------------------
                 // END of Today's SMS- PIE CHART 
                //-------------------------------      

                  //-------------------------
                 // Today's whatsapp- PIE CHART 
                //------------------------
                var todaywhatsapp =[obj.success_bot_session_today,obj.coverted_agent_bot_today];
                console.log('('+todaywhatsapp+')Today whatsapp');
                  var datawhatsapp = todaywhatsapp;
                  var piedata2  =
                    {
                      labels: [
                          'Bot Session',
                          'Converted Agent',
                      ],
                      datasets: [
                        {
                          data: datawhatsapp,
                          backgroundColor : ['crimson','#28a745'],
                        }
                      ]
                    }
                    // Get context with jQuery - using jQuery's .get() method.
                    var pieChartCanvas = $('#pieChart2').get(0).getContext('2d')
                    var pieData        = piedata2;
                    var pieOptions     = {
                      maintainAspectRatio : false,
                      responsive : true,
                    }
                    //Create pie chart
                    new Chart(pieChartCanvas, {
                      type: 'pie',
                      data: pieData,
                      options: pieOptions
                    })

                  //--------------------------------
                 // END of Today's whatsapp- PIE CHART 
                //-------------------------------    

                  //-------------------------
                 // overall whatsapp- PIE CHART 
                //------------------------
                var overallwhatsapp =[obj.success_bot_session_till_now,obj.coverted_agent_bot_till_now];
                console.log('('+overallwhatsapp+')Overall whatsapp');
                  var datawhatsapp2 = overallwhatsapp;
                  var piedata3  =
                    {
                      labels: [
                          'Bot Session',
                          'Converted Agent',
                      ],
                      datasets: [
                        {
                          data: datawhatsapp2,
                          backgroundColor : ['crimson','#28a745'],
                        }
                      ]
                    }
                    // Get context with jQuery - using jQuery's .get() method.
                    var pieChartCanvas = $('#pieChart3').get(0).getContext('2d')
                    var pieData        = piedata3;
                    var pieOptions     = {
                      maintainAspectRatio : false,
                      responsive : true,
                    }
                    //Create pie chart
                    new Chart(pieChartCanvas, {
                      type: 'pie',
                      data: pieData,
                      options: pieOptions
                    })

                  //--------------------------------
                 // END of overall whatsapp- PIE CHART 
                //-------------------------------    

                  //-------------------------------
                 //Last 5 days SMS- Bar CHART 
                //------------------------------ 
                var today = new Date();
                today.setDate(today.getDate());
                var today = today.getDate()+'-'+ (today.getMonth()+1) +'-'+today.getFullYear(); 
                
                var yesterday = new Date();
                yesterday.setDate(yesterday.getDate()-1);
                var yesterday = yesterday.getDate()+'-'+ (yesterday.getMonth()+1) +'-'+yesterday.getFullYear(); 
                
                var y2 = new Date();
                y2.setDate(y2.getDate()-2);
                var y2 = y2.getDate()+'-'+ (y2.getMonth()+1) +'-'+y2.getFullYear(); 
                
                var y3 = new Date();
                y3.setDate(y3.getDate()-3);
                var y3 = y3.getDate()+'-'+ (y3.getMonth()+1) +'-'+y3.getFullYear(); 
                
                var y4 = new Date();
                y4.setDate(y4.getDate()-4);
                var y4 = y4.getDate()+'-'+ (y4.getMonth()+1) +'-'+y4.getFullYear(); 
                
                var barchartdata1 = {
                  labels  : [y4,y3,y2,yesterday,today],
                  datasets: [
                    {
                      label               : 'Queue',
                      backgroundColor     : '#17a2b8',
                      borderColor         : 'rgba(210, 214, 222, 1)',
                      pointRadius         : false,
                      pointColor          : 'rgba(210, 214, 222, 1)',
                      pointStrokeColor    : '#c1c7d1',
                      pointHighlightFill  : '#fff',
                      pointHighlightStroke: 'rgba(220,220,220,1)',
                      data                : [obj.sms_queue_y4, obj.sms_queue_y3, obj.sms_queue_y2, obj.sms_queue_yesterday,obj.sms_queue_today]
                    },
                    {
                      label               : 'Submitted',
                      backgroundColor     : '#6c757d',
                      borderColor         : 'rgba(60,141,188,0.8)',
                      pointRadius          : false,
                      pointColor          : '#3b8bba',
                      pointStrokeColor    : 'rgba(60,141,188,1)',
                      pointHighlightFill  : '#fff',
                      pointHighlightStroke: 'rgba(60,141,188,1)',
                      data                : [obj.sms_submitted_y4, obj.sms_submitted_y3, obj.sms_submitted_y2, obj.sms_submitted_yesterday,obj.sms_submitted_today]
                    },
                    {
                      label               : 'Delivered',
                      backgroundColor     : '#28a745',
                      borderColor         : 'rgba(210, 214, 222, 1)',
                      pointRadius         : false,
                      pointColor          : 'rgba(210, 214, 222, 1)',
                      pointStrokeColor    : '#c1c7d1',
                      pointHighlightFill  : '#fff',
                      pointHighlightStroke: 'rgba(220,220,220,1)',
                      data                : [obj.sms_delivered_y4, obj.sms_delivered_y3, obj.sms_delivered_y2, obj.sms_delivered_yesterday, obj.sms_delivered_today]
                    },
                    {
                      label               : 'undelivered',
                      backgroundColor     : 'crimson',
                      borderColor         : 'rgba(210, 214, 222, 1)',
                      pointRadius         : false,
                      pointColor          : 'rgba(210, 214, 222, 1)',
                      pointStrokeColor    : '#c1c7d1',
                      pointHighlightFill  : '#fff',
                      pointHighlightStroke: 'rgba(220,220,220,1)',
                      data                : [obj.sms_undelivered_y4, obj.sms_undelivered_y3, obj.sms_undelivered_y2, obj.sms_undelivered_yesterday, obj.sms_undelivered_today]
                    }
                  ]
                }
                var barChartCanvas = $('#barchart1').get(0).getContext('2d')
                  var barChartData = $.extend(true, {}, barchartdata1)
                  var temp0 = barchartdata1.datasets[0]
                  var temp1 = barchartdata1.datasets[1]
                  barChartData.datasets[0] = temp1
                  barChartData.datasets[1] = temp0

                  var barChartOptions = {
                    responsive              : true,
                    maintainAspectRatio     : false,
                    datasetFill             : false
                  }

                  new Chart(barChartCanvas, {
                    type: 'bar',
                    data: barChartData,
                    options: barChartOptions
                })             
                  //--------------------------------------
                 // END of Last 5 days SMS- Bar CHART 
                //-------------------------------------- 


                  //-------------------------------
                 //Last 5 days whatsapp- Bar CHART 
                //------------------------------ 
                var today = new Date();
                today.setDate(today.getDate());
                var today = today.getDate()+'-'+ (today.getMonth()+1) +'-'+today.getFullYear(); 
                
                var yesterday = new Date();
                yesterday.setDate(yesterday.getDate()-1);
                var yesterday = yesterday.getDate()+'-'+ (yesterday.getMonth()+1) +'-'+yesterday.getFullYear(); 
                
                var y2 = new Date();
                y2.setDate(y2.getDate()-2);
                var y2 = y2.getDate()+'-'+ (y2.getMonth()+1) +'-'+y2.getFullYear(); 
                
                var y3 = new Date();
                y3.setDate(y3.getDate()-3);
                var y3 = y3.getDate()+'-'+ (y3.getMonth()+1) +'-'+y3.getFullYear(); 
                
                var y4 = new Date();
                y4.setDate(y4.getDate()-4);
                var y4 = y4.getDate()+'-'+ (y4.getMonth()+1) +'-'+y4.getFullYear(); 
                
                var barchartdata2 = {
                  labels  : [y4,y3,y2,yesterday,today],
                  datasets: [
                    {
                      label               : 'Coverted Agent',
                      backgroundColor     : '#28a745',
                      borderColor         : 'rgba(210, 214, 222, 1)',
                      pointRadius         : false,
                      pointColor          : 'rgba(210, 214, 222, 1)',
                      pointStrokeColor    : '#c1c7d1',
                      pointHighlightFill  : '#fff',
                      pointHighlightStroke: 'rgba(220,220,220,1)',
                      data                : [obj.coverted_agent_bot_y4, obj.coverted_agent_bot_y3, obj.coverted_agent_bot_y2, obj.coverted_agent_bot_yesterday,obj.coverted_agent_bot_today]
                    },
                    {
                      label               : 'Bot session',
                      backgroundColor     : 'crimson',
                      borderColor         : 'rgba(60,141,188,0.8)',
                      pointRadius          : false,
                      pointColor          : '#3b8bba',
                      pointStrokeColor    : 'rgba(60,141,188,1)',
                      pointHighlightFill  : '#fff',
                      pointHighlightStroke: 'rgba(60,141,188,1)',
                      data                : [obj.success_bot_session_y4, obj.success_bot_session_y3, obj.success_bot_session_y2, obj.success_bot_session_yesterday,obj.success_bot_session_today]
                    }
                  ]
                }
                var barChartCanvas = $('#barchart2').get(0).getContext('2d')
                  var barChartData2 = $.extend(true, {}, barchartdata2)
                  var temp0 = barChartData2.datasets[0]
                  var temp1 = barChartData2.datasets[1]
                  barChartData2.datasets[0] = temp1
                  barChartData2.datasets[1] = temp0

                  var barChartOptions = {
                    responsive              : true,
                    maintainAspectRatio     : false,
                    datasetFill             : false
                  }

                  new Chart(barChartCanvas, {
                    type: 'bar',
                    data: barChartData2,
                    options: barChartOptions
                })             
                  //--------------------------------------
                 // END of Last 5 days whatsapp- Bar CHART 
                //--------------------------------------  

          //-------------------------
         // Farhan :: 25-06-2021 
        //------------------------    

          } else {
              successMsg(data.msg);
          }
          $(".custom_loader").html("");

        },
        error: function (jqXHR, textStatus, errorThrown)
        {
          /*console.log('StatusERR ');
          console.log('textStatusERR '+textStatus);
          console.log(data);
          console.log(jqXHR);*/
            //$(".custom_loader").html("");
            //if fails      
        }
    });
  }


  //onload 
  $(function () {
    get_dashboard_box();
  
      //--------------
     //- AREA CHART -
    //--------------

    // Get context with jQuery - using jQuery's .get() method.
    var areaChartCanvas = $('#areaChart').get(0).getContext('2d')

    var areaChartData = {
      labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
      datasets: [
        {
          label               : 'Digital Goods',
          backgroundColor     : 'coral',
          borderColor         : 'crimson',
          pointRadius          : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : [28, 48, 40, 19, 86, 27, 90]
        },
        {
          label               : 'Electronics',
          backgroundColor     : 'gainsboro',
          borderColor         : 'coral',
          pointRadius         : false,
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : [65, 59, 80, 81, 56, 55, 40]
        },
      ]
    }

    var areaChartOptions = {
      maintainAspectRatio : false,
      responsive : true,
      legend: {
        display: false
      },
      scales: {
        xAxes: [{
          gridLines : {
            display : false,
          }
        }],
        yAxes: [{
          gridLines : {
            display : false,
          }
        }]
      }
    }

    // This will get the first returned node in the jQuery collection.
    new Chart(areaChartCanvas, {
      type: 'line',
      data: areaChartData,
      options: areaChartOptions
    })
    
    
  });
</script>
