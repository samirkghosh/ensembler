<?php
/**
 * Auth: Vastvikta Nishad
 * Date: 17 May 2024
 */
$name = $_SESSION['logged'];

$db = strtolower($db);
?>
<!-- <body onload="web_cat('<?=$_REQUEST['type']?>','<?=$_REQUEST['category']?>'); web_subcat('<?=$_REQUEST['category']?>','<?=$_REQUEST['subcategory']?>'); web_actiontaken('<?=$_REQUEST['assignfor']?>','<?=$_REQUEST['actiontaken']?>');get_disposition();"> -->
<form name="myform" method="post" id="agentwise_form">
    <div>
        <div class="table">
            <!-- <div class="background-white"></div> -->
            <table class="tableview tableview-2 main-form new-customer" id="agentwise_form">
                <tbody>
                    <tr class="background2">
                        <th height="44" colspan="2" align="left">Agentwise Assignment Report</th>
                    </tr>
                    <tr>
                        <td colspan="2" class="left boder0-left">
                            <span class="left boder0-right">
                                <label>Select Agent</label>
                                <?php
                                $sql_a = "SELECT AtxUserID, AtxDisplayName FROM $db.uniuserprofile WHERE AtxDesignation='Agent' ORDER BY AtxDisplayName ASC";
                                $q_a = mysqli_query($link, $sql_a);
                                ?>
                                <select name="AtxUserID" id="AtxUserID" class="select-styl1" style="width:190px" required>
                                    <option value="">Select Agent</option>
                                    <?php
                                    while ($row_a = mysqli_fetch_array($q_a)) {
                                        $sel_a = ($AtxUserID == $row_a['AtxUserID']) ? "selected" : "";
                                        ?>
                                        <option value="<?= $row_a['AtxUserID'] ?>" <?= $sel_a ?>><?= $row_a['AtxDisplayName'] ?></option>
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
                                <input type="submit" name="sub1" value="Run Report" class="button-orange1" onclick="dosubmit_agentwise(1)">
                                <input name="export" type="submit" value="Export Report" class="button-blue1" style="float:inherit;" onclick="dosubmit_agentwise(2);">
                                <input name="print" type="button" value="Print" class="button-gray1" style="float:inherit;" onclick="dosubmit_agentwise(3);">
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
        if ($_REQUEST['AtxUserID'] != "") {
            $whr_cond_agent = " AND i_AgentID='" . $_REQUEST['AtxUserID'] . "' ";
        }
        $qq = "SELECT * FROM $db.tbl_wfm_agent_sched_assignment WHERE 1=1 $whr_cond_agent";
        // echo $qq;
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
                    <table class="tableview tableview-2" id="agentwise_report">
                        <thead>
                            <tr class="background">
                                <td width="4%" align="center">Agent Name</td>
                                <td width="4%" align="center">Skill Sets</td>
                                <td width="4%" align="center">Preferred Shifts</td>
                                <td width="4%" align="center">Assigned Shifts and Skills</td>
                                <td width="4%" align="center">Under Utilised</td>
                                <td width="4%" align="center">From Date</td>
                                <td width="4%" align="center">To Date</td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!-- End Display the filter data -->
        <span style="display:block; text-align:right;">
            <input name="export" type="submit" value="Export Report" class="button-blue1" style="float:inherit;" onclick="dosubmit_agentwise(2);">
            <input name="print" type="button" value="Print" class="button-gray1" style="float:inherit;" onclick="dosubmit_agentwise(3);">
        </span>
    </div>
    <!-- End Right panel -->
</form>