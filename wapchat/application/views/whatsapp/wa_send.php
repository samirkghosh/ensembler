<?php
   defined('BASEPATH') OR exit('No direct script access allowed');
   ?>
<!-- Header start -->
<?php  $this->load->view('layout/header'); ?>
<!-- Header End  -->
<style>
   body {
   font-family: "Lato", sans-serif;
   }
   .sidenav {
   height: 100%;
   left: 0;
   background-color: #11111161;
   width: 52px
   }
   .sidenav a {
   padding: 6px 8px 6px 16px;
   text-decoration: none;
   font-size: 18px;
   color: #818181;
   display: block;
   }
   .sidenav a:hover {
   color: coral;
   }
   .card {
   border-radius: 0;
   }
   .direct-chat-text{
   color: #566069;
   font-size: 11px;
   line-height: 21px;
   letter-spacing: 0.3px;
   outline: none;
   
   }
   .direct-chat-img {
   border-radius: 0; 
   float: right;
   height: 25px;
   width: 25px;
   }
   .direct-chat-timestamp{
   font-size: 10px;
   letter-spacing: 0.5px;
   line-height: 16px;
   }
   .direct-chat-messages {
   -webkit-transform: translate(0,0);
   transform: translate(0,0);
   height:auto;
   padding: 10px;
   }
 
   * {
   scrollbar-width: thin;
   scrollbar-color: #d5d9dc #ffffff;
   }
   
   *::-webkit-scrollbar {
   width: 13px;
   }
   *::-webkit-scrollbar-track {
   background: #ffffff;
   }
   *::-webkit-scrollbar-thumb {
   background-color: #d5d9dc;
   border-radius: 10px;
   border: 3px solid #ffffff;
   }

   .active_bot{
    background: aliceblue;
    padding: 4px;
    border-radius: 9px;
    border: 1px solid #b7cee2;
   }

   .hover ,.hover:hover{
      color:#697582;
   }

   .nav-active{
      cursor: pointer;
   }

</style>
<!-- Content page  -->
<div class="content">
   <!-- Content Header (Page header) -->
   <div class="d-flex p-1" style="width:100%">
    
      <!-- left -->
      <div class="p-0">
         <!-- <div class="card card-info  card-outline" style="max-width: 400px;height:600px"> -->
         <div class="card card-info  card-outline" style="min-width: 235px;max-height:538px">
            <div class="card-header">
               <h3 class="card-title">Previous Bot session's</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body" style="overflow: auto;">
               <ul class="nav nav-pills flex-column">
                  <?php if(count($previous_bot_session)): 
                     foreach ($previous_bot_session as $key => $session) {  ?>
                        <li class="nav-item nav-active" style="width: 235px;font-size: 13px;line-height: 20px;" onclick="get_bot_by_session('<?php echo $session->chat_session ;?>')">
                           <a href="javascript:void(0);" title="Click here to view chat conversations"  class="hover" style="padding: 0;">
                           <i class="fas fa-user-circle"></i> <?php echo $customer_name ?> <span style=" font-size: x-small;"><?php echo date('d D Y H:i', strtotime($session->createdDatetime))?></span>
                           </a>
                          <span style="font-size:11px;color: cadetblue;"> ( BOT <?php echo $key+1; ?> ) <?php echo $session->chat_session ?></span>
                           <!-- <p style="font-size: 13px;
                              line-height: 20px;
                              opacity: 0.8;
                              height: 35px;
                              overflow: hidden;
                              letter-spacing: 0.3px;">We will provide you all the details via email within 48 hours</p> -->
                        </li>
                     


                  <?php 
                  } 
                  endif; 
                  ?>
                 
                   
               </ul>
            </div>
            <!-- /.card-body -->
         </div>
      </div>
      <!--Mid  -->
      <div class="p-0" >
         <!-- <div class="card card-info  card-outline" style="width:669px;height:600px"> -->
         <div class="card card-info  card-outline " style="min-width: 764px;height:538px">
            <div class="card-header">
               <h3 class="card-title">Whatsapp Conversations</h3>
               <!-- <img class="direct-chat-img" src="" alt="Image"> -->
            </div>
            <!-- /.card-header -->
            <div class="card-body conversation_board" style="overflow: auto;">
               <!-- Conversations are loaded here -->
               <div class="direct-chat-messages" id="conversation_board">
                  
                 
               </div>
               <!--/.direct-chat-messages-->
               <!-- /.direct-chat-pane -->
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <div class="row">
                <div class="col-md-10">
                  <form name="agent_bot_form" id="agent_bot_form" action="<?php echo site_url('agentbot/send_whatsapp_message') ?>" method="post">
                <input type="hidden" name="conversation_id" id="conversation_id" value="<?php echo $conversation_id ?>">
                <input type="hidden" name="agent_id" id="agent_id" value="<?php echo $agent_id ?>">
                  <div class="input-group">
                     <input type="text" name="bot_message" id="bot_message" placeholder="Type your Message here..." class="form-control">
                     <span class="input-group-append">
                     <button type="submit" class="btn btn-info" style="background:#1e3c72;border-color:#1e3c72">Send</button>
                     </span>
                  </div>
               </form>
                </div>
                <div class="col-md-2" >
                 <!--  <form style="float: right;" name="agent_bot_form_close" id="agent_bot_form_close" action="<?php echo site_url('agentbot/agent_close_chat_ajax') ?>" method="post">
                <input type="hidden" name="conversation_id_close" id="conversation_id_close" value="<?php echo $conversation_id ?>">
                
                  <div class="input-group">
                     
                     <span class="input-group-append">
                     <button type="submit" class="btn btn-primary">End Chat</button>
                     </span>
                  </div>
               </form> -->
                </div>
              </div>
               

               

            </div>
         </div>
      </div>
      <!--right  -->
      <div class="p-0" style="width:100%">
         <!-- <div class="card card-info  card-outline" style="height:600px;width:388px"> -->
         <div class="card card-info  card-outline" style="height:538px">
            <div class="card-header">
               <h3 class="card-title">BOT Wise Conversations : <small id="current_chat_id"><?php echo $current_session; ?></small></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body bot_session_history" style="overflow: auto;">
               <!-- Conversations are loaded here -->
               <div class="direct-chat-messages" id="bot_session_history">
                  <?php if(count($current_bot_chat)):
                    foreach ($current_bot_chat as $key => $value) { ?>
                   
                      <?php if($value->direction=='sent'): ?>
                        <div class="direct-chat-msg"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-left" style="font-weight:100;font-size: smaller;color:#6c757d">BOT</span><span class="direct-chat-timestamp float-right"><?php echo date('d M H:s a', strtotime($value->createdDatetime)) ?></span></div>
                        
                        
                        <div class="direct-chat-text" style="background:aliceblue"><?php echo $value->content_text ?></div></div>

                      <?php else: ?>  
                        <div class="direct-chat-msg right"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-right" style="font-weight:100;font-size: smaller;color:#6c757d">Customer</span><span class="direct-chat-timestamp float-left"><?php echo date('d M H:s a', strtotime($value->createdDatetime)) ?></span></div>
                        
                        <div class="direct-chat-text" style="background:#f3fff0"><?php echo $value->content_text ?></div></div>
                      <?php endif; ?>  
                    

                  <?php } endif; ?>
               </div>
               <!--/.direct-chat-messages-->
               <!-- /.direct-chat-pane -->
            </div>
            <!-- /.card-body -->
         </div>
      </div>
   </div>
</div>
<!-- Main Footer --> 
<?php $this->load->view('layout/footer');?>
<!-- End of footer -->

<script type="text/javascript">
  var last_message ='';
  var list_count = 0 ;
    var img_path = "<?php echo base_url() . '/assets/images/loading.gif' ?>";
      

    $("#agent_bot_form").submit(function (e) {
        $("[class$='_error']").html("");
       
         $(".custom_loader").html('<img src="' + img_path + '">');
         var url = $(this).attr('action'); // the script where you handle the form input.
         var bot_message = $("#bot_message").val();
         var message = '<div class="direct-chat-msg"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-left" style="font-weight:100;font-size: smaller;color:#6c757d">Agent</span><span class="direct-chat-timestamp float-right">23 Jan 2:05 pm</span></div><div class="direct-chat-text" style="background:aliceblue">'+bot_message+'</div></div>';


         $("#conversation_board").append(message);
         
         $(".conversation_board").stop().animate({ scrollTop: $(".conversation_board")[0].scrollHeight}, 1000);
         console.log('BEFOR AJAX CALL ');
         $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#agent_bot_form").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR){
              console.log('conversation_board');
              console.log(data);
              $("#bot_message").val('');
                if (data.status == 'close') {
                    successMsg(data.msg);
                     setTimeout(function(){
                        window.location.href = '<?php echo site_url('whatsapp/bot_request')?>';
                      },2000);
                } else {
                    successMsg(data.msg);
                }
                $(".custom_loader").html("");

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
                //if fails      
            }
        });
       
        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    $("#agent_bot_form_close").submit(function (e) {
        $("[class$='_error']").html("");
       
         $(".custom_loader").html('<img src="' + img_path + '">');
         var url = $(this).attr('action'); // the script where you handle the form input.
         
         var message = '<div class="direct-chat-msg"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-left" style="font-weight:100;font-size: smaller;color:#6c757d">Agent</span><span class="direct-chat-timestamp float-right">23 Jan 2:05 pm</span></div><div class="direct-chat-text" style="background:aliceblue">We have not got any response from your side and we are closing this session.</div></div>';
         $("#conversation_board").append(message);
         $(".conversation_board").stop().animate({ scrollTop: $(".conversation_board")[0].scrollHeight}, 1000);


         
         console.log('BEFOR AJAX CALL agent_bot_form_close');
         $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            data: $("#agent_bot_form_close").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR){
              console.log('conversation_board');
              console.log(data);
              $("#bot_message").val('');
                // if (data.st === 1) {
                //     $.each(data.msg, function (key, value) {
                //         $('.' + key + "_error").html(value);
                //     });
                // } else {
                //     successMsg(data.msg);
                // }
                $(".custom_loader").html("");

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $(".custom_loader").html("");
                //if fails      
            }
        });
       
        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

   // function formatAMPM(date) {
   //   var hours = date.getHours();
   //   var minutes = date.getMinutes();
   //   var ampm = hours >= 12 ? 'pm' : 'am';
   //   hours = hours % 12;
   //   hours = hours ? hours : 12; // the hour '0' should be '12'
   //   minutes = minutes < 10 ? '0'+minutes : minutes;
   //   var strTime = hours + ':' + minutes + ' ' + ampm;
   //   return strTime;
   // }


      function agent_customer_conversations() {
       
         var url = '<?php echo site_url('agentbot/agent_customer_conversations')?>';
         $.ajax({
            url:url,
            data:false,
            dataType:'JSON',
            method:'GET',
            processData:false,
            contentType:false,
            cache:false,
            success:function(data, textStatus, jqXHR){
              console.log('data');
              console.log(data);
              // console.log('Length '+ data.list.length);
              // console.log('list_count '+ list_count);

              // console.log('CHECK height 2');
              // console.log('ID DATA ' +$("#conversation_board")[0].scrollHeight);
              // console.log('CLASS DATA ' +$(".conversation_board")[0].scrollHeight);


              if(textStatus=='success'){
                if(data.list.length > list_count ){
                  list_count = data.list.length;
                  $("#conversation_board").empty();
                  $.each(data.list, function(i,val){
                     // let date_time = val.createdDatetime;
                     // console.log('Date Time '+ date_time);
                     // console.log('Date Time getDay 1 '+ date_time.getDay());
                     // console.log('Date Time getDay 1 '+ date_time.getMonth());

                    if(val.direction=='sent'){
                      $("#conversation_board").append('<div class="direct-chat-msg"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-left" style="font-weight:100;font-size: smaller;color:#6c757d">Agent</span><span class="direct-chat-timestamp float-right">'+val.createdDatetime+'</span></div><div class="direct-chat-text" style="background:aliceblue">'+val.content_text+'</div></div>');
                    } 
                    else{
                      $("#conversation_board").append('<div class="direct-chat-msg right"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-right" style="font-weight:100;font-size: smaller;color:#6c757d">Customer</span><span class="direct-chat-timestamp float-left">'+val.createdDatetime+'</span></div><div class="direct-chat-text" style="background:#f3fff0">'+val.content_text+'</div></div>');
                    }
                    $(".conversation_board").stop().animate({ scrollTop: $(".conversation_board")[0].scrollHeight}, 1000);

                    //$( ".card-comments" ).append('<div class="card-comment"><div class="comment-text"><span class="username">Farhan Akhtar<span class="text-muted float-right">11:00 AM Today</span></span>hi</div></div>').delay(60*8000);
                  });
                }
              }

              //$(".c_h").trigger('click');
            },
            error:function(jqXHR, textStatus, errorThrown){},
            complete:function(){},
         });
      }

      $('.nav-active').click(function(e) {
        e.preventDefault();
        $('.nav-active').removeClass('active_bot');
        $(this).addClass('active_bot');
    });


      function get_bot_by_session(chat_session_id) {
         console.log('get_bot_history_by_session '+chat_session_id);
         $("#current_chat_id").empty();
         $("#current_chat_id").text(chat_session_id);
         var url = '<?php echo site_url('agentbot/get_bot_by_session')?>';
         $.ajax({
            url:url,
            data:{ 'chat_session_id' : chat_session_id },
            dataType:'JSON',
            method:'POST',
            // processData:false,
            // contentType:false,
            // cache:false,
            success:function(data, textStatus, jqXHR){
              console.log('data');
              console.log(data);
              // console.log('Length '+ data.list.length);
              // console.log('list_count '+ list_count);

              // console.log('CHECK height 2');
              // console.log('ID DATA ' +$("#conversation_board")[0].scrollHeight);
              // console.log('CLASS DATA ' +$(".conversation_board")[0].scrollHeight);
              $("#bot_session_history").empty();
               if(textStatus=='success'){

                  if(data.list.length > 0 ){
                     
                     $("#bot_session_history").empty();
                     $.each(data.list, function(i,val){
                       if(val.direction=='sent'){
                         $("#bot_session_history").append('<div class="direct-chat-msg"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-left" style="font-weight:100;font-size: smaller;color:#6c757d">Agent</span><span class="direct-chat-timestamp float-right">'+val.createdDatetime+'</span></div><div class="direct-chat-text" style="background:aliceblue">'+val.content_text+'</div></div>');
                       } 
                       else{
                         $("#bot_session_history").append('<div class="direct-chat-msg right"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-right" style="font-weight:100;font-size: smaller;color:#6c757d">Customer</span><span class="direct-chat-timestamp float-left">'+val.createdDatetime+'</span></div><div class="direct-chat-text" style="background:#f3fff0">'+val.content_text+'</div></div>');
                       }
                        $(".bot_session_history").stop().animate({ scrollTop: $(".bot_session_history")[0].scrollHeight}, 1000);
                     });
                  }
                  else{
                     $("#bot_session_history").append('<div class="direct-chat-msg left"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-left" style="font-weight:100;font-size: smaller;color:#6c757d"></span><span class="direct-chat-timestamp float-right">23 Jan 2:05 pm</span></div><div class="direct-chat-text" style="background:aliceblue">No Record Found</div></div>');
                  }

               }
               else{

                  $("#bot_session_history").append('<div class="direct-chat-msg left"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-right" style="font-weight:100;font-size: smaller;color:#6c757d">Bot</span><span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span></div><div class="direct-chat-text" style="background:aliceblue">No Record Found</div></div>');
               }

              //$(".c_h").trigger('click');
            },
            error:function(jqXHR, textStatus, errorThrown){},
            complete:function(){},
         });
      }      
            

     var gatway_status = setInterval(function() {
       agent_customer_conversations();
       
     }, 5000);
  
</script>

<!-- <div class="direct-chat-msg right"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-right" style="font-weight:100;font-size: smaller;color:#6c757d">Sarah Bullock</span><span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span></div><div class="direct-chat-text" style="background:aliceblue">What is this offer? Can you explain me?</div></div> --> 