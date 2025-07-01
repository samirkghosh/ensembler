<?php
class Template_model extends CI_model{

    public function get_templates(){
       
        if($this->session->userdata('admin')){
           // $this->db->where_in('id', ) ;    

        }
        $this->db->order_by('id', 'desc');
        $this->db->limit('20');
        $result = $this->db->get('user_templates');
        log_message('error', $this->db->last_query());
        return $result->result();
    }


}

?>