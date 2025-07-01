<?php

/**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_post()
    {
        //$input = $this->input->post();
        $data = $this->db->get("tbl_user")->result();
        $d= json_decode(file_get_contents('php://input'));
        //ProductName
        $n = $d->ProductName ;
         $this->response(array('name' => $n), REST_Controller::HTTP_OK);
        //$this->db->insert('items',$input);
     
        $this->response(['Item created successfully.'], REST_Controller::HTTP_OK);
    } 
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_put($id)
    {
        $input = $this->put();
        $this->db->update('items', $input, array('id'=>$id));
     
        $this->response(['Item updated successfully.'], REST_Controller::HTTP_OK);
    }
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_delete($id)
    {
        $this->db->delete('items', array('id'=>$id));
       
        $this->response(['Item deleted successfully.'], REST_Controller::HTTP_OK);
    }

    public function image_Files_Upload_post($value='')
    {
        
        // $post = json_decode(file_get_contents('php://input'));

       //echo json_encode(array('error' => "fail", "message" => "please select image file", "fi" => $_FILES, "fi2" => $_POST ));die();  
        $target_dir = "uploads/user_data/";
        $user_id = $_POST['user_id'];
        if(isset($_FILES['pan_file']) && isset($_FILES['pan_file']['name']) && !empty($_FILES['pan_file']['name'] ) ){
            $errors= array();

            $file_name  =   $_FILES['pan_file']['name'];
            $file_size  =   $_FILES['pan_file']['size'];
            $file_tmp   =   $_FILES['pan_file']['tmp_name'];
            $file_type  =   $_FILES['pan_file']['type'];
            // $file_ext   =   strtolower(end(explode('.',$file_name)));
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

            $extensions= array("jpeg","jpg","png");

            if(in_array($file_ext, $extensions)=== false){
                $errors[]="extension not allowed, please choose a JPEG or PNG file.";
            }

            $target_file = $target_dir .$file_name ;
            if(empty($errors)==true){
                $image_path = base_url().$target_file;
                move_uploaded_file($file_tmp, $target_file);

               
                $insert_array = array('pan_image' => $image_path);
                $this->db->where('id' , $user_id);
                $this->db->update('tbl_team_member',$insert_array);

                echo json_encode(array('error' => "success", "message" => "file upload successfully", 'file_type' => 'pan', 'path' => $image_path ));die(); 
            }
            else{
                echo json_encode(array('error' => "fail", "message" => $errors ));die(); 
            }
        }

        if(isset($_FILES['aadhar_file']) && isset($_FILES['aadhar_file']['name']) && !empty($_FILES['aadhar_file']['name']) ){
            $errors= array();
            $file_name  =   $_FILES['aadhar_file']['name'];
            $file_size  =   $_FILES['aadhar_file']['size'];
            $file_tmp   =   $_FILES['aadhar_file']['tmp_name'];
            $file_type  =   $_FILES['aadhar_file']['type'];
            // $file_ext   =   strtolower(end(explode('.',$file_name)));
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

            $extensions= array("jpeg","jpg","png");

            if(in_array($file_ext, $extensions)=== false){
                $errors[]="extension not allowed, please choose a JPEG or PNG file.";
            }
            
             $target_file = $target_dir .$file_name ;
            if(empty($errors)==true){
                $image_path = base_url().$target_file;
                move_uploaded_file($file_tmp, $target_file);

                 $insert_array = array('aadhar_image' => $image_path);
             
                $this->db->where('id' , $user_id);
                $this->db->update('tbl_team_member',$insert_array);

                echo json_encode(array('error' => "success", "message" => "file upload successfully", 'file_type' => 'aadhar', 'path' => $image_path ));die(); 
            }
            else{
                echo json_encode(array('error' => "fail", "message" => $errors ));die(); 
            }
        }

        if(isset($_FILES['salary_file']) && isset($_FILES['salary_file']['name']) && !empty($_FILES['salary_file']['name'] ) ) {
            $errors= array();
            $file_name  =   $_FILES['salary_file']['name'];
            $file_size  =   $_FILES['salary_file']['size'];
            $file_tmp   =   $_FILES['salary_file']['tmp_name'];
            $file_type  =   $_FILES['salary_file']['type'];
            // $file_ext   =   strtolower(end(explode('.',$file_name)));
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

            $extensions= array("jpeg","jpg","png");

            if(in_array($file_ext, $extensions)=== false){
                $errors[]="extension not allowed, please choose a JPEG or PNG file.";
            }
            $target_file = $target_dir .$file_name ;
            if(empty($errors)==true){
                $image_path = base_url().$target_file;
                move_uploaded_file($file_tmp, $target_file);

                 $insert_array = array('salaryslip_image' => $image_path);
             
                $this->db->where('id' , $user_id);
                $this->db->update('tbl_team_member',$insert_array);

                echo json_encode(array('error' => "success", "message" => "file upload successfully", 'file_type' => 'salary', 'path' => $image_path ));die(); 
            }
            else{
                echo json_encode(array('error' => "fail", "message" => $errors ));die(); 
            }
        }

        if(isset($_FILES['cheque_file']) && isset($_FILES['cheque_file']['name']) && !empty($_FILES['cheque_file']['name']) ) {
            $errors= array();
            $file_name  =   $_FILES['cheque_file']['name'];
            $file_size  =   $_FILES['cheque_file']['size'];
            $file_tmp   =   $_FILES['cheque_file']['tmp_name'];
            $file_type  =   $_FILES['cheque_file']['type'];
            // $file_ext   =   strtolower(end(explode('.',$file_name)));
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

            $extensions= array("jpeg","jpg","png");

            if(in_array($file_ext, $extensions)=== false){
                $errors[]="extension not allowed, please choose a JPEG or PNG file.";
            }
             $target_file = $target_dir .$file_name ;
            if(empty($errors)==true){
                $image_path = base_url().$target_file;
                move_uploaded_file($file_tmp, $target_file);

                 $insert_array = array('cancelcheque_image' => $image_path);
                $this->db->where('id' , $user_id);
                $this->db->update('tbl_team_member',$insert_array);

                echo json_encode(array('error' => "success", "message" => "file upload successfully", 'file_type' => 'cheque', 'path' => $image_path ));die(); 
            }
            else{
                echo json_encode(array('error' => "fail", "message" => $errors ));die(); 
            }
        }

           /* if($file_size > 2097152){
             $errors[]='File size must be excately 2 MB';
            }*/
       
       
    }

#######################################################################################################################################
    public function is_exist_merchant_post($value='')
    {
        $post = json_decode(file_get_contents('php://input'));
        //$post->mobile; 

        //$this->response(array('mobile 1' => $post->mobile), REST_Controller::HTTP_OK);
        
        $data = $this->db->get_where('tbl_merchants',['mobile' => $post->mobile] )->row_array();
        if(count($data) > 0)
            $this->response(['status' => 'fail', 'mer' => $data, 'flag' => '1', 'message'=>'this mobile no already exist.'], REST_Controller::HTTP_OK);
        else 
            $this->response(['status' => 'success', 'message'=>'ok go ahead.'], REST_Controller::HTTP_OK);
    }

    public function add_merchant_post($value='')
    {
        $post = json_decode(file_get_contents('php://input'));
        
        if(isset($post->merchant_id) && $post->merchant_id > 0 ){
            $insert_array = array(
                'name'      => $post->name, 
                'mobile'    => $post->mobile, 
                'email'     => $post->email, 
                'Address'   => $post->address, 
                'latitute'   => $post->latitite, 
                'longitute'   => $post->longitute, 

                'nstore'            => !empty($post->nstore) ? 'yes': 'no', 
                'loyalty'           => !empty($post->loyalty) ? 'yes': 'no', 
                'website'           => !empty($post->website) ? 'yes': 'no', 
                'mobileapp'         => !empty($post->mobileapp) ? 'yes': 'no', 
                'boombilling'       => !empty($post->boombilling) ? 'yes': 'no', 
                'gprspayment'       => !empty($post->gprspayment) ? 'yes': 'no', 
                'pstnpayment'       => !empty($post->pstnpayment) ? 'yes': 'no',
                'mposmosambee'      => !empty($post->mposmosambee) ? 'yes': 'no', 
                'peperlessmpos'     => !empty($post->peperlessmpos) ? 'yes': 'no', 
                'reasone_for_no'    => $post->reasone_for_no, 
                'mposwithprinter'   => !empty($post->mposwithprinter) ? 'yes': 'no', 
                'restaurantsbilling'   => !empty($post->restaurantsbilling) ? 'yes': 'no',  

                'shop_status'   => $post->shop_status, 
                'intrested_area'   => $post->intrestarea, 
                'followup_date'   => date('Y-m-d' , strtotime($post->followupdate)), 
                'member_id'   => $post->merchant_id, 
                'remark'   => $post->remark, 
            );
             
            $this->db->where('id', $post->merchant_id);
            $this->db->update('tbl_merchants',$insert_array);
        }
        else{
            $insert_array = array(
                'name'      => $post->name, 
                'mobile'    => $post->Mobile, 
                'email'     => $post->email, 
                'Address'   => $post->address, 

                'shop_name'   => $post->shop_name, 
                'designation'   => $post->designation, 
                'businessCat'   => $post->businessCat,

                'nstore'            => !empty($post->nstore) ? 'yes': 'no', 
                'loyalty'           => !empty($post->loyalty) ? 'yes': 'no', 
                'website'           => !empty($post->website) ? 'yes': 'no', 
                'mobileapp'         => !empty($post->mobileapp) ? 'yes': 'no', 
                'boombilling'       => !empty($post->boombilling) ? 'yes': 'no', 
                'gprspayment'       => !empty($post->gprspayment) ? 'yes': 'no', 
                'pstnpayment'       => !empty($post->pstnpayment) ? 'yes': 'no',
                'mposmosambee'      => !empty($post->mposmosambee) ? 'yes': 'no', 
                'peperlessmpos'     => !empty($post->peperlessmpos) ? 'yes': 'no', 
                'reasone_for_no'    => $post->reasone_for_no, 
                'mposwithprinter'   => !empty($post->mposwithprinter) ? 'yes': 'no', 
                'restaurantsbilling'   => !empty($post->restaurantsbilling) ? 'yes': 'no', 

                'shop_status'   => $post->shop_status, 
                'intrested_area'   => $post->intrestarea, 
                'followup_date'   => date('Y-m-d' , strtotime($post->followupdate)), 
                'member_id'   => $post->user_id, 
                'remark'   => $post->remark, 
            );
            // $this->response($insert_array, REST_Controller::HTTP_OK);
            $this->db->insert('tbl_merchants',$insert_array);

        }
        
        $this->response(['status' => 'success', 'message'=>'Merchant created successfully.'], REST_Controller::HTTP_OK);
    }

    public function merchantList_post($value='')
    {
        $post = json_decode(file_get_contents('php://input'));
        
        if(isset($post->followup) && $post->followup =='1'){
            $data = $this->db->get_where("tbl_merchants", ['status' => '1', 'member_id' => $post->user_id, 'followup_date >=' => date('Y-m-d')." 00:00:00", 'followup_date <=' => date('Y-m-d 23:59:59', strtotime('+1day')) ])->result_array();
        }
        else{
         $data = $this->db->get_where("tbl_merchants", ['status' => '1', 'member_id' => $post->user_id, 'created_at >=' => date('Y-m-d')." 00:00:00" ])->result_array();   
        }


        if(count($data) > 0){
            $this->response(['status' => 'success', 'list' => $data,   'message'=>'Merchant created successfully.'], REST_Controller::HTTP_OK);
        }
        else{
            $this->response(['status' => 'fail',   'message'=>'Merchant Not Found.'], REST_Controller::HTTP_OK);
            //'query' => $this->db->last_query(),
        }
        
    }

    public function merchant_detail_post($value='')
    {
        $post = json_decode(file_get_contents('php://input')); 
        $post->merchant_id;
        $post->user_id ;
        $data = $this->db->get_where("tbl_merchants", ['status' => '1', 'id' => $post->merchant_id ])->row_array();   

        if(count($data) > 0){
            $this->response(['status' => 'success', 'merchant' => $data, 'message'=>'Merchant List successfully.'], REST_Controller::HTTP_OK);
        }
        else{
            $this->response(['status' => 'fail', 'message'=>'Merchant Not Found.'], REST_Controller::HTTP_OK);

        }
        
    }

    public function login_post($value='')
    {
        $post = json_decode(file_get_contents('php://input'));
        $post->mobile;
        $data = $this->db->get_where("tbl_team_member", ['phone' => $post->mobile, 'status' => '1'])->row_array();
        if($data !=null ){
            $this->response(['status' => 'success', 'user' => $data, 'message'=>'Login successfully .'], REST_Controller::HTTP_OK);
        }
        else{
            $this->response(['status' => 'fail', 'message'=>'Your Are not a valid user.'], REST_Controller::HTTP_OK);

        }
        //$this->db->insert('tbl_merchants',$insert_array);
    }

    public function userdata_post()
    {
        $post = json_decode(file_get_contents('php://input'));
        $post->login_id ;

        $data = $this->db->get_where("tbl_team_member", array('id' =>$post->login_id ))->row_array();
        
        $this->response(['status' => 'success', 'users' => $data, 'message'=>'User found.'], REST_Controller::HTTP_OK);
    }

    public function profile_update_post()
    {
        $post = json_decode(file_get_contents('php://input'));

        if(isset($post->updateslug) && $post->updateslug =='0'){
            $insert_array = array(
                'name'      => $post->name,  
                'email'     => $post->email,
            );
        }
        else{
            $insert_array = array(
                'name'          => $post->name,  
                'email'         => $post->email, 
                'bank_name'     => $post->bank_name, 
                'account_no'    => $post->account_no, 
                'ifsc_code'     => $post->ifsc_code,
                'pan_no'        => $post->pan_no, 
                'adhar_card'    => $post->adhar_card, 
                'registered_name'   => $post->registered_name,
                'user_update_flag'   => '0'
            );    
        }
         //$this->response($post, REST_Controller::HTTP_OK);
        $this->db->where('id' , $post->admin_id);
        $this->db->update('tbl_team_member',$insert_array);
        $rr = $this->db->last_query();
        
        $id = $this->db->affected_rows();
         
        $data = $this->db->get_where("tbl_team_member", ['phone' => $post->mobile])->row_array();

        if($id > 0)
            $this->response(['status' => 'success', 'user' => $data, 'message'=>'Profile Update Successfully'], REST_Controller::HTTP_OK);
        else    
            $this->response(['status' => 'fail', 'd' => $id , 'message'=>'No Change in data'], REST_Controller::HTTP_OK);
        
        
    }

?>