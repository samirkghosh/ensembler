<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

// createdDatetime to create_datetime 

class Agentwebchatbot extends Admin_Controller {
	// https://github.com/messagebird/php-rest-api
	// https://developers.messagebird.com/tutorials/send-sms-php
	private $conversation_id ;
	private $agent_id ; 
	private $bot_chat_session ; 
	private $msisdn ; 
	private $customer_name ; 

	public function __construct($value=''){
		parent::__construct();
		//$db = $this->load->database('database2', TRUE);
		// print_r($_SESSION);
		// print_r($this->session);
		// $agent_id = $this->session->userdata('admin')['id'];
		$agent_id = $_SESSION['userid'];
		$chat_session = $_GET['chat_session'];
		// $conversation = $this->db->get_where('bot_chat_session', array('user_id' => $agent_id) )->row();	
		$conversation = $this->db->get_where('bot_chat_session', array('chat_session' => $chat_session) )->row();
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
	}

	public function agentwindow(){

		$data["title"] = 'HELB chat';		 
		//$agent_id = $this->session->userdata('admin')['id'];
		$agent_id = $_SESSION['userid'];
		$chat_session = $_GET['chat_session'];
		$conversation = $this->db->get_where('bot_chat_session', array('chat_session' => $chat_session) )->row();
		// $conversation = $this->db->get_where('bot_chat_session', array('user_id' => $agent_id) )->row();
		
		$data['conversation_id'] = $conversation->conversation_id;
		$data['agent_id'] = $agent_id ;
		$data['customer_name'] = $this->customer_name ;
		$data['current_session'] = $conversation->chat_session;
		$data['previous_bot_session'] = $this->wa_model->get_previous_bot_by_msisdn($this->msisdn);
		$data['current_bot_chat'] = $this->wa_model->get_current_bot_chat($conversation->chat_session);
		$this->load->view('webchat/wa_send',$data);
	}


	// Ajax functioin 
	public function agentjoin_customer_bot_ajax($value=''){
		$agent_id = $_SESSION['userid'];		
		$conversation_id = $this->input->post('conversation_id');	
		$agent_current_chat = $this->db->get_where('bot_chat_session', array('user_id' => $agent_id) )->row();
		$conversation = $this->db->get_where('bot_chat_session', array('conversation_id' => $conversation_id) )->row();
		if($agent_current_chat->user_id > 0){
			// echo json_encode(array("status" => "fail", 'da' => $agent_current_chat, "msg" => "First close your previous chat"));die();
		}
		if($conversation->user_id > 0){
			// already agent asign
			echo json_encode(array("status" => "fail", "msg" => "Chat already joined by other agent"));die();
		}else{
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
				echo json_encode(array("status" => "fail", "msg" => "Customer Chat Join fail.", 'data' =>$_SESSION));die();

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


		



	public function send_whatsapp_message($value = ''){
		$companyId = $_SESSION['companyid'];
		// Define the base path storage
		$BasePath = "unistorage/" . $companyId;
		$webchat_path = '/var/www/html/'.$BasePath.'/webchat/';
		$base_path_Store = $BasePath.'/webchat/';
		$conversation_id = $this->input->post('conversation_id');
		$message = $this->input->post('bot_message');
		$type = 'text';
		$bot_chat_session = $this->input->post('chat_session');
		$agent_id = $_SESSION['userid'];
		$file_path = null; // Initialize file path as null

		// Check if a file is uploaded [vastvikta nishad][05-12-2024]
    	if (!empty($_FILES['file']['name'])) {
			$upload_path = $webchat_path;
			// echo "Step 2: File upload detected. Preparing to upload.\n";

			$config['upload_path'] = $upload_path;
			$config['allowed_types'] = 'jpg|jpeg|png|gif|pdf|doc|docx|txt'; // Allowed file types
			$config['file_name'] = $_FILES['file']['name'];

			$this->load->library('upload', $config);
			$file_path = '';
			if ($this->upload->do_upload('file')) {
				$file_data = $this->upload->data(); // Get file upload data
				$file_path = $base_path_Store . $file_data['file_name'];
				// echo "Step 3: File uploaded successfully. File path: $file_path\n";
			} else {
				$error_message = $this->upload->display_errors();
				// echo "Step 3: File upload failed. Error: $error_message\n";
				echo json_encode(['status' => 'error', 'msg' => $error_message]);
				die();
			}
		} else {
			// echo "Step 2: No file uploaded.\n";
		}

		$conversation = $this->db->get_where('bot_chat_session', ['conversation_id' => $conversation_id])->row();
		if (!empty($conversation)) {
			$insert_id = $this->io_model->save_wa_out_queue($conversation_id, $message, $type, $bot_chat_session, $agent_id, $file_path);
			echo json_encode(['status' => 'success', 'msg' => 'Message Sent', 'last_out_id' => $insert_id]);
			die();
		} else {
			echo json_encode(['status' => 'close', 'msg' => 'Chat Closed.']);
			die();
		}
	}

	public function agent_customer_conversations(){
		$chat_session = $_GET['chat_session'];
		$conversations = $this->wa_model->get_agent_customer_conversations($chat_session, $this->agent_id);
		$conversations_r = [];
		foreach ($conversations as $key => $value) {
			$value->createdDatetime = date('d M H:s a', strtotime($value->create_datetime));
			$conversations_r[] = $value;	
		}
		echo json_encode(array("status" => "success", 'bot' => $chat_session, "msg" => "conversation list", "list" => $conversations_r));die();
	}

	public function get_bot_by_session(){
		$chat_session_id = $this->input->post('chat_session_id');
		if(!empty($chat_session_id)){
			$conversations = $this->wa_model->bot_by_session($chat_session_id);
			$conversations_r = [];
			foreach ($conversations as $key => $value) {
				$value->createdDatetime = date('d M H:s a', strtotime($value->create_datetime));
				$conversations_r[] = $value;
			}
			echo json_encode(array("status" => "success", "msg" => "conversation list", "list" => $conversations_r));die();
		}
		echo json_encode(array("status" => "fail", "msg" => "List not found" ));die();
	}
	// close chat  
	public function close_chat(){
		$id = $this->input->post('id');
	// echo "ID: " . $id . "<br>"; // Debugging the ID

	$agent_id = $_SESSION['userid'];
	// echo "Agent ID: " . $agent_id . "<br>"; // Debugging the agent ID

	$chat_session_id = $this->input->post('chat_session_id');
	// echo "Chat Session ID: " . $chat_session_id . "<br>"; // Debugging the chat session ID

	// Fetch the conversation from the database
	$conversation = $this->db->get_where('bot_chat_session', array('chat_session' => $chat_session_id))->row();
	
    if(!empty($conversation->email)){
        include("../chat_function.php");

        $feedback_data = array();
        $feedback_data['createdBy'] = 'feedback';
		//changed the type 2 to 4  for feedback by chat option[vastvikta][04-12-2024]
        $feedback_data['Type'] = '4';
        $feedback_data['Call_id'] = $conversation->from;
        $feedback_data['Ticket_id'] = '';
        $feedback_data['Phone_Number'] = $conversation->from;
        $feedback_data['AgentID/Name'] = $agent_id; 
        $feedback_data['Extension_Number'] = '';
        $feedback_data['customer_email'] = $conversation->email;
        $feedback_data['customer_name'] = '';
        $feedback_link = create_feedbacklink($feedback_data);
        $data_array = array('chat_feedback_link' => $feedback_link.'&Type=4','email'=>$conversation->email,'name'=>$conversation->name,'caller_id'=>$conversation->from);
        $case_type = 'chat_close_feedback';
        $this->io_model->sms_email_template($case_type, $data_array, $assign_to = 'false');

        // for insert chathistory txt format in table
        $insertResult = insert_emailinformationout($conversation->email, $id);
    }
    
    if(!empty($id)) {
        // echo "Deleting chat session from database...<br>";  // Debugging statement
        $this->db->where('chat_session', $chat_session_id);
        $this->db->delete('bot_chat_session');
        $this->io_model->save_wa_out_queue($id,'Agent has closed this session, Thank you have a nice day', 'OUT', $chat_session_id, $agent_id);        
        echo json_encode(array("status" => "success", "msg" => "chat close")); die();
    } else {
        // echo json_encode(array("status" => "fail", "msg" => "failed to close")); die();
        echo json_encode(array("status" => "success", "msg" => "chat close")); die();
    }
}

}


// public function close_chat(){
// 	$id = $this->input->post('id');
// 	$agent_id = $_SESSION['userid'];
// 	$chat_session_id = $this->input->post('chat_session_id');
// 	$conversation = $this->db->get_where('bot_chat_session', array('chat_session' => $chat_session_id) )->row();
	
// 	if(!empty($conversation->email)){
// 		include("../CRM/chat_function.php");
// 		insert_emailinformationout($conversation->email,$id);
// 	}
// 	die;
// 	if(!empty($conversation->email)){
// 		include("../CRM/IMApp/function.php");
// 		$feedback_object = new ChatRooms;
// 		$feedback_data = array();
// 		$feedback_data['createdBy'] = 'feedback';
// 		$feedback_data['Type'] = '2';
// 		$feedback_data['Call_id'] = $conversation->from;
// 		$feedback_data['Ticket_id'] = '';
// 		$feedback_data['Phone_Number'] = $conversation->from;
// 		$feedback_data['AgentID/Name'] = $agent_id;
// 		$feedback_data['Extension_Number'] = '';
// 		$feedback_data['customer_email'] = $conversation->email;
// 		$feedback_data['customer_name'] = '';
// 		$feedback_link = $feedback_object->create_feedbacklink($feedback_data);
// 		$data_array = array('chat_feedback_link' => $feedback_link,'email'=>$conversation->email,'name'=>$conversation->name,'caller_id'=>$conversation->from);
// 		/*$case_type = 'chat_close_feedback';
// 		$this->io_model->sms_email_template($case_type, $data_array, $assign_to = 'false');*/
// 	}

	
// 	if(!empty($id))
// 	{
// 		$this->db->where('chat_session',$chat_session_id);
// 		$this->db->delete('bot_chat_session');
// 		$this->io_model->save_wa_out_queue($id,'Agent has closed this session, Thank you have a nice day', 'OUT',  $chat_session_id, $agent_id);		
// 		echo json_encode(array("status" => "success", "msg" => "chat close"));die();
	
// 	}else{
// 		// echo json_encode(array("status" => "fail", "msg" => "failed to close"));die();
// 		echo json_encode(array("status" => "success", "msg" => "chat close"));die();
// 	}
// }
?>
