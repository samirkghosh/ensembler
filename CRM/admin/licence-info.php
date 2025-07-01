<?php
/***
Auth: Aarti ojha
Description: This page displays user, company, support, and channel license information, with dynamic fetching of user lists based on selected channel types using PHP and AJAX.
*/

// [Aarti][15-04-2024] - For All Channel license module code 
function Channel_license_count($channel_type){
   global $db,$link;
   $sql= $link->query("SELECT count(*) as channel_count FROM $db.user_channel_assignment WHERE channel_type='$channel_type'");
   $row = $sql->fetch_assoc();
   $channel_count = $row['channel_count'];
   return $channel_count;
}
function get_licence_company_detail(){
    global $db_CampaignTracker, $link;
    $company_id = $_SESSION['companyid'];
    $sql="SELECT * FROM $db_CampaignTracker.companies where company_id= '$company_id'";
    $rs = mysqli_query($link, $sql);
    return $rs;
}
?>
<!-- Header -->
<span class="breadcrumb_head" style="height:37px;padding:9px 16px">License Information</span>
<!-- Main content -->
<div class="style2-table" style="border: #d4d4d4 1px solid;">
    <div class="row mt-3">
        <!-- Column 1 -->
        <div class="col-sm-5 card mx-4">
            <!-- DIV 1 START -->
            <!-- User Information Section -->
            <div class="row">
                <div class="col-sm-7">
                    <!-- User Icon -->
                    <span><img src="../public/images/contact_cards-128.png" width="64" alt=""></span>
                </div>
                <div class="col-sm-5 pt-3">
                    <!-- Title -->
                    <span class="h5">Users</span>
                </div>
            </div>
            <!-- User Table -->
            <table class="tableview tableview-2">
                <tbody>
                    <tr class="background">
                        <td>User Type</td>
                        <td colspan="2">License</td>
                        <td>License Type</td>
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
                            <td colspan="2">' . $row["UserLicence"] . '</td> 
                            <td>NAMED-LICENCE</td>';
                            // <td>' . $row["user_type"] . '</td> 
                        echo    '<td>' . F_Count_User_Licence($companyid, $db, $row['DisplayName'],$row["user_type"]) . '</td> 
                            </tr>';
                    }
                     $sql2="SELECT V_CompanyName, V_CompanyAddress, D_DateofRegistration, Website, V_PhoneNo FROM $db.tbl_mst_company";
                      $rs2=mysqli_query($link,$sql2) or die(mysqli_error());
                      $row2=mysqli_fetch_array($rs2);
                     echo '<tr><td>License Ver.</td><td colspan="2">7.0</td><td></td><td></td></tr>';
                      echo '<tr><td>License Type</td><td colspan="2">Perpetual</td><td></td><td></td></tr>';
                      
                      echo '<tr><td>Date of Subscription</td><td colspan="2">'.$row2['D_DateofRegistration'].'</td><td></td><td></td></tr>';
                      echo '<tr><td>Server License</td><td colspan="2">UC-20007512</td><td><td></td></td></tr>';
                    ?>
                </tbody>
            </table>
            <!-- DIV 1 END -->
        </div>
        <!-- Column 2 -->
        <div class="col-sm-3 card mx-1">
            <!-- DIV 2 START -->
            <!-- Company Information Section -->
            <div class="row">
                <div class="col-sm-4">
                    <!-- Company Icon -->
                    <span><img src="../public/images/company-info.png" width="64" alt=""></span>
                </div>
                <div class="col-sm-8 pt-3">
                    <!-- Title -->
                    <span class="h5">Company Info</span>
                </div>
            </div>
            <!-- Company Details -->
            <div class="card card px-3 py-3" style="background:#f5f5f5">
                <?php
                // Fetch company details
                $rs2 = get_licence_company_detail();
                $row2 = mysqli_fetch_array($rs2);
                // Display company information
                echo "<p style='text-align:left;'><strong>Company Name : </strong>" . $row2['company_name'] . "<br>";
                 echo "<strong>Company Id : </strong> " . $row2['company_id'] . "<br>";
                echo "<strong>Company Address : </strong> ". $row2['company_address'] . "<br>";
                echo "<strong>Date of Subscription : </strong>" . $row2['date_of_registration'] . "<br>";
                echo "<strong>Contact Voice : </strong>" . $row2['company_phoneno'] . "<br>";
                echo "<a class='text-white' href='" . $row2['Website'] . "' target='_blank'>" . $row2['Website'] . "</a></p>";
                ?>
            </div>
            <!-- DIV 2 END -->
        </div>
        <!-- Column 3 -->
        <div class="col-sm-3 card mx-1">
            <!-- DIV 3 START -->
            <!-- Support Section -->
            <div class="row ">
                <div class="col-sm-4">
                    <!-- Support Icon -->
                    <span><img src="../public/images/support_headphones_ask-128.png" width="64" alt=""></span>
                </div>
                <div class="col-sm-8 pt-3">
                    <!-- Title -->
                    <span class="h5">Support</span>
                </div>
            </div>
            <!-- Support Details -->
            <div class="card card px-2 py-2" style="background:#f5f5f5">
                <span style='font-size:12px;font-weight:700'>ALLIANCE INFOTECH PVT LTD</span>
                <p style="text-align:left;">
                    A-115, First Floor, Lajpat Nagar, New Delhi-110024, INDIA<br>
                    <strong>Contact Voice :</strong> +011 4051 7700<br>
                    <a class='text-dark' href="mailto:helpdeskzambia@groupmfi.com">info@alliance-infotech.com</a>
                </p>
            </div>
            <!-- DIV 3 END -->
        </div>
        <div class="col-sm-5 card mx-4">
            <!-- DIV 1 START -->
            <div class="row">
                <div class="col-sm-7">
                    <span><img src="../public/images/contact_cards-128.png" width="64" alt=""></span>
                </div>
                  <div class="col-sm-5 pt-3">
                  <span class="h5">Channel License</span>
                </div>
            </div>
            <table class="tableview tableview-2">                                 
                <tbody>
                    <tr class="background">
                    <td>Channel Type</td>
                    <td colspan="2">License</td>
                    <td>Used</td>
                    <td>Available</td>
                    </tr><?php $sql="SELECT * FROM $db.channel_license";
                    $rs=mysqli_query($link,$sql);
                    while($row=mysqli_fetch_array($rs)){
                        $avail = $row["count"] - Channel_license_count($row['name']);
                        echo '<tr> 
                        <td><a href="javascript:void(0)" class="channel_fetch" data-name="'. $row["name"].'">'. $row["name"].'</a></td> <td colspan="2">'.$row["count"].'</td> 
                        <td>'.Channel_license_count($row['name']).'</td><td>'.$avail.'</td> 
                        </tr>';
                    }
                    ?>
                </tbody>
            </table>
            <!-- DIV 1 END -->
        </div>
        <div class="col-sm-5 card mx-4" id="user_display" style="display: none;">
            <!-- DIV 1 START -->
            <div class="row">
                <div class="col-sm-7">
                    <span><img src="../public/images/contact_cards-128.png" width="64" alt=""></span>
                </div>
                  <div class="col-sm-5 pt-3">
                  <span class="h5">User List</span>
                </div>
            </div>
            <table class="tableview tableview-2">                                 
                <tbody id="channel_view"> 
                </tbody>
            </table>
            <!-- DIV 1 END -->
        </div>
    </div>
</div>
<script language="JavaScript" type="text/javascript">
    $('.channel_fetch').on('click', function(e){
        var select_menu = $(this).data('name');
        console.log(select_menu);
        $('#user_display').show();
        if(select_menu){
            $.ajax({
              type: "POST",
              url: 'common_function.php',
              data: { select_menu : select_menu,action:'license_list_channel'},
              success: function(data) {
                $('#channel_view').html(data);
              },
              error: function() {
                alert('somethink went wrong!');
            }
        });
    }
    });
</script>