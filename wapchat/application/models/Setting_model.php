<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Setting_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_active_sms_details($value=''){
        $result = $this->db->get_where('sms_config', array('is_active' => 'enabled'));
        return $result->row_array();
    }


    public function update_settings($value=''){
        
        // whatsapp Settings
        if($this->input->post('wa_channel_id') !='' ){
            $this->db->where('key', 'wa_channel_id');
            $this->db->update('system_settings', array('value' => $this->input->post('wa_channel_id')));
        }

        if($this->input->post('wa_live_key') !='' ){
            $this->db->where('key', 'wa_live_key');
            $this->db->update('system_settings', array('value' => $this->input->post('wa_live_key')));
        }

        // Imap Settings
        if($this->input->post('imap_host') !='' ){
            $this->db->where('key', 'imap_host');
            $this->db->update('system_settings', array('value' => $this->input->post('imap_host')));
        }

        if($this->input->post('imap_email') !='' ){
            $this->db->where('key', 'imap_email');
            $this->db->update('system_settings', array('value' => $this->input->post('imap_email')));
        }

        if($this->input->post('imap_pass') !='' ){
            $this->db->where('key', 'imap_pass');
            $this->db->update('system_settings', array('value' => $this->input->post('imap_pass')));
        }

        // SMTP Settings
        if($this->input->post('smtp_host') !='' ){
            $this->db->where('key', 'smtp_host');
            $this->db->update('system_settings', array('value' => $this->input->post('smtp_host')));
        }

        if($this->input->post('from_email') !='' ){
            $this->db->where('key', 'from_email');
            $this->db->update('system_settings', array('value' => $this->input->post('from_email')));
        }

        if($this->input->post('smtp_password') !='' ){
            $this->db->where('key', 'smtp_password');
            $this->db->update('system_settings', array('value' => $this->input->post('smtp_password')));
        }

        if($this->input->post('port') !='' ){
            $this->db->where('key', 'port');
            $this->db->update('system_settings', array('value' => $this->input->post('port')));
        }

        if($this->input->post('encryption') !='' ){
            $this->db->where('key', 'encryption');
            $this->db->update('system_settings', array('value' => $this->input->post('encryption')));
        }
    }

    public function get_previous_info($type){
        
            //$this->db->select('key', 'value');
        if($type=='whatsapp'){
            $this->db->where('key','wa_channel_id');
            $this->db->or_where('key','wa_live_key');
        }
        else if($type=='imap'){
            $this->db->where('key','imap_host');
            $this->db->or_where('key','imap_user');
            $this->db->or_where('key','imap_pass');
        }
        else if($type=='smtp'){
            $this->db->where('key','smtp_host');
            $this->db->or_where('key','from_email');
            $this->db->or_where('key','smtp_password');
            $this->db->or_where('key','port');
            $this->db->or_where('key','encryption');
        }
        $query = $this->db->get('system_settings');
        log_message('error', 'system_settings');
        log_message('error', $this->db->last_query());
        return $query->result_array();

    }

    public function settings_audit($previous, $new){
        $arr = array(
            'previous_data' => json_encode($previous),
            'new_data' =>   json_encode($new),
            'user_id' =>   $this->session->userdata('admin')['id'],
        );
        $this->db->insert('setting_audit', $arr);
    }






    /*public function get($id = null) {
        $this->db->select()->from('sms_config');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result();
        }
    }

    public function changeStatus($type) {
        $data = array('is_active' => 'disabled');
        $this->db->where('type !=', $type);
        $this->db->update('sms_config', $data);
    }

    public function add($data) {
        $this->db->where('type', $data['type']);
        $q = $this->db->get('sms_config');

        if ($q->num_rows() > 0) {
            $this->db->where('type', $data['type']);
            $this->db->update('sms_config', $data);
        } else {
            $this->db->insert('sms_config', $data);
        }
        if ($data['is_active'] == "enabled") {
            $this->changeStatus($data['type']);
        }
    }

    public function getActiveSMS() {
        $this->db->select()->from('sms_config');
        $this->db->where('is_active', 'enabled');
        $query = $this->db->get();
        return $query->row();
    }*/

}
