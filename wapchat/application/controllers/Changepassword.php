<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Changepassword extends Admin_Controller {


	public function __construct()
	{
		parent::__construct();
		$this->load->model('Auth_model');
		//print_r($this->session->userdata('admin')['id']);	
	}

	public function index()
	{
		if(!$this->isLoggedin())
			redirect('login', 'refresh');

		$this->load->view('auth/change_password');
	}

    //FARHAN ::22-06-2021
	public function change_pass()
	{	
		if(!$this->isLoggedin())
			redirect('login', 'refresh');
		
		$password = md5($this->input->post('oldpassword'));
		$confirmpassword = $this->input->post('confirmpassword');
		$id= $this->session->userdata('admin')['id'];
		$getpassword = $this->Auth_model->check_password($id);
		
		if($password == $getpassword){
			$return = $this->Auth_model->update_password($id, $confirmpassword);
			if($return == true){
				$data = array('success' => true, 'msg'=> 'Password has been successfully Updated');
				echo json_encode($data);exit();
			}	
		}else{
			$data = array('success' => false, 'msg'=> 'Old Password is Incorrect');
			echo json_encode($data);exit();
		}
	
	}


}
