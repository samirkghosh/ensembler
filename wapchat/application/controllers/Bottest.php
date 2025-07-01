<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bottest extends Admin_Controller {
	// https://github.com/messagebird/php-rest-api
	// https://developers.messagebird.com/tutorials/send-sms-php

	 
	public function index()
	{
		$data["title"] = 'Whatsapp || BIPA';
		$this->load->view('whatsapp/wa_send_test',$data);
	}

	


}
