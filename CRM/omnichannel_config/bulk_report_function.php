<?php
/* This file used for bulk sms and email related function-aarti*/
include("../../config/web_mysqlconnect.php");

// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);
class BULK_REPORT{
	function __construct(){
		global $link,$db;
		if(isset($_POST['action']) || isset($_GET['action'])){
			if($_POST['action'] == 'bulk_sms_report'){
				$this->bulk_report_data(); //[Aarti][02-05-2024]Report bulk sms details display
			}
			if($_POST['action'] == 'queue_report'){
				$this->get_queue_records(); //[Aarti][02-05-2024]Report bulk sms details display
			}
			if($_POST['action'] == 'get_bad_records'){
				$this->get_bad_records(); //[Aarti][02-05-2024]Report bulk sms details display
			}
		} 	
	}
	public function get_bulk_upload_list(){
		global $link,$db;
        if($_SESSION['userid'] != '1'){  // not for admin 
            $where = ' and created_by' . $_SESSION['userid'];
        }            
        $web_accounts="select * FROM $db.channel_bulk_uploads where list_name !='' $where order by id desc";
		$response=mysqli_query($link,$web_accounts);	
		if ($response) {
		    // Fetch all rows into an associative array
		    $result_array = mysqli_fetch_all($response, MYSQLI_ASSOC);
		    return $result_array;
		}
    }
    public function getUsers($id=''){
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
    public function get_status_of_list($list_id){
        global $link,$db;
        $sqlagent="select COUNT(id) AS ct ,status  from $db.sms_out_queue WHERE queue_session ='$list_id' GROUP BY status";
       	$result=mysqli_query($link,$sqlagent);
        $result_array = mysqli_fetch_all($result, MYSQLI_ASSOC);
	    return $result_array ;
    }
    public function get_emailstatus_of_list($list_id){
        global $link,$db;
        $sqlagent="select COUNT(id) AS ct ,status from $db.sms_out_queue WHERE queue_session ='$list_id' GROUP BY status";
       	$result=mysqli_query($link,$sqlagent);
        $result_array = mysqli_fetch_all($result, MYSQLI_ASSOC);
	    return $result_array ;
    }
    public function get_whatsappstatus_of_list($list_id){
        global $link,$db;
        // Query change fetching data whatsapp table [Aarti][24-07-2024]
        $sqlagent="select COUNT(id) AS ct ,status as status from $db.whatsapp_out_queue WHERE queue_session ='$list_id' GROUP BY status";
       	$result=mysqli_query($link,$sqlagent);
        $result_array = mysqli_fetch_all($result, MYSQLI_ASSOC);
	    return $result_array ;
    }
    public function get_list_details($list_id,$channeltype){
    	global $link,$db;
    	if(!empty($channeltype)){
	    	$where .= " and channel_type ='".$channeltype."'";
	    }
        $sqlagent="select * from $db.channel_bulk_uploads where id =".$list_id.$where;
       	$result=mysqli_query($link,$sqlagent);
        $result_array = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    return $result_array;
    }
	//[Aarti][02-05-2024]Report bulk sms details display
	/***
	 * This functon fetch email ,sms ,whatsapp bulk send report 
	 * fetch multiple channel reports
	 * message send or not , pending queue,submitted,deliverd
	 * ***/
	public function bulk_report_data($value=''){

        $report_data = array();
        // Filter portion
        $filter_data['report_name']   = $_POST['report_name'];
        $filter_data['report_in_out'] = $_POST['report_in_out'];
        if( !empty($_POST['from_date'])){
        	$filter_data['from_date']     = date("Ymd", strtotime($_POST['from_date']));
        	$filter_data['end_date']      = date("Ymd", strtotime($_POST['end_date']));
        }
        $filter_data['schedule']      = $_POST['schedule'];
        $filter_data['message_type']  = $_POST['message_type'];
        $filter_data['status']        = $_POST['status'];
        $filter_data['list_wise']     = $_POST['list_wise'];
        $filter_data['user_wise']     = $_POST['user_wise'];
        $filter_data['channeltype']     = $_POST['channeltype'];
        // Server side processing portion
        $columns = array(
            0 => '#',
            1 => 'create_date',
            2 => 'list_name',
            3 => 'total_count',
            4 => 'queue',
            5 => 'submitted',
            // 6 => 'pending',
            6 => 'delivered',
            7 => 'not_delivered',
            8 => 'message',
            9 => 'schedule_time',
            10 => 'schedule_flag',
            11 => 'create_by',
            12 => 'id'
        );

        // Coming from databale itself. Limit is the visible number of data
        $limit = htmlspecialchars($_POST['length']);
        $start = htmlspecialchars($_POST['start']);
        $order = "";
        $dir   = $_POST['order'][0]['dir'];

        $totalData = $this->count_all_data($filter_data); // fetching all data
        
        $totalFiltered = $totalData;

        // This block of code is handling the search event of datatable
        $report_data = $this->reports($limit, $start, $order, $dir, $filter_data);           
        
         // Fetch the data and make it as JSON format and return it.
        $data = array();
        $list_status = array();
        $queue = $submitted = $pending = $Delivered = $notDelivered ='0';
        if(!empty($report_data)) {
            foreach ($report_data as $key => $row) {
                $user = $this->getUsers($row['created_by']);	
                $list_details = $this->get_list_details($row['list_id'],$_POST['channeltype']);
                if($_POST['channeltype'] == 'SMS'){
                	$list_status = $this->get_status_of_list($row['out_queue_session']);
                }else if($_POST['channeltype'] == 'Email'){
            		$list_status = $this->get_emailstatus_of_list($row['out_queue_session']);
                }else if($_POST['channeltype'] == 'WhatsApp'){
                	$list_status = $this->get_whatsappstatus_of_list($row['out_queue_session']);
                }
                
                if($list_status !=null ){
                    foreach($list_status as $key =>$list ){ 
                    	if($_POST['channeltype'] == 'Email'){
                    		if($list['status'] =='0')
	                            $queue = $list['ct'];
	                        else if($list['status'] =='1')
	                        	$Delivered = $list['ct'];
	                        else if($list['status'] =='2')
	                            $submitted = $list['ct'];
	                        else if($list['status'] =='3')
	                            $notDelivered = $list['ct'];
	                    }else if($_POST['channeltype'] == 'WhatsApp'){
	                    	if($list['status'] =='0')
	                            $queue = $list['ct'];
	                        else if($list['status'] =='1')
	                        	$Delivered = $list['ct'];
	                        else if($list['status'] =='2')
	                            $submitted = $list['ct'];
	                        else if($list['status'] =='3')
	                            $notDelivered = $list['ct'];
	                    }else if($_POST['channeltype'] == 'SMS'){
	                    	if($list['status'] =='0')
	                            $Delivered = $list['ct'];
	                        else if($list['status'] =='1')
	                            $queue = $list['ct'];
	                        else if($list['status'] =='2')
	                            $submitted = $list['ct'];
	                        else if($list['status'] =='3')
	                            $notDelivered = $list['ct'];
	                    }                       
                       
                    }     
                }  
                         
                $nestedData['#'] = $key+1;
                $nestedData['create_date']  = date('d-m-Y', strtotime($row['created_at']));
                $nestedData['list_name']    =  $list_details['list_name'];
                $nestedData['total_count']  =  $row['total_count'];

                $nestedData['queue']        =  $queue ;
                $nestedData['submitted']    =  $submitted ;
                $nestedData['delivered']    =  $Delivered ;
                $nestedData['not_delivered'] =  $notDelivered ;

                  $nestedData['message'] = '<a href="#" id="'.$row['message'].'" onclick="show_full_message(this,'.$row['id'].')" title="'.$row['message'].'"> '.substr($row['message'], 0,40).'</a>'   ;
                
                $yes_no = $row['schedule_flag']=='0'?'<div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"><input type="checkbox" class="custom-control-input" checked disabled id="customSwitch3"><label class="custom-control-label" for="customSwitch3"></label></div>':'<div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"><input type="checkbox" disabled class="custom-control-input" id="customSwitch3"><label class="custom-control-label" for="customSwitch3"></label></div>' ;
                $small = ' <small>'.date('d-m-Y H:i', strtotime($row['schedule_time'])).'</small>';

               
                $nestedData['schedule_time'] = date('d-m-Y H:i', strtotime($row['schedule_time']));
                $nestedData['schedule_flag'] =  $yes_no ;
                $nestedData['create_by'] = $user['AtxUserName'];
                $nestedData['id'] = $row['id'];
                $data[] = $nestedData;

            }
        }
        $json_data = array(
            "draw"            => intval($_POST['draw']),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );
        echo json_encode($json_data);
    }
    public function count_all_data($filter_data = array()) {
    	global $link,$db;
	    $where = '';
	    if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
	      $where = ' and created_at >='.$filter_data['from_date'];
	    }

	    if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
	      $where .= ' and created_at <='.$filter_data['end_date'];
	    }

	    if ($filter_data['schedule'] != "all" && $filter_data['schedule'] !='') {
	      $where .= ' and schedule_flag'.$filter_data['schedule'];
	    }

	    if ($filter_data['list_wise'] != "all" && !empty($filter_data['list_wise'])) {
	      $where .= ' and list_id'.$filter_data['list_wise'];
	    }

	    if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
	      $where .= ' and created_by'.$filter_data['user_wise'];
	    }
	     
	    if ($filter_data['status'] != "all" && $filter_data['status'] !='' ) {
	      $where .= ' and status ='.$filter_data['status'];
	    }
	    if(!empty($filter_data['channeltype'])){
	    	$where .= " and channel_type ='".$filter_data['channeltype']."'";
	    }

	    // $query = $this->db->get('queue_bulk_relation');
	    $web_accounts="select * FROM $db.queue_bulk_relation where message!='' $where";
		$response=mysqli_query($link,$web_accounts);		
		$num_rows = mysqli_num_rows($response);
	    /*if($filter_data['report_name'] == 'sms'){
	      if($filter_data['report_in_out'] =='out'){
	       $query = $this->db->get('channel_bulk_uploads');

	      }
	      else{
	       $query = $this->db->get('sms_in_queue');
	        
	      }
	    }
	    else if($filter_data['report_name'] == 'Whatsapp'){
	      if($filter_data['report_in_out'] =='out'){
	        $query = $this->db->get('whatsapp_out_queue');
	      }
	      else{
	        $query = $this->db->get('whatsapp_in_queue');
	      }
	    }*/
	    return $num_rows;
	}
	// Servre side testing
	public function reports($limit, $start, $col, $dir, $filter_data){
	  	global $link,$db;
	  	$where = '';
	    if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
	      $where = ' and created_at >='.$filter_data['from_date'];
	    }

	    if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
	      $where .= ' and created_at <='.$filter_data['end_date'];
	    }

	    if ($filter_data['schedule'] != "all" && $filter_data['schedule'] !='') {
	      $where .= ' and schedule_flag'.$filter_data['schedule'];
	    }

	    if ($filter_data['list_wise'] != "all" && !empty($filter_data['list_wise'])) {
	      $where .= ' and list_id'.$filter_data['list_wise'];
	    }

	    if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
	      $where .= ' and created_by'.$filter_data['user_wise'];
	    }
	     
	    if ($filter_data['status'] != "all" && $filter_data['status'] !='' ) {
	      $where .= ' and status ='.$filter_data['status'];
	    }
	    if(!empty($filter_data['channeltype'])){
	    	$where .= " and channel_type ='".$filter_data['channeltype']."'";
	    }
	    $where .= " order by id desc limit $start ,$limit ";

	    $web_accounts="select * FROM $db.queue_bulk_relation where message!='' $where";
		$response=mysqli_query($link,$web_accounts);		
		$num_rows = mysqli_num_rows($response);	
		
	     /*if($filter_data['report_name'] == 'sms'){
	      if($filter_data['report_in_out'] =='out'){

	      }
	      else{
	       $query = $this->db->get('sms_in_queue');
	        
	      }
	    }
	    else if($filter_data['report_name'] == 'Whatsapp'){
	      if($filter_data['report_in_out'] =='out'){
	        $query = $this->db->get('whatsapp_out_queue');
	      }
	      else{
	        $query = $this->db->get('whatsapp_in_queue');
	      }
	    }*/	

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
	 // queue report to remove data fome queue   
	 //[Aarti][02-05-2024]Report queue report to remove data fome queue 
	/***
	 * This functon fetch email ,sms ,whatsapp bulk send report 
	 * fetch multiple channel reports
	 * message send or not , pending queue,submitted,deliverd
	 * ***/
	public function get_queue_records($value=''){
		global $link,$db;
        $report_data = array();
        // Filter portion
        $filter_data['report_name']   = $_POST['report_name'];
        $filter_data['report_in_out'] = $_POST['report_in_out'];
        if( !empty($_POST['from_date'])){
        	$filter_data['from_date']     = date("Ymd", strtotime($_POST['from_date']));
        	$filter_data['end_date']      = date("Ymd", strtotime($_POST['end_date']));
        }
        $filter_data['schedule']      = $_POST['schedule'];
        $filter_data['message_type']  = $_POST['message_type'];
        $filter_data['status']        = $_POST['status'];
        $filter_data['list_wise']     = $_POST['list_wise'];
        $filter_data['user_wise']     = $_POST['user_wise'];
        $filter_data['channeltype']     = $_POST['channeltype'];

        // Server side processing portion
        $columns = array(
            0 => '#',
            // 1 => 'send_from',
            1 => 'send_to',
            2 => 'message',
            3 => 'message_type_flag',
            4 => 'status',
            5 => 'scheduler_flag',
            6 => 'scheduled_time',
            7 => 'create_date',
            8 => 'status_response',
            9 => 'create_by',
            10 => 'id'
        );

        // Coming from databale itself. Limit is the visible number of data
        $limit = htmlspecialchars($_POST['length']);
        $start = htmlspecialchars($_POST['start']);
        $order = "";
        $dir   = $_POST['order'][0]['dir'];

        $totalData = $this->count_all_data_outbox($filter_data);
        $totalFiltered = $totalData;

        // This block of code is handling the search event of datatable
        $report_data = $this->reports_outbox($limit, $start, $order, $dir, $filter_data);            
         // Fetch the data and make it as JSON format and return it.
        $data = array();
        //$report_data = $this->report_model->reports();
        if(!empty($report_data)) {
            foreach ($report_data as $key => $row) {

                $user = $this->getUsers($row['created_by']);

                $contact_name = $this->get_contact($row['send_to']);

                $nestedData['#'] = $key+1;
                $nestedData['send_to'] =  $row['send_to'] ;

                $nestedData['name'] =  $contact_name;

                $nestedData['message'] = '<a href="#" id="'.$row['message'].'" onclick="show_full_message(this,'.$row['id'].')" title="'.$row['message'].'"> '.substr($row['message'], 0,40).'</a>'   ;

                $nestedData['units'] =  $row['message_unit']  ;

                $nestedData['message_type_flag'] =  $row['message_type_flag']=='0'?'Direct':($row['message_type_flag']=='1'?'Bulk':'App') ;
                 
                if($_POST['channeltype'] == 'Email'){
            		if($list['status'] =='0')
                        $queue = $list['ct'];
                    else if($list['status'] =='1')
                    	$Delivered = $list['ct'];
                    else if($list['status'] =='2')
                        $submitted = $list['ct'];
                    else if($list['status'] =='3')
                        $notDelivered = $list['ct'];
                }else if($_POST['channeltype'] == 'WhatsApp'){
                	if($list['status'] =='0')
                        $queue = $list['ct'];
                    else if($list['status'] =='1')
                    	$Delivered = $list['ct'];
                    else if($list['status'] =='2')
                        $submitted = $list['ct'];
                    else if($list['status'] =='3')
                        $notDelivered = $list['ct'];
                }else if($_POST['channeltype'] == 'SMS'){
                	if($list['status'] =='0')
                        $Delivered = $list['ct'];
                    else if($list['status'] =='1')
                        $queue = $list['ct'];
                    else if($list['status'] =='2')
                        $submitted = $list['ct'];
                    else if($list['status'] =='3')
                        $notDelivered = $list['ct'];
                } 
                
                $nestedData['status'] = $status ;
                $yes_no = $row['schedule_flag']=='0'?'<div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"><input type="checkbox" class="custom-control-input" checked disabled id="customSwitch3"><label class="custom-control-label" for="customSwitch3"></label></div>':'<div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"><input type="checkbox" disabled class="custom-control-input" id="customSwitch3"><label class="custom-control-label" for="customSwitch3"></label></div>' ;
                $small = '<small>'. date('d-m-Y H:i', strtotime($row['schedule_time'])) .'</small>';
                $nestedData['schedule'] = $row['schedule_flag']=='0'?'<small class="badge badge-success"><i class="far fa-clock"></i> '.date("d-m-Y H:i", strtotime($row['schedule_time'])).'</small>' : '';
                
                $nestedData['create_date'] = date('d-m-Y H:i', strtotime($row['schedule_time']));
                $nestedData['status_response'] = $row['status_response'];
                $nestedData['create_by'] = isset($user['AtxUserName'])?$user['AtxUserName']:'API' ;
                $nestedData['id'] = $row['id'];
                $data[] = $nestedData;              
            }
        }
        $json_data = array(
            "draw"            => intval($_POST['draw']),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );
        echo json_encode($json_data);
    } 
    #######################################################
	  #####   For Showing Data in Outbox use below function 
	#######################################################

	  // Servre side testing
  	function reports_outbox($limit, $start, $col, $dir, $filter_data){
  		global $link,$db;
	    if(!isset($filter_data['last_hundreds']) && $filter_data['last_hundreds'] !='yes'){ 
	      if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
	        $where = ' and created_at >='.$filter_data['from_date'];
	      }
	      if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
	        $where = ' and created_at >='.$filter_data['end_date'];
	      }
	    }
	    if ($filter_data['schedule'] != "all" && $filter_data['schedule'] !='') {
	      $where = " and schedule_flag ='".$filter_data['schedule']."'";
	    }
	    if ($filter_data['message_type'] != 'all' && $filter_data['message_type'] !='' ) {
	      $where = " and message_type_flag ='".$filter_data['message_type']."'";
	    }

	    if ($filter_data['list_wise'] != "all" && !empty($filter_data['list_wise'])) {
	      $where = " and bulk_session_id ='".$filter_data['list_wise']."'";
	    }

	    if($_SESSION['userid'] == '1'){
	      if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
	        $where = " and created_by ='".$filter_data['user_wise']."'";
	      }
	    }else{
	      $where = " and created_by ='".$_SESSION['userid']."'";
	    }

	    $where = " order by id desc limit $start,$limit ";

	    // $this->db->where_in('status', [0]);

	    // if($filter_data['channeltype'] == 'SMS'){
	    //    $web_accounts="select * FROM $db.queue_bulk_relation where message!='' $where";
		// 	$response=mysqli_query($link,$web_accounts);		
		// 	$num_rows = mysqli_num_rows($response);

	    // }else if($filter_data['channeltype'] == 'WhatsApp'){
	        $web_accounts="select * FROM $db.whatsapp_out_queue where message!='' $where";
			$response=mysqli_query($link,$web_accounts);		
			$num_rows = mysqli_num_rows($response);

	    // }else if($filter_data['channeltype'] == 'Email'){

	    // 	$web_accounts="select * FROM $db.queue_bulk_relation where message!='' $where";
		// 	$response=mysqli_query($link,$web_accounts);		
		// 	$num_rows = mysqli_num_rows($response);
	    // }
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
    function count_all_data_outbox($filter_data = array()) {
    	global $link,$db;
	    if(!isset($filter_data['last_hundreds']) && $filter_data['last_hundreds'] !='yes'){ 
	      if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
	        $where = ' and created_at >='.$filter_data['from_date'];
	      }
	      if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
	        $where = ' and created_at >='.$filter_data['end_date'];
	      }
	    }
	    if ($filter_data['schedule'] != "all" && $filter_data['schedule'] !='') {
	      $where = " and schedule_flag ='".$filter_data['schedule']."'";
	    }
	    if ($filter_data['message_type'] != 'all' && $filter_data['message_type'] !='' ) {
	      $where = " and message_type_flag ='".$filter_data['message_type']."'";
	    }

	    if ($filter_data['list_wise'] != "all" && !empty($filter_data['list_wise'])) {
	      $where = " and bulk_session_id ='".$filter_data['list_wise']."'";
	    }

	    if($_SESSION['userid'] == '1'){
	      if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
	        $where = " and created_by ='".$filter_data['user_wise']."'";
	      }
	    }else{
	      $where = " and created_by ='".$_SESSION['userid']."'";
	    }
	    // $this->db->where_in('status', [0]);

	    // if($filter_data['report_name'] == 'sms'){
	    //    $query = $this->db->get('sms_out_queue');
	      
	    // }else if($filter_data['report_name'] == 'whatsapp'){
	      	$web_accounts="select * FROM $db.whatsapp_out_queue where message!='' $where";
			$response=mysqli_query($link,$web_accounts);		
			$num_rows = mysqli_num_rows($response);
	    // }
	    return $num_rows;
	  }
    public function get_contact($mobile = '' ){
        global $link,$db;
    	if(!empty($mobile)){
            $sqlagent="select * from $db.contact where mobile_no ='".$mobile."'";
	       	$result=mysqli_query($link,$sqlagent);
	       	$num_rows = mysqli_num_rows($result);
	       	 if($num_rows> 0){
		        $result_array = mysqli_fetch_array($result, MYSQLI_ASSOC);
		        $name = $result_array['first_name'] . ' ' .  $result_array['last_name'];
			   return $name;
			 }
            return '' ; 
	    }
        $sqlagent="select * from $db.contact";
       	$result=mysqli_query($link,$sqlagent);
        $result_array = mysqli_fetch_array($result, MYSQLI_ASSOC);
	    return $result_array;
    }
    // 23-08-2021 :: Bad reports      
    public function get_bad_records($value=''){
        global $link,$db;
        $report_data = array();
        // Filter portion
        $filter_data['report_name']   = $_POST['report_name'];
        $filter_data['report_in_out'] = $_POST['report_in_out'];
        if( !empty($_POST['from_date'])){
        	$filter_data['from_date']     = date("Ymd", strtotime($_POST['from_date']));
        	$filter_data['end_date']      = date("Ymd", strtotime($_POST['end_date']));
        }
        $filter_data['schedule']        = $_POST['schedule'];
        $filter_data['message_type']        = $_POST['message_type'];
        $filter_data['status']        = $_POST['status'];
        $filter_data['list_wise']        = $_POST['list_wise'];
        $filter_data['user_wise']        = $_POST['user_wise'];
        // Server side processing portion
        $columns = array(
            0 => '#',
            1 => 'name',
            2 => 'mobile',
            3 => 'email',
            4 => 'create_by',
            5 => 'create_date',
            6 => 'id'
        );
        // Coming from databale itself. Limit is the visible number of data
        $limit = htmlspecialchars($_POST['length']);
        $start = htmlspecialchars($_POST['start']);
        $order = "";
        $dir   = $_POST['order'][0]['dir'];
        $totalData = $this->count_all_data_bad_records($filter_data);
        $totalFiltered = $totalData;
        // This block of code is handling the search event of datatable
        $report_data = $this->reports_bad_records($limit, $start, $order, $dir, $filter_data);
        // Fetch the data and make it as JSON format and return it.
        $data = array();
        //$report_data = $this->report_model->reports();
        if(!empty($report_data)) {
            foreach ($report_data as $key => $row) {
                $user = $this->getUsers($row['created_by']);
                $nestedData['#'] = $key+1 .'<input type="checkbox" name="select_contact[]" class="select_contact" value="'.$row['id'].'">';
                $nestedData['name'] =  $row['first_name'] .' '.$row['last_name'];
                $nestedData['mobile'] =  $row['mobile_no'] ;
                $nestedData['email'] =  $row['email'];
                $nestedData['create_by'] = isset($user['AtxUserName'])?$user['AtxUserName']:'API' ;
                $nestedData['create_date'] = ddmmyyyy_date($row['created_date']);
                $nestedData['id'] = $row['id'];
                $data[] = $nestedData;
            }
        }
        $json_data = array(
            "draw"            => intval($_POST['draw']),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );
        echo json_encode($json_data);
    }
    #######################################################
	  #####   For Showing bad records 
	#######################################################
	// Servre side testing
	function reports_bad_records($limit, $start, $col, $dir, $filter_data){
		global $link,$db;
	    $where = '';
	    if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
	      $where = ' and created_at >='.$filter_data['from_date'];
	    }
	    if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
	      $where = ' and created_at >='.$filter_data['end_date'];
	    }
	    if($_SESSION['userid'] !='1'){
	      $where = ' and created_by ="'.$_SESSION['userid']."'";
	    }else{
	       if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
	        $where = ' and created_by ="'.$filter_data['user_wise']."'";
	      }
	    }
	    $where .= " order by id desc limit $start ,$limit ";
	    $web_accounts="select * FROM $db.bad_contact where campaign_name!='' $where";
		$response=mysqli_query($link,$web_accounts);		
		$num_rows = mysqli_num_rows($response);	
		// Check if query was successful
		if ($num_rows) {
		    // Fetch all rows into an associative array
		    $result_array = mysqli_fetch_all($response, MYSQLI_ASSOC);
		    mysqli_free_result($response);
		    return $result_array;
		} else {
		    return null;
		}
	}
    function count_all_data_bad_records($filter_data = array()) {
	    global $link,$db;
	    $where = '';
	    if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
	      $where = ' and created_at >='.$filter_data['from_date'];
	    }
	    if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
	      $where = ' and created_at >='.$filter_data['end_date'];
	    }
	    if($_SESSION['userid'] !='1'){
	      $where = ' and created_by ="'.$_SESSION['userid']."'";
	    }else{
	       if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
	        $where = ' and created_by ="'.$filter_data['user_wise']."'";
	      }
	    }  
	    $web_accounts="select * FROM $db.bad_contact where campaign_name!='' $where";
		$response=mysqli_query($link,$web_accounts);		
		$num_rows = mysqli_num_rows($response);	
	    return $num_rows;
	  }			 				
	}
$a = new BULK_REPORT();
?>