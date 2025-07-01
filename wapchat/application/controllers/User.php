<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends Admin_Controller {

	private $user_id ;

	function __construct() {
        parent::__construct();

        $this->user_id = $this->uri->segment(3,0);
         
    }
	 
	public function index()
	{
		$data['title'] = 'Users List || BIPA ';
		$data['user_groups'] = $this->user_model->get_user_groups();
		$data['statuslist'] = $this->customlib->getStatus();
		$data['users'] = $this->user_model->getUsers();

		$this->load->view('users/index', $data);
	}

	public function bot_session(){

		$data["title"] = 'Bot Session || BIPA';
		$data['bot_session'] = $this->user_model->getBotSession();
		$this->load->view('chat/chat_session',$data);
	}

	// Date : 10-09-2021
	public function is_emailexist($email){
		$query = $this->db->get_where('users', array('email' => $email, 'delete_status' =>'0'));
		if($query->num_rows() > 0){
			 $this->form_validation->set_message('is_emailexist', 'The {field} already exist');
			return false;
		}
		else{
			return true;
		}
	}

	// Date : 10-09-2021
	public function is_mobileexist($mobile){
		$query = $this->db->get_where('users', array('mobile' => $mobile, 'delete_status' =>'0'));
		if($query->num_rows() > 0){
			 $this->form_validation->set_message('is_mobileexist', 'The {field} already exist');
			return false;
		}
		else{
			return true;
		}
	}

	// Date : 10-09-2021
	public function isedit_emailexist($email){
		$user_eid = $this->user_id ;
		$query = $this->db->get_where('users', array('email' => $email, 'delete_status' =>'0', 'id !=' =>$user_eid  ));
		if($query->num_rows() > 0){
			 $this->form_validation->set_message('is_emailexist', 'The {field} already exist');
			return false;
		}
		else{
			return true;
		}
	}

	// Date : 10-09-2021
	public function isedit_mobileexist($mobile){
		$user_eid = $this->user_id ;
		$query = $this->db->get_where('users', array('mobile' => $mobile, 'delete_status' =>'0', 'id !=' =>$user_eid ));
		if($query->num_rows() > 0){
			 $this->form_validation->set_message('is_mobileexist', 'The {field} already exist');
			return false;
		}
		else{
			return true;
		}
	}


	public function add_user(){

		$data['title'] = 'Add New User || BIPA ';
		$data['user_id'] = 0;
		$data['user_groups'] = $this->user_model->get_user_groups();
		$data['statuslist'] = $this->customlib->getStatus();
		$data['sms_templates'] = $this->smsuser_model->get_sms_template();
		
		$this->form_validation->set_rules('fname', 'First Name', 'required');
		$this->form_validation->set_rules('lname', 'Last Name', 'required');
		$this->form_validation->set_rules('name', 'Name', 'required'); 
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_is_emailexist');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|numeric|exact_length[12]|callback_is_mobileexist');
		 
		//$this->form_validation->set_rules('', 'Mobile', 'required');

		if ($this->form_validation->run()==false){
			// show error hare
		}
		else{
			$user_data['templates_id'] = !empty($this->input->post('templates')) ? implode(",", $this->input->post('templates')) : '' ;
			$user_data['sms_quota'] = $this->input->post('sms_quota');
			$user_data['whatsapp_quota'] = 0;//$this->input->post('whatsapp_quota')
			$user_data['first_name'] = $this->input->post('fname');
			$user_data['last_name'] = $this->input->post('lname');
			$user_data['username'] = $this->input->post('name');
			$user_data['email'] = $this->input->post('email');
			$user_data['mobile'] = $this->input->post('mobile');
			$user_data['password'] = md5('bipa@1234');
			$user_data['role_id'] = $this->input->post('roles');
			$user_data['is_active'] = $this->input->post('custom_status');

			

			$return = $this->user_model->save_users($user_data);
			if($return > 0){
				// Insert Quota records to maintain quota history
				$quota_upd = ['user_id'=>$return, 'sms_update'=>$user_data['sms_quota'],'till_now_sms'=>$user_data['sms_quota'] ];
				$this->user_model->update_user_quota($quota_upd);

				$roles['role_id']	= $this->input->post('roles');
				$roles['user_id']	= $return ;
				$roles['is_active']  = '1';
				$this->db->insert('users_roles', $roles);
			}
			if($return)
				$this->session->set_flashdata('success', "User Added Successfully");
			else
				$this->session->set_flashdata('fail', "User Not Added");


			redirect('user/add_user', 'refresh');
		}


		$this->load->view('users/adduser', $data);
	}

	public function edit($user_id)
	{
		$data['title'] = 'Edit User || BIPA ';
		$data['user_id'] = $user_id;
		$data['user_groups'] = $this->user_model->get_user_groups();
		$data['statuslist'] = $this->customlib->getStatus();
		$data['user_detail'] = $this->user_model->getUsers($user_id);
		$data['sms_templates'] = $this->smsuser_model->get_sms_template();
		

		$this->form_validation->set_rules('name', 'Name', 'required'); 
		// //$this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
		// $this->form_validation->set_rules('mobile', 'Mobile', 'required|max_length[15]');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_isedit_emailexist');
		$this->form_validation->set_rules('mobile', 'Mobile', 'required|numeric|exact_length[12]|callback_isedit_mobileexist');
		 

		if ($this->form_validation->run()==false){
			// show error hare
		}
		else{
			$previous_sms = $this->user_model->get_quota_sms($user_id);
			$previous_whatsapp = $this->user_model->get_quota_whatsapp($user_id);
			 

			 //farhan :: 24-06-2021 
			$user_data['templates_id'] = !empty($this->input->post('templates')) ? implode(",", $this->input->post('templates')) : '' ;
			$user_data['username'] = $this->input->post('name');
			$user_data['first_name'] = $this->input->post('fname');
			$user_data['last_name'] = $this->input->post('lname');					
			$user_data['mobile'] = $this->input->post('mobile');
			$user_data['role_id'] = $this->input->post('roles');
			$user_data['is_active'] = $this->input->post('custom_status');
			$user_data['update_by'] = $this->session->userdata('admin')['id'];
			$user_data['update_date'] = date('Y-m-d H:i:s', strtotime('now'));

			$return = $this->user_model->save_users($user_data, $user_id);
			/*$inputsms = $this->input->post('sms_quota');
			$inputwhatsapp = $this->input->post('whatsapp_quota');
			 
			$till_now_inputsms = $till_now_inputwhatsapp ='0' ;
            if($previous_sms == $inputsms){
            	$user_data['sms_quota'] = $inputsms;
				$inputsms = 0;
				$previous_sms = 0;

			}
			else{
				$user_data['sms_quota'] = $previous_sms+$inputsms;	
				$till_now_inputsms = $inputsms+$previous_sms;

			}

			if($previous_whatsapp == $inputwhatsapp){
				$user_data['whatsapp_quota'] = $inputwhatsapp;	
				$inputwhatsapp=0;
				$previous_whatsapp=0;
			}
			else{
				$user_data['whatsapp_quota'] = $inputwhatsapp+$previous_whatsapp;	
				$till_now_inputwhatsapp = $inputwhatsapp+$previous_whatsapp;
			}
			$return = $this->user_model->save_users($user_data, $user_id);
			 
			$quota_upd = [
				'user_id'=>$user_id, 
				'previous_sms'	=>	$previous_sms, 
				'sms_update'	=> 	$inputsms, 
				'till_now_sms'	=>	$till_now_inputsms,  
				'previous_whatsapp' =>	$previous_whatsapp, 
				'whatsapp_update'	=>	$inputwhatsapp,
				'till_now_whatsapp'	=>	$till_now_inputwhatsapp, 
			];
			$this->user_model->update_user_quota($quota_upd);
			*/

				
			if($return > 0){
				$roles['role_id']	= $this->input->post('roles');
				$this->db->where('user_id', $user_id);
				$this->db->update('users_roles', $roles);
			}


			if($return)
				$this->session->set_flashdata('success', "User Update Successfully");
			else
				$this->session->set_flashdata('fail', "No update");


			redirect('user', 'refresh');
		}


		$this->load->view('users/edit', $data);
	}


	public function update_user_quota()
	{
		$flag = $this->input->post('flag');
		$user_id = $this->input->post('user_id');
		$value = $this->input->post('count_value');
		if(empty($value)){
			echo json_encode(array('status' => 'fail', 'message' => "Please Enter Quota Value"));die();
		}


		if($flag=='sms'){
			$previous_sms = $this->user_model->get_quota_sms($user_id);
			log_message('error', 'LAST sms '.$previous_sms);
			$insert['user_id'] 		=	$user_id;
			$insert['previous_sms'] = $previous_sms;
			$insert['sms_update'] 	= $value; 
			$insert['till_now_sms'] = $previous_sms+$value;
			$user_data['sms_quota'] = $previous_sms+$value;
		}
		if($flag=='whatsapp'){
			$previous_whatsapp = $this->user_model->get_quota_whatsapp($user_id);
			$insert['user_id'] 		=	$user_id;
			$insert['previous_whatsapp'] = $previous_whatsapp;
			$insert['whatsapp_update'] 	= $value; 
			$insert['till_now_whatsapp'] = $previous_whatsapp+$value;
			$user_data['whatsapp_quota'] = $previous_whatsapp+$value;
		}
		$return = $this->user_model->save_users($user_data, $user_id);
		$res = $this->user_model->update_user_quota($insert);
		log_message('error', 'HELLO KON H '.$res);
		log_message('error', 'HELLO KON H');
			log_message('error', $this->db->last_query());
		if($res > 0){
			echo json_encode(array('status' => 'success', 'message' => strtoupper($flag)." Quota updated successfully"));die();
		}
		else{
			echo json_encode(array('status' => 'fail', 'message' => "Quota not update"));die();
		}
	}




		
	public function user_groups(){
		$this->load->view('users/adduser');
	}

	public function permission(){
		
		$this->load->view('users/permission');
	}

	   //farhan :: 24-06-2021  
	public function delete(){
		$id = $this->uri->segment(3);   
		$this->user_model->delete_user($id);  
		redirect('user','refresh'); 
	}


}
