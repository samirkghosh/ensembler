<?php
/* This file used for bulk twitter related function-Ritu*/
include("../../config/web_mysqlconnect.php");
    if(isset($_POST['action']) || isset($_GET['action'])){
    	if($_POST['action'] == 'twiter_report'){
    		twiter_data(); 
    	}
    }
	
	function getUsers($id=''){
    	global $link,$db;
        if(!empty($id)){
            $sqlagent="select AtxUserID,AtxUserName from $db.uniuserprofile where AtxUserStatus='1' and AtxUserID =".$id;
            $result=mysqli_query($link,$sqlagent);
	       $result_array = mysqli_fetch_array($result, MYSQLI_ASSOC);
	       return $result_array ;

        }
       $sqlagent="select AtxUserID,AtxUserName from $db.uniuserprofile where AtxUserStatus='1'";
       $result=mysqli_query($link,$sqlagent);
       $result_array = mysqli_fetch_all($result, MYSQLI_ASSOC);
       return $result_array ;
    }
function twiter_data() {
    global $link, $db;
    $report_data = array();

    if (!empty($_POST['from_date'])) {
        $filter_data['from_date'] = date("Ymd", strtotime($_POST['from_date']));
        $filter_data['end_date'] = date("Ymd", strtotime($_POST['end_date']));
    }
    $filter_data['user_wise'] = $_POST['user_wise'];
    $filter_data['channeltype'] = $_POST['channeltype'];

    $columns = array(
        0 => '#',
        1 => 'created_date',
        2 => 'queue',
        3 => 'submitted',
        4 => 'delivered',
        5 => 'not_delivered',
        6 => 'message_data',
        7 => 'schedule_time',
    );

    $limit = htmlspecialchars($_POST['length']);
    $start = htmlspecialchars($_POST['start']);
    $order = $columns[$_POST['order'][0]['column']];
    $dir = $_POST['order'][0]['dir'];

    $totalData = count_all_data($filter_data);
    $totalFiltered = $totalData;

    $report_data = reports($limit, $start, $order, $dir, $filter_data);

    $data = array();
    if (!empty($report_data)) {
        foreach ($report_data as $key => $row) {
            $list_status = get_twiter_of_list($row['i_TweetID']);

            $queue = $submitted = $Delivered = $notDelivered = '0';
            if ($list_status != null) {
                foreach ($list_status as $list) {
                    if ($list['status'] == '0') $queue = $list['ct'];
                    else if ($list['status'] == '1') $Delivered = $list['ct'];
                    else if ($list['status'] == '2') $submitted = $list['ct'];
                    else if ($list['status'] == '3') $notDelivered = $list['ct'];
                }
            }

            $nestedData['#'] = $key + 1;
            $nestedData['created_date'] = date('d-m-Y', strtotime($row['created_date']));
            $nestedData['queue'] = $queue;
            $nestedData['submitted'] = $submitted;
            $nestedData['delivered'] = $Delivered;
            $nestedData['not_delivered'] = $notDelivered;
            $nestedData['message_data'] = $row['message_data'];
            $nestedData['schedule_time'] = date('H:i', strtotime($row['created_date']));
            $data[] = $nestedData;
        }
    }

    $json_data = array(
        "draw" => intval($_POST['draw']),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
    );
    echo json_encode($json_data);
}
    function get_twiter_of_list($i_TweetID){
        global $link,$db;
        $sqlagent="select COUNT(id) AS ct ,sent_flag as status from $db.web_twitter_directmsg where i_TweetID = $i_TweetID GROUP BY sent_flag";
       	$result=mysqli_query($link,$sqlagent);
        $result_array = mysqli_fetch_all($result, MYSQLI_ASSOC);
	    return $result_array ;
    }
    function count_all_data($filter_data = array()) {
    	global $link,$db;
	    $where = '';
	    if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
	      $where = ' and created_date >='.$filter_data['from_date'];
	    }

	    if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
	      $where .= ' and created_date <='.$filter_data['end_date'];
	    }

	    $web_accounts="select * FROM $db.web_twitter_directmsg where message_data!='' $where";
		$response=mysqli_query($link,$web_accounts);		
		$num_rows = mysqli_num_rows($response);
	    return $num_rows;
	}

    // Servre side testing
	function reports($limit, $start, $col, $dir, $filter_data){
	  	global $link,$db;
	  	$where = '';
	    if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
	      $where = ' and created_date >='.$filter_data['from_date'];
	    }

	    if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
	      $where .= ' and created_date <='.$filter_data['end_date'];
	    }

	    $where .= " order by id desc limit $start ,$limit ";

	    $web_accounts="select * FROM $db.web_twitter_directmsg where message_data!='' $where";
		$response=mysqli_query($link,$web_accounts);		
		$num_rows = mysqli_num_rows($response);	
		
	    // Check if query was successful
		if ($num_rows) {
		    // Fetch all rows into an associative array
		    $result_array = mysqli_fetch_all($response, MYSQLI_ASSOC);
		    // Free result set
		    mysqli_free_result($response);

		    // Do something with $result_array
		    return $result_array;
		} else {
		    // Handle query error
		    // echo "Error: " . mysqli_error($link);
		    return null;
		}
	}
?>