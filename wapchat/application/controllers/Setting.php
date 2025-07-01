<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		// $this->load->model('Auth_model');
		// $this->load->library('excel');
	}

    public function is_active_gateway($value=''){
        $result = $this->db->get('tbl_gateway_status')->row();
        $diff = strtotime('now') - strtotime($result->status_update_time);
        if($diff <= 300)
            echo 'true';
        else
            echo 'false';
    }

        
    public function is_active_sendstatus($value=''){
        $result = $this->db->get('tbl_gateway_status')->row();
        echo json_encode($result);die();
    }

    public function update_sendstatus(){
        $this->db->update('tbl_gateway_status', array('i_sendstatus' => '0', 'v_sendreason' => ''));
    }



    public function general_settings($value=''){
        $data['title'] = 'Settings || Bipa Notification System' ;
        $data['breadcrumb'] = "General Settings";

        

        $this->form_validation->set_rules('wa_channel_id', 'Whatsapp Channel Id', 'trim|required');
        $this->form_validation->set_rules('wa_live_key', 'Whatsapp API Key', 'trim|required');
        $this->form_validation->set_rules('imap_host', 'Imap Host', 'trim|required');
        $this->form_validation->set_rules('imap_email', 'Imap Email', 'trim|required');
        $this->form_validation->set_rules('imap_pass', 'Imap Password', 'trim|required');
        
        if($this->form_validation->run()==true){
            $this->setting_model->update_settings();
            $this->session->set_flashdata('success','Settings Update successfully');
            redirect('setting/general_settings');
        }
        
        $this->load->view('setting/sys_settings', $data);
    }

   

	/*public function system_settings($param1 = "") {
        if ($this->session->userdata('admin_login') != true) {
            redirect(site_url('login'), 'refresh');
        }

        if ($param1 == 'system_update') {
            $this->crud_model->update_system_settings();
            $this->session->set_flashdata('flash_message', get_phrase('system_settings_updated'));
            redirect(site_url('admin/system_settings'), 'refresh');
        }

        if ($param1 == 'logo_upload') {
            move_uploaded_file($_FILES['logo']['tmp_name'], 'assets/backend/logo.png');
            $this->session->set_flashdata('flash_message', get_phrase('backend_logo_updated'));
            redirect(site_url('admin/system_settings'), 'refresh');
        }

        if ($param1 == 'favicon_upload') {
            move_uploaded_file($_FILES['favicon']['tmp_name'], 'assets/favicon.png');
            $this->session->set_flashdata('flash_message', get_phrase('favicon_updated'));
            redirect(site_url('admin/system_settings'), 'refresh');
        }

        $page_data['languages']  = $this->crud_model->get_all_languages();
        $page_data['page_name'] = 'system_settings';
        $page_data['page_title'] = get_phrase('system_settings');
        $this->load->view('backend/index', $page_data);
    }*/

    public function whatsapp_setting($value=''){
        
        $this->form_validation->set_rules('wa_channel_id', 'Whatsapp Channel Id', 'trim|required');
        $this->form_validation->set_rules('wa_live_key', 'Whatsapp API Key', 'trim|required');

        if($this->form_validation->run() == true){
            $previous_data = array('wa_channel_id' => get_settings('wa_channel_id'),'wa_live_key'=>get_settings('wa_live_key') );
            $this->setting_model->settings_audit($previous_data, $_POST);
            $this->setting_model->update_settings();
            echo json_encode(array('st' => 0, 'msg' => 'Success! Whatsapp setting update success'));die();
        }
        else{
            $data = array(
                'wa_channel_id' => form_error('wa_channel_id'),
                'wa_live_key' => form_error('wa_live_key'),                
            );
                                
            echo json_encode(array('st' => 1, 'msg' => $data));die();
        }

    }

    public function imap_setting($value=''){
        
        $this->form_validation->set_rules('imap_host', 'Imap Host', 'trim|required');
        $this->form_validation->set_rules('imap_email', 'Imap Email', 'trim|required');
        $this->form_validation->set_rules('imap_pass', 'Imap Password', 'trim|required');

        if($this->form_validation->run() == true){
            $previous_data = array('imap_host' => get_settings('imap_host'),'imap_email'=>get_settings('imap_email') ,'imap_pass'=>get_settings('imap_pass') );
            $this->setting_model->settings_audit($previous_data, $_POST);

            $this->setting_model->update_settings();
            echo json_encode(array('st' => 0, 'msg' => 'Success! Imap setting update success'));die();
        }
        else{
            $data = array(
                'imap_host' => form_error('imap_host'),
                'imap_email' => form_error('imap_email'),                
                'imap_pass' => form_error('imap_pass'),                
            );
                                
            echo json_encode(array('st' => 1, 'msg' => $data));die();
        }

    }


    public function smtp_setting($value=''){
        
        $this->form_validation->set_rules('smtp_host', 'Smtp Host', 'trim|required');
        $this->form_validation->set_rules('from_email', 'Smtp Email', 'trim|required');
        $this->form_validation->set_rules('smtp_password', 'Smtp Password', 'trim|required');
        $this->form_validation->set_rules('port', 'Smtp Port', 'trim|required');
        $this->form_validation->set_rules('encryption', 'Encryption', 'trim|required');

        if($this->form_validation->run() == true){
            $previous_data = array('smtp_host' => get_settings('smtp_host'),'from_email'=>get_settings('from_email') ,'smtp_password'=>get_settings('smtp_password'),'port'=>get_settings('port'),'encryption'=>get_settings('encryption') );
            $this->setting_model->settings_audit($previous_data, $_POST);

            $this->setting_model->update_settings();
            echo json_encode(array('st' => 0, 'msg' => 'Success! Smtp setting update success'));die();
        }
        else{
            $data = array(
                'smtp_host' => form_error('smtp_host'),
                'from_email' => form_error('from_email'),                
                'smtp_password' => form_error('smtp_password'),                
                'port' => form_error('port'),                
                'encryption' => form_error('encryption'),                
            );
                                
            echo json_encode(array('st' => 1, 'msg' => $data));die();
        }

    }


    public function setting_ini($value=''){
        $data['title'] = 'Settings || Bipa Notification System' ;
        $data['breadcrumb'] = "Settings";



        $myfile = fopen("smpp_ini.txt", "r+") or die("Unable to open file!");
        if($myfile){
            // echo fread($myfile,filesize("smpp_ini.txt"));
            while(!feof($myfile)) {
                // convert string to array
                $ar =[];
                $ar = explode("=", fgets($myfile));
                 $key = $ar['0'];
                 $val = $ar['1'];

                if($key == 'loglevel') $data['loglevel'] = $val;
                if($key == 'logpath') $data['logpath'] = $val;
                if($key == 'pollinterval') $data['pollinterval'] = $val;
                if($key == 'smpphost') $data['smpphost'] = $val;
                if($key == 'port')  $data['port'] = $val;
                if($key == 'systemid')  $data['systemid'] = $val;
                if($key == 'password')  $data['password'] = $val;
                if($key == 'dbhost') $data['dbhost'] = $val;
                if($key == 'dbuid') $data['dbuid'] = $val;
                if($key == 'dbpwd')  $data['dbpwd'] = $val;
                if($key == 'dbname') $data['dbname'] = $val;
                if($key == 'email_from')  $data['email_from'] = $val;
                if($key == 'email_to')  $data['email_to'] = $val;
                if($key == 'sms2email')  $data['sms2email'] = $val;
                if($key == 'from')  $data['from'] = $val;
                if($key == 'to')  $data['to'] = $val;
                if($key == 'maxduplicate') $data['maxduplicate'] = $val;
                if($key == 'batchsize')  $data['batchsize'] = $val;
            }
            fclose($myfile);
        }
                 
        // print_r($data);
        $this->load->view('setting/ini_settings', $data);
    }
                    
                                  
               

    public function ini_update($value=''){

        $this->form_validation->set_rules('loglevel', 'Log Level', 'trim|required|integer|max_length[255]');
        $this->form_validation->set_rules('logpath', 'Logpath', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('pollinterval', 'Poll interval', 'trim|required|integer|max_length[4]');
        $this->form_validation->set_rules('smpphost', 'SMPP Host', 'trim|required|valid_ip|max_length[15]');
        $this->form_validation->set_rules('port', 'SMPP port', 'trim|required|integer|max_length[5]');
        $this->form_validation->set_rules('systemid', 'SystemId', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('dbhost', 'DB Host', 'trim|required|max_length[64]');
        $this->form_validation->set_rules('dbuid', 'DB User', 'trim|required|max_length[64]');
        $this->form_validation->set_rules('dbpwd', 'DB Password', 'trim|required|max_length[64]');
        $this->form_validation->set_rules('dbname', 'DB Name', 'trim|required|max_length[64]');
        $this->form_validation->set_rules('from', 'SMPP from', 'trim|required|integer|max_length[5]');
        $this->form_validation->set_rules('sms2email', 'SMS to email', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('email_from', 'From Email', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('email_to', 'To Email', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('maxduplicate', 'Max Duplicate', 'trim|required|integer|max_length[4]');
        $this->form_validation->set_rules('batchsize', 'Batch Size', 'trim|required|integer|max_length[4]');
        
        if($this->form_validation->run()==true){
            // Audit Trail 
            $myfile = fopen("smpp_ini.txt", "r+") or die("Unable to open file!");
            if($myfile){
                // echo fread($myfile,filesize("smpp_ini.txt"));
                while(!feof($myfile)) {
                    // convert string to array
                    $ar =[];
                    $ar = explode("=", fgets($myfile));
                     $key = $ar['0'];
                     $val = $ar['1'];

                    if($key == 'loglevel') $data['loglevel'] = $val;
                    if($key == 'logpath') $data['logpath'] = $val;
                    if($key == 'pollinterval') $data['pollinterval'] = $val;
                    if($key == 'smpphost') $data['smpphost'] = $val;
                    if($key == 'port')  $data['port'] = $val;
                    if($key == 'systemid')  $data['systemid'] = $val;
                    if($key == 'password')  $data['password'] = $val;
                    if($key == 'dbhost') $data['dbhost'] = $val;
                    if($key == 'dbuid') $data['dbuid'] = $val;
                    if($key == 'dbpwd')  $data['dbpwd'] = $val;
                    if($key == 'dbname') $data['dbname'] = $val;
                    if($key == 'email_from')  $data['email_from'] = $val;
                    if($key == 'email_to')  $data['email_to'] = $val;
                    if($key == 'sms2email')  $data['sms2email'] = $val;
                    if($key == 'from')  $data['from'] = $val;
                    if($key == 'to')  $data['to'] = $val;
                    if($key == 'maxduplicate') $data['maxduplicate'] = $val;
                    if($key == 'batchsize')  $data['batchsize'] = $val;
                }
                fclose($myfile);
            }

             
            $this->setting_model->settings_audit($data, $_POST);



            $myfile = fopen("smpp_ini.txt", "w") or die("Unable to open file!");
            if($myfile){
                $txt .= "[main]"."\n";
                $txt .= "loglevel=".$this->input->post('loglevel')."\n";
                $txt .= "logpath=".$this->input->post('logpath')."\n";
                $txt .= "\n\n";
                $txt .= "#sms sending interval to look into out queue in sec"."\n";
                $txt .= "pollinterval=".$this->input->post('pollinterval')."\n";
                $txt .= "\n\n";
                $txt .= "#SMPP Hosts"."\n";
                $txt .= "smpphost=".$this->input->post('smpphost')."\n";
                $txt .= "port=".$this->input->post('port')."\n";
                $txt .= "systemid=".$this->input->post('systemid')."\n";
                $txt .= "password=".$this->input->post('password')."\n";
                $txt .= "\n\n";
                $txt .= "#database"."\n";
                $txt .= "dbhost=".$this->input->post('dbhost')."\n";
                $txt .= "dbuid=".$this->input->post('dbuid')."\n";
                $txt .= "dbpwd=".$this->input->post('dbpwd')."\n";
                $txt .= "dbname=".$this->input->post('dbname')."\n";
                $txt .= "\n\n";
                $txt .= "#SMS to EMail"."\n";
                $txt .= "sms2email=".$this->input->post('sms2email')."\n";
                $txt .= "email_from=".$this->input->post('email_from')."\n";
                $txt .= "email_to=".$this->input->post('email_to')."\n";
                $txt .= "\n\n";
                //$txt .= "[client]"."\n";
                $txt .= "from=".$this->input->post('from')."\n";
                $txt .= "maxduplicate=".$this->input->post('maxduplicate')."\n";
                $txt .= "batchsize=".$this->input->post('batchsize')."\n";
                $txt .= "\n\n";
                fwrite($myfile, $txt);
                fclose($myfile);

                

                /**/
            }
            echo json_encode(array('st' => 0, 'msg' => 'Success! ini setting update success'));die();
            /*$this->session->set_flashdata('success','Settings Update successfully');
            redirect('setting/ini_settings');*/
        }
        else{
            $data = array(
                'loglevel' => form_error('loglevel'),
                'logpath' => form_error('logpath'),
                'pollinterval' => form_error('pollinterval'),
                'smpphost' => form_error('smpphost'),
                'port' => form_error('port'),
                'systemid' => form_error('systemid'),
                'password' => form_error('password'),
                'dbhost' => form_error('dbhost'),
                'dbuid' => form_error('dbuid'),
                'dbpwd' => form_error('dbpwd'),
                'dbname' => form_error('dbname'),
                'sms2email' => form_error('sms2email'),
                'email_from' => form_error('email_from'),
                'email_to' => form_error('email_to'),
                'from' => form_error('from'),
                'maxduplicate' => form_error('maxduplicate'),
                'batchsize' => form_error('batchsize'),
            );
                                
            echo json_encode(array('st' => 1, 'msg' => $data, 'pd' => $_POST));die();
        }

    }

       
        

    
        
}
