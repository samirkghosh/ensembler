<?php
    /* check license access or not for  this module*/ 
    include_once "classify_function.php"; 
    $module_flag_customer = module_license('Email');
    if($module_flag_customer !='1') {
      header("Location:web_admin_dashboard.php"); 
      exit();
    }
    /***END***/
    include "web_mysqlconnect.php"; 
    $name= $_SESSION['logged'];
    $selecttab=8;
    include "header.php";



    $disposition_types = fetchDispositionTypes($link, $db);
    $selected_disposition_type = $disposition_type ?? '';




?>
<link rel="stylesheet" href="assets/datatable/css/dataTables.dataTables.css">
<link rel="stylesheet" href="assets/datatable/css/buttons.dataTables.css">
<link rel="stylesheet" href="assets/css/email.css">

<body>
    <div class="wrapper">
      <div class="container-fluid">
          <div class="row" style="min-height:90vh">
              <div class="col-sm-2" style="padding-left:0">
                  <? include "sidebar.php";?>
              </div>
              <div class="col-sm-10 mt-3" style="padding-left:0">
                <div class="row inbox-wrapper">
                    <!-- <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                
                            </div>
                        </div>
                    </div> -->
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-2 email-aside border-lg-right">
                                        <div class="aside-content">
                                            <div class="aside-compose">
                                                <h5><svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="50" height="50" viewBox="0 0 48 48">
                                                    <path fill="#4caf50" d="M45,16.2l-5,2.75l-5,4.75L35,40h7c1.657,0,3-1.343,3-3V16.2z"></path><path fill="#1e88e5" d="M3,16.2l3.614,1.71L13,23.7V40H6c-1.657,0-3-1.343-3-3V16.2z"></path><polygon fill="#e53935" points="35,11.2 24,19.45 13,11.2 12,17 13,23.7 24,31.95 35,23.7 36,17"></polygon><path fill="#c62828" d="M3,12.298V16.2l10,7.5V11.2L9.876,8.859C9.132,8.301,8.228,8,7.298,8h0C4.924,8,3,9.924,3,12.298z"></path><path fill="#fbc02d" d="M45,12.298V16.2l-10,7.5V11.2l3.124-2.341C38.868,8.301,39.772,8,40.702,8h0 C43.076,8,45,9.924,45,12.298z"></path>
                                                    </svg></h5>
                                            </div>

                                            <div class="aside-nav collapse">
                                                <ul class="nav">
                                                    <li class="active">
                                                        <a href="javascript:void(0)" id="inbox"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-inbox"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg></span>All Mails</a>
                                                    </li>
                                                
                                                    <li>
                                                        <a href="javascript:void(0)" id="read"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file read"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><polyline points="13 2 13 9 20 9"></polyline></svg></span>Read</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)" id="unread"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star unread"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></span>Unread</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)" id="spam"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tag text-warning"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg> Spam </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)" id="inquiry">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle text-danger"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>Inquiry</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)" id="servicable">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tool text-secondary"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg> Servicable</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)" id="nonservicable">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-slash text-info"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg> Non Servicable</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)" id="positive">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle text-success"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg> Positive</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)" id="negative">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-minus-circle text-danger"><circle cx="12" cy="12" r="10"></circle><line x1="8" y1="12" x2="16" y2="12"></line></svg> Negative</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)" id="neutral">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-zap-off text-primary"><polyline points="12.41 6.75 13 2 10.57 4.92"></polyline><polyline points="18.57 12.91 21 10 15.66 10"></polyline><polyline points="8 8 3 14 12 14 11 22 16 16"></polyline><line x1="1" y1="1" x2="23" y2="23"></line></svg> Neutral</a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:void(0)" id="trash"><span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></span>Trash</a>
                                                    </li>

                                                </ul>
                                               
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-lg-10 email-content email-list">
                                        <div class="email-head">
                                        
                                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                <div class="d-flex align-items-center">
                                                    
                                                    <div class="d-flex align-items-end">
                                                        <div class="row">
                                                            <div class="form-group col-md-3" style="align-content: center;">
                                                                <div class="input-group">
                                                                    <input type="text" name="startdate" id="startdate" class="form-control date_class" autocomplete="off" placeholder="Start Date">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-md-3" style="align-content: center;">
                                                                <div class="input-group">
                                                                    <input type="text" name="enddate" id="enddate" class="form-control date_class" autocomplete="off" placeholder="End Date">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-md-3" style="align-content: center;">
                                                                <div class="input-group">
                                                                    <input type="text" name="emailid" id="emailid" class="form-control" autocomplete="off" placeholder="Email or Ticket">
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="form-group col-md-3">
                                                          
                                                                <div class="input-group">
                                                                    
                                                                    <a href="javascript:void(0)" name="search-record" id="search-record" class="mt-3 px-1" data-toggle="tooltip" title="Search">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 48 48">
                                                                    <path fill="#616161" d="M34.6 28.1H38.6V45.1H34.6z" transform="rotate(-45.001 36.586 36.587)"></path><path fill="#616161" d="M20 4A16 16 0 1 0 20 36A16 16 0 1 0 20 4Z"></path><path fill="#37474F" d="M36.2 32.1H40.2V44.400000000000006H36.2z" transform="rotate(-45.001 38.24 38.24)"></path><path fill="#64B5F6" d="M20 7A13 13 0 1 0 20 33A13 13 0 1 0 20 7Z"></path><path fill="#BBDEFB" d="M26.9,14.2c-1.7-2-4.2-3.2-6.9-3.2s-5.2,1.2-6.9,3.2c-0.4,0.4-0.3,1.1,0.1,1.4c0.4,0.4,1.1,0.3,1.4-0.1C16,13.9,17.9,13,20,13s4,0.9,5.4,2.5c0.2,0.2,0.5,0.4,0.8,0.4c0.2,0,0.5-0.1,0.6-0.2C27.2,15.3,27.2,14.6,26.9,14.2z"></path>
                                                                    </svg></a>

                                                                    <div class="dropbtn" onclick="myDropdown()">
                                                                        <div class="bar"></div>
                                                                        <div class="bar"></div>
                                                                        <div class="bar"></div>
                                                                    </div>
                                                                    
                                                                    <div id="myDropdown" class="dropdown-content">

                                                                        <a href="javascript:void(0)" id="btn_spam"> Mark as spam <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-tag text-warning"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg></a>

                                                                        <a href="javascript:void(0)" id="btn_trash"> Move to tash <span class="icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash text-danger"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></span></a>

                                                                        <a href="javascript:void(0)" id="btn_delete"> Delete mails &nbsp;<span class="icon">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 40 40">
                                                                        <path fill="#f78f8f" d="M21 24.15L8.857 36.293 4.707 32.143 16.85 20 4.707 7.857 8.857 3.707 21 15.85 33.143 3.707 37.293 7.857 25.15 20 37.293 32.143 33.143 36.293z"></path><path fill="#c74343" d="M33.143,4.414l3.443,3.443L25.15,19.293L24.443,20l0.707,0.707l11.436,11.436l-3.443,3.443 L21.707,24.15L21,23.443l-0.707,0.707L8.857,35.586l-3.443-3.443L16.85,20.707L17.557,20l-0.707-0.707L5.414,7.857l3.443-3.443 L20.293,15.85L21,16.557l0.707-0.707L33.143,4.414 M33.143,3L21,15.143L8.857,3L4,7.857L16.143,20L4,32.143L8.857,37L21,24.857 L33.143,37L38,32.143L25.857,20L38,7.857L33.143,3L33.143,3z"></path>
                                                                        </svg></span></a>

                                                                    </div>
                                                                
                                                                </div>
                                                            </div>
                                                               
                                                        </div>
                                                    </div>
                                                   
                                                </div>
                                                
                                            </div>
                                        </div>

                                        <div class="table-body">   
                                           <table id="complaint_records" class="table table-bordered table-hover table-responsive">
                                                <thead class="bg-success text-white">
                                                    <tr>
                                                        <th scope="col"><input type="checkbox" id="checkall"></th>
                                                        <th scope="col">From</th>
                                                        <th scope="col">Subject</th>
                                                        <th scope="col">Case ID</th>
                                                        <th scope="col">Classification</th>
                                                        <th scope="col">Sentiment</th>
                                                        <th scope="col">Date Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                </tbody>
                                            </table>
                                        </div>

                                    </div> 

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>

          </div>
        </div>
        <div class="footer">
            <? include "web_footer.php"; ?>
        </div>
    </div>
    
    <!-- Modal FOR VIEWING MAILS -->
    <div class="modal fade" id="mailsModal" tabindex="-1" aria-labelledby="mailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star text-warning px-1"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </span>
                    <h5 class="modal-title text-success" id="mailsModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="email-content email-view">
                        <div class="email-head">
                            <div class="email-head-sender d-flex align-items-center justify-content-between flex-wrap">
                                <div class="d-flex align-items-center">
                                    <div class="avatar">
                                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRjnswlsgugOxzkHlyTIB7rU2sgLX9u2N34urEg2VMi8Q&s" alt="Avatar" class="rounded-circle user-avatar-md">
                                    </div>
                                    <div class="sender d-flex align-items-center text-success">
                                        <span class="mail-id"></span>to me
                                    </div>
                                </div>
                                <div class="date email-date text-danger"></div>
                            </div>
                        </div>
                        <div class="email-body"></div>
                        <div class="email-attachment"></div>
                    </div>
                </div>
                <div class="msg-alert text-center text-secondary fw-bold"></div>
                <div class="modal-footer justify-content-center"></div>

                <input type="hidden" name="channel_id" id="channel_id">
                <input type="hidden" name="channel_type" id="channel_type" value="Email">

                <!-- Disposition Block -->
                <div class="container-fluid">
                    <div class="email-head">
                        <div class="email-head-sender d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex align-items-center">
                                <div class="avatar">
                                <img src="https://cdn-icons-png.flaticon.com/512/8766/8766320.png" alt="Avatar" class="rounded-circle user-avatar-md">
                                </div>
                                <div class="sender d-flex align-items-center text-success mx-2">
                                Channel Disposition
                                </div>
                                <form id="modalForm">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <select name="dispostion_type" id="dispostion_type" class="form-control form-control-sm mb-2" required>
                                            <?php echo DispoSelectOptions($disposition_types, $selected_disposition_type); ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-12">
                                            <textarea name="email_remark" id="email_remark" class="form-control form-control-sm mb-2" rows="3" placeholder="Type here..." required></textarea>
                                        </div>
                                        <div class="col-sm-12 text-center">
                                            <input type="button" id="create_disposition" class="btn btn-success btn-sm mb-2" value="Dispose">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-dispo text-center"></div>
                </div>
                <!-- Disposition Block -->
            </div>
        </div>
    </div>

</body>

<script src="assets/datatable/js/dataTables.js"></script>
<script src="assets/datatable/js/dataTables.buttons.js"></script>
<script src="assets/datatable/js/buttons.dataTables.js"></script>
<script src="assets/datatable/js/jszip.min.js"></script>
<script src="assets/datatable/js/pdfmake.min.js"></script>
<script src="assets/datatable/js/vfs_fonts.js"></script>
<script src="assets/datatable/js/buttons.html5.min.js"></script>
<script src="assets/datatable/js/buttons.print.min.js"></script>
<script src="assets/customjs/mail.js"></script>
</html>
