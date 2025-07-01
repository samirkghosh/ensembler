<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
	
		$this->load->model('Auth_model');	
	}

	public function index()
	{
		$this->form_validation->set_rules('username', 'Username', 'required'); 
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required');

		if ($this->form_validation->run() == FALSE){

			// Show Error if invalid

		}
		else{

			// redirect when registration success
			$res = $this->Auth_model->user_check();

			if($res){
				$this->session->set_flashdata('success', 'Successfully registered');
				redirect('login');
			}
			else{
				$this->session->set_flashdata('fail', 'Email already exists try different');
				redirect('register', 'refresh');
			}

		}
		    $this->load->view('LoginReg/register.php');
	}

}
