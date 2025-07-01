<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class IO_model extends CI_Model {

	function __construct(){
		parent::__construct();
	}



	public function read_output_on_cutomer_input($input, $no_input = '', $menu_id = 0 ){ 
		
		if($no_input != ''){
			$next = $this->db->get_where('bot_flow', array('i_id' => $no_input))->row();
			log_message('error', 'ERROR 1' );
			log_message('error', $this->db->last_query() );
			return $next; 
		}
		else{
			if($menu_id > 0){
						// $this->db->where_in('i_id' , [$menu_id]);
				$next = $this->db->get_where('bot_flow', array('v_items' => $input, 'i_id' => $menu_id))->row();
			}
			else{
				$next = $this->db->get_where('bot_flow', array('v_items' => $input))->row();
			}
		}
		log_message('error', 'ERROR 2' );
		log_message('error', $this->db->last_query() );



		if($next->v_next > 0 && $next->e_type != 'taction'){
			$r = $this->db->get_where('bot_flow', array('i_id' => $next->v_next ))->row();
			log_message('error', 'ERROR 3' );
			log_message('error', $this->db->last_query() );
			return $r ;
		}
		else if($next->e_type = 'taction'){
			$r = $this->db->get_where('bot_flow', array('i_id' => $next->v_next ))->row();
			log_message('error', 'ERROR 4' );
			log_message('error', $this->db->last_query() );
			return $r ;
		}

		return false ;
	}


	public function store_last_selected_option($data, $conversation_id)
	{
		$this->db->where('conversation_id', $conversation_id);
		$this->db->update('bot_chat_session', $data);
		log_message('error', 'store_last_selected_option');
		log_message('error', $this->db->last_query());
	}




	###########################################################################################################
	##   Manage In Queue And Out Queue
	###########################################################################################################


	//added the code to  store the attachment in the file [vastvikta nishad][07-12-2024]m
	public function save_wa_out_queue($conversation_id, $message, $type = 'text', $bot_chat_session = '', $agent_id ='' ,$file_path =''){
		if(empty($file_path)){
			$file_path = '';
		}
		$insert = array(
            // 'message_id' =>  $v->id, 
            'customer_id' =>  $conversation_id,           
            'message' => $message,                             
            'agent_id' => $agent_id,
            'direction' => 'OUT',
            'create_datetime' => date('Y-m-d H:i:s'),
            'chat_session_id' => $bot_chat_session,
            'attachment' => $file_path,
          );

           $this->db->insert('in_out_data', $insert);    // this will store message in whatsapp in queue.           
           $insert_id = $this->db->insert_id();
            return $insert_id;
          
	}
	public function sms_email_template($case_type, $data_arr=[], $assign_to = 'false'){
		global $link,$db;
		include("../config/web_mysqlconnect.php");

		$sql_sms="select * from $db.tbl_smsformat where smsstatus=1 AND smstemplatename='$case_type'"; 
		$qsms = mysqli_query($link,$sql_sms)or die(mysqli_error($link));
		$rowSms = mysqli_fetch_array($qsms);
		$header = $rowSms['smsheader'];
		$footer = $rowSms['smsfooter'];
		$body = $rowSms['smsbody'];
 		$feedback_link = $data_arr['chat_feedback_link'];
 		$fname = $data_arr['name'];
 		$caller_id = $data_arr['caller_id'];

 		$header = str_replace("%customer%", $fname, $header);
 		$body = str_replace("%chat_feedback_link%", $feedback_link, $body);
		$content = $header.','.$body.$footer;

 		$sql_sms_feed="insert into $db.tbl_smsmessages (v_category,v_mobileNo,v_smsString,V_Type,V_AccountName,V_CreatedBY,d_timeStamp, i_status) values ('$caller_id','$caller_id','$content','Sms','$fname','',NOW(), '1')";
		$result_sms= mysqli_query($link,$sql_sms_feed) or die("Error In Query24 ".mysqli_error($link));
		/*----end sms code---------*/

		$sql_email="SELECT * FROM $db.tbl_mailformats WHERE MailStatus=1 AND MailTemplateName='$case_type'";
		$qsmsemail = mysqli_query($link,$sql_email)or die(mysqli_error($link));
		$rowemail = mysqli_fetch_array($qsmsemail);

		$subject =$rowemail['MailSubject'];
		$greeting =$rowemail['MailGreeting'];
		$body=$rowemail['MailBody'];
		$signature=$rowemail['MailSignature'];
		$email = $data_arr['email'];
		$chat_feedback_link = $data_arr['chat_feedback_link'];
		$body= str_replace("%chat_feedback_link%", $chat_feedback_link, $body);
		$content = $greeting.$body.$signature;
		$from="rajdubey.alliance@gmail.com";

		$sql_email="insert into $db.web_email_information_out(v_toemail,v_fromemail,v_subject, v_body,email_type,module) values ('$email', '$from', '$subject', '$content', 'OUT', 'New Case Call')";
		mysqli_query($link,$sql_email) or die("Error In Query23 ".mysqli_error($link));
	}















}