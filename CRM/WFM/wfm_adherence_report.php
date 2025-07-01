<?php
/**
 * Auth: Vastvikta Nishad
 * Date: 17 May 2024
 */
include_once("../includes/header.php");
include_once("function.php");

$groupid = $_SESSION['user_group'];
$rspoc = $_SESSION['reginoal_spoc'];
?>
<body onload="get_adherence()">
    <form name="frmagentdashboardd" action="" method="post">
        <span class="breadcrumb_head" style="height:37px; padding:9px 16px;">Monthly Adherence Report</span>
        <table class="tableview tableview-2 main-form new-customer">
            <tbody>
                <tr>
                    <td class="left boder0-right">
                        <label for="Schedule">Schedule</label>
                        <select style="width:190px;" class="select-styl1" id="sch_id" name="sch_id">
                            <?php
                            $sqlschedule = "SELECT i_procSchedID, v_schedName FROM $db.tbl_wfm_proc_schedule";    // Query for getting agent between satisfied date
                            $scheduleresult = mysqli_query($link, $sqlschedule);
                            while ($rowschedule = mysqli_fetch_array($scheduleresult)) {
                                ?>
                                <option value="<?= $rowschedule['i_procSchedID'] ?>"><?= $rowschedule['v_schedName'] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="left boder0-right">
                        <label for="agent_list">Select Agent</label>
                        <select style="width:190px;" class="select-styl1" id="agent_list" name="agent_list">
                            <option value="">Select</option>
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
                </tr>
                <tr>
                    <td class="left boder0-right">
                        <label for="startdatetime">From Date</label>
                        
                        <input type="text" name="startdatetime" class="date_class dob1 select-styl1" style="width:190px;" value="<?= $_REQUEST['sttartdatetime'] ?>" id="startdatetime" autocomplete="off">
                    </td>
                    <td class="left boder0-right">
                        <label for="enddatetime">To Date</label>
                        <input type="text" name="enddatetime" class="date_class dob1 select-styl1" style="width:190px;" value="<?= $_REQUEST['enddatetime'] ?>" id="enddatetime" autocomplete="off">
                    </td>
                    <td class="left boder0-right">
                        <input type="submit" name="sub1" value="Show" class="submit_wfm button-orange1">
                    </td>
                </tr>
            </tbody>
        </table>
        <br/>
        <div style="margin-bottom: 10px;">
            <table class="cch-table blue small text-center table table-striped table-bordered table-hover">
                <thead>
                    <tr class="row_2">
                        <th><b>Agent Name</b></th>
                        <th><b>Adherence %</b></th>
                        <th><b>Shrinkage %</b></th>
                        <th><b>Internal Shrinkage (h:m)</b></th>
                        <th><b>External Shrinkage %</b></th>
                        <th><b>Working Hours (h:m)</b></th>
                    </tr>
                </thead>
                <tbody id="div_adherence_monthly">
                    <!-- Data will be populated here -->
                </tbody>
            </table>
        </div>
    </form>
</body>
