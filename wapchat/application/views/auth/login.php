<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$title?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
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
    <link rel="stylesheet"
        href="<?php echo base_url() ?>/assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="icon" type="image/png" href="<?php echo base_url() ?>/assets/dist/img/bipa.png" />
    <!-- DataTables -->
    <link rel="stylesheet"
        href="<?php echo base_url() ?>/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet"
        href="<?php echo base_url() ?>/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet"
        href="<?php echo base_url() ?>/assets/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Custom css -->
    <?php $color_css='custom';?>
    <link rel="stylesheet" href="<?php echo site_url()?>/assets/dist/css/<?php echo $color_css;?>.css">



    <!-- Toastr -->
    <link rel="stylesheet" href="<?php echo base_url() ?>/assets/plugins/toastr/toastr.min.css">

    <style>
    .error {
        color: #fff;
        color: #fff;
        font-size: smaller;
    }

    .navbar-light {
        background-color: coral;
    }

    .navbar-light .navbar-nav .nav-link {
        color: #fff;
    }

    @media (min-width: 768px) {

        body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .content-wrapper,
        body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-footer,
        body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header {
            transition: margin-left .3s ease-in-out;
            margin-left: 0;
        }
    }

    .btn {
        border: 2px solid #2222;
    }
    </style>


</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">



        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-light navbar-light" style="margin-left:0">
            <div class="container-fluid">

                <a href="" class="navbar-brand">
                    <img src="<?php echo base_url()?>assets/dist/img/bipa.jpg" alt="Bipa logo" class="brand-image"
                        style="width:50%; height: 56px;">
                    <!-- <span class="brand-text font-weight-light">BIPA</span> -->
                </a>


                <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse"></div>

                <!-- Right navbar links -->
                <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                    <!-- Messages Dropdown Menu -->
                    <li class="nav-item dropdown user-menu">
                        <form action="<?php echo site_url('login')?>" method="post">
                            <?php  
                      if($this->session->flashdata('fail')){
                        echo "<span class='error'>".$this->session->flashdata('fail')."</span>";
                      }  
                      ?>
                            <div class="row">
                                <div class="col-5">
                                    <input type="text" class="form-control form-control-sm" name="email" placeholder="Enter User Email"
                                        value="<?php echo set_value('email'); ?>">
                                    <?php echo form_error('email');?>

                                </div>
                                <div class="col-5">
                                    <input type="password" class="form-control form-control-sm" name="password" placeholder="Password">
                                    <?php echo form_error('password'); ?>
                                </div>
                                <div class="col-2">
                                    <input type="submit" class="btn btn-sm">
                                </div>
                            </div>
                            <p class="mb-1">
                                <a href="<?php echo site_url('login/forgot_password')?>"
                                    style="color: #fff;font-weight:800">Forgot password</a>
                            </p>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="content">
            <div class="row">
                <table>
                    
                <tr> <td><div style="padding:10px 0px 0px 20px">SMS GateWay Status : <span id="indecator" class="brand-text">Connecting... &nbsp;&nbsp;&nbsp;</span> </div></td> </tr>
                <tr> <td><div style="padding:10px 0px 0px 20px">SMS Sender Status : <span id="sender_status" class="brand-text">Connecting... &nbsp;&nbsp;&nbsp;</span> &nbsp;<span id="sendreason"></span> </div></td> </tr>
                </table>
                
                
            </div>
            <img src="<?php echo base_url()?>assets/dist/img/login_bg.png" style="height:77vh">
        </div>

        <footer class="main-footer">
            <div class="row">

                <div class="col-sm-4">
                    <img src="<?php echo base_url()?>/assets/dist/img/alliance.png"
                        style="height: 46px;margin-top:-10px">
                </div>

                <div class="col-sm-8">
                    <strong>Copyright &copy; 2021-2022 <a href="#" class="link">Alliance Infotech Pvt Ltd</a>.</strong>
                    All rights reserved.
                </div>

            </div>
        </footer>


<script src="<?php echo base_url() ?>/assets/plugins/jquery/jquery.min.js"></script>
<script type="text/javascript">
    function is_active_gateway() {
      var url = '<?php echo site_url('setting/is_active_gateway')?>';
      $.ajax({
        url:url,
        data:false,
        // dataType:'JSON',
        method:'GET',
        processData:false,
        contentType:false,
        cache:false,
        success:function(data, textStatus, jqXHR){
          console.log('data');
          console.log(data);
          if(data=='true'){
            $("#indecator").html('YES').css('color', 'green').css('background-color', 'green');
          }
          else{
            $("#indecator").html('NO').css('color', 'red').css('background-color', 'red');
          }
        },
        error:function(jqXHR, textStatus, errorThrown){},
        complete:function(){},

      });
    }

    function is_active_sendstatus() {
      var url = '<?php echo site_url('setting/is_active_sendstatus')?>';
      $.ajax({
        url:url,
        data:false,
        dataType:'JSON',
        method:'GET',
        processData:false,
        contentType:false,
        cache:false,
        success:function(data, textStatus, jqXHR){
         
          if(data.i_sendstatus=='0'){
            $("#sender_status").html('YES').css('color', 'green').css('background-color', 'green');
            $("#sendreason").text(data.v_sendreason);
          }
          else{
            $("#sender_status").html('NO').css('color', 'red').css('background-color', 'red');
            $("#sendreason").text(data.v_sendreason);
          }
        },
        error:function(jqXHR, textStatus, errorThrown){},
        complete:function(){},

      });
    }

    var gatway_status = setInterval(function() {
      is_active_gateway();
      is_active_sendstatus();
      console.log('call funcation is_active_gateway');
    }, 3000);
</script>        
</body>

</html>