<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Agentbot extends Admin_Controller {
	// https://github.com/messagebird/php-rest-api
	// https://developers.messagebird.com/tutorials/send-sms-php
	private $conversation_id ;
	private $agent_id ; 
	private $bot_chat_session ; 
	private $msisdn ; 
	private $customer_name ; 

	public function __construct($value=''){
		parent::__construct();
		 
		// print_r($_SESSION);
		// print_r($this->session);
		// $agent_id = $this->session->userdata('admin')['id'];
		$agent_id = $_SESSION['userid'];
		$conversation = $this->db->get_where('bot_chat_session', array('user_id' => $agent_id) )->row();		 
		$customer = $this->auth_model->get_contact($conversation->from);
		$this->agent_id = $agent_id;
		$this->conversation_id = $conversation->conversation_id;
		$this->bot_chat_session = $conversation->chat_session;
		$this->msisdn = $conversation->from;
		$this->customer_name = !empty($customer) ? $customer : 'Customer';
	}
	 
	public function index()
	{
		$data["title"] = 'Whatsapp || BIPA';
		$this->load->view('whatsapp/whatsappsms',$data);
		//// API KEY : 1kXyRIAeyyjCQbvEtJxzjQVWG    PROD lJCQOmCOjaWKOzPx4jynuhT3M
		// Channel ID:89d5d7a0-55ae-4465-ace9-1c8c5467aac9
		// echo '<pre>' ;
	}

	public function agentwindow(){

		$data["title"] = 'Bot Request';		 
		//$agent_id = $this->session->userdata('admin')['id'];
		$agent_id = $_SESSION['userid'];
		$conversation = $this->db->get_where('bot_chat_session', array('user_id' => $agent_id) )->row();
		$data['conversation_id'] = $conversation->conversation_id ;
		$data['agent_id'] = $agent_id ;
		$data['customer_name'] = $this->customer_name ;
		$data['current_session'] = $conversation->chat_session ;

		$data['previous_bot_session'] = $this->wa_model->get_previous_bot_by_msisdn($this->msisdn);
		$data['current_bot_chat'] = $this->wa_model->get_current_bot_chat($conversation->chat_session);
		$this->load->view('whatsapp/wa_send',$data);

	}


	// Ajax functioin 

	public function agentjoin_customer_bot_ajax($value='')
	{
		// echo json_encode($_POST);die();
		$agent_id = $_SESSION['userid'];		
		$conversation_id = $this->input->post('conversation_id');	

		$agent_current_chat = $this->db->get_where('bot_chat_session', array('user_id' => $agent_id) )->row();
		$conversation = $this->db->get_where('bot_chat_session', array('conversation_id' => $conversation_id) )->row();

		// if(empty($agent_current_chat)){
		// 	echo json_encode(array("status" => "close", "msg" => "Chat Session Closed, Now you can join new session"));die();
		// }

		if($agent_current_chat->user_id > 0){
			echo json_encode(array("status" => "fail", 'da' => $agent_current_chat, "msg" => "First close your previous chat"));die();
		}



		if($conversation->user_id > 0){
			// already agent asign
			echo json_encode(array("status" => "fail", "msg" => "Chat already joined by other agent"));die();
		}
		else{
			// update over all conversation
			$this->db->where('conversation_id' , $conversation_id);
			$this->db->update('overall_bot_chat_session', array('user_id' => $agent_id, 'bot_agent_flag' => '1'));
			log_message('error', 'QQQQQQQQQQ!!!!!!!!!!!!');
			log_message('error', $this->db->last_query());
			// update bot conversation 
			$this->db->where('conversation_id' , $conversation_id);
			$this->db->update('bot_chat_session', array('user_id' => $agent_id));
			log_message('error', 'QQ@222222');
			log_message('error', $this->db->last_query());
			$result = $this->db->affected_rows();
			if($result > 0){
				echo json_encode(array("status" => "success", "msg" => "You have successfully joind with customer, will redirect to chat window."));die();
			}
			else{
				echo json_encode(array("status" => "fail", "msg" => "Customer Chat Join fail."));die();

			}

		}
	}

	// Force Chat close by agent button click
	/*public function agent_close_chat_ajax($value='')
	{
		// echo json_encode($_POST);die();
		$agent_id = $this->session->userdata('admin')['id'];		
		$conversation_id = $this->input->post('conversation_id_close');		

		$this->io_model->save_wa_out_queue($conversation_id, 'We have not got any response from your side and we are closing this session.');
		$this->io_model->save_wa_out_queue($conversation_id, 'Thank you and have a nice day!');

		$this->db->where('from',$value->from);
	    $this->db->delete('bot_chat_session') ;	

		$agent_current_chat = $this->db->get_where('bot_chat_session', array('user_id' => $agent_id) )->row();
		$conversation = $this->db->get_where('bot_chat_session', array('conversation_id' => $conversation_id) )->row();

		if(empty($agent_current_chat)){
			echo json_encode(array("status" => "close", "msg" => "Chat Session Closed, Now you can join new session"));die();
		}

		if($agent_current_chat->user_id > 0){
			echo json_encode(array("status" => "fail", "msg" => "First close your previous chat"));die();
		}
	}*/


		



	public function send_whatsapp_message($value='')
	{
		$conversation_id = $this->input->post('conversation_id');
		$message = $this->input->post('bot_message');
		$type =   'text';
		$bot_chat_session = $this->bot_chat_session;

		$conversation = $this->db->get_where('bot_chat_session', array('conversation_id' => $conversation_id) )->row();
		if(!empty($conversation)){
			// save message in out queue
			$insert_id = $this->io_model->save_wa_out_queue($conversation_id, $message, $type , $bot_chat_session);
			echo json_encode(array("status" => "success", "msg" => "Message Sent", "last_out_id" => $insert_id));die();
		}
		else{
			echo json_encode(array("status" => "close", "msg" => "Chat Closed."));die();
			
		}
	}





	public function agent_customer_conversations(){
		
		$conversations = $this->wa_model->get_agent_customer_conversations($this->bot_chat_session, $this->agent_id);
		$conversations_r = [];
		foreach ($conversations as $key => $value) {
			$value->createdDatetime = date('d M H:s a', strtotime($value->createdDatetime));
			$conversations_r[] = $value;
		}


		echo json_encode(array("status" => "success", "msg" => "conversation list", "list" => $conversations_r));die();

	}

	public function get_bot_by_session(){
		$chat_session_id = $this->input->post('chat_session_id');
		if(!empty($chat_session_id)){
			$conversations = $this->wa_model->bot_by_session($chat_session_id);
			// $conversations = $this->db->get_where('bot_chat_session', array('chat_session' => $chat_session_id) )->row();
			$conversations_r = [];
			foreach ($conversations as $key => $value) {
				$value->createdDatetime = date('d M H:s a', strtotime($value->createdDatetime));
				$conversations_r[] = $value;
			}
			echo json_encode(array("status" => "success", "msg" => "conversation list", "list" => $conversations_r));die();
		}
		echo json_encode(array("status" => "fail", "msg" => "List not found" ));die();
	}
			
			



		



	


}
