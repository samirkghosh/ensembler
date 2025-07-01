<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lazyloaddata_model extends CI_Model {

  // constructor
	function __construct()
	{
		parent::__construct();

	}

  // Servre side testing
  function reports($limit, $start, $col, $dir, $filter_data){

    $this->db->limit($limit,$start);
    // $this->db->order_by($col,$dir);
    $this->db->order_by('id','desc');

    // apply the filter data
    // check if the user is admin. Admin can not see the draft courses
    // if (strtolower($this->session->userdata('role')) == 'admin') {
    //     $this->db->where("status !=", 'draft');
    // }
    //if(!isset($filter_data['last_hundreds']) && $filter_data['last_hundreds'] !='yes'){
      if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
        $this->db->where('create_date >=', yyyymmdd_date($filter_data['from_date']));
      }

      if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
        $this->db->where('create_date <=', yyyymmdd_date($filter_data['end_date']));
      }
    //}


    if ($filter_data['schedule'] != "all" && $filter_data['schedule'] !='') {
      $this->db->where('schedule_flag', $filter_data['schedule']);
    }
    if ($filter_data['message_type'] != 'all' && $filter_data['message_type'] !='' ) {
      $this->db->where('message_type_flag', $filter_data['message_type']);
    }

    if ($filter_data['list_wise'] != "all" && !empty($filter_data['list_wise'])) {
      $this->db->where('bulk_session_id', $filter_data['list_wise']);
    }
    if(strtolower($this->session->userdata('admin')['role']) !='admin'):
      if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
        $this->db->where('created_by', $filter_data['user_wise']);
      }
    endif;
     
    if ($filter_data['status'] != "all" && $filter_data['status'] !='' ) {
      $this->db->where('status', $filter_data['status']);
    }

    if($filter_data['report_name'] == 'sms'){
      if($filter_data['report_in_out'] =='out'){
       $query = $this->db->get('sms_out_queue');

      }
      else{
       $query = $this->db->get('sms_in_queue');
        
      }
    }
    else if($filter_data['report_name'] == 'whatsapp'){
      if($filter_data['report_in_out'] =='out'){
        $query = $this->db->get('whatsapp_out_queue');
      }
      else{
        $query = $this->db->get('whatsapp_in_queue');
      }
    }

    // echo $this->db->last_query();
    log_message('error', '####################################################');
    log_message('error', $this->db->last_query());

    if($query->num_rows() > 0)
    return $query->result();
    else
    return null;

  }

  function count_all_data($filter_data = array()) {
    // apply the filter data
    // check if the user is admin. Admin can not see the draft courses
    // if (strtolower($this->session->userdata('role')) == 'admin') {
    //     $this->db->where("status !=", 'draft');
    // }
    
    //if(!isset($filter_data['last_hundreds']) && $filter_data['last_hundreds'] !='yes'){ 
      if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
        $this->db->where('create_date >=', yyyymmdd_date($filter_data['from_date']));
      }

      if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
        $this->db->where('create_date <=', yyyymmdd_date($filter_data['end_date']));
      }
    //}

    if ($filter_data['schedule'] != "all" && $filter_data['schedule'] !='') {
      $this->db->where('schedule_flag', $filter_data['schedule']);
    }
    if ($filter_data['message_type'] != 'all' && $filter_data['message_type'] !='' ) {
      $this->db->where('message_type_flag', $filter_data['message_type']);
    }

    if ($filter_data['list_wise'] != "all" && !empty($filter_data['list_wise'])) {
      $this->db->where('bulk_session_id', $filter_data['list_wise']);
    }
    if(strtolower($this->session->userdata('admin')['role']) !='admin'):
      if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
        $this->db->where('created_by', $filter_data['user_wise']);
      }
    endif;  
     
    if ($filter_data['status'] != "all" && $filter_data['status'] !='' ) {
      $this->db->where('status', $filter_data['status']);
    }
    
     log_message('error', 'COUNT QUERY11111111111111111111111');
     log_message('error', $filter_data['report_name']);
     log_message('error', json_encode($filter_data));


    if($filter_data['report_name'] == 'sms'){
      if($filter_data['report_in_out'] =='out'){
       $query = $this->db->get('sms_out_queue');

      }
      else{
       $query = $this->db->get('sms_in_queue');
        
      }
    }
    else if($filter_data['report_name'] == 'whatsapp'){
      if($filter_data['report_in_out'] =='out'){
        $query = $this->db->get('whatsapp_out_queue');
      }
      else{
        $query = $this->db->get('whatsapp_in_queue');
      }
    }
      
    log_message('error', 'COUNT QUERY');
    log_message('error', $this->db->last_query());
    return $query->num_rows();
  }
  #######################################################
  #####   For Showing Data Whatsapp 
  #######################################################

  function reports_wa($limit, $start, $col, $dir, $filter_data){

    $this->db->limit($limit,$start);
    // $this->db->order_by($col,$dir);
    $this->db->order_by('id','desc');

    if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
      $this->db->where('session_start_time >=', yyyymmdd_date($filter_data['from_date']));
    }

    if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
      $this->db->where('session_start_time <=', yyyymmdd_date($filter_data['end_date']));
    }

    if(strtolower($this->session->userdata('admin')['role']) !='admin'):
      if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
        $this->db->where('user_id', $filter_data['user_wise']);
      }
    endif;
  
    if($filter_data['report_name'] == 'sms'){
      if($filter_data['report_in_out'] =='out'){
       $query = $this->db->get('overall_bot_chat_session');

      }
    }

    log_message('error', '############### bot session datewise #####################');
    log_message('error', $this->db->last_query());

    if($query->num_rows() > 0)
    return $query->result();
    else
    return null;

  }

  function count_all_data_wa($filter_data = array()) {
   
    if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
      $this->db->where('session_start_time >=', yyyymmdd_date($filter_data['from_date']));
    }

    if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
      $this->db->where('session_start_time <=', yyyymmdd_date($filter_data['end_date']));
    }
    if(strtolower($this->session->userdata('admin')['role']) !='admin'):
      if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
        $this->db->where('user_id', $filter_data['user_wise']);
      }
    endif;


    if($filter_data['report_name'] == 'sms'){
      if($filter_data['report_in_out'] =='out'){
       $query = $this->db->get('overall_bot_chat_session');

      }
   
    }
    
      
    log_message('error', 'COUNT BOT QUERY');
    log_message('error', $this->db->last_query());
    return $query->num_rows();
  }

  #######################################################
  #####   For Showing Data in Inbox use below function 
  #######################################################

  // Servre side testing
  function reports_inbox($limit, $start, $col, $dir, $filter_data){

    $this->db->limit($limit,$start);
    // $this->db->order_by($col,$dir);
    $this->db->order_by('id','desc');

    // apply the filter data
    // check if the user is admin. Admin can not see the draft courses
    // if (strtolower($this->session->userdata('role')) == 'admin') {
    //     $this->db->where("status !=", 'draft');
    // }
    
    if(!isset($filter_data['last_hundreds']) && $filter_data['last_hundreds'] !='yes'){ 
      if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
        $this->db->where('create_date >=', yyyymmdd_date($filter_data['from_date']));
      }

      if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
        $this->db->where('create_date <=', yyyymmdd_date($filter_data['end_date']));
      }
    }

    if ($filter_data['schedule'] != "all" && $filter_data['schedule'] !='') {
      $this->db->where('schedule_flag', $filter_data['schedule']);
    }
    if ($filter_data['message_type'] != 'all' && $filter_data['message_type'] !='' ) {
      $this->db->where('message_type_flag', $filter_data['message_type']);
    }

    if ($filter_data['list_wise'] != "all" && !empty($filter_data['list_wise'])) {
      $this->db->where('bulk_session_id', $filter_data['list_wise']);
    }

    if(strtolower($this->session->userdata('admin')['role']) =='admin'){
      if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise']) && $filter_data['user_wise'] !='1'):
        $this->db->where('created_by', $filter_data['user_wise']);
      endif;
    }
    else if(strtolower($this->session->userdata('admin')['role_id']) =='2'){
      // $this->db->where_in('created_by', [$this->session->userdata('admin')['id'],1]);
    }  
    else{
      if($filter_data['report_in_out'] =='out'){
        // $this->db->where('created_by', $this->session->userdata('admin')['id']);
      }
      if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])):
        $this->db->where('created_by', $filter_data['user_wise']);
      endif;
    }

      
    if($filter_data['name_number'] != "all" && !empty($filter_data['name_number'] && $filter_data['report_in_out'] =='in')) {
      $this->db->where('send_from', $filter_data['name_number']);
    }

    if ($filter_data['status'] != "all" && $filter_data['status'] !='' ) {
      if($filter_data['report_in_out'] =='out'){
        $this->db->where('status', $filter_data['status']);
      }
         
      if($filter_data['report_in_out'] =='in'){  
        $this->db->where_in('message_flag', $filter_data['status']);
      }
        
    }
    else{
      if($filter_data['report_in_out'] =='out'){
        $this->db->where_in('status', [1,2,3]);
      }
    }
        


    if($filter_data['report_name'] == 'sms'){
      if($filter_data['report_in_out'] =='out'){
       $query = $this->db->get('sms_out_queue');

      }
      else{
       
       $query = $this->db->get('sms_in_queue');
        
      }
    }
    else if($filter_data['report_name'] == 'whatsapp'){
      if($filter_data['report_in_out'] =='out'){
        $query = $this->db->get('whatsapp_out_queue');
      }
      else{
        $query = $this->db->get('whatsapp_in_queue');
      }
    }

    // echo $this->db->last_query();
    log_message('error', '#################################################### INBOX');
    log_message('error', $this->db->last_query());

    if($query->num_rows() > 0)
    return $query->result();
    else
    return null;

  }

  // Show Inbox, sent and outbox data 
  function count_all_data_inbox($filter_data = array()) {
    // apply the filter data
    // check if the user is admin. Admin can not see the draft courses
    // if (strtolower($this->session->userdata('role')) == 'admin') {
    //     $this->db->where("status !=", 'draft');
    // }
    
     
    if(!isset($filter_data['last_hundreds']) && $filter_data['last_hundreds'] !='yes'){ 
      if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
        $this->db->where('create_date >=', yyyymmdd_date($filter_data['from_date']));
      }

      if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
        $this->db->where('create_date <=', yyyymmdd_date($filter_data['end_date']));
      }
    }

    if ($filter_data['schedule'] != "all" && $filter_data['schedule'] !='') {
      $this->db->where('schedule_flag', $filter_data['schedule']);
    }
    if ($filter_data['message_type'] != 'all' && $filter_data['message_type'] !='' ) {
      $this->db->where('message_type_flag', $filter_data['message_type']);
    }

    if ($filter_data['list_wise'] != "all" && !empty($filter_data['list_wise'])) {
      $this->db->where('bulk_session_id', $filter_data['list_wise']);
    }



    if(strtolower($this->session->userdata('admin')['role']) =='admin'){
      if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise']) && $filter_data['user_wise'] !='1') {
        $this->db->where('created_by', $filter_data['user_wise']);
      }
    }
    else{
      if($filter_data['report_in_out'] =='out'){
        // $this->db->where('created_by', $this->session->userdata('admin')['id']);
      }
    }  
     
    if($filter_data['name_number'] != "all" && !empty($filter_data['name_number'] && $filter_data['report_in_out'] =='in')) {
      $this->db->where('send_from', $filter_data['name_number']);
    }

    if ($filter_data['status'] != "all" && $filter_data['status'] !='' ) {
      if($filter_data['report_in_out'] =='out'){
        $this->db->where('status', $filter_data['status']);
      }
      if($filter_data['report_in_out'] =='in'){
        $this->db->where_in('message_flag', $filter_data['status']);
      }
    }
    else{
      if($filter_data['report_in_out'] =='out'){
        $this->db->where_in('status', [1,2,3]);
      }
    }
        

    
     log_message('error', 'COUNT QUERY11111111111111111111111 INBOX');
     log_message('error', $filter_data['report_name']);
     log_message('error', json_encode($filter_data));


    if($filter_data['report_name'] == 'sms'){
      if($filter_data['report_in_out'] =='out'){
       $query = $this->db->get('sms_out_queue');

      }
      else{
       $query = $this->db->get('sms_in_queue');
        
      }
    }
    else if($filter_data['report_name'] == 'whatsapp'){
      if($filter_data['report_in_out'] =='out'){
        $query = $this->db->get('whatsapp_out_queue');
      }
      else{
        $query = $this->db->get('whatsapp_in_queue');
      }
    }
     

    


    
    log_message('error', 'COUNT QUERY');
    log_message('error', $this->db->last_query());
    return $query->num_rows();
  }


  #######################################################
  #####   For Showing Data in Outbox use below function 
  #######################################################

  // Servre side testing
  function reports_outbox($limit, $start, $col, $dir, $filter_data){

    $this->db->limit($limit,$start);
    // $this->db->order_by($col,$dir);
    $this->db->order_by('id','desc');

    // apply the filter data
    // check if the user is admin. Admin can not see the draft courses
    // if (strtolower($this->session->userdata('role')) == 'admin') {
    //     $this->db->where("status !=", 'draft');
    // }

    if(!isset($filter_data['last_hundreds']) && $filter_data['last_hundreds'] !='yes'){ 
      if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
        $this->db->where('create_date >=', yyyymmdd_date($filter_data['from_date']));
      }

      if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
        $this->db->where('create_date <=', yyyymmdd_date($filter_data['end_date']));
      }
    }

    if ($filter_data['schedule'] != "all" && $filter_data['schedule'] !='') {
      $this->db->where('schedule_flag', $filter_data['schedule']);
    }
    if ($filter_data['message_type'] != 'all' && $filter_data['message_type'] !='' ) {
      $this->db->where('message_type_flag', $filter_data['message_type']);
    }

    if ($filter_data['list_wise'] != "all" && !empty($filter_data['list_wise'])) {
      $this->db->where('bulk_session_id', $filter_data['list_wise']);
    }

    if(strtolower($this->session->userdata('admin')['role']) == 'admin'){
      if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
        $this->db->where('created_by', $filter_data['user_wise']);
      }
    }
    else if(strtolower($this->session->userdata('admin')['role_id']) =='2'){
      $this->db->where_in('created_by', [$this->session->userdata('admin')['id'],1]);
    }
    else{
      
      $this->db->where('created_by', $this->session->userdata('admin')['id']);
    }
    
    
    
        
      

      
    $this->db->where_in('status', [0]);
     
    
    if($filter_data['report_name'] == 'sms'){
       $query = $this->db->get('sms_out_queue');
      /*if($filter_data['report_in_out'] =='out'){

      }
      else{
       $query = $this->db->get('sms_in_queue');
        
      }*/
    }
    else if($filter_data['report_name'] == 'whatsapp'){
        $query = $this->db->get('whatsapp_out_queue');
      /*if($filter_data['report_in_out'] =='out'){
      }
      else{
        $query = $this->db->get('whatsapp_in_queue');
      }*/
    }

    // echo $this->db->last_query();
    log_message('error', '#################################################### OUTBOX');
    log_message('error', $this->db->last_query());

    if($query->num_rows() > 0)
    return $query->result();
    else
    return null;

  }

  function count_all_data_outbox($filter_data = array()) {
    // apply the filter data
    // check if the user is admin. Admin can not see the draft courses
    // if (strtolower($this->session->userdata('role')) == 'admin') {
    //     $this->db->where("status !=", 'draft');
    // }
    
     
    if(!isset($filter_data['last_hundreds']) && $filter_data['last_hundreds'] !='yes'){ 
      if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
        $this->db->where('create_date >=', yyyymmdd_date($filter_data['from_date']));
      }

      if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
        $this->db->where('create_date <=', yyyymmdd_date($filter_data['end_date']));
      }
    }

    if ($filter_data['schedule'] != "all" && $filter_data['schedule'] !='') {
      $this->db->where('schedule_flag', $filter_data['schedule']);
    }
    if ($filter_data['message_type'] != 'all' && $filter_data['message_type'] !='' ) {
      $this->db->where('message_type_flag', $filter_data['message_type']);
    }

    if ($filter_data['list_wise'] != "all" && !empty($filter_data['list_wise'])) {
      $this->db->where('bulk_session_id', $filter_data['list_wise']);
    }

    if(strtolower($this->session->userdata('admin')['role']) == 'admin'){
      if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
        $this->db->where('created_by', $filter_data['user_wise']);
      }
    }
    else if(strtolower($this->session->userdata('admin')['role_id']) == '2'){
      // if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
        $this->db->where_in('created_by', [$this->session->userdata('admin')['id'],1]);
      // }
    }
    else{
      $this->db->where('created_by', $this->session->userdata('admin')['id']);
    }
       
     
    $this->db->where_in('status', [0]);
     
    
    
     log_message('error', 'COUNT QUERY11111111111111111111111 OUTBOX');
     log_message('error', $filter_data['user_wise']);
     log_message('error', json_encode($filter_data));


    if($filter_data['report_name'] == 'sms'){
       $query = $this->db->get('sms_out_queue');
      
    }
    else if($filter_data['report_name'] == 'whatsapp'){
      $query = $this->db->get('whatsapp_out_queue');
     
    }
        
    log_message('error', 'COUNT QUERY');
    log_message('error', $this->db->last_query());
    return $query->num_rows();
  }


  #######################################################
  #####   For Showing bad records 
  #######################################################

  // Servre side testing
  function reports_bad_records($limit, $start, $col, $dir, $filter_data){

    $this->db->limit($limit,$start);
    // $this->db->order_by($col,$dir);
    $this->db->order_by('id','desc');


     if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
      $this->db->where('created_date >=', yyyymmdd_date($filter_data['from_date']));
    }

    if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
      $this->db->where('created_date <=', yyyymmdd_date($filter_data['end_date']));
    }
    
   

    if(strtolower($this->session->userdata('admin')['role']) !='admin'):
      $this->db->where('created_by', $this->session->userdata('admin')['id']);
    else:
       if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
        $this->db->where('created_by', $filter_data['user_wise']);
      }
    endif;
    
    // $this->db->where_in('status', [0]);
    $query = $this->db->get('bad_contact');
    // echo $this->db->last_query();
    log_message('error', '#################################################### reports_bad_records');
    log_message('error', $this->db->last_query());

    if($query->num_rows() > 0)
    return $query->result();
    else
    return null;

  }
     
  function count_all_data_bad_records($filter_data = array()) {

    if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
      $this->db->where('created_date >=', yyyymmdd_date($filter_data['from_date']));
    }

    if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
      $this->db->where('created_date <=', yyyymmdd_date($filter_data['end_date']));
    }

    
    if(strtolower($this->session->userdata('admin')['role']) !='admin'){
      $this->db->where('created_by', $this->session->userdata('admin')['id']);
    }
    else{
       if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
        $this->db->where('created_by', $filter_data['user_wise']);
      }
    }
       
    $query = $this->db->get('bad_contact');
    log_message('error', 'COUNT QUERY11111111111111111111111 reports_bad_records');
    log_message('error', $this->db->last_query());
    return $query->num_rows();
  }
     

}
