<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WASandbox extends CI_Controller {
	// https://github.com/messagebird/php-rest-api
	// https://developers.messagebird.com/tutorials/send-sms-php
	private $message_limit  ;
	private $message_offset ;
	private $show_count ;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('WA_model', 'wa_model');
		  $this->load->model('IO_model', 'io_model');
		$this->message_limit ='20' ;
		$this->message_offset ='0' ;
		$this->show_count = 0 ; 
		# code...
	}
	 
	public function index()
	{
		$data["title"] = 'Whatsapp || BIPA';
		$this->load->view('whatsapp/whatsappsms',$data);
		//// API KEY : 1kXyRIAeyyjCQbvEtJxzjQVWG    PROD lJCQOmCOjaWKOzPx4jynuhT3M
		// Channel ID:89d5d7a0-55ae-4465-ace9-1c8c5467aac9
		echo '<pre>' ;
	}

	public function balance_check(){
		// require_once __DIR__.'/vendor/autoload.php';
		$messageBird = new \MessageBird\Client('1kXyRIAeyyjCQbvEtJxzjQVWG');
		// Get your balance
		// Return Type Object array
		// MessageBird\Objects\Balance Object
		// (
		// 	[payment] => prepaid
		// 	[type] => credits
		// 	[amount] => 10
		// )
		$balance = $messageBird->balance->read();		 

		// echo '<pre>';
		echo "<br>MY CURRENT BALANCE IS <br>" ;
		echo "<br>PAYMENT MODE ".$balance->payment;	
		echo "<br>PAYMENT type ".$balance->type;	
		echo "<br>PAYMENT amount ".$balance->amount;	



		// log_message('error', 'whatsapp REPORT URL  ERROR LOG ');
		// log_message('error', json_decode($_GET));
		// log_message('error', json_decode($_POST));
		
	}
	// Conversations WhatsApp Sandbox
	// To use the WhatsApp sandbox you need to add \MessageBird\Client::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX to the list of features you want enabled. Don't forget to replace YOUR_ACCESS_KEY with your actual access key.
	
	 

	public function hsm_message($value='')
	{
		
		$messageBird = new \MessageBird\Client('lJCQOmCOjaWKOzPx4jynuhT3M', null, [\MessageBird\Client::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX]);

		$hsmParam1 = new \MessageBird\Objects\Conversation\HSM\Params();
		$hsmParam1->default = '4';

		// $hsmParam2 = new \MessageBird\Objects\Conversation\HSM\Params();
		// $hsmParam2->default = 'YOUR SECOND TEMPLATE PARAM VALUE';

		$hsmLanguage = new \MessageBird\Objects\Conversation\HSM\Language();
		$hsmLanguage->policy = \MessageBird\Objects\Conversation\HSM\Language::DETERMINISTIC_POLICY;
		//$hsmLanguage->policy = \MessageBird\Objects\Conversation\HSM\Language::FALLBACK_POLICY;
		$hsmLanguage->code = 'US';

		$hsm = new \MessageBird\Objects\Conversation\HSM\Message();
		$hsm->templateName = 'sample_shipping_confirmation';
		$hsm->namespace = '3a6c6ee2_2ef3_45f3_a48e_7349abafee64';	// sendbox
		// $hsm->namespace = '88a2f4b0_c53b_4e7d_ab34_a6a1863c0bee';	// live
		$hsm->params = [$hsmParam1];
		// $hsm->params = [$hsmParam1, $hsmParam2];
		$hsm->language = $hsmLanguage;

		$content = new \MessageBird\Objects\Conversation\Content();
		$content->hsm = $hsm;

		$message = new \MessageBird\Objects\Conversation\Message();
		$message->channelId = 'a095672544f44a2ca3ab279f1d994977';	// sandbox
		// $message->channelId = '89d5d7a0-55ae-4465-ace9-1c8c5467aac9';		// live
		$message->content = $content;
		$message->to = '917869031599';
		// $message->to = '+917869031599';
		$message->type = 'hsm';

		try {
			echo '<pre>';
		    $conversation = $messageBird->conversations->start($message);
			print_r($conversation);
		    // var_dump($conversation);
		} catch (\Exception $e) {
		    echo sprintf("%s: %s", get_class($e), $e->getMessage());
		}

	}

	public function all_conversations($value='')
	{
		// Retrieves all conversations for this account. Pagination is supported
		// through the optional 'limit' and 'offset' parameters.

		//$messageBird = new \MessageBird\Client('lJCQOmCOjaWKOzPx4jynuhT3M'); // Set your own API access key here.
		$messageBird = new \MessageBird\Client(
		   'lJCQOmCOjaWKOzPx4jynuhT3M',
		   null,
		   [\MessageBird\Client::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX]
		);
		// Take 10 objects, but skip the first 5.  max limit 20
		$optionalParameters = [
		    'limit' => '20',
		    'offset' => '0',
		];

		try {
			echo '<pre>' ;

		    $conversations = $messageBird->conversations->getList($optionalParameters);
		    // print_r($conversations);

		    $res=  $this->wa_model->update_conversations($conversations->items);	
		    // print_r($res);
		} catch (\Exception $e) {
		    echo sprintf("%s: %s", get_class($e), $e->getMessage());
		}
	}

	 

	public function messages_list($value='')
	{
		echo '<pre>' ;
		

		//$messageBird = new \MessageBird\Client('lJCQOmCOjaWKOzPx4jynuhT3M'); // Set your own API access key here.
		
		// Take 10 objects, but skip the first 5.  max limit 20
		$messageBird = new \MessageBird\Client(
		   'lJCQOmCOjaWKOzPx4jynuhT3M',
		   null,
		   [\MessageBird\Client::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX]
		);

		$optionalParameters = [
		    'limit' => $this->message_limit,
		    'offset' => $this->message_offset,
		];

		try {
		    $messages = $messageBird->conversationMessages->getList('85c93116735b4a3ab364f78b76b7701a',$optionalParameters); // CONVERSATION_ID	
		    //print_r($messages);
		    /*$page  = ceil($messages->totalCount/20);
		    echo "<br>SHOW FIRST COUNT ".$this->show_count += $messages->count ;
		    if($messages->totalCount >=  $this->show_count ){
		    	// if($i == 0 )continue;

		    	// $res = $this->wa_model->save_messages(array_reverse($messages->items, true));
		    	echo "<br>SHOW FIRST message_offset ". $this->message_offset = $messages->count;
		    	$this->messages_list();		// this fucntion call recusively till all message not read 

		    }

		    // echo "<br>PAGE ".$page;*/ 



		   
		} 
		catch (\Exception $e) {
		    echo sprintf("%s: %s", get_class($e), $e->getMessage());
		}
	}

	 
	public function send_relay()
    {
        

        $messageBird = new \MessageBird\Client('1kXyRIAeyyjCQbvEtJxzjQVWG'); // Set your own API access key here.
    	$query =  $this->db->get_where('wa_out_queue', array('sent_status' => '1'));
    	$results = $query->result();

    	// print_r(count($results));
    	//die();

    	if(count($results) > 0){
	    	foreach ($results as $key => $result) {
	    		if($key=='5') break;
	    		// Enable the whatsapp sandbox feature
		        $messageBird = new \MessageBird\Client(
		           'lJCQOmCOjaWKOzPx4jynuhT3M',
		           null,
		           [\MessageBird\Client::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX]
		        );

		        // $conversationId = '85c93116735b4a3ab364f78b76b7701a';
		        $conversationId = $result->conversation_id;

		        $content = new \MessageBird\Objects\Conversation\Content();
		        $content->text = $result->content_text ;

		        $message = new \MessageBird\Objects\Conversation\Message();
		        $message->channelId = 'a095672544f44a2ca3ab279f1d994977';
		        $message->type = 'text';
		        $message->content = $content;

		        try {
		            $conversation = $messageBird->conversationMessages->create($conversationId, $message);
		            print_r($conversation);

		            $this->db->where('id' , $result->id);
		            $this->db->update('wa_out_queue', array('sent_status' => '0'));

		        } catch (\Exception $e) {
		            echo sprintf("%s: %s", get_class($e), $e->getMessage());
		        }
	    	}
    	}


        
        
         
    }


	public function read_message(){
        // Retrieves a message.

        // require(__DIR__ . '/../../autoload.php');

         $messageBird = new \MessageBird\Client('lJCQOmCOjaWKOzPx4jynuhT3M', null, [\MessageBird\Client::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX]);

        try {
        	echo '<pre>' ;
            $message = $messageBird->conversationMessages->read('8f88b961d614462bacf74f0150a87652');
            $this->wa_model->update_message_status($message); 
            print_r($message);
        } catch (\Exception $e) {
            echo sprintf("%s: %s", get_class($e), $e->getMessage());
        }
    } 


   /* public function send_confirm_message_before_close($mobile, $msg){

		// $messageBird = new \MessageBird\Client('lJCQOmCOjaWKOzPx4jynuhT3M'); // Set your own API access key here.
		 $messageBird = new \MessageBird\Client(
		   'lJCQOmCOjaWKOzPx4jynuhT3M',
		   null,
		   [\MessageBird\Client::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX]
		);

		$conversationId = '85c93116735b4a3ab364f78b76b7701a';

		$content = new \MessageBird\Objects\Conversation\Content();
		$content->text = $msg;

		$message = new \MessageBird\Objects\Conversation\Message();
		$message->channelId = 'a095672544f44a2ca3ab279f1d994977';
		$message->type = 'text';
		$message->content = $content;

		try {
		    $conversation = $messageBird->conversationMessages->create($conversationId, $message);
		    print_r($conversation);
		} catch (\Exception $e) {
		    echo sprintf("%s: %s", get_class($e), $e->getMessage());
		}
	}*/



    #######################################################################
    ## Cron Scriptd here
    #######################################################################

    /*
    *	Check no response message from customer 
    */

    public function check_Customer_norespose($value=''){
    	$result = $this->db->get_where('bot_chat_session')->result();
    	if(!empty($result)){
    		foreach ($result as $key => $value) {
    			$bot_start_time = $value->createdDatetime;
    			
    			// get last message by mobile 
    			$last_message = $this->wa_model->last_chat_message_userwise($value->from);
    			// $timediff = strtotime($last_message->createdDatetime) - strtotime($bot_start_time)  ;
    			$timediff = strtotime('now') - strtotime($last_message->createdDatetime)  ;
    			log_message('error', json_encode($last_message));
    			log_message('error', 'message '.$timediff);
    			// 5 mint
    			if( $timediff > 100  ){
    				log_message('error','Conversation time ok');

    				if($value->chat_warning_status=='1'){
    					$this->io_model->save_wa_out_queue($value->conversation_id, 'thank you for contact us.');
    					// $this->send_confirm_message_before_close($value->from, 'thank you for contact us.');
    					$this->db->where('from',$value->from);
	    				$this->db->delete('bot_chat_session') ;

    				}
    				else{
    					// through last worning message 
    					$this->io_model->save_wa_out_queue($value->conversation_id, 'there is no response from your side, we will close this chat session.');
	    				// $this->send_confirm_message_before_close($value->from, 'there is no response from your side, we will close this chat session.');
	    				$this->db->where('from',$value->from);
	    				$this->db->update('bot_chat_session', array('chat_warning_status' =>1) ) ;
    				}
    			}
    			else{
    				log_message('error','SAME TIME OF CONVERSIOn');
    			}	 
    				


    		}
    	}
    }



    
	


}
