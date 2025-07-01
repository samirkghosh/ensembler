<?php
/*
Customer page
Name -Ritu Modi
Date -27-02-24
Description- This file use for fetch data and insert data in database
*/ 
include("web_function.php");
include('Customer/web_consumer_function.php');
?>

<body>
<style>
        .form-wrapper {
            text-align: center;
        }
        .form-wrapper form {
            display: inline-block;
        }
        .input-style1::placeholder {
            color: black;
        }
        .button-search {
            padding: 2px;
            justify-content: center;
            align-items: center;
        }
        .search-form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .search-input {
            margin-bottom: 5px;
        }
    </style>
   <div class="wrapper">
        <div class="container-fluid">
            <div class="row" style="min-height:90vh;">
                <div class="col-sm-2" style="padding-left:0">
                  <?php include('includes/sidebar.php');?>
                </div>
                <?php $token = base64_decode($_GET['token']);
                    if($token == 'web_consumer_home'){?>
                <div class="col-sm-2">
                    <div class="breadcrumb_head mt-3" style="height:89px;margin-bottom:9px;">
                        <form action="" method="post" class="search-form">
                            <label>Search Customer :</label>
                            <?php
                                if(isset($_POST['search']) && $_POST['search']!='') { ?>
                                <input name="search" id="search" type="text" class="input-style1 search-input" value="<?=$_POST['search']?>" onfocus="clearText(this)" onblur="clearText(this)" style="/*border: #0e0e0e47 1px solid;*/color: #4a4a4a;min-height: 20px;/*padding: 0 3px 0 5px;*/font-size: 10px;width:180px;">
                            <?php }
                                else { ?>
                            <input name="search" id="search" type="text" class="input-style1 search-input" placeholder="Enter Name / Mobile / Email / FB / X" onfocus="clearText(this)" onblur="clearText(this)" style="/*border: #0e0e0e47 1px solid;*/color: #4a4a4a;min-height: 20px;/*padding: 0 3px 0 5px;*/font-size: 10px;width:180px;">
                            <?php } ?>
                            <input name="" type="submit" value="Go" class="button-search" style="padding: 2px;justify-content: center;align-items: center;">
                        </form>
                    </div>
                    <div class="recentitem-bar-panel">
                        <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Recent 5 Cases</span>
                        <?php  include("helpdesk/web_recent_complaints.php"); ?>
                    </div>
                </div>
            <?php }
            if($token == 'web_consumer_home'){
              $class = 'col-sm-8 mt-3';
            }else{
              $class = 'col-sm-10 mt-3';
            }?>
            <div class="<?php echo $class;?>" style="padding-left:0">
                <div class="rightpanels"> 
                    <!-- this code change our requirement -->
                    <?php
                        if($token == 'web_consumer_home'){
                            include_once("Customer/web_consumer_home.php");
                        }else if($token == 'web_customer_detail'){
                            include_once("Customer/web_customer_detail.php");
                        }else if($token == 'case_detail_backoffice'){
                            include_once("helpdesk/case_detail_backoffice.php");
                        }else{
                           // If none of the conditions match, redirect to logout page
                            echo "<script>window.location.href = '../web_logout.php';</script>";
                            exit; // Stop script execution
                        }
                    ?>
                    <!-- End -->
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <?php include("includes/web_footer.php"); ?>
    </div>
</div>
</body>
<script src="<?=$SiteURL?>public/js/customer.js"></script>
