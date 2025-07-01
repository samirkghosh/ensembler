<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends Admin_Controller {


	 
	public function index()
	{
		$name = $_SESSION['username'];
		$data['username'] =  $name; 
		$data['title'] ='Dashboard || BIPA ';

		if(!isset($_SESSION['username'])){
			redirect('login');	
		}
		$this->load->view('dashboard',$data);
	}


}
