<?php
/***
 * CES NPS Feedback
 *Author: Aarti
 *Date: 10-04-2024
 *NPS & CUSTOMER EFFORT FORM Combine
 *email and one link for feedbacks - Customer Effort & NPS; to customers as per the mail send
 *
 * **/

  $url = $_SERVER['REQUEST_URI'];
  $cesid = $_GET['cesid'];
  $companyID = $_GET['company_id'];
  $Type = $_GET['Type'];
  
// added code for selecting  database name according to company id [vastvikta][26-02-2025]
  $functionFilePath = "IMApp/function.php";
  include_once($functionFilePath);
  
  
  $controller = new ChatRooms();

  $db = $controller->get_db_name($companyID);
  // echo "cpomm".$companyID;
  $ids = base64_decode($cesid);
  $flag = '0';
  $flag_1 = '0';
  // Data base connection start
  if(!empty($ids)){
    include_once("../config/web_mysqlconnect.php"); //  Connection to database // Please do not remove this 
    // customer effort code start
    $sql_document="select * from $db.tbl_customer_effort where id = $ids and flag = '1'";
    $record=mysqli_query($link, $sql_document);
    $numrow=mysqli_num_rows($record);
    if($numrow == '0' || $numrow == ''){
      $flag = '0';
    }else{
      $flag = '1';
    }

    // Nps code start
    $npsid = $_GET['npsid'];
    // for getting company id 
    $cus_id= $_GET['company_id'];
    $idnps = base64_decode($npsid);
    $sql_document="select * from $db.tbl_nps where id = $idnps and flag = '1'";
    $record=mysqli_query($link, $sql_document);
    $numrowx=mysqli_num_rows($record);
    if($numrowx == '0' || $numrowx == ''){
      $flag_1 = '0';
    }else{
      $flag_1 = '1';
    }
} 
?>
<!DOCTYPE html>    
<html>    
<head>    
<style> 
  .container {    
    border-radius: 5px;    
    background-color: #f2f2f2;    
    padding: 20px;    
  }
  /* Style the header */
  .header {
    background-color: #f1f1f1;
    padding: 20px;
    text-align: center;
  }
  .message{
    text-align: center;
      font-size: 15px;
      font-weight: 500;
  } 
  .message_nps{
    text-align: center;
      font-size: 15px;
      font-weight: 500;
  }
   .radio-button {
        opacity: 0;
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        margin: 0;
        cursor: pointer;
  }


  .radio-tile-group .input-container .radio-button:checked + .radio-tile {
      background-color: #079ad9;
      border: 2px solid #079ad9;
      color: white;
      transform: scale(1.1, 1.1);
  }
  .radio-tile-group .input-container .radio-button:checked + .radio-tile {
      background-color: #079ad9;
      border: 2px solid #079ad9;
      color: white;
      transform: scale(1.1, 1.1);
 }
 .btn 
  {
    width: -webkit-fill-available;
  }

  .label_1{
    background: #FB596C !important;
  }
  .label_2{
    background: #F8909E !important;
  }
  .label_3{
    background: #FACB68 !important;
  }
  .label_4{
    background: #76D4A2 !important;
  }
  .label_5{
    background: #11BA5D !important;
  }

  .btn-primary
  {
    width: initial;
  }
  .head 
  {
    color: dimgrey;
  }
  .detractors{
  background: #ff6575 !important;
}
.passives{
  background: #ffdc10 !important;
}
.promoters{
  background: #55e072 !important;
}
.slider-track{
  display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    flex-grow: 1;
    position: relative;
    width: 100%;
    border-radius: 32px;
    padding: 15px;
    box-shadow: 1px 1px 5px #0000001a;
    background: rgb(255,255,255);
}
label.btn.btn-secondarys {
    width: 57px;
    height: 36px;
    font-weight: 600;
}

.submit_form{
  background-color: #ff1d1e !important;
    border-color: #ff1d1e !important;
    color: #fff !important;
    border-radius: 6px !important;
    width: 97px !important;
    text-align: center;
}
</style>  
<meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>  
</head>    
<body>
<div class="header">
  <img class="logo" src="../public/images/ensembler-logo.png" alt="Logo" width="278" height="100">
</div>          
  <div class="container mt-3">
  <?php 
  if($flag == '1'){ 
  ?>
    <p id="thank-you-message" class="message">
      <span>Customer Effort Score (CES) form is already processed.</span><br/>
      Thank you for contacting us.
   </p>
  <?php }else{ ?>
    <p id="thank-you-message" class="message" style="display: none">
      Thank you for contacting us.
    </p>
    <div class="main_div">
          <div class="text-center head"><h4>Customer Effort Score (CES)<h4></div>
            <form name="customer_effort"  id="customer_effort">

              <input type="hidden" name="Type" id="Type" value="<?php echo $Type;?>">
              <input type="hidden" name="companyID" id="companyID" value="<?php echo $companyID;?>">
              <input type="hidden" name="user_id" id="user_ids" value="<?php echo $ids;?>">
              <input type="hidden" name="action" id="action" value="Add_CES">

              <div class="row justify-content-md-center">

                  <div class="col-sm-2 col-md-2 col-xl-2 mb-2">
                    <label class="btn btn-secondary label_1 text-white" for="option1">Very Low Effort</label>
                    <input type="radio" class="btn-check radio_button" name="options" id="option1" value="1">
                  </div>
                  <div class="col-sm-2 col-md-2 col-xl-2 mb-2">
                    <label class="btn btn-secondary label_2 text-white" for="option2">Low Effort</label>
                    <input type="radio" class="btn-check radio_button" name="options" id="option2" value="2">
                  </div>
                  <div class="col-sm-2 col-md-2 col-xl-2 mb-2">
                    <label class="btn btn-secondary label_3 text-white" for="option3">Neutral</label>
                    <input type="radio" class="btn-check radio_button" name="options" id="option3" value="3">
                  </div>
                  <div class="col-sm-2 col-md-2 col-xl-2 mb-2">
                    <label class="btn btn-secondary label_4 text-white" for="option4">High Effort</label>
                    <input type="radio" class="btn-check radio_button" name="options" id="option4" value="4">
                  </div>
                  <div class="col-sm-2 col-md-2 col-xl-2 mb-2">
                    <label class="btn btn-secondary label_5 text-white" for="option5">Very High Effort</label>
                    <input type="radio" class="btn-check radio_button" name="options" id="option5" value="5">
                  </div>
                  
              </div>
            </form>
            <div class="col-sm-12 col-md-12 col-xl-12 mb-2 text-center">
             <input name="submit_form" type="submit" value="Submit" class="btn btn-primary mt-3 submit_form_effort">
            </div>
      </div>
    </div>
  <?php }?>
  <div class="container mt-3">
    <?php if($flag_1 == '1'){ ?>
      <p id="thank-you-message" class="message_nps">
        <span>Net Promoter Score (NPS) form is already processed.</span><br/>
        Thank you for contacting us.
     </p>
    <?php }else{ ?> 
      <p id="thank-you-message" class="message_nps" style="display: none">
        Thank you for contacting us.
      </p>
    <div class="main_div_nps">
      <div class="text-center head"><h4>Net Promoter Score (NPS)</h4></div>
      <div class="text-center head"><p>"On a scale from 0-10, how likely are you to recommend this company to a friend or colleague?"</p></div>
      <form name="feedregistration"  id="feedregistration">
        <input type="hidden" name="Type" id="Type" value="<?php echo $Type;?>">
        <input type="hidden" name="companyID" id="companyID" value="<?php echo $companyID;?>">
        <input type="hidden" name="user_id" id="user_id" value="<?php echo $ids;?>">
        <input type="hidden" name="action" id="action" value="updateNpsFeddback">
        <div class="slider-track">
           
            <div class="slider_number">
              <label class="btn btn-secondarys detractors label0" for="option_0">0</label>
              <input type="radio" class="btn-check radio_button2" name="options" id="option_0" value="0">
            </div>
            <div class="slider_number">
              <label class="btn btn-secondarys detractors label1" for="option_1">1</label>
              <input type="radio" class="btn-check radio_button" name="options" id="option_1" value="1">
            </div>
            <div class="slider_number">
              <label class="btn btn-secondarys detractors label2" for="option_2">2</label>
              <input type="radio" class="btn-check radio_button2" name="options" id="option_2" value="2">
            </div>
            <div class="slider_number">
              <label class="btn btn-secondarys detractors label3" for="option_3">3</label>
            <input type="radio" class="btn-check radio_button2" name="options" id="option_3" value="3">
          </div>
            <div class="slider_number">
              <label class="btn btn-secondarys detractors label4" for="option_4">4</label>
             <input type="radio" class="btn-check radio_button2" name="options" id="option_4" value="4" autocomplete="off">
           </div>
            <div class="slider_number">
              <label class="btn btn-secondarys detractors label5" for="option_5">5</label>
              <input type="radio" class="btn-check radio_button2" name="options" id="option_5" value="5" autocomplete="off">
            </div>
            <div class="slider_number"><label class="btn btn-secondarys detractors label6" for="option_6">6</label>
             <input type="radio" class="btn-check radio_button2" name="options" id="option_6" value="6" autocomplete="off">
           </div>
            <div class="slider_number">
              <label class="btn btn-secondarys passives label7" for="option_7">7</label>
             <input type="radio" class="btn-check radio_button2" name="options" id="option_7" value="7" autocomplete="off">
           </div>
            <div class="slider_number">
              <label class="btn btn-secondarys passives label8" for="option_8">8</label>
             <input type="radio" class="btn-check radio_button2" name="options" id="option_8" value="8" autocomplete="off">
           </div>
            <div class="slider_number">
              <label class="btn btn-secondarys promoters label9" for="option_9">9</label>
             <input type="radio" class="btn-check radio_button2" name="options" id="option_9" value="9" autocomplete="off">
           </div>
            <div class="slider_number">
              <label class="btn btn-secondarys promoters label10" for="option_10">10</label>
             <input type="radio" class="btn-check radio_button2" name="options" id="option_10" value="10" autocomplete="off">
           </div>
        </div>
        <span id="captcha" style="color:red"></span> <!-- this will show captcha errors -->
      </form>
       <div class="col-sm-12 col-md-12 col-xl-12 mb-2 text-center">
         <input name="submit_form" type="submit" value="Submit" class="btn btn-primary mt-3 Nps_submit_form">
        </div>
  <?php }?>
  </div>
  </div>
</body>    
</html>  
<!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->
<script src="https://code.jquery.com/jquery-2.1.1.min.js" type="text/javascript"></script>
<script src="../public/js/common.js"></script>
<script type="text/javascript">
  // Customer effort Js code
   $('#customer_effort .radio_button').click(function() {
    console.log('customer_effort');
    var id = $(this).val()
    console.log(id);
    $('.btn-secondary').css('box-shadow', 'unset')
    $('.label_'+id).css('box-shadow', '0 0 0 0.2rem rgb(10 9 20 / 26%)');
  })

   // Nps js code
  $('#feedregistration .radio_button2').click(function() {
    console.log('feedregistration');
    var id = $(this).val();
    console.log(id);
    $('.btn-secondarys').css('box-shadow', 'unset');
    $('.label'+id).css('box-shadow', '0 0 0 0.2rem rgb(10 9 20 / 26%)');
  })
</script>


