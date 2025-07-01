<?php 
/***
 * Status Update Page
 * Author: Aarti Ojha
 * Date: 07-11-2024
 * Description: This file handles change theme color, images one click
 */


   define('IN_SITE', true);
   // Include the database connection file
   include("web_mysqlconnect.php");

   $allowed_image_extension = array(
    "png",
    "jpg",
    "jpeg"
);
    if(isset($_POST['head']) && !empty($_FILES["headlogo"]["name"])) {

           
            // Get image file extension
            $file_extension = pathinfo($_FILES["headlogo"]["name"], PATHINFO_EXTENSION);

            // Validate file input to check if is not empty
            if (! file_exists($_FILES["headlogo"]["tmp_name"])) {
                $response = array(
                    "type" => "error",
                    "message" => "Choose image file to upload."
                );
            }    // Validate file input to check if is with valid extension
            else if (! in_array($file_extension, $allowed_image_extension)) {
                $response = array(
                    "type" => "error",
                    "message" => "Upload valiid images. Only PNG and JPEG are allowed."
                );
                echo $result;
            }    // Validate image file size
            else if (($_FILES["headlogo"]["size"] > 2000000)) {
                $response = array(
                    "type" => "error",
                    "message" => "Image size exceeds 2MB"
                );
            }    
             else {
                $fullname = basename($_FILES["headlogo"]["name"]);
                $target = "images/".$fullname;
              
                if (move_uploaded_file($_FILES["headlogo"]["tmp_name"], $target)) {

                    // upload query
                    $headquery = "UPDATE $db.crm_setting SET header_logo='$fullname' WHERE status_theme='1'";
                    mysqli_query($link,$headquery);

                    $response = array(
                        "type" => "success",
                        "message" => "Image uploaded successfully."
                    );
                } else {
                    $response = array(
                        "type" => "error",
                        "message" => "Problem in uploading image files."
                    );
                }
            }


    }

    if(isset($_POST['foot'])&& !empty($_FILES["footlogo"]["name"])) {


        // Get image file extension
        $file_extension = pathinfo($_FILES["footlogo"]["name"], PATHINFO_EXTENSION);

        // Validate file input to check if is not empty
        if (! file_exists($_FILES["footlogo"]["tmp_name"])) {
            $response = array(
                "type" => "error",
                "message" => "Choose image file to upload."
            );
        }    // Validate file input to check if is with valid extension
        else if (! in_array($file_extension, $allowed_image_extension)) {
            $response = array(
                "type" => "error",
                "message" => "Upload valiid images. Only PNG and JPEG are allowed."
            );
            echo $result;
        }    // Validate image file size
        else if (($_FILES["footlogo"]["size"] > 2000000)) {
            $response = array(
                "type" => "error",
                "message" => "Image size exceeds 2MB"
            );
        }    
        else {
            $fullname = basename($_FILES["footlogo"]["name"]);
            $target = "images/".$fullname;
            
            if (move_uploaded_file($_FILES["footlogo"]["tmp_name"], $target)) {

                // upload query
                $headquery = "UPDATE $db.crm_setting SET footer_logo='$fullname' WHERE status_theme='1'";
                mysqli_query($link,$headquery);

                $response = array(
                    "type" => "success",
                    "message" => "Image uploaded successfully."
                );
            } else {
                $response = array(
                    "type" => "error",
                    "message" => "Problem in uploading image files."
                );
            }
        }

    }

    if(isset($_POST['landing'])&& !empty($_FILES["landlogo"]["name"])) {
    
          // Get image file extension
          $file_extension = pathinfo($_FILES["landlogo"]["name"], PATHINFO_EXTENSION);

          // Validate file input to check if is not empty
          if (! file_exists($_FILES["landlogo"]["tmp_name"])) {
              $response = array(
                  "type" => "error",
                  "message" => "Choose image file to upload."
              );
          }    // Validate file input to check if is with valid extension
          else if (! in_array($file_extension, $allowed_image_extension)) {
              $response = array(
                  "type" => "error",
                  "message" => "Upload valid images. Only PNG and JPEG are allowed."
              );
              echo $result;
          }    // Validate image file size
          else if (($_FILES["landlogo"]["size"] > 2000000)) {
              $response = array(
                  "type" => "error",
                  "message" => "Image size exceeds 2MB"
              );
          }    
          else {
              $fullname = basename($_FILES["landlogo"]["name"]);
              $target = "../images/".$fullname;
              
              if (move_uploaded_file($_FILES["landlogo"]["tmp_name"], $target)) {
  
                  // upload query
                  $headquery = "UPDATE $db.crm_setting SET landing_logo='$fullname' WHERE status_theme='1'";
                  mysqli_query($link,$headquery);
  
                  $response = array(
                      "type" => "success",
                      "message" => "Image uploaded successfully."
                  );
              } else {
                  $response = array(
                      "type" => "error",
                      "message" => "Problem in uploading image files."
                  );
              }
          }
  
       
    }

   $selecttab=6;
   include("header.php");
   ?>
<style>
    .response {
    padding: 10px;
    margin-top: 10px;
    border-radius: 2px;
}



</style>

<body>

    <div class="wrapper">


        <div class="container-fluid">

            <div class="row" style="min-height:90vh">

                <div class="col-sm-2" style="padding-left:0">
                    <?include("sidebar.php");?>
                </div>

                <div class="col-sm-10 mt-3" style="padding-left:0">
                
                    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Change Theme</span>
                   
                    <div class="style2-table" style="border: #d4d4d4 1px solid;"> 
                    <!-- Change theme Start-->
                        <!-- <form class="row g-3 p-2" action="<?=$_SERVER['PHP_SELF']; ?>" method="post">
                            <div class="col-auto">
                                <?php
                                $query_theme = "Select * from $db.theme_colors where status='1'";
                                $result_query = mysqli_query($link,$query_theme);
                                $num_rows = mysqli_num_rows($result_query);
                                ?>
                                    <select name="theme" id="theme" class="form-control" style="width: 225px;" required>
                                        <option value="">Select Theme</option>
                                    <?php if($num_rows > 0) :?>
                                        <?php while ($theme = mysqli_fetch_assoc($result_query)):
                                            if($theme['theme']== $_POST['theme']):
                                                $sel = 'selected';
                                            else:
                                                $sel='';
                                            endif;
                                            ?>
                                           <option value="<?=$theme['theme']?>" <?=$sel?>><?=$theme['theme']?></option>
                                        <?php endwhile;?>
                                    <?php endif;?>
                                    </select>
                            </div>
                            <div class="col-auto">
                                <input type="submit" class="button-orange1" style="padding:0.6em" name="submit" value="Apply theme">
                            </div>
                        </form>
                        <hr>-->

                    <!-- End -->

                    <!-- Upload header logo start  -->
                        <form class="row g-3 p-2" action="<?=$_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">

                            <div class="col-auto" style="margin-right: 11px;">
                                <h6>Header Logo</h6>
                            </div>
                            <div class="col-auto">
                               <input type="file" name="headlogo" class="form-control">
                            </div>

                            <div class="col-auto">
                                <input type="submit" class="button-orange1" style="padding:0.6em" name="head" value="Upload">
                            </div>
                           
                        </form>
                    <!-- END -->

                    <!-- Upload footer logo start  -->
                        <form class="row g-3 p-2" action="<?=$_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">

                            <div class="col-auto" style="margin-right: 15px;">
                                <h6>Footer Logo</h6>
                            </div>
                            <div class="col-auto">
                            <input type="file" name="footlogo" class="form-control">
                            </div>

                            <div class="col-auto">
                                <input type="submit" class="button-orange1" style="padding:0.6em" name="foot" value="Upload">
                            </div>

                        </form>
                    <!-- END -->

                    <!-- Upload landing image start  -->
                        <form class="row g-3 p-2" action="<?=$_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">

                            <div class="col-auto">
                                <h6>Landing image</h6>
                            </div>
                            <div class="col-auto">
                            <input type="file" name="landlogo" class="form-control">
                            </div>

                            <div class="col-auto">
                                <input type="submit" class="button-orange1" style="padding:0.6em" name="landing" value="Upload">
                            </div>

                        </form>
                    <!-- END -->
                    </div>
                    <!-- End Right panel -->

                <?php if(!empty($response)) { ?>
                <div class="response <?php echo $response["type"]; ?>"><?php echo $response["message"]; ?></div>
                <?php }?>

                </div>

            </div>

        </div>

        <div class="footer">
            <? include("web_footer.php"); ?>
        </div>

    </div>


</body>

</html>