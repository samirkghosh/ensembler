<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Customsms {

    private $_CI;
    private $URL = ""; //your AUTH_KEY here
    private $AUTH_KEY = ""; //your AUTH_KEY here
    private $username = ""; //your senderId here
    private $password = ""; //your senderId here
    private $senderId = ""; //your senderId here
    private $routeId = ""; //your routeId here
    private $smsContentType = ""; //your smsContentType here

    function __construct() {
        $this->_CI = & get_instance();
        $this->_CI->load->model('smsconfig_model');

        // $this->session_name = $this->_CI->setting_model->getCurrentSessionName();
        $sms_sett =  $this->_CI->smsconfig_model->getActiveSMS();
        log_message('error', 'SMS SETTING ');
        log_message('error', json_encode($sms_sett));
        log_message('error', $sms_sett->type);
        //{"id":"2","type":"custom","name":"BIPA GATEWAY","api_id":"","authkey":"","senderid":"0","contact":"SMS:TEXT","username":"BIPA_Test","url":"http:\/\/41.205.135.19:9501\/api?action=","password":"BIPA$321","is_active":"enabled","created_at":"2021-05-31 19:07:36"}
 
        $this->URL = $sms_sett->url; //your routeId here
        $this->AUTH_KEY = $sms_sett->authkey; //your AUTH_KEY here
        $this->username = $sms_sett->username; //your senderId here
        $this->password = $sms_sett->password; //your senderId here
        $this->senderId = $sms_sett->senderid; //your senderId here
        $this->smsContentType = $sms_sett->contact; //your smsContentType here
       


    }

    function sendSMS($to, $message, $action ='') {
        

        // $content = 'AUTH_KEY=' . rawurlencode($this->AUTH_KEY) .
        // '&senderId=' . rawurlencode($this->senderId) .
        // '&routeId=' . rawurlencode($this->routeId) .
        
        $content = '&recipient=' . rawurlencode($to) . '&messagetype=' . $this->smsContentType.'&messagedata=' . $message ;

        if(!empty($action)){
            $base_url = $this->URL.$action.'&username='.$this->username.'&password='.$this->username.$content;
        }
        else{
            $base_url = $this->URL.'&username='.$this->username.'&password='.$this->username.$content;
        }

         log_message('error', 'SMS SENDING ');
        log_message('error', 'base_url');
        log_message('error', $base_url);
                
        // $ch = curl_init('http://41.205.135.19:9501/api?action='.$url.$content);
        $ch = curl_init($base_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        log_message('error', json_encode($response));
        curl_close($ch);
        return $response;
        return $response ='{"status":"false", "message":"action not passed"}';
        
    }

}
?>