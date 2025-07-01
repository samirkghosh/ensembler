<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_model extends CI_Model {

	function __construct(){
		parent::__construct();
	}

  public function is_insms_exist($id){
     
      $query = $this->db->get_where('sms_in_queue', array('id' => $id) );
      if($query->num_rows() > 0){
        $result = $query->row();
        if($result->status =='1')
          return 'sms id already updated';
        else
          return 'true';
      }
      else
        return 'sms id not exist';
     

  }

  public function getlast_insms($last_id=''){
    
    $this->db->limit(1);
    $this->db->order_by('id', 'ASC');
    $this->db->where('status', '0');
    $query = $this->db->get('sms_in_queue');
    if($query->num_rows() > 0 )
      $result = $query->row_array() ;
    else
      $result ='';

    return $result ;
  }

  public function update_insms($insms_id){
    if(!empty($insms_id)){
      $this->db->where('id',$insms_id);
      $this->db->update('sms_in_queue', array('status' => '1'));
      return $this->db->affected_rows();
    }
    return false;

  }

}

