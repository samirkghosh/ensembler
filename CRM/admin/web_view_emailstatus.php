<?php
/***
Auth: Vastvikta Nishad
Date: 09 Feb 2024
Description:Display Outgoing Email Report
*/
$I_Status = isset($_REQUEST['I_Status']) ? $_REQUEST['I_Status'] : '';
$to_email = isset($_REQUEST['to_email']) ? $_REQUEST['to_email'] : '';
$from_email = isset($_REQUEST['from_email']) ? $_REQUEST['from_email'] : '';
$timeperiod = isset($_REQUEST['timeperiod']) ? $_REQUEST['timeperiod'] : '';
$startdatetime = isset($_REQUEST['sttartdatetime']) ? $_REQUEST['sttartdatetime'] : date("01-m-Y 00:00:00");
$enddatetime = isset($_REQUEST['enddatetime']) ? $_REQUEST['enddatetime'] : date("d-m-Y 23:59:59");
?>
<div class="style2-table">
    <div class="style-title">
        <h3>Outgoing Email Report</h3> 
    </div>
    <div id="success"></div>
    <form name="frmService" action="" method="post">
        <div class="table" id="SRallview"> 
            <div class="">
                <table class="tableview tableview-2 main-form new-customer">
                <tbody>
                <tr>
                    <?php
                    $startdatetime= ($_REQUEST['sttartdatetime']!='') ? ($_REQUEST['sttartdatetime']) : date("01-m-Y 00:00:00");
                    $enddatetime = ($_REQUEST['enddatetime']!='') ? ($_REQUEST['enddatetime'])  : date("d-m-Y 23:59:59");

                    ?>
                    <td class="left boder0-left "  colspan="1"><span class="left  boder0-right">
                        From 
                        <input type="text" name="sttartdatetime" class="date_class dob1" style="width:160px" value="<?=$startdatetime?>"  autocomplete="off">&nbsp;
                        To <input type="text" name="enddatetime" class="date_class dob1"  style="width:160px" value="<?=$enddatetime?>" autocomplete="off">
                        </span>
                    </td>
                    <td class="left boder0-right">
                        <label>Status</label>
                        <div class="log-case">
                        <select name="I_Status" id="I_Status" class="select-styl1" style="width:190px">
                            <option value="">Select status</option>
                            <option value="1"  <? if($I_Status==1) echo "selected";?>>Pending</option>
                            <option value="2"  <? if($I_Status==2) echo "selected";?> >Sent</option>
                            <option value="3" <? if($I_Status==3) echo "selected";?> >Abort</option>
                        </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="left boder0-right">
                        <label>To</label>
                        <div class="log-case">
                        <?php 
                            $agnetresult=getToEmailInformation();?>
                            <select name="to_email" id="to_email" class="select-styl1" style="width:190px">
                            <option value="">All</option>
                                <?                                                 
                                while($row=mysqli_fetch_array($agnetresult)) {
                                    $v_toemail=$row['v_toemail'];
                                    $id=$row['id'];
                                    if($v_toemail == $_REQUEST['to_email'])
                                    {
                                    $sel = ' selected';
                                    }
                                    else
                                    {
                                    $sel = '';
                                    }
                                ?>  
                            <option value='<?php echo$v_toemail?>' <?php echo $sel?>><?php echo $v_toemail?></option>
                                <? } ?>
                            </select>
                        </div>
                    </td>
                    <td class="left boder0-right">
                        <label>From</label>
                        <?php 
                            $agnetresults=getFromEmailInformation();
                            ?>
                            <select name="from_email" id="from_email" class="select-styl1" style="width:190px">
                            <option value="">All</option>
                                <?                                                 
                                while($rows=mysqli_fetch_array($agnetresults)){
                                    $v_fromemail=$rows['v_fromemail'];
                                    $id=$rows['id'];
                                    if($v_fromemail == $_REQUEST['from_email'])
                                    {
                                    $sel = ' selected';
                                    }
                                    else
                                    {
                                    $sel = '';
                                    }
                                ?>       
                            <option value='<?php echo $v_fromemail?>' <?php echo $sel?>><?php echo $v_fromemail?></option>
                                <? } ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td  class="left boder0-right" colspan="2">
                        <center>
                        <?php $view_emailstatus = base64_encode('view_emailstatus');?>
                        <input type='submit' name='sub1' value='Run Report' class="button-orange1" onclick="dosubmit(3);" style="float:inherit;"/>
                        <a href="admin_index.php?token=<?php echo $view_emailstatus;?>"class="button-orange1" name="resetbtn" id="resetbtn" style="float:inherit;color:#222;text-decoration:none; ">Reset</a>
                    </center>
                    </td>
                </tr>
                </tbody>
                </table>
                <div class="div2">
                    <table class="tableview tableview-2" id="admin_table">
                        <thead>
                            <tr class="background">
                                <td align="center" valign="middle" width="5%"> S.No</td>
                                <td align="center" valign="middle" width="10%" >To </td>
                                <td align="center" valign="middle" width="10%">From </td>
                                <td align="center" valign="middle" width="20%">Date </td>
                                <td align="center" valign="middle" width="5%">Status </td>
                                <td align="center" valign="middle" width="30%">Remark </td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $to_email= $_REQUEST['to_email'];
                            $from_email= $_REQUEST['from_email'];
                            if(!empty($_REQUEST['timeperiod'])){ $timeperiod=$_REQUEST['timeperiod']; }else{  $timeperiod=''; }
                            if(!empty($_REQUEST['sttartdatetime'])){ $startdatetime=$_REQUEST['sttartdatetime']; }else{  $startdatetime=''; }
                            if(!empty($_REQUEST['enddatetime'])){ $enddatetime=$_REQUEST['enddatetime']; }else{  $enddatetime=''; }
                            if($timeperiod!="" || !empty($timeperiod))
                            {
                            //echo "1".$timeperiod;
                            $from=date('Y-m-d',strtotime(getFromDate('from',$timeperiod)));
                            $to=date('Y-m-d',strtotime(getFromDate('to',$timeperiod)));
                            $datefilter = " AND d_email_date >= '$from 00:00:00' AND d_email_date <= '$to 23:59:59' ";
                            }
                            else
                            {
                            //echo "2";
                            $from=date('Y-m-d H:i:s',strtotime($startdatetime));
                            $to=date('Y-m-d H:i:s',strtotime($enddatetime));
                            if($startdatetime!='' && $enddatetime!=''){ $datefilter .=" and d_email_date>='$from' and d_email_date<='$to'  "; }
                            }
                            if($I_Status!="" || !empty($I_Status))
                            { 
                            $datefilter .="  AND I_Status='".$I_Status."' "; 
                            }
                            if($to_email!="" || !empty($to_email))
                            {
                                $datefilter .="  AND v_toemail='".$to_email."' "; 
                            }
                            if($from_email!="" || !empty($from_email))
                            { 
                                $datefilter .="  AND v_fromemail='".$from_email."' "; 
                            }
                            $res = getAllEmailInformation($datefilter);             
                            if (!$res) {
                                die("Query failed: " . mysqli_error($link));
                            }
                            $numrow=mysqli_num_rows($res);                           
                            if($numrow==0) 
                            {                            
                            $message="No record Found!";
                            echo '<tr><td colspan=6 align="right" class="contentred"><img src=images/no_records.jpg>&nbsp;'.$message.'</td></tr>';
                            }
                            else{
                            $no=0;
                            while($row=mysqli_fetch_array($res))
                            {
                            $no++;
                            $EMAIL_ID=$row['EMAIL_ID'];
                            $v_fromemail=$row['v_fromemail'];
                            $v_toemail  =$row['v_toemail'];
                            $createdon=$row['d_email_date'];
                            $c=explode("-",$createdon);
                            $cd=$c[2]."-".$c[1]."-".$c[0];
                            $count=$count+1;  
                            $check="check".$count;
                            if($count%2==0) $clr="#efefef"; else $clr="#ffffff";
                            ?>
                            <tr >
                            <td align="center"><?php echo $no; ?></td>
                            <td align="center"><?php echo $v_toemail ?></td>
                            <td><?php echo $v_fromemail; ?></td>
                            <td align="center"><?php echo date('d-m-Y H:i:s', strtotime($createdon));?></td>
                            <td align="center"><?php 
                            $status=$row['I_Status'];
                            if($status==1)
                            {
                            echo "Pending";
                            }else if($status==2)
                            {
                            echo "Sent";
                            }else if($status==3)
                            {
                            echo "Abort";
                            }                            
                            ?></td>
                            <td><?php echo ($row['v_LastError']=='') ? 'NA' : $row['v_LastError']?></td>
                            </tr>
                            <?php 
                            }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>