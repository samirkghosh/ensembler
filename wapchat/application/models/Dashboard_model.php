<?php
class Dashboard_model extends CI_model{

    protected $table  = 'sms_out_queue' ; 

    protected $today_date ;   

    protected $from_date  ; 
    protected $to_date    ;  


    public function __construct()
    {
        $this->today_date   = date('Y-m-d ').'00:00:00';
        $this->from_date    = '2021-05-01 00:00:00';
        $this->to_date      = date('Y-m-d ').'23:59:59';


        $this->today_date_wa = date('Y-m-d').'T00:00:00Z';
        $this->from_date_wa = '2021-05-01T00:00:00Z';
        $this->to_date_wa = date('Y-m-d').'T23:59:59Z';

        //farhan :: 25-06-2021
        $this->yesterday_date = date( 'Y-m-d 00:00:00', strtotime( "-1 days" ) );
        $this->yesterday_to = date( 'Y-m-d 23:59:59', strtotime( "-1 days" ) );

        $this->y2_date = date( 'Y-m-d 00:00:00', strtotime( "-2 days" ) );
        $this->y2_to = date( 'Y-m-d 23:59:59', strtotime( "-2 days" ) );

        $this->y3_date = date( 'Y-m-d 00:00:00', strtotime( "-3 days" ) );
        $this->y3_to = date( 'Y-m-d 23:59:59', strtotime( "-3 days" ) );

        $this->y4_date = date( 'Y-m-d 00:00:00', strtotime( "-4 days" ) );
        $this->y4_to = date( 'Y-m-d 23:59:59', strtotime( "-4 days" ) );
    }
    
    /*
     *  0 = Queue :  in the queue to be  delivered to the gateway 
     *  1 = submitted   : - sent to gateway
     *  2 = pending     :   unable to send to gateway
     *  3 = Delivered/Not delivered :   by the gateway
     */
    
    #######################################
    ####// SMS SECTION TO SHOW REPORTS DATA
    #######################################


    public function total_sms_sent($today = false )
    {


        $this->db->select('id');
        if($today == false){
            // get Total record till now
            
            $this->db->where_in('status', [0,1,2,3,4]);
            $this->db->where('create_date >=', $this->from_date);
            $this->db->where('create_date <=', $this->to_date);
        }
        else{
            // get Total record today
            $this->db->where_in('status', [0,1,2,3,4]);
            $this->db->where('create_date >=', $this->today_date);
            $this->db->where('create_date <=', $this->to_date);
        }

        if($this->session->userdata('admin')['role_id'] != '1'){
            $this->db->where_in('created_by', $this->session->userdata('admin')['id']);    
        }

        
        $result = $this->db->get('sms_out_queue');
        log_message('error', 'DASHBOARD');
        log_message('error', $this->db->last_query());

        return $result->result_array();
       
    }

    

    public function total_sms_recieved($today = false )
    {

        $this->db->select('id');
        if($today == false){
            // get Total record till now
            
            $this->db->where('create_date >=', $this->from_date);
            $this->db->where('create_date <=', $this->to_date);
        }
        else{
            // get Total record today
            
            $this->db->where('create_date >=', $this->today_date);
            $this->db->where('create_date <=', $this->to_date);
        }

        $result = $this->db->get('sms_in_queue');
        log_message('error', 'DASHBOARD 2');
        log_message('error', $this->db->last_query());

        return $result->result_array();

        
    }

    public function total_active_boat($today = false )
    {

        $this->db->select('id');
        if($today == false){
            // get Total record till now
            //$this->db->where('bot_agent_flag =',0);
            $this->db->where('session_start_time >=', $this->from_date);
            $this->db->where('session_start_time <=', $this->to_date);
            $result = $this->db->get('overall_bot_chat_session');
        }
        else{
            // get Total record today
            
            $this->db->where('session_start_time >=', $this->today_date);
            $this->db->where('session_start_time <=', $this->to_date);
            $result = $this->db->get('bot_chat_session');
        }

        log_message('error', 'DASHBOARD 2');
        log_message('error', $this->db->last_query());

        return $result->result_array();

        
    }



    #######################################
    ####// WHATSAPP SECTION TO SHOW REPORTS DATA
    #######################################

    public function total_whatsapp_sent($today = false)
    {

        $this->db->select('id');
        if($today == false){
            // get Total record till now
            $this->db->where('direction=' ,'sent');
            $this->db->where('createdDatetime >=', $this->from_date_wa);
            $this->db->where('createdDatetime <=', $this->to_date_wa);
        }
        else{
            // get Total record today
            $this->db->where('direction=' ,'sent');
            $this->db->where('createdDatetime >=', $this->today_date_wa);
            $this->db->where('createdDatetime <=', $this->to_date_wa);
        }

        $result = $this->db->get('wa_in_out');
        log_message('error', 'whastsapp sent');
        log_message('error', $this->db->last_query());

        return $result->result_array();
       
    }

    public function total_whatsapp_received($today = false )
    {
        $this->db->select('id');
        if($today == false){
            // get Total record till now
            $this->db->where('direction=' ,'received');
            $this->db->where('createdDatetime >=', $this->from_date_wa);
            $this->db->where('createdDatetime <=', $this->to_date_wa);
        }
        else{
            // get Total record today
            $this->db->where('direction=' ,'received');
            $this->db->where('createdDatetime >=', $this->today_date_wa);
            $this->db->where('createdDatetime <=', $this->to_date_wa);
        }

        $result = $this->db->get('wa_in_out');
        log_message('error', 'whastsapp recieved');
        log_message('error', $this->db->last_query());

        return $result->result_array();
       
    }


    #######################################
    ####// Show LIve Notification  
    #######################################

    /*public function today_notifications($value='')
    {
        // SELECT siq.id, siq.send_from, siq.message FROM `sms_in_queue` AS siq RIGHT JOIN contact ON siq.send_from =contact.mobile_no WHERE send_from !=''
        $this->db->select('siq.id, siq.send_from, siq.message, contact.first_name');
        $this->db->from('sms_in_queue AS siq');
        $this->db->join('contact', 'siq.send_from =contact.mobile_no', 'right');
        $this->db->where('send_from !=', '');

        //$this->db->where('create_date >=', $this->today_date);
        //$this->db->where('create_date >=', $this->to_date);
        $result = $this->db->get();
        if($result->num_rows() > 0)
            return $result->result();
        else
            return false;
        log_message('error', 'checlin quere');
        log_message('error', $this->db->last_query());
    }*/
    public function today_notifications($value='')
    {
        // SELECT siq.id, siq.send_from, siq.message FROM `sms_in_queue` AS siq RIGHT JOIN contact ON siq.send_from =contact.mobile_no WHERE send_from !=''
        // $this->db->select('siq.id, siq.send_from, siq.message,siq.create_date');
        // $this->db->from('sms_in_queue AS siq');
        //$this->db->join('contact', 'siq.send_from =contact.mobile_no', 'right');
        $this->db->select('id,send_from,message,create_date');
        $this->db->from('sms_in_queue');
        $this->db->where(
            ['send_from !='=>'',
            'message_flag='=>0,
            'create_date >='=> $this->today_date,
            'create_date <='=> $this->to_date
            ]);
        $result = $this->db->get();
        if($result->num_rows() > 0)
            return $result->result_array();
        else
            return false;
        //log_message('error', 'checlin quere');
        //log_message('error', $this->db->last_query());
    }
        // farhan :: 06/07/2021

    public function get_name($no)
    {
        $query = $this->db->get_where('contact', ['mobile_no'=>$no]);    
        $res = $query->row_array();
        return $res['first_name'];

    }

    #######################################
    ####// Show chart data  //farhan :: 25-06-2021
    #######################################

    public function total_sms_queue($today)
    {


        $this->db->select('id');
        $this->db->where_in('status',0);

        if ($today == 'today'){
            // get Total record today  
            $this->db->where('create_date >=', $this->today_date);
            $this->db->where('create_date <=', $this->to_date);

        }else if($today == 'yesterday'){

            $this->db->where('create_date >=', $this->yesterday_date);
            $this->db->where('create_date <=', $this->yesterday_to);

        }else if($today == 'y2'){
           
            $this->db->where('create_date >=', $this->y2_date);
            $this->db->where('create_date <=', $this->y2_to);

        }else if($today == 'y3'){
           
            $this->db->where('create_date >=', $this->y3_date);
            $this->db->where('create_date <=', $this->y3_to);
        }
        else if ($today == 'y4'){
          
            $this->db->where('create_date >=', $this->y4_date);
            $this->db->where('create_date <=', $this->y4_to);
        }
  
        if($this->session->userdata('admin')['role_id'] != '1'){
            $this->db->where_in('created_by', $this->session->userdata('admin')['id']);    
        }

        $result = $this->db->get('sms_out_queue');
         log_message('error', 'SMS Queue');
        log_message('error', $this->db->last_query());

        return $result->result_array();
       
    }

    public function total_sms_submitted($today)
    {

        $this->db->select('id');
        $this->db->where_in('status',1);
            if ($today == 'today'){
                // get Total record today  
                $this->db->where('create_date >=', $this->today_date);
                $this->db->where('create_date <=', $this->to_date);

            }else if($today == 'yesterday'){

                $this->db->where('create_date >=', $this->yesterday_date);
                $this->db->where('create_date <=', $this->yesterday_to);

            }else if($today == 'y2'){
               
                $this->db->where('create_date >=', $this->y2_date);
                $this->db->where('create_date <=', $this->y2_to);

            }else if($today == 'y3'){
               
                $this->db->where('create_date >=', $this->y3_date);
                $this->db->where('create_date <=', $this->y3_to);
            }
            else if ($today == 'y4'){
              
                $this->db->where('create_date >=', $this->y4_date);
                $this->db->where('create_date <=', $this->y4_to);
            }

        if($this->session->userdata('admin')['role_id'] != '1'){
            $this->db->where_in('created_by', $this->session->userdata('admin')['id']);    
        }
        $result = $this->db->get('sms_out_queue');
         log_message('error', 'DASHBOARD');
        log_message('error', $this->db->last_query());

        return $result->result_array();
       
    }

    public function total_sms_delivered($today)
    {


        $this->db->select('id');
        $this->db->where_in('status',2);
        
        if ($today == 'today'){
            // get Total record today  
            $this->db->where('create_date >=', $this->today_date);
            $this->db->where('create_date <=', $this->to_date);

        }else if($today == 'yesterday'){

            $this->db->where('create_date >=', $this->yesterday_date);
            $this->db->where('create_date <=', $this->yesterday_to);

        }else if($today == 'y2'){
           
            $this->db->where('create_date >=', $this->y2_date);
            $this->db->where('create_date <=', $this->y2_to);

        }else if($today == 'y3'){
           
            $this->db->where('create_date >=', $this->y3_date);
            $this->db->where('create_date <=', $this->y3_to);
        }
        else if ($today == 'y4'){
          
            $this->db->where('create_date >=', $this->y4_date);
            $this->db->where('create_date <=', $this->y4_to);
        }

        if($this->session->userdata('admin')['role_id'] != '1'){
            $this->db->where_in('created_by', $this->session->userdata('admin')['id']);    
        }
        $result = $this->db->get('sms_out_queue');
         log_message('error', 'DASHBOARD');
        log_message('error', $this->db->last_query());

        return $result->result_array();
       
    }

    public function total_sms_undelivered($today)
    {


        $this->db->select('id');
        $this->db->where_in('status',3);
        
        if ($today == 'today'){
            // get Total record today  
            $this->db->where('create_date >=', $this->today_date);
            $this->db->where('create_date <=', $this->to_date);

        }else if($today == 'yesterday'){

            $this->db->where('create_date >=', $this->yesterday_date);
            $this->db->where('create_date <=', $this->yesterday_to);

        }else if($today == 'y2'){
           
            $this->db->where('create_date >=', $this->y2_date);
            $this->db->where('create_date <=', $this->y2_to);

        }else if($today == 'y3'){
           
            $this->db->where('create_date >=', $this->y3_date);
            $this->db->where('create_date <=', $this->y3_to);
        }
        else if ($today == 'y4'){
          
            $this->db->where('create_date >=', $this->y4_date);
            $this->db->where('create_date <=', $this->y4_to);
        }
        if($this->session->userdata('admin')['role_id'] != '1'){
            $this->db->where_in('created_by', $this->session->userdata('admin')['id']);    
        }
        $result = $this->db->get('sms_out_queue');
         log_message('error', 'DASHBOARD');
        log_message('error', $this->db->last_query());

        return $result->result_array();
       
    }

    // farhan :: 05-07-2021
    public function total_quota_utilized($today = false )
    {


        $this->db->select_sum('message_unit');;
        if($today == false){
            // get Total record till now
            
            $this->db->where_in('status', [0,1,2,3,4]);
            $this->db->where('create_date >=', $this->from_date);
            $this->db->where('create_date <=', $this->to_date);
        }
        else{
            // get Total record today
            $this->db->where_in('status', [0,1,2,3,4]);
            $this->db->where('create_date >=', $this->today_date);
            $this->db->where('create_date <=', $this->to_date);
        }

        if($this->session->userdata('admin')['role_id'] != '1'){
            $this->db->where_in('created_by', $this->session->userdata('admin')['id']);    
        }

        $result = $this->db->get('sms_out_queue')->row();
        return $result->message_unit;
       
    }


    public function coverted_agent_bot($today = false)
    {

        $this->db->select('id');
        if($today == false)
        {
            // get Total record till now
            $this->db->where('bot_agent_flag =',1);
            $this->db->where('session_start_time >=', $this->from_date);
            $this->db->where('session_start_time <=', $this->to_date);
            $result = $this->db->get('overall_bot_chat_session');
        }
        else
        {
            // get Total record today
            $this->db->where('bot_agent_flag =',1);
            $this->db->where('session_start_time >=', $this->today_date);
            $this->db->where('session_start_time <=', $this->to_date);
            $result = $this->db->get('overall_bot_chat_session');
        }

        log_message('error', 'Bot converted to Agent');
        log_message('error', $this->db->last_query());

        return $result->result_array();

    }


    public function success_bot_session($today = false)
    {
    
        $this->db->select('id');
        if($today == false)
        {
            // get Total record till now
            $this->db->where('bot_agent_flag =',0);
            $this->db->where('session_start_time >=', $this->from_date);
            $this->db->where('session_start_time <=', $this->to_date);
            $result = $this->db->get('overall_bot_chat_session');
        }
        else
        {
            // get Total record today
            $this->db->where('bot_agent_flag =',0);
            $this->db->where('session_start_time >=', $this->today_date);
            $this->db->where('session_start_time <=', $this->to_date);
            $result = $this->db->get('overall_bot_chat_session');
        }
        log_message('error', 'Bot Success');
        log_message('error', $this->db->last_query());
        return $result->result_array();

    }


    public function converted_agent_history($today){

        $this->db->select('id');
        $this->db->where('bot_agent_flag =',1);

        if($today == 'yesterday'){
          
            $this->db->where('session_start_time >=', $this->yesterday_date);
            $this->db->where('session_start_time <=', $this->yesterday_to);

        }else if($today == 'y2'){
            $this->db->where('session_start_time >=', $this->y2_date);
            $this->db->where('session_start_time <=', $this->y2_to);

        }else if($today == 'y3'){
            $this->db->where('session_start_time >=', $this->y3_date);
            $this->db->where('session_start_time <=', $this->y3_to);

        }else if($today == 'y4'){
            $this->db->where('session_start_time >=', $this->y4_date);
            $this->db->where('session_start_time <=', $this->y4_to);

        }

        $result = $this->db->get('overall_bot_chat_session');
        log_message('error', 'History Converted Agent');
        log_message('error', $this->db->last_query());
        return $result->result_array();

    }

    public function success_bot_history($today){

        $this->db->select('id');
        $this->db->where('bot_agent_flag =',0);

        if($today == 'yesterday'){
            $this->db->where('session_start_time >=', $this->yesterday_date);
            $this->db->where('session_start_time <=', $this->yesterday_to);
           
        }else if($today == 'y2'){
            $this->db->where('session_start_time >=', $this->y2_date);
            $this->db->where('session_start_time <=', $this->y2_to);
            
        }else if($today == 'y3'){
            $this->db->where('session_start_time >=', $this->y3_date);
            $this->db->where('session_start_time <=', $this->y3_to);
        }else if($today == 'y4'){
            $this->db->where('session_start_time >=', $this->y4_date);
            $this->db->where('session_start_time <=', $this->y4_to);
        }
        $result = $this->db->get('overall_bot_chat_session');
        log_message('error', 'History Bot');
        log_message('error', $this->db->last_query());
        return $result->result_array();
        
    }


}

?>