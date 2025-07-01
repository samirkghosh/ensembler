<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact_model extends CI_Model {

  // constructor
	function __construct()
	{
		parent::__construct();
	}

  // Servre side testing
  function reports($limit, $start, $col, $dir, $filter_data)
  {
    $this->db->limit($limit,$start);
    $this->db->order_by('id','desc');
    // $this->db->order_by($col,$dir);
    // $this->db->order_by('id','desc');
    $query = $this->db->get('contact');

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
    $query = $this->db->get('contact');
    return $query->num_rows();
  }


  
}
