<?php
/*
auth: Vastvikta Nishad 
Date: 19-12-2024 
Description: TO fetch Data From Database for ticket deletion
*/

// Include necessary files and database connection
include("../../config/web_mysqlconnect.php");
include("date.php");
if(isset($_POST['action']) && $_POST['action'] =='ticket_deletion_report'){
	ticket_deletion_report();
}

function ticket_deletion_report(){
    global $db, $link;
    $condition = "";
    
    // Initialize SQL query
    $sql = "SELECT * FROM $db.web_problemdefination_archive  where id !='' ";

    // Get startdatetime and enddatetime from POST or set defaults
    if (isset($_POST['startdatetime']) && !empty($_POST['startdatetime'])) {
        $startdatetime = $_POST['startdatetime'];
    }
    if (isset($_POST['enddatetime']) && !empty($_POST['enddatetime'])) {
        $enddatetime = $_POST['enddatetime'];
    }

    $ticketid = isset($_REQUEST['ticketid']) ? $_REQUEST['ticketid'] : null;
    $i_CreatedBY = isset($_REQUEST['i_CreatedBY']) ? $_REQUEST['i_CreatedBY'] : null;

    // Date range filter
    if (!empty($startdatetime) && !empty($enddatetime)) {
        $from = date('Y-m-d H:i:s', strtotime($startdatetime));
        $to = date('Y-m-d H:i:s', strtotime($enddatetime));
        $sql .= " AND d_createDate BETWEEN '$from' AND '$to'";
    } 
    if (!empty($ticketid)) {
        $ticket_id = mysqli_real_escape_string($link, $ticketid);
        $sql .= " AND (ticketid = '$ticket_id')";
    }
    if (!empty($i_CreatedBY)) {
        $CreatedBY = mysqli_real_escape_string($link, $i_CreatedBY);
        $sql .= " AND (deleted_by = '$CreatedBY')";
    }
    $sql .= ' ORDER BY `d_createDate` desc';
    // Get the total number of records
    $totalRecordsQuery = mysqli_query($link, $sql);
    $totalRecords = mysqli_num_rows($totalRecordsQuery);
    
    // Apply limit code
    if ($_POST["length"] != -1) {
        $start = $_POST['start'];
        $length = $_POST['length'];
        $sql .= " LIMIT $start, $length";
    }
    
    // Execute the query
    $result = mysqli_query($link, $sql);
    
    // Prepare data array
    $data = array();
 
    while ($row = mysqli_fetch_assoc($result)){
        $ticket_id = $row['ticketid'];
        $flag = '1';
        $token = base64_encode('web_case_detail');
        $mid = base64_encode($ticket_id);
        $ticket_link = '<a href="helpdesk_index.php?token=' . $token . '&id=' . $mid . '&delflag=' . $flag . '" style="padding-left: 10px;" target="_blank">' . $ticket_id . '</a>';
        
        
        $sub_array = array();
        $sub_array[] = $ticket_link;
        $sub_array[] = username($row['vCustomerID']);
        $sub_array[] = phonenumber($row['vCustomerID']);
        $sub_array[] = $row['d_createDate'];
        $sub_array[] = $row['deleted_createddate'];
        $sub_array[] = getname($row['deleted_by']);
        $sub_array[] = $row['deleted_remark'];
        $sub_array[] = ticketstatus($row['iCaseStatus']);
        $sub_array[] = source($row['i_source']);
      
        $sub_array[] = category($row['vCategory']);
        $sub_array[] = subcategory($row['vSubCategory']);
        $sub_array[] = department($row['vProjectID']);
        $sub_array[] = $row['priority'];
       
        $data[] = $sub_array;
    }

    // Return JSON formatted data
    $output = array(
        "draw" => $draw,
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecords,
        "data" => $data
    );

    echo json_encode($output);
}
function username($AccountNumber)
{
 global $db,$link;
	$res=mysqli_fetch_array(mysqli_query($link,"select fname from $db.web_accounts where AccountNumber='$AccountNumber' ; "));
	return $res['fname'];
}
function phonenumber($AccountNumber)
{
 global $db,$link;
	$res=mysqli_fetch_array(mysqli_query($link,"select phone from $db.web_accounts where AccountNumber='$AccountNumber' ; "));
	return $res['phone'];
}
function ticketstatus($ticketstatus)
{
 global $db,$link;
	$res=mysqli_fetch_array(mysqli_query($link,"select ticketstatus from $db.web_ticketstatus where id='$ticketstatus' ; "));
	return $res['ticketstatus'];
}
function getname($i_CreatedBY)
{
 global $db,$link;
	$res=mysqli_fetch_array(mysqli_query($link,"SELECT AtxUserName FROM $db.uniuserprofile WHERE AtxUserID ='$i_CreatedBY' ; "));
	return $res['AtxUserName'];
}

function source($source)
{
 global $db,$link;
	$res=mysqli_fetch_array(mysqli_query($link,"select source from $db.web_source where id='$source' ; "));
	return $res['source'];
}

function category($cat)
{
 	global $db,$link;
	$res=mysqli_fetch_array(mysqli_query($link,"select category from $db.web_category where id='$cat' ; "));
	return $res['category'];
}

function department($id)
{
 	global $db,$link;
	$res=mysqli_fetch_array(mysqli_query($link,"select vProjectName from $db.web_projects where pId='$id' ; "));
	return $res['vProjectName'];
}

function subcategory($subcat)
{
 global $db,$link;
	$res=mysqli_fetch_array(mysqli_query($link,"select subcategory from $db.web_subcategory where id='$subcat' ; "));
	return $res['subcategory'];
}

?>