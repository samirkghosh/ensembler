<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms extends Admin_Controller {

	protected $customer_array ;
	protected $bad_customer_array ;
	protected $campaign_name ;
	public function __construct(){

		parent::__construct();
		$this->customer_array = [] ;
		$this->bad_customer_array = [] ;
		$this->load->model('Auth_model');	
		$this->load->model('Model_Sms', 'sms_model');
		$this->load->library('excel');
		//print_r($this->session->userdata('admin')['role_id']);	
	}

	// vijay :	 
	
	public function send_sms() {

		if(!$this->isLoggedin())
			redirect('login', 'refresh');
		 
		$data['id'] = $this->session->userdata('admin')['id'];
		if(isset($_SESSION['session_array'])){
			unset($_SESSION['session_array']);
			log_message('error' , 'check Session data');
			//log_message('error' , json_encode($_SESSION['session_array']));
		}
		
		$data['title'] = 'Send SMS || Bipa' ;
		$data['breadcrumb'] = "SMS Panel";
		$data['contacts'] = $this->Auth_model->get_contact();
		$data['template_assign'] = $this->template_model->get_templates();
		$data['recent_uploads'] =  $this->sms_model->get_bulk_upload_list();
		 
		$this->load->view('sms/sendsms',$data);
    }

    // 21-08-2021 :: validate_phone
    public function validate_phone($mobile)
    {
    	if(!validate_mobile_number($mobile)) {
			$this->form_validation->set_message('validate_phone', 'Invalid Mobile Number."');
            return FALSE;
		} 
    }
          
            

 

	public function send_sms_index() {
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

			// No Quota limts For Admin 				
			if($this->session->userdata('admin')['role_id'] != '1'){
				if($quota == 0){
					echo json_encode(array('st' => 2, 'msg' => "You Can't Send messages Please contact admin..Quota Exceed it's limit"));die();
					
				}
				else{
					if($quota >= $finalcnt){

						// $this->Auth_model->insert_sms();
						$result = $this->Auth_model->insert_sms();
						if($result){
							$this->Auth_model->sms_quota_update($finalcnt);
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
					echo json_encode(array('st' => 0, 'msg' => "Message sent successfully"));die();
				}
				else{
					echo json_encode(array('st' => 2, 'msg' => "Send Sms Fail, please Try after some time."));die();
				}
			} 				

            // $this->Auth_model->insert_sms();
            // echo json_encode(array('st' => 0, 'msg' => "Record updated successfully"));die();
        } else {

            $data = array(
                'mobile' => form_error('mobile[]'),
                'message' => form_error('message'),
                'date' => form_error('date'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));die();
        }
    }

    public function validateDate($date, $format = 'Y-m-d'){
	    $d = DateTime::createFromFormat($format, $date);
	    return $d && $d->format($format) === $date;
	}

    #################################################################################################
    ###  Bulk Upload Section Start here 						
    #################################################################################################
	function multi_unique($src){
		$output = array_map("unserialize",array_unique(array_map("serialize", $src)));
	   	return $output;
	}

    // vijay update : 24-06-2021
    // Save Contact From Bulk Upload list
    public function save_contact_from_list($value=''){

    	// check Array 
    	if(count($this->customer_array) > 0 ){
    		$customer_data = $this->multi_unique($this->customer_array) ;
    		// $customer_data = $this->customer_array;
    		$filtered = [] ;
    		foreach ($customer_data as $key => $value) {
    			if(!$this->Auth_model->is_customer_exist($value['mobile_no'])){
    				array_push($filtered, $value); 
    				log_message('error', 'save_contact_from_list '.$value['mobile_no']);
    			}
    		}

    		// insert in batch
    		if(count($filtered) > 0 ){
    			log_message('error', json_encode($filtered));
    			$this->db->insert_batch('contact',$filtered);
    		}
    	}

    	if(count($this->bad_customer_array) > 0 ){
    		// $bad_customer_data = $this->bad_customer_array;
    		$this->db->insert_batch('bad_contact',$this->bad_customer_array);
    		
    	}


    }
    			 

		

	// vijay update : 10-06-2021
	public function bulkuploadSMS(){							 
		$data["title"] = 'SMS Bulk Uploads || BIPA'; 
		$data['recent_uploads'] = $this->sms_model->get_bulk_upload_list();
        $this->load->view('sms/bulk',$data);
	}       	

	// vijay update : 10-06-2021
	public function check_upload_file_exist($filename=''){
		if(!empty($filename)){
			$result = $this->db->get_where('sms_bulk_uploads' ,array('file_name' => $filename) );
			if($result->num_rows() > 0)
				return $result->row();
			else
				return false;
		}
		return false ;
		// Response error 
	}

	// vijay update : 10-06-2021
	public function get_selected_file_name($file_id)
	{
		$result = $this->db->get_where('sms_bulk_uploads' ,array('id' => $file_id) );
		$file = $result->row();
		return $file->file_name;
		
	}

	//  this function filter a list and make simple array  
	public function make_bulk_upload_list($path, $scheduletime, $bulk_session_id='', $queue_session_id='' ){
		if(!$this->isLoggedin())
			redirect('login', 'refresh');

		// check for scheduel date time
	    if($scheduletime=='on'){
            $schedule_date_time = date('Y-m-d H:i', strtotime($this->input->post('date')));
            $schedule_flag='0';
        }
        else{
            $schedule_date_time = date('Y-m-d H:i');
            $schedule_flag='1';
        }	
        $bad_customer_data =[] ;
        $customer_data =[] ;
		$object = PHPExcel_IOFactory::load($path);
		foreach($object->getWorksheetIterator() as $worksheet){

			$highestRow = $worksheet->getHighestRow();
			for($row=2; $row<=$highestRow; $row++){

				$bad_records = false ;
				$send_to = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
				// $mobile = str_replace("'","",$send_to) ;
				$mobile = trim($send_to,'\'"');
				$mobile = preg_replace("/[^0-9]/", '', $mobile);
				log_message('error', $mobile);	

				if(!validate_mobile_number($mobile)){

					$bad_records = true ;
					// $data['file_upload'] = "Invalid Mobile Number in line no $row value is $mobile";
					// echo json_encode(array('status' => 'fail', 'msg' => $data  ));die();
				}

				$first_name = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
				$last_name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
			    $email = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
			    $reference_id = $worksheet->getCellByColumnAndRow(4, $row)->getValue();					

				/*if(is_numeric($mobile) ){
				}
				else{
					$data['file_upload'] = "Invalid Mobile Number in line no $row value is $mobile";
					echo json_encode(array('status' => 'fail', 'msg' => $data  ));die();
				}*/

				if($bad_records){
					$bad_customer_data[] = array(
						'first_name' 	=>	$first_name,
						'last_name' 	=>	$last_name,
						'email' 		=>	$email,
						'mobile_no' 	=>	$mobile,
						'reference' 	=>	$reference_id,
						'campaign_name' 	=>	$this->input->post('file_upload_type') == 'overwrite' ? $_FILES["file"]["name"] : $bulk_session_id,
						'created_by' 	=>	$this->session->userdata('admin')['id'],
					);
				}
				else{
					// Good Records 
					$customer_data[] = array(
						'first_name' 	=>	$first_name,
						'last_name' 	=>	$last_name,
						'email' 		=>	$email,
						'mobile_no' 	=>	$mobile,
						'reference' 	=>	$reference_id,
						'created_by' 	=>	$this->session->userdata('admin')['id'],
					);
					// message array 					 
					$data[] = array(
						'send_to' =>	$mobile,
						'send_from' =>	'BIPA',
						'message' =>	$this->input->post('message'),
						'message_type_flag' => '1',
						'status' =>	'0',
						'schedule_flag'  => $schedule_flag,
						'schedule_time'  => $schedule_date_time,
						'queue_session'  => $queue_session_id,
						'created_by'  => $this->session->userdata('admin')['id'],
						'bulk_session_id' => $this->input->post('file_upload_type') == 'overwrite' ? $_FILES["file"]["name"] : $bulk_session_id
					);
				}
				$this->customer_array = $customer_data ;
				$this->bad_customer_array = $bad_customer_data ;
													           
			}	/* Close For Loop Here */
		
		}	/* Close Foreach Loop Here */
		log_message('error', 'CUSTOMER GOOD DATA');									           
		log_message('error', json_encode($this->customer_array));
		log_message('error', 'CUSTOMER BAD DATA');									           
		log_message('error', json_encode($this->bad_customer_array));

		return $data ;
	}

				
				
				
    
    // vijay update : 10-06-2021    
    // this function is responsible to upload lsit
	public function import(){
		if(!$this->isLoggedin())
			redirect('login', 'refresh');

		/*log_message('error', 'CHECK POAST DATA');
		log_message('error', json_encode($_POST));*/

			 
		
		$bulk_session_id="Bulk_".uniqid()."_".rand(0,1000000);	// Make Ther Unique BULK Upload ID 
		$queue_session_id = md5(microtime() . mt_rand());	// Make Ther Unique BULK Upload ID 
		
		if($this->input->post('select_file_name') == 0){
			$this->form_validation->set_rules('list_name', 'List Name', 'trim|required|is_unique[sms_bulk_uploads.list_name]');
		}
		$this->form_validation->set_rules('message', 'Message', 'trim|required');//|max_length[1600]


		if($this->form_validation->run()==false){
			// Error Handling no File Select 
			if($this->input->post('select_file_name') == 0){
				$data = array(
	                'list_name' => form_error('list_name'),
	                'message' => form_error('message'),
	            );
			}
			else{
				$data = array(
	                'message' => form_error('message'),
	            );	
			}
            echo json_encode(array('status' => 'fail', 'msg' => $data));die();
		}
		else{

			$schedule_time = $this->input->post('scheduletime');
			if($schedule_time=='on'){
	            $schedule_date_time = date('Y-m-d H:i', strtotime($this->input->post('date')));
	            $schedule_flag='0';
	        }
	        else{
	            $schedule_date_time = date('Y-m-d H:i');
	            $schedule_flag='1';
	        }
 			
 			// fetch avialable sms quota for loggedin user
 			$quota = $this->Auth_model->get_quota_sms();

			// Read and store for new file if User Add New File 
			if(isset($_FILES["file"]["name"]) && !empty($_FILES["file"]["name"]) ){

				$path = $_FILES["file"]["tmp_name"];
				// Check file exist or not 
				if($this->input->post('file_upload_type') != 'overwrite'){
					$result = $this->check_upload_file_exist(basename($_FILES["file"]["name"], '.csv')); 
					if($result != false){
						$list_id = $result->id;
						if($this->input->post('file_upload_type') != 'no_new'){
							$data['file_upload'] = "you want to update file";
							echo json_encode(array('status' => 'fail', 'error_type' => '1' , 'msg' => $data  ));die();
						}
					}
				}

				
				$this->db->trans_start();
        		$this->db->trans_strict(FALSE);

				/* make bulk Upload data from file */
			    $data = $this->make_bulk_upload_list($path, $schedule_time, $bulk_session_id, $queue_session_id);

			    // Check for Qouta avialablty
			    $msgLnth = strlen($this->input->post('message'));
            	$finalcnt = (floor($msgLnth/153)+1) * count($data);
            	
            	// No Quota limts For Admin 				
				if($this->session->userdata('admin')['role_id'] != '1'){
	            	if($quota < $finalcnt){
	            		$data['file_upload'] = "Your available quota limit is $quota, less than that required $finalcnt";
						echo json_encode(array('status' => 'fail', 'msg' => $data));die();
	            	}
				}


				
				// File Remove first before save it in case of overwrite
				if($this->input->post('file_upload_type') == 'overwrite'){
					// remove old file in case or overwrite
					$file_with_path = 'uploads/'.$_FILES["file"]["name"] ;
					log_message('error', ' file_with_path '.$file_with_path);
					if(file_exists($file_with_path)){
						unlink($file_with_path);
					}

				}
				
           		// Save uploaded File first	           		 
				$new_name = $this->input->post('file_upload_type') == 'overwrite' ? $_FILES["file"]["name"] : $bulk_session_id;
	            $config['file_name'] =  $new_name;
				$config['upload_path']          = './uploads';
				$config['allowed_types']        = 'xls|xlsx|csv';
				$config['max_size']             = 1000000;
				$this->load->library('upload', $config);
			

				if($this->upload->do_upload('file')){
	            	// Save Customer Details 
	            	$this->save_contact_from_list();
	            	if($this->session->userdata('admin')['role_id'] != '1'){
		            	$this->Auth_model->sms_quota_update($finalcnt);	// update SMS Quota 	
		            }

	            	// file Upload Successfully
	            	$this->Auth_model->insert_bulk($data); // insert bukl data in to database 	



					// Dont make any change in here in case of overwite
					if($this->input->post('file_upload_type') != 'overwrite'){

						// save new list record 
			           	$upload_data['list_name'] = $this->input->post('list_name');
			           	$upload_data['description'] = $this->input->post('description');
			           	$upload_data['file_name']	= $new_name;
						$upload_data['slug'] = str_replace(" ", "-", strtolower($this->input->post('list_name')))  ;
						$upload_data['total_count'] = count($data);
						$upload_data['created_by']  = $this->session->userdata('admin')['id'];
						$this->db->insert('sms_bulk_uploads', $upload_data);
						$list_id = $this->db->insert_id();
						


						
					}
					else{
						// if File Overwrite than update Count In Mail list Table
						$new_name = basename($_FILES["file"]["name"], '.csv') ;
						$this->db->where('file_name', $new_name);
						$this->db->update('sms_bulk_uploads', array('total_count' =>count($data) ,'updated_at' => date('Y-m-d H:i:s', strtotime('now')) ));
						/*log_message('error', 'Check Log File 2');
						log_message('error', $this->db->last_query());*/


						// if file exist get list id
						$result = $this->check_upload_file_exist(basename($_FILES["file"]["name"], '.csv')); 
						$list_id = $result->id;
						/*log_message('error', 'Check Log File ');
						log_message('error', json_encode($result));
						log_message('error', $list_id);*/

					}

					// save Relation 
					$relation_data['list_id'] = $list_id ;
					//$relation_data['file_name']	= $new_name;
					$relation_data['message'] = $this->input->post('message');
					$relation_data['total_count'] = count($data);
					$relation_data['out_queue_session'] = $queue_session_id;
					$relation_data['schedule_flag']	= $schedule_flag ;
					$relation_data['schedule_time']	= $schedule_date_time ;
					$relation_data['created_at']  = date('Y-m-d H:i', strtotime('now'));
					$relation_data['created_by']  = $this->session->userdata('admin')['id'];
					
					$this->db->insert('queue_bulk_relation', $relation_data);

					$this->db->trans_complete();
			        if ($this->db->trans_status() === FALSE) {

			            $this->db->trans_rollback();
			           $data['file_upload'] = "file upload Fial, Please try after some time";
						echo json_encode(array('status' => 'fail', 'msg' => $data));die();
			        } else {

			            $this->db->trans_commit();
			            echo json_encode(array('status' => 'success', "message" => "Data Imported successfully"));die();
			        }


					
				}
				else{
					//$error = array('error' => $this->upload->display_errors());
					$data['file_upload'] = $this->upload->display_errors();
					echo json_encode(array('status' => 'fail', 'msg' => $data));die();
				}
	           	 


				  	
			   // }
			 //   $data['file_upload'] = "file upload Fial, Please try after some time";
				// echo json_encode(array('status' => 'fail', 'msg' => $data));die();			
			}
			else{

				// this is responsible for file slected from dropdown
				if($this->input->post('select_file_name') > 0){					
					$selected_file = $this->get_selected_file_name($this->input->post('select_file_name'));
					// file path 
					$path = 'uploads/'.$selected_file.'.csv' ;
					log_message('error', 'check selcted file ');
					log_message('error', $path);
					if(file_exists($path)){
						/*Code Remove from here*/
					    $data = $this->make_bulk_upload_list($path, $schedule_time, $selected_file, $queue_session_id);
						

						// Check for Qouta avialablty
					    $msgLnth = strlen($this->input->post('message'));
		            	$finalcnt = (floor($msgLnth/153)+1) * count($data);
		            	
		            	// No Quota limts For Admin 				
						if($this->session->userdata('admin')['role_id'] != '1'){
			            	if($quota < $finalcnt){
			            		$data['file_upload'] = "Your available quota limit is $quota, less than that required $finalcnt";
								echo json_encode(array('status' => 'fail', 'msg' => $data));die();
			            	}
						}
						/* START TRANS HERE */
						$this->db->trans_start();
        				$this->db->trans_strict(FALSE);

						$res = $this->Auth_model->insert_bulk($data);
						if($this->session->userdata('admin')['role_id'] != '1'){
							$this->Auth_model->sms_quota_update($finalcnt);
						}
						// save Relation 
						$relation_data['list_id'] = $this->input->post('select_file_name') ;
						//$relation_data['file_name']	= $new_name;
						$relation_data['message'] = $this->input->post('message');
						$relation_data['total_count'] = count($data);
						$relation_data['out_queue_session'] = $queue_session_id;
						$relation_data['schedule_flag']	= $schedule_flag ;
						$relation_data['schedule_time']	= $schedule_date_time ;
						$relation_data['created_at']  = date('Y-m-d H:i', strtotime('now'));
						$relation_data['created_by']  = $this->session->userdata('admin')['id'];
						$this->db->insert('queue_bulk_relation', $relation_data);
						
						$this->db->trans_complete();
				        if ($this->db->trans_status() === FALSE) {

				            $this->db->trans_rollback();
				           $data['file_upload'] = "file upload Fial, Please try after some time";
							echo json_encode(array('status' => 'fail', 'msg' => $data));die();
				        } else {

				            $this->db->trans_commit();
				            // file Uploaded with selected file 
							echo json_encode(array('status' => 'success', "message" => "Data Imported successfully, Selected file is $selected_file"));die();
				        }
				        /* START TRANS CLOSE HERE */
				    }
					else{
						$data['file_upload'] = 'file not exist on path';
						echo json_encode(array('status' => 'fail', 'msg' => $data  ));die();
					}
				}
				else{
					$data['file_upload'] = 'Please select file to upload';
					echo json_encode(array('status' => 'fail', 'msg' => $data  ));die();
				}
			}
					 
				

		}
	}


	/*public function delete_campaign($value=''){
		$campaign_id = $this->uri->segment(3);
		$file_name = $this->uri->segment(5);
		if($campaign_id > 0 && $file_name){}
	}*/


	#################################################################################################
    ###  ..../// Bulk Upload Section Close here 						
    #################################################################################################

	// vijay update : 10-06-2021
	public function download_files($file_name='')
    {
    	if(!$this->isLoggedin())
			redirect('login', 'refresh');
         
        $this->load->helper('download');

        if(!empty($file_name) ){
           
            //$path = 'assets/web/pdf/'.$file_name;
           // $pdf_path = base_url().'assets/web/pdf/'.$file_name;
            
            $pdf_name = $file_name ;
            $file = 'uploads/'.$pdf_name ;

            $data = file_get_contents($file); // Read the file's contents
            force_download(basename($file), $data);
        }
        
    }

	// Vijay : 17-06-2021
	public function get_interaction()
	{
        // $mob=$this->input->post('mobile');
		$history=array();
		$mobile=array();

		$mobile = $this->input->post('mobile');
		log_message('error', 'get_interaction');
		log_message('error', json_encode($mobile));

		if(isset($_SESSION['session_array']) && !empty($mobile)){
			log_message('error', 'IF SESSION IN IF BLOCK ');
			log_message('error', 'SESSION COUNT '. count($_SESSION['session_array']));
			log_message('error', 'MOBILE COUNT '. count($mobile) );
			if(count($mobile) > count($_SESSION['session_array'])){
				log_message('error', 'if mobile is greater than ');
				// Here for Add value 
				$history = $_SESSION['session_array'] ;
				foreach ($mobile as $key => $value) {
					log_message('error', 'KEY '.$key .' VALUE '.$value);
					// condtion true to check if $history empty 
					if(count($history)==0):
						//push
						array_push($history,$value);
						$result = $value;
						
					else:
						//compare session array and mobile 
						 log_message('error', json_encode($history));
						// log_message('error', json_encode($mobile));
						//store session array into history veriable and compare
						$result = in_array($value, $history);
						if($result == false){
							array_push($history,$value);
							$result = $value;
							break;
						}
						
					endif;
				}
				$_SESSION['session_array'] = $history;
			}
			else if(count($mobile) < count($_SESSION['session_array'])){
				// log_message('error', 'SESSION ARRAY indelete section ');
				// log_message('error', json_encode($_SESSION['session_array']));
				// // Here for delete value
				// log_message('error', 'if mobile is less than ');
				$history = $_SESSION['session_array'] ;
				// log_message('error', json_encode($_SESSION['session_array']) );
				// log_message('error', json_encode($history) );
				// log_message('error', '======================= MOBILE ');
				// log_message('error', json_encode($mobile) );
				$result ='';
				foreach($history as $key => $value ){
					$result = in_array($value, $mobile);
					if($result == false){
						log_message('error', 'SEARCH FOR  '. $value);
						// get the key of value in an array
						$arr_key = 	array_search($value, $history);

						log_message('error', 'array key  ');
						log_message('error', $arr_key);
						unset($history[$arr_key]);
						// array_push($history,$value);
						$result = $value;
						break;
					}
				}
				log_message('error', 'after unset');
				log_message('error', json_encode($history));
				
				// check last array index here 
				$last_index = end($history);
				$result = str_replace('"', "", $last_index);
				log_message('error', 'LAST INDEX');
				log_message('error', $result);

				

				 

				$_SESSION['session_array'] = $history; 
				//echo "TO REMOVE VAL" ; die();
			}
		}	
		else{
			log_message('error', 'if session is not set ');
			if($mobile !=null){

				foreach ($mobile as $key => $value) {
					log_message('error', 'KEY '.$key .' VALUE '.$value);
					array_push($history,$value);
					$result = $value;
					// store history in session 
					$_SESSION['session_array'] = $history;
					
				}
			}
			else{
				log_message('error', 'Check Session last value ');
				log_message('error', json_encode($_SESSION['session_array']));
				$get_key = key($_SESSION['session_array']);
				log_message('error', 'Check Session last value '. $get_key);

				$result = $_SESSION['session_array'][$get_key];
				log_message('error', 'Check Session last value '. $result);
				if(isset($_SESSION['session_array']))
					unset($_SESSION['session_array']);
			}
			//echo "IN ISSET " ;die();
		}
		log_message('error', 'SESSION ARRAY ');
		if(isset($_SESSION['session_array'])){
			log_message('error', json_encode($_SESSION['session_array']));
		}

		// print_r($result);
		// die();
		
        $this->sms_model->get_history_in($result);
		 
	}

	public function get_interaction_compose(){

		$mobile = $this->input->post('mobile');
        $this->sms_model->get_history_in($mobile);
	}
       

		
		 

	public function get_contact_list(){

		if(!$this->isLoggedin())
			redirect('login', 'refresh');
		
		$q = $this->input->get('q');
		if(!empty($q)){
			$this->db->limit(20);
			$this->db->like('first_name', $q);
			$this->db->or_like('mobile_no', $q);
		$results = $this->db->get('contact')->result();
			$contact = [];
			foreach ($results as $key => $value) {
				$ar = array('id'=> $value->mobile_no, 'text' => $value->first_name.'-'.$value->mobile_no ) ;
				array_push($contact, $ar);
			}
		}
		echo json_encode($contact);die();
	}



	##############################  AJAX Functions 

	public function ajax_campaign_contact_count(){
		$campaign_id = $this->uri->segment(3,0);
		if($campaign_id > 0 ){
			$result = $this->db->get_where('sms_bulk_uploads', array('id' => $campaign_id))->row();
			echo 'Total Count In selected Campaign '. $result->total_count ;die(); 
		}
		echo '' ;die();
	}

}
