<?php
include "../../config/web_mysqlconnect.php"; // or whatever your DB connection file is
include "../web_function.php";

// Sanitize input
$ticketid = mysqli_real_escape_string($link, $_POST['ticketid']);

$sql = "SELECT * FROM case_dept_timelines WHERE case_id = '$ticketid'";
$result = $link->query($sql);

$html = '<table class="child-table" style="width:100%; font-size:12px;">
            <thead>
              <tr>
                <th>Department</th>
                <th>Start Time</th>
                <th>End Time</th>
                <th>Duration</th>
                <th>Category</th>
                <th>Subcategory</th>
                <th>Status</th>
                <th>Comment</th>
                <th>Remark</th>
              </tr>
            </thead>
            <tbody>';

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $html .= '<tr>
                    <td>' . department($row['department']) . '</td>
                    <td>' . $row['start_time'] . '</td>
                    <td>' . $row['end_time'] . '</td>
                    <td>' . formatDuration($row['duration']) . '</td>
                    <td>' . category($row['category']) . '</td>
                    <td>' . subcategory($row['subcategory']) . '</td>
                    <td>' . ticketstatus($row['case_status']) . '</td>
                    <td>' . htmlspecialchars($row['comment']) . '</td>
                    <td>' . htmlspecialchars($row['remark']) . '</td>
                  </tr>';
    }
} else {
    $html .= '<tr><td colspan="9" style="text-align:center;">No timeline records found.</td></tr>';
}

$html .= '</tbody></table>';

echo $html;
?>

