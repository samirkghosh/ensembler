<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bulksms extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Auth_model');
		$this->load->library('excel');
	}

	 
	public function index()
	{
		$data["title"] = 'Bulk sms || Bipa'; 
       $this->load->view('sms/bulk',$data);
	}

	public function import(){
       

		$bulk_session_id="Bulk_".uniqid()."_".rand(0,1000000);
		// Read and store
		if(isset($_FILES["file"]["name"]))
		{
			$path = $_FILES["file"]["tmp_name"];
			$object = PHPExcel_IOFactory::load($path);
			foreach($object->getWorksheetIterator() as $worksheet)
			{
				$highestRow = $worksheet->getHighestRow();
				for($row=2; $row<=$highestRow; $row++)
				{
					$send_to = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
					if(is_numeric($send_to)){
						
						$send_from = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
						$message = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
					    $schedule_flag = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
						
					}else{
						echo "Failed to import Please Select Valid Formatted File";
						exit();
					}

		            if($schedule_flag == 0){
						$schedule_time = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
					}else{
						$schedule_time = '';
					}
		
					$data[] = array(
						'send_to' =>	$send_to,
						'send_from' =>	$send_from,
						'message' =>	$message,
						'message_type_flag' => '0',
						'status' =>	'0',
						'scheduler_flag'	=>	$schedule_flag,
						'scheduled_time' => $schedule_time,
						'bulk_session_id' => $bulk_session_id
					);
					
				}
			
			}
			
			 
			$res = $this->Auth_model->insert_bulk($data);

           if($res){
			   //file upload 
			$new_name = $bulk_session_id;
            $config['file_name'] = $new_name;
			$config['upload_path']          = './uploads';
			$config['allowed_types']        = 'xls|xlsx|csv';
			$config['max_size']             = 1000000;
			
			$this->load->library('upload', $config);
            
			if($this->upload->do_upload('file')){
				echo 'Data Imported successfully';
			}

		   }
			

		}
	}
		
			

}
