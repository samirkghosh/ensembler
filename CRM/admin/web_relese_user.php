<?php
/***
Auth: Aarti ojha
Description: This page is used to release a user by updating their logout time in the database. It checks if the logout time is valid, and if so, updates the session record with the release details.
*/
   session_start(); 
   
   include("../../config/web_mysqlconnect.php");
   
   $SNo=$_REQUEST['sno'];
    $sql_l="SELECT * FROM $db.logip as l 
     LEFT JOIN $db.uniuserprofile as u ON u.AtxUserName=l.UserName LEFT JOIN $db.unigroupdetails as ug ON u.AtxUserID=ug.ugdContactID
     where l.SNo='$SNo' ";
   $queryl=mysqli_query($link,$sql_l)or die(mysqli_error($link).'err');
   $fetchl=mysqli_fetch_array($queryl);
   $AccessedAt=$fetchl['AccessedAt'];//login time
   $AtxUserID  = $fetchl['AtxUserID'];
   $AtxUserName = $fetchl['AtxUserName'];
   $err=0;
   if(($_REQUEST['btnrelease']=="Release")){
   	$logouttime=$_REQUEST['logouttime'];
   	$currenttime=date("Y-m-d H:i:s");
   
   	if($logouttime=="")
   	{
   		$msg="Please enter logout time";
   		$err=1;
   	}
   	if(	strtotime($logouttime) <=strtotime($AccessedAt))
   	{
   		$msg="<br>Logout time should be greated than Login time";
   		$err=1;
   	}
   	if(strtotime($logouttime) >=strtotime($currenttime)){
   		$msg="<br>Logout time should be less than current time";
   		$err=1;
   	}
   	if($err==1){
   		//echo $msg;
     	}else{
     		$ldate = strtotime($logouttime); 
     		$ldate= date('Y-m-d H:i:s', $ldate);
     
     		$sql_upd="UPDATE $db.logip SET release_by='".$_SESSION['userid']."' ,release_on	=NOW(),TimePeriod='".$ldate."' where SNo='$SNo'";
     		mysqli_query($link,$sql_upd);

        // for update user status offline[Aarti][27-11-2024]
        $logintime   = date("Y-m-d H:i:s");
        $sql = "UPDATE $db.uniuserprofile SET login_status = 'offline' , login_datetime = '$logintime' where AtxUserID= '".$AtxUserID."'";
        mysqli_query($link, $sql);
        // for updating logout time in logip table [vastvikta][03-04-2025]
        $sql2 = "UPDATE $db.logip SET TimePeriod = '$logintime' WHERE UserName = '$AtxUserName'";
        mysqli_query($link, $sql2);
     		$msg = 'Sucessfully updated<br>';	
     }
   }
   include("../includes/head.php");
   ?>
<style>
.addinteraction-popup h1 {
    color: #545454;
    font-size: 14px;
    text-transform: uppercase;
    border-bottom: #d2232a 3px solid;
    padding: 0 0 10px 0;
    margin: 0 0 10px 0;
}
</style>

<body>
    <form method="post" name="frm" id="frm">
        <div class="addinteraction-popup">
            <!-- <h1>Release User</h1> -->
            <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Release User</span>
            <table class="tableview tableview-2 main-form">
                <tbody>
                    <tr>
                        <td colspan="2" class="left">
                            <? if($msg!=''){?>
                            <div
                                style="color:#ffffff; width:100%; background:#00CC66; padding:5px; border:1px solid #02944b; text-align:center;">
                                <?=$msg?>
                            </div>
                            <? } ?>
                            <input type="hidden" name="uname" value="<?=$name?>" />

                            <div class="form-group">
                                <label style="font-weight:700">User :</label>
                                <?=$fetchl['UserName']?>(<?=$fetchl['IP']?>)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="left">
                            <div class="form-group">
                                <label style="font-weight:700">Login Time:</label>
                                <?=$fetchl['AccessedAt']?>
                            </div>
                    </tr>
                    <tr>
                        <td colspan="2" class="left">
                            <div class="form-group">
                                <label style="font-weight:700">Logout Time:</label>

                                <?php
                        $logouttime = ($_REQUEST['logouttime']!='') ? $_REQUEST['logouttime'] : date("Y-m-d H:i:s");?>
                                <input type="text" name="logouttime" id="" value="<?=$logouttime?>" autocomplete="off"
                                    style="border:none;color:#828282;" readonly />
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <center>
                                <!-- Added supervisor groupid so that the supervisor can release the user [vastvikta][12-02-2025] -->
                   
                                <?  if(($_SESSION['user_group']=='0000')||($_SESSION['user_group']=='080000')) { ?>
                                <input name="btnrelease" type="submit" value="Release" class="button-orange1"
                                    style="float:inherit;">
                                <!-- <input name="reset"  type="submit" value="Reset " class="button-gray1" > -->
                                <? } ?>
                                <input type="hidden" name="SNo" value="<?=$_REQUEST['sno']?>">
                            </center>
                        </td>
                    </tr>

                </tbody>
            </table>


        </div>
    </form>


    <script type="text/javascript">
    $('.date_class').datetimepicker();
    </script>
</body>

</html>