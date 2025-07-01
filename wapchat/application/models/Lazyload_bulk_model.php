<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lazyload_bulk_model extends CI_Model {

  // constructor
	function __construct()
	{
		parent::__construct();
	}

  // Servre side testing
  function reports($limit, $start, $col, $dir, $filter_data)
  {
    $this->db->limit($limit,$start);
    // $this->db->order_by($col,$dir);
    $this->db->order_by('id','desc');

    // apply the filter data
    // check if the user is admin. Admin can not see the draft courses
    // if (strtolower($this->session->userdata('role')) == 'admin') {
    //     $this->db->where("status !=", 'draft');
    // }

    if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
      $this->db->where('created_at >=', $filter_data['from_date']);
    }

    if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
      $this->db->where('created_at <=', $filter_data['end_date']);
    }

    if ($filter_data['schedule'] != "all" && $filter_data['schedule'] !='') {
      $this->db->where('schedule_flag', $filter_data['schedule']);
    }
    // if ($filter_data['message_type'] != 'all' && $filter_data['message_type'] !='' ) {
    //   $this->db->where('message_type_flag', $filter_data['message_type']);
    // }

    if ($filter_data['list_wise'] != "all" && !empty($filter_data['list_wise'])) {
      $this->db->where('list_id', $filter_data['list_wise']);
    }

    if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
      $this->db->where('created_by', $filter_data['user_wise']);
    }
     
    if ($filter_data['status'] != "all" && $filter_data['status'] !='' ) {
      $this->db->where('status', $filter_data['status']);
    }

       $query = $this->db->get('queue_bulk_relation');
     /*if($filter_data['report_name'] == 'sms'){
      if($filter_data['report_in_out'] =='out'){

      }
      else{
       $query = $this->db->get('sms_in_queue');
        
      }
    }
    else if($filter_data['report_name'] == 'Whatsapp'){
      if($filter_data['report_in_out'] =='out'){
        $query = $this->db->get('whatsapp_out_queue');
      }
      else{
        $query = $this->db->get('whatsapp_in_queue');
      }
    }*/

    // echo $this->db->last_query();
    log_message('error', '####################################################');
    log_message('error', $this->db->last_query());

    if($query->num_rows() > 0)
    return $query->result();
    else
    return null;

  }

  function reports_search($limit, $start, $search, $col, $dir, $filter_data)
  {
    //$this->db->like('title', $search);
    $this->db->limit($limit, $start);
    $this->db->order_by($col, $dir);
    // apply the filter data
    // check if the user is admin. Admin can not see the draft courses
    /*if (strtolower($this->session->userdata('role')) == 'admin') {
        $this->db->where("status !=", 'draft');
    }*/
    if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
      $this->db->where('created_at >=', $filter_data['from_date']);
    }

    if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
      $this->db->where('created_at <=', $filter_data['end_date']);
    }

    if ($filter_data['schedule'] != "all" && $filter_data['schedule'] !='') {
      $this->db->where('schedule_flag', $filter_data['schedule']);
    }
    /*if ($filter_data['message_type'] != 'all' && $filter_data['message_type'] !='' ) {
      $this->db->where('message_type_flag', $filter_data['message_type']);
    }*/

    if ($filter_data['list_wise'] != "all" && !empty($filter_data['list_wise'])) {
      $this->db->where('list_id', $filter_data['list_wise']);
    }

    if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
      $this->db->where('created_by', $filter_data['user_wise']);
    }
     
    if ($filter_data['status'] != "all" && $filter_data['status'] !='' ) {
      $this->db->where('status', $filter_data['status']);
    }

       $query = $this->db->get('queue_bulk_relation');
     /*if($filter_data['report_name'] == 'sms'){
      if($filter_data['report_in_out'] =='out'){
       $query = $this->db->get('sms_bulk_uploads');
       log_message('error', 'SMS OUT 1');
      }
      else{
       log_message('error', 'SMS IN 1');
       $query = $this->db->get('sms_in_queue');
        
      }
    }
    else if($filter_data['report_name'] == 'Whatsapp'){
      if($filter_data['report_in_out'] =='out'){
        $query = $this->db->get('whatsapp_out_queue');
        log_message('error', 'WHATSAPP OUT 1');

      }
      else{
        $query = $this->db->get('whatsapp_in_queue');
        log_message('error', 'WHATSAPP IN 1');
      }
    }*/

      // echo $this->db->last_query();
    log_message('error', '#################################################### reports_search');
    log_message('error', $this->db->last_query());
    
    if($query->num_rows() > 0)
    return $query->result();
    else
    return null;
  }

  function course_search_count($search)
  {
    $this->db->where("delete_flag", '1');
    $query = $this
    ->db
    ->like('title', $search)
    ->get('course');

    return $query->num_rows();
  }

  function count_all_data($filter_data = array()) {
    // apply the filter data
    // check if the user is admin. Admin can not see the draft courses
    // if (strtolower($this->session->userdata('role')) == 'admin') {
    //     $this->db->where("status !=", 'draft');
    // }
    
     
    if ($filter_data['from_date'] != "all" && !empty($filter_data['from_date']) ) {
      $this->db->where('created_at >=', $filter_data['from_date']);
    }

    if ($filter_data['end_date'] != 'all' && !empty($filter_data['end_date'])) {
      $this->db->where('created_at <=', $filter_data['end_date']);
    }

    if ($filter_data['schedule'] != "all" && $filter_data['schedule'] !='') {
      $this->db->where('schedule_flag', $filter_data['schedule']);
    }
    // if ($filter_data['message_type'] != 'all' && $filter_data['message_type'] !='' ) {
    //   $this->db->where('message_type_flag', $filter_data['message_type']);
    // }

    if ($filter_data['list_wise'] != "all" && !empty($filter_data['list_wise'])) {
      $this->db->where('list_id', $filter_data['list_wise']);
    }

    if ($filter_data['user_wise'] != "all" && !empty($filter_data['user_wise'])) {
      $this->db->where('created_by', $filter_data['user_wise']);
    }
     
    if ($filter_data['status'] != "all" && $filter_data['status'] !='' ) {
      $this->db->where('status', $filter_data['status']);
    }
    


       $query = $this->db->get('queue_bulk_relation');
    /*if($filter_data['report_name'] == 'sms'){
      if($filter_data['report_in_out'] =='out'){
       $query = $this->db->get('sms_bulk_uploads');

      }
      else{
       $query = $this->db->get('sms_in_queue');
        
      }
    }
    else if($filter_data['report_name'] == 'Whatsapp'){
      if($filter_data['report_in_out'] =='out'){
        $query = $this->db->get('whatsapp_out_queue');
      }
      else{
        $query = $this->db->get('whatsapp_in_queue');
      }
    }*/
     

    


    
    log_message('error', 'COUNT QUERY');
    log_message('error', $this->db->last_query());
    return $query->num_rows();
  }
}
