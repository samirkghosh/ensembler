<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Smsconfig extends Admin_Controller {

    function __construct() {
        parent::__construct();
         $this->load->library('customsms');

    }

    function index() {
        // if (!$this->rbac->hasPrivilege('sms_setting', 'can_edit')) {
        //     access_denied();
        // }
        //$this->session->set_userdata('top_menu', 'System Settings');
        //$this->session->set_userdata('sub_menu', 'smsconfig/index');
        $data['title'] = 'SMS Config || BIPA';
        $data['page_title'] = 'SMS Configuration';
        $data['breadcrumb'] = 'SMS Config';
        $sms_result = $this->smsconfig_model->get();
        
        $data['statuslist'] = $this->customlib->getStatus();
        $data['smslist'] = $sms_result;
       
        $this->load->view('smsconfig/smsList', $data);
         
    }

    public function check_send_sms()
    {
        $this->customsms->sendSMS('7869031599', 'Hello Wonder Mushonga', 'sendmessage');
       // $res = $this->customsms->sendSMS('7869031599', 'Hello Wonder Mushonga', 'sendmessage');
       // print_r($res);
    }


    public function custom() {

        $this->form_validation->set_error_delimiters('', '');

        $this->form_validation->set_rules('name', 'Gateway Name', 'required|trim');
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        $this->form_validation->set_rules('url', 'URL', 'required');
        $this->form_validation->set_rules('sender_id', 'Sender Id', 'required');


        if ($this->form_validation->run()) {

            $data = array(
                'type' => 'custom',
                'name' => $this->input->post('name'),
                'username' => $this->input->post('username'),
                'password' => $this->input->post('password'),
                'url' => $this->input->post('url'),
                'senderid' => $this->input->post('sender_id'),
                'contact' => $this->input->post('content_type'),
                'is_active' => $this->input->post('custom_status')
            );
            $this->smsconfig_model->add($data);
            echo json_encode(array('st' => 0, 'msg' => "Record updated successfully"));
        } else {

            $data = array(
                'name' => form_error('name'),
                'username' => form_error('username'),
                'password' => form_error('password'),
                'url' => form_error('url'),
                'sender_id' => form_error('sender_id'),
                'content_type' => form_error('content_type'),
            );

            echo json_encode(array('st' => 1, 'msg' => $data));
        }
    }

    


}

?>