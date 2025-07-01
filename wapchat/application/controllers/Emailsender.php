<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emailsender extends Admin_Controller {

	private $to ; 
	private $from ;
	private $add_cc ;
	private $add_bcc ;
	private $subject ;
	
	public function __construct(){
		parent::__construct();

		// $this->to = 'vijaypippal1988@gmail.com' ; // change this email to bipa reciver email
		// $this->from = 'uc2000helpdesk@gmail.com' ; // change this email to bipa from email
		$this->add_cc = 'uc2000helpdesk@gmail.com' ; // change this email to bipa add_cc email
		$this->add_bcc = 'uc2000helpdesk@gmail.com' ; // change this email to bipa add_bcc email
		$this->subject = 'SMS TO EMAIL' ; // change this email to bipa add_bcc email
	} 
	// $arr = array('email_type'=> 'OUT', 'I_Status'=>'1' );
	// $this->db->select('EMAIL_ID, v_toemail, v_fromemail,add_cc,add_bcc, v_subject, v_body, V_rule, d_email_date, email_type, module, size, subjectid ,ICASEID, original_subject,i_RetryCount');

	##########################################################
	### read incomming sms to sent bipa 
	### neet to set in cron 
	 
	public function send_sms_to_bipa($value=''){
		
		$arr = array('status'=> '0' );
		$query = $this->db->get_where('tbl_email_out_queue', $arr);
		 
		if($query->num_rows() > 0 ){
			$result = $query->result();
			foreach ($result as $key => $value): 

				$V_Body = $value->body;

				require_once(APPPATH.'libraries/class-phpmailer.php');
				$mail = new PHPMailer();
				$mail->IsSMTP();

				if ( EMAIL_SSL == "1"){
					$mail->SMTPAuth = true;
					$mail->SMTPSecure = get_settings('encryption');
				}
				else
					$mail->SMTPSecure = get_settings('encryption');

				$mail->SMTPDebug = 1;

				$mail->Host = get_settings('smtp_host');
				$mail->Username = get_settings('from_email');
				$mail->Password = get_settings('smtp_password');
				$mail->Port = get_settings('port');

				// $mail->From = $fromAddr; 
				// $mail->FromName = $fromAddr; //EMAIL_FROM_NAME;
				$mail->setFrom($value->v_from);
				$mail->AddAddress($value->v_to);
				// $mail->AddCC($this->add_cc);	
				// $mail->AddBCC($this->add_bcc);
							
				$mail->WordWrap = 50;
				$mail->IsHTML(true);

				$mail->Subject  =  $value->v_subject;

				//$mail->AddAttachment(ATTACHMENT_PATH . '/'.$filename1, $filename);
				/*if ( file_exists($fileNames) )
				{
					$filename = basename($fileNames);
					$mail->AddAttachment($fileNames, $filename);
				}*/

				$mail->Body     =  $value->body;

				//$iRetry = $iRetry +1;
				if($mail->Send()){	// Update status after send successfully.
					$this->db->where('id', $value->id);
					$this->db->update('tbl_email_out_queue',array('status' => '1'));
				}
					
				log_message('error', 'SMS TO EMAIL '.$value->v_subject);
				log_message('error', 'STATUS '. $mail->ErrorInfo);
				//echo "<br> STATUS ". $mail->ErrorInfo ;
			endforeach;

			sleep(10);
			//$this->db->close();

		}
	}

		
		
	public function bipa_email_sender($value=''){

		log_message('error', 'SMS TO EMAIL '.date('d-m-Y H:i:s'));
		$arr = array('status'=> '0' );
		$query = $this->db->get_where('tbl_email_out_queue', $arr);
		 
		if($query->num_rows() > 0 ){
			$result = $query->result();
			foreach ($result as $key => $value): 

				$V_Body = $value->body;
				require_once(APPPATH.'libraries/PHPMailer-5.2.28/PHPMailerAutoload.php');
				//SMTP needs accurate times, and the PHP time zone MUST be set
				//This should be done in your php.ini, but this is how to do it if you don't have access to that
				date_default_timezone_set('Etc/UTC');

				// require_once '../PHPMailerAutoload.php';

				//Create a new PHPMailer instance
				$mail = new PHPMailer;

				//Tell PHPMailer to use SMTP
				$mail->isSMTP();


				//Enable SMTP debugging
				// 0 = off (for production use)
				// 1 = client messages
				// 2 = client and server messages
				$mail->SMTPDebug = 3;

				//Ask for HTML-friendly debug output
				//$mail->Debugoutput = 'html';

				//Set the hostname of the mail server
				$mail->Host = get_settings('smtp_host');

				//Set the SMTP port number - likely to be 25, 465 or 587
				$mail->Port =587 ;
				// $mail->Host = get_settings('smtp_host');
				// $mail->Username = get_settings('from_email');
				// $mail->Password = get_settings('smtp_password');
				// $mail->Port = get_settings('port');

				// $mail->From = $fromAddr; 
				// $mail->FromName = $fromAddr; //EMAIL_FROM_NAME;
				$mail->setFrom($value->v_from);
				$mail->AddAddress($value->v_to);


				//Whether to use SMTP authentication
				$mail->SMTPAuth = true;
				//Set who the message is to be sent from
				$mail->Username = get_settings('from_email');
				$mail->Password = get_settings('smtp_password');
				$mail->SMTPSecure = 'tls';



				$mail->setFrom(get_settings('from_email'), 'First Last');

				//Set an alternative reply-to address

				$mail->addReplyTo(get_settings('from_email'), 'First Last');
				//Set who the message is to be sent to
				$mail->addAddress(get_settings('from_email'), 'John Doe');
				//Set the subject line

				$mail->Subject = 'PHPMailer SMTP without auth test';
				$mail->Body='Test';
				//Read an HTML message body from an external file, convert referenced images to embedded,
				//convert HTML into a basic plain-text alternative body
				//$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
				//Replace the plain text body with one created manually
				$mail->AltBody = 'This is a plain-text message body';
				//Attach an image file
				//$mail->addAttachment('images/phpmailer_mini.png');

				//send the message, check for errors
				if (!$mail->send()) {
				    echo "Mailer Error: " . $mail->ErrorInfo;
				} else {
				    echo "Message sent!";
				}
			endforeach;
		}

	}
}
