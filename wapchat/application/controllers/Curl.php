<?php

class Curl extends CI_Controller {

     public function __construct() {
       parent::__construct();
    }

    public function curPostRequest($url_endpoint = '', $method, ) {
        /* Endpoint */
        $url = 'http://www.165.232.183.220.com/endpoint';
   
        /* eCurl */
        $curl = curl_init($url);
   
        /* Data */
        $data = [
            'name'=>'John Doe', 
            'email'=>'johndoe@yahoo.com'
        ];
   
        /* Set JSON data to POST */
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            
        /* Define content type */
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        /*curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'App-Key: JJEK8L4',
            'App-Secret: 2zqAzq6'
        ));*/
            
        /* Return json */
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
        /* make request */
        $result = curl_exec($curl);
             
        /* close curl */
        curl_close($curl);
         
    }

     public function getExample()
    {
        /* API URL */
        $url = 'http://www.abc.com/api';
   
        /* Init cURL resource */
        $curl = curl_init($url);
   
       curl_setopt_array($curl, array(
         CURLOPT_URL => $url,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => "",
         CURLOPT_TIMEOUT => 30000,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => "GET",
         CURLOPT_HTTPHEADER => array(
          // Set Here Your Requesred Headers
             'Content-Type: application/json',
         ),
     ));
            
        /* execute request */
        $result = curl_exec($curl);
             
        /* close cURL resource */
        curl_close($curl);
 
    }

    // https://weichie.com/blog/curl-api-calls-with-php/
    
    function callAPI($method, $url, $data){
       $curl = curl_init();
       switch ($method){
          case "POST":
             curl_setopt($curl, CURLOPT_POST, 1);
             if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
             break;
          case "PUT":
             curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
             if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);                              
             break;
          default:
             if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
       }
       // OPTIONS:
       curl_setopt($curl, CURLOPT_URL, $url);
       curl_setopt($curl, CURLOPT_HTTPHEADER, array(
          'APIKEY: 111111111111111111111',
          'Content-Type: application/json',
       ));
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
       // EXECUTE:
       $result = curl_exec($curl);
       if(!$result){die("Connection Failure");}
       curl_close($curl);
       return $result;
    }

    

}

?>