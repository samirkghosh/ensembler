<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WA_model extends CI_Model {

	function __construct(){
		parent::__construct();
	}

  public function isCustomer_exist($mobile){
    $query = $this->db->get_where('wa_conversations', array('contact_msisdn' => $mobile));
    if($query->num_rows() > 0)
      return true;
    else
      return false ;

  }

  public function isConversation_exist($con_id){
    $query = $this->db->get_where('wa_conversations', array('conversation_id' => $con_id));
    if($query->num_rows() > 0)
      return true;
    else
      return false ;

  }

  public function isMessageExist($message_id){
    $query = $this->db->get_where('wa_in_out', array('message_id' => $message_id));
    if($query->num_rows() > 0)
      return true;
    else
      return false ;
  }

  public function isBotSessionExist($mobile){
    $query = $this->db->get_where('bot_chat_session', array('from' => $mobile));
    if($query->num_rows() > 0)
      return true;
    else
      return false ;
  }

  public function last_chat_message_userwise($mobile){
    $this->db->limit(1);
    $this->db->order_by('id', 'DESC');
    return $this->db->get_where('wa_in_out', array('direction' => 'received', 'from' => $mobile))->row();
  }

  public function conversationby_id($con_id){
    $query = $this->db->get_where('wa_conversations', array('conversation_id' => $con_id));
    if($query->num_rows() > 0)
      return $query->result();
    else
      return false ;

  }


  public function botSessionby_id($con_id){
    $query = $this->db->get_where('bot_chat_session', array('conversation_id' => $con_id));
    if($query->num_rows() > 0)
      return $query->row();
    else
      return false ;
  }

  public function get_previous_bot_by_msisdn($msisdn){
    $this->db->order_by('id', 'desc');
    $result = $this->db->get_where('overall_bot_chat_session', array('from' => $msisdn))->result();
    return $result ;
  }

  public function get_current_bot_chat($bot_session_id){
    if(!empty($bot_session_id)){
      $this->db->order_by('id', 'asc');
      $result = $this->db->get_where('in_out_data', array('chat_session_id' => $bot_session_id))->result();
      return $result ;
    }
    else{
     return array(); 
    }

  }

  public function get_agent_customer_conversations($bot_session_id, $agent_id){
        $this->db->order_by('id', 'asc');
    $result = $this->db->get_where('in_out_data', array('chat_session_id' => $bot_session_id, 'agent_id' => $agent_id))->result();
    return $result ; 
  }

  public function bot_by_session($bot_session_id){
      $this->db->order_by('id', 'asc');
      $result = $this->db->get_where('in_out_data', array('chat_session_id' => $bot_session_id ))->result();
      // $result = $this->db->get_where('wa_in_out', array('bot_session_id' => $bot_session_id ))->result();
      return $result ; 
  }


  #####################################################################################
  #####  Validation  above the bar
  #####################################################################################

  // update conversation
  public function update_conversations($convers){
      
      foreach ($convers as $key => $con) {
        
        if(!$this->isCustomer_exist($con->contact->msisdn)){
            $insert_data = array(
              'conversation_id' => $con->id,
              'href'            => $con->href,            
              'contact_id'      => $con->contact->id,
              'contact_href'    => $con->contact->href,
              'contact_msisdn'  => $con->contact->msisdn,  
              'channel_id'      => $con->channels[0]->id,
              'channel_name'    => $con->channels[0]->name,
              'channel_platform' => $con->channels[0]->platformId,
              'status'          => $con->status,
              'messages_href'   => $con->messages->href,
              'message_count'   => $con->messages->totalCount,
              'lastUsedChannelId' => $con->lastUsedChannelId,
              'lastReceivedDatetime' => $con->lastReceivedDatetime,
              'createdDatetime'   => $con->createdDatetime,
              'updatedDatetime'   => $con->updatedDatetime,
            );
            $this->db->insert('wa_conversations', $insert_data);
        }

      } /*Close foreach loop*/

      // return $insert_data ;
  }

  public function save_messages($v , $bot_session_id ='' , $agent_id=''){
    
    if(!empty($v)){
      // foreach ($messages as $key => $v) {
        log_message('error', "CHECK KEY ".$v->id);
        if(!$this->isMessageExist($v->id)){
          $insert = array(
            'message_id' =>  $v->id, 
            'conversation_id' =>  $v->conversationId, 
            'channelId' => $v->channelId,
            'to' => $v->to,
            'from' => $v->from,
            'type' => $v->type,
            'content_text' => $v->content->text,
            'direction' => $v->direction,
            'status' => $v->status,
            'createdDatetime' => $v->createdDatetime,
            'updatedDatetime' => $v->updatedDatetime,
            'content_hsm' => $v->type == 'hsm' ? 'yes' : 'no',
            'namespace' => $v->type == 'hsm' ? $v->content->hsm->namespace : '',
            'templateName' => $v->type == 'hsm' ? $v->content->hsm->templateName : '',
            'language_code' => $v->type == 'hsm' ? $v->content->hsm->language->code : '',
            'params' => $v->type == 'hsm' ? json_encode($v->content->hsm->params) : '',
            'bot_session_id' => $bot_session_id,
            'user_id' => $agent_id,
            'platform' => $v->platform,
          );
          if($v->direction =='received'){
            $this->db->insert('wa_in_queue', $insert);    // this will store message in whatsapp in queue.
          }

          $this->db->insert('wa_in_out', $insert);      // this will store whole comunication in and out.
          log_message('error', "LAST INSRET ");
          log_message('error', $this->db->last_query());


        // }
           
      }
    }
  }
         
  public function update_message_status($res){
    log_message('error', 'update_message_status');
    $this->db->where('message_id', $res->id);
    $this->db->update('wa_in_out', array('status' => $res->status));
    log_message('error', $this->db->last_query());
  }





   ##########################################################
    ### API controlls fron notification server to bipa 
    ###########################################################


    public function api_to_get_application_status($conversation_id, $application_id, $request_type){
        
      // get chat session detail by  id 
      //$result = $this->conversationby_id($conversation_id);
      //$result->last_input_value;

      if($request_type =='application_status'){

      }
      else if($request_type =='application_status'){
        
      }

      return array('status' => 'fail' , 'messages' => 'Your Application Status Approved.');

    }

}

