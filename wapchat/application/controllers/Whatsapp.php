<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Whatsapp extends Admin_Controller {
	// https://github.com/messagebird/php-rest-api
	// https://developers.messagebird.com/tutorials/send-sms-php

	 
	public function index()
	{
		$data["title"] = 'Whatsapp || BIPA';
		$this->load->view('whatsapp/whatsappsms',$data);
		//// API KEY : 1kXyRIAeyyjCQbvEtJxzjQVWG    PROD lJCQOmCOjaWKOzPx4jynuhT3M
		// Channel ID:89d5d7a0-55ae-4465-ace9-1c8c5467aac9
		echo '<pre>' ;
	}

	public function bot_request(){

		$data["title"] = 'Bot Request || BIPA';
		$data['bot_session'] = $this->user_model->getBotSession();
		$this->load->view('whatsapp/chat_session',$data);

	}



	public function balance_check()
	{
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
	
	public function whatsapp_hsm_message()
	{	
		// Set Access key
		// $messageBird = new \MessageBird\Client('1kXyRIAeyyjCQbvEtJxzjQVWG'); 
		// Enable the whatsapp sandbox feature
		$messageBird = new \MessageBird\Client('1kXyRIAeyyjCQbvEtJxzjQVWG', null, [\MessageBird\Client::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX]);

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
		$message->to = '+917869031599';
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

	public function hsm_message($value='')
	{
		$messageBird = new MessageBirdClient('lJCQOmCOjaWKOzPx4jynuhT3M');

		// Enable the whatsapp sandbox feature
		//$messageBird = new MessageBirdClient(
		//    lJCQOmCOjaWKOzPx4jynuhT3M',
		//    null,
		//    [MessageBirdClient::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX]
		//);

		$content = new MessageBirdObjectsConversationContent();
		$hsm = new MessageBirdObjectsConversationHSMMessage();
		$hsmParamsName = new MessageBirdObjectsConversationHSMParams();
		$hsmParamsName->default = 'Bob';

		$hsmParamsWhen = new MessageBirdObjectsConversationHSMParams();
		$hsmParamsWhen->default = 'Tommorrow!';

		$hsmLanguage = new MessageBirdObjectsConversationHSMLanguage();
		$hsmLanguage->policy = MessageBirdObjectsConversationHSMLanguage::DETERMINISTIC_POLICY;
		//$hsmLanguage->policy = MessageBirdObjectsConversationHSMLanguage::FALLBACK_POLICY;
		$hsmLanguage->code = 'YOUR LANGUAGE CODE';

		$hsm->templateName = 'support';
		$hsm->namespace = '3a6c6ee2_2ef3_45f3_a48e_7349abafee64';
		$hsm->params = array($hsmParamsName, $hsmParamsWhen);
		$hsm->language = $hsmLanguage;

		$content->hsm = $hsm;

		$message = new MessageBirdObjectsConversationMessage();
		$message->channelId = 'a095672544f44a2ca3ab279f1d994977';
		$message->content = $content;
		$message->to = '917869031599';
		$message->type = 'hsm';

		try {
		    $conversation = $messageBird->conversations->start($message);
		    var_dump($conversation);
		} catch (Exception $e) {
		    echo sprintf("%s: %s", get_class($e), $e->getMessage());
		}
	}

	public function enable_whatsapp_sandbox($value='')
	{
		// Set Access key
		$messageBird = new \MessageBird\Client('1kXyRIAeyyjCQbvEtJxzjQVWG'); 

		// Set your own API access key here.
		// Create a client with WhatsApp sandbox enabled.
		$messageBird = new \MessageBird\Client('1kXyRIAeyyjCQbvEtJxzjQVWG', null, [\MessageBird\Client::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX]);

		// Use WhatsApp sandbox channel as normal.

		$content = new \MessageBird\Objects\Conversation\Content();
		$content->text = 'Hello world';

		$message = new \MessageBird\Objects\Conversation\Message();
		$message->channelId = '89d5d7a0-55ae-4465-ace9-1c8c5467aac9';
		$message->content = $content;
		$message->to = '+447418310508'; // Channel-specific, e.g. MSISDN for SMS.
		$message->type = 'text';

		try {
		    $conversation = $messageBird->conversations->start($message);
		    print_r($conversation);
		    // var_dump($conversation);
		} catch (\Exception $e) {
		    echo sprintf("%s: %s", get_class($e), $e->getMessage());
		}
	}

	public function create_single_message(Type $var = null)
	{
		$messageBird = new \MessageBird\Client('1kXyRIAeyyjCQbvEtJxzjQVWG'); // Set your own API access key here.

		$content = new \MessageBird\Objects\Conversation\Content();
		$content->text = 'Hello world Again';

		$message = new \MessageBird\Objects\Conversation\Message();
		$message->channelId = '89d5d7a0-55ae-4465-ace9-1c8c5467aac9';
		$message->content = $content;
		// $message->to = '+447418310508'; // Channel-specific, e.g. MSISDN for SMS.
		$message->to = '+917869031599'; // Channel-specific, e.g. MSISDN for SMS.
		$message->type = 'text';

		try {
			echo '<pre>' ;
			$conversation = $messageBird->conversations->start($message);
			echo "IN SIDE OF TRY BLOCK <br>";
			print_r($conversation);
			// var_dump($conversation);
		} catch (\Exception $e) {
			echo sprintf("%s: %s", get_class($e), $e->getMessage());
		}
	}

	 

	/* 1.1 */
	public function all_conversations($value='')
	{
		// Retrieves all conversations for this account. Pagination is supported
		// through the optional 'limit' and 'offset' parameters.

		$messageBird = new \MessageBird\Client('1kXyRIAeyyjCQbvEtJxzjQVWG'); // Set your own API access key here.

		// Take 10 objects, but skip the first 5.  max limit 20
		$optionalParameters = [
		    'limit' => '20',
		    'offset' => '0',
		];

		try {
			echo '<pre>' ;

		    $conversations = $messageBird->conversations->getList($optionalParameters);
		    print_r($conversations);
		} catch (\Exception $e) {
		    echo sprintf("%s: %s", get_class($e), $e->getMessage());
		}
	}

	public function message_read($value='')
	{
		// Retrieves a message.
		echo '<pre>' ;
		$messageBird = new \MessageBird\Client('1kXyRIAeyyjCQbvEtJxzjQVWG'); // Set your own API access key here.

		try {
		    $message = $messageBird->conversationMessages->read('4b20f2c9c5574d59ba161f60464b76ca');

		    print_r($message);
		} catch (\Exception $e) {
		    echo sprintf("%s: %s", get_class($e), $e->getMessage());
		}
	}

	// receive messages 
	public function messages_list($value='')
	{
		echo '<pre>' ;
		$messageBird = new \MessageBird\Client('1kXyRIAeyyjCQbvEtJxzjQVWG'); // Set your own API access key here.
		try {
		    // $messages = $messageBird->conversationMessages->getList('33e244dda05e43bdb8fad20e3bd28bdf'); // CONVERSATION_ID	
		    $messages = $messageBird->conversationMessages->getList('9e4e39f0f4984facb3b3bd2006812a4f'); // CONVERSATION_ID	

		    print_r($messages);
		} catch (\Exception $e) {
		    echo sprintf("%s: %s", get_class($e), $e->getMessage());
		}
	}

	public function read_single_conversation($value='')
	{
		// Retrieves a single conversation by its ID.
		echo '<pre>' ;
		$messageBird = new \MessageBird\Client('1kXyRIAeyyjCQbvEtJxzjQVWG'); // Set your own API access key here.

		// Setting the optional 'include' parameter to 'content' requests the API to
		// include the expanded Contact object in its response. Excluded by default.
		$optionalParameters = [
		    'include' => 'content',
		];

		try {
		    $conversation = $messageBird->conversations->read('33e244dda05e43bdb8fad20e3bd28bdf' ,$optionalParameters);

		    print_r($conversation);
		} catch (\Exception $e) {
		    echo sprintf("%s: %s", get_class($e), $e->getMessage());
		}
	}


	


}
