<?php 
$base_url_new = 'http://165.232.183.220/ensembler/';

$agent_id = $_SESSION['userid'];
 $chat_session = $_GET['chat_session'];
// $conversation = $this->db->get_where('bot_chat_session', array('user_id' => $agent_id) )->row();
$conversation = $this->db->get_where('bot_chat_session', array('chat_session' => $chat_session) )->row();
$phone = $conversation->from;
$chat_session = $conversation->chat_session;
$url = '';
if(!empty($chat_session)){
  // $url = 'http://165.232.183.220/zra/CRM/new_case_manual.php?mr=5&phone='.$phone.'&chatid='.$chat_session;
  // $window = "window.location.href='".$url."'";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?=$title?></title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?php echo base_url() ?>/assets/plugins/fontawesome-free/css/all.min.css">
  <!-- IonIcons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url() ?>/assets/dist/css/adminlte.min.css">
  <!-- Datetimepicker -->
  <link rel="stylesheet" href="<?php echo base_url() ?>/assets/dist/css/jquery.datetimepicker.min.css">   
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo base_url() ?>/assets/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo base_url() ?>/assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <link rel="icon" type="image/png" href="http://165.232.183.220/zra/CRM/images/zra.png"/>
    <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url() ?>/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo base_url() ?>/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="<?php echo base_url() ?>/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
  
  <!-- Bsmultiselect -->
  <!-- <link href="<?php echo base_url() ?>/assets/dist/css/BsMultiSelect.css" rel="stylesheet" type="text/css"> -->

  <!-- Tokenfield -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tokenfield/0.12.0/css/bootstrap-tokenfield.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">

  <!-- Custom css -->
  <?php $color_css='custom';?>
  <link rel="stylesheet" href="<?php echo site_url()?>/assets/dist/css/<?php echo $color_css;?>.css">



    <!-- Toastr -->
  <link rel="stylesheet" href="<?php echo base_url() ?>/assets/plugins/toastr/toastr.min.css">

  <style>
  table.dataTable.table-sm .sorting:before, table.dataTable.table-sm .sorting_asc:before, table.dataTable.table-sm .sorting_desc:before {
      display:none;
  }
  table.dataTable.table-sm .sorting:after, table.dataTable.table-sm .sorting_asc:after, table.dataTable.table-sm .sorting_desc:after {
      display: none;
  }

  /* Chrome, Safari, Edge, Opera */
  input::-webkit-outer-spin-button,
  input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  /* Firefox */
  input[type=number] {
    -moz-appearance: textfield;
  }


      .limi {
          color: darkgray;
          font-size: 14px;;
      }
      .error{
        color: red;
      }
      .navbar-light{
        background-color: #ECE9E6;
      }
      .card-info.card-outline {
    border-color: #1e3c72;
}
      .navbar-light .navbar-nav .nav-link {
        color: #fff;
     }
    .dropdown-item.active, .dropdown-item:active {
        color: #fff;
        text-decoration: none;
        background-color: coral;
    }
    @media (min-width: 768px){
    body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .content-wrapper, body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-footer, body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header {
    transition: margin-left .3s ease-in-out;
    margin-left: 0;
  }
}
.btn{
  color: #fff;
}

/*.content {
  min-height:73vh;
  }
*/
/*Vijay */


/* Tooltip container */
.tooltip {
  position: relative;
  display: inline-block;
  border-bottom: 1px dotted black; /* If you want dots under the hoverable text */
}

/* Tooltip text */
.tooltip .tooltiptext {
  visibility: hidden;
  width: 120px;
  background-color: black;
  color: #fff;
  text-align: center;
  padding: 5px 0;
  border-radius: 6px;
 
  /* Position the tooltip text - see examples below! */
  position: absolute;
  bottom: 100%;
  left: 50%;
  margin-left: -60px;
  z-index: 1;
}

.tooltip .tooltiptext::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: black transparent transparent transparent;
}

/* Show the tooltip text when you mouse over the tooltip container */
.tooltip:hover .tooltiptext {
  visibility: visible;
}


/*...Vijay!! */


/*Farhan : 11-06-2021 */
.card-info.card-outline-tabs>.card-header a.active {
    border-top: 3px solid coral;
}  

.interaction-chat {
    -webkit-transform: translate(0,0);
    transform: translate(0,0);
    height: 500px;
    padding: 10px;
    overflow: hidden;
}
.interaction-chat:hover{
  overflow: auto;
}

.para{
    font-size: 13px;
    line-height: 20px;
    opacity: 0.8;
    /* height: 29px; */
    overflow: hidden;
    letter-spacing: 0.3px;
}
.left-bg{
  background:#f5f5f5;
}
.right-bg{
  background: aliceblue;
}
.direct-chat-text{
  color: #566069;
    font-size: 13px;
    line-height: 21px;
    letter-spacing: 0.3px;
    outline: none;
    line-height: 2;
}
.direct-chat-img {
     border-radius: 0; 
    float: left;
    height: 25px;
    width: 25px;
}
.content-header {
    padding: 0px .5rem;
}
/* .main-footer {
    width: 100%;
    position: fixed;
    bottom: 0;
    right: 0px;
    z-index: 5000;
    display: block;
} */

/*Farhan : Close */

/* 23-06-2021 */
.l_c_h {
		width:300px;
		position:fixed;
		bottom:0;
		right:6px;
		background:#fff;
		border-radius:3px;
		z-index:5000;
						display:block;
					}
	.l_c_h .c_h {
		cursor:pointer;
		border-radius:0px;
				/*background:#c61432;*/
        /* background: #2dc3e8; */
        border-top:1px solid coral;
				line-height: 34px;

	}
	.l_c_h .left_c {
		color:#ebebeb;
		width:150px;
		font-size:16px;
		font-family:Arial, Helvetica, sans-serif;
	}
	.l_c_h .right_c {
		text-align:center;
		/*background:#DE4A4A;*/
    background:coral;
		line-height: 36px;
	}
	.l_c_h .right_c  a {
		color:#ebebeb;
		border-radius: 3px;
		box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05) !important;
		cursor: pointer !important;
		font-size: 16px;
		line-height: normal !important;
		margin-top: 0 !important;
		padding: 1px 0px !important;
		text-align: center !important;
		text-decoration:none;
		font-weight:600;
	}
	.clear {
		clear:both;
	}

	.left1{
		float:left;
	}
	.right1{
		float:right;
	}
	.left_icons{
		width:35px;
		height:auto;
		text-align:center;
		color:#999;
		/*background:#DE4A4A;*/
        background: #ff8f32;
		font-size:15px;
	}
	.left_icons a{
		color:#fff;
		font-weight:normal;
	}
	.center_icons{
		text-align:center;
    font-size:smaller;
    color:#222;
		padding:2px 0px 0px 5px;
	}
	.logout img{
		margin-top:8px;
	}

  .card-comments .comment-text {
    color: #78838e;
     margin-left: 0;
}

  .button-orange1 {
    background: #fff;
    border: #004b8b82 2px solid;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 12px;
    padding: 4px 10px;
    margin-right: 5px;
    cursor: pointer;
    float: left;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
}
  </style>

 
</head>

<body class="hold-transition sidebar-mini layout-fixed">
   <div class="wrapper">

  
  
 <!-- Navbar -->
 <nav class="main-header navbar navbar-expand-md navbar-light navbar-light" style="margin-left:0">
    <div class="container-fluid">

      <a href="<?php echo $base_url_new;?>/CRM/wap.php" class="navbar-brand" style="margin-top: -27px; margin-bottom: -27px;" >
        <img src="<?php echo base_url() ?>/assets/images/alliance_logo.png" alt="Ensembler Logo" class="brand-image" style="height: 56px;">
        
      </a>

  

      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
                  
          
        <?php if ($this->module_lib->hasActive('sms_send')): ?>
          <!-- <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">SMS</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
              <li><a href="<?php echo site_url('sms/send_sms');?>" class="dropdown-item">SMS Send</a></li>
              <li><a href="<?php echo site_url('smsbox/inbox');?>" class="dropdown-item">SMS inbox</a></li>
            </ul>
          </li> -->
        <?php endif; ?> 


        <?php if ($this->module_lib->hasActive('whatsapp_send')): ?>
          <?php if ( $this->rbac->hasPrivilege('whatapp_message', 'can_view')  && $this->rbac->hasPrivilege('whatapp_message', 'can_add') ): ?>
          <!-- <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Whatsapp</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
              <li><a href="<?php echo site_url('whatsapp/bot_request');?>" class="dropdown-item">Bot Session</a></li>
            </ul>
          </li> -->
        <?php endif; ?>
        <?php endif; ?>
        
        <?php if ($this->module_lib->hasActive('contact')): ?>
        <?php //if ( $this->rbac->hasPrivilege('contacts', 'can_view') ): ?>  
          <!-- <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Contact</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
              <li><a href="<?php echo site_url('contact');?>" class="dropdown-item">Contact</a></li>
            </ul>
          </li> -->
          <?php //endif; ?>
          <?php endif; ?>

        <?php if ($this->module_lib->hasActive('reports')): ?>
          <!-- <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Reports</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
              
              <?php if ( $this->rbac->hasPrivilege('basic-report', 'can_view') ): ?>
                <li><a href="<?php echo site_url('reports');?>" class="dropdown-item">Message Report</a></li>
              <?php endif; ?> 
              
              <?php if ( $this->rbac->hasPrivilege('whatsapp-report', 'can_view') ): ?>
                <li><a href="<?php echo site_url('reports/whatsapp_report');?>" class="dropdown-item">Whatsapp Report</a></li>
              <?php endif; ?> 
              
              <?php if ( $this->rbac->hasPrivilege('bulk-report', 'can_view') ): ?>
                <li><a href="<?php echo site_url('reports/bulk_message_report');?>" class="dropdown-item">Bulk Report</a></li>
              <?php endif; ?>

              <?php if ( $this->rbac->hasPrivilege('bad-contact-report', 'can_view') ): ?>
                <li><a href="<?php echo site_url('reports/bad_report');?>" class="dropdown-item">Bad contact Report</a></li>
              <?php endif; ?> 
              
              <?php if ( $this->rbac->hasPrivilege('customer-report', 'can_view') ): ?>
              <li><a href="<?php echo site_url('reports/customer_wise');?>" class="dropdown-item">Customer Wise</a></li>
              <?php endif; ?>
              
              <?php if ( $this->rbac->hasPrivilege('quota-report', 'can_view') ): ?>
              <li><a href="<?php echo site_url('reports/quota_report');?>" class="dropdown-item">Quota Report</a></li>
              <?php endif; ?>
              
              <?php if ( $this->rbac->hasPrivilege('queue-report', 'can_view') ): ?>
              <li><a href="<?php echo site_url('reports/queue_report');?>" class="dropdown-item">Queue Report</a></li>
              <?php endif; ?>
            </ul>
          </li> -->
        <?php endif; ?>

        <?php if ($this->module_lib->hasActive('user_management')): 
          if ( $this->rbac->hasPrivilege('add_user', 'can_view') ):

          ?>
          <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">User Management</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
              <?php if ( $this->rbac->hasPrivilege('add_user', 'can_add') ): ?>
              <li><a href="<?php echo site_url('user/add_user');?>" class="dropdown-item">Add User</a></li>
               <?php endif; ?>

              <?php if ( $this->rbac->hasPrivilege('add_user', 'can_view') ): ?>
              <li><a href="<?php echo site_url('user');?>" class="dropdown-item">Users</a></li>
               <?php endif; ?>
            </ul>
          </li>
          <?php endif; ?>

          <?php endif; ?>
        </ul>
      </div>

      

      <!-- Right navbar links -->
      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto" >
        <!-- Messages Dropdown Menu -->
        <!-- <li class="nav-item dropdown user-menu">
          <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
            <img src="<?php echo base_url() ?>assets/dist/img/user.png" class="user-image img-circle elevation-2" alt="User Image">
            <span class="d-none d-md-inline"><?php echo $this->session->userdata('admin')['username'];?> <br><small>Role - <?php echo key($this->session->userdata('admin')['roles']);?></small></span>
          </a>
        </li> -->
       <?php if(!empty($url)){ ?>
        <li>
          <!-- <a id="anchorID" href="<?php echo $url;?>" target="_blank" class="button-orange1">CREATE CASE</a></li>         -->
      <?php }?>
      <li class="nav-item dropdown user-menu">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle" style="padding: 0px;color: white;">
              <span style="color:#453629;"><?php echo $_SESSION['logged'];?></span>
              <img src="<?php echo base_url() ?>assets/dist/img/user.png" class="user-image img-circle elevation-2" alt="User Image" style="position: relative; margin: -18px 0px -18px 0px;">
              <!-- <span class="d-none d-md-inline"><?php echo $this->session->userdata('admin')['username'];?></span>  <span> <small ><?php echo $_SESSION['logged'];?></small></span> -->
              <!-- <?php if($this->session->userdata('admin')['role_id'] != '1'): ?> -->
              <br>

              <span style="position: relative; margin: 0px 0px 0px 35px;"> 
              <!-- <i class="far fa-comment fa-xs"></i>  : <?php //echo $this->user_model->get_quota_sms();?> &nbsp;  -->
              <!-- <i class="fab fa-whatsapp fa-xs"></i> : </span> -->
              <?php endif; ?>
            </a>
            
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow" style="width: 250px;">
               <?php if ($this->module_lib->hasActive('system_settings')): 
                  //if ( $this->rbac->hasPrivilege('add_user', 'can_view') ):
                ?>
              <!-- <li><a href="<?php echo site_url('smstemplate');?>" class="dropdown-item">SMS Template </a></li> -->
              <!-- <li><a href="<?php echo site_url('smsconfig')?>" class="dropdown-item">SMS Configuration </a></li> -->
              <!-- <li><a href="<?php echo site_url('login/user_release')?>" class="dropdown-item">Release Users </a></li>
              <li><a href="<?php echo site_url('setting/general_settings')?>" class="dropdown-item">Email Settings </a></li>
              <li><a href="<?php echo site_url('setting/setting_ini')?>" class="dropdown-item">ini Settings </a></li>
              <li><a href="<?php echo site_url('reports/settings_trace')?>" class="dropdown-item">Settings Audit </a></li> -->
               
              <?php //endif; ?>
              <?php endif; ?>

              <!-- <li><a href="<?php echo site_url('login/logout')?>" class="dropdown-item">Logout</a></li> -->
            </ul>
        </li>
            
      </ul>
    </div> <!-- /. container close -->
  </nav>
  <!-- /.navbar -->