<?php
class User_model extends CI_model{

    public function get($id = null) {

        $this->db->select('users.*,roles.name as user_type,roles.id as role_id')->from('users')->join("users_roles", "users_roles.user_id = users.id", "left")->join("roles", "users_roles.role_id = roles.id", "left");


        if ($id != null) {
            $this->db->where('users.id', $id);
        } else {
            $this->db->where('users.is_active', 1);
            $this->db->order_by('users.id');
        }
        $query = $this->db->get();
        
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }


    public function get_user_groups()
    {
        $this->db->where('is_active', '1');
        $this->db->where('is_superadmin', '0');
        return $this->db->get('roles')->result();
    }

    public function get_role_by_id($id)
    {
        if($id > 0){
            $result = $this->db->get_where('roles', array('id' => $id))->row();
            return $result->name;
        }
        return '' ;
    }

    public function getUsers($id='', $loggedin=''){

        if(!empty($id)){
            $this->db->where('id', $id);
            $ret =$this->db->get('users')->row();
            log_message('error', $this->db->last_query()); 
            return $ret;
        }
         
        if($loggedin=='1'):
            $this->db->where(['role_id !='=>'1', 'delete_status !='=>'1', 'active_login' => '1']);
        else:
            $this->db->where(['role_id !='=>'1','delete_status !='=>'1']);
        endif;

        $res = $this->db->get('users')->result(); 
       
        return $res ;
    }

    public function save_users($data, $id = 0 )
    {
        if($id>0){
            // Update command here
            $this->db->where('id', $id);
            $this->db->update('users', $data);
            return $this->db->affected_rows();
        }
        else{
            // Insert Record
            $this->db->insert('users', $data);
            return $this->db->insert_id();
        }
        
    }

    // Vijay : 03-07-2021
    //  track record of user quota update
    public function update_user_quota($data)
    {
        $this->db->insert('quota_record', $data);
        return $this->db->insert_id();        
    }


    public function quota_record($data, $id = 0)
    {
        if($id>0){
            // Update User Quota Record
            $this->db->insert('quota_record', $data);
        
        }
        else{
            // Insert User Quota Record
            $this->db->insert('quota_record', $data);

        }
    }

     //28-06-2021  i think not in use 
    public function get_quota_user($id, $row)
    {
        $this->db->from('users');
        $this->db->where('id', $id);
        $query = $this->db->get();

        if($query->num_rows()>0) {

            $data = $query->row_array();
            $value = $data[$row];
            return $value;
        } 
        else{
            return false;
        }
    }


        

    //farhan :: 19-06-2021 -- Get
    public function get_quota_record($id, $type = '')
    {

       if($id=='all'){

        $query = $this->db->get('quota_record');    
        return array();

       }
       else{
            if($type =='sms'){
                // $this->db->where('sms_update !=', '0');
                $this->db->select('id, user_id, SUM(sms_update) AS total_alloted ');

               /* $this->db->where('sms_update !=', '0');
                $this->db->select('id, user_id,previous_sms AS previous ,sms_update AS update, till_now_sms AS till_now , created_date');*/
            }
            else{
                $this->db->where('whatsapp_update !=', '0');
                $this->db->select('id, user_id, previous_whatsapp AS previous, whatsapp_update AS update , till_now_whatsapp AS till_now, created_date');
            }

           $query= $this->db->where('user_id', $id);
           return $this->db->get('quota_record')->result_array();
        }
    }


    //farhan ::24-06-2021
   public function delete_user($id){
        $data = array (
            'delete_status'=>1,
            'delete_date'=>date('Y-m-d H:i:s')
        );
        $this->db->update('users', $data, array('id' => $id));
        return true;   
   }



   public function get_quota_sms($user_id =''){
        if(empty($user_id)){
            $user_id=$this->session->userdata('admin')['id'];
        }

        $resut = $this->db->get_where('users', ['id'=>$user_id]);    
        $res = $resut->row_array();
        log_message('error', "SMS LAST Q ".$this->db->last_query());
        return $res['sms_quota'];

    }
    public function get_quota_whatsapp($user_id =''){
        if(empty($user_id)){
            $user_id=$this->session->userdata('admin')['id'];
        }
        $resut = $this->db->get_where('users', ['id'=>$user_id]);    
        $res = $resut->row_array();
        log_message('error', "WHATS LAST Q ".$this->db->last_query());
        return $res['whatsapp_quota'];
    }


    public function getBotSession($id = '')
    {
        if(!empty($id)){
            $this->db->where('id', $id);
            $ret =$this->db->get('bot_chat_session')->row();
            return $ret;
        }
        $this->db->where(['agent_forworded ='=>'1']);
        return $this->db->get('bot_chat_session')->result();
    }



}

?>