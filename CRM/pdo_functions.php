<?php
include_once "../config/web_mysqlconnect.php";

try {
    // Create a new PDO instance
    $connect = new PDO(
        "mysql:host=$configdbhost;dbname=$configdbname",
        (string) $configdbuser,
        (string) $configdbpass
    );
    
    // Set the PDO error mode to exception
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Handle connection errors
    echo "Connection failed: " . $e->getMessage();
}

function statusname($id) {
    global $connect;
    $query = "SELECT ticketstatus FROM ensembler.web_ticketstatus WHERE id = :id";
    $statement = $connect->prepare($query);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result['ticketstatus'] ?? null; // Return null if 'ticketstatus' is not found
}

function status($id) {
    global $connect;
    $query = "SELECT iCaseStatus FROM ensembler.web_problemdefination WHERE ticketid = :id";
    $statement = $connect->prepare($query);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if ($result && isset($result['iCaseStatus'])) {
    return '-('.statusname($result['iCaseStatus']).')';
    }
    return null; // Return null if 'iCaseStatus' is not found
}
function TotalRecords($table) {
    global $connect;
    $query = "SELECT * FROM $table";
    $statement = $connect->prepare($query);
    $statement->execute();
    return $statement->rowCount();
}

function mail_view($params) {
    global $connect;

    // Extract parameters from the associative array
    $startdate = $params['startdate'] ?? '';
    $enddate = $params['enddate'] ?? '';
    $email = $params['email'] ?? '';
    $read = $params['read'] ?? '';
    $unread = $params['unread'] ?? '';
    $spam = $params['spam'] ?? '';
    $trash = $params['trash'] ?? '';
    $classify = $params['classify'] ?? '';
    $sentiment = $params['sentiment'] ?? '';

    $query = "SELECT * FROM web_email_information WHERE I_Status = 1 "; 

    if(isset($_POST['startdate'], $_POST['enddate']) && !empty($_POST['startdate']) && !empty($_POST['enddate'])){
    $query .= " AND d_email_date BETWEEN '$startdate' AND '$enddate'";
    }

    if(isset($email) && !empty($email)){
        $query .= " AND (v_fromemail LIKE '%$email%' OR ICASEID LIKE '%$email%') ";
    }
    if(isset($read) && !empty($read)){
        $query .= " AND Flag = 1";
    }
    if(isset($unread) && !empty($unread)){
        $query .= " AND Flag = 0";
    }

    if(isset($spam) && !empty($spam)){
        $query .= " AND classification = 4";
    }else{
        $query .= " AND classification != 4";
    }

    if(!empty($trash)){
        $query .= " AND i_DeletedStatus = 2";
    }else{
        $query .= " AND i_DeletedStatus = 0";
    }
    
    $query .= ' ORDER BY d_email_date DESC';


    $query1 = '';
    if($_POST["length"] != -1){
    $query1 = ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
    }

    $statement = $connect->prepare($query);
    $statement->execute();
    $number_filter_row = $statement->rowCount();
    $statement = $connect->prepare("$query$query1");
    $statement->execute();
    $result = $statement->fetchAll();


    $data = [];
    foreach($result as $row){
        $flag = $row['Flag'];
        $emailid=$row['EMAIL_ID'];
        $case_id=$row['ICASEID'];
        $checkbox = "<input type=\"checkbox\" class=\"checkbox\" name=\"email_qID[]\" value=\"$emailid\">";

        if($flag == 1){ // read mails
            $start = "<span class='read'>";
        }else if($flag == 0){ // Unread mails
            $start = "<span class='unread'>"; 
        }

        $end = "</span>";
        
        $div1 = "<a href=\"javascript:void(0)\" id=\"btn_viewmail\" data-id= $emailid data-bs-toggle=\"modal\" data-bs-target=\"#mailsModal\">";
        $div2 = '</a>';
        $div3 = "<a href=\"case_detail_backoffice.php?id=$case_id\" target=\"_blank\">";
        $div4 = '</a>';
        $status = status($case_id);

        $sub_array = [];
        $sub_array[] = $checkbox;
        $sub_array[] = $start.$row['v_fromemail'].$end;
        $sub_array[] = $div1.$start.substr($row['v_subject'],0,30).$end.$div2;
        $sub_array[] = "$div3$start$case_id$status$end$div4";
        $sub_array[] = $start.($row['classification']=='0') ? '' : $row['classification'].$end;
        $sub_array[] = $start.$row['sentiment'].$end;
        $sub_array[] = $start.date('d-m-Y h:i A',strtotime($row['d_email_date'])).$end;
        $data[] = $sub_array;
    }
    
    $table = 'web_email_information'; 
    
    $output = [
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => TotalRecords($table),
        "recordsFiltered" => $number_filter_row,
        "data" => $data
    ];
    
    
    return json_encode($output);
}