<?php
/***
Auth: Vastvikta Nishad
Date: 10 Feb 2024
Description:Display Details of User Logins
*/
$datefilter = '';
// $startdatetime = isset($_REQUEST['startdatetime']) ? $_REQUEST['startdatetime'] : date("01-m-Y 00:00:00");
// $enddatetime = isset($_REQUEST['enddatetime']) ? $_REQUEST['enddatetime'] : date("d-m-Y 23:59:59");
$no = 0;
?>
<div class="style2-table">
    <div class="style-title">
        <h3>View User Logins</h3>  
    </div>
    <div id="success"></div>
    <form name="frmService" action="" method="post">
        <div class="table" id="SRallview"> 
            <div class="">
                <table class="tableview tableview-2 main-form new-customer">
                    <tr>
                        <td width="95" class="left boder0-right">
                            <label>Start Date </label>
                        </td>
                        <td width="226" class="left boder0-right">
                            <?
                              if($_REQUEST['startdatetime']!="")
                              {
                                $startdatetime=strtotime($_REQUEST['startdatetime']);
                                $sdate= date('Y-m-d H:i:s', $startdatetime);
                              }
                               if($_REQUEST['enddatetime']!="")
                              {
                                $enddatetime=strtotime($_REQUEST['enddatetime']);
                                $edate= date('Y-m-d H:i:s', $enddatetime);
                              }
                              $startdate = ($_REQUEST['startdatetime']!='') ? $sdate : date("Y-m-d 00:00:00");
                              $enddate = ($_REQUEST['enddatetime']!='') ? $edate : date("Y-m-d H:i:s");
                              ?>
                            <span class="left boder0-left">
                                <input type="text" name="startdatetime" class="date_class dob1" style="width:160px" 
                                    value="<?php echo $startdate ?>" >
                            </span>
                        </td>
                        <td width="112" class="left boder0-right"><label>End Date </label></td>
                        <td width="230" align="left" class="left boder0-right"><span class="left boder0-left">
                                <input type="text" name="enddatetime" class="date_class dob1" style="width:160px" 
                                    value="<?php echo $enddate ?>" >
                            </span>
                        </td>
                        <td class="left boder0-left" colspan="4">
                            <input type='submit' name='sub1' value='Run Report' class="button-orange1" />
                        </td>
                    </tr>
                </table>
                <div class="div2">
                    <table class="tableview tableview-2" id="admin_table">
                        <thead>
                        <tr class="background">
                            <td> S.No.</td>
                            <td>IP </td>
                            <td align="center">Login Time </td>
                            <td align="center">Username </td>
                            <td align="center">Logout Time </td>
                            <td align="center">Action </td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $datefilter .= " and (l.AccessedAt)>='".$startdate."' and l.AccessedAt<='".$enddate."'";
                            $result = getUserLogin($datefilter);
                            while($res = mysqli_fetch_array($result)){
                            $no++;
                            $SNo=$res['SNo'];
                        ?>
                        <tr>
                            <td align="center"><?php echo $no;   ?></td>
                            <td align="center"><?php echo $res['IP'];  ?></td>
                            <td align="center"><?php echo $res['AccessedAt']; ?></td>
                            <td>
                                <?php echo $res['UserName']."(".$res['AtxDesignation'].")"; ?>
                            </td>
                            <td align="center"><?php echo $res['TimePeriod']; ?></td>
                            <td align="center"><a
                                href="admin/web_relese_user.php?sno=<?php echo $SNo?>&amp;value=1"
                                class="newdocument cboxElement"><img
                                src="<?=$SiteURL?>public/images/task.jpg" border="0" alt="realse"></a>
                            </td>
                        <?php 
                            }
                        ?>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
