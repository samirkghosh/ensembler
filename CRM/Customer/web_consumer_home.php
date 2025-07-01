<?php
/**
 * Customer List Page
 * Author: Ritu Modi
 * Date: 19-02-2024
 * 
 * This page is used to display a list of customers with their basic information such as name, contact number, email, location, and priority status. It retrieves the customer data from the database and dynamically generates a table to present it to the user. Priority customers are highlighted with a different color for easy identification. Each customer entry in the table is linked to a detailed view page where more information about the customer can be accessed and edited.
 */
// Retrieve user session data
    $vuserid = $_SESSION['userid'];
    $name = $_SESSION['logged'];
    // Encode the table name for security
    $web_customer_detail = base64_encode('web_customer_detail');
    $ref1 = $web_customer_detail."&CustomerID=?";

?>
<div class="rightpanels mt-3">
    <form name="frmcustomer" method="post">
        <!-- Table for displaying customer information -->
        <div class="style2-table">
            <!-- Header section -->
            <span class="breadcrumb_head" style="height:37px;padding:9px 16px;">
                <div class="row">
                    <!-- added link for inserting new customer withooout creating case [vastvikta [02-04-2025]] -->
                    <div class="col-sm-6">Customers <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 600 600">
                        <a href="customer_index.php?token=<?php echo $ref1;?>" >
                            <path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304l91.4 0C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7L29.7 512C13.3 512 0 498.7 0 482.3zM504 312l0-64-64 0c-13.3 0-24-10.7-24-24s10.7-24 24-24l64 0 0-64c0-13.3 10.7-24 24-24s24 10.7 24 24l0 64 64 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-64 0 0 64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/>
                        </a>
                        </svg>
                    </div>
                    <div class="col-sm-5">
                        <div class="row">
                            <div class="col-sm-1"><div style="background:#CFB53B;width:20px;height:20px;border-radius:5px"></div></div>
                            <div class="col-sm-5" style="font-size: 10px;"><b>Priority Customer</b></div>
                            <div class="col-sm-1"><div style="background:#fff;width:20px;height:20px;border:1px solid #999999;border-radius:5px"></div></div>
                           <div class="col-sm-5" style="font-size: 10px;"><b>Non Priority Customer</b></div>
                        </div>
                    </div>
                </div>
            </span>
            <div class="table">
                <div class="wrapper2">
                <div >
                    <table width="100%" class="tableview tableview-2">
                        <tbody>
                            <tr class="background">
                            <td width="10%" align="center" style="padding:10px;">Customer Id</td>
                            <td width="23%" align="left">Full Name</td>
                            <td width="17%" align="center">Contact Number</td>
                            <td width="20%" align="center">County</td>
                            <td width="20%" align="center">Sub County</td>
                            <td width="18%" align="center">Email</td>
                            <td width="18%" align="center">Priority Customers/ Non Priority Customers</td>
                            </tr>
                            <?php
                            // Query to retrieve customer list
                            $customer_query = view_customer_list();
                            while($res = mysqli_fetch_array($customer_query)){
                                // Extracting relevant customer information
                                $id = $res['AccountNumber'];
                                $name = $res['fname'];
                                $phone = $res['phone'];
                                $district =city($res['district']);
                                $Villages =village($res['v_Village']);
                                $email = $res['email'];
                                $address_1=$res['address'];
                                $address_2=$res['v_Location'];
                                $priority = ($res['priority_user']=='1') ? 'Priority' : 'Non Priority';
                                $web_customer_detail = base64_encode('web_customer_detail');
                                $ref = $web_customer_detail."&CustomerID=".base64_encode($id);

                                $no++;
                             // Applying different styles based on priority

                             if($res['priority_user']=='1')
                             {
                                $color = "background:#CFB53B;color:#fff";
                                $a_color = "color:#fff";
                             }else
                             {
                                $color="";
                                $a_color = "";
                             }
                             ?>
                        <tr style="<?=$color?>">  
                            <td align="center"><a href="customer_index.php?token=<?php echo $ref;?>" style="<?=$a_color?>"><?=$id?></a></td>
                            <td align="left"><?=$name?></td>
                            <td align="center"><?=$phone?></td>
                            <!-- <td align="center"><?=$res['tpin']?></td> -->
                            <td align="center"><?=$district?></td>
                            <!-- <td align="center"><?=$twitterhandle?></td>
                            <td align="center"><?=$fbhandle?></td> -->
                            <td align="center"><?=$Villages?></td>
                            <td align="center"><?=$email?></td>
                            <td align="center"><?=$priority?></td>
                        </tr>
                        <?
                        }
                        $numrows = mysqli_num_rows($customer_query);// Check if no records found
                        
                        if ($numrows == '0') {   ?>
                           <tr>
                              <td align="center" class="select" colspan="8">No Records Found</td>
                           </tr>
                        <?php } ?>
                     </tbody>
                     <!-- code for showing total number of records [vastvikta][16-04-2025] -->
                     <tfoot>
                        <tr>
                            <td colspan="7" align="right" style="padding:10px; font-weight:bold;">
                                Total Records: <?=$numrows?>
                            </td>
                        </tr>
                    </tfoot>
                 </table>
             </div>
             </div>
             
         </div>
     </div>
     <!-- Hidden form inputs -->
     <input type="hidden" name="Action">
     <input type="hidden" name="CustomerID">
 </form>
</div>