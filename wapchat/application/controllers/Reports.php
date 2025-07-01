<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
	
		$this->load->model('Auth_model');	
		$this->load->model('Model_Sms', 'sms_model');
		$this->load->library('excel');
		// print_r($this->session);	
	}

    /*
    *  Basic Report with Filters option For SMS and Whatsapp
    */
	public function index($value=''){
        if(!$this->isLoggedin())
            redirect('login', 'refresh');

		$data['title'] = "Basic Report || BIPA ";
        $data['breadcrumb'] = "Message Report";
		$data['sms_reports'] = $this->report_model->reports();
         $data['lists'] =  $this->sms_model->get_bulk_upload_list();
         $data['users'] =  $this->user_model->getUsers();

        //  Filter option 
        $data['report_name'] =  !empty($this->input->post('report_name')) ? $this->input->post('report_name') :'sms' ; 
        $data['report_in_out'] =  !empty($this->input->post('report_in_out')) ? $this->input->post('report_in_out') :'out'; 
        $data['from_date']     = !empty($this->input->post('from_date')) ? yyyymmdd_date($this->input->post('from_date')) : date('d-m-Y 00:00', strtotime('now'));
        $data['end_date']      = !empty($this->input->post('end_date')) ? yyyymmdd_date($this->input->post('end_date')) : date('d-m-Y 23:59', strtotime('now')); 
        $data['schedule'] =  $this->input->post('schedule'); 
        $data['message_type'] =  $this->input->post('message_type'); 
        $data['status'] =  $this->input->post('status'); 
        $data['list_wise'] =  $this->input->post('list_wise'); 
        $data['user_wise'] = $this->input->post('user_wise');

		$this->load->view('reports/index',$data);

	}

    /*
    *  Report For Bulk Messages   
    */
    public function bulk_message_report($value=''){
        if(!$this->isLoggedin())
            redirect('login', 'refresh');


        $data['title'] = "Bulk Message Report || BIPA";
        $data['breadcrumb'] = "Bulk Message Report";
        
        $data['sms_reports'] = $this->report_model->reports();
        $data['bulk_lists'] =  $this->sms_model->get_bulk_upload_list();
        $data['users'] =  $this->user_model->getUsers();

        $data['report_name'] =  !empty($this->input->post('report_name')) ? $this->input->post('report_name') :'sms' ; 
        $data['report_in_out'] =  !empty($this->input->post('report_in_out')) ? $this->input->post('report_in_out') :'out'; 
        $data['from_date']     = !empty($this->input->post('from_date')) ? yyyymmdd_date($this->input->post('from_date')) : date('d-m-Y 00:00', strtotime('now'));
        $data['end_date']      = !empty($this->input->post('end_date')) ? yyyymmdd_date($this->input->post('end_date')) : date('d-m-Y 23:59', strtotime('now')); 
        $data['schedule'] =  $this->input->post('schedule'); 
        $data['message_type'] =  $this->input->post('message_type'); 
        $data['status'] =  $this->input->post('status'); 
        $data['list_wise'] =  $this->input->post('list_wise'); 
        $data['user_wise'] = $this->input->post('user_wise');

        $this->load->view('reports/bulk_messages_report',$data);
    }



    public function customer_wise(){
        if(!$this->isLoggedin())
            redirect('login', 'refresh');
        
        $data['title'] = "Customer Report || BIPA";
        $data['breadcrumb'] = "Customer Report";
        
        $data['sms_reports'] = $this->report_model->reports();
        $data['bulk_lists'] =  $this->sms_model->get_bulk_upload_list();
        $data['users'] =  $this->user_model->getUsers();

        $data['channel'] =  !empty($this->input->post('report_name')) ? $this->input->post('report_name') :'all' ; 
        $data['report_in_out'] =  !empty($this->input->post('report_in_out')) ? $this->input->post('report_in_out') :'all'; 
        $data['from_date'] =  $this->input->post('from_date'); 
        $data['end_date'] =  $this->input->post('end_date'); 
        $data['mobile'] =  $this->input->post('mobile'); 
        $data['message_type'] =  $this->input->post('message_type'); 
        $data['status'] =  $this->input->post('status'); 
        /*$data['schedule'] =  $this->input->post('schedule'); 
        $data['list_wise'] =  $this->input->post('list_wise'); 
        $data['user_wise'] = $this->input->post('user_wise');*/

        $this->load->view('reports/customer',$data);
    }


    public function queue_report($value=''){
        if(!$this->isLoggedin())
            redirect('login', 'refresh');

        $data['title'] = "Queue Report || BIPA ";
        $data['breadcrumb'] = "Queue Report";
        $data['sms_reports'] = $this->report_model->reports();
         $data['lists'] =  $this->sms_model->get_bulk_upload_list();
         $data['users'] =  $this->user_model->getUsers();

        //  Filter option 
        $data['report_name'] =  !empty($this->input->post('report_name')) ? $this->input->post('report_name') :'sms' ; 
        $data['report_in_out'] =  !empty($this->input->post('report_in_out')) ? $this->input->post('report_in_out') :'out'; 
        $data['from_date']     = !empty($this->input->post('from_date')) ? yyyymmdd_date($this->input->post('from_date')) : date('d-m-Y 00:00', strtotime('now'));
        $data['end_date']      = !empty($this->input->post('end_date')) ? yyyymmdd_date($this->input->post('end_date')) : date('d-m-Y 23:59', strtotime('now')); 
        $data['schedule'] =  $this->input->post('schedule'); 
        $data['message_type'] =  $this->input->post('message_type'); 
        $data['status'] =  0; 
        $data['list_wise'] =  $this->input->post('list_wise'); 
        $data['user_wise'] = $this->input->post('user_wise')  ;

        $this->load->view('reports/queue_report',$data);

    }

    public function bad_report($value=''){
        if(!$this->isLoggedin())
            redirect('login', 'refresh');

        $data['title'] = "Bad Report || BIPA ";
        $data['breadcrumb'] = "Queue Report";
        $data['sms_reports'] = $this->report_model->reports();
        $data['lists'] =  $this->sms_model->get_bulk_upload_list();
        $data['users'] =  $this->user_model->getUsers();

        //  Filter option 
        $data['report_name'] =  !empty($this->input->post('report_name')) ? $this->input->post('report_name') :'sms' ; 
        $data['report_in_out'] =  !empty($this->input->post('report_in_out')) ? $this->input->post('report_in_out') :'out'; 
        $data['from_date']     = !empty($this->input->post('from_date')) ? yyyymmdd_date($this->input->post('from_date')) : date('d-m-Y 00:00', strtotime('now'));
        $data['end_date']      = !empty($this->input->post('end_date')) ? yyyymmdd_date($this->input->post('end_date')) : date('d-m-Y 23:59', strtotime('now')); 
        $data['schedule'] =  $this->input->post('schedule'); 
        $data['message_type'] =  $this->input->post('message_type'); 
        $data['status'] =  0; 
        $data['list_wise'] =  $this->input->post('list_wise'); 
        $data['user_wise'] = $this->input->post('user_wise')  ;

        $this->load->view('reports/bad_report',$data);

    }

	 
    // Vijay ::  Messages Report Data   
	public function get_basic_records($value=''){
		
        $report_data = array();

		// Filter portion
        $filter_data['report_name']   = $this->input->post('report_name');
        $filter_data['report_in_out'] = $this->input->post('report_in_out');
        $filter_data['from_date']     = yyyymmdd_date($this->input->post('from_date'));
        $filter_data['end_date']      = yyyymmdd_date($this->input->post('end_date'));
        $filter_data['schedule']      = $this->input->post('schedule');
        $filter_data['message_type']  = $this->input->post('message_type');
        $filter_data['status']        = $this->input->post('status');
        $filter_data['list_wise']     = $this->input->post('list_wise');
        $filter_data['user_wise']     = $this->input->post('user_wise');

        // Server side processing portion
        $columns = array(
            0 => '#',
            // 1 => 'send_from',
            1 => 'send_to',
            2 => 'message',
            3 => 'message_type_flag',
            4 => 'status',
            5 => 'scheduler_flag',
            6 => 'scheduled_time',
            7 => 'create_date',
            8 => 'status_response',
            9 => 'create_by',
            10 => 'id'
        );

        // Coming from databale itself. Limit is the visible number of data
        $limit = html_escape($this->input->post('length'));
        $start = html_escape($this->input->post('start'));
        $order = "";
        $dir   = $this->input->post('order')[0]['dir'];

        // $totalData = $this->lazyload->count_all_courses($filter_data);
        $totalData = $this->lazyload->count_all_data($filter_data);
        // if(empty($report_data)){
        //     $filter_data['last_hundreds'] = 'yes';
        //     $totalData = $this->lazyload->count_all_data($filter_data);
        // }

        $totalFiltered = $totalData;

        // This block of code is handling the search event of datatable
        if(empty($this->input->post('search')['value'])) {
            
            $report_data = $this->lazyload->reports($limit, $start, $order, $dir, $filter_data);
            // if(empty($report_data)){
            //     $limit ='100';
            //     $start = 0 ;
            //     $filter_data['last_hundreds'] = 'yes';
            //     $report_data = $this->lazyload->reports($limit, $start, $order, $dir, $filter_data);
            // }
            
        }
        else {
            
            $search = $this->input->post('search')['value'];
            $report_data =  $this->lazyload->reports_search($limit, $start, $search, $order, $dir, $filter_data);
            $totalFiltered = $this->lazyload->course_search_count($search);
             
        }

         // Fetch the data and make it as JSON format and return it.
        $data = array();
        //$report_data = $this->report_model->reports();
		if(!empty($report_data)) {
            foreach ($report_data as $key => $row) {

            if($filter_data['report_in_out'] =='out'){
                $user = $this->user_model->getUsers($row->created_by);
                $contact_name = $this->Auth_model->get_contact($row->send_to);
                $nestedData['#'] = $key+1;
                $nestedData['send_to'] =  $row->send_to ;
                $nestedData['name'] =  $contact_name;
                // $nestedData['message'] =  substr($row->message, 0,40)  ;
                $nestedData['message'] = '<a href="#" id="'.$row->message.'" onclick="show_full_message(this,'.$row->id.')" title="'.$row->message.'"> '.substr($row->message, 0,40).'</a>'   ;
                $nestedData['units'] =  $row->message_unit  ;



                $nestedData['message_type_flag'] =  $row->message_type_flag=='0'?'Direct':($row->message_type_flag=='1'?'Bulk':($row->message_type_flag=='2'?'App':'EmailtoSms') ) ;

                // $nestedData['message'] =  wordwrap($row->message,50,"<br>") ;
                 
                if($row->status =='0')
                    $status = 'QUEUE';
                else if($row->status =='1')
                    $status = 'SUBMITTED';
                else if($row->status =='2')
                    $status = 'DELIVERED';
                else if($row->status =='3')
                    $status = 'NOT DELIVERED';
                 
                // $yes_no = $row->scheduler_flag=='0'?'yes':'no' ;

                $nestedData['status'] = $status ;
                $yes_no = $row->schedule_flag=='0'?'<div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"><input type="checkbox" class="custom-control-input" checked disabled id="customSwitch3"><label class="custom-control-label" for="customSwitch3"></label></div>':'<div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"><input type="checkbox" disabled class="custom-control-input" id="customSwitch3"><label class="custom-control-label" for="customSwitch3"></label></div>' ;

                $small = ' <small>'.ddmmyyyy_date($row->schedule_time).'</small>';
                $nestedData['schedule'] = $row->schedule_flag=='0'?'<small class="badge badge-success"><i class="far fa-clock"></i> '.date("d-m-Y H:i", strtotime($row->schedule_time)).'</small>' : '';
                // $nestedData['scheduler_flag'] =  $yes_no ;
                $nestedData['create_date'] = ddmmyyyy_date($row->create_date);
                $nestedData['status_response'] = $row->status_response;
                $nestedData['create_by'] = isset($user->username)?$user->username:'API' ;
                $nestedData['id'] = $row->id;
                $data[] = $nestedData;
            }   
            else if($filter_data['report_in_out'] =='in'){
                $contact_name = $this->Auth_model->get_contact($row->send_from);

                $nestedData['#'] = $key+1;
                $nestedData['send_to'] =  $row->send_from ;
                $nestedData['name'] =  $contact_name;
                $nestedData['message'] =  substr($row->message, 0,40)  ;
                
                 // $nestedData['units'] =  5 ;  
                $nestedData['create_date'] = ddmmyyyy_date($row->create_date);
                $nestedData['id'] = $row->id;
                $data[] = $nestedData;
            } 
                 
                

            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);

	}


    // Vijay ::  Bulk Report Data 
    public function bulk_report_data($value='')
    {
        $report_data = array();

        // Filter portion
        $filter_data['report_name']   = $this->input->post('report_name');
        $filter_data['report_in_out'] = $this->input->post('report_in_out');
        $filter_data['from_date']     = yyyymmdd_date($this->input->post('from_date')) ;
        $filter_data['end_date']      = yyyymmdd_date($this->input->post('end_date')) ;
        $filter_data['schedule']      = $this->input->post('schedule');
        $filter_data['message_type']  = $this->input->post('message_type');
        $filter_data['status']        = $this->input->post('status');
        $filter_data['list_wise']     = $this->input->post('list_wise');
        $filter_data['user_wise']     = $this->input->post('user_wise');

        // Server side processing portion
        $columns = array(
            0 => '#',
            1 => 'create_date',
            2 => 'list_name',
            3 => 'total_count',
            4 => 'queue',
            5 => 'submitted',
            // 6 => 'pending',
            6 => 'delivered',
            7 => 'not_delivered',
            8 => 'message',
            9 => 'schedule_time',
            10 => 'schedule_flag',
            11 => 'create_by',
            12 => 'id'
        );

        // Coming from databale itself. Limit is the visible number of data
        $limit = html_escape($this->input->post('length'));
        $start = html_escape($this->input->post('start'));
        $order = "";
        $dir   = $this->input->post('order')[0]['dir'];

        // $totalData = $this->lazyload->count_all_courses($filter_data);
        $totalData = $this->lazy_bulk_model->count_all_data($filter_data);
        $totalFiltered = $totalData;

        // This block of code is handling the search event of datatable
        if(empty($this->input->post('search')['value'])) {
            
            $report_data = $this->lazy_bulk_model->reports($limit, $start, $order, $dir, $filter_data);
            
        }
        else {
            
            $search = $this->input->post('search')['value'];
            $report_data =  $this->lazy_bulk_model->reports_search($limit, $start, $search, $order, $dir, $filter_data);
            $totalFiltered = $this->lazy_bulk_model->course_search_count($search);
             
        }
         
         // Fetch the data and make it as JSON format and return it.
        $data = array();
        $list_status = array();
        //$report_data = $this->report_model->reports();
        $queue = $submitted = $pending = $Delivered = $notDelivered ='0';
        if(!empty($report_data)) {
            foreach ($report_data as $key => $row) {
                $user = $this->user_model->getUsers($row->created_by);
                $list_details = $this->report_model->get_list_details($row->list_id);
                $list_status = $this->report_model->get_status_of_list($row->out_queue_session);

                if($list_status !=null ):
                    foreach($list_status as $key =>$list ):
                        
                       if($list->status =='0')
                            $queue = $list->ct;
                        else if($list->status =='1')
                            $submitted = $list->ct;
                        else if($list->status =='2')
                            $Delivered = $list->ct;
                        else if($list->status =='3')
                            $notDelivered = $list->ct;
                    endforeach;     
                endif;     
                         

                $nestedData['#'] = $key+1;
                $nestedData['create_date']  = date('d-m-Y', strtotime($row->created_at));
                $nestedData['list_name']    =  $list_details->list_name ;
                $nestedData['total_count']  =  $row->total_count ;

                $nestedData['queue']        =  $queue ;
                $nestedData['submitted']    =  $submitted ;
                // $nestedData['pending']      =  $pending ;
                $nestedData['delivered']    =  $Delivered ;
                $nestedData['not_delivered'] =  $notDelivered ;
                // $nestedData['message'] =  substr($row->message, 0,40)  ;
                
                
                //$nestedData['message'] = '<div class="tooltip" style="display:contents">Hover over me<span class="tooltiptext">Tooltip text</span></div>'   ;
                //$nestedData['message'] = '<a href="#" id="'.$row->message.'"  onclick="show_full_message(this,'.$row->send_from.','.$row->id.')">'.substr($row->message, 0,40).'</a>'  ;

                  $nestedData['message'] = '<a href="#" id="'.$row->message.'" onclick="show_full_message(this,'.$row->id.')" title="'.$row->message.'"> '.substr($row->message, 0,40).'</a>'   ;
                // $nestedData['message_type_flag'] =  $row->message_type_flag=='0'?'direct':($row->message_type_flag=='1'?'bulk':'application') ;

                
              


                // $nestedData['status'] = $row->status=='0'?'queue':'sent';;
                // $yes_no = $row->schedule_flag=='0'?'yes':'no' ;
                $yes_no = $row->schedule_flag=='0'?'<div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"><input type="checkbox" class="custom-control-input" checked disabled id="customSwitch3"><label class="custom-control-label" for="customSwitch3"></label></div>':'<div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"><input type="checkbox" disabled class="custom-control-input" id="customSwitch3"><label class="custom-control-label" for="customSwitch3"></label></div>' ;
                $small = ' <small>'.date('d-m-Y H:i', strtotime($row->schedule_time)).'</small>';

                



                $nestedData['schedule_time'] = date('d-m-Y H:i', strtotime($row->schedule_time));
                $nestedData['schedule_flag'] =  $yes_no ;
                $nestedData['create_by'] = $user->username;
                $nestedData['id'] = $row->id;
                $data[] = $nestedData;
                //$nestedData['status_response'] = $row->status_response;


            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);

    }

   
    
    /* ommented on 21-09-2021
    //Vijay ::  Customer details data 
    public function customer_wise_data($value='')
    {
        $report_data = array();

        // Filter portion
        $filter_data['channel']   = $this->input->post('channel');
        $filter_data['report_in_out'] = $this->input->post('report_in_out');
        $filter_data['from_date']         = $this->input->post('from_date');
        $filter_data['end_date']         = $this->input->post('end_date');
        $filter_data['mobile'] =  $this->input->post('mobile'); 
        $filter_data['message_type'] =  $this->input->post('message_type'); 
        $filter_data['status'] =  $this->input->post('status'); 

        // Server side processing portion
        $columns = array(
            0 => '#',
            1 => 'date',
            2 => 'channel',
            3 => 'type',
            4 => 'message',
            5 => 'status',
            6 => 'created_by',
            7 => 'id'
        );

        // Coming from databale itself. Limit is the visible number of data
        $limit = html_escape($this->input->post('length'));
        $start = html_escape($this->input->post('start'));
        $order = "";
        $dir   = $this->input->post('order')[0]['dir'];

        // $totalData = $this->lazyload->count_all_courses($filter_data);
        $totalData = $totalData_s = $totalData_w = 0 ;
        if(!empty($this->input->post('mobile'))){
            if ($filter_data['channel'] == 'sms' ) {
                $totalData_s = $this->lazyload_customer->count_all_data_sms($filter_data);
            }
            if ($filter_data['channel'] == 'whatsapp' ) {
                $totalData_w = $this->lazyload_customer->count_all_data_whatspp($filter_data);
            }
            if ($filter_data['channel'] == 'all' ) {
                $totalData_s = $this->lazyload_customer->count_all_data_sms($filter_data);
                $totalData_w = $this->lazyload_customer->count_all_data_whatspp($filter_data);
            }

            $totalData = $totalData_s+$totalData_w;
        }
        $totalFiltered = $totalData;


        // This block of code is handling the search event of datatable
        if(empty($this->input->post('search')['value'])) {
            if(!empty($this->input->post('mobile'))){
                if ($filter_data['channel'] == 'sms' ) {
                    $report_data = $this->lazyload_customer->reports_sms($limit, $start, $order, $dir, $filter_data);
                }
                if ($filter_data['channel'] == 'whatsapp' ) {
                    $report_data = $this->lazyload_customer->reports_whatspp($limit, $start, $order, $dir, $filter_data);
                }
                if ($filter_data['channel'] == 'all' ) {
                    if($totalData_s > 0){
                        $report_data = $this->lazyload_customer->reports_sms($limit, $start, $order, $dir, $filter_data);
                    }

                    if($totalData_s == 0 && $totalData_w > 0){
                        $report_data = $this->lazyload_customer->reports_whatspp($limit, $start, $order, $dir, $filter_data);
                    }
                    if($totalData_s > 0 && $totalData_w > 0){
                       $report_data_s = $this->lazyload_customer->reports_sms($limit, $start, $order, $dir, $filter_data);
                       $report_data_w = $this->lazyload_customer->reports_whatspp($limit, $start, $order, $dir, $filter_data);
                       $report_data =$this->array_sort(array_merge($report_data_s, $report_data_s), 'create_date', 'SORT_DESC');
                    }
                }

                // $totalData = $totalData_s+$totalData_w;
            }
             
                
            
        }
        else {
            
            // $search = $this->input->post('search')['value'];
            // $report_data =  $this->lazyload_customer->reports_search($limit, $start, $search, $order, $dir, $filter_data);
            // $totalFiltered = $this->lazyload_customer->course_search_count($search);
             
        }
         
         // Fetch the data and make it as JSON format and return it.
        $data = array();
        if(!empty($report_data)) {
            foreach ($report_data as $key => $row) {
                 // $user = $this->user_model->getUsers($row->created_by);

              
                  // if($row->status =='0')
                   // $status = 'QUEUE';
                //else if($row->status =='1')
                  //  $status = 'SUBMITTED';
               // else if($row->status =='2')
                  //  $status = 'DELIVRD';
                //else if($row->status =='3')
                   // $status = 'NOT DELIVRD';

                $nestedData['#'] = $key+1;
                $nestedData['date'] =  date('d-m-Y H:i:s', strtotime($row->create_date)); ;
                $nestedData['channel'] =  $row->channel_type =='0' ? 'sms' :'whatsapp' ;
                 $nestedData['type'] =  '' ; 
                $nestedData['status'] = '';
                $nestedData['message'] =  substr($row->message, 0,40)  ;
                $nestedData['create_by'] = '';

                // $nestedData['status_response'] = $row->status_response;

                $nestedData['id'] = $row->id;

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);

    }

    */

   


    // Vijay ::  Customer details data 
    public function customer_wise_data($value='')
    {
        $report_data = array();

        // Filter portion
        $filter_data['channel']   = $this->input->post('channel');
        $filter_data['report_in_out'] = $this->input->post('report_in_out');
        $filter_data['from_date']         = $this->input->post('from_date');
        $filter_data['end_date']         = $this->input->post('end_date');
        $filter_data['mobile'] =  $this->input->post('mobile'); 
        $filter_data['message_type'] =  $this->input->post('message_type'); 
        $filter_data['status'] =  $this->input->post('status'); 

        // Server side processing portion
        $columns = array(
            0 => '#',
            1 => 'date',
            2 => 'channel',
            3 => 'type',
            4 => 'message',
            5 => 'status',
            6 => 'created_by',
            7 => 'id'
        );

        // Coming from databale itself. Limit is the visible number of data
        $limit = html_escape($this->input->post('length'));
        $start = html_escape($this->input->post('start'));
        $order = "";
        $dir   = $this->input->post('order')[0]['dir'];

        $totalData = $totalData_s = $totalData_w = 0 ;
        if(!empty($this->input->post('mobile'))){
           
            if ($filter_data['channel'] == 'all' ) {
                $totalData_s = $this->lazyload_customer->count_all_data_sms($filter_data);
                $totalData_w=0;
            }

            $totalData = $totalData_s+$totalData_w;
            
        }
        $totalFiltered = $totalData;


        // This block of code is handling the search event of datatable
        if(empty($this->input->post('search')['value'])) {
            if(!empty($this->input->post('mobile'))){
       
                if ($filter_data['channel'] == 'all' ) {
                    if($totalData_s > 0 && $totalData_w ==0){
                        $report_data = $this->lazyload_customer->reports_sms($limit, $start, $order, $dir, $filter_data);
                        
                       log_message('error', 'COMBO SMS AND WHATSAPP RECORDS');
                       log_message('error', json_encode($report_data));
                  
                    }
                }
            }   
        }
        
            
            // Fetch the data and make it as JSON format and return it.
        $data = array();
        if(!empty($report_data)) {
            foreach ($report_data as $key => $row) {
                $nestedData['#'] = $key+1;
                $nestedData['date'] =  date('d-m-Y H:i:s', strtotime($row->create_date)); ;
                $nestedData['channel'] =  $row->channel_type =='0' ? 'sms' :'whatsapp' ;
                $nestedData['type'] =  '' ; 
                $nestedData['status'] = '';
                //$nestedData['message'] =  substr($row->message, 0,40)  ;
                $nestedData['message'] =  $row->message;
                $nestedData['create_by'] = '';
                $nestedData['id'] = $row->id;
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);

    }


    // 20-08-2021 :: queue report to remove data fome queue     
    public function get_queue_records($value=''){
        
        $report_data = array();

        // Filter portion
        $filter_data['report_name']   = $this->input->post('report_name');
        $filter_data['report_in_out'] = $this->input->post('report_in_out');
        $filter_data['from_date']         = yyyymmdd_date($this->input->post('from_date'));
        $filter_data['end_date']         = yyyymmdd_date($this->input->post('end_date'));
        $filter_data['schedule']        = $this->input->post('schedule');
        $filter_data['message_type']        = $this->input->post('message_type');
        $filter_data['status']        = $this->input->post('status');
        $filter_data['list_wise']        = $this->input->post('list_wise');
        $filter_data['user_wise']        = $this->input->post('user_wise');

        // Server side processing portion
        $columns = array(
            0 => '#',
            // 1 => 'send_from',
            1 => 'send_to',
            2 => 'message',
            3 => 'message_type_flag',
            4 => 'status',
            5 => 'scheduler_flag',
            6 => 'scheduled_time',
            7 => 'create_date',
            8 => 'status_response',
            9 => 'create_by',
            10 => 'id'
        );

        // Coming from databale itself. Limit is the visible number of data
        $limit = html_escape($this->input->post('length'));
        $start = html_escape($this->input->post('start'));
        $order = "";
        $dir   = $this->input->post('order')[0]['dir'];

        // $totalData = $this->lazyload->count_all_courses($filter_data);
        $totalData = $this->lazyload->count_all_data_outbox($filter_data);
        $totalFiltered = $totalData;

        // This block of code is handling the search event of datatable
        if(empty($this->input->post('search')['value'])) {
            
            $report_data = $this->lazyload->reports_outbox($limit, $start, $order, $dir, $filter_data);
            
        }
        else {
            
            $search = $this->input->post('search')['value'];
            $report_data =  $this->lazyload->reports_search($limit, $start, $search, $order, $dir, $filter_data);
            $totalFiltered = $this->lazyload->course_search_count($search);
             
        }

         // Fetch the data and make it as JSON format and return it.
        $data = array();
        //$report_data = $this->report_model->reports();
        if(!empty($report_data)) {
            foreach ($report_data as $key => $row) {

            
                $user = $this->user_model->getUsers($row->created_by);
                $contact_name = $this->Auth_model->get_contact($row->send_to);
                $nestedData['#'] = $key+1 .'<input type="checkbox" name="select_contact[]" class="select_contact" value="'.$row->id.'">';
                $nestedData['send_to'] =  $row->send_to ;
                $nestedData['name'] =  $contact_name;
                // $nestedData['message'] =  substr($row->message, 0,40)  ;
                $nestedData['message'] = '<a href="#" id="'.$row->message.'" onclick="show_full_message(this,'.$row->id.')" title="'.$row->message.'"> '.substr($row->message, 0,40).'</a>'   ;
                $nestedData['units'] =  $row->message_unit  ;

                $nestedData['message_type_flag'] =  $row->message_type_flag=='0'?'Direct':($row->message_type_flag=='1'?'Bulk':'App') ;

                // $nestedData['message'] =  wordwrap($row->message,50,"<br>") ;
                 
                if($row->status =='0')
                    $status = 'QUEUE';
                else if($row->status =='1')
                    $status = 'SUBMITTED';
                else if($row->status =='2')
                    $status = 'DELIVERED';
                else if($row->status =='3')
                    $status = 'NOT DELIVERED';
                 
                // $yes_no = $row->scheduler_flag=='0'?'yes':'no' ;

                $nestedData['status'] = $status ;
                $yes_no = $row->schedule_flag=='0'?'<div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"><input type="checkbox" class="custom-control-input" checked disabled id="customSwitch3"><label class="custom-control-label" for="customSwitch3"></label></div>':'<div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success"><input type="checkbox" disabled class="custom-control-input" id="customSwitch3"><label class="custom-control-label" for="customSwitch3"></label></div>' ;

                $small = ' <small>'.ddmmyyyy_date($row->schedule_time).'</small>';
                $nestedData['schedule'] = $row->schedule_flag=='0'?'<small class="badge badge-success"><i class="far fa-clock"></i> '.date("d-m-Y H:i", strtotime($row->schedule_time)).'</small>' : '';
                // $nestedData['scheduler_flag'] =  $yes_no ;
                $nestedData['create_date'] = ddmmyyyy_date($row->create_date);
                $nestedData['status_response'] = $row->status_response;
                $nestedData['create_by'] = isset($user->username)?$user->username:'API' ;
                $nestedData['id'] = $row->id;
                $data[] = $nestedData;
               
             
                 
                

            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);

    }


    // 20-08-2021 :: remove_outqueue_records
    public function remove_outqueue_records($value=''){
        $arr = $this->input->post('select_contact');
        if(!empty($arr)) {
            $this->db->where_in('id', $arr);
            $result = $this->db->delete('sms_out_queue');
            if($result){
            echo json_encode(array('status' => 'success', 'msg' => 'Record removed Successfully') );die();
            }
            else{
                echo json_encode(array('status' => 'fail', 'msg' => 'Record not delete '));die();
            }
        }
        echo json_encode(array('status' => 'fail', 'msg' => 'please select atleast one record to delete. '));die();
    }

    // 23-08-2021 :: remove_bad_records
    public function remove_bad_records($value=''){
        $arr = $this->input->post('select_contact');
        if(!empty($arr)) {
            $this->db->where_in('id', $arr);
            $result = $this->db->delete('bad_contact');
            if($result){
            echo json_encode(array('status' => 'success', 'msg' => 'Record removed Successfully') );die();
            }
            else{
                echo json_encode(array('status' => 'fail', 'msg' => 'Record not delete '));die();
            }
        }
        echo json_encode(array('status' => 'fail', 'msg' => 'please select atleast one record to delete. '));die();
    }

    // 23-08-2021 :: Bad reports      
    public function get_bad_records($value=''){
        
        $report_data = array();

        // Filter portion
        $filter_data['report_name']   = $this->input->post('report_name');
        $filter_data['report_in_out'] = $this->input->post('report_in_out');
        $filter_data['from_date']         = yyyymmdd_date($this->input->post('from_date'));
        $filter_data['end_date']         = yyyymmdd_date($this->input->post('end_date'));
        $filter_data['schedule']        = $this->input->post('schedule');
        $filter_data['message_type']        = $this->input->post('message_type');
        $filter_data['status']        = $this->input->post('status');
        $filter_data['list_wise']        = $this->input->post('list_wise');
        $filter_data['user_wise']        = $this->input->post('user_wise');

        // Server side processing portion
        $columns = array(
            0 => '#',
            1 => 'name',
            2 => 'mobile',
            3 => 'email',
            4 => 'create_by',
            5 => 'create_date',
            6 => 'id'
        );

        // Coming from databale itself. Limit is the visible number of data
        $limit = html_escape($this->input->post('length'));
        $start = html_escape($this->input->post('start'));
        $order = "";
        $dir   = $this->input->post('order')[0]['dir'];

        $totalData = $this->lazyload->count_all_data_bad_records($filter_data);
        $totalFiltered = $totalData;

        // This block of code is handling the search event of datatable
        if(empty($this->input->post('search')['value'])) {
            
            $report_data = $this->lazyload->reports_bad_records($limit, $start, $order, $dir, $filter_data);
            
        }
        else {
            
            $search = $this->input->post('search')['value'];
            $report_data =  $this->lazyload->reports_search($limit, $start, $search, $order, $dir, $filter_data);
            $totalFiltered = $this->lazyload->course_search_count($search);
             
        }

         // Fetch the data and make it as JSON format and return it.
        $data = array();
        //$report_data = $this->report_model->reports();
        if(!empty($report_data)) {
            foreach ($report_data as $key => $row) {
                $user = $this->user_model->getUsers($row->created_by);
                
                $nestedData['#'] = $key+1 .'<input type="checkbox" name="select_contact[]" class="select_contact" value="'.$row->id.'">';
                
                $nestedData['name'] =  $row->first_name .' '.$row->last_name;
                $nestedData['mobile'] =  $row->mobile_no ;
                $nestedData['email'] =  $row->email;
                $nestedData['create_by'] = isset($user->username)?$user->username:'API' ;
                $nestedData['create_date'] = ddmmyyyy_date($row->created_date);
                $nestedData['id'] = $row->id;
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);

    }

    ##################################

    //farhan :: 19-06-2021
    public function quota_report()
    {
        $data['title'] = "Quota Report || BIPA";
        $data['breadcrumb'] = "Quota Report";
        $data['users'] =  $this->user_model->getUsers();
        //  Filter option 
        $data['report_name'] =  !empty($this->input->post('report_name')) ? $this->input->post('report_name') :'sms' ; 
        $user = $this->input->post('user_wise');
        $data['quota_record'] = $this->user_model->get_quota_record($user, $data['report_name']);        
        $this->load->view('reports/quota_report',$data);
    }

    // whatsapp report

    public function whatsapp_report($value=''){

        if(!$this->isLoggedin())
            redirect('login', 'refresh');

        $data['title'] = "Whatsapp Report || BIPA ";
        $data['breadcrumb'] = "Message Report";
        $data['sms_reports'] = $this->report_model->reports();
         $data['lists'] =  $this->sms_model->get_bulk_upload_list();
         $data['users'] =  $this->user_model->getUsers();

        //  Filter option 
        $data['report_name'] =  !empty($this->input->post('report_name')) ? $this->input->post('report_name') :'sms' ; 
        $data['report_in_out'] =  !empty($this->input->post('report_in_out')) ? $this->input->post('report_in_out') :'out'; 
        $data['from_date']     = !empty($this->input->post('from_date')) ? yyyymmdd_date($this->input->post('from_date')) : date('d-m-Y 00:00', strtotime('now'));
        $data['end_date']      = !empty($this->input->post('end_date')) ? yyyymmdd_date($this->input->post('end_date')) : date('d-m-Y 23:59', strtotime('now')); 
        $data['user_wise'] = $this->input->post('user_wise');
      

        $this->load->view('reports/whatsapp',$data);

    }


    //  whatsapp Report Data   
	public function get_wa_records($value=''){
		
        $report_data = array();

		// Filter portion
        $filter_data['report_name']   = $this->input->post('report_name');
        $filter_data['report_in_out'] = $this->input->post('report_in_out');
        $filter_data['from_date']         = yyyymmdd_date($this->input->post('from_date'));
        $filter_data['end_date']         = yyyymmdd_date($this->input->post('end_date'));
        $filter_data['user_wise']        = $this->input->post('user_wise');
       

        // Server side processing portion
        $columns = array(
            0 => '#',
            1 => 'from',
            2 => 'to',
            3 => 'content_text',
            4 => 'chat_session',
            5 =>'session_start_time',
            6 => 'id',
            7 =>'bot_agent_flag',
            8 =>'user'
        );

        // Coming from databale itself. Limit is the visible number of data
        $limit = html_escape($this->input->post('length'));
        $start = html_escape($this->input->post('start'));
        $order = "";
        $dir   = $this->input->post('order')[0]['dir'];

        // $totalData = $this->lazyload->count_all_courses($filter_data);
        $totalData = $this->lazyload->count_all_data_wa($filter_data);
        $totalFiltered = $totalData;

        // This block of code is handling the search event of datatable
        if(empty($this->input->post('search')['value'])) {
            
            $report_data = $this->lazyload->reports_wa($limit, $start, $order, $dir, $filter_data);
            
        }
        else {
            
            $search = $this->input->post('search')['value'];
            $report_data =  $this->lazyload->reports_search($limit, $start, $search, $order, $dir, $filter_data);
            $totalFiltered = $this->lazyload->course_search_count($search);
             
        }

         // Fetch the data and make it as JSON format and return it.
        $data = array();
        //$report_data = $this->report_model->reports();
		if(!empty($report_data)) {
            foreach ($report_data as $key => $row) {

            if($filter_data['report_in_out'] =='out'){


                $user= $this->user_model->getUsers($row->user_id);
                $contact_name_to = $this->Auth_model->get_contact($row->to);
                $contact_name_from = $this->Auth_model->get_contact($row->from);

                $nestedData['#'] = $key+1;
                $nestedData['to'] = ($contact_name_to)!='' ? $contact_name_to : $row->to  ;
                $nestedData['from'] =  $row->from ;
                $nestedData['content_text'] = ($contact_name_from)!='' ? $contact_name_from : 'NA' ; 
                $nestedData['chat_session'] = '<a href="#" id="'.$row->chat_session.'" onclick="show_full_message(this.id,'.$row->from.')" title="'.$row->chat_session.'"> '.$row->chat_session.'</a>'; 
                $nestedData['session_start_time'] = ddmmyyyy_date($row->session_start_time);
                $nestedData['id'] = $row->id;
                $nestedData['user'] = isset($user->username)?$user->username:'BOT' ;
                $nestedData['bot_agent_flag'] = ($row->bot_agent_flag)=='0' ? 'BOT' : 'Agent' ;
                $data[] = $nestedData;


            }   
                

            }
        }

        $json_data = array(
            "draw"            => intval($this->input->post('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        echo json_encode($json_data);

	}

    public function view_conversation($chat_session,$contact)
	{
		$data['title'] ='Conversation || BIPA ';
        $data['conversation'] = $this->report_model->getConversation($chat_session);
        $data['mobile'] = $contact;
        $data['chat_session']=$chat_session;
        $data['contactname'] = $this->Auth_model->get_contact($contact);
		$this->load->view('reports/conversation',$data);
	}

    /*
    *  Admin audit Report For setings
    */

    public function settings_trace($value=''){
        if(!$this->isLoggedin())
            redirect('login', 'refresh');

        $data['title'] = "Basic Report || BIPA ";
        $data['breadcrumb'] = "Settings Audit";
        $data['audit'] =  $this->db->get('setting_audit')->result();

        $this->load->view('reports/audit',$data);

    }


    

}
