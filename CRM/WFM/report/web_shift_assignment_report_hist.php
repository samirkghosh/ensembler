<?php
/**
 * Auth: Vastvikta Nishad
 * Date: 17 May 2024
 */
$name = $_SESSION['logged'];

$db = strtolower($db);
?>

<form name="myform" method="post" id="shift_hist_form">
    <div>
        <div class="table">
            <table class="tableview tableview-2 main-form new-customer">
                <tbody>
                    <tr class="background2">
                        <th height="44" colspan="2" align="left">Shift Assignment Report</th>
                    </tr>
                    <tr>
                        <td colspan="2" class="left boder0-left">
                            <span class="left boder0-right">
                                <label>Select Schedule</label>
                                <?php
                                $sql_scedule = "select v_schedName, i_procSchedID from $db.tbl_wfm_proc_schedule_hist";
                                $q_scedule = mysqli_query($link, $sql_scedule);
                                ?>
                                <select name="i_procSchedID" id="i_procSchedID" class="select-styl1" style="width:190px" required>
                                    <option value="">Select Schedule</option>
                                    <?php
                                    while ($row_scedule = mysqli_fetch_array($q_scedule)) {
                                        $sel_sche = ($i_procSchedID == $row_scedule['i_procSchedID']) ? "selected" : "";
                                        ?>
                                        <option value="<?= $row_scedule['i_procSchedID'] ?>" <?= $sel_sche ?>><?= $row_scedule['v_schedName'] ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <label></label>
                            <span class="left boder0-left">
                                <input type="submit" name="sub1" value="Run Report" class="button-orange1" onclick="dosubmit_shift_hist(1)">
                                <input name="export" type="submit" value="Export Report" class="button-blue1" style="float:inherit;" onclick="dosubmit_shift_hist(2);">
                                <input name="print" type="button" value="Print" class="button-gray1" style="float:inherit;" onclick="dosubmit_shift_hist(3);">
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
        if ($_REQUEST['i_procSchedID'] != '') {
            $cond_sc = " where i_procSchedID= '" . $i_procSchedID . "' ";
        }
        $qq = "select * from $db.tbl_wfm_proc_schedule_list_hist $cond_sc ";
        $ticket_query = mysqli_query($link, $qq);
        $total = mysqli_num_rows(mysqli_query($link, $qq));
        ?>
        <!-- Start Display the filter data -->
        <div id="display-error" style="margin-top:5px;">Total Records Found - <?= $total ?> </div>
        <div class="table">
            <div class="wrapper1">
                <div class="div1" style="width:1800px;"></div>
            </div>
            <div class="wrapper2">
                <div class="div2" style="width:1800px;">
                    <table class="tableview tableview-2" id="shift_report_hist">
                        <thead>
                            <tr class="background">
                                <td width="4%" align="center">Schedule Name</td>
                                <td width="4%" align="center">Shift Name</td>
                                <td width="4%" align="center">Total No Agent Required</td>
                                <td width="4%" align="center">Total number of Agents Assigned</td>
                                <td width="4%" align="center">Deficiency</td>
                                <td width="4%" align="center">Number of Agents With Preferred Shift</td>
                                <td width="4%" align="center">Number of Agents non-preferred Shift</td>
                                <td width="4%" align="center">Number Agents with Matching Skills</td>
                                <td width="2%" align="center">Number Agents with over Skilled</td>
                                <td width="2%" align="center">Number Agents with non-Matching Skills</td>
                                <td width="2%" align="center">Deleted on</td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!-- End Display the filter data -->
        <span style="display:block; text-align:right;">
            <input name="export" type="submit" value="Export Report" class="button-blue1" style="float:inherit;" onclick="dosubmit_shift_hist(2);">
            <input name="print" type="button" value="Print" class="button-gray1" style="float:inherit;" onclick="dosubmit_shift_hist(3);">
        </span>
    </div>
    <!-- End Right panel -->
</form>
