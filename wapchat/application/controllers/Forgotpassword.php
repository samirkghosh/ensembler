<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Forgotpassword extends Admin_Controller {

	public function index()
	{
		$this->load->view('LoginReg/forgot-password.php');
	}

}
