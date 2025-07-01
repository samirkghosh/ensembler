<?php

class Smsbox extends Admin_Controller{

	private $unread_where;
	private $outbox_where;

	public function __construct(){
		parent::__construct();

	 	// print_r($this->session);
	
		$this->load->model('Auth_model');	
		$this->load->model('Model_Sms', 'sms_model');	
		// print_r($this->session->userdata('admin')['role']);

		if(strtolower($this->session->userdata('admin')['role']) =='admin'): // Admin
			$this->unread_where = ['1'=>'1' ] ; 		
			$this->outbox_where = ['status'=>0 ] ; 
		else:		// users only :
			$this->unread_where = ['message_flag'=>0 ] ; 
			$this->outbox_where = ['status'=>0, 'created_by'=>$this->session->userdata('admin')['id'] ] ; 
		endif;
		 

	}
	
	// update 26-07-2021
    public function new_message(){
    	if(!$this->isLoggedin())
			redirect('login', 'refresh');

		if(isset($_SESSION['session_array'])){
			unset($_SESSION['session_array']);
			log_message('error' , 'check Session data');
			//log_message('error' , json_encode($_SESSION['session_array']));
		}

		// read Inbox Messages  

		$message_id =  $this->uri->segment(4,0) ;
		$this->sms_model->read_inbox_message($message_id);
		

		$data['title'] ='Smsbox || BIPA ';
		$data['breadcrumb'] = "Compose new message";
		$data['message_id'] = $message_id;
		// $data['contacts'] = $this->Auth_model->get_contact();
		// $data['template_assign'] = $this->template_model->get_templates();
		$data['total_unread_count'] = $this->db->where($this->unread_where)->from('sms_in_queue')->count_all_results();;
		$data['total_outbox_count'] = $this->db->where($this->outbox_where)->from('sms_out_queue')->count_all_results(); 
		$this->load->view('sms/compose',$data);
	}

	public function compose(){
		if(!$this->isLoggedin())
			redirect('login', 'refresh');


        $this->form_validation->set_error_delimiters('', '');

        $this->form_validation->set_rules('mobile[]', 'Mobile', 'required'); 
		$this->form_validation->set_rules('message', 'Message', 'required');

		if($this->input->post('scheduletime')=='on'):
			$this->form_validation->set_rules('date', 'Date', 'required');
        endif;

        if ($this->form_validation->run()) {

        	// farhan :: 19-06-2021   
			$quota = $this->Auth_model->get_quota_sms();
			$mobCnt = count($this->input->post('mobile'));
			
			$msgLnth = strlen($this->input->post('message'));
            $finalcnt = (floor($msgLnth/153)+1) * $mobCnt;

            $message_id = $this->input->post('message_id') ;


			// No Quota limts For Admin 				
			if($this->session->userdata('admin')['role_id'] != '1'){
				if($quota == 0){
					echo json_encode(array('st' => 2, 'msg' => "You Can't Send messages Please contact admin..Quota Exceed it's limit"));die();
					
				}
				else{
					if($quota >= $finalcnt){

						// $this->Auth_model->insert_sms();
						$result = $this->Auth_model->insert_sms();			
						$this->Auth_model->sms_quota_update($finalcnt);
						if($result){
							// Update if only Replayed 
							if($message_id > 0){
								$this->sms_model->update_replied_id($message_id, $result);
							}
							echo json_encode(array('st' => 0, 'msg' => "Message sent successfully"));die();
						}
						else{
							echo json_encode(array('st' => 2, 'msg' => "Send Sms Fail, please Try after some time."));die();
						}

					}
					else{
						echo json_encode(array('st' => 2, 'msg' => "Your available quota limit is ".$quota.""));die();

					}
				}

			}
			else{
				//$this->Auth_model->sms_quota_update($mobCnt);
				$result = $this->Auth_model->insert_sms();
				if($result){
					$this->Auth_model->sms_quota_update($finalcnt);
					// Update if only Replayed 
					if($message_id > 0){
						$this->sms_model->update_replied_id($message_id, $result);
					}

					echo json_encode(array('st' => 0, 'msg' => "Message sent successfully"));die();
				}
				else{
					echo json_encode(array('st' => 2, 'msg' => "Send Sms Fail, please Try after some time."));die();
				}

			} 				

        } 
        else {

            $data = array(
                'mobile' => form_error('mobile[]'),
                'message' => form_error('message'),
                'date' => form_error('date'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));die();
        }
		

    }

	public function inbox(){

		if(!$this->isLoggedin())
			redirect('login', 'refresh');


		// Compose
		$data['title'] ='Inbox || BIPA ';
		$data['breadcrumb'] = "inbox";
		$data['total_unread_count'] = $this->db->where($this->unread_where)->from('sms_in_queue')->count_all_results();; 
		$data['total_outbox_count'] = $this->db->where($this->outbox_where)->from('sms_out_queue')->count_all_results();
		if(strtolower($this->session->userdata('admin')['role_id']) == '2'){
			$data['total_outbox_count'] = $this->db->where(['status'=>0])->where_in('created_by',[1, $this->session->userdata('admin')['id'] ] )->from('sms_out_queue')->count_all_results();

		}
		$data['report_name'] = 'sms' ; 
		$data['report_in_out'] ='in';
		$data['user_wise'] ='all'; 
		$data['box_name'] ='inbox'; 
		$data['name_number'] =$this->input->post('name_number'); 
		$data['status'] =  $this->input->post('status'); 
		$data['from_date']     = !empty($this->input->post('from_date')) ? yyyymmdd_date($this->input->post('from_date')) : date('Y-m-d 23:59', strtotime('-30 days')) ;
        $data['end_date']      = !empty($this->input->post('end_date')) ? yyyymmdd_date($this->input->post('end_date')) : date('Y-m-d 23:59', strtotime('now'));
		$this->load->view('sms/inbox',$data);

	}

	public function outbox(){
		if(!$this->isLoggedin())
			redirect('login', 'refresh');

		// sent
		$data['title'] ='Outbox || BIPA ';
		$data['breadcrumb'] = "outbox";
		
		$data['total_unread_count'] = $this->db->where($this->unread_where)->from('sms_in_queue')->count_all_results();
		$data['total_outbox_count'] = $this->db->where($this->outbox_where)->from('sms_out_queue')->count_all_results();
		if(strtolower($this->session->userdata('admin')['role_id']) == '2'){
			$data['total_outbox_count'] = $this->db->where(['status'=>0])->where_in('created_by',[1, $this->session->userdata('admin')['id'] ] )->from('sms_out_queue')->count_all_results();

		}
		

		 
		// $data['contacts'] = $this->Auth_model->get_contact();
		// $data['template_assign'] = $this->template_model->get_templates();
		//  datatable
		$data['report_name'] = 'sms' ; 
		$data['report_in_out'] ='out'; 
		$data['box_name'] 		='outbox'; 
		
		$this->load->view('sms/outbox',$data);
	}
	
	public function sent(){
		if(!$this->isLoggedin())
			redirect('login', 'refresh');

		// sent
		$data['title'] ='Sent || BIPA ';
		$data['breadcrumb'] = "sent";
		$data['sms_reports'] = $this->report_model->reports();
        $data['lists'] =  $this->sms_model->get_bulk_upload_list();
		$data['users'] =  $this->user_model->getUsers();
		$data['total_unread_count'] = $this->db->where($this->unread_where)->from('sms_in_queue')->count_all_results();
		$data['total_outbox_count'] = $this->db->where($this->outbox_where)->from('sms_out_queue')->count_all_results();
		if(strtolower($this->session->userdata('admin')['role_id']) == '2'){
		$data['total_outbox_count'] = $this->db->where(['status'=>0])->where_in('created_by',[1, $this->session->userdata('admin')['id'] ] )->from('sms_out_queue')->count_all_results();
		}

		//  datatable
		$data['report_name'] = 'sms' ; 
		$data['report_in_out'] ='out'; 
		$data['box_name'] 		='sent'; 
		$data['user_wise'] = $this->session->userdata('admin')['id'];
		//  Filter option 
        $data['from_date']     = !empty($this->input->post('from_date')) ? yyyymmdd_date($this->input->post('from_date')) : date('d-m-Y 00:00', strtotime('now'));
        $data['end_date']      = !empty($this->input->post('end_date')) ? yyyymmdd_date($this->input->post('end_date')) : date('d-m-Y 23:59', strtotime('now')); 
        $data['schedule'] =  $this->input->post('schedule'); 
        $data['message_type'] =  $this->input->post('message_type'); 
        $data['status'] =  $this->input->post('status'); 
        $data['list_wise'] =  $this->input->post('list_wise'); 
		$this->load->view('sms/sent',$data);

	}


	/*public function delivered(){

		if(!$this->isLoggedin())
			redirect('login', 'refresh');


		// delivered
		$data['title'] ='Delivered || BIPA ';
		$data['breadcrumb'] = "Delivered";
		$data['contacts'] = $this->Auth_model->get_contact();
		$data['template_assign'] = $this->template_model->get_templates();
		//  datatable
		$data['report_name'] = 'sms' ; 
		$data['report_in_out'] ='out';
		$data['user_wise'] ='all'; 
		$data['status'] = '2';
		$data['box_name'] ='delivered'; 
		$this->load->view('sms/delivered',$data);

	}

	public function undelivered(){

		if(!$this->isLoggedin())
			redirect('login', 'refresh');

		// undelivered
		$data['title'] ='Undelivered || BIPA ';
		$data['breadcrumb'] = "UnDelivered";
		$data['contacts'] = $this->Auth_model->get_contact();
		$data['template_assign'] = $this->template_model->get_templates();
		//  datatable
		$data['report_name'] = 'sms' ; 
		$data['report_in_out'] ='out';
		$data['user_wise'] ='all'; 
		$data['status'] = '3';
		$data['box_name'] ='undelivered'; 
		$this->load->view('sms/undelivered',$data);

	}*/

	public function get_basic_records($value=''){
		$report_data = array();
		// Filter portion
		$filter_data['box_name']   = $this->input->post('box_name');
		$filter_data['report_name']   = $this->input->post('report_name');
		$filter_data['report_in_out'] = $this->input->post('report_in_out');
		$filter_data['from_date']         = $this->input->post('from_date');
		$filter_data['end_date']         = $this->input->post('end_date');
		$filter_data['schedule']        = $this->input->post('schedule');
		$filter_data['message_type']        = $this->input->post('message_type');
		$filter_data['status']        = $this->input->post('status');
		$filter_data['list_wise']        = $this->input->post('list_wise');
		$filter_data['user_wise']        = $this->input->post('user_wise');
		$filter_data['name_number'] = $this->input->post('name_number') ; 
		
		 

		// Server side processing portion
		$columns = array(
			0 => '#',
			// 1 => 'send_from',
			// 1 => 'send_to',
			1 => 'message',
			2 => 'message_type_flag',
			3 => 'status',
			4 => 'scheduler_flag',
			5 => 'scheduled_time',
			6 => 'create_date',
			7 => 'status_response',
			8 => 'id'
		);

		// Coming from databale itself. Limit is the visible number of data
		$limit = html_escape($this->input->post('length'));
		$start = html_escape($this->input->post('start'));
		$order = "";
		$dir   = $this->input->post('order')[0]['dir'];

		/*if($filter_data['box_name']=='inbox'){
			$totalData = $this->lazyload->count_all_data($filter_data);
		}
		if($filter_data['box_name']=='sent'){
		}*/

		$totalData = $this->lazyload->count_all_data_inbox($filter_data);
		if(empty($report_data)){
            $filter_data['last_hundreds'] = 'yes';
            $totalData = $this->lazyload->count_all_data_inbox($filter_data);
        }
		
	
		$totalFiltered = $totalData;

		// This block of code is handling the search event of datatable
			
			
		if(empty($this->input->post('search')['value'])) {
			/*if($filter_data['box_name']=='inbox'){
				$report_data = $this->lazyload->reports($limit, $start, $order, $dir, $filter_data);
			}
			if($filter_data['box_name']=='sent'){
			}*/
			$report_data = $this->lazyload->reports_inbox($limit, $start, $order, $dir, $filter_data);
			if(empty($report_data)){
	            $limit ='100';
	            $start = 0 ;
	            $filter_data['last_hundreds'] = 'yes';
	            $report_data = $this->lazyload->reports_inbox($limit, $start, $order, $dir, $filter_data);
	        }
			
		}
		else {
			
			$search = $this->input->post('search')['value'];
			$report_data =  $this->lazyload->reports_search($limit, $start, $search, $order, $dir, $filter_data);
			$totalFiltered = $this->lazyload->course_search_count($search);
				
		}

		$data = array();
		if(!empty($report_data)) {
			foreach ($report_data as $key => $row) {

				
				
				$replied_by = $contact_name = '';
				$nestedData['#'] = $key+1;
				 
				if($filter_data['box_name']=='inbox'){
					
					if($row->action_by > 0 ){

						$replied_by = $this->user_model->getUsers($row->action_by)->username;
					}
					$contact_name = $this->Auth_model->get_contact($row->send_from);

					// $nestedData['replyed'] = $row->message_flag=='2'?'<a href="#" class="" data-container="body" title="Reply"><i class="fas fa-reply"></i></a>': ' ';
					$nestedData['replyed'] = ucfirst($replied_by) ;
					$nestedData['send_from'] = (($row->message_flag=='1' || $row->message_flag=='2') ?$row->send_from: '<strong>'.$row->send_from.'</strong>' ) ;
					$nestedData['name'] =  $contact_name;
					$nestedData['message'] = '<a href="#" id="'.$row->message.'"  onclick="show_full_message(this,'.$row->send_from.','.$row->id.')">'.substr($row->message, 0,40).'</a>'  ;
				 
					$nestedData['create_date'] = (($row->message_flag=='1' || $row->message_flag=='2') ?date("d-m-Y H:i", strtotime($row->create_date)): '<strong>'.date("d-m-Y H:i", strtotime($row->create_date)).'</strong>' ) ;
					// Use Bold An unbold content for read adn unread

					// $badge_status = $row->message_flag=='2'?'<span class="badge badge-success">Replyed</span>' : ($row->message_flag == '1'? '<span class="badge badge-warning">Read</span>' :'<span class="badge badge-danger">Unread</span>' ); 

					$nestedData['action'] = '<a href='.base_url('smsbox/new_message/').$row->send_from.'/'.$row->id.' class="" data-container="body" title="Reply"><i class="fas fa-reply"></i></a> ';
					$nestedData['action_by'] = '';
					$nestedData['id'] = $row->id;
					$data[] = $nestedData;  
				}

				if($filter_data['box_name']=='sent'){
					$user = $this->user_model->getUsers($row->created_by);
					$contact_name = $this->Auth_model->get_contact($row->send_to);

					$nestedData['send_to'] = $row->send_to ;
					$nestedData['name'] =  $contact_name;
					$nestedData['message'] = '<a href="#" id="'.$row->message.'"  onclick="show_full_message(this,'.$row->send_to.','.$row->id.')">'.substr($row->message, 0,40).'</a>'  ;
				 
					
					
					if($row->status =='2')	
						$status  = 'Delivered' ;	
					elseif($row->status =='1' || $row->status =='3')
						$status  = 'Undelivered' ;	

					$nestedData['status'] = $status;
					// $nestedData['message_type'] =  $row->message_type_flag=='0'?'Direct':($row->message_type_flag=='1'?'Bulk':'App') ;
					$nestedData['message_type'] =  $row->message_type_flag=='0'?'Direct':($row->message_type_flag=='1'?'Bulk':($row->message_type_flag=='2'?'App':'EmailtoSms') ) ;
					$nestedData['create_date'] = date("d-m-Y H:i", strtotime($row->create_date));

					$nestedData['schedule'] = $row->schedule_flag=='0'?'<small class="badge badge-success"><i class="far fa-clock"></i> '.ddmmyyyy_date($row->schedule_time).'</small>' : '';

				 

					$nestedData['remark'] = $row->status_response;
					$nestedData['action_by'] = isset($user->username)?$user->username:'API';
					$nestedData['id'] = $row->id;
					$data[] = $nestedData;  
				}
				 
				
			}
		}

		$json_data = array(
			"draw"            => intval($this->input->post('draw')),
			"recordsTotal"    => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data"            => $data
		);

		echo json_encode($json_data);

	}

	public function outbox_record($value=''){
		$report_data = array();
		// Filter portion
		$filter_data['box_name']   = $this->input->post('box_name');
		$filter_data['report_name']   = $this->input->post('report_name');
		$filter_data['report_in_out'] = $this->input->post('report_in_out');
		$filter_data['from_date']         = $this->input->post('from_date');
		$filter_data['end_date']         = $this->input->post('end_date');
		$filter_data['schedule']        = $this->input->post('schedule');
		$filter_data['message_type']        = $this->input->post('message_type');
		$filter_data['status']        = $this->input->post('status');
		$filter_data['list_wise']        = $this->input->post('list_wise');
		$filter_data['user_wise']        = $this->input->post('user_wise');

		// Server side processing portion
		$columns = array(
			0 => '#',
			// 1 => 'send_from',
			// 1 => 'send_to',
			1 => 'message',
			2 => 'message_type_flag',
			3 => 'status',
			4 => 'scheduler_flag',
			5 => 'scheduled_time',
			6 => 'create_date',
			7 => 'status_response',
			8 => 'id'
		);

		// Coming from databale itself. Limit is the visible number of data
		$limit = html_escape($this->input->post('length'));
		$start = html_escape($this->input->post('start'));
		$order = "";
		$dir   = $this->input->post('order')[0]['dir'];

		/*if($filter_data['box_name']=='inbox'){
			$totalData = $this->lazyload->count_all_data($filter_data);
		}
		if($filter_data['box_name']=='sent'){
		}*/

		$totalData = $this->lazyload->count_all_data_outbox($filter_data);
		if(empty($report_data)){
            $filter_data['last_hundreds'] = 'yes';
            $totalData = $this->lazyload->count_all_data_outbox($filter_data);
        }
		$totalFiltered = $totalData;

		// This block of code is handling the search event of datatable
			
			
		if(empty($this->input->post('search')['value'])) {
			/*if($filter_data['box_name']=='inbox'){
				$report_data = $this->lazyload->reports($limit, $start, $order, $dir, $filter_data);
			}
			if($filter_data['box_name']=='sent'){
			}*/
			$report_data = $this->lazyload->reports_outbox($limit, $start, $order, $dir, $filter_data);
			if(empty($report_data)){
	            $limit ='100';
	            $start = 0 ;
	            $filter_data['last_hundreds'] = 'yes';
	            $report_data = $this->lazyload->reports_outbox($limit, $start, $order, $dir, $filter_data);
	        }
			
		}
		else {
			
			/*$search = $this->input->post('search')['value'];
			$report_data =  $this->lazyload->reports_search($limit, $start, $search, $order, $dir, $filter_data);
			$totalFiltered = $this->lazyload->course_search_count($search);*/
				
		}

		$data = array();
		if(!empty($report_data)) {
			foreach ($report_data as $key => $row) {
				$user = $this->user_model->getUsers($row->created_by);
				$contact_name ='' ; 
				 

				$nestedData['#'] = $key+1;
				if($filter_data['box_name']=='outbox'){
					$nestedData['send_to'] = $row->send_to ;
					$contact_name = $this->Auth_model->get_contact($row->send_to);
					$nestedData['name'] =  $contact_name;
					$nestedData['message'] = '<a href="#" id="'.$row->message.'"  onclick="show_full_message(this,'.$row->send_to.','.$row->id.')">'.substr($row->message, 0,40).'</a>'  ;
				 
					$nestedData['create_date'] = date("d-m-Y H:i", strtotime($row->create_date));
					
					if($row->status =='0')	
						$status  = 'Queue';	
					else if($row->status =='2')
						$status  = 'Delivered' ;
					else if($row->status =='1' || $row->status =='3')
						$status  = 'Undelivered' ;	

					$nestedData['status'] = $status;

					$nestedData['schedule'] = $row->schedule_flag=='0'?'<small class="badge badge-success"><i class="far fa-clock"></i> '.ddmmyyyy_date($row->schedule_time).'</small>' : '';

				 

					$nestedData['remark'] = $row->sms_response_code;
					$nestedData['action_by'] = isset($user->username)?$user->username:'API';
					$nestedData['id'] = $row->id;
					$data[] = $nestedData;  
				}
				 
				
			}
		}

		$json_data = array(
			"draw"            => intval($this->input->post('draw')),
			"recordsTotal"    => intval($totalData),
			"recordsFiltered" => intval($totalFiltered),
			"data"            => $data
		);

		echo json_encode($json_data);

	}

	public function get_time_difference_php($created_time){

		date_default_timezone_set('Asia/Calcutta'); //Change as per your default time
		$str = strtotime($created_time);
		$today = strtotime(date('Y-m-d H:i:s'));

		// It returns the time difference in Seconds...
		$time_differnce = $today-$str;

		// To Calculate the time difference in Years...
		$years = 60*60*24*365;

		// To Calculate the time difference in Months...
		$months = 60*60*24*30;

		// To Calculate the time difference in Days...
		$days = 60*60*24;

		// To Calculate the time difference in Hours...
		$hours = 60*60;

		// To Calculate the time difference in Minutes...
		$minutes = 60;

		if(intval($time_differnce/$years) > 1)
		{
			return intval($time_differnce/$years)." years ago";
		}else if(intval($time_differnce/$years) > 0)
		{
			return intval($time_differnce/$years)." year ago";
		}else if(intval($time_differnce/$months) > 1)
		{
			return intval($time_differnce/$months)." months ago";
		}else if(intval(($time_differnce/$months)) > 0)
		{
			return intval(($time_differnce/$months))." month ago";
		}else if(intval(($time_differnce/$days)) > 1)
		{
			return intval(($time_differnce/$days))." days ago";
		}else if (intval(($time_differnce/$days)) > 0) 
		{
			return intval(($time_differnce/$days))." day ago";
		}else if (intval(($time_differnce/$hours)) > 1) 
		{
			return intval(($time_differnce/$hours))." hours ago";
		}else if (intval(($time_differnce/$hours)) > 0) 
		{
			return intval(($time_differnce/$hours))." hour ago";
		}else if (intval(($time_differnce/$minutes)) > 1) 
		{
			return intval(($time_differnce/$minutes))." minutes ago";
		}else if (intval(($time_differnce/$minutes)) > 0) 
		{
			return intval(($time_differnce/$minutes))." minute ago";
		}else if (intval(($time_differnce)) > 1) 
		{
			return intval(($time_differnce))." seconds ago";
		}else
		{
			return "few seconds ago";
		}
	}

	// Read Inbox Message 
	public function read_inbox_message($value=''){

		// echo json_encode($_POST);die();
		$id = $this->input->post('row_id');
		$message_type = $this->input->post('message_type');
		

		if($message_type =='outbox'){
			$message_details = $this->db->get_where('sms_out_queue', array('id' => $id))->row();
			$details['sent_by'] = $message_details->send_from ;
			$details['replied_by'] = $message_details->created_by > 0 ? $this->user_model->getUsers($message_details->created_by)->username : '' ;
			$details['replied_date'] =  ddmmyyyy_date($message_details->update_date);
			$details['content'] = $message_details->message;
		}
		else{
			$this->sms_model->read_inbox_message($id);
			$message_details = $this->db->get_where('sms_in_queue', array('id' => $id))->row();
			$details['sent_by'] = $message_details->send_from ;
			$details['replied_by'] = $message_details->action_by > 0 ? $this->user_model->getUsers($message_details->action_by)->username : '' ;
			$details['replied_date'] =  ddmmyyyy_date($message_details->update_date);
			$details['content'] = $message_details->message;
		}



		

		echo json_encode(array('status' => 'success' , 'res' => $details));die();

		echo 'success';die();
	}

}







