<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Side Navigation</title>
    <style>
        .report_side {
            margin: 10px;
        }

        .report_side ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .report-item {
            margin: 5px 0; 
        }

        .report-item a {
            text-decoration: none;
            color: F0F8FF;
            padding: 10px;
            display: block;
            border: 4px solid transparent;
            border-radius: 7px;
            transition: background-color 0.3s, color 0.3s, border-color 0.3s;
            overflow: hidden; 
            text-overflow: ellipsis; 
            width: 100px!important;
            height: 100px!important; 
            background-color: #0f9fb4;;
            font-size: 14px; 
            word-wrap: break-word; 
            font-family: 'Times New Roman ';
            border-color:#002366;
            font-weight: bold;
            padding-top:13px;
        }

        .report-item a:hover,
        .report-item a.active { 
            background-color: #002366;
            
            color: white;
            border-color: #0f9fb4;
        }
    </style>
</head>
<body>
    <div class="report_side">
        <ul>
            <?php 
            $shift_assignment_report = base64_encode('shift_assignment_report');
            $agentwise_assignment_report = base64_encode('agentwise_assignment_report');
            $schedule_adherence_report = base64_encode('schedule_adherence_report');
            $shift_assignment_report_hist = base64_encode('shift_assignment_report_hist');
            $agentwise_assignment_report_hist = base64_encode('agentwise_assignment_report_hist');
            $schedule_adherence_report_hist = base64_encode('schedule_adherence_report_hist');

            // Determine the current page (example logic, you may need to adjust this)
            $current_page = basename($_SERVER['REQUEST_URI']);
            ?>
            <li class="report-item"><a href="wfm_reports.php?token=<?php echo $shift_assignment_report?>" class="<?php echo ($current_page == 'wfm_reports.php?token='.$shift_assignment_report) ? 'active' : ''; ?>">Shift Assignment Report</a></li>
            <li class="report-item"><a href="wfm_reports.php?token=<?php echo $agentwise_assignment_report?>" class="<?php echo ($current_page == 'wfm_reports.php?token='.$agentwise_assignment_report) ? 'active' : ''; ?>">Agentwise Assignment Report</a></li>
            <li class="report-item"><a href="wfm_reports.php?token=<?php echo $schedule_adherence_report?>" class="<?php echo ($current_page == 'wfm_reports.php?token='.$schedule_adherence_report) ? 'active' : ''; ?>">Schedule Adherence Report</a></li>
            <li class="report-item"><a href="wfm_reports.php?token=<?php echo $shift_assignment_report_hist?>" class="<?php echo ($current_page == 'wfm_reports.php?token='.$shift_assignment_report_hist) ? 'active' : ''; ?>">Shift Assignment Report Hist</a></li>
            <li class="report-item"><a href="wfm_reports.php?token=<?php echo $agentwise_assignment_report_hist?>" class="<?php echo ($current_page == 'wfm_reports.php?token='.$agentwise_assignment_report_hist) ? 'active' : ''; ?>">Agentwise Assignment Report Hist</a></li>
            <li class="report-item"><a href="wfm_reports.php?token=<?php echo $schedule_adherence_report_hist?>" class="<?php echo ($current_page == 'wfm_reports.php?token='.$schedule_adherence_report_hist) ? 'active' : ''; ?>">Schedule Adherence Report Hist</a></li>
        </ul>
    </div>
