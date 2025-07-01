<?php
/**
* Auth: Ritu modi 
* Date- 20-03-24
* page :Overall Dashboard Page.
*/
$new_dashboard = base64_encode('new_dashboard');
$web_admin_dashboard = base64_encode('web_admin_dashboard');
$id = base64_decode($_GET['id']);
 $current_date = date("Y/m/d H:i");
$current_date1 = date("Y/m/d") . " 23:59";
$today_date         = date("Y-m-d");
$start_date = (isset($_REQUEST['start_date'])) ? date("Y-m-d H:i", strtotime($start_date)) . ":00" : date("Y-m-d 00:00", strtotime($current_date1)) . ":00";
$end_date = (isset($_REQUEST['end_date'])) ? date("Y-m-d H:i", strtotime($end_date)) . ":00" : date("Y-m-d 23:59", strtotime($current_date)) . ":00";
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Overall Dashboard</title>
   <!-- Google Font: Source Sans Pro -->
   <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
   <link rel="stylesheet" href="<?=$SiteURL?>public/LTE/plugins/fontawesome-free/css/all.min.css">
   <link rel="stylesheet" href="<?=$SiteURL?>public/LTE/css/adminlte.min.css">
   <!-- Font Awesome Icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/jquery.datetimepicker.min.css" />
   <link rel="stylesheet" href="<?=$SiteURL?>public/css/NewDashboard.css">
   <script type="text/javascript" src="https://code.highcharts.com/highcharts.js"></script>
   <script type="text/javascript" src="https://code.highcharts.com/modules/exporting.js"></script>
   <script type="text/javascript" src="https://code.highcharts.com/modules/export-data.js"></script>
   <script type="text/javascript" src="https://code.highcharts.com/modules/accessibility.js"></script>

</head>
<body>
   <div class="wrapper">
      <section class="contents" >
         <div class="container-fluid mt-2"  id="html-content" style="background-color: white !important;">
            <!-- BOXES ---START  -->
            <!-- Row 1 ~ 2 rows -->
            <div class="row px-2">
               <div class="col-sm-6 mb-2">
                  <a href="dashboard_index.php?token=<?php echo $web_admin_dashboard;?>">Home</a> / Overall Dashboard
               </div>
               <?php
               $startdatetime = ($_REQUEST['sttartdatetime'] != '') ? ($_REQUEST['sttartdatetime']) : date("d-m-Y 00:00:00");
               $enddatetime = ($_REQUEST['enddatetime'] != '') ? ($_REQUEST['enddatetime'])  : date("d-m-Y 23:59:59");
               ?>
               <div class="col-sm-6 mb-2">
                  <input type="text" name="startdatetime" id="startdatetime" class="form-control date_class dob1" placeholder="Start Date" autocomplete="off" value="<?= $startdatetime ?>" style="width:190px;display: inline; background-image: none;">
                  <input type="text" name="enddatetime" id="enddatetime" class="form-control date_class dob1" placeholder="End Date" autocomplete="off" value="<?= $enddatetime ?>" style="width:190px;display: inline; background-image: none;">
                  <input type="submit" class="btn btn-sm btn-success" value="Search" onclick="get_dashboard_box();" style="padding: 7px;margin-top: -6px;">
                  <input type="submit" class="btn btn-sm btn-danger" value="Reset" id="reset" onClick="window.location.reload();" style="padding: 7px;margin-top: -6px;">
                  <input type="button" value="JPG Convert" id="btnConvert" class="btn btn-sm btn-primary" style="padding: 7px;margin-top: -6px;">
               </div>
               <div class="col-sm-6 card" style="padding-top: 15px;background: floralwhite;border: 0.5px solid #2222;">
                  <span>CRM</span>
                  <div class="row">
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-info"><i class="fas fa-suitcase-rolling"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12">
                                    <span class="info-box-text">Total Case</span>
                                 </div>
                                 <div class="col-sm-12">
                                    <span class="info-box-number" id="total_case_today">0</span>
                                 </div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-secondary"><i class="fas fa-archive"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Complaints</span></div>
                                 <div class="col-sm-12"><span class="info-box-number" id="total_compalint">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-success"><i class="fas fa-person-booth"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Inquiries</span></div>
                                 <div class="col-sm-12"> <span class="info-box-number" id="total_inquiries">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-green2"><i class="fab fa-instalod"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Others</span></div>
                                 <div class="col-sm-12"><span class="info-box-number" id="total_others">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon" style="background:#C41E3A;color:#fff;"><i class="fas fa-sync"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Pending</span></div>
                                 <div class="col-sm-12"> <span class="info-box-number" id="pending">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon" style="background:#F28C28;color:#fff;"><i class="fas fa-times-circle"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">In Progress</span></div>
                                 <div class="col-sm-12"> <span class="info-box-number" id="resolved">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon" style="background:#228B22;color:#fff;"><i class="fas fa-times-circle"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Closed</span></div>
                                 <div class="col-sm-12"> <span class="info-box-number" id="closed">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-primary"><i class="fas fa-check-circle"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Escalated</span></div>
                                 <div class="col-sm-12"> <span class="info-box-number" id="escalated">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                  </div>
               </div>
               <div class="col-sm-6 card" style="padding-top: 15px;background: aliceblue;border: 0.5px solid #2222;">
                  <span>Telephony</span>
                  <div class="row">
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-info"><i class="fas fa-phone-alt"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12">
                                    <span class="info-box-text">Inbound</span>
                                 </div>
                                 <div class="col-sm-12">
                                    <span class="info-box-number" id="inbound">0</span>
                                 </div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-secondary"><i class="fas fa-phone"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Outbound</span></div>
                                 <div class="col-sm-12"><span class="info-box-number" id="outbound">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-success"><i class="fas fa-phone-slash"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Missed Call</span></div>
                                 <div class="col-sm-12"> <span class="info-box-number" id="missedcall">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-green2"><i class="fas fa-envelope-open-text"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Voice Mail</span></div>
                                 <div class="col-sm-12"><span class="info-box-number" id="voicemail">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-purple2"><i class="fas fa-at"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Abandon</span></div>
                                 <div class="col-sm-12"> <span class="info-box-number" id="abandon">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-danger"><i class="fas fa-user-clock"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Total Talktime</span></div>
                                 <div class="col-sm-12"> <span class="info-box-number" id="total_talktime">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-primary"><i class="fas fa-stopwatch"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Avg Talktime</span></div>
                                 <div class="col-sm-12"> <span class="info-box-number" id="average_talktime">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-purple"><i class="fas fa-times"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">In Wrap Up</span></div>
                                 <div class="col-sm-12"> <span class="info-box-number" id="wrapup">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                  </div>
               </div>
            </div>
            <!-- End of Row 1 -->
            <!-- Row 2 -->
            <div class="row px-2">
               <div class="col-sm-6 card" style="padding-top: 15px;background: #b063eb2b;border: 0.5px solid #2222;">
                  <span>Channel Utilization</span>
                  <div class="row">
                     
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-primary"><i class="fab fa-twitter"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Twitter</span></div>
                                 <div class="col-sm-12"><span class="info-box-number" id="twitter_recieved">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-warning"><i class="fas fa-user"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Email</span></div>
                                 <div class="col-sm-12"> <span class="info-box-number" id="email">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
              
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-info"><i class="fas fa-sms"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">SMS</span></div>
                                 <div class="col-sm-12"> <span class="info-box-number" id="sms_cnt">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-secondary"><i class="fas fa-comment"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Live Chat</span></div>
                                 <div class="col-sm-12"> <span class="info-box-number" id="chat_bot">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>

                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-info"><i class="fab fa-facebook-f"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">facebook</span></div>
                                 <div class="col-sm-12"><span class="info-box-number" id="facebook_recieved">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>

                     <div class="col-md-3">
                        <div class="info-box">
                           <span class="info-box-icon bg-success"><i class="fab fa-whatsapp"></i></span>
                           <div class="info-box-content">
                              <div class="row">
                                 <div class="col-sm-12"><span class="info-box-text">Whatsapp</span></div>
                                 <div class="col-sm-12"><span class="info-box-number" id="whatsapp_bot">0</span></div>
                              </div>
                           </div>
                           <!-- /.info-box-content -->
                        </div>
                        <!-- /.info-box -->
                     </div>
                    
                  </div>
               </div>
               <div class="col-sm-6 card" style="padding-top: 15px;background: #b063eb2b;border: 0.5px solid #2222;">
               <span>Telephony Utilization</span>
                  <div class="row">
                     <div class="col-md-3">
                           <div class="info-box">
                              <span class="info-box-icon bg-danger"><i class="fas fa-phone-alt"></i></span>
                              <div class="info-box-content">
                                 <div class="row">
                                    <div class="col-sm-12">
                                       <span class="info-box-text">In + Out</span>
                                    </div>
                                    <div class="col-sm-12">
                                       <span class="info-box-number" id="total_calls">0</span>
                                    </div>
                                 </div>
                              </div>
                              <!-- /.info-box-content -->
                           </div>
                           <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                           <div class="info-box">
                              <span class="info-box-icon bg-purple"><i class="fas fa-phone-alt"></i></span>
                              <div class="info-box-content">
                                 <div class="row">
                                    <div class="col-sm-12">
                                       <span class="info-box-text">Outbound</span>
                                    </div>
                                    <div class="col-sm-12">
                                       <span class="info-box-number" id="total_outbound">0</span>
                                    </div>
                                 </div>
                              </div>
                              <!-- /.info-box-content -->
                           </div>
                           <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                           <div class="info-box">
                              <span class="info-box-icon" style="background:#F28C28;color:#fff;"><i class="fas fa-phone-alt"></i></span>
                              <div class="info-box-content">
                                 <div class="row">
                                    <div class="col-sm-12">
                                       <span class="info-box-text">Out Ans</span>
                                    </div>
                                    <div class="col-sm-12">
                                       <span class="info-box-number" id="out_answer">0</span>
                                    </div>
                                 </div>
                              </div>
                              <!-- /.info-box-content -->
                           </div>
                           <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                           <div class="info-box">
                              <span class="info-box-icon bg-green"><i class="fas fa-phone-alt"></i></span>
                              <div class="info-box-content">
                                 <div class="row">
                                    <div class="col-sm-12">
                                       <span class="info-box-text">Out No Ans</span>
                                    </div>
                                    <div class="col-sm-12">
                                       <span class="info-box-number" id="out_noanswer">0</span>
                                    </div>
                                 </div>
                              </div>
                              <!-- /.info-box-content -->
                           </div>
                           <!-- /.info-box -->
                     </div>
                  </div>
                  <span>Past Hour</span>
                  <div class="row">
                     <div class="col-md-3">
                           <div class="info-box">
                              <span class="info-box-icon bg-success"><i class="fas fa-phone-alt"></i></span>
                              <div class="info-box-content">
                                 <div class="row">
                                    <div class="col-sm-12">
                                       <span class="info-box-text">Avg Talktime</span>
                                    </div>
                                    <div class="col-sm-12">
                                       <span class="info-box-number" id="average_duration">0</span>
                                    </div>
                                 </div>
                              </div>
                              <!-- /.info-box-content -->
                           </div>
                           <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                           <div class="info-box">
                              <span class="info-box-icon bg-primary"><i class="fas fa-phone-alt"></i></span>
                              <div class="info-box-content">
                                 <div class="row">
                                    <div class="col-sm-12">
                                       <span class="info-box-text">Avg Wraptime</span>
                                    </div>
                                    <div class="col-sm-12">
                                       <span class="info-box-number" id="average_wrapup">0</span>
                                    </div>
                                 </div>
                              </div>
                              <!-- /.info-box-content -->
                           </div>
                           <!-- /.info-box -->
                     </div>
                     <div class="col-md-3">
                           <div class="info-box">
                              <span class="info-box-icon bg-info"><i class="fas fa-phone-alt"></i></span>
                              <div class="info-box-content">
                                 <div class="row">
                                    <div class="col-sm-12">
                                       <span class="info-box-text">Highest TT</span>
                                    </div>
                                    <div class="col-sm-12">
                                       <span class="info-box-number" id="longest_duration">0</span>
                                    </div>
                                 </div>
                              </div>
                              <!-- /.info-box-content -->
                           </div>
                           <!-- /.info-box -->
                     </div>
                    
                  </div>
               </div>
            </div>
            <!-- End of Row 2 -->
            <!-- BOXES ---END -->
            <!-- CHARTS ---START -->
            <div class="row">
               <div class="col-sm-4">
                  <!-- PIE CHART -->
                  <div class="card card-info card-outline card-outline">
                     <div class="card-header">
                        <h3 class="card-title">Category</h3>
                        <div class="card-tools">
                           <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                           </button>
                        </div>
                     </div>
                     <div class="card-body" id="category">
                        <canvas id="pieChart1" style="min-height:197px"></canvas>
                     </div>
                     <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
               </div>
               <div class="col-sm-4">
                  <!-- PIE CHART -->
                  <div class="card card-info card-outline card-outline">
                     <div class="card-header">
                        <h3 class="card-title">Sub Category</h3>
                        <div class="card-tools">
                           <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                           </button>
                        </div>
                     </div>
                     <div class="card-body" id="subcategory">
                        <canvas id="pieChart2" style="min-height:197px"></canvas>
                     </div>
                     <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
               </div>
               <div class="col-sm-4">

                  <div class="card card-info card-outline card-outline">
                     <div class="card-header">
                        <h3 class="card-title">Case Priority</h3>
                        <div class="card-tools">
                           <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                           </button>
                        </div>
                     </div>
                     <div class="card-body" id="priority">

                        <canvas id="pieChart3" style="min-height:197px"></canvas>
                     </div>

                  </div>
               </div>
               <div class="col-sm-6">
                  <!-- LINE CHART -->
                  <div class="card card-info card-outline">
                     <div class="card-header">
                        <h3 class="card-title">Social Media</h3>
                        <div class="card-tools">
                           <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                           </button>
                        </div>
                     </div>
                     <div class="card-body">
                        <div class="chart" id="socialmedia">
                           <canvas id="barchart1" style="min-height:197px"></canvas>
                        </div>
                     </div>
                     <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
               </div>
               <div class="col-sm-6">
                  <!-- LINE CHART -->
                  <div class="card card-info card-outline">
                     <div class="card-header">
                        <h3 class="card-title">Ageing Matrix</h3>
                        <div class="card-tools">
                           <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                           </button>
                        </div>
                     </div>
                     <div class="card-body" id="ageing">
                        <div class="chart">
                           <canvas id="barchart3" style="min-height:197px"></canvas>
                        </div>
                     </div>
                     <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
               </div>
               <div class="col-sm-8">
                  <div class="card card-info card-outline card-outline">
                     <div class="card-header">
                        <h3 class="card-title">Overall Campaign Disposition List</h3>
                        <div class="card-tools">
                           <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                           </button>
                        </div>
                     </div>
                     <div class="card-body" style="min-height:282px;overflow-x:scroll">
                      <div id="disposition_data"></div>
                     </div>
                     <!-- /.card-body -->
                  </div>
                  <!-- /.card -->
               </div>
               <div class="col-sm-4">
                  <div class="card card-info card-outline card-outline">
                     <div class="card-header">
                        <h3 class="card-title">Agents</h3>
                        <div class="card-tools">
                           <?php
                           $sql_agent = getLiveAgentsCount($link, $today_date);
                           $num_liveuser = mysqli_num_rows($sql_agent);
                           ?>
                           <span class="badge badge-danger"><?= $num_liveuser ?> Live Agents</span>
                           <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                           </button>
                        </div>
                     </div>
                     <!-- /.card-header -->
                     <div class="card-body" style="min-height:282px;overflow-x:scroll">
                        <ul class="users-list clearfix">
                           <?php
                           if ($num_liveuser) {
                              while ($row_agent = mysqli_fetch_array($sql_agent)) {

                                 if ($row_agent['status'] == 'INCALL') {

                                    $status_show = '<span class="users-list-date badge badge-danger text-white">Incall</span>';
                                 } else if ($row_agent['status'] == 'PAUSED') {
                                    $status_show = '<span class="users-list-date badge badge-success text-white">paused</span>';
                                 } else if ($row_agent['status'] == 'READY') {
                                    $status_show = '<span class="users-list-date badge badge-info text-white">ready</span>';
                                 } else if ($row_agent['status'] == 'WRAPUP') {
                                    $status_show = '<span class="users-list-date badge badge-success text-white">wrapup</span>';
                                 } else if ($row_agent['status'] == 'MWRAPUP') {
                                    $status_show = '<span class="users-list-date badge badge-primary text-white">mwrapup</span>';
                                 } else if ($row_agent['status'] == 'MINCALL') {
                                    $status_show = '<span class="users-list-date badge badge-warning text-white">mincall</span>';
                                 }
                                    
                           ?>
                                 <li>

                                    <img alt="<?= $row_agent['user'] ?>" src="../public/images/agent.png" alt="User Image" style="    max-width: 65%;">
                                    <a class="users-list-name" href="#"><?= $row_agent['user'] ?></a>
                                    <?= $status_show ?>
                                 </li>
                              <? } //end of while
                           } else {
                              ?>
                              <li>
                                 <img src="../public/images/agent.png" alt="User Image" style="max-width: 65%;">
                                 <a class="users-list-name" href="#">No Live User</a>
                              </li>
                           <?
                           } ?>
                        </ul>
                        <!-- /.users-list -->
                     </div>
                     <!-- /.card-body -->
                  </div>
               </div>
               <div class="col-sm-3" style="display:none">
                  <div class="card card-info card-outline card-outline">
                     <div class="card-header">
                        <h3 class="card-title">Total Cases V/s Productive Calls</h3>
                        <div class="card-tools">
                           <button type="button" class="btn btn-tool" data-card-widget="collapse">
                              <i class="fas fa-minus"></i>
                           </button>
                        </div>
                     </div>
                     <card class="card-body" style="min-height:282px">
                        <span id="productive"></span>
                     </card>
                  </div>
               </div>
            </div>
            <!-- CHARTS ---END -->
            <!-- /.row -->
         </div>
      </section></div>
<!-- jQuery -->
<script type="text/javascript" src="<?=$SiteURL?>public/LTE/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script type="text/javascript" src="<?=$SiteURL?>public/LTE/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script type="text/javascript" src="<?=$SiteURL?>public/LTE/js/adminlte.min.js"></script>
<!-- Chart SCRIPTS -->
<script type="text/javascript" src="<?=$SiteURL?>public/LTE/plugins/chart.js/Chart.min.js"></script>
<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/LTE/js/guage.min.js"></script>
<script type="text/javascript" src="<?=$SiteURL?>public/js/NewDashboard.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.4/build/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>
<script  type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.debug.js"></script>

<!--<script type="text/javascript" src="<?=$SiteURL?>public/LTE/js/pages/dashboard3.js"></script>
Guage js -->

</html>
