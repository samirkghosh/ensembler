<?php
   
require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;
     
class Api extends REST_Controller {

    private $api_key ='e10adc3949ba59abbe56e057f20f883e' ;
    
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function __construct() {
       
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == "OPTIONS") {
            die();
        }
        parent::__construct();
        $this->load->database();
        $this->load->model('Auth_model');   
        // $this->load->helper('common'); 


    }


    function test_input($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }

    // 21-08-2021 :: Validate And Sanetize number
    function validate_mobile_number($string){
        //eliminate every char except 0-9
        $justNums = preg_replace("/[^0-9]/", '', $string);

        if(strlen($justNums) < 12) return false;
        if(strlen($justNums) > 12) return false;
                
        // check country code.
        if(strlen($justNums) == '12' ){
            if(substr($justNums, 0, 3) != '264') return false;
        }
        return true ;
    } 


    protected function check_isint($value){
        if(is_int($value))
            return true;
        else
            return false;


    }
    #########################################################################################
    #########################################################################################


       
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_get($id = 0){
        log_message('error', 'REQUEST_METHOD '. $_SERVER['REQUEST_METHOD'] );
        if($_SERVER['REQUEST_METHOD']=='GET'){
            if(!empty($id)){
                $data = $this->db->get_where("users", ['id' => $id])->result_array();
            }
            else{
                $data = $this->db->get("users")->result_array();
            }
            $this->response($data, REST_Controller::HTTP_OK);
        }
    }


    public function check_access_key($access_token_key){
        if($access_token_key != $this->api_key)
            return false;
        else
            return true;
    }



    public function check_mandatry($required_array = [], $request_param){
        $error_flag = false ;
        $error = '';
        $error .= 'Required parament is missing or empty is ';
        // log_message('error', json_encode($request_param) );
       

        foreach($required_array as $key => $val){
            // if(!array_key_exists($val,$request_param) || empty($request_param[$val])){
            if(!isset($val) || empty($request_param[$val])){
                $error .= $val .',' ;
                $error_flag = true; 
            }
        }

        if($error_flag){
            return $error ;
        }
        return false ;
    }


                   



    /**
     * use to push new sms data from bipa server to our system.
     * and store in our INQUEUE 
     * @return Response
    */
    public function push_new_message_post(){            
        //log_message('error', 'REQUEST_METHOD '. $_SERVER['REQUEST_METHOD'] );
        if($_SERVER['REQUEST_METHOD']=='POST'){
            log_message('error', json_encode($_POST));

            if(empty($this->input->post('access_token'))){
                $data = ['status' => 'fail', 'message' => 'access_token is required'] ;
                $this->response($data, REST_Controller::HTTP_OK);
            }
            if($this->check_access_key($this->input->post('access_token')) == false){
                $data = ['status' => 'fail', 'message' => 'invalid access_token'] ;
                $this->response($data, REST_Controller::HTTP_OK);
            }



            // check mandatory fileds 
            $res = $this->check_mandatry(array('sms_to', 'sms_from', 'message'), $_POST);
            if($res != false){
                $data = ['status' => 'fail', 'message' => $res] ;
                    $this->response($data, REST_Controller::HTTP_OK);
            }

            // validate phone number.
            if($this->validate_mobile_number($this->input->post('sms_to')) == false ){
                $data = ['status' => 'fail', 'message' => 'Invalid phone no.'] ;
                    $this->response($data, REST_Controller::HTTP_OK);
            } 

            // schedule and not schedule flag  0 = schedule , 1= not schedule   
            if($this->input->post('schedule_flag') !='0' && $this->input->post('schedule_flag') !='1' ){
                $data = ['status' => 'fail', 'message' => 'schedule flag is required'] ;
                $this->response($data, REST_Controller::HTTP_OK);
            }
            if($this->input->post('schedule_flag')=='0'){
                if(empty($this->input->post('schedule_time'))){
                    $data = ['status' => 'fail', 'message' => 'schedule time is required'] ;
                    $this->response($data, REST_Controller::HTTP_OK);       
                }
            }
            
            $data['send_to'] = $this->test_input($this->input->post('sms_to'));
            $data['send_from'] = $this->test_input($this->input->post('sms_from'));             

            $data['message'] = $this->test_input($this->input->post('message'));
            $data['message_type_flag'] = 2;		
            $data['status'] = 0;
            $data['created_by'] = 1;
            $data['schedule_flag'] = $this->test_input($this->input->post('schedule_flag'));
            $data['schedule_time'] = $this->input->post('schedule_flag') =='0' ? $this->input->post('schedule_time'): date('Y-m-d H:i') ;
            
            // input name param 
            $name = $this->input->post('name');
            if(!empty($name) && !$this->Auth_model->is_customer_exist($this->input->post('sms_to')) ){
                $this->db->insert('contact', array('first_name' => $name, 'mobile_no' => $data['send_to'] ));
                log_message('error', 'API CONTACT SAVE');
                log_message('error', $this->db->last_query());
            }

            $response = $this->db->insert('sms_out_queue', $data);
            if($response)
                $data = ['status' => 'success', 'message' => 'Record save successfully'] ;
            else
                $data = ['status' => 'fail', 'message' => 'Record not save, please try after some time.'] ;
    
            
            $this->response($data, REST_Controller::HTTP_OK);
        }
        $data = ['status' => 'fail', 'message' => 'Request method not allowed'] ;
        $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
    }



    /**
     * reterive incoming sms from sms_in_queue
     * base on status 0 => not read by bipa and 1 => read by bipa ( can never read again )  
     * @return Response
    */
    public function insms_get(){            
        //log_message('error', 'REQUEST_METHOD '. $_SERVER['REQUEST_METHOD'] );
        if($_SERVER['REQUEST_METHOD']=='GET'){
            log_message('error', json_encode($_GET));

            // check acces token is not empty
            if(empty($this->input->get('access_token'))){
                $data = ['status' => 'fail', 'message' => 'access_token is required'] ;
                $this->response($data, REST_Controller::HTTP_OK);
            }

            // check access is valid 
            if($this->check_access_key($this->input->get('access_token')) == false){
                $data = ['status' => 'fail', 'message' => 'invalid access_token'] ;
                $this->response($data, REST_Controller::HTTP_OK);
            }            

            $in_sms = $this->api_model->getlast_insms();
            if(!empty($in_sms))
                $data = ['status' => 'success',  'message' => 'New in sms recieved.', 'data' =>$in_sms] ;
            else
                $data = ['status' => 'fail',  'message' => 'no more in sms'] ;

            $this->response($data, REST_Controller::HTTP_OK);
        }
        $data = ['status' => 'fail', 'message' => 'Request method not allowed'] ;
        $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
    }


    /**
     * update read sms by bipa as accoprdingly above.     
     * @return Response
    */
    public function insms_update_post(){            
        //log_message('error', 'REQUEST_METHOD '. $_SERVER['REQUEST_METHOD'] );
        if($_SERVER['REQUEST_METHOD']=='POST'){
            log_message('error', json_encode($_POST));

            // check acces token is not empty
            if(empty($this->input->post('access_token'))){
                $data = ['status' => 'fail', 'message' => 'access_token is required'] ;
                $this->response($data, REST_Controller::HTTP_OK);
            }

            // check access is valid 
            if($this->check_access_key($this->input->post('access_token')) == false){
                $data = ['status' => 'fail', 'message' => 'invalid access_token'] ;
                $this->response($data, REST_Controller::HTTP_OK);
            }


            // check mandatory fileds 
            $res = $this->check_mandatry(array('in_sms_id'), $_POST);
            if($res != false){
                $data = ['status' => 'fail', 'message' => $res] ;
                $this->response($data, REST_Controller::HTTP_OK);
            }

            $insms_id = $this->input->post('in_sms_id');
            
            // check int
            if(!is_numeric($insms_id)){ $this->response(['status' => 'fail', 'message' => 'invalid number'], REST_Controller::HTTP_OK); }

            // check in sms id exist or not
            $result = $this->api_model->is_insms_exist($insms_id) ;
            if($result != 'true'){
                $data = ['status' => 'fail', 'message' => $result] ;
                $this->response($data, REST_Controller::HTTP_OK);
            } 


            $in_sms = $this->api_model->update_insms($insms_id);
            if($in_sms)
                $data = ['status' => 'success',  'message' => 'Record update successfully', 'data' => $this->db->get_where('sms_in_queue', array('id'=>$insms_id))->row()] ;
            else
                $data = ['status' => 'fail',  'message' => 'Something went wrong in update.'] ;

            $this->response($data, REST_Controller::HTTP_OK);
        }
        $data = ['status' => 'fail', 'message' => 'Request method not allowed'] ;
        $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
    }

    
            

      
    

    
     
      
}