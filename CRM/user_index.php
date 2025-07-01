<?php
/***
 * User Module Page
 * Author: Aarti Ojha
 * Date: 13-03-2024
 * Description: This file is for creating, editing, and deleting user details.
 **/
include("web_function.php"); // Includes some common functions
include("User/web_user_function.php"); // Includes insert, edit, delete user functions

/*********** This code for Licence Modules ***************/ 
function View_licence_info(){
    global $link, $db;
    $sql="SELECT DisplayName, UserLicence,user_type, atxGid FROM $db.unigroupid WHERE atxGid != '0000' AND status ='1' ORDER BY DisplayName ";
    $res= mysqli_query($link, $sql);
    return $res;
}
// This code for check which type of licence and display used licence
function F_Count_User_Licence($companyid,$db,$usertype,$licence_type){
   global $link,$dbname,$licence_Concurrent,$db_asterisk;
   if($licence_type == $licence_Concurrent && $usertype == 'Agent'){
        $sql_user_ID = "SELECT count(*) as total FROM $db_asterisk.`autodial_live_agents`";
        $Fetch_USERID=mysqli_query($link,$sql_user_ID);
        $row=mysqli_fetch_array($Fetch_USERID);
        return $USERID=$row['total'];
   }else{
        $sql_user_ID = "SELECT count(u.AtxDesignation) as ID FROM  $dbname.tbl_mst_user_company as tmuc, $db.uniuserprofile as u where 
        tmuc.V_EmailID = u.AtxEmail AND u.AtxDesignation = '$usertype' AND tmuc.I_CompanyID='$companyid' AND u.AtxUserStatus = '1' ";
        $Fetch_USERID=mysqli_query($link,$sql_user_ID);
        $row=mysqli_fetch_array($Fetch_USERID);
        return $USERID=$row['ID'];
   }
    
}
?>
<!-- Fixed code, no need to change -->
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row" style="min-height:90vh">
                <div class="col-sm-2" style="padding-left:0">
                    <?php include("includes/sidebar.php"); ?> <!-- Include side menu file -->
                </div>
                <?php 
                $token = base64_decode($_GET['token']);
                if($token == 'web_userhome'){?>
                    <div class="col-sm-2">
                        <div class="breadcrumb_head mt-3" style="height:81px;margin-bottom:9px;">
                            <form action="user_index.php?token=<?php echo $web_userhome;?>&go=1" method="post" name="searchfrm" id="searchfrm">
                                <label>Search Users :</label>
                                <input name="search" type="text" class="input-style1" id="txtSearch" placeholder="Enter User Name " style="/*border: #0e0e0e47 1px solid;*/color: #4a4a4a;min-height: 26px;/*padding: 0 3px 0 5px;*/font-size: 10px;"/>
                                <input type="button" value="Go" class="button-search button_test_serach" style="padding: 2px;"/>
                            </form>
                        </div>
                        <div class="recentitem-bar-panel">
                            <!-- as per requirement added this code [Aarti][03-01-2025] -->
                            <span class="breadcrumb_head" style="height:37px;padding:9px 16px">License Users </span>
                             <!-- User Table -->
                            <table class="tableview tableview-2">
                                <tbody>
                                    <tr class="background">
                                        <td>User Type</td>
                                        <td colspan="1">License</td>
                                        <td>Used</td>
                                    </tr>
                                    <?php
                                    // Fetch license information
                                    $rs = View_licence_info();
                                    // Loop through fetched data
                                    while ($row = mysqli_fetch_array($rs)) {
                                        $user_group = $row['atxGid'];
                                        // Display user information
                                        echo '<tr> 
                                            <td>' . $row["DisplayName"] . '</td> 
                                            <td colspan="1">' . $row["UserLicence"] . '</td>';
                                        echo    '<td>' . F_Count_User_Licence($companyid, $db, $row['DisplayName'],$row["user_type"]) . '</td> 
                                            </tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <!-- DIV 1 END -->
                            <!-- as per requirement commented this code [Aarti][03-01-2025] -->
                            <?php 
                            // include_once("helpdesk/web_recent_complaints.php"); 
                            ?>
                        </div>
                    </div>
                    <?php 
                    $style='col-sm-8 mt-3';
                } else {
                    $style='col-sm-9 mt-3';
                }?>
                <div class="<?php echo $style;?>" style="padding-left:0">
                    <div class="rightpanels"> 
                        <!-- Dynamic inclusion based on token -->
                        <?php                        
                          if($token == 'web_userhome'){
                            include_once("User/web_userhome.php"); // Display user listing page
                          } else if($token == 'web_usercreate'){
                            include_once("User/web_usercreate.php"); // Display user create form page
                          } else if($token == 'web_userdetailview'){
                            include_once("User/web_userdetailview.php"); // Display user details page
                          }else if($token == 'case_detail_backoffice'){
                            include_once("helpdesk/case_detail_backoffice.php");
                          }else{
                               // If none of the conditions match, redirect to logout page
                                echo "<script>window.location.href = '../web_logout.php';</script>";
                                exit; // Stop script execution
                          }
                        ?>
                        <!-- End dynamic inclusion -->
                    </div>
                </div>
            </div>
        </div>
        <div class="footer"> 
          <? include("includes/web_footer.php"); ?> <!-- Includes web footer -->
        </div>
    </div>
</body>
<script language="javascript" src="<?=$SiteURL?>/public/js/user_script.js"></script> <!-- Includes user_script.js -->
<!-- End -->
