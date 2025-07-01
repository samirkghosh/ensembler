<?php
class Report_model extends CI_model{

    public function reports(){
        $this->db->order_by('id', 'desc');
        $this->db->limit('20');
        return $this->db->get('sms_out_queue')->result();
    }

    public function get_status_of_list($list_id){
        $sql = "SELECT COUNT(id) AS ct, status, status_response FROM `sms_out_queue` WHERE queue_session ='$list_id' GROUP BY status" ;
        $result = $this->db->query($sql);
        $res = $result->result();
        return $res;
    }


    public function get_list_details($list_id){
        $query = $this->db->get_where('sms_bulk_uploads',array('id' => $list_id));
        $res = $query->row();
        return $res;
    }

    public function getConversation($chat_session)
    {
        $this->db->order_by('create_date', 'desc');
        $this->db->where('bot_session_id', $chat_session);
        return $this->db->get('wa_in_out')->result();

    }
      


}

?>