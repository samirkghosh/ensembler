<?php 
include("../../config/web_mysqlconnect.php");
$recipient_id=$_REQUEST['comment_id'];
$id=$_REQUEST['id'];//caseid
$msg="";
/*change flog for unread post/comment*/
mysqli_query($link,"update $db.tbl_facebook set flag_read_unread='3' where comment_id='".$recipient_id."' || post_id = '".$recipient_id."'");
if(isset($_REQUEST['dm_button']))
{
  if($_POST['comment_id']!="" && trim($_POST["dm_message"])!="")
  {
    $sql = "SELECT * from $db.tbl_facebook where comment_id='".$recipient_id."' || post_id = '".$recipient_id."'";
    $query_1=mysqli_query($link,$sql);
    $rowt=mysqli_fetch_assoc($query_1);
    $name = '';
    $userid = $rowt['userid'];
    $date = date("Y-m-d H:i:s");
    $comment = $_POST["dm_message"];
    $post_id = $rowt['post_id'];
    $parent_comment_id = $recipient_id;
    $msg = $rowt['msg'];
    $comment_id = '';
    

    $insert = "insert into $db.tbl_facebook ( name , comment , createddate , post_id , comment_id ,parent_comment_id, post , userid,comment_type, sent_flag,msg_flag,flag_read_unread) values( '$name' , '$comment' , '$date' , '$post_id' , '$comment_id','$parent_comment_id' , '$msg' , '$userid','multiple','0' ,'OUT','3')";
    mysqli_query($link,$insert);
    $msg="Message sent sucessfully !!";
  }
}
  
  if($_REQUEST['flag'] == '1'){
    $comments = "post_id ='".$recipient_id."'";
  }else{
    $comments = "comment_id ='".$recipient_id."'";
  }
  $select_qry = mysqli_query($link,"select * from $db.tbl_facebook where $comments and id!='' and i_deletestatus!='0' and comment_type IS null and parent_comment_id='' $str order by createddate ASC ");
   $facebook_data = array();
   $i=0;
  while($row = mysqli_fetch_array($select_qry)){
     $caseee = $row['ICASEID'];
     $count=$count+1; 
     $check="check".$count;
     if($count%2==0) $clr="#efefef"; else $clr="#ffffff";

      $facebook_data['parent'][$i]['id'] = $row['id'];    
      $facebook_data['parent'][$i]['name'] = $row['name'];
      $facebook_data['parent'][$i]['comment'] = $row['comment'];
      $facebook_data['parent'][$i]['createddate'] = $row['createddate'];
      $facebook_data['parent'][$i]['comment_id'] = $row['comment_id'];
      $facebook_data['parent'][$i]['post_id'] = $row['post_id'];
      $facebook_data['parent'][$i]['status'] = $row['status'];
      $facebook_data['parent'][$i]['post'] = $row['post'];
      $facebook_data['parent'][$i]['attachment'] = $row['attachment'];
      $facebook_data['parent'][$i]['flag_read_unread'] = $row['flag_read_unread'];
      $facebook_data['parent'][$i]['msg_flag'] = $row['msg_flag'];
      $comment_id = $row['comment_id'];
      $select_qry2 = mysqli_query($link,"select * from $db.tbl_facebook where id!='' and i_deletestatus!='0' and parent_comment_id='".$comment_id."'");
      $num_rows=mysqli_num_rows($select_qry2);
      if($num_rows>0){
        while($childlist = mysqli_fetch_array($select_qry2)){
          $i++;
          $facebook_data['parent'][$i]['id'] = $childlist['id'];   
          $facebook_data['parent'][$i]['name'] = $childlist['name'];
          $facebook_data['parent'][$i]['comment'] = $childlist['comment'];
          $facebook_data['parent'][$i]['createddate'] = $childlist['createddate'];
          $facebook_data['parent'][$i]['comment_id'] = $childlist['comment_id'];
          $facebook_data['parent'][$i]['post_id'] = $childlist['post_id'];
          $facebook_data['parent'][$i]['status'] = $childlist['status'];
          $facebook_data['parent'][$i]['post'] = $childlist['post'];
          $facebook_data['parent'][$i]['attachment'] = $childlist['attachment'];
          $facebook_data['parent'][$i]['flag_read_unread'] = $childlist['flag_read_unread'];
          $facebook_data['parent'][$i]['msg_flag'] = $childlist['msg_flag'];
          $facebook_data['parent'][$i]['parent_comment_id'] = $childlist['parent_comment_id'];
        }
      }
  }
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Facebook Comments</title> 
 <!-- Required meta tags -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" type="text/css" href="assets/font-awesome/4.5.0/css/font-awesome.min.css"/> 
<!-- Dynamic css -->
<link rel="stylesheet" type="text/css" href="../../public/css/<?=$dbtheme?>.css"/> 
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css" />
<link rel="stylesheet" type="text/css" href="css/select2-bootstrap.min.css"> 
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>    
<!-- <script src="datetimepicker.js"></script> -->
<script src="https://code.jquery.com/jquery-2.2.0.min.js"></script> 
<script type="text/javascript" src="../../public/js/jquery-ui.min2.js"></script>
<style>
.container{
  width:495px;
  display:block;
  margin:0 auto;
  box-shadow:0 2px 5px rgba(0,0,0,0.4);
}
.header_t h2{
  font-size:13px;
  line-height:10px;
  display:inline-block;
}
.chat-box, .enter-message {
    background: #ECECEC;
    /* padding: 0 10px; */
    color: #a1a1a1;
    width: 100%;
    float: left;
    box-sizing: border-box;
    padding-bottom: 20px;
    }

    .chat-box {
    height: 350px;
    overflow-y: auto;
}
.chat-box .message-box{
  padding:15px 0 10px;
  clear:both;

}
.message-box .picture{
  float:left;
  width:42px;
  display:block;
  padding-right:10px;
}
.main_pop_heading {
    text-align: center;
    font-size: 16px;
    font-weight: 600;
}
.oop_services {
    width: 100%;
    float: left;
    box-sizing: border-box;
    margin-bottom: 10px;
    margin-top: 10px;
    border-bottom: #eee 1px solid;
    padding-bottom: 10px;
}
.picture img{
  width:43px;
  height:48px;
  border-radius:5px;
}
.picture span{
  font-weight:bold;
  font-size:12px;
  clear:both;
  display:block;
  text-align:center;
  margin-top:3px;
}
.message{
  background:#fff;
  display:inline-block;
  /*padding:13px;*/
  width:249px;
  border-radius:2px;
  box-shadow: 0 1px 1px rgba(0,0,0,.04);
  position:relative;
  
  height: auto;
  border-radius: 13px; 
}
.message:before{
  content:"";
  position:absolute;
  display:block;
  left:0;
  border-right:6px solid #fff;
  border-top: 6px solid transparent;
  border-bottom:6px solid transparent;
  top:10px;
  margin-left:-6px;
}
.message span{
  color:#555;
  font-weight:bold;
}
.message p{
  /*padding-top:5px;*/
}
.message-box.right-img .picture{
  float:right;
  padding:0;
  padding-left:10px;
}
.message-box.right-img .picture img {
    float: right;
    position: relative;
    right: 9px;
}
.message-box.left-img .picture img {
    position: relative;
    left: 10px;
}
.form_area select, .form_area textarea {
    width: 100%;
    border: #ccc 1px solid;
    padding: 5px;
    box-sizing: border-box;
}
.form_area {
    padding: 10px;
    box-sizing: border-box;
    /* background: #fff; */
    width: 100%;
}
input#dm_button {
    background: #000;
    color: #fff;
    padding: 5px 10px;
    float: right;
    border-radius: 5px;
}
.message-box.right-img .message:before{
  left:100%;
  margin-right:6px;
  margin-left:0;
  border-right:6px solid transparent;
  border-left:6px solid #fff;
  border-top: 6px solid transparent;
  border-bottom:6px solid transparent;
}
.enter-message{
  padding:13px 0px;
}
.enter-message input{
  border:none;
  padding:8px 8px;
  background:#d3d3d3;
  width:300px;
  border-radius:2px;
}
/*.enter-message .reply{
  padding:10px 15px;
  background:#6294c2;
  border-radius:2px;
  float:right;
}*/

.response_area{
    color: green;
    font-weight: bold;
}

.clearbtn{
 background: url("../../public/images/clear_img.png") no-repeat;
  cursor:pointer;
  border: none;
  }

  .popup_area .col-md-3 {
    float: left;
    width: 40%;
}
.popup_area .col-md-9 {
    float: left;
    width: 60%;
    padding: 0 15px;
    box-sizing: border-box;
}

</style>
</head>
<body>
<div class="popup_area">
<div class="popheading"> 
  <div class="main_pop_heading" style="margin-top: 5px;">
    <?php
        if($v_Screenname!="") echo 'Twitter handle::@'.$v_Screenname;
     ?>
  </div>
  <div class="col-md-12">
      <div class="row">       
        <div class="col-md-3" style="display: none">
          <div class="response_area"><div id="response_msg"></div></div>
          <div class="form_area"><h2>Facebook Comment Reply</h2>
            <form name="dm_form" id="dm_form" enctype="multipart/form-data" method="post">        
                <div style="clear: both; height:10px;"></div>
                <textarea  name="dm_message" id="dm_message" placeholder="Enter your message.." style="height:85px;"></textarea>
               <div style="clear: both; height:10px;"></div>                     
                  <input type="submit"  class="btn_submit" name="dm_button" id="dm_button"  style="" value="Send">
                  <a href="javascript:void(0)" title="clear text" onclick="clear_btn()"> 
                   <img src="../../public/images/clear_img.png" width="20" height="20"></a>
                  <input type="hidden" name="id" value="<?=$_REQUEST['id']?>">
                  <input type="hidden" name="comment_id" value="<?=$_REQUEST['comment_id']?>">
                  <span id="responseMessage"></span>
                   <div style="clear: both; height:10px;"></div>
                </div>
            </form>
          </div>
        </div><!--end of col-md-3-->
        <div class="col-md-12" >
          <div class="response_area"><div id="response_msg"><?=$msg?></div></div>
            <div class="form_area">           
                <!---SHOW DM Message-->
                <?php
                if(!empty($facebook_data)){
                ?>
                <div class="row">
                  <form name="dm_form_msg" id="dm_form_msg"   enctype="multipart/form-data" method="post"  >
                    <input type="hidden" name="type" value="assigndm">
                    <div class="chat_box_wrap">
                      <div class="header_t"> 
                        <!-- <?php if($id && $numrec!=0) {?><input type="checkbox" class="chk_boxes" label="check all"  /><?php }?> -->
                        <h2>Comments</h2>
                      </div>
                      <!-- <div class="chat-box" style="max-height: 400px;overflow:scroll; "> -->
                       <div class="chat-box" style=" ">
                      <?php
                        if($facebook_data){
                          // while($rm=mysqli_fetch_assoc($qdm)){
                          foreach ($facebook_data['parent'] as $rm) {
                            
                            if($rm['msg_flag']=='IN') {
                                  $msg_class="left-img";
                                  $msg_float="left";
                                  $symbol=">>>";
                                  // $userName="@".$v_Screenname;
                                  $imgsrc='<img src="../../public/images/facebook.png" alt="" style="height: 30px;width:30px;" title="user name">';
                              }else{
                                  $symbol=">>>";
                                    $msg_class="right-img";
                                    $msg_float="right";
                                    // $userName="@luska_water";
                                    $imgsrc='<img src="../../public/images/ensembler-logo.png" alt="" style="height: 30px;width:30px;" title="user name">';
                                    $msg_Color='aliceblue';
                              }
                          ?>
                          <div class="message-box <?=$msg_class?>">
                            <div class="picture">
                              <?php echo $imgsrc;?>
                             <!--  <span class="time">10 mins</span> -->
                            </div>
                            <div class="message" style="background:<?=$msg_Color?>;float: <?=$msg_float?>" ><!-- <span>sdasd Moloney</span> -->
                              <p style="padding: 7px 0px 0px 13px;margin: 0px; "><?php echo $rm["comment"];?></p>
                              <p style=" float:right;padding: 0px 9px 3px 0px;margin: 0px;font-size: 12px;"><?php echo $rm["createddate"];?>
                              </p>
                            </div>
                            </div>
                            <?php if(!empty($rm['attachment'])){?><a style="color: #4a90e2;margin-left: 10px;" href="<?=$rm['attachment']?>" target="_blank">attachment</a><?php }?>
                          
                          <?php 
                          }
                        //end of while
                        }else{?>
                          <div class="enter-message">No message</div>
                        <?php }?>
                      </div>
                    </div>
                  </form>
                </div>
                <? }else{?> 
                <div class="chat_box_wrap"> 
                  <div class="header_t" syle="margin:10px;"><h2>Direct Messages</h2></div>
                  <div class="" style="text-align: left;"> 
                  No message
                  </div>
                </div>
              <? }?>
              <!---SHOW DM Message-->
            </div>
          </div><!--end of col-md-9-->
      </div><!--end of row-->
  </div>
  </div>
</body>
</html>
<script type="text/javascript">
  function clear_btn(){
   $("textarea").val("");
   $("#response_msg").text('');
  }
  function clearText(field){
      if (field.defaultValue == field.value) field.value = '';
      else if (field.value == '') field.value = field.defaultValue;
  }
</script>