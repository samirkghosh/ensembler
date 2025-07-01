<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Admin_Controller {

	public function __construct()
	{
	
		parent::__construct();
		$this->load->model('Auth_model');
		$this->form_validation->set_error_delimiters('<span class="error">', '</span>');	
	}

	public function index(){
		
		$data['title'] = 'Login || BIPA' ;
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');

		if ($this->form_validation->run() == FALSE){

			// Show Error if invalid

		}
		else{

			// redirect when login success
            	
			// redirect when login success
			$res = $this->Auth_model->login_check();
		
            // $login_status = $this->session->userdata('first_login_status');
			//FARHAN ::24-06-2021
			if($res =='logedin'){
				$this->session->set_flashdata('fail', 'This email is already logedin, Please logout first');
				redirect('login');
			} 
			else if($res != ''){
		 		if($res == 1){
					redirect('dashboard');
			 	}else if($res == 0){
			 		redirect('changepassword');
			 	}
			}
			else{
				$this->session->set_flashdata('fail', 'Invalid username or password');
				redirect('login');
			}
		}

		$this->load->view('auth/login.php', $data);
	
	}
				
			
			

	//FARHAN ::22-06-2021
	public function logout(){

		$id= $this->session->userdata('admin')['id'];
		$res = $this->Auth_model->logout($id);
		$this->session->sess_destroy();
		redirect('login');
	}

	// : 07-07-2021
	public function forgot_password()
	{
		$data['title'] = 'Forgot Password || BIPA' ;
		$this->load->view('auth/forgot-password.php', $data);
	}

	// Date : 01-10-2021
	public function user_release(){
		
		$data['title'] = 'User Release || BIPA' ;
		$data['loggedin_users'] = $this->user_model->getUsers($id='', 1);
		$this->load->view('auth/user_release.php', $data);
	}

	public function userrelease(){
		$id = $this->uri->segment(3);
		$data['active_login']  = '0' ;  
		$this->user_model->save_users($data, $id );  

		redirect('login/user_release','refresh'); 
	}


}
