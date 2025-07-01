<?php
/***
 * Pdf Page 
 * Author: Aarti Ojha
 * Date: 07-10-2024
 * Description: This file for download case details in pdf  format also send mail with pdf
 */
   // Include autoloader 
   include_once("../config/web_mysqlconnect.php"); // Connection to database 
   require_once("web_function.php");
   require_once 'dompdf/autoload.inc.php'; 
   $ticket = $_GET['id']; 
   $res = mysqli_fetch_array(mysqli_query($link,"select * from $db.web_accounts a, $db.web_problemdefination p where a.AccountNumber=p.vCustomerID and p.ticketid='$ticket'; "));

   $date=$res['d_createDate'];
   $root_cause=$res['root_cause'];
   $category=category($res['vCategory']);
   $subcategory=subcategory($res['vSubCategory']);
   $remark=$res['vRemarks'];
   $v_OverAllRemark  = $res['v_OverAllRemark'];
   $mode=source($res['i_source']);
   $corrective_measure = $res['corrective_measure'];
   $ticketid_new = $ticket;

   // Reference the Dompdf namespace 
   use Dompdf\Dompdf; 
   // Instantiate and use the dompdf class 
   $dompdf = new Dompdf();
   
$html  ='<table border="1" cellpadding="2" cellspacing="2" style="width: -webkit-fill-available;">
          
        <tr>
          <td colspan="2"><center>CASE CLOSURE FORM</center></td>
        </tr>

        <tr>
          <td>Case ID</td>
          <td>'.$ticketid_new.'</td>
        </tr>
        <tr>
          <td>Category</td>
          <td>'.$category.'</td>
        </tr>
        <tr>
          <td>Sub Category</td>
          <td>'.$subcategory.'</td>
        </tr>
        <tr>
          <td>Root Cause</td>
          <td>'.$root_cause.'</td>
        </tr>
        <tr>
          <td>Corrective Measure</td>
          <td>'.$corrective_measure.'</td>
        </tr>
        <tr>
          <td>Case Origin</td>
          <td>'.$mode.'</td>
        </tr>
        <tr>
          <td>Case Creation Date & Time</td>
          <td>'.date('d-m-Y H:i:s',strtotime($date)).'</td>
        </tr>
        <tr>
          <td>Customer Name </td>
          <td>'.$res['fname'].'</td>
        </tr>
        <tr>
          <td>Email </td>
          <td>'.$res['email'].'</td>
        </tr>
        <tr>
          <td>Phone </td>
          <td>'.$res['phone'].'</td>
        </tr>
        <tr>
          <td>Remarks on Case Creation</td>
          <td>'.$remark.'</td>
        </tr>
        <tr>
          <td>Remarks on Case Closure</td>
          <td>'.$v_OverAllRemark.'</td>
        </tr>
        <tr>
          <td>Case Closed By</td>
          <td>'.agentname($res['back_office_action_by']).'</td>
        </tr>
        <tr>
          <td>Case Closure Date & Time</td>
          <td>'.date('d-m-Y H:i:s',strtotime($res['d_updateTime'])).'</td>
        </tr>

        </table>';


   // Load HTML content 
   $dompdf->loadHtml($html);   
   // (Optional) Setup the paper size and orientation 
   $dompdf->setPaper('A4', 'portrait'); 
    
   // Render the HTML as PDF 
   $dompdf->render(); 
    
    // Output the generated PDF 
     $output = $dompdf->output();

  //  $ticketname = str_replace('/', '-', $ticket);


   //file_put_contents('pdf/closure'.$ticketname.'.pdf', $output); // download file on server
   $dompdf->stream("closure_".$ticket.".pdf", array("Attachment" => true));// save file on system
  
   ?>

