<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
class WA_Setting extends Admin_Controller{
    
    private $access_key ;
    function __construct(){
        $this->access_key = 'lJCQOmCOjaWKOzPx4jynuhT3M';
    } 

    public function index(){
        echo "WA_Setting";
    }

    public function balance_check($value='')
    {

       $messageBird = new \MessageBird\Client($this->access_key); // Set your own API access key here.
        try {
            $balance = $messageBird->balance->read();
            print_r($balance);
        } 
        catch (\MessageBird\Exceptions\AuthenticateException $e) {
            // That means that your accessKey is unknown
            echo 'wrong login';
        } 
        catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }

    public function avialable_phone_number($value=''){
        echo '<pre>' ;
       $messageBird = new \MessageBird\Client($this->access_key);

        try {
            $phoneNumbers = $messageBird->availablePhoneNumbers->getList("nl", []);
            print_r($phoneNumbers);
        } 
        catch (\MessageBird\Exceptions\AuthenticateException $e) {
            print_r($e->getMessage());
            // That means that your accessKey is unknown
            print("wrong login\n");
        } 
        catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }

    /*public function chat_contact_list($value='')
    {
        $messageBird = new \MessageBird\Client($this->access_key); // Set your own API access key here.

        try {
            $chatContactResult = $messageBird->chatContacts->getList();
            print_r($chatContactResult);
        } 
        catch (\MessageBird\Exceptions\AuthenticateException $e) {
            // That means that your accessKey is unknown
            echo 'wrong login';
        } 
        catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function chat_platform_list($value='')
    {
        $messageBird = new \MessageBird\Client($this->access_key); // Set your own API access key here.

        $chatPlatform = new \MessageBird\Objects\Chat\Channel();
        try {
              $chatPlatformResult = $messageBird->chatPlatforms->getList();
            print_r($chatPlatformResult);
        } 
        catch (\MessageBird\Exceptions\AuthenticateException $e) {
            // That means that your accessKey is unknown
            echo 'wrong login';
        } 
        catch (\Exception $e) {
            echo $e->getMessage();
        }
    }*/

    public function contact_create($value='')
    {
        $messageBird = new \MessageBird\Client($this->access_key); // Set your own API access key here.
        echo '<pre>';
        $contact             = new \MessageBird\Objects\Contact();
        $contact->msisdn = "918878440134";
        $contact->firstName = "TESTING";
        $contact->lastName = "TESTING";
        // $contact->custom1 = "test_custom1";
        // $contact->custom2 = "test_custom2";
        // $contact->custom3 = "test_custom3";
        // $contact->custom4 = "test_custom4";


        try {
            $contactResult = $messageBird->contacts->create($contact);
            print_r($contactResult);
        } 
        catch (\MessageBird\Exceptions\AuthenticateException $e) {
            // That means that your accessKey is unknown
            echo 'Wrong login';
        } 
        catch (\Exception $e) {
            echo $e->getMessage();
        }

        /*MessageBird\Objects\Contact Object ( [id:protected] => d0b24db0305140b79a313c50ffd2032f [href:protected] => https://rest.messagebird.com/contacts/d0b24db0305140b79a313c50ffd2032f [msisdn] => 918878440134 [firstName] => TESTING [lastName] => TESTING [customDetails:protected] => stdClass Object ( [custom1] => [custom2] => [custom3] => [custom4] => ) [groups:protected] => stdClass Object ( [totalCount] => 0 [href] => https://rest.messagebird.com/contacts/d0b24db0305140b79a313c50ffd2032f/groups ) [messages:protected] => stdClass Object ( [totalCount] => 0 [href] => https://rest.messagebird.com/contacts/d0b24db0305140b79a313c50ffd2032f/messages ) [createdDatetime:protected] => 2021-08-07T12:03:08+00:00 [updatedDatetime:protected] => 2021-08-07T12:03:08+00:00 )*/
    }

    public function contact_get_groups($value=''){

        $messageBird = new \MessageBird\Client($this->access_key); // Set your own API access key here.
        echo '<pre>';

        try {
           $contactGroupsList = $messageBird->contacts->getGroups('d0b24db0305140b79a313c50ffd2032f');
            print_r($contactGroupsList);
        } 
        catch (\MessageBird\Exceptions\AuthenticateException $e) {
            // That means that your accessKey is unknown
            echo 'Wrong login';
        } 
        catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function contact_get_messages($value=''){
        
        $messageBird = new \MessageBird\Client($this->access_key); // Set your own API access key here.
        echo '<pre>';

        try {
            $contactMessageList = $messageBird->contacts->getMessages('d0b24db0305140b79a313c50ffd2032f');
            print_r($contactMessageList);
        } 
        catch (\MessageBird\Exceptions\AuthenticateException $e) {
            // That means that your accessKey is unknown
            echo 'Wrong login';
        } 
        catch (\Exception $e) {
            echo $e->getMessage();
        }
    }   
    
    public function contact_list($value=''){
        
        $messageBird = new \MessageBird\Client($this->access_key); // Set your own API access key here.
        echo '<pre>';

        try {
            $contactList = $messageBird->contacts->getList([]);
            print_r($contactList);
        } catch (\MessageBird\Exceptions\AuthenticateException $e) {
            // That means that your accessKey is unknown
            echo 'Wrong login';
        } catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }   

         

   
}

?>

