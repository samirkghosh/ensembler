<?php
/**
 * Auth: Vastvikta Nishad
 * Date: 17 May 2024
 */
include("../../config/web_mysqlconnect.php");

$groupid = $_SESSION['user_group'];
$rspoc = $_SESSION['reginoal_spoc'];
?>
<form name="frmagentdashboardd" action="" method="post">
    <!-- Start Right panel -->
    <span class="breadcrumb_head" style="height:37px; padding:9px 16px;">Agent Monthly Schedule Details</span>
    <div class="row row1">
        <table style="width: 100%;" class="tableview tableview-2 main-form new-customer">
            <tr>
                <td class="left boder0-right">Schedule :</td>
                <td>
                    <select style="width:150px;" id="sch_id" name="sch_id">
                        <?php
                        $sqlschedule = "SELECT i_procSchedID, v_schedName FROM $db.tbl_wfm_proc_schedule";    // Query for getting agent between satisfied date
                        $scheduleresult = mysqli_query($link, $sqlschedule);
                        while ($rowschedule = mysqli_fetch_array($scheduleresult)) {
                            ?>
                            <option value="<?= $rowschedule['i_procSchedID'] ?>" <?= ($_REQUEST['sch_id'] == $rowschedule["i_procSchedID"]) ? 'selected' : '' ?>><?= $rowschedule['v_schedName'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td>Select Agent :</td>
                <td>
                    <select id="agent_list" name="agent_list">
                        <option value="select">Select</option>
                        <?php
                        $agent_query = mysqli_query($link, "SELECT AtxUserID, AtxDisplayName FROM $db.uniuserprofile WHERE AtxDisplayName!='' AND AtxUserStatus=1 AND AtxDesignation='Agent'");
                        while ($agent_name_res = mysqli_fetch_array($agent_query)) {
                            ?>
                            <option value='<?= $agent_name_res["AtxUserID"] ?>' <?= ($_REQUEST['agent_list'] == $agent_name_res["AtxUserID"]) ? 'selected' : '' ?>><?= $agent_name_res["AtxDisplayName"] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <input type='submit' name='btn_Show' id="btn_Show" class="btn_Show" value='Show' style="height: 25px; width:70px; padding: 5px; background-color: ;">
                </td>
            </tr>
        </table>
    </div>
    <div id="div_adherence">
        <?php 
        include("wfm_get_monthly_event.php");
        ?>
    </div>
    <!-- End Right panel -->
</form>
