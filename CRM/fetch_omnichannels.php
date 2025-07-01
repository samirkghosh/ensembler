<?php
include "pdo_functions.php";

if(isset($_POST['action']) && $_POST['action'] == 'mail_complaint') {

    $params = [
        'startdate' => date('Y-m-d H:i:s',strtotime($_POST['startdate'])),
        'enddate' => date('Y-m-d H:i:s',strtotime($_POST['enddate'])),
        'email' => $_POST['email'],
        'read' => $_POST['read'],
        'unread' => $_POST['unread'],
        'spam' => $_POST['spam'],
        'trash' => $_POST['trash'],
        'classify' => $_POST['classify'],
        'sentiment' => $_POST['sentiment']
    ];

    echo mail_view($params);

}else if(isset($_POST['action']) && $_POST['action'] == 'x_handle') {

   
    $query = "SELECT * FROM ensembler.tbl_tweet";
    $query .= ' ORDER BY d_TweetDateTime DESC';


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
        $messageid = $row['i_ID'];
        $v_Screenname=$row['v_Screenname'];
        $ICASEID = $row['ICASEID'];
        $i_TweetID = $row['i_TweetID'];
        $flag = $row['Flag'];
        $checkbox = '<input type="checkbox" class="checkbox">';

        if($flag == 1){ // read mails
            $start = "<span class='read'>";
        }else if($flag == 0){ // Unread mails
            $start = "<span class='unread'>";
            
        }
        $end = "</span>";
        
        $dm = "<a style=\"text-decoration:none;cursor:pointer;\" class=\"DM-x\" data-id=$i_TweetID data-bs-toggle=\"modal\" data-bs-target=\"#exampleModal\"> DM </a>";

        $sub_array = [];
        $sub_array[] = $checkbox;
        $sub_array[] = "$start$v_Screenname$end";
        $sub_array[] = $start.substr($row['v_TweeterDesc'],0,30).$end;
        $sub_array[] = "$start$end";
        $sub_array[] = $start.date('d-m-Y h:i A',strtotime($row['d_TweetDateTime'])).$end;
        $sub_array[] = "$start$dm$end";
        $data[] = $sub_array;
    }
    
    function count_all_data($connect){
     $query = "SELECT * FROM ensembler.tbl_tweet";
     $statement = $connect->prepare($query);
     $statement->execute();
     return $statement->rowCount();
    }
    
    $output = [
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => count_all_data($connect),
        "recordsFiltered" => $number_filter_row,
        "data" => $data
    ];
    
    echo json_encode($output);

}else if(isset($_POST['action']) && $_POST['action'] == 'dispostion_channel_insert') {

    $createdby        =  $_SESSION['userid'];
    $created_date = date("Y-m-d H:i:s");
    $remarks        =  $_POST['remarks'];
    $channel_id        =  $_POST['channel_id'];
    $disposition_type        =  $_POST['dispostion_type'];
    $channel_type = $_POST['channel_type'];
    //disposition channel insert data
    // get dispostion table details 
    $query_dis = mysqli_query($link,"SELECT * FROM $db.multichannel_disposition WHERE channel_id='$channel_id' AND channel_type = '$channel_type'");
    if(mysqli_num_rows($query_dis) > 0) {
        $update_customer = "UPDATE $db.multichannel_disposition SET disposition_type='$disposition_type' ,remarks='$remarks' WHERE channel_id='$channel_id' AND channel_type = '$channel_type'";
        mysqli_query($link, $update_customer) or die("Error In Query2 " . mysqli_error($link));
    }else {  
        $insert_customer_query = "INSERT INTO $db.multichannel_disposition(channel_type, disposition_type, remarks,created_date, channel_id, createdby) VALUES('$channel_type','$disposition_type','$remarks','$created_date','$channel_id','$createdby')  ";
        mysqli_query($link, $insert_customer_query) or die("Error In Query2 " . mysqli_error($link));
    }
    // mail read status update
    switch ($channel_type) {
        case 'Email':
            mysqli_query($link, "UPDATE $db.web_email_information SET Flag='1' WHERE EMAIL_ID='$channel_id'");
            break;
        case 'SMS':
            $link->query("UPDATE $db.sms_out_queue SET Flag='1' WHERE id='$channel_id'; ");
            break;
        case 'Twitter':
            mysqli_query($link, "UPDATE $db.tbl_tweet SET Flag='1' WHERE i_ID='$channel_id'");
            break;
        case 'Whatsapp':
            mysqli_query($link, "UPDATE $db.whatsapp_in_queue SET flag='0' WHERE id='$channel_id'");
            break;
        case 'Facebook':
            mysqli_query($link, "UPDATE $db.tbl_facebook SET Flag='1' WHERE id='$channel_id'");
            break;
    }
}
?>
