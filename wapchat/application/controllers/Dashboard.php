<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller {

	public  function __construct() {
		parent::__construct();
		//print_r($this->session->userdata('admin'));
	}

	 
	public function index()
	{
		if(!$this->isLoggedin())
			redirect('login', 'refresh');

		$name = $this->session->userdata('admin')['username'];
		$data['title'] ='Dashboard || BIPA ';
		$data['username'] =  $name; 
		 $data['breadcrumb'] = "Dashboard";

		$this->load->view('dashboard',$data);
	}


	public function get_dashboard_box(){
		
		
		//FARHAN::25-06-2021

		// QUEUED
		$data['sms_queue_today'] = count($this->dashboard_model->total_sms_queue('today'));
		$data['sms_queue_yesterday'] = count($this->dashboard_model->total_sms_queue('yesterday'));
		$data['sms_queue_y2'] = count($this->dashboard_model->total_sms_queue('y2'));
		$data['sms_queue_y3'] = count($this->dashboard_model->total_sms_queue('y3'));
		$data['sms_queue_y4'] = count($this->dashboard_model->total_sms_queue('y4'));

		// SUBMITTED
		$data['sms_submitted_today'] = count($this->dashboard_model->total_sms_submitted('today'));
		$data['sms_submitted_yesterday'] = count($this->dashboard_model->total_sms_submitted('yesterday'));
		$data['sms_submitted_y2'] = count($this->dashboard_model->total_sms_submitted('y2'));
		$data['sms_submitted_y3'] = count($this->dashboard_model->total_sms_submitted('y3'));
		$data['sms_submitted_y4'] = count($this->dashboard_model->total_sms_submitted('y4'));

        // DELIVERED
		$data['sms_delivered_today'] = count($this->dashboard_model->total_sms_delivered('today'));
		$data['sms_delivered_yesterday'] = count($this->dashboard_model->total_sms_delivered('yesterday'));
		$data['sms_delivered_y2'] = count($this->dashboard_model->total_sms_delivered('y2'));
		$data['sms_delivered_y3'] = count($this->dashboard_model->total_sms_delivered('y3'));
		$data['sms_delivered_y4'] = count($this->dashboard_model->total_sms_delivered('y4'));

		// UNDELIVERED
		$data['sms_undelivered_today'] = count($this->dashboard_model->total_sms_undelivered('today'));
		$data['sms_undelivered_yesterday'] = count($this->dashboard_model->total_sms_undelivered('yesterday'));
		$data['sms_undelivered_y2'] = count($this->dashboard_model->total_sms_undelivered('y2'));
		$data['sms_undelivered_y3'] = count($this->dashboard_model->total_sms_undelivered('y3'));
		$data['sms_undelivered_y4'] = count($this->dashboard_model->total_sms_undelivered('y4'));

		//25-06-2021 :: END

		// farhan :: 05-07-2021:: Quota utilized
		$data['quota_utilized_today'] = $this->dashboard_model->total_quota_utilized(true);
		$data['quota_utilized_till_now'] =$this->dashboard_model->total_quota_utilized(false);
		// END
		
		$data['sms_sent_today'] = count($this->dashboard_model->total_sms_sent(true));
		$data['sms_sent_till_now'] = count($this->dashboard_model->total_sms_sent(false));
		$data['whatsapp_sent_today']  = count($this->dashboard_model->total_whatsapp_sent(true));
		$data['whatsapp_sent_till_now']= count($this->dashboard_model->total_whatsapp_sent(false));
		$data['whatsapp_received_today']  = count($this->dashboard_model->total_whatsapp_received(true));
		$data['whatsapp_received_till_now']= count($this->dashboard_model->total_whatsapp_received(false));
		$data['sms_received_today']= count($this->dashboard_model->total_sms_recieved(true));;
		$data['sms_received_till_now']= count($this->dashboard_model->total_sms_recieved(false));;

		$data['live_conversion']= 0;
		$data['monthly_active_whatsapp']= 0;
		$data['active_bot']= count($this->dashboard_model->total_active_boat(true));
		$data['active_bot_till_now']= count($this->dashboard_model->total_active_boat(false));

		//success bot 
		$data['success_bot_session_today'] = count($this->dashboard_model->success_bot_session(true));
		$data['success_bot_session_yesterday'] = count($this->dashboard_model->success_bot_history('yesterday'));
		$data['success_bot_session_y2'] = count($this->dashboard_model->success_bot_history('y2'));
		$data['success_bot_session_y3'] = count($this->dashboard_model->success_bot_history('y3'));
		$data['success_bot_session_y4'] = count($this->dashboard_model->success_bot_history('y4'));
		$data['success_bot_session_till_now'] = count($this->dashboard_model->success_bot_session(false));

		//converted Agent
		$data['coverted_agent_bot_today'] = count($this->dashboard_model->coverted_agent_bot(true));
		$data['coverted_agent_bot_yesterday'] = count($this->dashboard_model->converted_agent_history('yesterday'));
		$data['coverted_agent_bot_y2'] = count($this->dashboard_model->converted_agent_history('y2'));
		$data['coverted_agent_bot_y3'] = count($this->dashboard_model->converted_agent_history('y3'));
		$data['coverted_agent_bot_y4'] = count($this->dashboard_model->converted_agent_history('y4'));
		$data['coverted_agent_bot_till_now'] = count($this->dashboard_model->coverted_agent_bot(false));
		$data['templates']= 3;
		// $data['sms_received_till_now']= 0;

		echo json_encode(array('status' => 'success', 'info' => $data));die();
	}

	/*public function get_today_in_sms()
	{
		$r = $this->dashboard_model->today_notifications();
		echo json_encode($r);die();
		// print_r($r);

	}*/


	public function get_today_in_sms()
	{
		$r = $this->dashboard_model->today_notifications();
		
        $output='';
		if(!empty($r)){	
		foreach ($r as $val) {
			$getname =$this->dashboard_model->get_name($val['send_from']);
			if($getname!=''){
			  $name = $getname;
			}else{
			  $name = $val['send_from'];
			}
			$message = $val['message'];
			$createdate = $this->get_time_difference_php($val['create_date']);
			
			// $output.='<div class="card-comment bg-white"><div class="comment-text"><span class="username-notif">'.$name.'<span class="text-muted float-right">'.$createdate.'</span></span><span class="text-notif card bg-coral" title="'.$message.'">'.substr($message, 0,60).'</span></div></div>';
			$output.='<div class="media mt-1" style="border-bottom: 1px solid #2222">
			<img src="'.base_url('assets/dist/img/notif.jpg').'" alt="User Avatar" class="img-size-50 mr-3">
			<div class="media-body">
			  <h3 class="dropdown-item-title">
			  '.$name.'
				<span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
			  </h3>
			  <p class="text-sm" title="'.$message.'" style="cursor:pointer;margin-top: 0;margin-bottom: 3px">'.substr($message, 0,20).'</p>
			  <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i>'.$createdate.'</p>
			</div>
		  </div>';
	    }
	    }
		echo $output;
		//print_r($r);die();
	
	}

	public function get_time_difference_php($created_time)
	{
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

	// 20-08-2021 :: 
	public function unauthorized() {
        $data = array();
        $data['title'] = 'Access Denied || Bipa' ;
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }


}
