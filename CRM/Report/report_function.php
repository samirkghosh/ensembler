<?php
/***
 * REPORT VIEW
 * Author: Aarti Ojha & Ritu 
 * Date: 16-01-2024
 * This file is handling multiple report details 
 * 
 */
include "../../config/web_mysqlconnect.php"; //  Connection to database // Please do not remove this
// fetch user details
include "../web_function.php";
/*Auth:Vastvikta Nishad
*Date: 12-09-2024
*/
// Check if the action is to view agent Break report
if($_POST['action'] == 'agent_break_report'){
  agent_break_report(); // Call function to view agent break report
}
if($_POST['action'] == 'break_report_details'){
  break_report_details(); 
}
// Check if the action is to view a case report
if($_POST['action'] == 'view_case_reportt'){
  view_case_reportt(); // Call function to view case report
}
// Check if the action is to view a customer report
if($_POST['action'] == 'view_customer_report'){
  view_customer_report(); // Call function to view customer report
}
// Check if the action is to display subcounty
if($_POST['action'] == 'display_subcounty'){
  display_subcounty(); // Call function to display subcounty
}
if($_POST['action'] == 'display_subcategory'){
  display_subcategory(); // Call function to display_subcategory
}
// Check if the action is to view an agent report
if($_POST['action'] =='view_agentreport'){
  view_agentreport(); // Call function to view agent report
}
// Check if the action is to view a report overview
if($_POST['action']=='view_report_overview'){
  view_report_overview(); // Call function to view report overview
}
// Check if the action is to view an audit report
if($_POST['action'] == 'view_audit_report'){
  view_audit_report(); // Call function to view audit report
}
// Check if the action is to view frequent callers
if($_POST['action'] == 'view_frequent_caller'){
  view_frequent_caller(); // Call function to view frequent callers
}
// Check if the action is to view frequent tickets
if($_POST['action'] == 'view_frequent_ticket'){
  view_frequent_ticket(); // Call function to view frequent tickets
}
// Check if the action is to view CSAT/DSAT details
if($_POST['action'] == 'view_CSAT_DSAT_detail'){
  view_CSAT_DSAT_detail(); // Call function to view CSAT/DSAT details
}
// Check if the action is to view CSAT/DSAT summary
if($_POST['action'] == 'view_CSAT_DSAT_summary'){
  view_CSAT_DSAT_summary(); // Call function to view CSAT/DSAT summary
}
// Check if the action is to view a customer effort report
if($_POST['action'] == 'view_customer_effort_report'){
  view_customer_effort_report(); // Call function to view customer effort report
}
// Check if the action is to view an NPS report
if($_POST['action'] == 'view_nps_report'){
  view_nps_report(); // Call function to view NPS report
}
// Check if the action is to view voicemail
if($_POST['action'] == 'view_voicemail'){
  view_voicemail(); // Call function to view voicemail
}
// Check if the action is to view an FCR report
if($_POST['action'] == 'view_fcr_report'){
  view_fcr_report(); // Call function to view FCR report
}
// Check if the action is to view a custom case report
if($_POST['action'] == 'view_Customecase_report'){
  view_Customecase_report(); // Call function to view custom case report
}
// Check if the action is to view a disposition (generateReport) report
if($_POST['action'] == 'generateReport'){
  generateReport();
}
if($_POST['action'] == 'updateStatus'){
  updateStatus($id); // Call function to set status to read on playing  the voicemail [vastvikta nishad][27-01-2025]
}
if($_POST['action'] == 'export_case_data'){
  export_case_data();
}
function get_uniuserprofile($id) {
  global $db, $link;

  // Ensure the ID is an integer to prevent SQL injection
  $id = (int)$id;

  // Prepare the SQL query
  $sqlagent = "SELECT AtxUserName FROM $db.uniuserprofile WHERE AtxUserStatus='1' AND AtxUserID = ?";

  // Prepare the statement
  if ($stmt = mysqli_prepare($link, $sqlagent)) {
      // Bind the parameter
      mysqli_stmt_bind_param($stmt, "i", $id);

      // Execute the statement
      mysqli_stmt_execute($stmt);

      // Bind the result variable
      mysqli_stmt_bind_result($stmt, $username);

      // Fetch the result
      if (mysqli_stmt_fetch($stmt)) {
          // Close the statement
          mysqli_stmt_close($stmt);

          // Return the username
          return $username;
      } else {
          // Close the statement
          mysqli_stmt_close($stmt);

          // Return false if no result is found
          return false;
      }
  }
}

function uniuserprofile(){
  global $db,$link;
  $sqlagent="select AtxUserID,AtxUserName from $db.uniuserprofile where AtxUserStatus='1' ORDER BY AtxUserName ASC";
    $result=mysqli_query($link,$sqlagent);
    return $result;
}
// fetch status details
function get_status(){
  global $db,$link;
  $query = "select id,ticketstatus from $db.web_ticketstatus where status='1' ORDER BY ticketstatus ASC";
  $result=mysqli_query($link,$query);
  return $result;
}
// get project details
function get_projects(){
  global $db,$link;
  $query = "select pId,vProjectName from $db.web_projects where i_Status='1' ORDER BY vProjectName ASC";
  $project_query = mysqli_query($link,$query);
  return $project_query;
}
// get category list
function get_category(){
  global $db,$link;
  $sqlsource="select id, category from $db.web_category where status=1 ORDER BY category ASC";
  $sourceresult=mysqli_query($link,$sqlsource);
  return $sourceresult;
}

// get subcategory list
function get_subcategory($cateid){
  global $db,$link;
  $sqlsource="SELECT id, subcategory FROM $db.web_subcategory where status=1 ";
  $sourceresult=mysqli_query($link,$sqlsource);
  return $sourceresult;
}
//get city list
function get_city(){
  global $db,$link;
  $sqlsource="select id, city from $db.web_city where status=1 ORDER BY city ";
  $sourceresult=mysqli_query($link,$sqlsource);
  return $sourceresult;
}
// get Village list
function get_Village($district){
    global $db,$link;
    // Prepare the SQL query
    $sql = "SELECT id, vVillage FROM $db.web_Village WHERE iDistrictID='$district' AND status='1' ORDER BY vVillage ASC";
    // Execute the query
    $villages_query = mysqli_query($link, $sql);    
    // Check if the query was successful
    if (!$villages_query) {
        // Query failed, handle the error (this is just a basic example)
        die('Error: ' . mysqli_error($link));
    }
    // Return the result set
    return $villages_query;
}

// fecth complaint type
function get_complaint_type(){
  global $db,$link;
  $query = "select * from $db.tbl_complaint_type ORDER BY category ";
  $comp_query = mysqli_query($link, $query);
  return $comp_query;
}
// fetch source for filter
function get_source(){
  global $db,$link;
  $sqlsource="select id,source from $db.web_source where status='1' ORDER BY source";
  $sourceresult=mysqli_query($link,$sqlsource);
  return $sourceresult;
}
// fetch source 'customer portal'
function get_source_portal(){
  global $db,$link;
  $sqlsource="select id,source from $db.web_source where source='Customer Portal'";
  $poralsourceresult=mysqli_query($link,$sqlsource);
  return $poralsourceresult;
}
// Reasons for calling
function get_callingtype(){
  global $db,$link;
  $query = "select id, complaint_name, slug, status from $db.complaint_type where status = 1";
  $complaint_sql = mysqli_query($link,$query); 
  return $complaint_sql;
}
// fetch Customer type for filter
function get_Customertype(){
  global $db,$link;
  $query = "select * from $db.tbl_customertype";
  $tbl_regional = mysqli_query($link, $query);
  return $tbl_regional;
}
// fetch  no_of_survey
function get_no_of_survey(){
  global $db, $link;
  // $query ="SELECT count(*) as total FROM $db.`tbl_civrs_cdr` $datefilter AND AgentName='$cust_agent'";
  // $no_of_survey = mysqli_query($link, $query);
  // return $no_of_survey;

}
// fetch agent filter
function get_agent_name(){
  global $db,$link;
  $query="select distinct AgentName from $db.tbl_civrs_cdr where AgentName!=''";
  $agentname=mysqli_query($link,$query);
  return $agentname;
}
function get_agent_number(){
  global $db,$link;
  $sqlagent="select AgentName from $db.tbl_civrs_cdr";
  $agentresult=mysqli_query($link,$sqlagent);
  return $agentresult;
}
// fetch feedbackmode
function get_mode(){
  global $db,$link;
  $query="SELECT mode FROM $db.feedback_mode WHERE status=1 ORDER BY mode ASC";
  $result=mysqli_query($link,$query);
  return $result;
}
// fetch status from tbl ces
function get_status_ces(){
  global $db,$link;
  $query="SELECT id,customer_effort FROM $db.tbl_ces WHERE status=1";
  $result=mysqli_query($link,$query);
  return $result;
}
function break_time_sum($username, $start_date) {
  global $db, $link;
  
  // Fetch records for the specific user and date
  $query = "SELECT * FROM $db.agent_break WHERE username = '$username' AND DATE(startdatetime) = '$start_date' ORDER BY id DESC";

  $res = mysqli_query($link, $query);
  $total_duration = 0;

  // Loop through each break and calculate the duration
  while ($row = mysqli_fetch_array($res)) {
      $start = strtotime($row['startdatetime']);
      $end = strtotime($row['enddatetime']);
      $total_duration += ($end - $start);  // Sum all durations
  }

  return $total_duration;  // Return total duration in seconds
}
function get_date_agent_break(){
  global $db,$link;
  // Get the last record's date
  $query = "SELECT MAX(startdatetime) as last_date FROM $db.agent_break"; 
  $result = mysqli_query($link, $query);
  $row = mysqli_fetch_assoc($result);
  
  if ($row && $row['last_date']) {
      $last_date = $row['last_date']; // Last record date
      $end_date = date('Y-m-d', strtotime($last_date)); // Set End Date
      $month = date('m', strtotime($last_date)); // Get month
      $year = date('Y', strtotime($last_date)); // Get year
      $start_date = "$year-$month-01"; // Set Start Date as 01-month-year
  } else {
      $start_date = date('Y-m-01'); // First day of the current month
      $end_date = date('Y-m-d');    // Current date
 
  }

  return [
      'start_date' => $start_date,
      'end_date' => $end_date
  ];
}
function agent_break_report() {
  global $db, $link;
  $column = array('id', 'username', 'startdatetime', 'enddatetime');
  $out = '';
  $startdatetime = !empty($_POST['startdatetime']) ? $_POST['startdatetime'] : '';
  $enddatetime = !empty($_POST['enddatetime']) ? $_POST['enddatetime'] : '';

  // Apply the date range filter
  if ($startdatetime != '' && $enddatetime != '') {
      $from = date('Y-m-d H:i:s', strtotime($startdatetime));
      $to = date('Y-m-d H:i:s', strtotime($enddatetime));
      $out .= " WHERE startdatetime >= '$from' AND enddatetime <= '$to' "; 
  }

  // Handle grouping and ordering
  $out .= ' GROUP BY username, DATE(startdatetime) ';

  // Handle ordering
  if (isset($_POST['order'])) {
      $order_column = $column[$_POST['order'][0]['column']];
      $order_dir = $_POST['order'][0]['dir'];
      $out .= ' ORDER BY ' . $order_column . ' ' . $order_dir . ' ';
  } else {
      $out .= ' ORDER BY id DESC ';  // Default ordering
  }

  // Handle pagination (LIMIT clause)
  $limit = ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];

  // Final query
  $query = "SELECT * FROM $db.agent_break " . $out . $limit;

  // Debugging: Echo the query to see if it's constructed properly
  error_log($query);

  // Execute the query
  $res = mysqli_query($link, $query);
  
  // Get total filtered records count
  $filtered_res = mysqli_query($link,$query);
  $total_filtered = mysqli_num_rows($filtered_res);
  
  // Get total records count (without filtering)
  $total_row = mysqli_num_rows(mysqli_query($link, "SELECT * FROM $db.agent_break"));

  // Prepare the data to return in the response
  $data = array();
  $no = $_POST['start'];  // Start numbering from the correct offset
  
  while ($row = mysqli_fetch_array($res)) {
      $no++;
      
      $start = strtotime($row['startdatetime']);
      $end = strtotime($row['enddatetime']);
      
      // Calculate the difference in seconds
      $diff = $end - $start;
      
      $start_date = date("Y-m-d", $start);
   
      // Format the difference as hh:mm:ss
      $duration = gmdate("H:i:s", $diff);
      
      $username = $row['username'];

      $total_duration_seconds = break_time_sum($username, $start_date);
    
      // Format the total duration as H:i:s
      $total_duration_formatted = gmdate("H:i:s", $total_duration_seconds);
  

      $sub_array = array();
      $sub_array[] = $no;                      // Serial Number
      $sub_array[] = $row['username'];         // Username
      $sub_array[] = $start_date . ' || ' . $total_duration_formatted;
      $sub_array[] = '<a href="#" class="toggle-view" data-username="' . $row['username'] . '" data-startdate="' . $start_date . '">Toggle View</a>';
      $data[] = $sub_array;
  }

  // Prepare the JSON output
  $output = array(
    "sql_agent_break"=>$query,
      "draw" => intval($_POST["draw"]),
      "recordsTotal" => $total_filtered,
      "recordsFiltered" => $total_filtered,
      "data" => $data,
     
  );

  echo json_encode($output);
}

function break_report_details() {
  global $db, $link;
  
  $username = mysqli_real_escape_string($link, $_POST['username']);
  $startdate = mysqli_real_escape_string($link, $_POST['startdate']);
  
  $query = "SELECT * FROM $db.agent_break WHERE username = '$username' AND DATE(startdatetime) = '$startdate' ORDER BY id DESC";

  $res = mysqli_query($link, $query);
  $data = array();
  $no = $_POST['start'];

  while ($row = mysqli_fetch_array($res)) {
      $no++;
      $start = strtotime($row['startdatetime']);
      $end = strtotime($row['enddatetime']);
      $duration = gmdate("H:i:s", $end - $start);

      $sub_array = array();
      $sub_array[] = $no;
      $sub_array[] = $row['break_name'];
      $sub_array[] = $row['startdatetime'];
      $sub_array[] = $row['enddatetime'];
      $sub_array[] = $duration;

      $data[] = $sub_array;
  }

  $output = [
    "draw" => intval($_POST["draw"]),
    "recordsTotal" => mysqli_num_rows(mysqli_query($link, $query)),
    "recordsFiltered" => mysqli_num_rows($res),
    "data" => $data,
    // "total_duration" => $total_duration  // Total duration in seconds

  ];

  echo json_encode($output);
}

function get_date_login_user(){
  global $db,$link;
  // Get the last record's date
  $query = "SELECT MAX(AccessedAt) as last_date FROM $db.logip"; 
  $result = mysqli_query($link, $query);
  $row = mysqli_fetch_assoc($result);
  
  if ($row && $row['last_date']) {
      $last_date = $row['last_date']; // Last record date
      $end_date = date('Y-m-d', strtotime($last_date)); // Set End Date
      $month = date('m', strtotime($last_date)); // Get month
      $year = date('Y', strtotime($last_date)); // Get year
      $start_date = "$year-$month-01"; // Set Start Date as 01-month-year
  } else {
      $start_date = date('Y-m-01'); // First day of the current month
$end_date = date('Y-m-d');    // Current date
 
  }

  return [
      'start_date' => $start_date,
      'end_date' => $end_date
  ];
}
function view_agentreport(){
    global $db, $link;
    $column = array('SNo','UserName','AccessedAt', 'TimePeriod','TimePeriod',  'IP');
    $out = '';

    // Filters
    $agent_n = !empty($_POST['agent_n']) ? $_POST['agent_n'] : '';
    $timeperiod = !empty($_POST['timeperiod']) ? $_POST['timeperiod'] : '';
    $startdatetime = !empty($_POST['startdatetime']) ? $_POST['startdatetime'] : '';
    $enddatetime = !empty($_POST['enddatetime']) ? $_POST['enddatetime'] : '';

    // Apply filters to query
    if ($agent_n != '') {  
        $out .= " AND UserName='$agent_n' "; 
    }

    // Handle date filtering (time period or start/end date)
    // updated the code for the timeperiod as the previous code was wrong [vastvikta][29-03-2025]
    if ($timeperiod != "") {
      if ($timeperiod == "1") { // Current month
          $from = date('Y-m-01'); // First day of the current month
          $to = date('Y-m-t'); // Last day of the current month
      } elseif ($timeperiod == "2") { // Previous month
          $from = date('Y-m-01', strtotime('first day of last month'));
          $to = date('Y-m-t', strtotime('last day of last month'));
      } elseif ($timeperiod == "3") { // Financial year (April to March)
          $currentYear = date('Y');
          $startYear = (date('m') >= 4) ? $currentYear : $currentYear - 1;
          $endYear = $startYear + 1;
          $from = "$startYear-04-01";
          $to = "$endYear-03-31";
      } else { // Default case if other values exist
          $from = date('Y-m-d', strtotime(getFromDate('from', $timeperiod)));
          $to = date('Y-m-d', strtotime(getFromDate('to', $timeperiod)));
      }
      $out .= " AND AccessedAt>='$from 00:00:00' AND AccessedAt<='$to 23:59:59' "; 
  } 
  if (!empty($startdatetime) && !empty($enddatetime)) {
      $from = date('Y-m-d H:i:s', strtotime($startdatetime));
      $to = date('Y-m-d H:i:s', strtotime($enddatetime));
      $out .= " AND AccessedAt>='$from' AND AccessedAt<='$to' "; 
  }

    // Handle ordering
    if (isset($_POST['order'])) {
        $out .= ' ORDER BY ' . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'] . ' ';
    } else {
        $out .= ' ORDER BY SNo DESC ';
    }

    // Handle pagination (LIMIT clause)
    $limit = ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
    
    // Query to get the filtered data
    $query = "SELECT * FROM $db.logip WHERE UserName!='' ";
    $res = mysqli_query($link, $query . $out . $limit);
    
    // Get total filtered records count
    $filtered_res = mysqli_query($link, $query . $out);
    $total_filtered = mysqli_num_rows($filtered_res);
    
    // Get total records count (without any filtering)
    $total_row = mysqli_num_rows(mysqli_query($link, $query));

    $data = array();
    $no = $_POST['start']; // Start numbering from the correct offset

    // Loop through each row and prepare data for display
    while ($row = mysqli_fetch_array($res)) {
      $no++;
      $login = $row['AccessedAt'];
      $logout = $row['TimePeriod'];
      $agentname = $row['UserName'];
      $ip = $row['IP'];
      
      // Convert login and logout times to hh:mm:ss format
      $login_time = date('H:i:s', strtotime($login));
  
      if (!empty($logout)) {
          $logout_time = date('H:i:s', strtotime($logout));
  
          // Calculate the time difference in seconds
          $login_timestamp = strtotime($login);
          $logout_timestamp = strtotime($logout);
          $time_difference_seconds = $logout_timestamp - $login_timestamp;
  
          // Convert seconds to hh:mm:ss format
          $hours = floor($time_difference_seconds / 3600);
          $minutes = floor(($time_difference_seconds % 3600) / 60);
          $seconds = $time_difference_seconds % 60;
          $time_difference_formatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
      } else {
          $logout_time = ''; // Leave blank if logout time is not available
          $time_difference_formatted = ''; // Leave blank if logout time is not available
      }
  
      $sub_array = array();
      $sub_array[] = $no; // Display proper row numbering
      $sub_array[] = $agentname;
      $sub_array[] = $login;
      $sub_array[] = $logout;
      $sub_array[] = $time_difference_formatted;
      $sub_array[] = $ip;
      $data[] = $sub_array;
  }
  

    // Prepare the output in JSON format
    $output = array(
      "out" =>$startdatetime,
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => $total_filtered, // Total number of records in the database
        "recordsFiltered" => $total_filtered, // Number of records after filtering
        "data" => $data // The actual data to display
    );

    echo json_encode($output);
}

function count_all_data(){
    global $db, $link;
    $query = "SELECT * FROM logip";
    $case_result = mysqli_query($link, $query); // get status filter option
    $total = mysqli_num_rows($case_result);
    return $total;
}

function get_date_custom_case(){
  global $db,$link;
  $query = "SELECT p.*, a.*
  FROM $db.web_problemdefination AS p
  LEFT JOIN $db.web_accounts AS a ON a.AccountNumber = p.vCustomerID
  WHERE p.d_createDate = (SELECT MAX(d_createDate) FROM $db.web_problemdefination)";
  $result = mysqli_query($link, $query);
  $row = mysqli_fetch_assoc($result);
  
  if ($row && $row['d_createDate']) {
      $last_date = $row['d_createDate']; // Last record date
      $end_date = date('Y-m-d', strtotime($last_date)); // Set End Date
      $month = date('m', strtotime($last_date)); // Get month
      $year = date('Y', strtotime($last_date)); // Get year
      $start_date = "$year-$month-01"; // Set Start Date as 01-month-year
  } else {
      $start_date = date('Y-m-01'); // First day of the current month
$end_date = date('Y-m-d');    // Current date
 
  }

  return [
      'start_date' => $start_date,
      'end_date' => $end_date
  ];
}
// fetch case report with filter option
function view_Customecase_report() {
    global $db, $link;
    $column = array('d_createDate', 'i_source', 'fname', 'vCategory', 'vSubCategory', 'vRemarks', 'root_cause', 'corrective_measure', 'iCaseStatus', 'd_updateTime');
    $condition = "";

    // Retrieve search parameters
    $startdatetime = !empty($_REQUEST['startdatetime']) ? $_REQUEST['startdatetime'] : '';
    $enddatetime = !empty($_REQUEST['enddatetime']) ? $_REQUEST['enddatetime'] : '';

    if ($startdatetime != '' && $enddatetime != '') {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $condition .= " AND p.d_createDate >= '$from' AND p.d_createDate <= '$to' ";
    }
    

    // Other filters
    $filters = [
        'type' => 'vCaseType',
        'category' => 'vCategory',
        'subcategory' => 'vSubCategory',
        'customertype' => 'customertype',
        'casetype' => 'vCaseType',
        'source' => 'i_source',
        'status' => 'iCaseStatus',
        'casee' => 'p.ticketid',
        'fname' => 'fname',
        'agent' => 'i_CreatedBY',
        'vTypeOfcaller' => 'vTypeOfcaller',
        'assign' => 'iAssignTo',
        'district' => 'district',
        'v_Village' => 'v_Village',
        'vProject' => 'vProjectID',
        'perpetrator' => 'perpetrator',
        'affected' => 'affected',
        'service' => 'service',
        'comp' => 'complaint_type',
        'priority' => 'priority_user'
    ];

    foreach ($filters as $request_key => $db_field) {
        $value = !empty($_REQUEST[$request_key]) ? mysqli_real_escape_string($link, $_REQUEST[$request_key]) : '';
        if (!empty($value)) {
            if ($db_field == 'fname' || $db_field == 'perpetrator' || $db_field == 'affected' || $db_field == 'service') {
                $condition .= " AND $db_field LIKE '%$value%' ";
            } else {
                $condition .= " AND $db_field = '$value' ";
            }
        }
    }

    // Order by clause
    if (isset($_POST['order'])) {
        $order_column = $column[$_POST['order']['0']['column']];
        $order_dir = $_POST['order']['0']['dir'];
        $condition .= " ORDER BY $order_column $order_dir ";
    } else {
        $condition .= ' ORDER BY iPID DESC ';
    }

    // Limit code here
    $start = mysqli_real_escape_string($link, $_POST['start']);
    $length = mysqli_real_escape_string($link, $_POST['length']);
    $limit = "LIMIT $start, $length";

    // Base query
    $base_query = "SELECT p.*, a.* FROM $db.web_problemdefination AS p LEFT JOIN $db.web_accounts AS a ON a.AccountNumber = p.vCustomerID WHERE p.ticketid != ''";

    // For total data count (without filter)
    $total_row_query = "SELECT COUNT(*) as count FROM $db.web_problemdefination WHERE ticketid != ''";
    $total_row_result = mysqli_query($link, $total_row_query);
    $total_row = mysqli_fetch_assoc($total_row_result)['count'];

    // For filtered data
    $filtered_query = $base_query . $condition . " " . $limit;
    $filtered_res = mysqli_query($link, $filtered_query) or die(mysqli_error($link));

    // Fetch filtered data count
    $total_filtered = $total_row; // Adjust this to be the total number of records if filtering is used, otherwise it's the total count

    // Fetch filtered data
    $data = array();
    $no = $start + 1; // Start numbering from the current page's offset
    while ($row = mysqli_fetch_array($filtered_res)) {
        $sub_array = array();
        $sub_array[] = $no++;
        $sub_array[] = $row['ticketid'];
        $sub_array[] = $row['d_createDate'];
        $sub_array[] = source($row['i_source']);
        $sub_array[] = $row['fname'];
        $sub_array[] = category($row['vCategory']);
        $sub_array[] = subcategory($row['vSubCategory']);
        $sub_array[] = $row['vRemarks'];
        $sub_array[] = $row['root_cause'];
        $sub_array[] = $row['corrective_measure'];
        $sub_array[] = ticketstatus($row['iCaseStatus']);
        $sub_array[] = $row['d_updateTime'];
        $data[] = $sub_array;
    }

    // Return JSON formatted data
    $output = array(
        "sql_customcase"=>$filtered_query,
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => $total_filtered,
        "recordsFiltered" => $total_filtered,
        "data" => $data
    );

    echo json_encode($output);
}
function get_date_cases(){
  global $db,$link;
  $query = "SELECT p.*, a.*
  FROM $db.web_problemdefination AS p
  LEFT JOIN $db.web_accounts AS a ON a.AccountNumber = p.vCustomerID
  WHERE p.d_createDate = (SELECT MAX(d_createDate) FROM $db.web_problemdefination)";
  $result = mysqli_query($link, $query);
  $row = mysqli_fetch_assoc($result);
  
  if ($row && $row['d_createDate']) {
      $last_date = $row['d_createDate']; // Last record date
      $end_date = date('Y-m-d', strtotime($last_date)); // Set End Date
      $month = date('m', strtotime($last_date)); // Get month
      $year = date('Y', strtotime($last_date)); // Get year
      $start_date = "$year-$month-01"; // Set Start Date as 01-month-year
  } else {
      $start_date = date('Y-m-01'); // First day of the current month
$end_date = date('Y-m-d');    // Current date
 
  }

  return [
      'start_date' => $start_date,
      'end_date' => $end_date
  ];
}
function view_case_reportt() {
    global $db, $link;
    $column = array('d_createDate', 'age_grp', 'createdate', 'updatedate', 'iCaseStatus', 'i_source', 'fname', 'i_CreatedBY', 'vCategory', 'district', 'v_Village', 'priority_user', 'customertype', 'vRemarks');
    $condition = "";
    
    date('Y-m-d 23:59:59');date('Y-m-01 00:00:00');
    // Retrieve search parameters
    $timeperiod = !empty($_REQUEST['timeperiod']) ? $_REQUEST['timeperiod'] : '';
    $startdatetime = !empty($_REQUEST['startdatetime']) ? $_REQUEST['startdatetime'] : date('Y-m-01 00:00:00');
    $enddatetime = !empty($_REQUEST['enddatetime']) ? $_REQUEST['enddatetime'] :  date('Y-m-d 23:59:59');
    $priority = isset($_REQUEST['priority']) ? $_REQUEST['priority'] : '';
    $casetype = isset($_REQUEST['casetype']) ? $_REQUEST['casetype'] : '';
    $esc_status = isset($_REQUEST['esc_status']) ? $_REQUEST['esc_status'] : '';
    $village = !empty($_REQUEST['v_Village']) ? $_REQUEST['v_Village'] : '';
    $district = !empty($_REQUEST['district']) ? $_REQUEST['district'] : '';
    $limit = isset($_REQUEST['length']) ? intval($_REQUEST['length']) : 10;
    $start = isset($_REQUEST['start']) ? intval($_REQUEST['start']) : 0;
    $draw = isset($_REQUEST['draw']) ? intval($_REQUEST['draw']) : 1;

       // if (!empty($village)) {
    //     $condition .= " AND p.v_Village LIKE '%$village%' ";
    // } elseif (!empty($district)) {
    //     $condition .= " AND p.district = '$district' ";
    // }

    // Date filtering
    if ($timeperiod != "" || !empty($timeperiod)) {
        $from = date('Y-m-d', strtotime(getFromDate('from', $timeperiod)));
        $to = date('Y-m-d', strtotime(getFromDate('to', $timeperiod)));
        $condition .= " AND p.d_createDate >= '$from 00:00:00' AND p.d_createDate <= '$to 23:59:59' ";
    } else {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        if ($startdatetime != '' && $enddatetime != '') {
            $condition .= " AND p.d_createDate >= '$from' AND p.d_createDate <= '$to' ";
        }
    }

    $filters = [
        'category' => 'vCategory',
        'subcategory' => 'vSubCategory',
        'customertype' => 'customertype',
        'casetype' => 'vCaseType',
        'source' => 'i_source',
        'status' => 'iCaseStatus',
        'casee' => 'ticketid',
        'fname' => 'fname',
        'agent' => 'i_CreatedBY',
        'vTypeOfcaller' => 'vTypeOfcaller',
        'assign' => 'iAssignTo',
        'district' => 'district',
        'v_Village' =>'village',
        'vProject' => 'vProjectID',
        'perpetrator' => 'perpetrator',
        'affected' => 'affected',
        'service' => 'service',
        'companyname' => 'companyname',
        'company_registration' => 'company_registration',
        'regional' => 'regional',
        'priority' => 'priority'
    ];

    foreach ($filters as $request_key => $db_field) {
        $value = !empty($_REQUEST[$request_key]) ? $_REQUEST[$request_key] : '';
        if (!empty($value)) {
            if (in_array($db_field, ['fname', 'perpetrator', 'affected', 'service'])) {
                $condition .= " AND a.$db_field LIKE '%$value%' ";

            }else if(in_array($db_field, ['district','village','companyname','company_registration','regional','customertype'])) {
                $condition .= " AND a.$db_field = '$value' ";
            } else {
                $condition .= " AND p.$db_field = '$value' ";
            }
        }
    }
    // added condition related to escalation status and level[vastvikta][12-05-2025]

    if ($casetype !== '') {
        $condition .= " AND p.vCaseType = '$casetype' ";
    }

    if($esc_status==4){
      $condition .= "AND escalate_status>'0'";
    }
    if(!empty($esc_status)&&$esc_status!=4){
      $condition .= "AND escalation_level = '$esc_status'";
    }
    // Base query
    $base_query = "SELECT p.*, a.* 
                   FROM $db.web_problemdefination AS p 
                   LEFT JOIN $db.web_accounts AS a ON a.AccountNumber = p.vCustomerID 
                   WHERE p.ticketid != '' $condition";

    // For total data count
    $total_res = mysqli_query($link, $base_query);
    $total_row = mysqli_num_rows($total_res);

    $pagination_query = $base_query . " GROUP BY p.ticketid ORDER BY p.iPID DESC LIMIT $start, $limit";

    // For filtering data
    $filtered_res = mysqli_query($link, $pagination_query) or die(mysqli_error($link));
    $total_filtered = mysqli_num_rows($filtered_res);

    // Fetch filtered data
    $data = array();
    while ($row = mysqli_fetch_array($filtered_res)) {
      // added code for the ticket link  and priority and age [vastvikta[23-12-2024]]
      $ticket_id = $row['ticketid'];
      $token = base64_encode('web_case_detail');
      $mid = base64_encode($ticket_id);
      $ticket_link = '<a href="helpdesk_index.php?token=' . $token . '&id=' . $mid . '" style="padding-left: 10px;" target="_blank">' . $ticket_id . '</a>';
  
      if($row['priority_user']=='1'){
        $priority = "Priority";
      }
      else{
        $priority = "Non Priority";
      }
      $date1=  $row['d_createDate'];
      $date2 = date("Y-m-d H:i:s");
      $date3= $row['d_updateTime'];
      $status=$row['iCaseStatus'];
        $sub_array = array();
        $sub_array[] = $ticket_link;
        $sub_array[] = $row['d_createDate'];
        $sub_array[] = dateDiffInDaysaAging($date1,$date2,$status,$date3);
        $sub_array[] = $row['createdate'];
        $sub_array[] = $row['updatedate'];
        $sub_array[] = ticketstatus($row['iCaseStatus']);
        $sub_array[] = source($row['i_source']);
        $sub_array[] = $row['fname'];
        $sub_array[] = agentname($row['i_CreatedBY']);
        $sub_array[] = category($row['vCategory']);
        $sub_array[] = subcategory($row['vSubCategory']);
        $sub_array[] = department($row['vProjectID']);
        $sub_array[] = city($row['district']);
        $sub_array[] = village($row['v_Village']);
        $sub_array[] = $priority;
        $sub_array[] = wordwrap($row['vRemarks']);
        $data[] = $sub_array;
    }

    // Return JSON formatted data
    $output = array(
      "cases" => $base_query,
        "draw" => $draw,
        "recordsTotal" => $total_row,
        "recordsFiltered" => $total_row,
        "data" => $data
    );

    echo json_encode($output);
}
function export_case_data() {
  global $db, $link;

  header('Content-Type: application/json');

  $selectedColumns = isset($_POST['selectedColumns']) ? $_POST['selectedColumns'] : [];
  $exportType = $_POST['exportType'] ?? 'csv';
  $startdatetime = !empty($_POST['startdatetime']) ? $_POST['startdatetime'] : date('Y-m-01 00:00:00');
  $enddatetime = !empty($_POST['enddatetime']) ? $_POST['enddatetime'] : date('Y-m-d 23:59:59');

  $condition = "";
  $from = date('Y-m-d H:i:s', strtotime($startdatetime));
  $to = date('Y-m-d H:i:s', strtotime($enddatetime));
  $condition .= " AND p.d_createDate >= '$from' AND p.d_createDate <= '$to' ";

  $filters = [
      'category' => 'vCategory', 'subcategory' => 'vSubCategory', 'customertype' => 'customertype',
      'casetype' => 'vCaseType', 'source' => 'i_source', 'status' => 'iCaseStatus', 'casee' => 'ticketid',
      'fname' => 'fname', 'agent' => 'i_CreatedBY', 'vTypeOfcaller' => 'vTypeOfcaller', 'assign' => 'iAssignTo',
      'district' => 'district', 'village' =>'village', 'vProject' => 'vProjectID', 'perpetrator' => 'perpetrator',
      'affected' => 'affected', 'service' => 'service', 'companyname' => 'companyname',
      'company_registration' => 'company_registration', 'regional' => 'regional', 'priority' => 'priority'
  ];

  foreach ($filters as $request_key => $db_field) {
      $value = $_POST[$request_key] ?? '';
      if (!empty($value)) {
          if (in_array($db_field, ['fname', 'perpetrator', 'affected', 'service'])) {
              $condition .= " AND a.$db_field LIKE '%" . mysqli_real_escape_string($link, $value) . "%' ";
          } else if (in_array($db_field, ['district','village','companyname','company_registration','regional','customertype'])) {
              $condition .= " AND a.$db_field = '" . mysqli_real_escape_string($link, $value) . "' ";
          } else {
              $condition .= " AND p.$db_field = '" . mysqli_real_escape_string($link, $value) . "' ";
          }
      }
  }

  if (!empty($_POST['casetype'])) {
      $condition .= " AND p.vCaseType = '" . mysqli_real_escape_string($link, $_POST['casetype']) . "' ";
  }

  if ($_POST['esc_status'] == 4) {
      $condition .= " AND escalate_status > '0' ";
  } elseif (!empty($_POST['esc_status']) && $_POST['esc_status'] != 4) {
      $condition .= " AND escalation_level = '" . mysqli_real_escape_string($link, $_POST['esc_status']) . "' ";
  }

  $base_query = "SELECT p.*, a.* 
                 FROM $db.web_problemdefination AS p 
                 LEFT JOIN $db.web_accounts AS a ON a.AccountNumber = p.vCustomerID 
                 WHERE p.ticketid != '' $condition 
                 GROUP BY p.ticketid 
                 ORDER BY p.iPID DESC";

  $result = mysqli_query($link, $base_query);
  $data = [];

  while ($row = mysqli_fetch_assoc($result)) {
      $record = [];
      foreach ($selectedColumns as $colIndex) {
        switch ($colIndex) {
          case '0': // Case Id
              $record[] = $row['ticketid']; // or $ticket_link if hyperlink is needed
              break;
          case '1': // Created On
              $record[] = $row['d_createDate'];
              break;
          case '2': // Aged
              $record[] = dateDiffInDaysaAging($row['d_createDate'], date('Y-m-d H:i:s'), $row['iCaseStatus'], $row['d_updateTime']);
              break;
          case '3': // In Progress
              $record[] = $row['createdate'];
              break;
          case '4': // Closed On
              $record[] = $row['updatedate'];
              break;
          case '5': // Status
              $record[] = ticketstatus($row['iCaseStatus']);
              break;
          case '6': // Complaint Origin
              $record[] = source($row['i_source']);
              break;
          case '7': // Name
              $record[] = $row['fname'];
              break;
          case '8': // Agent
              $record[] = agentname($row['i_CreatedBY']);
              break;
          case '9': // Category
              $record[] = category($row['vCategory']);
              break;
          case '10': // Sub Category
              $record[] = subcategory($row['vSubCategory']);
              break;
          case '11': // Department
              $record[] = department($row['vProjectID']);
              break;
          case '12': // County
              $record[] = city($row['district']);
              break;
          case '13': // Sub County
              $record[] = village($row['v_Village']);
              break;
          case '14': // Priority/Non Priority
              $record[] = $row['priority_user'] == '1' ? 'Priority' : 'Non Priority'; // or use $priority if already calculated
              break;
          case '15': // Remark
              $record[] = wordwrap($row['vRemarks']);
              break;
      }
    }      
      $data[] = $record;
  }

  echo json_encode($data);
}




/**
 * Author: Ritu Modi
 * Date: 24-01-2024
 * 
 * Function to fetch customer report with filter options.
 *
 * This function retrieves customer information from the database based on specified filters.
 * Filters include start and end datetime range, phone number, gender, and district.
 *
 * @return mysqli_result|false The result set from the database query, or false on failure.
 */
function get_date_customer(){
  global $db,$link;
  $query = "SELECT MAX(createddate) as last_date FROM $db.web_accounts";
 
  $result = mysqli_query($link, $query);
  $row = mysqli_fetch_assoc($result);
  
  if ($row && $row['last_date']) {
      $last_date = $row['last_date']; // Last record date
      $end_date = date('Y-m-d', strtotime($last_date)); // Set End Date
      $month = date('m', strtotime($last_date)); // Get month
      $year = date('Y', strtotime($last_date)); // Get year
      $start_date = "$year-$month-01"; // Set Start Date as 01-month-year
  } else {
      $start_date = date('Y-m-01'); // First day of the current month
$end_date = date('Y-m-d');    // Current date
 
  }

  return [
      'start_date' => $start_date,
      'end_date' => $end_date
  ];
}

function view_customer_report() {
    global $db, $link;
    $column = array('fname', 'phone', 'mobile', 'email', 'address', 'v_Location', 'district', 'fbhandle', 'twitterhandle', 'createddate');
    $filter_query = "";

    // Retrieve and sanitize input parameters
    $startdatetime = !empty($_POST['startdatetime']) ? mysqli_real_escape_string($link, $_POST['startdatetime']) : '';
    $enddatetime = !empty($_POST['enddatetime']) ? mysqli_real_escape_string($link, $_POST['enddatetime']) : '';
    $phone = !empty($_POST['phone']) ? mysqli_real_escape_string($link, $_POST['phone']) : '';
    $gender = !empty($_POST['gender']) ? mysqli_real_escape_string($link, $_POST['gender']) : '';
    $district = !empty($_POST['district']) ? mysqli_real_escape_string($link, $_POST['district']) : '';

    // Date filtering
    if ($startdatetime != '' && $enddatetime != '') {
        $from = date('Y-m-d', strtotime($startdatetime));
        $to = date('Y-m-d', strtotime($enddatetime));
        $filter_query .= " AND updatedate >= '$from 00:00:00' AND updatedate <= '$to 23:59:59' ";
    }

    // Additional filters
    if ($phone != '') {
        $filter_query .= " AND phone LIKE '%$phone%'";
    }

    if ($gender != '') {
        $filter_query .= " AND gender='$gender'";
    }

    if ($district != '') {
        $filter_query .= " AND district='$district'";
    }

    // Order by clause
    if (isset($_POST['order'])) {
        $order_column_index = $_POST['order']['0']['column'];
        $order_column = isset($column[$order_column_index]) ? $column[$order_column_index] : 'fname';
        $order_dir = mysqli_real_escape_string($link, $_POST['order']['0']['dir']);
        $filter_query .= " ORDER BY $order_column $order_dir ";
    } else {
        $filter_query .= " ORDER BY fname ASC ";
    }

    // Limit and offset for pagination
    $start = mysqli_real_escape_string($link, $_POST['start']);
    $length = mysqli_real_escape_string($link, $_POST['length']);
    $limit = "LIMIT $start, $length";

    // Base query
    $base_query = "SELECT * FROM $db.web_accounts WHERE AccountNumber != ''";

    // Total records count (without filtering)
    $total_row_query = $base_query;
    $total_row_result = mysqli_query($link, $total_row_query);
    if (!$total_row_result) {
        die('Error: ' . mysqli_error($link));
    }
    $total_row = mysqli_num_rows($total_row_result);

    // Filtered records query
    $filtered_query = $base_query . $filter_query . " " . $limit;
    $filtered_res = mysqli_query($link, $filtered_query);
    if (!$filtered_res) {
        die('Error: ' . mysqli_error($link));
    }

    // For filtered data count
    $filtered_count_query = "SELECT COUNT(*) as count FROM $db.web_accounts WHERE AccountNumber != ''" . $filter_query;
    $filtered_count_result = mysqli_query($link, $filtered_count_query);
    if (!$filtered_count_result) {
        die('Error: ' . mysqli_error($link));
    }
    $total_filtered = mysqli_fetch_assoc($filtered_count_result)['count'];

    // Fetch filtered data
    $data = array();
    $id = $start + 1; // Start numbering from the current page's offset
    while ($row = mysqli_fetch_array($filtered_res)) {
        $sub_array = array();
        $sub_array[] = $id++;
        $sub_array[] = $row['fname'];
        $sub_array[] = $row['phone'];
        $sub_array[] = $row['mobile'];
        $sub_array[] = $row['email'];
        $sub_array[] = $row['address'];
        $sub_array[] = $row['v_Location'];
        $sub_array[] = city($row['district']);  // Assuming city() is a valid function
        $sub_array[] = $row['fbhandle'];
        $sub_array[] = $row['twitterhandle'];
        $sub_array[] = $row['createddate'];
        $data[] = $sub_array;
    }

    // Return JSON formatted data
    $output = array(
      "sql_customer"=> $filtered_query,
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => $total_filtered,
        "recordsFiltered" => $total_filtered,
        "data" => $data
    );

    echo json_encode($output);
}
/**
 * Author: Ritu Modi
 * Date: 25-01-2024
 * 
 * Function to view report overview with filter option
 * 
 * This function retrieves report overview data from the database based on the provided filters.
 * The function supports filtering by time period, start datetime, and end datetime.
 * It also determines the title of the report based on the request parameters.
 * 
 * @return mysqli_result|bool Returns the result of the SQL query or false if the query fails.

 */
function get_date_overview(){
  global $db,$link;
 // Get the last record's date
 $query = "SELECT MAX(d_createDate) AS last_date FROM $db.web_problemdefination";

 $result = mysqli_query($link, $query);
 $row = mysqli_fetch_assoc($result);
 
 if ($row && $row['last_date']) {
     $last_date = $row['last_date']; // Last record date
     $end_date = date('Y-m-d', strtotime($last_date)); // Set End Date
     $month = date('m', strtotime($last_date)); // Get month
     $year = date('Y', strtotime($last_date)); // Get year
     $start_date = "$year-$month-01"; // Set Start Date as 01-month-year
 } else {
     $start_date = date('Y-m-01'); // First day of the current month
$end_date = date('Y-m-d');    // Current date
 
 }

 return [
     'start_date' => $start_date,
     'end_date' => $end_date
 ];
}
function view_report_overview(){
    global $db, $link; // Global database connection variables
    
    $column = array('i_source', 'iCaseStatus', 'total');
    
    // Initialize variables
    $timeperiod = isset($_POST['timeperiod']) ? $_POST['timeperiod'] : '';
    $startdatetime = isset($_POST['startdatetime']) ? $_POST['startdatetime'] : '';
    $enddatetime = isset($_POST['enddatetime']) ? $_POST['enddatetime'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';    
    $condition = ''; // Initialize an empty condition string
    
    // Date filtering logic
    if ($timeperiod != "") {
        $from = date('Y-m-d', strtotime(getFromDate('from', $timeperiod)));
        $to = date('Y-m-d', strtotime(getFromDate('to', $timeperiod)));
        $condition .= " AND d_createDate >= '$from 00:00:00' AND d_createDate <= '$to 23:59:59' ";
    } elseif ($startdatetime != '' && $enddatetime != '') {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $condition .= " AND d_createDate >= '$from' AND d_createDate <= '$to' ";
    }
     
    if(!empty($status)){
      $condition .= "AND iCaseStatus = '$status'";
    }
    
    // Determine the title based on the request parameters
    if ($_POST['case_issues'] > 0) {
        $title = 'Issue Report';
    } elseif ($_POST['type'] == 2) {
        $title = 'PAP';
        $case_type = true;
    } elseif ($_POST['type'] == 3) {
        $title = 'PS';
        $case_type = true;
    } else {
        $title = 'PR';
    }
    // updated code according to status and  total records filtered [vastvikta][16-05-2025]
    // Define the statuses to filter
    $statuses = "1, 2, 3, 4, 8";

    // Query to get paginated data
    $baseQuery = "SELECT i_source, iCaseStatus, COUNT(i_source) AS total 
                  FROM `$db`.`web_problemdefination` 
                  WHERE iCaseStatus IN ($statuses) $condition 
                  GROUP BY i_source, iCaseStatus";


    // Apply pagination if requested
    $limitClause = '';
    if ($_POST["length"] != -1) {
        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
        $limitClause = " LIMIT $start, $length";
    }

    // Query for filtered + paginated result
    $filteredQuery = $baseQuery . $limitClause;
    $result = mysqli_query($link, $filteredQuery);

    // Get total filtered records (after filtering but before pagination)
    $filteredCountQuery = "SELECT COUNT(*) AS filtered_count FROM (
                            $baseQuery
                          ) AS grouped";
    $filteredResult = mysqli_query($link, $filteredCountQuery);
    $filteredRecords = mysqli_fetch_assoc($filteredResult)['filtered_count'];

    // Prepare data
    $data = array();
    $count = $_POST["start"]; // For proper pagination row count

    while ($row = mysqli_fetch_array($result)) {
        $count++;
        $i_source = source($row['i_source']);          // Your helper function
        $iCaseStatus = ticketstatus($row['iCaseStatus']); // Your helper function
        $total = $row['total'];

        $data[] = array($count, $i_source, $iCaseStatus, $total);
    }

    // JSON response for DataTables
    $output = array(
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => intval($filteredRecords),
        "recordsFiltered" => intval($filteredRecords),
        "data" => $data
    );

    echo json_encode($output);
}

/**
 * Author: Ritu Modi
 * Date: 29-01-2024
 * 
 * Function to view audit report with filter options.
 *
 * This function retrieves audit report data from the database based on specified filter options,
 * such as time period, start datetime, end datetime, agent, and ticket ID. It constructs an SQL query
 * to fetch audit history records and returns the result.
 *
 * @return mysqli_result|bool Returns the result of the SQL query or false if the query fails.
 */
function get_date_audit_report(){
  global $db,$link;
 // Get the last record's date
 $query = "SELECT MAX(created_on) AS last_date FROM $db.web_audit_history";

 $result = mysqli_query($link, $query);
 $row = mysqli_fetch_assoc($result);
 
 if ($row && $row['last_date']) {
     $last_date = $row['last_date']; // Last record date
     $end_date = date('Y-m-d', strtotime($last_date)); // Set End Date
     $month = date('m', strtotime($last_date)); // Get month
     $year = date('Y', strtotime($last_date)); // Get year
     $start_date = "$year-$month-01"; // Set Start Date as 01-month-year
 } else {
     $start_date = date('Y-m-01'); // First day of the current month
$end_date = date('Y-m-d');    // Current date
 
 }

 return [
     'start_date' => $start_date,
     'end_date' => $end_date
 ];
}
function view_audit_report() {
    global $db, $link;

    // Columns to fetch from the database
    $columns = array('created_on', 'user_id', 'comments', 'ip_address');

    // Initialize variables to store time period and date range
    $agent = !empty($_POST['agent']) ? mysqli_real_escape_string($link, $_POST['agent']) : '';
    $startdatetime = !empty($_POST['startdatetime']) ? mysqli_real_escape_string($link, $_POST['startdatetime']) : '';
    $enddatetime = !empty($_POST['enddatetime']) ? mysqli_real_escape_string($link, $_POST['enddatetime']) : '';

    $condition = " WHERE ip_address != ''"; // Initial condition with IP address check

    // Additional conditions based on agent and date range
    if (!empty($agent)) {
        $condition .= " AND user_id = '$agent'";
    }

    // Date range filter
    if (!empty($startdatetime) && !empty($enddatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $condition .= " AND created_on BETWEEN '$from' AND '$to'";
    } else {
        if (!empty($startdatetime)) {
            $from = date('Y-m-d H:i:s', strtotime($startdatetime));
            $condition .= " AND created_on >= '$from'";
        } else if (!empty($enddatetime)) {
            $to = date('Y-m-d H:i:s', strtotime($enddatetime));
            $condition .= " AND created_on <= '$to'";
        }
    }

    // Order by clause
    if (isset($_POST['order'])) {
        $order_column = $columns[$_POST['order'][0]['column']];
        $order_dir = $_POST['order'][0]['dir'];
        $condition .= " ORDER BY $order_column $order_dir";
    } else {
        $condition .= " ORDER BY created_on DESC";
    }

    // Limit clause
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : -1;
    $limit = ($length != -1) ? " LIMIT $start, $length" : "";

    // Construct the SQL query to fetch audit history data
    $query = "SELECT * FROM $db.web_audit_history $condition $limit";

    // Execute the query
    $audit_query = mysqli_query($link, $query);

    // Check for query execution success
    if (!$audit_query) {
        die('Error in SQL query: ' . mysqli_error($link));
    }

    // Fetch total count of filtered records
    $total_count_query = "SELECT COUNT(*) AS total FROM $db.web_audit_history $condition";
    $total_count_result = mysqli_query($link, $total_count_query);

    // Check for total count query execution success
    if (!$total_count_result) {
        die('Error in SQL query: ' . mysqli_error($link));
    }

    $total_row = mysqli_fetch_assoc($total_count_result)['total'];
    $data = array();
    $count = $start + 1;

    while ($row = mysqli_fetch_array($audit_query)) {
        $created_on = $row['created_on'];
        $user_id = displayagentname($row['user_id']);
        $comments = $row['comments'];
        $ip_address = $row['ip_address'];

        $sub_array = array();
        $sub_array[] = $count;
        $sub_array[] = $created_on;
        $sub_array[] = $user_id;
        $sub_array[] = $comments;
        $sub_array[] = $ip_address;

        $data[] = $sub_array;
        $count++;
    }


    // Return JSON formatted data
    $output = array(
        "sql_audit"=>$total_count_query,
        "draw" => isset($_POST["draw"]) ? intval($_POST["draw"]) : 0,
        "recordsTotal" => $total_row,
        "recordsFiltered" => $total_row,
        "data" => $data
    );

    echo json_encode($output);
}
function get_active_agents($db, $link) {
    $sql = "SELECT AtxUserID, AtxUserName FROM $db.uniuserprofile WHERE AtxUserStatus='1' ORDER BY AtxUserName ASC";
    return mysqli_query($link, $sql);
}


/**
 * Author: Ritu Modi
 * Date: 01-02-2024
 * 
 * Function to view frequent caller report with filter options.
 *
 * This function retrieves data of frequent callers from the database based on specified filter options,
 * such as time period, start datetime, end datetime, and agent. It constructs an SQL query
 * to fetch the report and returns the result.
 *
 * @return mysqli_result|bool Returns the result of the SQL query or false if the query fails.
 */
function get_date_frequent_caller(){
  global $db,$link;
 // Get the last record's date
 $query = "SELECT MAX(web_problemdefination.d_createDate) AS last_date FROM $db.web_problemdefination";

 $result = mysqli_query($link, $query);
 $row = mysqli_fetch_assoc($result);
 
 if ($row && $row['last_date']) {
     $last_date = $row['last_date']; // Last record date
     $end_date = date('Y-m-d', strtotime($last_date)); // Set End Date
     $month = date('m', strtotime($last_date)); // Get month
     $year = date('Y', strtotime($last_date)); // Get year
     $start_date = "$year-$month-01"; // Set Start Date as 01-month-year
 } else {
     $start_date = date('Y-m-01'); // First day of the current month
$end_date = date('Y-m-d');    // Current date
 
 }

 return [
     'start_date' => $start_date,
     'end_date' => $end_date
 ];
}
function view_frequent_caller() {
    global $db, $link;
    $column = array('fname', 'phone', 'email', 'address', 'total_complaint');

    // Initialize variables to store time period and date range
    $timeperiod = !empty($_POST['timeperiod']) ? $_POST['timeperiod'] : '';
    $startdatetime = !empty($_POST['sttartdatetime']) ? $_POST['sttartdatetime'] : '';
    $enddatetime = !empty($_POST['enddatetime']) ? $_POST['enddatetime'] : '';
    $agent = !empty($_POST['agent']) ? $_POST['agent'] : '';

    $condition = ""; // Initialize condition string

    // Build condition based on 'timeperiod' or specific 'startdatetime' and 'enddatetime'
    if ($timeperiod != "") {
        $from = date('Y-m-d 00:00:00', strtotime(getFromDate('from', $timeperiod)));
        $to = date('Y-m-d 23:59:59', strtotime(getFromDate('to', $timeperiod)));
        $condition .= " AND web_problemdefination.d_createDate >= '$from' AND web_problemdefination.d_createDate <= '$to'"; 
    } elseif ($startdatetime != '' && $enddatetime != '') {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $condition .= " AND web_problemdefination.d_createDate >= '$from' AND web_problemdefination.d_createDate <= '$to'"; 
    } else {
        if ($startdatetime != '') {
            $from = date('Y-m-d H:i:s', strtotime($startdatetime));
            $condition .= " AND web_problemdefination.d_createDate >= '$from'"; 
        } 
        if ($enddatetime != '') {
            $to = date('Y-m-d H:i:s', strtotime($enddatetime));
            $condition .= " AND web_problemdefination.d_createDate <= '$to'"; 
        }
    }

    // Ensure the enddatetime does not exceed the current date and time
    $current_datetime = date('Y-m-d H:i:s');
    if ($enddatetime == '' || strtotime($enddatetime) > strtotime($current_datetime)) {
        $condition .= " AND web_problemdefination.d_createDate <= '$current_datetime'";
    }

    // Order by clause
    if (isset($_POST['order'])) {
        $order_column = $column[$_POST['order'][0]['column']];
        $order_dir = $_POST['order'][0]['dir'];
        $order_clause = " ORDER BY $order_column $order_dir";
    } else {
        $order_clause = " ORDER BY total_complaint DESC";
    }

    // Limit clause
    $limit_clause = "";
    if ($_POST["length"] != -1) {
        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
        $limit_clause = " LIMIT $start, $length";
    }

    // Construct the SQL query to fetch frequent caller report
    $query = "SELECT COUNT(*) AS total_complaint, vCustomerID, a.fname, a.phone, a.email, a.address
              FROM $db.web_problemdefination
              JOIN $db.web_accounts a ON a.AccountNumber = web_problemdefination.vCustomerID
              WHERE vCustomerID != 0 $condition
              GROUP BY vCustomerID
              $order_clause
              $limit_clause";

    // Execute the query
    $caller_query = mysqli_query($link, $query);

    // Total number of records without limit
    $total_count_query = "SELECT COUNT(DISTINCT vCustomerID) AS total 
                          FROM $db.web_problemdefination 
                          WHERE vCustomerID != 0 $condition";
    $total_count_result = mysqli_query($link, $total_count_query);
    $total_row = mysqli_fetch_assoc($total_count_result)['total'];

    $data = array();

    while ($row = mysqli_fetch_array($caller_query)) {
        $sub_array = array();
        $sub_array[] = $row['fname'];
        $sub_array[] = $row['phone'];
        $sub_array[] = $row['email'];
        $sub_array[] = $row['address'];
        $sub_array[] = $row['total_complaint'];
        $data[] = $sub_array;
    }

    // Return JSON formatted data
    $output = array(
        "sql_fc"=>$total_count_query,
        "draw" => isset($_POST["draw"]) ? intval($_POST["draw"]) : 0,
        "recordsTotal" => $total_row,
        "recordsFiltered" => $total_row,
        "data" => $data
    );

    echo json_encode($output);
}


/**
 * Author: Ritu Modi
 * Date: 02-02-2024
 * 
 * Function to view frequent ticket report with filter options.
 *
 * This function retrieves data of frequent tickets from the database based on specified filter options,
 * such as time period, start datetime, end datetime, and agent. It constructs an SQL query
 * to fetch the report and returns the result.
 *
 * @return mysqli_result|bool Returns the result of the SQL query or false if the query fails.
 */

function view_frequent_ticket() {
  global $db, $link;

  // Columns for ordering
  $columns = array('vCategory', 'vSubCategory', 'total_complaint');

  // Default values for variables
  $timeperiod = isset($_REQUEST['timeperiod']) ? $_REQUEST['timeperiod'] : '';
  $startdatetime = isset($_POST['startdatetime']) ? $_POST['startdatetime'] : '';
  $enddatetime = isset($_POST['enddatetime']) ? $_POST['enddatetime'] : '';

  // Initialize $where variable for SQL conditions
  $where = ' WHERE vCustomerID != 0';

  // Build SQL condition based on 'timeperiod' or specific 'startdatetime' and 'enddatetime'
  if (!empty($timeperiod)) {
    $from = date('Y-m-d', strtotime(getFromDate('from', $timeperiod)));
    $to = date('Y-m-d', strtotime(getFromDate('to', $timeperiod)));
    $where .= " AND web_problemdefination.d_createDate >= '$from 00:00:00' AND web_problemdefination.d_createDate <= '$to 23:59:59'";
  } elseif (!empty($startdatetime) && !empty($enddatetime)) {
    $from = date('Y-m-d H:i:s', strtotime($startdatetime));
    $to = date('Y-m-d H:i:s', strtotime($enddatetime));
    $where .= " AND web_problemdefination.d_createDate >= '$from' AND web_problemdefination.d_createDate <= '$to'";
  } elseif (!empty($startdatetime)) {
    $from = date('Y-m-d H:i:s', strtotime($startdatetime));
    $where .= " AND web_problemdefination.d_createDate >= '$from'";
  } elseif (!empty($enddatetime)) {
    $to = date('Y-m-d H:i:s', strtotime($enddatetime));
    $where .= " AND web_problemdefination.d_createDate <= '$to'";
  }

  // Order by column specified in POST or default order by total_complaint DESC
  $order = '';
  if (isset($_POST['order'])) {
    $order_column = intval($_POST['order'][0]['column']);
    $order_dir = $_POST['order'][0]['dir'];
    if (array_key_exists($order_column, $columns)) {
      $order = ' ORDER BY ' . $columns[$order_column] . ' ' . $order_dir;
    }
  } else {
    $order = ' ORDER BY total_complaint DESC';
  }

  // Limit results based on DataTables request
  $limit = '';
  if (isset($_POST['length']) && $_POST['length'] != -1) {
    $limit = ' LIMIT ' . intval($_POST['start']) . ', ' . intval($_POST['length']);
  }

  // Construct the SQL query to fetch frequent ticket report
  $query = "SELECT COUNT(*) AS total_complaint, vCategory, vSubCategory 
            FROM $db.web_problemdefination 
            $where 
            GROUP BY vCategory, vSubCategory 
            $order 
            $limit";

  // Execute the query
  $ticket_query = mysqli_query($link, $query);
  if (!$ticket_query) {
    die('Error executing query: ' . mysqli_error($link));
  }

  // Fetch data and prepare for JSON response
  $data = array();
  $id = 0;
  while ($row = mysqli_fetch_array($ticket_query)) {
    $id++;
    $vCategory = category($row['vCategory']); // Assuming category() function is defined
    $vSubCategory = subcategory($row['vSubCategory']); // Assuming subcategory() function is defined
    $total_complaint = $row['total_complaint'];

    $data[] = array(
      $id,
      $vCategory,
      $vSubCategory,
      $total_complaint
    );
  }

  // Return JSON formatted data
  $output = array(
    "draw" => isset($_POST["draw"]) ? intval($_POST["draw"]) : 0,
    "recordsTotal" => count($data),
    "recordsFiltered" => count($data), // Since no filtering applied on server side, same as recordsTotal
    "data" => $data
  );

  echo json_encode($output);
}

function calculateTotalPercentage($from, $to) {
  global $db, $link;
    // $no_of_survey_total = mysqli_fetch_assoc(mysqli_query($link, "SELECT count(*) as total FROM $db.`tbl_civrs_cdr` where Connect_time>='$from' and Connect_time<='$to'"));
    // $total = mysqli_num_rows(mysqli_query($link, $total_score));
    // $total_percentage = $total / ($no_of_survey_total['total'] * 5) * 100;
    // return $total_percentage;
}
/**
 * 
 * Author: Ritu Modi
 * Date: 05-02-2024
 * Fetches CSAT (Customer Satisfaction) and DSAT (Dissatisfaction) report summary.
 *
 * This function retrieves CSAT and DSAT scores for each agent within a specified time period.
 * It calculates the total score for each agent based on the specified time period or date range.
 *
 * @return mixed Returns the result of the SQL query containing the total CSAT and DSAT scores grouped by agent.
 * Please do not modify this file without permission.
 */

 function get_date_csat_dsat(){
  global $db, $link;

  // Get the last record's date
  $query = "SELECT MAX(Connect_time) AS last_date FROM $db.tbl_civrs_cdr";
  $result = mysqli_query($link, $query);
  $row = mysqli_fetch_assoc($result);
  
  if ($row && $row['last_date']) {
      $last_date = $row['last_date']; // Last record date
      $end_date = date('Y-m-d', strtotime($last_date)); // Set End Date
      $month = date('m', strtotime($last_date)); // Get month
      $year = date('Y', strtotime($last_date)); // Get year
      $start_date = "$year-$month-01"; // Set Start Date as 01-month-year
  } else {
      $start_date = date('Y-m-01'); // First day of the current month
$end_date = date('Y-m-d');    // Current date
 
  }

  return [
      'start_date' => $start_date,
      'end_date' => $end_date
  ];
}

function view_CSAT_DSAT_summary(){
    global $db, $link;
    $column = array('AgentName', 'total');

    // Initialize time period to 1 if startdatetime and enddatetime are not set
    if(empty($_POST['startdatetime']) && empty($_POST['enddatetime'])){
        $timeperiod = 1;
    } else {
        $startdatetime = isset($_POST['startdatetime']) ? $_POST['startdatetime'] : '';
        $enddatetime = isset($_POST['enddatetime']) ? $_POST['enddatetime'] : '';
    }

    $datefilter = ""; // Initialize date filter

    // changed agentfilter and orderby  to make correct report 
    // Build date filter based on 'timeperiod' or specific 'startdatetime' and 'enddatetime'
    if($timeperiod != "" || !empty($timeperiod)){
        $from = date('Y-m-d', strtotime(getFromDate('from', $timeperiod)));
        $to = date('Y-m-d', strtotime(getFromDate('to', $timeperiod)));
        $datefilter .= " WHERE Connect_time >= '$from 00:00:00' AND Connect_time <= '$to 23:59:59'";
    } else {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        if($startdatetime != '' && $enddatetime != ''){
            $datefilter .= " WHERE Connect_time >= '$from' AND Connect_time <= '$to'";
        }
    }
// updated the query [vastvikta][12-03-2025]
$agentfilter = '';
    // Check if 'agent' is set, add agent filter to date filter
    if(isset($_POST['agent']) && $_POST['agent'] != ''){
        $agent = $_POST['agent'];
        $agentfilter .= " AND AgentName = '$agent'";
    }

    // order by code
    $orderby = '';
    if(isset($_POST['order'])){
        $orderby.= ' ORDER BY ' . $column[$_POST['order'][0]['column']] . ' ' . $_POST['order'][0]['dir'];
    } else {
        $orderby .= ' GROUP BY AgentName';
    }

    // limit code here
    $limit = "";
    if($_POST["length"] != -1){
        $limit = ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
    }

    // Construct the SQL query to fetch CSAT and DSAT report summary
    $csat_query = "SELECT SUM(Dialed_Digit) AS score, AgentName FROM $db.tbl_civrs_cdr";

    // Execute the query
    $res = mysqli_query($link, $csat_query . $datefilter . $agentfilter . $orderby . $limit);
    $total_row = mysqli_num_rows(mysqli_query($link, $csat_query . $datefilter . $agentfilter . $orderby ));

    $data = array();
    $no = 0;

    while($row = mysqli_fetch_array($res)){
        $no++;
        $AgentName = $row['AgentName'];
        
        // Count total surveys for the agent
        $query_total_surveys = "SELECT COUNT(*) AS total FROM $db.tbl_civrs_cdr  $datefilter AND AgentName = '$AgentName' $orderby ";
        $res_total_surveys = mysqli_query($link, $query_total_surveys);
        $row_total_surveys = mysqli_fetch_assoc($res_total_surveys);
        $total_surveys = $row_total_surveys['total'];

        // Calculate total score percentage
        $score_total = $row['score'];
        $total = $score_total / ($total_surveys * 5) * 100;

        $sub_array = array();
        $sub_array[] = $no;
        $sub_array[] = $AgentName;
        $sub_array[] = round($total,2) . '%' ;
        $data[] = $sub_array;
    }

    // Return JSON formatted data
    $output = array(
        "sql_csat_summary
        "=>$csat_query . $datefilter . $agentfilter . $orderby . $limit,
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => $total_row,
        "recordsFiltered" => $total_row, // Assuming no specific filtering criteria for the total count
        "data" => $data
    );

    echo json_encode($output);
}

/**
 * Author: Ritu Modi
 * Date: 03-02-2024
 * 
 * Fetches detailed CSAT (Customer Satisfaction) and DSAT (Dissatisfaction) report with filter options.
 * 
 * This function retrieves detailed CSAT and DSAT records based on specified filters such as time period, type, agent, phone number, and customer email.
 *
 * @return mixed Returns the result of the SQL query containing detailed CSAT and DSAT records based on the provided filters.
 */

function view_CSAT_DSAT_detail(){
  global $db, $link;
  $column = array('Connect_time', 'Type', 'email', 'mobile', 'AgentName', 'Dialed_Digit', 'fname', 'AccountNumber');
  // Initialize time period to 1 if startdatetime and enddatetime are not set
  if($_POST['startdatetime']=='' && $_POST['enddatetime']==''){
    $timeperiod=1;
  }                                                    
  // Check if 'sttartdatetime' is set, otherwise set it to an empty string
  if(!empty($_POST['startdatetime'])){ 
    $startdatetime=$_POST['startdatetime']; 
  } else {  
    $startdatetime=''; 
  }
  // Check if 'enddatetime' is set, otherwise set it to an empty string
  if(!empty($_POST['enddatetime'])){ 
    $enddatetime=$_POST['enddatetime']; 
  } else {  
    $enddatetime=''; 
  }
  $datefilter=""; // Initialize date filter
  // Build date filter based on 'timeperiod' or specific 'startdatetime' and 'enddatetime'
  if($timeperiod!="" || !empty($timeperiod)){
    $from=date('Y-m-d',strtotime(getFromDate('from',$timeperiod)));
    $to=date('Y-m-d',strtotime(getFromDate('to',$timeperiod)));
    $datefilter .=" AND Connect_time>='$from 00:00:00' AND Connect_time<='$to 23:59:59'"; 
  } else {
    $from=date('Y-m-d H:i:s',strtotime($startdatetime));
    $to=date('Y-m-d H:i:s',strtotime($enddatetime));
    if($startdatetime!='' && $enddatetime!=''){  
      $datefilter .=" AND Connect_time>='$from' AND Connect_time<='$to'"; 
    }
  }
  // Check if 'Type' is set, add type filter to date filter
  if($_POST['Type']!=''){
    if($_POST['Type']==1) $datefilter .=" AND tbl_civrs_cdr.Type=1";
    if($_POST['Type']==2) $datefilter .=" AND tbl_civrs_cdr.Type=2";
    if($_POST['Type']==3) $datefilter .=" AND tbl_civrs_cdr.Type=3";
    if($_POST['Type']==4) $datefilter .=" AND tbl_civrs_cdr.Type=4";
  }
  // Check if 'agent' is set, add agent filter to date filter
  if($_POST['agent']!=''){
    $datefilter .=" AND AgentName='".$_POST['agent']."'";
  }
  // Check if 'Phone_Number' is set, add phone number filter to date filter
  if($_POST['Phone_Number']!=''){
    $datefilter .=" AND Phone_Number='".$_POST['Phone_Number']."'";
  }
  // Check if 'Customer_email' is set, add customer email filter to date filter
  if($_POST['Customer_email']!=''){
    $datefilter .=" AND Customer_email='".$_POST['Customer_email']."'";
  }
     // order by code
     $out = '';
    if(isset($_POST['order'])){
     $out .= ' ORDER BY '.$column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
    }
    // limit code here
    if($_POST["length"] != -1){
      $out .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
    }

    // Limit clause
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : -1;
    $limit = ($length != -1) ? " LIMIT $start, $length" : "";

  // Construct the SQL query to fetch detailed CSAT and DSAT report
  $querys = "SELECT * FROM $db.tbl_civrs_cdr WHERE Phone_Number !=''";  
  $total = mysqli_query($link, $querys.$datefilter.$limit);
  // updated the code for total fetched data[vastvikta][11-03-2025]
  $total_row = mysqli_num_rows(mysqli_query($link, $querys.$datefilter));
  $data = array();
 
  $no = $start ;
    while($row = mysqli_fetch_array($total)) {
      $no++;
      $Connect_time=$row['Connect_time'];
      $Type=$row['Type'];
      if($Type==1)
      {
          $Type="IVRS";
      }else if ($Type==2){
          $Type="Email";
      }else if ($Type==4){
        $Type="CHAT";
      }else{
        $Type="SMS";
      }
      //[vastivkta nishad ][2-12-2024]  changes the code for  ticket link to open in the case 
      $ticket_id = $row['ticket_id'];
        if($ticket_id ==0){
          $ticket_link = $ticket_id;
        }
        else{
          $token = base64_encode('web_case_detail');
          $mid = base64_encode($ticket_id);
          $ticket_link = '<a href="helpdesk_index.php?token=' . $token . '&id=' . $mid . '" style="padding-left: 10px;" target="_blank">' . $ticket_id . '</a>';
        }
        
        $fname = getcustomername($row['Customer_id']);
        
        $email= getEmailCustomerName($row['Phone_Number'],1);
        if(empty($row['Customer_email'])){
          $email= getEmailCustomerName($row['Phone_Number'],1);
        }
        else{
          $email=$row['Customer_email'];
        }
        $mobile=$row['Phone_Number'];
        $AgentName=$row['AgentName'];
        $Dialed_Digit=GetScore_Name($row['Dialed_Digit']);
        // $fname= getEmailCustomerName($row['Phone_Number'],2);
        $AccountNumber = $row['AccountNumber'];
        $sub_array = array();
        $sub_array[] = $no;
        $sub_array[] = $Connect_time;
        $sub_array[] = $mobile;
        $sub_array[] = $ticket_link; 
        $sub_array[] = $AgentName;
        $sub_array[] = $fname;
        $sub_array[] = $email;
        $sub_array[] = $Dialed_Digit;
        $sub_array[] = $row['Dialed_Digit'];
        $sub_array[] = $Type;     
        $data[] = $sub_array;

  }
    // Return JSON formatted data
  $output = array(
    
   "sql_csat_detailed" => $querys.$datefilter.$limit,
   "draw"       =>  intval($_POST["draw"]),
   "recordsTotal"   =>  $total_row,
   "recordsFiltered"  =>  $total_row,
   "data"       =>  $data
  );
  echo json_encode($output);
}
/**
 * Author: Ritu Modi
 * Date: 07-02-2024
 * 
 * Fetches voicemail records with filter options.
 *
 * This function retrieves voicemail records from the database based on specified filter options such as startdatetime, enddatetime, and status.
 * It constructs a SQL query to fetch voicemail records with the provided filters and returns the result.
 *
 * @return mixed Returns the result of the SQL query containing voicemail records based on the provided filters.
 */
function get_date_voicemail(){
  global $db_asterisk,$link;
  // Get the last record's date
  $query = "SELECT MAX(voicemailtime) as last_date  FROM $db_asterisk.tbl_cc_voicemails"; 
  $result = mysqli_query($link, $query);
  $row = mysqli_fetch_assoc($result);
  
  if ($row && $row['last_date']) {
      $last_date = $row['last_date']; // Last record date
      $end_date = date('Y-m-d', strtotime($last_date)); // Set End Date
      $month = date('m', strtotime($last_date)); // Get month
      $year = date('Y', strtotime($last_date)); // Get year
      $start_date = "$year-$month-01"; // Set Start Date as 01-month-year
  } else {
      $start_date = date('Y-m-01'); // First day of the current month
$end_date = date('Y-m-d');    // Current date
 
  }

  return [
      'start_date' => $start_date,
      'end_date' => $end_date
  ];
}
function view_voicemail(){
  global $db_asterisk, $link;
    $column = array('callerid', 'client_name', 'voicemailtime', 'flag1','newFileName');

    $status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
    $startdatetime = isset($_REQUEST['startdatetime']) ? $_REQUEST['startdatetime'] : '';
    $enddatetime = isset($_REQUEST['enddatetime']) ? $_REQUEST['enddatetime'] : '';

  // Check if 'startdatetime' is set, otherwise set it to an empty string
  if(!empty($_REQUEST['sttartdatetime'])){ 
    $startdatetime=$_REQUEST['sttartdatetime']; 
  } else {  
    $startdatetime=$startdatetime; 
  }
  // Check if 'enddatetime' is set, otherwise set it to an empty string
  if(!empty($_REQUEST['enddatetime'])){ 
    $enddatetime=$_REQUEST['enddatetime']; 
  } else {  
    $enddatetime=$enddatetime; 
  }
  $condition=""; // Initialize condition variable
  // Add status filter to condition if status is set
  if($status!=""){
    $condition .='AND flag1 ='."'".$status."'";
  }
  // Check if both startdatetime and enddatetime are set, then add date range filter to condition
  if($startdatetime!='' && $enddatetime!='')
  { 
      $from = date('Y-m-d H:i:s', strtotime($startdatetime));
      $to = date('Y-m-d H:i:s', strtotime($enddatetime));
      $condition .="AND voicemailtime BETWEEN '$from' AND '$to'"; 
  }
      // order by code
      if (isset($_POST['order'])) {
        $condition .= ' ORDER BY ' . $column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir'] . ' ';
    } else {
        $condition .= ' ORDER BY id DESC ';
    }
    // limit code here
    if($_POST["length"] != -1){
      $condition .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
    }
  // Construct the SQL query to fetch voicemail records with the specified conditions
  $query = "SELECT * FROM $db_asterisk.tbl_cc_voicemails WHERE callerid!='' $condition";

  // Execute the query
  $total=mysqli_query($link, $query); 
  $total_row = mysqli_num_rows(mysqli_query($link, $query));
    $data = array();
    $count=0;
    while($row = mysqli_fetch_array($total)) {
          $newFileName = substr($row['recordingname'], 0 , (strrpos($row['recordingname'], ".")));
          $file = "../../../voicemail/IVR/DROP/".$newFileName.".WAV";
           // Constructed the "Play" link [vastvikta][27-01-2025]
          //  $play_link = '<a href="#" class="play" id="' . $row['id'] . '" onClick="onPlayWithStatusUpdate(this.id, \'' . SmartFileName_voice($newFileName) . '\')" style="color:#254988">Play</a>';
           // changed condition from and to  or as this  condition file_exists(SmartFileName_voice($newFileName)) is giving false result [vastvikta][27-01-2025]
          if(file_exists($file) || file_exists(SmartFileName_voice($newFileName))){
           $count++;
           $callerid = $row['callerid'];
           $client_name = $row['client_name'];
           $voicemailtime = $row['voicemailtime']; 
           $flag1 = ($row['flag1'] == '1') ? 'read' : 'unread';
           $case_id = $row['case_id'];
           $newFileName = $row['newFileName']; 
             $sub_array = array();
             $sub_array[] = $count;
             $sub_array[] = $callerid;
             $sub_array[] = $client_name;
             $sub_array[] = $voicemailtime;
             $sub_array[] = $flag1;
            //  $sub_array[] = $case_id;
            //  $sub_array[] = $play_link; // Add the "Play" link to the output array
             $data[] = $sub_array;
         }
  }
    // Return JSON formatted data
  $output = array(
   "draw"       =>  intval($_POST["draw"]),
   "recordsTotal"   =>  $total_row,
   "recordsFiltered"  =>  $total_row,
   "data"       =>  $data
  );
  echo json_encode($output); 
}
// added code to update status to read upon playing the voicemail[vastvikta ][27-01-2025]
function updateStatus($id){
  
  global $db_asterisk, $link;
  if (isset($_POST['id'])) {
  $id = $_POST['id'];
    
  // Update the flag1 to 0 (unread) in the database
  $query = "UPDATE $db_asterisk.tbl_cc_voicemails SET flag1 = '1' WHERE id = ?";
  if ($stmt = $link->prepare($query)) {
      $stmt->bind_param("i", $id);  // "i" means integer
      if ($stmt->execute()) {
          echo json_encode(["status" => "success"]);
      } else {
          echo json_encode(["status" => "error", "message" => "Failed to update status"]);
      }
      $stmt->close();
  } else {
      echo json_encode(["status" => "error", "message" => "Query preparation failed"]);
  }
  } else {
    echo json_encode(["status" => "error", "message" => "Invalid ID"]);
  }
}
/**
 * Author: Ritu Modi
 * Date: 08-02-2024
 * 
 * Fetches FCR (First Call Resolution) report with filter options.
 *
 * This function retrieves FCR report from the database based on specified filter options such as startdatetime and enddatetime.
 * It constructs a SQL query to fetch FCR report with the provided filters and returns the result.
 *
 * @return mixed Returns the result of the SQL query containing FCR report based on the provided filters.
 */
function get_date_fcr(){
  global $db,$link;
  // Get the last record's date
  $query = "SELECT MAX(d_createDate) as last_date FROM $db.web_problemdefination AS p 
  LEFT JOIN $db.web_accounts AS a ON a.AccountNumber = p.vCustomerID "; 
  $result = mysqli_query($link, $query);
  $row = mysqli_fetch_assoc($result);
  
  if ($row && $row['last_date']) {
      $last_date = $row['last_date']; // Last record date
      $end_date = date('Y-m-d', strtotime($last_date)); // Set End Date
      $month = date('m', strtotime($last_date)); // Get month
      $year = date('Y', strtotime($last_date)); // Get year
      $start_date = "$year-$month-01"; // Set Start Date as 01-month-year
  } else {
      $start_date = date('Y-m-01'); // First day of the current month
$end_date = date('Y-m-d');    // Current date
 
  }

  return [
      'start_date' => $start_date,
      'end_date' => $end_date
  ];
}
function view_fcr_report(){
    global $db, $link;
    $column = array('ticketid', 'd_createDate', 'd_updateTime', 'iCaseStatus', 'i_source', 'fname', 'age_grp', 'i_CreatedBY', 'vCategory', 'vSubCategory', 'vProjectID', 'vRemarks');


    // Initialize startdatetime and enddatetime to empty strings if not set
    $startdatetime = isset($_REQUEST['startdatetime']) ? $_REQUEST['startdatetime'] : '';
    $enddatetime = isset($_REQUEST['enddatetime']) ? $_REQUEST['enddatetime'] : '';

    if(empty($startdatetime)){
      $startdatetime = date('Y-m-01 00:00:00');
    }
    if(empty($enddatetime)){
      $enddatetime = date('Y-m-d 23:59:59');
    }
    $condition = " WHERE p.ticketid != ''"; // Initialize condition variable

    // Convert startdatetime and enddatetime to date format
    if (!empty($startdatetime) && !empty($enddatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));

        // Add date range filter to condition
        $condition .= " AND p.d_createDate >= '$from' AND p.d_createDate <= '$to'";
    }

    // Add additional condition for FCR (First Call Resolution)
    // removed this condition for  p.d_createDate = p.d_updateTime AND the below code as it was giving no records [vastvikta][11-04-2025]
     // $condition .= " AND p.d_createDate = p.d_updateTime AND p.iCaseStatus = 3";
     $condition .= " AND p.iCaseStatus = 3";

    // order by code
    if (isset($_POST['order'])) {
        $orderColumnIndex = $_POST['order'][0]['column'];
        $orderDir = $_POST['order'][0]['dir'];
        $condition .= ' ORDER BY ' . $column[$orderColumnIndex] . ' ' . $orderDir;
    } else {
        // Default ordering
        $condition .= ' ORDER BY p.iPID DESC';
    }

    // Construct the SQL query to fetch FCR report with the specified conditions
    $query = "SELECT COUNT(*) AS total FROM $db.web_problemdefination AS p 
              LEFT JOIN $db.web_accounts AS a ON a.AccountNumber = p.vCustomerID 
              $condition";

    // Execute query to get total number of records
    $totalResult = mysqli_query($link, $query);
    $totalRow = mysqli_fetch_assoc($totalResult)['total'];

    // limit code here
    $start = isset($_POST['start']) ? $_POST['start'] : 0; // Start index for pagination
    $length = isset($_POST['length']) ? $_POST['length'] : -1; // Number of records per page (-1 means all)

    // Construct main query with pagination
    $mainQuery = "SELECT p.*, a.* 
                  FROM $db.web_problemdefination AS p 
                  LEFT JOIN $db.web_accounts AS a ON a.AccountNumber = p.vCustomerID 
                  $condition";

    if ($length != -1) {
        $mainQuery .= " LIMIT $start, $length";
    }

    // Execute the main query
    $result = mysqli_query($link, $mainQuery);

    $data = array();
    $count = $start; // Start count from the start index for pagination

    while ($row = mysqli_fetch_array($result)) {
        $count++;
        $ticket_id = $row['ticketid'];
        $d_createDate = $row['d_createDate'];
        $d_updateTime = $row['d_updateTime']; 
        $iCaseStatus = $row['iCaseStatus'];
        $i_source = source($row['i_source']);
        $fname = $row['fname'];
        $age_grp = get_age_group($row['age_grp']);
        $i_CreatedBY = agentname($row['i_CreatedBY']);
        $vCategory = category($row['vCategory']);
        $vSubCategory = subcategory($row['vSubCategory']);
        $vProjectID = department($row['vProjectID']);
        $vRemarks = $row['vRemarks'];
        //added the code for the  hyperlink of case created [vastvikta][13-12-2024]
          $token = base64_encode('web_case_detail');
          $mid = base64_encode($ticket_id);
          $ticket_link = '<a href="helpdesk_index.php?token=' . $token . '&id=' . $mid . '" style="padding-left: 10px;" target="_blank">' . $ticket_id . '</a>';
        //[vastvikta][13-12-2024]
          if($iCaseStatus == '3'){
          $case_status = 'Closed';
        }elseif($$iCaseStatus = '1'){
          $case_status = 'Pending';
        }elseif($iCaseStatus = '8'){
          $case_status = 'Resolve';
        }else{
          $case_status = "Undefined";
        }

        $sub_array = array();
        //$sub_array[] = $count;
        $sub_array[] =  $ticket_link;
        $sub_array[] = $d_createDate; 
        $sub_array[] = $d_updateTime;
        $sub_array[] = $case_status;
        $sub_array[] = $i_source;
        $sub_array[] = $fname;
        //removed age group no field of age grp in the web_problemdefinition table  [vastvikta][13-12-2024]
        // $sub_array[] = $age_grp;
        $sub_array[] = $i_CreatedBY;
        $sub_array[] = $vCategory;
        $sub_array[] = $vSubCategory;
        $sub_array[] = $vProjectID;
        $sub_array[] = $vRemarks;

        $data[] = $sub_array;
    }

    // Return JSON formatted data
    $output = array(
      "enddatetime"=>  $enddatetime ,
      "query"=> $mainQuery,
        "draw" => isset($_POST["draw"]) ? intval($_POST["draw"]) : 0,
        "recordsTotal" => $totalRow,
        "recordsFiltered" => $totalRow,
        "data" => $data
    );

    echo json_encode($output);
}

/**
 * 
 * Author: Ritu Modi
 * Date: 13-02-2024
 * 
 * Fetches NPS (Net Promoter Score) report with filter options.
 *
 * This function retrieves NPS report from the database based on specified filter options such as startdatetime, enddatetime, source, mode, category, and subcategory.
 * It constructs a SQL query to fetch NPS report with the provided filters and returns the result.
 *
 * @return mixed Returns the result of the SQL query containing NPS report based on the provided filters.
 */
function get_date_NPS(){
  global $db,$link;
  // Get the last record's date
  $query = "SELECT MAX(a.created_date) as last_date
  FROM $db.tbl_nps AS a  
  JOIN $db.web_problemdefination AS b ON a.ticket_id = b.ticketid;
  "; 

  $result = mysqli_query($link, $query);
  $row = mysqli_fetch_assoc($result);
  
  if ($row && $row['last_date']) {
      $last_date = $row['last_date']; // Last record date
      $end_date = date('Y-m-d', strtotime($last_date)); // Set End Date
      $month = date('m', strtotime($last_date)); // Get month
      $year = date('Y', strtotime($last_date)); // Get year
      $start_date = "$year-$month-01"; // Set Start Date as 01-month-year
  } else {
      $start_date = date('Y-m-01'); // First day of the current month
$end_date = date('Y-m-d');    // Current date
 
  }

  return [
      'start_date' => $start_date,
      'end_date' => $end_date
  ];
}
function view_nps_report() {
    global $db, $link;
    $column = array('ticket_id', 'customer_id', 'phone_number', 'customer_email', 'vCategory', 'vSubcategory', 'feedback_value', 'i_source', 'created_date', '', '', '');

    $startdatetime = !empty($_REQUEST['startdatetime']) ? $_REQUEST['startdatetime'] : '';
    $enddatetime = !empty($_REQUEST['enddatetime']) ? $_REQUEST['enddatetime'] : '';
    $isource = !empty($_POST['source']) ? $_POST['source'] : '';
    $mode = !empty($_POST['mode']) ? $_POST['mode'] : '';
    $category = !empty($_POST['category']) ? $_POST['category'] : '';
    $subcategory = !empty($_POST['subcategory']) ? $_POST['subcategory'] : '';
    
    // removed a.flag = 1 condition because no data was fetched [vastvikta][11-04-2025]
    //$condition = " WHERE a.ticket_id = b.ticketid and a.flag = 1 ";
    $condition = " WHERE a.ticket_id = b.ticketid  ";
    // Add date range filter to condition
    if ($startdatetime != '' && $enddatetime != '') {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $condition .= " AND a.created_date >= '$from' AND a.created_date <= '$to'"; 
    } 

    // Add mode filter to condition if mode is set
    if (!empty($mode)) {
        $condition .= " AND a.media = '$mode'"; 
    }

    // Add source filter to condition if source is set
    if (!empty($isource)) {
        $condition .= " AND b.i_source = '$isource'"; 
    }

    // Add category filter to condition if category is set
    if (!empty($category)) {
        $condition .= " AND b.vCategory = '$category'";
    }

    // Add subcategory filter to condition if subcategory is set
    if (!empty($subcategory)) {
        $condition .= " AND b.vSubCategory = '$subcategory'";
    }

    // Add order by clause
    if (isset($_POST['order'])) {
        $order_column = $column[$_POST['order']['0']['column']];
        $order_dir = $_POST['order']['0']['dir'];
        $condition .= " ORDER BY $order_column $order_dir ";
    } else {
        $condition .= " GROUP BY a.created_date ORDER BY a.id DESC";
    }

    // Add limit clause
    if ($_POST["length"] != -1) {
        $start = $_POST['start'];
        $length = $_POST['length'];
        $condition .= " LIMIT $start, $length";
    }

    // Construct the SQL query to fetch NPS report with the specified conditions
    $query = "SELECT a.*, b.vCategory, b.vSubcategory, b.i_source, b.regional 
              FROM $db.tbl_nps AS a, $db.web_problemdefination AS b" . $condition;

    // Execute the query
    $result = mysqli_query($link, $query);
    $total_row = mysqli_num_rows($result);
    $data = array();
    
    $sno = $start ;

    while ($row = mysqli_fetch_array($result)) {
        $sno++;
        $ticket_id = $row['ticket_id'];
        $customer_id = getfname($row['customer_id']);
        $phone_number = $row['phone_number'];
        $customer_email = $row['customer_email'];
        $vCategory = category($row['vCategory']);
        $vSubCategory = subcategory($row['vSubcategory']);
        $feedback_value = $row['feedback_value'];
        $media = $row['media'];
        $score = get_customer_rate($row['score']);
        $media = $row['media'];
        $i_source = source($row['i_source']);
        $created_date = $row['created_date'];

       
        $token = base64_encode('web_case_detail');
        $mid = base64_encode($ticket_id);
        //[vastivkta nishad ][30-11-2024]  changes the code for  ticket link to open in the case 
        $ticket_link = '<a href="helpdesk_index.php?token=' . $token . '&id=' . $mid . '" style="padding-left: 10px;" target="_blank">' . $ticket_id . '</a>';

        $sub_array = array();
        $sub_array[] = $sno;
        $sub_array[] = $ticket_link; // Add the link in place of the ticket_id
        $sub_array[] = $customer_id;
        $sub_array[] = $phone_number;
        $sub_array[] = $customer_email;
        $sub_array[] = $vCategory;
        $sub_array[] = $vSubCategory;
        $sub_array[] = $feedback_value;
        $sub_array[] = $score;
        $sub_array[] = $media;
        $sub_array[] = $i_source;
        $sub_array[] = $created_date;

        $data[] = $sub_array;
    }

    // Return JSON formatted data
    $output = array(
      "query"=>$query,
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => $total_row,
        "recordsFiltered" => $total_row,
        "data" => $data
    );

    echo json_encode($output);
}

/**
 * Author: Ritu Modi
 * Date: 15-02-2024
 * 
 * Fetches customer effort report with filter options.
 *
 * This function retrieves customer effort report from the database based on specified filter options such as startdatetime, enddatetime, customer_effort, source, mode, category, and subcategory.
 * It constructs a SQL query to fetch customer effort report with the provided filters and returns the result.
 *
 * @return mixed Returns the result of the SQL query containing customer effort report based on the provided filters.
 */
function get_date_ces(){
  global $db,$link;
  // Get the last record's date
  $query = "SELECT
  MAX(a.created_date) AS last_date 
  FROM $db.tbl_customer_effort AS a 
  JOIN $db.web_problemdefination AS b ON a.ticket_id = b.ticketid ";

  $result = mysqli_query($link, $query);
  $row = mysqli_fetch_assoc($result);
  
  if ($row && $row['last_date']) {
      $last_date = $row['last_date']; // Last record date
      $end_date = date('Y-m-d', strtotime($last_date)); // Set End Date
      $month = date('m', strtotime($last_date)); // Get month
      $year = date('Y', strtotime($last_date)); // Get year
      $start_date = "$year-$month-01"; // Set Start Date as 01-month-year
  } else {
      $start_date = date('Y-m-01'); // First day of the current month
$end_date = date('Y-m-d');    // Current date
 
  }

  return [
      'start_date' => $start_date,
      'end_date' => $end_date
  ];
}
function view_customer_effort_report() {
    global $db, $link;

    $column = array('ticket_id', 'customer_id', 'phone_number', 'customer_email', 'vCategory', 'vSubcategory', 'feedback_value', 'i_source', 'created_date', '', '', '');

    // Initialize variables to store filter values
    $startdatetime = !empty($_POST['startdatetime']) ? $_POST['startdatetime'] : '';
    $enddatetime = !empty($_POST['enddatetime']) ? $_POST['enddatetime'] : '';
    $customer_effort = isset($_POST['customer_effort']) ? mysqli_real_escape_string($link, $_POST['customer_effort']) : '';
    $mode = isset($_POST['mode']) ? mysqli_real_escape_string($link, $_POST['mode']) : '';
    $category = isset($_POST['category']) ? mysqli_real_escape_string($link, $_POST['category']) : '';
    $subcategory = isset($_POST['subcategory']) ? mysqli_real_escape_string($link, $_POST['subcategory']) : '';
    $source = isset($_POST['source']) ? mysqli_real_escape_string($link, $_POST['source']) : '';

    // removed the below condition of a.flag=1 as it was giving no records in the current month [vastvikta][11-04-2025]
    //$condition = " WHERE a.ticket_id = b.ticketid AND a.flag = 1 ";
    $condition = " WHERE a.ticket_id = b.ticketid ";

    // Apply date range filter
    if ($startdatetime != '' && $enddatetime != '') {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $condition .= " AND a.created_date >= '$from' AND a.created_date <= '$to' ";
    } else {
        if ($startdatetime != '') {
            $from = date('Y-m-d H:i:s', strtotime($startdatetime));
            $condition .= " AND a.created_date >= '$from' ";
        }
        if ($enddatetime != '') {
            $to = date('Y-m-d H:i:s', strtotime($enddatetime));
            $condition .= " AND a.created_date <= '$to' ";
        }
    }

    // Apply other filters
    if (!empty($customer_effort)) {
        $condition .= " AND a.feedback_value = '$customer_effort' ";
    }

    if (!empty($mode)) {
        $condition .= " AND a.media = '$mode' ";
    }

    if (!empty($source)) {
        $condition .= " AND b.i_source = '$source' ";
    }

    if (!empty($category)) {
        $condition .= " AND b.vCategory = '$category' ";
    }

    if (!empty($subcategory)) {
        $condition .= " AND b.vSubcategory = '$subcategory' ";
    }

    // Order by clause
    $order_by = '';
    if (isset($_POST['order'])) {
        $order_by = ' ORDER BY ' . $column[$_POST['order'][0]['column']] . ' ' . mysqli_real_escape_string($link, $_POST['order'][0]['dir']) . ' ';
    } else {
        $order_by = ' ORDER BY a.id DESC ';
    }

    // Limit clause
    $limit = '';
    if ($_POST["length"] != -1) {
        $start = intval($_POST['start']);
        $length = intval($_POST['length']);
        $limit = ' LIMIT ' . $start . ', ' . $length;
    }

    // Construct the SQL query
    $query = "SELECT a.*, b.vCategory, b.vSubcategory, b.i_source 
              FROM $db.tbl_customer_effort AS a 
              JOIN $db.web_problemdefination AS b ON a.ticket_id = b.ticketid
              $condition 
              GROUP BY a.created_date, a.id 
              $order_by 
              $limit";

    // Execute main query
    $result = mysqli_query($link, $query);

    // Total number of records without limit
    $total_count_query = "SELECT COUNT(*) AS total 
                          FROM $db.tbl_customer_effort AS a 
                          JOIN $db.web_problemdefination AS b ON a.ticket_id = b.ticketid
                          $condition";
    $total_count_result = mysqli_query($link, $total_count_query);
    $total_row = mysqli_fetch_assoc($total_count_result)['total'];

    $data = array();
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : -1;
    
    $sno = $start ;

    // Fetch data for JSON response
    while ($row = mysqli_fetch_array($result)) {
      $ticket_id = $row['ticket_id'];
      $token = base64_encode('web_case_detail');
      $mid = base64_encode($ticket_id);
      $ticket_link = '<a href="helpdesk_index.php?token=' . $token . '&id=' . $mid . '" style="padding-left: 10px;" target="_blank">' . $ticket_id . '</a>';

     
        $sno++;
        $sub_array = array();
        $sub_array[] = $sno;
        $sub_array[] = $ticket_link;
        $sub_array[] = getfname($row['customer_id']);
        $sub_array[] = $row['phone_number'];
        $sub_array[] = $row['customer_email'];
        $sub_array[] = category($row['vCategory']);
        $sub_array[] = subcategory($row['vSubcategory']);
        $sub_array[] = get_customer_effort($row['feedback_value']);
        $sub_array[] = $row['media'];   
        $sub_array[] = source($row['i_source']);
        $sub_array[] = $row['created_date'];
        $data[] = $sub_array;
    }

    // Return JSON formatted data
    $output = array(
        "sql"=>$query,
        "draw" => isset($_POST["draw"]) ? intval($_POST["draw"]) : 0,
        "recordsTotal" => $total_row,
        "recordsFiltered" => $total_row, // Assuming total_row matches filtered count
        "data" => $data
    );

    echo json_encode($output);
}

   
// fetch comment, ip address in audit page
function audit_tempalte_view($agent , $action , $ticket, $data,$case_process_type)
{
    global $link,$db;
   $orginal_jsonVal=$case_process_type;
  if($action=="user_created" || $action=="password_change" || $action=="project_remove" || $action=="project_assigne" || $action=="mail_sent") 
  {
    $description=$data;
  }
    if($action=="customer_update")
  {
     $json_arr=explode("old_new",$case_process_type);
     $arr1=($json_arr[0]);
     $arr2=($json_arr[1]);
     $ar1=json_decode($arr1,true);
     $ar2=json_decode($arr2,true);
        if(is_array($ar1) && is_array($ar2))
        { 
            $strr="Customer Information is updated \n \n";
            if($ar1['fname']!=$ar2['fname'])
            {
                $strr.="Name: ".($ar1['fname'])." to ".($ar2['fname'])."\n";  
            }
            if($ar1['mobile']!=$ar2['mobile'])
            {
                $strr.="Alternate No: ".(($ar1['mobile']=="") ? 'NULL' : $ar1['mobile'])." to ".(($ar2['mobile']=="") ? 'NULL' : $ar2['mobile'])."\n";  
            }
            if($ar1['email']!=$ar2['email'])
            {
                $strr.="Email: ".(($ar2['email']=="") ? 'NULL' : $ar2['email'])." to ".(($ar2['email']=="") ? 'NULL' : $ar2['email'])."\n"; 
            }
            if($ar1['address']!=$ar2['address'])
            {
                $strr.="Address 1: ".(($ar1['address']=="") ? 'NULL' : $ar1['address'])." to ".(($ar2['address']=="") ? 'NULL' : $ar2['address'])."\n"; 
            }
            if($ar1['v_Location']!=$ar2['v_Location'])
            {
                $strr.="Address 2: ".(($ar1['v_Location']=="") ? 'NULL' : $ar1['v_Location'])." to ".(($ar2['v_Location']=="") ? 'NULL' : $ar2['v_Location'])."\n"; 
            }
            if($ar1['district']!=$ar2['district'])
            {
                $strr.="Province: ".(($ar1['district']=="0") ? 'NULL' : city($ar1['district']))." to ".(($ar2['district']=="0") ? 'NULL' : city($ar2['district']))."\n";  
            }
            if($ar1['v_Village']!=$ar2['v_Village'])
            {
                $strr.="District: ".(($ar1['v_Village']=="0") ? 'NULL' :village($ar1['v_Village']))." to ".(($ar2['v_Village']=="0") ? 'NULL' : village($ar2['v_Village']))."\n"; 
            }
            if($ar1['fbhandle']!=$ar2['fbhandle'])
            {
                $strr.="Facebook handle: ".(($ar1['fbhandle']=="") ? 'NULL' : $ar1['fbhandle'])." to ".(($ar2['fbhandle']=="") ? 'NULL' : $ar2['fbhandle'])."\n"; 
            }
            if($ar1['twitterhandle']!=$ar2['twitterhandle'])
            {
                $strr.="Twitter handle: ".(($ar1['twitterhandle']=="") ? 'NULL' : $ar1['twitterhandle'])." to ".(($ar2['twitterhandle']=="") ? 'NULL' : $ar2['twitterhandle'])."\n";  
            }
            if($ar1['language']!=$ar2['language'])
            {
                $strr.="Language: ".(($ar1['language']=="") ? 'NULL' : getlanguagename($ar1['language']))." to ".(($ar2['language']=="") ? 'NULL' : getlanguagename($ar2['language']))."\n";  
            }
            if($ar1['gender']!=$ar2['gender'])
            {
                $strr.="Gender: ".get_gender($ar1['gender'])." to ".get_gender($ar2['gender'])."\n";  
            }
            if($ar1['age_grp']!=$ar2['age_grp'])
            {
                $strr.="Age Group: ".(get_age_group($ar1['age_grp']))." to ".(get_age_group($ar2['age_grp']))."\n"; 
            }
            if($ar1['priority_user']!=$ar2['priority_user'])
            {
                $strr.="Priority User: ".(($ar1['priority_user']=="0") ? 'Non Priority' : 'Priority')." to ".(($ar2['priority_user']=="0") ? 'Non Priority' : 'Priority')."\n"; 
            }
            $description=(nl2br($strr));
        }else{
            $description= "Error while decoding the string!!!";
        }
  }
  if($action=="update_backoffice" || $action=="update_case")
  {
     $orginal_jsonVal=explode("old_new",$case_process_type);
     $array1=($orginal_jsonVal[0]);
     $array2=($orginal_jsonVal[1]);
     $a1=json_decode($array1,true);
     $a2=json_decode($array2,true);
  if(is_array($a1) && is_array($a2))
  { 
    $str="Updated ticket id - ".$a1['ticketid']." ";
    if($a2['iStatus']!='')
    {
      if($a2['iStatus']==2) $str.=" is Validated\n";
      if($a2['iStatus']==1) $str.=" is Rejected\n";
    } 
    if($a1['iCaseStatus']!=$a2['iCaseStatus'] && $a2['iCaseStatus']!='')
    {
       $str.="Status:".ticketstatus($a1['iCaseStatus'])." to ".ticketstatus($a2['iCaseStatus'])."\n"; 
    }
    if($a1['vProjectID']!=$a2['vProjectID'] && $a2['vProjectID']!='')
    {
      $str.="Department:".project($a1['vProjectID'])." to ".project($a2['vProjectID'])."\n";  
    }
    if($a1['iAssignTo']!=$a2['iAssignTo'] && $a2['iAssignTo']!='')
    {
      $str.="Assigned:".assignto($a1['iAssignTo'])." to ".assignto($a2['iAssignTo'])."\n";  
    }
    if($a1['vCaseType']!=$a2['vCaseType'] && $a2['vCaseType']!='')
    {
      $str.="Case Type:".($a1['vCaseType'])." to ".($a2['vCaseType'])."\n"; 
    }
    if($a1['vCategory']!=$a2['vCategory'] && $a2['vCategory']!='')
    {
      $str.="Category:".category($a1['vCategory'])." to ".category($a2['vCategory'])."\n";  
    } 
    if($a1['vSubCategory']!=$a2['vSubCategory'] && $a2['vSubCategory']!='')
    {
      $str.="Subcategory:".subcategory($a1['vSubCategory'])." to ".subcategory($a2['vSubCategory'])."\n"; 
    } 
    if($a2['v_OverAllRemark']!="") $str.="Remark:".($a2['v_OverAllRemark'])."\n";
    if($a2['v_SuggestFollowup']!="") 
    {
      $v_SuggestFollowup=explode(",",$a2['v_SuggestFollowup']);
      $suggest="";
      $result = mysqli_query($link,"SELECT id, name,value, email FROM $db.web_suggested_followup WHERE status=1 ");
      while($row = mysqli_fetch_assoc($result))
      {
        if(in_array($row['id'], $v_SuggestFollowup))
        {
           $suggest.=$row['value'].",&nbsp";
        }   
      }
      $str.= "Suggested Follow up:".$suggest."\t";
    }
      $description=(nl2br($str));
  }else{
    $description= "Error while decoding the string!!!";
  }
  }
  $data=json_decode($data,true);
    if($action=="update_superviser" )
  {
    //print_r($data);echo '<br>';//print_r($orginal_jsonVal);
    if($data['v_OverAllRemark']!="") $description= "Supervisor Remark:: ".base64_decode($data['v_OverAllRemark']);
  }
  if($action=='loggedin')
  {
    $description="Logged in";
  }
  if($action=='logout')
  {
    $description=" Logged Out";
  }
  if($action=='report_view')
  {
    $description="View case report section";
  }
  if($action=='helpdesk')
  {
    $description="View case report section";
  }
  if($action=='new_case_create')
  {
    $description="New case created and the ticket No. is ".$ticket;
  }
  if($action=='create_customer')
  {
    if($data['mr']!="") $source=source($data['mr']);
    $description="New customer is created (.".$source.") and the caller No. is ".$data['phone'];
  }
  if($action=='case_open')
  {
    $description="Open the case (".$ticket.") to view the details";
  }
  if( $action=="update_case_2")
  {
    $description="Checking the case  (".$ticket.") to update";
  }
  if($action=="update")
  {
    $description="New ticket is created for the existing customer and the Mob No is ".$data['phone'];
  }
  if($action=="user_remove")
  {
    $description="$case_process_type User removed By $agent" ;
  }
    if($action=="interaction_remark")
    {
        $description="$case_process_type" ;
    } 
return $description;
}  
 //funciton to fetch data from the database for the web_faq_view.php AUTH: Vastvikta Nishad 04-03-2024
 function getFaqData() {
  // Global variables for database connection and database name
  global $db, $link;
  // Check if the form is submitted with a search query
  if (isset($_POST['submit'])) {
      // If submitted, construct and execute a query to retrieve data based on the search query
      $query = mysqli_query($link, "SELECT * FROM $db.tbl_mst_faq WHERE i_status=1 AND (v_qus LIKE '%{$_POST['queryString']}%' OR v_ans LIKE '%{$_POST['queryString']}%') ORDER BY i_id ASC");
  } else {
      // If not submitted, construct and execute a query to retrieve all data
      $query = mysqli_query($link, "SELECT * FROM $db.tbl_mst_faq WHERE i_status=1 ORDER BY i_id ASC");
  }

  // Initialize an empty array to store fetched FAQ data
  $faqData = [];

  // Loop through the query result and store each row in the $faqData array
  while ($row = mysqli_fetch_assoc($query)) {
      $faqData[] = $row;
  }

  // Return the array containing the fetched FAQ data
  return $faqData;
}
function get_date_dispo(){
  global $db_asterisk,$link;
  // Get the last record's date
  $query = "SELECT  max(event_time) as last_date  FROM $db_asterisk.autodial_list as list inner join $db_asterisk.autodial_agent_log as list_agent_log on list.lead_id=list_agent_log.lead_id ";

  $result = mysqli_query($link, $query);
  $row = mysqli_fetch_assoc($result);
  
  if ($row && $row['last_date']) {
      $last_date = $row['last_date']; // Last record date
      $end_date = date('Y-m-d', strtotime($last_date)); // Set End Date
      $month = date('m', strtotime($last_date)); // Get month
      $year = date('Y', strtotime($last_date)); // Get year
      $start_date = "$year-$month-01"; // Set Start Date as 01-month-year
  } else {
      $start_date = date('Y-m-01'); // First day of the current month
$end_date = date('Y-m-d');    // Current date
 
  }

  return [
      'start_date' => $start_date,
      'end_date' => $end_date
  ];
}
//This function retrieves data from the database to generate a report.
function generateReport() {
  global $link, $db_asterisk;
  $column = array('event_time', 'phone_number', 'first_name', 'alt_phone', 'user', 'status', 'remarks', 'talk_sec', 'sentiment', 'case_id', 'ans_duration', 'filename');
  if(!empty($_REQUEST['timeperiod'])){ $timeperiod=$_REQUEST['timeperiod']; }else{  $timeperiod=''; }
  if(!empty($_REQUEST['sttartdatetime'])){ $startdatetime=$_REQUEST['sttartdatetime']; }else{  $startdatetime=''; }
  if(!empty($_REQUEST['enddatetime'])){ $enddatetime=$_REQUEST['enddatetime']; }else{  $enddatetime=''; }
  $out="";
  $pdf_heading = "Disposition Report";
    if($_REQUEST['sttartdatetime']=='' && $_REQUEST['enddatetime']=='')
    {
        $timeperiod=1;
    }
    if(!empty($_REQUEST['sttartdatetime'])){ $startdatetime=$_REQUEST['sttartdatetime']; }else{  $startdatetime=''; }
    if(!empty($_REQUEST['enddatetime'])){ $enddatetime=$_REQUEST['enddatetime']; }else{  $enddatetime=''; }
    $datefilter="";
    if($timeperiod!="" || !empty($timeperiod)){
        $from=date('Y-m-d',strtotime(getFromDate('from',$timeperiod)));
        $to=date('Y-m-d',strtotime(getFromDate('to',$timeperiod)));
        $datefilter .=" event_time>='$from 00:00:00' and event_time<='$to 23:59:59'  "; 
    }else{
        $from=date('Y-m-d H:i:s',strtotime($startdatetime));
        $to=date('Y-m-d H:i:s',strtotime($enddatetime));
        if($startdatetime!='' && $enddatetime!=''){ 
          $datefilter .=" event_time>='$from 00:00:00' and event_time<='$to 23:59:59'  "; }
        $pdf_heading = "Agent Report from ". date('d F Y',strtotime($startdatetime)) . ' to '  . date('d F Y',strtotime($enddatetime));
    }
    // if($fname!=""){
    //   $condition .='AND first_name  LIKE '."'%".$fname."%'";
    // }
    // if($CallerID!=""){
    //   $condition .='AND phone_number ='."'".$CallerID."'";
    // } 
    // if($sentiment > '0'){
    //   $condition .='AND sentiment ='."'".$sentiment."'";
    // }
    // if($disposition > '0'){
    //   $condition .='AND list_agent_log.status ='."'".$disposition."'";
    // }
    // if($casetype!=''){
    //   $condition .='AND reason_call ='."'".$casetype."'";
    // }
    // print_r($_POST);
    // if($user==''){
    //   if($userLevel!=9) 
    //   {
    //     $userCond=" and list_agent_log.user  IN (select user from $db_asterisk.autodial_users WHERE i_status=1 AND active_status=1 AND user_level !=9) ";
    //   }else{
    //     $userCond=" ";
    //   }
    // }elseif($user!=""){
      // $userCond=" and list_agent_log.user ='$user'";  
    // }
     $query="SELECT list_agent_log.reason_call,list_agent_log.callid,list.agent,list_agent_log.user,list.comments,list_agent_log.remarks,list.first_name,list.alt_phone,list.email,list.list_id,list_agent_log.filename,list.entry_date,list_agent_log.event_time,list_agent_log.sentiment,list_agent_log.status, list_agent_log.talk_sec,list.phone_number,list.called_count ,list.last_name,list.postal_code,list.industry,list.city  FROM $db_asterisk.autodial_list as list inner join $db_asterisk.autodial_agent_log as list_agent_log on list.lead_id=list_agent_log.lead_id where $datefilter  order by list_agent_log.event_time desc";   
     //echo $query;
      $res = mysqli_query($link, $query);
          $total_row = mysqli_num_rows(mysqli_query($link, $query));
    $data = array();
    $id = 0;
    while($row = mysqli_fetch_array($res)) {
        $id++;
        $event_time = $row['event_time'];
        $first_name = $row['first_name'];
        $user = $row['user'];
        $status = $row['status'];
        $remarks = $row['remarks'];
        $talk_sec = $row['talk_sec'];
        $sentiment = $row['sentiment'];
        $ans_duration = $row['ans_duration'];
        $callid = $row['callid'];
        $phone_number = get_feedback ($row['phone_number']);
        $alt_phone = get_casetype($row['alt_phone']);
        $filename = $row['filename'];
       $sub_array = array();
       $sub_array[] = $id;
       $sub_array[] = $event_time;
       $sub_array[] = $phone_number;
       $sub_array[] = $first_name;
       $sub_array[] = $alt_phone;
       $sub_array[] = $user;
       $sub_array[] = $status;
       $sub_array[] = $remarks;
       $sub_array[] = $talk_sec;
       $sub_array[] = $sentiment;
      //  $sub_array[] = $callerid;
       $sub_array[] = $ans_duration;
       $sub_array[] = $filename;
       $data[] = $sub_array;
    }
    // Return JSON formatted data
  $output = array(
    "sql_dispo"=>$query,
   "draw"       =>  intval($_POST["draw"]),
   "recordsTotal"   =>  $total_row,
   "recordsFiltered"  =>  $total_row,
   "data"       =>  $data
  );
  echo json_encode($output);

}
//Get feedback for a call based on its call ID.
   function get_feedback($callid){
     global $link,$db;
      $query = "select * FROM $db.tbl_civrs_cdr WHERE Unique_Id='$callid'" ;
      $res = mysqli_query($link,$query);
      $row = mysqli_fetch_assoc($res);
      $cust_service=GetScore_Name($row['Dialed_Digit']);
      return $cust_service;
   }
//Get the case type for a given phone number.
  function get_casetype($phone){
     global $link,$db;
      $query = "select webpro.vCaseType FROM $db.web_accounts as webacc join $db.web_problemdefination as webpro on webacc.AccountNumber=webpro.vCustomerID WHERE webacc.mobile='$phone'" ;
      $res = mysqli_query($link,$query);
      $row = mysqli_fetch_assoc($res);
      return $row['vCaseType'];
   }
//Convert a SmartFileName to a WAV file and return its path.
   function SmartFileName_voice($SmartFileName){    
   $filename=$SmartFileName;
   $filename=substr($filename, 0, 8);//12Jul2019
   $year=substr($filename, 0, 4);
   $day=substr($filename, 6, 2);
   $m=$year.substr($filename, 4, 2).$day;
   $month1=date('M',strtotime($m));
   //echo '<br>Date::'.$day.$month1.$year;
   $folderpath=$day.$month1.$year."/";
    $path='../../calls/'.$folderpath.$SmartFileName.'.wav';
    $pathWithoutExtention='../../calls/'.$folderpath.$SmartFileName;
   if (file_exists($path)) {
      $recFile= $SmartFileName.".wav";
      $pathWithoutExtention=$pathWithoutExtention.".wav";
   }else{
      $pathWithoutExtention=$pathWithoutExtention.".WAV";
      $recFile= $SmartFileName.".WAV";
   }
   $recFile = str_replace('/','_', $recFile);
   $destFile = "../../tmp/".$recFile;
   $cmd = "/usr/bin/sox $pathWithoutExtention -b 8 $destFile";
   system($cmd);
   return $destFile;
   
}
function display_subcounty(){
  global $link,$db;
  $district = $_POST['dis_id'];
  $village = $_POST['vill_id'];
  if(isset($_POST['dis_id'])){
    ?>
    <select name="village" id="village" class="select-styl1" style="width:190px;">
    <option value="">Select Sub County</option>
    <?php
    $villages_query = mysqli_query($link,"select * from $db.`web_Village` where `iDistrictID`='$district' AND `status` =1 ORDER BY `vVillage` ASC ");
    while($villages_res = mysqli_fetch_array($villages_query)){?>
    <option value="<?=$villages_res['id']?>" <?php if($villages_res['id']==$village){ echo "selected"; } ?>>
      <?=$villages_res['vVillage']?>
    </option>
      <?php } ?>
    </select>
    <?php
  }
}
function display_subcategory($category){
    global $link,$db;
    $sqlsource="select id, subcategory from $db.web_subcategory where category=$category";
    $sourceresult=mysqli_query($link,$sqlsource);
    return $sourceresult;
}
function getComplaints() {
    global $link,$db;
    $query = "SELECT id, complaint_name, slug, status FROM $db.complaint_type WHERE status = 1";
    $result = mysqli_query($link, $query);
    return $result;
  }
function getDispositions() {
    global $link, $db_asterisk;
    $query = "SELECT V_DISPO, V_DISPOSITION FROM $db_asterisk.tbl_disposition WHERE I_Status = 1 ORDER BY V_DISPOSITION ASC";
    $result = mysqli_query($link, $query);
    return $result;
    }
function getSentiments() {
    global $link, $db;
    $sentiments = array();
    $query = "SELECT sentiment FROM $db.tbl_sentiment WHERE status = 1";
    $result = mysqli_query($link, $query);
     return $result;
  }

  function getcustomername($id) {
    global $link, $db;

    // Ensure $link is set
    if (!$link) {
        die("Database connection error.");
    }

    // Use a prepared statement to prevent SQL injection
    $stmt = $link->prepare("SELECT fname FROM {$db}.web_accounts WHERE AccountNumber = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $link->error);
    }

    $stmt->bind_param("i", $id); // Assuming id is an integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        return $row['fname'];
    } else {
        return null; // No customer found
    }
}

function getEmailCustomerName($phone, $action) {
  global $link, $db;

  // Sanitize the input
  $phone = $link->real_escape_string($phone);

  // Run the query
  $query = $link->query("SELECT fname, email FROM $db.web_accounts WHERE phone = '$phone'");

  // Check if query was successful and fetched data is not empty
  if ($query && $fetch = $query->fetch_assoc()) {
    if ($action == 1) {
      return $fetch['email'];
    } else {
      return $fetch['fname'];
    }
  } else {
    // Handle case where no data was found
    return null;
  }
}


?> 



