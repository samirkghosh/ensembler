<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Smstemplate extends Admin_Controller {

    public function __construct()
	{
		parent::__construct();
	
		$this->load->model('Auth_model');	
	}

	public function index(){

		$data['fetch_data'] = $this->Auth_model->get_sms_template();   
		$data["title"] = 'Sms Template || Bipa';
		$this->load->view("sms/template", $data);  
	}

	 
	public function form_validation()
	{
         
		
		 $this->form_validation->set_rules('tempname', 'Name', 'required');
		 $this->form_validation->set_rules('tempcontent', 'Template Content', 'required');

		if ($this->form_validation->run() == FALSE){

			// Show Error if invalid
			$this->index();

		}
		else{
        
			// redirect when registration success
			$name = $this->input->post('tempname');  
			$content = $this->input->post('tempcontent');
			$slug = explode(' ',strtolower($name));
			$slug2= implode('-',$slug);
			//insert 
			$data = array(
				'name'=>$name,
				'template_content'=>$content,
				'slug'=>$slug2,
		 );
		
		  if($this->input->post("insert"))  
		  {  
			// insert
			$res = $this->Auth_model->insert_sms_template($data);
			if($res){
				$this->session->set_flashdata('success', 'Successfully added');
				redirect('smstemplate');
			}
		  }

		  if($this->input->post("update"))  
		  {  
			// update
			$res = $this->Auth_model->update_sms_template($data,$this->input->post("hidden_id")); 
			if($res){
				$this->session->set_flashdata('success', 'Successfully Updated');
				redirect('smstemplate');
			}
			
		  }
		  
		}

	}
	public function update_sms_template(){ 
		$user_id = $this->uri->segment(3);   
		$data["user_data"] = $this->Auth_model->fetch_single_template($user_id);  
		$data["fetch_data"] = $this->Auth_model->get_sms_template();  
		$this->load->view("sms/template", $data);  
   }

	public function delete_sms_template(){
		$id = $this->uri->segment(3);   
		$this->Auth_model->delete_sms_template($id);  
		redirect('smstemplate' , 'refresh'); 
	}
	public function get_template_content()
	{
		 $tempname=$this->input->post('tempname');
		 $this->Auth_model->get_template($tempname);
      
	}


}
