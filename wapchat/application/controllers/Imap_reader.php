<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**************************************************************************************
* FileName: Imap_reader.php
* Purpose: Readed mails from server and added to the in-queue email list for forther processing
*       Uses only IMAP server
*       Reads only UNSEEN mail
*       Download all attchment and body
*       Store information in database
*       Mark it is as SEEN
*
*   [18-06-2021]    [SG]    Modified the existing code 
************************************************************************************************/

class Imap_reader extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		define ("DOWNLOADPATH", "/var/www/html/zra");
        define ("DBG","0");
        error_reporting(0);

        /**********************************************************************
        * define message types comes in header for processing
        ***************************************************************************/
        $message = array();
        $message["attachment"]["type"][0] = "text";
        $message["attachment"]["type"][1] = "multipart";
        $message["attachment"]["type"][2] = "message";
        $message["attachment"]["type"][3] = "application";
        $message["attachment"]["type"][4] = "audio";
        $message["attachment"]["type"][5] = "image";
        $message["attachment"]["type"][6] = "video";
        $message["attachment"]["type"][7] = "other";

	}

    ###########################################################
    ### cron script to send puch from email to sms out queue
    ### This is not schedule, this send emidiate 
    ### Need to Set in cron. 

    public function email_to_sms_out_queue(){
        $arr = array('status' => '0');
        $result = $this->db->get_where('tbl_email_in_queue', $arr);
        log_message('error', 'email_to_sms_out_queue');
        log_message('error', "NUM ROWS ".$result->num_rows() );
        if($result->num_rows() > 0 ){
            $rows = $result->result();
            foreach ($rows as $key => $value) {
                $to = trim(str_replace("Fwd: ","",$value->v_subject));
                $send_to = trim(str_replace("Re: ","",$to));

                $data = array(
                    'send_to'=> $send_to,
                    'send_from'=>$value->v_from,
                    'message' => trim($value->body),
                    'message_type_flag'=>'0',
                    'status'=>'0',
                    'schedule_flag' => 1,
                    'schedule_time' => date('Y-m-d H:i'),
                    'created_by'  => 1,
                    'message_type_flag'  => 3,
                );
                
                if($this->db->insert('sms_out_queue', $data)){
                    $this->db->where('id',$value->id);
                    $this->db->update('tbl_email_in_queue', array('status' => '1'));
                }
            }
        }
    }

    ###########################################################
    ### cron script to read imap email to send email to sms 
    ### save in tbl_email_out_queue    
    ### Need to Set in cron.

    public function sync_imap_records($value='')
    {
        // phpinfo();
        // echo "IMAP ";
        $data = $this->fetch_imap_message();
       
        if(count($data) > 0 || !empty($data)){
            // print_r($data);
            log_message('error', 'Read email to sms');
            log_message('error', json_encode($data));
            $this->db->insert_batch('tbl_email_in_queue',$data);
              log_message('error', 'INSTER QUERY ');
              log_message('error', $this->db->last_query());

            //echo $this->db->last_query();
        }
    }


    public function fetch_imap_message($value=''){

        /************************************************************************************
        * Get the IMAP credential from the table for accessing the mail server
        ***************************************************************************************/
        // $sql_connect_result="SELECT v_ipaddress,v_username,v_pasowrd FROM zra_master.tbl_connection";

        $row_result_connect = $this->db->get('imap_settings')->row_array();
        // $result_connect_result =mysql_query($sql_connect_result);

        /*********************************************************************************************
         Process for each email server
        ********************************************************************************************/
        // while($row_result_connect=mysql_fetch_array($result_connect_result))
        // {
            //$hostname             ='{imap.rediffmailpro.com:993/imap/ssl/novalidate-cert}INBOX';


            /*$hostname               =$row_result_connect['v_ipaddress'];
            $username               =$row_result_connect['v_username'];
            $password               =$row_result_connect['v_pasowrd'];*/

            $hostname               =get_settings('imap_host');
            $username               =get_settings('imap_email');
            $password               =get_settings('imap_pass');
            log_message('error', "HOST ".$hostname);
            log_message('error', "USER ".$username);
            log_message('error', "PASS ".$password);
            
            /* connect to mail server */
            $inbox = imap_open($hostname,$username,$password) or die('Cannot connect to mail server: ' . imap_last_error());
             
            // echo "Check INBOX<br>" ;
            // print_r(imap_errors());
            // echo "Check INBOX<br> errr" ;

            /* grab emails */

            // $emails = imap_search($inbox,'ALL');
            $emails = imap_search($inbox,'UNSEEN');
            // echo "CHEKCK EMAIL coount ".count($emails) ;
            

            /* if emails are returned, cycle through each... */
            if(!empty($emails)){

                /* begin output var */
                $output = '';
            
                log_message('error', "EMAIL COUNT ".count($emails)); 
                $save_arr = [];
                $total_inserted = 0; 
                $total_already_inserted = 0;
                rsort($emails); // 

                /* for every email... */
                foreach($emails as $email_number) 
                {

                    $iUID = imap_UID($inbox, $email_number);
                    
                    log_message('error', "Email no: $email_number, UID=$iUID");
                    /************************************************************************
                    * using  UID check if the mail is already there
                    ***********************************************************************************/
                    
                    /*$sql_qry = "SELECT i_UID FROM zra_master.web_email_information where i_UID = '".$iUID."'";
                    $res = mysql_query($sql_qry);
                    $numrow = mysql_num_rows($res);
                    if ( $numrow > 0)
                    { 
                        $header = imap_headerinfo($inbox,$email_number,0); 
                        print "*** Already downloaded.seqno: $email_number, UID=$iUID\n";
                        print_r($header);
                        print "*******************************************\n";
                        $total_already_inserted++;

                        continue;

                    }
                    else
                        print "*** New Message: $iUID\n";*/

                    $header = imap_headerinfo($inbox,$email_number,0); 
                    
                    if(DBG)
                    {
                         print_r($header);
                        log_message('error', json_encode($header));

                    }

                    $overview = imap_fetch_overview($inbox,$email_number,0); 
                    $oMsg = "";

                    //$message = nl2br(getMessageContent($inbox,$email_number, $oMsg)); //imap_fetchbody($inbox,$email_number,2);
                    //[25-05-2020]  [SG] why nl2br????

                    // log_message('error', json_encode($overview));

                    $message = $this->getMessageContent($inbox, $email_number, $oMsg); //imap_fetchbody($inbox,$email_number,2);
                    $entry_log="Orginalmessage ".$oMsg;
                    //print_r($message);die();
                
                    $from = $header->from[0]->personal;
                    $from = ($this->validateEmail($from)) ? $from : $header->from[0]->mailbox."@".$header->from[0]->host;      

                    $to   = $header->to[0]->mailbox."@".$header->to[0]->host;
                    $date = date('Y-m-d H:i:s', strtotime($header->Date)); 

                    /* output the email header information */
                    $subject = $overview[0]->subject;
                    /*
                    $subject_t=imap_utf8($overview[0]->subject);
                    if($part->type==0)
                    {
                        $subject = imap_utf8($overview[0]->subject);
                    }
                    else
                    {
                        $subject = $overview[0]->subject;
                    }   
                    */
                    //print_r($subject);

                    $entry_log.="\n\nOrginal subject- \n\n".$subject."\nPart type-".$part->type."\n\n".$subject_t;
                    //$subject =subject_decodestring($subject1);//new function added to remove utf8 on 17Jan20
                    // print_r($message);die();
                
                    //$from = $overview[0]->from;
                    $subject = addslashes($subject);
                    $body    = addslashes($message);
                    $undeliverablearray=explode(":",$subject);
                    $undeliverable = $undeliverablearray[0];
                    $entry_log = addslashes($entry_log);
                    
                    // log_message('error', 'BODY');
                    // log_message('error', json_encode($body));
                
                    /* ***** */


                    /******* */
                    /* fetch attactment */
                    //$structure = imap_fetchstructure($inbox, $email_number);    

                    //$parts = $structure->parts; 

                    $fpos=2; 
                    $multifiles="";

                    /* Save all the attachments in the download folder for this mail */
                    /*for($ii = 1; $ii < count($parts); $ii++)
                    { 
                        //$message["pid"][$ii] = ($ii); //echo "ii > ".$ii;
                        $part = $parts[$ii]; 
                        //$part->disposition="";

                        if(strtolower($part->disposition) == "attachment") 
                        {
                                            
                            $ext=$part->subtype;
                            $params = $part->dparameters; //print_r($params);

                             // Store the files in imap folder 
                            $filename="imap/".$part->dparameters[0]->value;
                            if ( strstr($filename,"UTF"))
                            {
                                
                                $filePath = tempnam("/var/www/html/zra/imap","attach_");
                                $filePath = $filePath.".$ext";
                                $filename = strstr($filePath,"imap");
                            }
                            else
                            {
                                $filename = "$filename"; //echo 'type > '.$part->type.'<br>';
                                $filePath= "/var/www/html/zra/".$filename;

                                if ( file_exists($filePath) )
                                {
                                    $filePath = tempnam("/var/www/html/zra/imap","attach_");
                                    $filePath = $filePath.".$ext";
                                    $filename = strstr($filePath,"imap");

                                }
                            }
                                                                
                            $mege="";
                            $data="";

                            $mege = imap_fetchbody($inbox,$email_number,$fpos);  


                            $data = getdecodevalue($mege,$part->type);  
                            log_message('error', "FileName: $filePath")
                            
                            $fp = fopen($filePath, w);
                            fputs($fp,$data);
                            fclose($fp);

                            $fpos+=1;   
                            
                            $multifiles = ($multifiles=="") ? $filename : $multifiles.",".$filename;        
                        }
                    }*/
                    /* end fetching attachment */

                    /* get information specific to this email */
                    //echo "<pre>";
                
                    $total_inserted ++;

                    /* insert the mail informatin in the mail queue */
                    $imap_data['v_to']              =  $to ;
                    $imap_data['v_from']            =  $from ;
                    $imap_data['create_date']       =  $date ;
                    $imap_data['v_subject']         =  $subject ;
                    $imap_data['body']              =  $body ;
                    $imap_data['last_email_id']     =  $iUID ;
                    $imap_data['orginal_subject']   =  $entry_log ;
                    

                    log_message('error' , 'Check Array ' ) ;
                    // log_message('error' , json_encode($imap_data) ) ;
                    array_push($save_arr, $imap_data);
                    /*if($iUID > 177){
                    }*/


                     
                } // for ecach

                log_message('error' , 'Check Array final ' ) ;
                    // log_message('error' , json_encode($save_arr) ) ;
                
                log_message('error', "Total Inserted -> ".$total_inserted);
                log_message('error', "Already Inserted -> ".$total_already_inserted);
                log_message('error', "*************************************************************");
            
            }  // end if email
            
            /* close the connection */
            imap_close($inbox);

            return $save_arr ;
        //} // end while

        //mysql_close($link);
    }


    /***********************************************************************
    * Note: all the functions used is defined below
    *******************************************************************************/
    function log1($msg)
    {
        if (DBG)
        {
            // file_put_contents("/tmp/imap.log", $msg, FILE_APPEND);
            // file_put_contents("/tmp/imap.log","\n--\n", FILE_APPEND);
        }

    }


    /*****************************************/
    function subject_decodestring($subject)
    {

        $utf = substr($subject, 0, 10);
        if(strcasecmp($utf, "=?utf-8?B?") == '0')
        {
            // $subject = base64_decode(str_ireplace("=?UTF-8?", "",$subject));
            $d = str_ireplace("=?utf-8?B?", "",$subject);//echo  $d."<br>";
            return base64_decode($d);
        }
        return $subject ;

    }

     
    /* function */

    function validateEmail($email)
    {
        // SET INITIAL RETURN VARIABLES

            $emailIsValid = FALSE;

        // MAKE SURE AN EMPTY STRING WASN'T PASSED

            if (!empty($email))
            {
                // GET EMAIL PARTS

                    $domain = ltrim(stristr($email, '@'), '@');
                    $user   = stristr($email, '@', TRUE);

                // VALIDATE EMAIL ADDRESS

                    if
                    (
                        !empty($user) &&
                        !empty($domain) &&
                        checkdnsrr($domain)
                    )
                    {$emailIsValid = TRUE;}
            }

        // RETURN RESULT

            return $emailIsValid;
    }

    function getdecodevalue($message,$coding)
    {
        //return quoted_printable_decode($message);
        switch($coding) {
        case 0:
            $message = quoted_printable_decode($message);
            break;
        case 1:
            $message = imap_8bit($message);
            break;
        case 2:
            $message = imap_binary($message);
            break;
        case 3:
            $message = imap_base64($message);
            break;
        case 5:
            $message = imap_base64($message);
            break;
        case 6:
        case 7:
            $message=imap_base64($message);
            break;
        case 4:
            $message = imap_qprint($message);
            break;
        }
        return $message;
    }

    function getMessageContent($mail,$id, &$orgMsg)
    {
        
        // Get content of text message.
       
        $mid = $id;
        
        $struct = imap_fetchstructure($mail, $mid);
        $parts = $struct->parts;
       
        $i = 0;
        if (DBG == 1)
        {
            echo "Single Part\n";
            print_r($struct);
            echo "Single Part ends\n";
        }

        //  log1("Parts:".print_r($parts));
        if (!$parts)
        {
            // log_message('error', 'IN IF CONDITION');

            /* Simple message, only 1 piece */
            $attachment = array(); /* No attachments */
            $content = imap_body($mail, $mid);
            $this->log1("Content:".$content);
            //echo 'OK FINE 222 3322' ;die();
        }
        else
        {

            /* Complicated message, multiple parts */
            $endwhile = false;
        
            $stack = array();       /* Stack while parsing message */
            $content = "";          /* Content of message */
            $attachment = array();  /* Attachments */
        
            while (!$endwhile)
            {

                if (!$parts[$i])
                {   
                    log_message('error', 'COunt of STACK '.count($stack));
                    if (count($stack) > 0)
                    {
                        $parts = $stack[count($stack)-1]["p"];
                        $i = $stack[count($stack)-1]["i"] + 1;
                        array_pop($stack);
                    }
                    else
                    {
                        $endwhile = true;
                    }
                    //echo 'SETP 2' ;die();
                }
        
                if (!$endwhile)
                {
                   
                    /* Create message part first (example '1.2.3') */
                    $partstring = "";
                    foreach ($stack as $s)
                    {
                        $partstring .= ($s["i"]+1) . ".";
                    }
                    $partstring .= ($i+1);
                  
                    

                    if (isset($parts[$i]->disposition) && strtoupper($parts[$i]->disposition) == "ATTACHMENT" && $parts[$i]->ifparameters != 0) 
                    { /* Attachment */
                        if (DBG)
                        {
                            echo "******xxxxxxx********************[$i]\n";
                            print_r($parts[$i]);
                        }
                        /* decode the file name */
                        $x = imap_mime_header_decode($parts[$i]->parameters[0]->value);
                        
                        //$attachment[] = array("filename" => $parts[$i]->parameters[0]->value,
                        $attachment[] = array("filename" => $x[0]->text, "filedata" => imap_fetchbody($mail, $mid, $partstring));
                        if(DBG)
                        {
                            echo "**************************\n";
                        }
                        // log_message('error', 'Create message part first VIJAY 1111'); 
                    }
                    elseif (strtoupper($parts[$i]->subtype) == "PLAIN")
                    { 
                        /* Message */
                        //$content .= imap_fetchbody($mail, $mid, $partstring);
                        //[25-05-2020]  [SG] check for encoding and decode
                        $msg = imap_fetchbody($mail, $mid, $partstring);
                        //$this->log1("PLAIN:".$content);
                        if( $parts[$i]->encoding == 3){
                            $msg = base64_decode($msg);
                        }

                        $content .= $msg;
                        // log_message('error', 'Create message part first VIJAY 2222'); 
                        //$this->log1("PLAIN1:".$content);
                    }
                }
                // echo 'SETP 5566 -- -- 1' ;die();
                /*log_message('error', 'Create message part first 33333333333333 '.$i);    
                log_message('error', json_encode($parts[$i]));    
                log_message('error', 'Create message part first 444444444444444444 '.$i); */
                
                if(isset($parts[$i]->parts)){
                    log_message('error', json_encode($parts[$i]->parts));    
                }   

                //log_message('error', json_encode($parts->parts));
                // echo 'SETP 5566================' ;die();

                // if ($parts[$i]->parts)
                if (isset($parts[$i]->parts)) // Update Vijay : 19-06-2021
                {
                    // log_message('error', 'Create message part first 55555555555555555'); 
                    $stack[] = array("p" => $parts, "i" => $i);
                    $parts = $parts[$i]->parts;
                    $i = 0;
                    // log_message('error', 'STACK ');
                    // log_message('error', json_encode($stack));
                }
                else
                {
                    // log_message('error', 'Create message part first 6666666666666666666666'); 
                    $i++;
                }
            }    
        } /* while */
          /* complicated message */
        
        // Convert quoted-printable characters in text content
        $orgMsg = $content;
        if (DBG)
        {
           // echo "[$content]";
        }

        $x = imap_mime_header_decode($content);
        $out="";
        for ( $i = 0; $i < count($x); $i++)
            $out=$out.' '. $x[$i]->text;
        //$Body_text = quoted_printable_decode($content);
        return $out;

    }


    /********************************************************************************
    *
    ******************************************************************************************/
    function is_base64_encoded($data)
    {
            if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
                return TRUE;
            } else {
                return FALSE;
            }
    }


    /***************************************************************************************
    *********************************************************************************************/

    function getStringBetween($str,$from,$to)
    {
        $sub = substr($str, stripos($str,$from)+strlen($from),strlen($str));
        return substr($sub,0,stripos($sub,$to));
    }

    /* end function */

    

     
}
