<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bipa || Change Password</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="<?php echo base_url()?>assets/dist/css/custom.css">
    <link rel="icon" type="image/png" href="<?php echo base_url() ?>/assets/dist/img/bipa.png" />
    <!-- Toastr -->
    <link rel="stylesheet" href="<?php echo base_url() ?>/assets/plugins/toastr/toastr.min.css">
    <style>
    .error {
        color: #ed143dd1;
    }

    .lockscreen-wrapper {
        margin: 0 auto;
        margin-top: 3%;
        /* max-width: 534px; */
        min-height: 72vh;
    }
    </style>
</head>

<body class="bg-white">
    <nav class="main-header navbar navbar-expand-md navbar-light navbar-light" style="margin-left:0">
        <div class="container-fluid">

            <a href="" class="navbar-brand">
                <img src="<?php echo base_url()?>assets/dist/img/bipa.jpg" alt="no Logo" class="brand-image"
                    style="width:50%;height: 56px;">
                <!-- <span class="brand-text font-weight-light">BIPA</span> -->
            </a>
    </nav>

    <div class="container">

        <div class="lockscreen-wrapper">
            <div class="lockscreen-logo">
                <span style="font-weight:400;color:#8fa0b1;">Change Password</span>
            </div>


            <form id="quickForm" method="post">
                <div class="card-body">

                    <div class="row mb-4">
                        <div class="col-sm-10">
                            <input type="password" name="oldpassword" class="form-control" id="oldpassword"
                                placeholder="Enter Old Password" autocomplete="off">
                        </div>
                        <div class="col-sm-2" style="margin-left: inherit;">
                            <div class="input-group-append" style="height: 38px;">
                                <span class="input-group-text" id="oldpassword_eye"><i onclick="show_oldpassword()"
                                        class="fas fa-eye-slash"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-10">
                            <input type="password" name="newpassword" class="form-control" id="newpassword"
                                placeholder="Enter New Password" autocomplete="off">
                        </div>
                        <div class="col-sm-2" style="margin-left: inherit;">
                            <div class="input-group-append" style="height: 38px;">
                                <span class="input-group-text" id="newpassword_eye"><i onclick="show_newpassword()"
                                        class="fas fa-eye-slash"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-10">
                            <input type="password" name="confirmpassword" class="form-control" id="confirmpassword"
                                placeholder="Confirm Password" autocomplete="off">
                        </div>
                        <div class="col-sm-2" style="margin-left: inherit;">
                            <div class="input-group-append" style="height: 38px;">
                                <span class="input-group-text" id="c_password_eye"><i onclick="show_confirmpassword()"
                                        class="fas fa-eye-slash"></i></span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group text-center">
                        <button type="submit" id="update_password" class="btn">Update</button>
                    </div>
                </div>

            </form>

            <div class="text-center">
                <a href="<?php echo site_url('login')?>" class="link">Or Log in as a different user</a>
            </div>
        </div>

    </div>

    <footer class="main-footer">
        <div class="row">

            <div class="col-sm-4">
                <img src="<?php echo base_url()?>/assets/dist/img/alliance.png" style="height: 46px;margin-top:-10px">
            </div>

            <div class="col-sm-8">
                <strong>Copyright &copy; 2021-2022 <a href="#" class="link">Alliance Infotech Pvt Ltd</a>.</strong>
                All rights reserved.
            </div>

        </div>

    </footer>


    <!-- jQuery -->
    <script src="<?php echo base_url() ?>assets/plugins/jquery/jquery.min.js"></script>


    <!-- jquery-validation -->
    <script src="<?php echo base_url() ?>assets/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="<?php echo base_url() ?>assets/plugins/jquery-validation/additional-methods.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo base_url() ?>assets/dist/js/adminlte.min.js"></script>
    <!-- <script src="<?php echo base_url() ?>assets/js/sstoast.js"></script> -->

    <!-- Toastr -->
    <script src="<?php echo base_url()?>/assets/plugins/toastr/toastr.min.js"></script>


    <script>
    if ($("#quickForm").length > 0) {
        $("#quickForm").validate({

            rules: {
                oldpassword: {
                    required: true,
                },
                newpassword: {
                    required: true,
                    minlength: 5,
                    maxlength: 12,
                },
                confirmpassword: {
                    required: true,
                    minlength: 5,
                    maxlength: 12,
                    equalTo: "#newpassword"
                },
            },
            messages: {

                oldpassword: {
                    required: "Please enter old password",
                },
                newpassword: {
                    required: "Please enter New password",
                    minlength: "Length Should be min 5 characters long.",
                    maxlength: "Length Should be max 12 characters long."
                },
                confirmpassword: {
                    required: "Please enter Confirm password",
                    equalTo: "Password must be matched"
                },

            },
            submitHandler: function(form) {
                // $('#update_password').html('Updating..');
                var img_path = "<?php echo base_url() . '/assets/images/loading.gif' ?>";
                $("[class$='_error']").html("");
                $(".custom_loader").html('<img src="' + img_path + '">');

                $.ajax({
                    url: "<?php echo base_url('changepassword/change_pass') ?>",
                    type: "POST",
                    data: $('#quickForm').serialize(),
                    dataType: "json",
                    success: function(data, textStatus, jqXHR) {
                        console.log(data);
                        console.log(data.success);
                        if (data.success === true) {
                            toastr.success(data.msg);
                            setTimeout(function() {
                                location.replace("<?php echo site_url('login')?>");
                            }, 2000);
                        } else {
                            toastr.error(data.msg);
                        }


                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $(".custom_loader").html("");
                    }
                });
            }
        })
    }

    function show_oldpassword() {
        var x = document.getElementById("oldpassword");
        if (x.type === "password") {
            x.type = "text";
            $("#oldpassword_eye").html('<i onclick="show_oldpassword()" class="fas fa-eye"></i></span>');
        } else {
            x.type = "password";
            $("#oldpassword_eye").html('<i onclick="show_oldpassword()" class="fas fa-eye-slash"></i></span>');
        }
    }

    function show_newpassword() {
        var x = document.getElementById("newpassword");
        if (x.type === "password") {
            x.type = "text";
            $("#newpassword_eye").html('<i onclick="show_newpassword()" class="fas fa-eye"></i></span>');
        } else {
            x.type = "password";
            $("#newpassword_eye").html('<i onclick="show_newpassword()" class="fas fa-eye-slash"></i></span>');
        }
    }

    function show_confirmpassword() {
        var x = document.getElementById("confirmpassword");
        if (x.type === "password") {
            x.type = "text";
            $("#c_password_eye").html('<i onclick="show_confirmpassword()" class="fas fa-eye"></i></span>');
        } else {
            x.type = "password";
            $("#c_password_eye").html('<i onclick="show_confirmpassword()" class="fas fa-eye-slash"></i></span>');
        }
    }
    </script>


</body>

</html>