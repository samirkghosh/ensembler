<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lazyload_customer_model extends CI_Model {

  // constructor
	function __construct()
	{
		parent::__construct();
	}

  // Servre side testing
  function reports_sms($limit, $start, $col, $dir, $filter_data)
  {
   // $this->db->limit($limit,$start);
    // $this->db->order_by($col,$dir);
    // $this->db->order_by('id','desc');

    
    // this Will return for in and out queue Both
    $sql ="SELECT id, send_to, send_from, message, create_date, channel_type FROM sms_out_queue WHERE send_to LIKE '%".$filter_data['mobile']."%' UNION SELECT id, send_to, send_from, message, create_date, channel_type FROM sms_in_queue WHERE send_from LIKE '%".$filter_data['mobile']."%' UNION SELECT  id , `to` as `send_to`, `from` as `send_from`, `content_text` as `message`,  `create_date`, `type` as `channel_type` FROM `wa_in_out` WHERE `to` LIKE '%".$filter_data['mobile']."%' or `from` LIKE '%".$filter_data['mobile']."%' ORDER BY create_date desc LIMIT $start ,$limit " ;
    
    $query = $this->db->query($sql);

  

    log_message('error', '#################################################### CHACK CHANNEl');
     log_message('error', $this->db->last_query());
    // log_message('error', json_encode($new_result));

    if($query->num_rows() > 0)
    return $query->result();
    else
    return null;

  }

  function reports_whatspp($limit, $start, $col, $dir, $filter_data)
  {
    $this->db->limit($limit,$start);
    // $this->db->order_by($col,$dir);
    $this->db->order_by('id','desc');

   
     // this Will return for in and out queue Both
    //$sql ="SELECT id, send_to, send_from, message, create_date, channel_type FROM whatsapp_out_queue WHERE send_to ='".$filter_data['mobile']."' UNION SELECT id, send_to, send_from, message, create_date, channel_type FROM whatsapp_in_queue WHERE send_from ='".$filter_data['mobile']."' ORDER BY create_date DESC" ;

    $sql ="SELECT  id , `to` as `send_to`, `from` as `send_from`, `content_text` as `message`, `createdDatetime` as `create_date`, `type` as `channel_type` FROM `wa_in_out` WHERE `to` LIKE '%".$filter_data['mobile']."%' or `from` LIKE '%".$filter_data['mobile']."%' ORDER BY id desc LIMIT $start ,$limit ";

    $query = $this->db->query($sql);


    

    // echo $this->db->last_query();
    // log_message('error', '####################################################');
    // log_message('error', $this->db->last_query());

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

  function count_all_data_sms($filter_data = array()) {
   

     // this Will return for in and out queue Both
    $sql ="SELECT id, send_to, send_from, message, create_date, channel_type FROM sms_out_queue WHERE send_to ='".$filter_data['mobile']."' UNION SELECT id, send_to, send_from, message, create_date, channel_type FROM sms_in_queue WHERE send_from ='".$filter_data['mobile']."' UNION SELECT  id , `to` as `send_to`, `from` as `send_from`, `content_text` as `message`,  `create_date`, `type` as `channel_type` FROM `wa_in_out` WHERE `to` LIKE '%".$filter_data['mobile']."%' or `from` LIKE '%".$filter_data['mobile']."%' ORDER BY id desc" ;
    $query = $this->db->query($sql);

      
    
    log_message('error', 'COUNT QUERY SMS ');
    log_message('error', $this->db->last_query());
    log_message('error', $query->num_rows());
    return $query->num_rows();
  }


  function count_all_data_whatspp($filter_data = array()) {
    

     // this Will return for in and out queue Both
    //$sql ="SELECT id, send_to, send_from, message, create_date, channel_type FROM whatsapp_out_queue WHERE send_to ='".$filter_data['mobile']."' UNION SELECT id, send_to, send_from, message, create_date, channel_type FROM whatsapp_in_queue WHERE send_from ='".$filter_data['mobile']."' " ;

    $sql ="SELECT id , `to` as `send_to`, `from` as `send_from`, `content_text` as `message`, `createdDatetime` as `create_date`, `type` as `channel_type` FROM `wa_in_out` WHERE `to` LIKE '%".$filter_data['mobile']."%' or `from` LIKE '%".$filter_data['mobile']."%'";
    $query = $this->db->query($sql);

       // $query = $this->db->get('whatsapp_out_queue');
       
    // log_message('error', 'COUNT QUERY WHAT APP ');
    // log_message('error', $this->db->last_query());
    // log_message('error', $query->num_rows());
    return $query->num_rows();
  }
}
