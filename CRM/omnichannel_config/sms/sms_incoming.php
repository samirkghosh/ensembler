<?php
/**
 * SMS Incoming
 * Author: Aarti Ojha
 * Date: 01-07-2024
 * Description: This file handles Social Media API Data for Outgoing and Incoming Response Store in the database.
 * 
 * Please do not modify this file without permission.
 */
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

global $last_insert_id;
echo "################### STARTSMS Getting Code #######################";echo"<br>";
include_once("../../../config/web_mysqlconnect.php"); // Include database connection file

$url = 'http://172.17.3.218/smsservice/pullsms';
$shortcode = '4111';
$username = 'ZRA';
$password = 'ZrA@22!!';
$last_insert_id = '0';

$data_curl['auth']['username'] = $username;
$data_curl['auth']['password'] = $password;
$data_curl['auth']['filters']['shortcode'] = $shortcode;
echo "### SMS Curl Connection Start ###";echo"<br/><br/>";
$n = 1;
$limit = 20;
for ($i=0; $i<$n; $i++) { 
  $n++;
  # code...
    $sql_qry1 = "SELECT * FROM $db.tbl_smsmessagesin ORDER BY i_id DESC LIMIT 1";
    $res11 = mysqli_query($link,$sql_qry1);
    $numrow1 = mysqli_num_rows($res11);
    $row=mysqli_fetch_array($res11);
    if($numrow1>0){
      $last_sms_id = $row['sms_id'];
    }else{
      $last_sms_id = '0';
    } 
    $data_curl['auth']['filters']['last_sms_id'] = $last_sms_id;
    $data_curl['auth']['filters']['limit'] = $limit;
    $data_curl_json = json_encode($data_curl);
    
    echo "### curl hit post data format ###";echo"<br/><br/>"; 
    print_r($data_curl_json);
    // $curl = curl_init();
    // curl_setopt_array($curl, array(
    //   CURLOPT_URL => $url,
    //   CURLOPT_RETURNTRANSFER => true,
    //   CURLOPT_ENCODING => '',
    //   CURLOPT_MAXREDIRS => 10,
    //   CURLOPT_TIMEOUT => 0,
    //   CURLOPT_FOLLOWLOCATION => true,
    //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //   CURLOPT_CUSTOMREQUEST => 'POST',
    //   CURLOPT_POSTFIELDS =>$data_curl_json,
    //   CURLOPT_HTTPHEADER => array(
    //     'Content-Type: application/json',
    //     'Cookie: MUTUMIKI=abe0ifme1anrj5k7vccf94trqn'
    //   ),
    // ));
    // $response = curl_exec($curl);
    // curl_close($curl);
    // echo $response;
    // $all_Sms = json_decode($response);
    $all_Sms2 = '{"messages":[{"inbox":{"id":"60803","date_received":"1674122663","sms_body":"%a","mobile":"+260960709902","keyword_id":"0","mobile_operator":"UNKNOWN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60804","date_received":"1676565899","sms_body":"2010","mobile":"+260761468629","keyword_id":"0","mobile_operator":"UNKNOWN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60805","date_received":"1677313127","sms_body":"","mobile":"+260768449542","keyword_id":"0","mobile_operator":"UNKNOWN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60806","date_received":"1677314980","sms_body":"Test","mobile":"+260761543693","keyword_id":"0","mobile_operator":"UNKNOWN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60807","date_received":"1677315182","sms_body":"%a","mobile":"%q","keyword_id":"0","mobile_operator":"UNKNOWN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60808","date_received":"1677737364","sms_body":"Test","mobile":"+260960709902","keyword_id":"0","mobile_operator":"MTN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60809","date_received":"1678091017","sms_body":"MTN Time2Share has been moved to *119#. Please dial *119#.","mobile":"+260964558279","keyword_id":"0","mobile_operator":"MTN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60810","date_received":"1679117640","sms_body":"STOPW","mobile":"+260968485150","keyword_id":"0","mobile_operator":"MTN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60811","date_received":"1679408465","sms_body":"","mobile":"+260769537667","keyword_id":"0","mobile_operator":"UNKNOWN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60812","date_received":"1679410652","sms_body":"","mobile":"+260961266010","keyword_id":"0","mobile_operator":"MTN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60813","date_received":"1679410662","sms_body":"","mobile":"+260961266010","keyword_id":"0","mobile_operator":"MTN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60814","date_received":"1679410675","sms_body":"","mobile":"+260961266010","keyword_id":"0","mobile_operator":"MTN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60815","date_received":"1680025563","sms_body":"","mobile":"+260768270602","keyword_id":"0","mobile_operator":"UNKNOWN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60816","date_received":"1680029554","sms_body":"","mobile":"+260963562528","keyword_id":"0","mobile_operator":"MTN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60817","date_received":"1680099278","sms_body":"Afternoonam trying to call but its not goingthrough","mobile":"+260966892053","keyword_id":"0","mobile_operator":"MTN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60818","date_received":"1680099653","sms_body":"Test","mobile":"+260761543693","keyword_id":"0","mobile_operator":"UNKNOWN","publication":true,"reply_status":"0","agent_id":"0"}},{"inbox":{"id":"60822","date_received":"1680170142","sms_body":"Test","mobile":"+260761543693","keyword_id":"27","mobile_operator":"UNKNOWN","publication":true,"reply_status":"0","agent_id":"0"}}],"last_sms_id":"0","error":0}';
    $all_Sms = json_decode($all_Sms2,true);
    print_r($all_Sms2);
    // $all_Sms = '{"messages":[],"last_sms_id":"61215","error":0}';
    if(!empty($all_Sms['messages'])){
      echo"<br/><br/>";echo "### data availble in sms api  ###";echo"<br/><br/>";
      foreach($all_Sms['messages'] as $sms_list_array){
          $sms_list = $sms_list_array['inbox'];
          $sms_id = $sms_list['id'];
          $sql_qry = "SELECT sms_id FROM $db.tbl_smsmessagesin where sms_id = '".$sms_id."'";
          $res1 = mysqli_query($link,$sql_qry);
          $numrow = mysqli_num_rows($res1);
          if ( $numrow > 0){
            print "*** Already downloaded.seqno: sms_id=$sms_id\n";echo"<br/>";
            print "*******************************************\n";echo"<br/>";
            continue;
          }else{
            print "*** Start to inserting data in tbl_smsmessagesin table *** \n";echo"<br/><br/>";
              echo  'SmsId = '.$sms_list['id'].'<br />';
              echo  'Date_received = '.$sms_list['date_received'].'<br />';
              echo  'Mobile = '.$sms_list['mobile'].'<br />';

              $date_received = $sms_list['date_received'];
              $date_received1 = date('Y-m-d H:i:s',$date_received); 
              $sms_body = $sms_list['sms_body'];
              $mobile = $sms_list['mobile'];
              $mobile = str_replace("+260", "",$mobile);
              $keyword_id = $sms_list['keyword_id'];
              $Extra = 'mobile_operator:'.$sms_list['mobile_operator'].',publication'.$sms_list['publication'].',reply_status'.$sms_list['reply_status'].',agent_id'.$sms_list['agent_id'];
              $insertdate = date('Y-m-d H:i:s'); 

              $sql_insert_information="INSERT INTO $db.tbl_smsmessagesin (v_mobileNo, v_smsString, d_timeStamp, v_msgkey, sms_id,insertdate,extra_field) VALUES ('$mobile','$sms_body', '$date_received1', '$keyword_id', '$sms_id', '$insertdate','$Extra')";
              echo "<br><br>";
              $result_insert=mysqli_query($link,$sql_insert_information) ;
              if ( $result_insert == FALSE){
                echo "Unable to insert:".$sql_insert_information."Error:";
                print("\n");
                exit();
              }
              // echo $sql_insert_information; die;

          }
      }
    }else{
      echo "sms data is blank.. <br>";
      break;
    }
}