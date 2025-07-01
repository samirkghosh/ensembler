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
  <link rel="icon" type="image/png" href="<?php echo base_url() ?>/assets/dist/img/bipa.png"/>
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
      .limi {
          color: darkgray;
          font-size: 14px;;
      }
      .error{
        color: red;
      }
      .navbar-light{
        background-color: coral;
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
/*Farhan : Close */

  
  </style>

 
</head>

<body class="hold-transition sidebar-mini layout-fixed">
   <div class="wrapper">

  
  
 <!-- Navbar -->
 <nav class="main-header navbar navbar-expand-md navbar-light navbar-light" style="margin-left:0">
    <div class="container-fluid">

      <a href="<?php echo site_url('dashboard')?>" class="navbar-brand" style="margin-top: -27px; margin-bottom: -27px;" >
        <img src="<?php echo base_url()?>assets/dist/img/bipa.jpg" alt="AdminLTE Logo" class="brand-image" style="width:50%;height: 56px;">
        <!-- <span class="brand-text font-weight-light">BIPA</span> -->
      </a>

  

      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
                  
          
        <?php if ($this->module_lib->hasActive('sms_send')): ?>
          <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">SMS</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
              <li><a href="<?php echo site_url('sms/send_sms');?>" class="dropdown-item">Send SMS</a></li>
              <!-- <li><a href="<?php echo site_url('sms/bulkuploadSMS');?>" class="dropdown-item">Bulk SMS Upload</a></li> -->
            </ul>
          </li>
        <?php endif; ?> 


        <?php if ($this->module_lib->hasActive('whatsapp_send')): ?>
          <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Whatsapp</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
              <li><a href="<?php echo site_url('whatsapp');?>" class="dropdown-item">Send Whatsapp</a></li>
            </ul>
          </li>
        <?php endif; ?>
        
        <?php if ($this->module_lib->hasActive('contact')): ?>  
          <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Contact</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
              <li><a href="<?php echo site_url('contact');?>" class="dropdown-item">Add Contact</a></li>
            </ul>
          </li>
          <?php endif; ?>

        <?php if ($this->module_lib->hasActive('reports')): 
          if ( $this->rbac->hasPrivilege('basic-report', 'can_view') ):
            ?>
          <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Reports</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
              <li><a href="<?php echo site_url('reports');?>" class="dropdown-item">Message Report</a></li>
              <li><a href="<?php echo site_url('reports/bulk_message_report');?>" class="dropdown-item">Bulk Report</a></li>
              <li><a href="<?php echo site_url('reports/customer_wise');?>" class="dropdown-item">Customer Wise</a></li>
              <li><a href="<?php echo site_url('reports/quota_report');?>" class="dropdown-item">User Quota</a></li>
            </ul>
          </li>
        <?php endif; ?><?php endif; ?>

        <?php if ($this->module_lib->hasActive('user_management')): 
          if ( $this->rbac->hasPrivilege('add_user', 'can_view') ):

          ?>
          <li class="nav-item dropdown">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">User Management</a>
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">

              <li><a href="<?php echo site_url('user/add_user');?>" class="dropdown-item">Add User</a></li>
              <li><a href="<?php echo site_url('user');?>" class="dropdown-item">Users</a></li>
            </ul>
          </li>
          <?php endif; ?>

          <?php endif; ?>
        </ul>
      </div>

      

      <!-- Right navbar links -->
      <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
        <!-- Messages Dropdown Menu -->
        <!-- <li class="nav-item dropdown user-menu">
          <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
            <img src="<?php echo base_url() ?>assets/dist/img/user.png" class="user-image img-circle elevation-2" alt="User Image">
            <span class="d-none d-md-inline"><?php echo $this->session->userdata('admin')['username'];?> <br><small>Role - <?php echo key($this->session->userdata('admin')['roles']);?></small></span>
          </a>
        </li> -->
        <li class="nav-item dropdown user-menu">
            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">
              <img src="<?php echo base_url() ?>assets/dist/img/user.png" class="user-image img-circle elevation-2" alt="User Image">
            <span class="d-none d-md-inline"><?php echo $this->session->userdata('admin')['username'];?></span>  <span> <small >(Role - <?php echo key($this->session->userdata('admin')['roles']);?>)</small></span>
            </a>
            
            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
               <?php if ($this->module_lib->hasActive('system_settings')): 
                  //if ( $this->rbac->hasPrivilege('add_user', 'can_view') ):
                ?>
              <li><a href="<?php echo site_url('smstemplate');?>" class="dropdown-item">SMS Template </a></li>
              <li><a href="<?php echo site_url('smsconfig')?>" class="dropdown-item">SMS Configuration </a></li>
              <li><a href="#" class="dropdown-item">Whatsapp Configuration </a></li>
              <li><a href="#" class="dropdown-item">General Settings </a></li>
              <?php //endif; ?>
              <?php endif; ?>

              <li><a href="<?php echo site_url('login/logout')?>" class="dropdown-item">Logout</a></li>
            </ul>
        </li>
            
      </ul>
    </div> <!-- /. container close -->
  </nav>
  <!-- /.navbar -->