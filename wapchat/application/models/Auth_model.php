<?php
class Auth_model extends CI_model{

    public function getStaffRoles($staffid) {
        $this->db->select('users_roles.*,roles.name');
        $this->db->from('users_roles');
        $this->db->join('roles', 'roles.id=users_roles.role_id', 'inner');
        $this->db->where('users_roles.user_id', $staffid);
        $query = $this->db->get();
        log_message('error', 'getStaffRoles');
        log_message('error', $this->db->last_query());
        return $query->result(); 
    }

    public function getByEmail($email) {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('email', $email);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return FALSE;
        }
    }

    public function login_check(){
        $email = html_escape($this->input->post('email')); 
        $password = html_escape($this->input->post('password'));
        
        $login_credentials = array('email' => $email, 'password'=> md5($password) ) ;
        
        $resut = $this->db->get_where('users', $login_credentials);    
        if($resut->num_rows() > 0 ){
            $user_details = $resut->row_array();
            log_message('error', json_encode($user_details));

            $result = $this->getByEmail($email);
            $roles = $this->getStaffRoles($result->id);
            $result->roles = array($roles[0]->name => $roles[0]->role_id);
            
            if($result->is_active!='enabled')
                return false; 

            if($result->delete_status =='1')
                return false;

            if($result->role_id != '1' ){
                if($result->active_login == '1'){
                    return 'logedin';    
                }
            }    


          

            $session_data = array(
                'id' => $result->id,
                'username' => $result->username,
                'email' => $result->email,
                'roles' => $result->roles,
                'role' => $roles[0]->name,
                'role_id' => $roles[0]->role_id,
                'templates_id' => $result->templates_id,
                'first_login_status'=>$result->first_login_status,
                'last_login'=>$result->last_login,
                'delete_status'=>$result->delete_status
                 
            );

            $id = $result->id ;
            
             //insert login history //FARHAN :: 22-06-2021
            $ip = $this->input->ip_address();
            $arr = array ('user'=>$id, 'login_time'=>date('Y-m-d H:i:s'), 'status'=>1, 'ip'=>$ip );
            $this->db->insert('login_history', $arr, array('id' => $id));

            //update login_time in users //FARHAN :: 22-06-2021
            $login_time = array ('last_login'=>date('Y-m-d H:i:s'), 'active_login' => '1' );
            $this->db->update('users', $login_time, array('id' => $id));
            
            $this->session->set_userdata('admin',$session_data);
           

            return $result->first_login_status;
        }
        else{
            return false;
        }
    }

    



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

    ###################################################################
    ###############
    ###################################################################
    
    public function get_quota_sms($user_id=''){
        if(empty($user_id)){
            $user_id=$this->session->userdata('admin')['id'];
        }
       
        $resut = $this->db->get_where('users', ['id'=>$user_id]);    
        $res = $resut->row_array();
        return $res['sms_quota'];

    }

    // : 01-10-2021 : 

    public function get_consumend_quota_sms($user_id =''){
        if(empty($user_id)){
            $user_id=$this->session->userdata('admin')['id'];
        }

        $resut = $this->db->get_where('users', ['id'=>$user_id]);    
        $res = $resut->row_array();
        return $res['sms_quota_consumed'];

    }
            
    // 21-06-2021 Farhan Update 
    public function insert_sms(){
        log_message('error', "insert_sms");
        log_message('error', json_encode($_POST));
        $message_id = $this->input->post('message_id'); 
        $mobile = $this->input->post('mobile'); 
        $message = $this->input->post('message');
        $scheduletime = $this->input->post('scheduletime');
        
        log_message('error', json_encode($mobile));
        if($scheduletime=='on'){
            $scheduledate = date('Y-m-d H:i', strtotime($this->input->post('date')));
            $scheduleflag='0';
        }else{
            $scheduleflag='1';
            $scheduledate = date('Y-m-d H:i');
        }
        
        //send sms
            foreach($mobile as $mob):
                $data = array(
                    'send_to'=>$mob,
                    'send_from'=>'bipa',
                    'message' => $message,
                    'message_type_flag'=>'0',
                    'status'=>'0',
                    'schedule_flag' =>$scheduleflag,
                    'schedule_time' => $scheduledate,
                    'created_by'  => $this->session->userdata('admin')['id'],
                );
              $respose = $this->db->insert('sms_out_queue', $data);  
            endforeach; 
            if($message_id > 0){
                $respose = $this->db->insert_id() ;
            } 

        return $respose;
    }
    // 21-06-2021 Farhan Update
    public function sms_quota_update($mobCnt){
        
        $quotaCnt = $this->get_quota_sms();
        $consumed_ct = $this->get_consumend_quota_sms();
        $user_id=$this->session->userdata('admin')['id'];
        $sms_quota_count = $quotaCnt-$mobCnt;

        // $this->db->set("sms_quota",$sms_quota_count);
        $data['sms_quota'] = $sms_quota_count ;
        $data['sms_quota_consumed'] = $consumed_ct+$mobCnt ;

        $this->db->where('id', $user_id);
        $this->db->update('users', $data);

       
        log_message('error', $this->db->last_query());

    }   

    // Comment - 21-06-2021
   /* public function insert_sms(){

        $mobile = $this->input->post('mobile');  
        $message = $this->input->post('message');
        $scheduletime = $this->input->post('scheduletime');

        if($scheduletime=='on'){
            $scheduledate = date('Y-m-d H:i', strtotime($this->input->post('date')));
            $scheduleflag='0';
        }else{
            $scheduleflag='1';
            $scheduledate = date('Y-m-d H:i');
        }

        log_message('error', json_encode($_POST));
        
        foreach($mobile as $mob):
            $data = array(
                'send_to'=>$mob,
                'send_from'=>'bipa',
                'message' => $message,
                'message_type_flag'=>'0',
                'status'=>'0',
                'schedule_flag' =>$scheduleflag,
                'schedule_time' => $scheduledate,
            );
        $respose=$this->db->insert('sms_out_queue', $data);
        endforeach;
        return $respose;
    }*/

    // Vijay : 24-06-2021
    public function is_customer_exist($mobile)
    {
        $res = $this->db->get_where('contact', ['mobile_no'=>$mobile]);  
        if($res->num_rows() > 0 )
            return true;
        else 
            return false;
    }



    public function contact_check(){
        $name = $this->input->post('inputname');
        $mobile = $this->input->post('inputmobile');
               
        if(!$this->is_customer_exist($mobile)){
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
        

    public function get_contact($mobile = '' ){
        if(!empty($mobile)){
            $this->db->like('mobile', $mobile , 'both');
            $query = $this->db->get('customers');
            if($query->num_rows() > 0){
                $result = $query->row();
                return $result->name;
            }
            return '' ;    
        }
        
        // $query = $this->db->get('customers');    
        // $result = $query->result_array();
        // return $result;
    }
      
#################################### (Contact curd) ################################################

    public function is_mobile_exist($mobile, $id='')
    {   
        if(!empty($id)){
            $arr = ['mobile_no'=>$mobile, 'id !=' => $id ]; 
        }
        $query = $this->db->get_where('contact', $arr);
        log_message('error', 'update contact ');
        log_message('error',  $this->db->last_query());
        if($query->num_rows() > 0)
            return true;
        else
            return false;
        
    } 




    // insert contact
    public function insert_contact($data){

        $mobile = $this->input->post('inputmobile');      
        $query = $this->db->get_where('contact', ['mobile_no'=>$mobile]);    
        $res = $query->result_array();
        if(!$res){
            //insert 
          $this->db->insert('contact', $data); 
          return true;
        }
        else{
            return false;
        }
        

    }
    // view all contacts
    public function fetch_contact(){
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('contact');    
        return $query;
    }
    // view single contact
    public function fetch_single_contact($id){
        $this->db->where("id", $id);  
        $query = $this->db->get("contact");  
        return $query;  
    }
    // update contact
    public function update_contact($data, $id){
        if($this->is_mobile_exist($data['mobile_no'], $id)){
            return false ;
        }
        else{
            $this->db->where("id", $id);  
            $this->db->update("contact", $data);
            return true;
        }

    }
    // delete contact
    public function delete_contact($id){
        $this->db->where("id", $id);  
        $this->db->delete("contact");  
    }

#################################### (SMS template crud) ################################################
    //insert sms template 
    public function insert_sms_template($data){
        $this->db->insert('user_templates', $data);
        return true; 
    }

    // view all sms template   
    public function get_sms_template(){        
        $query = $this->db->get('user_templates');    
        return $query;
    }
    
    //view single sms template
    public function get_template($tempname)
    {
        $this->db->where("name", $tempname);  
        $query = $this->db->get("user_templates");  
        $result= $query->row_array(); 
        print_r(json_encode($result));

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

           
        

#################################### (Bulk SMS) ################################################
    public function insert_bulk($data){
        // log_message('error', $this->db->last_query());
        return $this->db->insert_batch('sms_out_queue',$data);
        //return true;
    }

    public function last_contact_message(){
        $query = $this->db->get('contact');    
        $result= $query->result_array();
        $data=[];
        foreach($result as $val){
            //print_r($val);
            //farhan:: 22-06-2021
            $name= $val['first_name'];
            $mobile = $val['mobile_no'];
            $query2 = $this->db->query("Select send_to,message from sms_out_queue where send_to='$mobile' order by id desc limit 1");
            $result2= $query2->row_array();
            $message  =$result2['message'];
            $arr= array('first_name'=>$name,'mobile' =>$mobile, 'message' => $message );
            array_push($data, $arr);    
        }
        print_r(json_encode($data));
    }


    //update password //FARHAN ::22-06-2021
    public function update_password($id,$password)
    {
        $pass = html_escape($password);
        $md5 = md5($pass);
        $data = array (
            'password'=>$md5,
            'first_login_status'=>1,
            'active_login'=>0
        );
        $this->db->update('users', $data, array('id' => $id));
        return true;
    }

    //logout //FARHAN ::22-06-2021
    public function logout($user_id)
    {
        $data = array (
            'logout_time'=>date('Y-m-d H:i:s'),
            'status'=>0
        );
        $this->db->update('login_history',$data, array('user' => $user_id,'status'=>1));

        $login_time = array('active_login' => '0');
        $this->db->update('users', $login_time, array('id' => $user_id, ));
        
        return true;
    }

    public function check_password($id)
    {
        $this->db->select('password');
        $this->db->from('users');
        $this->db->where('id', $id);
        return $this->db->get()->row()->password;
    }

}

?>