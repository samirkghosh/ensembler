<?php
class Model_Sms extends CI_model{


    
         

    public function user_check(){
        $username = $this->input->post('username');
        $email = $this->input->post('email');
        $password = $this->input->post('password'); 

        $resut = $this->db->get_where('users', ['email'=>$email]);    
        $res = $resut->result_array();
        if(!$res){
            //insert 
            $data = array(
                'username'=>$username,
                'email' => $email,
                'password' => $password,
        );
          $this->db->insert('user', $data); 
          return true;
        }
        else{
            return false;
        }
    }




    public function insert_sms(){

        $mobile = $this->input->post('mobile');  
        $message = $this->input->post('message');
        $scheduletime = $this->input->post('scheduletime');

        if($scheduletime=='on'){
            $scheduledate = $this->input->post('date');
            $scheduleflag='1';
        }else{
            $scheduleflag='0';
            $scheduledate = '';
        }
        
        foreach($mobile as $mob):
         //insert 
            $data = array(
                'send_to'=>$mob,
                'send_from'=>'bipa',
                'message' => $message,
                'message_type_flag'=>'1',
                'status'=>'0',
                'scheduler_flag' =>$scheduleflag,
                'scheduled_time' => $scheduledate,
            );
        $respose=$this->db->insert('sms_out_queue', $data);
        endforeach;
        
        return $respose;
    }



    public function contact_check(){
        $name = $this->input->post('inputname');
        $mobile = $this->input->post('inputmobile');
        
        $query = $this->db->get_where('contact', ['mobile_no'=>$mobile]);    
        $res = $query->result_array();
        if(!$res){
            //insert 
            $data = array(
                'name'=>$name,
                'mobile_no' => $mobile,
            );
          $this->db->insert('contact', $data); 
          return true;
        }
        else{
            return false;
        }
        

    }
    
    

    // Update :: 20-08-2021

    public function get_bulk_upload_list(){
        $this->db->order_by('id', 'desc');
        if(strtolower($this->session->userdata('admin')['role']) !='admin'):
            $this->db->where('created_by' ,$this->session->userdata('admin')['id'] );
        endif;
            
        //$this->db->limit('10'); 
        return $this->db->get('sms_bulk_uploads')->result_array();
    } 

    // farhan :11-06-2021
   public function get_contact(){
        $query = $this->db->get('contact');    
        $result= $query->result_array();
        return $result;
    }
      
    public function get_history_in($mob){
        log_message('error', 'check mobil for fetch data ');
            log_message('error', $mob);
        $query1 = $this->db->query("SELECT send_to, message,create_date, IF( CHAR_LENGTH(send_to) >7, 'out', 'in') AS account_status   FROM sms_out_queue WHERE send_to=$mob UNION ALL SELECT send_to, message,create_date, IF( CHAR_LENGTH(send_to) >7, 'out', 'in') AS account_status FROM sms_in_queue WHERE send_from=$mob ORDER BY create_date");    
        log_message('error', 'LAST QUERY 1');
        log_message('error', $this->db->last_query());
        $query2 = $this->db->query("SELECT send_to, message,create_date, IF( CHAR_LENGTH(send_to) >7, 'out', 'in') AS account_status FROM whatsapp_out_queue WHERE send_to=$mob UNION ALL SELECT send_to, message,create_date, IF( CHAR_LENGTH(send_to) >7, 'out', 'in') AS account_status FROM whatsapp_in_queue WHERE send_from=$mob ORDER BY create_date");    
        log_message('error', 'LAST QUERY 2');
        log_message('error', $this->db->last_query());
        $result['sms']= $query1->result_array();
        $result['whatsapp']= $query2->result_array();
        print_r(json_encode($result));

    }


    // 
    public function read_inbox_message($id)
    {
        $this->db->where('id', $id);
        $this->db->where('message_flag', '0');
        $this->db->update('sms_in_queue', array('message_flag' => '1', 'action_by' => $this->session->userdata('admin')['id']));
    }


    // Update 26-07-2021
    public function update_replied_id($message_id, $last_insert_id)
    {
        $this->db->where('id', $message_id);
        // $this->db->where('message_flag', '0');
        $this->db->update('sms_in_queue', array('update_date' => date('Y-m-d H:i:s', strtotime('now')), 'message_flag' => '2', 'replied_id' =>$last_insert_id,  'action_by' => $this->session->userdata('admin')['id']));
        
    }

       
        


}

?>