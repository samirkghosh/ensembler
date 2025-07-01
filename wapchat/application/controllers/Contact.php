<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact extends Admin_Controller {

    public function __construct()
	{
		parent::__construct();
	
		$this->load->model('Auth_model');	
	}

	// update	: 20-08-2021
	public function index(){
		if(!$this->isLoggedin())
			redirect('login', 'refresh');


		// $data["fetch_data"] = $this->Auth_model->fetch_contact();   
		$data["title"] = 'Contact || Bipa';
		$data['lists'] =  $this->sms_model->get_bulk_upload_list();
		$data['breadcrumb'] = "Add New Contacts";
		$data['list_wise'] =  $this->input->post('list_wise');
		
		$this->load->view("contact/addcontact", $data);  
	}

	 
	public function form_validation()
	{
        if(!$this->isLoggedin())
			redirect('login', 'refresh');
		  

		$this->form_validation->set_rules('inputfirstname', 'Name', 'required'); 
		$this->form_validation->set_rules('inputmobile', 'Mobile', 'required|min_length[9]|max_length[13]|callback_validate_mobile');
		if ($this->form_validation->run() == FALSE){
			// Show Error if invalid
			$this->index();

		}
		else{
        
			// redirect when registration success
           //farhan:: 22-06-2021
            $data = array (
				'first_name'=>$this->input->post("inputfirstname"),
				'last_name'=>$this->input->post("inputlastname"),
				'email'=>$this->input->post("inputemail"),
				'mobile_no'=>$this->input->post("inputmobile"),
				'reference'=>$this->input->post("inputreference")

			);
		
		  if($this->input->post("insert"))  
		  {  
			// insert
			$res = $this->Auth_model->insert_contact($data); // Duplicacy of contact is check before insert
			if($res){
				$this->session->set_flashdata('success', 'Successfully added');
				redirect('contact', 'refresh');
			}
			else{
				$this->session->set_flashdata('fail', 'Mobile number is already Exists');
				redirect('contact', 'refresh');
			}
		  }

		  if($this->input->post("update"))  
		  {  
			// update
			$res = $this->Auth_model->update_contact($data,$this->input->post("hidden_id")); 
			if($res){
				$this->session->set_flashdata('success', 'Successfully Updated');
				redirect('contact');
			}
			else{
				$this->session->set_flashdata('fail', 'Mobile number is already Exists');
				redirect('contact', 'refresh');
			}
			
		  }
		  
		}

	}
	public function update_contact(){ 
		if(!$this->isLoggedin())
			redirect('login', 'refresh');
		
		$user_id = $this->uri->segment(3); 
		$data["title"] = 'Contact || Bipa';  
		$data['breadcrumb'] = "Update Contact";
		$data["user_data"] = $this->Auth_model->fetch_single_contact($user_id);  
		$data["fetch_data"] = $this->Auth_model->fetch_contact();  
		$this->load->view("contact/addcontact", $data);  
   }

   	
	public function delete_contact(){  
		$id = $this->uri->segment(3);   
		$this->Auth_model->delete_contact($id);  
		redirect('contact' , 'refresh'); 
   	}

   	// New	: 20-08-2021
   	public function remove_multiple_contacts(){  
		
   		$contact_arr = $this->input->post('select_contact');
   		if(!empty($contact_arr)) {
   			$this->db->where_in('id', $contact_arr);
   			$result = $this->db->delete('contact');

   		}
   		if($result){
			echo json_encode(array('status' => 'success', 'msg' => 'Record removed Successfully') );die();
   		}
		else{
			echo json_encode(array('status' => 'fail', 'msg' => 'Record not delete '));die();
		}
   	}

   	public function ajax_customer_search($value='')
   	{
   		$search = $this->input->post('search');
   		/*$this->db->select('sms_in_queue.*, contact.first_name, contact.last_name');
   		$this->db->from('sms_in_queue');
   		$this->db->join('contact', 'sms_in_queue.send_from = contact.mobile_no');*/
   		$this->db->select('mobile_no, first_name, last_name');
   		$this->db->from('contact');
   		$this->db->like('mobile_no', $search );
   		$this->db->or_like('first_name', $search );
   		$this->db->or_like('last_name', $search );
   		$query = $this->db->get();
   		log_message('error', "QUERY ");
   		log_message('error', $this->db->last_query());
   		echo json_encode(array('status' => 'success', 'search' => $query->result_array() ));die();
   	}


   	// new	: 20-08-2021
   	public function get_contacts($value=''){
		
        $report_data = array();

		// Filter portion
        $filter_data['campaign']   	= '';
        /*$filter_data['report_in_out'] 	= $this->input->post('report_in_out');
	        $filter_data['from_date']      	= yyyymmdd_date($this->input->post('from_date'));
	        $filter_data['end_date']        = yyyymmdd_date($this->input->post('end_date'));
	        $filter_data['schedule']        = $this->input->post('schedule');
	        $filter_data['message_type']    = $this->input->post('message_type');
	        $filter_data['status']       	= $this->input->post('status');
	        $filter_data['list_wise']       = $this->input->post('list_wise');
	        $filter_data['user_wise']       = $this->input->post('user_wise');
		*/
        // Server side processing portion
        $columns = array(
            0 => '#',
            1 => 'name',
            2 => 'email',
            3 => 'mobile',
            4 => 'action',
            5 => 'id'
        );

        // Coming from databale itself. Limit is the visible number of data
        $limit = html_escape($this->input->post('length'));
        $start = html_escape($this->input->post('start'));
        $order = "";
        $dir   = $this->input->post('order')[0]['dir'];

        // $totalData = $this->lazyload->count_all_courses($filter_data);
        $totalData = $this->contact_model->count_all_data($filter_data);
        $totalFiltered = $totalData;

        // This block of code is handling the search event of datatable
        if(empty($this->input->post('search')['value'])) {
            
            $report_data = $this->contact_model->reports($limit, $start, $order, $dir, $filter_data);
            
        }
        else {
            
            $search = $this->input->post('search')['value'];
            $report_data =  $this->contact_model->reports_search($limit, $start, $search, $order, $dir, $filter_data);
            $totalFiltered = $this->contact_model->course_search_count($search);
             
        }

         // Fetch the data and make it as JSON format and return it.
        $data = array();
        //$report_data = $this->report_model->reports();
		if(!empty($report_data)) {
            foreach ($report_data as $key => $row) {

                $nestedData[''] ='<input type="checkbox" name="select_contact[]" class="select_contact" value="'.$row->id.'">' ;
                $nestedData['#'] = $key+1 ;
                $nestedData['name'] =  $row->first_name .' ' . $row->last_name;
                $nestedData['email'] =  $row->email ;
                $nestedData['mobile'] =  $row->mobile_no  ;                 
                $nestedData['action'] = '<a href="'.base_url().'contact/update_contact/'.$row->id.'" style="color:mediumseagreen"><i class="nav-icons fas fa-edit"></i></a>
                     <a href="#" style="color:crimson;" class="delete_data" onclick="remove_contacts(this)" id="'.$row->id.'"><i class="nav-icons fas fa-trash"></i>
                     </a>';
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





}
