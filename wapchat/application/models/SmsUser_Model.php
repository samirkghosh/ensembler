<?php
class SmsUser_Model extends CI_model{

    //insert sms template 
    public function insert_sms_template($data){
        $this->db->insert('user_templates', $data);
        return true; 
    }
    // view all sms template   
    public function get_sms_template($id =''){
        if(!empty($id)){
                    $this->db->where('id',$id);
            $query = $this->db->get('user_templates');    
            return $query->row();

        }
        $query = $this->db->get('user_templates');    
        return $query;
    }
    // view single sms template
    public function fetch_single_template($id){
        $this->db->where("id", $id);  
        $query = $this->db->get("user_templates");  
        return $query;  
    }   
    //update sms template
    public function update_sms_template($data, $id){
        $this->db->where("id", $id);  
        $this->db->update("user_templates", $data);
        return true;
    } 
    //delete sms templates
    public function delete_sms_template($id){
        $this->db->where("id", $id);  
        $this->db->delete("user_templates");  
    }

    //Vijay : 07-06-2021


    //Vijay : 07-06-2021       
    
}

?>