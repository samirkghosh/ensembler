<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webhooks extends CI_Controller{
	
    private $v_next ;
    private $message ;
    private $conversation_id ;
    private $last_e_type ;
    private $recursive_count ;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('WA_model', 'wa_model');
        $this->load->model('IO_model', 'io_model');

        $this->v_next = '' ;
        $this->message = '' ;
        $this->conversation_id = '' ;
        $this->last_e_type ='';
        $this->recursive_count = 0;
    }   

    public function index(){ 
        echo "Webhooks";
    }

    public function hsm_message($mobile_no, $template_name , $param_count='', $param_arr = array()){
        
        log_message('error', 'HSM MESSAGE CALL '); 
        log_message('error', 'HSM mobile_no '.$mobile_no); 
        log_message('error', 'HSM template_name '.$template_name); 
        log_message('error', 'HSM MESSAGE CALL '); 
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
        $hsm->templateName = $template_name; //'sample_shipping_confirmation';
        // $hsm->namespace = '3a6c6ee2_2ef3_45f3_a48e_7349abafee64';   // sendbox
        $hsm->namespace = '88a2f4b0_c53b_4e7d_ab34_a6a1863c0bee';    // live
        $hsm->params = [$hsmParam1];
        // $hsm->params = [$hsmParam1, $hsmParam2];
        $hsm->language = $hsmLanguage;

        $content = new \MessageBird\Objects\Conversation\Content();
        $content->hsm = $hsm;

        $message = new \MessageBird\Objects\Conversation\Message();
        $message->channelId = 'a095672544f44a2ca3ab279f1d994977';   // sandbox
        // $message->channelId = '89d5d7a0-55ae-4465-ace9-1c8c5467aac9';        // live
        $message->content = $content;
        $message->to = $mobile_no;
        // $message->to = '+917869031599';
        $message->type = 'hsm';

        try {
            //echo '<pre>';
            $conversation = $messageBird->conversations->start($message);
            log_message('error', 'HSM');
            log_message('error', json_encode($conversation));
            //print_r($conversation);
            // var_dump($conversation);
        } catch (\Exception $e) {
            echo sprintf("%s: %s", get_class($e), $e->getMessage());
        }

    } 


    public function send_relay($conversation_id, $message)
    {
        
        // $messageBird = new \MessageBird\Client('1kXyRIAeyyjCQbvEtJxzjQVWG'); // Set your own API access key here.

        // Enable the whatsapp sandbox feature
        $messageBird = new \MessageBird\Client(
           'lJCQOmCOjaWKOzPx4jynuhT3M',
           null,
           [\MessageBird\Client::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX]
        );

        $conversationId = '85c93116735b4a3ab364f78b76b7701a';

        $content = new \MessageBird\Objects\Conversation\Content();
        $content->text = $message;

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
        
        
    }

    #######################################################################################################################
    #######################################################################################################################



    public function webhook_create($value=''){
        // Webhooks enable real-time notifications of conversation events to be
        // delivered to endpoints on your own server. This example creates a webhook
        // that is invoked when new conversations and messages are created in the
        // specified channel.

        /* Creadted : vijay pippal : 09-08-2021 */
        /*MessageBird\Objects\Conversation\Webhook Object ( 
            [id] => 4c10b59721ca42dd8b3f62627c76cd62 
            [href] => 
            [channelId] => 89d5d7a0-55ae-4465-ace9-1c8c5467aac9 
            [events] => Array ( 
                [0] => conversation.created 
                [1] => message.created 
                ) 
            [url] => https://ensembler.com/bipa/webhooks/webhook_path 
            [createdDatetime] => 2021-08-09T06:46:32.084655501Z 
            [updatedDatetime] => )
            */

        // $messageBird = new \MessageBird\Client('lJCQOmCOjaWKOzPx4jynuhT3M'); // Set your own API access key here.
            $messageBird = new \MessageBird\Client('lJCQOmCOjaWKOzPx4jynuhT3M', null, [\MessageBird\Client::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX]);

        try {
            $webhook = new \MessageBird\Objects\Conversation\Webhook();
            $webhook->channelId = 'a095672544f44a2ca3ab279f1d994977';
            // $webhook->url = 'https://ensembler.com/bipa/webhooks/webhook_path';
            $webhook->url = 'http://167.71.232.201:7080/webhooks/webhook_path';
            $webhook->events = [
                \MessageBird\Objects\Conversation\Webhook::EVENT_CONVERSATION_CREATED,
                \MessageBird\Objects\Conversation\Webhook::EVENT_MESSAGE_CREATED,
                \MessageBird\Objects\Conversation\Webhook::EVENT_CONVERSATION_UPDATED,
                \MessageBird\Objects\Conversation\Webhook::EVENT_MESSAGE_UPDATED,

                // Other options:
            ];

            $res = $messageBird->conversationWebhooks->create($webhook);
            print_r($res);

        } catch (\Exception $e) {
            echo sprintf("%s: %s", get_class($e), $e->getMessage());
        }
    }



    public function webhook_path($value=''){
        $response1 = "\n\r#############################################################################\n\r" ;
        $file = fopen('webhook1.txt', "a") or die('unable to open file');
        fwrite($file, "\n\r============= > ".date('Y-m-d H:i:s'));
        fwrite($file, "\n\r============= > ");
        fwrite($file, $response1);
        fwrite($file, file_get_contents('php://input'));
        fclose($file);

        $messages = json_decode(file_get_contents('php://input'));

        if($messages->type=='message.created'){
            // if Message created 
            log_message('error', 'message created ');
            //  Save Record 
            $res = $this->wa_model->save_messages($messages->message);
        }
        // if mesage sent by bipa than update status from pendign to read
         log_message('error', 'update message created ');
         log_message('error', $messages->message->direction);

        if($messages->message->direction =='sent'){
            log_message('error', 'update message created IN SIDE');
            $this->readMessage($messages->message->id);
        }

        if($messages->message->direction =='received'){
            log_message('error', 'update message received IN SIDE');
            if(strtolower($messages->message->content->text) == 'hi'){
                $this->initiate_bot($messages->message);
            }
            // after bot intiate checck each message input
            $this->isMessageCreated($messages->message, $messages->conversation, true);
        }

    }




    ##########################################################



    public function isMessageCreated($message, $conversation, $no_input= false){
        //$recursive_count = 0;
        $this->message = $message;
        $this->conversation_id = $conversation;
        //$input_text ='';
        if($message->type=='text'){
            if($no_input == true){
                $input_text = strtolower($message->content->text) ;
                $user_input = $input_text ;
            }
            else{
                $input_text = '' ;
                $user_input = '' ;
            }

            $bot_info = $this->wa_model->botSessionby_id($conversation->id);

            log_message('error', 'check no input  '.$no_input);
            log_message('error', 'check no input 2  '.$input_text);
            log_message('error', json_encode($bot_info) );

            if($input_text =='0'){
                // $this->send_relay($conversation->id, 'am sorry can\'t understand ');
                $this->io_model->save_wa_out_queue($conversation->id, 'am sorry can\'t understand ');
                exit();
            }

            
            if($bot_info != false){
                // contain last store information
                // and contain last action value.
                if($bot_info->next_action > 0  ){
                    // if contain next action 
                    log_message('error', 'FUNCTION CALL 1  '.$input_text);
                     $io_res = $this->io_model->read_output_on_cutomer_input($input_texts ='', $bot_info->next_action);
                }
                else{
                    log_message('error', 'FUNCTION CALL 2  '.$input_text);
                    // not contain next value handel error . first call 
                    $io_res = $this->io_model->read_output_on_cutomer_input($input_text, $no_input);

                }
            }
            else{
                log_message('error', 'FUNCTION CALL 3  '.$input_text);
                // No infomation will hold.  or first request 
                $io_res = $this->io_model->read_output_on_cutomer_input($input_text, $no_input);
            }
            

            

             log_message('error', 'FUNCTION CALL AFTER   '.$input_text);
             log_message('error', json_encode($io_res));
            if($io_res != false){

                if($io_res->e_type =='display'){
                    // $this->send_relay($conversation->id, $io_res->v_name);
                    $this->io_model->save_wa_out_queue($conversation->id, $io_res->v_name);

                    // Save Next action 
                    if($io_res->e_type !='taction' && $io_res->v_next > 0 ){
                        $data = array('next_action' => $io_res->v_next );
                        $this->io_model->store_last_selected_option($data, $conversation->id);
                    }
                    
                    // Recusively call this funtion for display next display Message if is there. 
                    log_message('error', 'Recursive 1 '.$io_res->v_next);
                    if($io_res->v_next > 0 && $io_res->e_type !='input'):
                        $this->recursive_count++;
                        $this->isMessageCreated($message, $conversation, $io_res->v_next = false);
                    endif;
                }
                else if($io_res->e_type =='input'){
                    log_message('error', 'only input '.$input_text);
                    $next_menu = $io_res->v_next;  // read comma seperated values. and convert into array 
                    $array_valid_input = explode(",", $io_res->v_valid_input) ;  // read comma seperated values. and convert into array 

                    if(in_array($user_input, $array_valid_input) ){
                        // if input is valid

                        if($input_text =='hi'){
                            $user_input_details = $this->db->get_where('bot_flow', array('v_items' =>$user_input ))->row();
                        }
                        else{
                            $user_input_details = $this->db->get_where('bot_flow', array('i_id' => $io_res->v_next, 'v_items' =>$user_input ))->row();

                        }
                        $next_to_menu = $user_input_details->v_next;

                        // Save Next action 
                        if($user_input_details->e_type !='taction' && $user_input_details->v_next > 0 ){
                            $data = array('next_action' => $user_input_details->v_next );
                            $this->io_model->store_last_selected_option($data, $conversation->id);
                        }

                        if($next_to_menu > 0):
                            $this->isMessageCreated($message, $conversation, $next_to_menu = false);
                        endif;

                         

                    }
                    else{
                        if($this->recursive_count == 0){  // not show if recusive function call only check with user input.
                            $this->io_model->save_wa_out_queue($conversation->id, 'Not A valid Input.');
                        }
                    }


                    // $data = array('last_input_value' => $input_text );
                    // $this->io_model->store_last_selected_option($data, $conversation->id);
                }
                else if($io_res->e_type =='menu'){
                    log_message('error', 'menu input '.$input_text);
                    // $this->send_relay($conversation->id, $io_res->v_name);
                    
                    // $this->io_model->save_wa_out_queue($conversation->id, $io_res->v_name);
                }
                else if($io_res->e_type =='a_input'){
                    $application_id = $user_input ;

                    if(!empty($application_id)){
                        

                        $io_res = $this->io_model->read_output_on_cutomer_input($input_text ='', $io_res->v_next);
                        $this->v_next = $io_res->v_next ;
                        $arr = explode(",", $this->v_next);
                        
                        $succ = $arr[0];
                        $fail = $arr[1];


                        $this->io_model->save_wa_out_queue($conversation->id, 'Please Wait...');
                        $result = $this->wa_model->api_to_get_application_status($conversation->id, $user_input, $io_res->v_items);

                        log_message('error', 'TACTION RESPONSE 1');
                        log_message('error', json_encode($result));

                        if($result['status'] =='success'){
                            // if($io_res->e_type !='taction' && $io_res->v_next > 0 ){
                                // $data = array('next_action' => $ss_res );
                                // $this->io_model->store_last_selected_option($data, $conversation->id);
                            // }
                            // $this->io_model->save_wa_out_queue($conversation->id, $result->messages);
                            $this->io_model->save_wa_out_queue($conversation->id, 'This is you aplication template status message, Application approved');
                            // get template message name 
                            $ss_res = $this->io_model->read_output_on_cutomer_input($input_text ='', $succ);
                            $this->hsm_message($bot_info->from, $ss_res->v_name , $param_count='', $param_arr='');
                            $data = array('next_action' => $ss_res->v_next );
                            $this->io_model->store_last_selected_option($data, $conversation->id);
                            $this->isMessageCreated($message, $conversation, $ss_res->v_next= false);
                            // here we need to change with template message 
                            // required things e.g ==> param : 4, 
                        }
                        else{
                            if($io_res->e_type !='taction' && $io_res->v_next > 0 ){
                                $data = array('next_action' => $fail );
                                $this->io_model->store_last_selected_option($data, $conversation->id);
                            }
                            $this->isMessageCreated($message, $conversation, $fail= false);
                            // $this->io_model->save_wa_out_queue($conversation->id, 'please wait...');
                        }

                    }
                    else{

                    }

                    log_message('error', 'a_input a_input a_input a_input '.$input_text);
                }

                    
                


            }
            else{


                if(!$this->wa_model->isBotSessionExist($message->from)){
                    // if boat not started show first message 
                    //$this->send_relay($conversation->id, 'Say hi to start chat with bot.');
                    $this->io_model->save_wa_out_queue($conversation->id, 'Say hi to start chat with bot.');
                }
                else{
                    log_message('error', 'INVALID REQUEST 22');    
                    // when bot start, no first message 
                    $this->io_model->save_wa_out_queue($conversation->id, 'I am sorry can\'t understand, invalid input.');

                     // if type not text invalid request 
                    // $this->send_relay($conversation->id, 'am sorry can\'t understand, invalid input ');
                }
            }

                
        }
        else{
            // if type not text invalid request 
            // $this->send_relay($conversation->id, 'sorry, we can\'t understand your input, Please select option or say hi to start conversation.');
            $this->io_model->save_wa_out_queue($conversation->id, 'sorry, we can\'t understand your input, Please select option or say hi to start conversation.');
            log_message('error', 'INVALID REQUEST ');
        }


    }

    public function readMessage($message_id){
        // Retrieves a message.

        // require(__DIR__ . '/../../autoload.php');

         $messageBird = new \MessageBird\Client('lJCQOmCOjaWKOzPx4jynuhT3M', null, [\MessageBird\Client::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX]);

        try {
            $message = $messageBird->conversationMessages->read($message_id);
            $this->wa_model->update_message_status($message);

            // print_r($message);
        } catch (\Exception $e) {
            echo sprintf("%s: %s", get_class($e), $e->getMessage());
        }
    }



    public function initiate_bot($v){                  
        if(!empty($v)){
            if(!$this->wa_model->isBotSessionExist($v->from)){
              $insert = array(
                'message_id' =>  $v->id, 
                'conversation_id' =>  $v->conversationId, 
                'channelId' => $v->channelId,
                'to' => $v->to,
                'from' => $v->from,
                'type' => $v->type,
                'content_text' => $v->content->text,
                'direction' => $v->direction,
                'status' => $v->status,
                'createdDatetime' => $v->createdDatetime,
                'updatedDatetime' => $v->updatedDatetime,
                'content_hsm' => $v->type == 'hsm' ? 'yes' : 'no',
                'namespace' => $v->type == 'hsm' ? $v->content->hsm->namespace : '',
                'templateName' => $v->type == 'hsm' ? $v->content->hsm->templateName : '',
                'language_code' => $v->type == 'hsm' ? $v->content->hsm->language->code : '',
                'params' => $v->type == 'hsm' ? json_encode($v->content->hsm->params) : '',
                'chat_session' => uniqid(),
                'session_start_time' => date('Y-m-d H:i:s', strtotime('now')),
                'platform' => $v->platform,
              );
              $this->db->insert('bot_chat_session', $insert);
              log_message('error', "LAST INSRET ");
              log_message('error', $this->db->last_query()); 
            }
        }
    }




    // After bot initiate 



   
    


                  


}

?>

