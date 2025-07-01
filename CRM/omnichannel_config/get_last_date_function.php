<?php
/*
auth: Vastvikta Nishad 
Date: 18-03-2025
Description: function  file for updating date according to latest month record 
*/

// Include necessary files and database connection
include("../../config/web_mysqlconnect.php");
function get_date_email_complaint(){
    global $db,$link;
    // Get the last record's date
  $query = "SELECT MAX(d_email_date) as last_date FROM $db.web_email_information WHERE queue_type='complain' "; 

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
function get_date_email_enquiry(){
    global $db,$link;
    // Get the last record's date
$query = "SELECT MAX(d_email_date) as last_date FROM $db.web_email_information WHERE queue_type='inquiry' "; 

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
function get_date_twitter(){
    global $db,$link;
    // Get the last record's date


  $query = "SELECT MAX(d_TweetDateTime) as last_date FROM $db.tbl_tweet "; 

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
function get_date_sms(){
    global $db,$link;
    // Get the last record's date


  $query = "SELECT MAX(d_timeStamp) as last_date FROM $db.tbl_smsmessagesin "; 

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
function get_date_whatsapp(){
    global $db,$link;
    // Get the last record's date

  $query = "SELECT MAX(create_date) as last_date FROM $db.whatsapp_in_queue "; 

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

function get_date_messenger(){
    global $db,$link;
    // Get the last record's date

  $query = "SELECT MAX(create_date) as last_date FROM $db.messenger_in_queue "; 

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

function get_date_instagram(){
    global $db,$link;
    // Get the last record's date

  $query = "SELECT MAX(create_date) as last_date FROM $db.instagram_in_queue "; 

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

function get_date_chat(){
    global $db,$link;
    // Get the last record's date

  $query = "SELECT MAX(createdDatetim) as last_date FROM $db.overall_bot_chat_session"; 

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

function get_date_disposition(){
    global $db,$link;
    // Get the last record's date

  $query = "SELECT MAX(created_date) as last_date FROM $db.multichannel_disposition"; 

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

function get_date_ticketdel(){
    global $db,$link;
    // Get the last record's date

  $query = "SELECT MAX(d_createDate) as last_date FROM $db.web_problemdefination_archive"; 

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

function get_date_email_report(){
    global $db,$link;
    // Get the last record's date

  $query = "SELECT MAX(d_email_date) as last_date FROM $db.web_email_information_out"; 

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

function get_date_twitter_report(){
    global $db,$link;
    // Get the last record's date

  $query = "SELECT MAX(created_date) as last_date FROM $db.web_twitter_directmsg"; 

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
function get_date_sms_report(){
    global $db,$link;
    // Get the last record's date
  $query = "SELECT MAX(create_date) as last_date FROM $db.sms_out_queue"; 

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

function get_date_whatsapp_report(){
    global $db,$link;
    // Get the last record's date
  $query = "SELECT MAX(create_date) as last_date FROM $db.whatsapp_out_queue"; 

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

function get_date_messenger_report(){
    global $db,$link;
    // Get the last record's date
  $query = "SELECT MAX(create_date)  as last_date FROM $db.messenger_out_queue"; 

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

function get_date_insta_report(){
    global $db,$link;
    // Get the last record's date
  $query = "SELECT MAX(create_date)  as last_date FROM   $db.instagram_out_queue "; 

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
?>