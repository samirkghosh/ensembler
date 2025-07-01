<?php
   defined('BASEPATH') OR exit('No direct script access allowed');
   // change by farhan :: 20 june 2022 

   /**
    * changed the code for notification sound 
    * changed by Vastvikta Nishad
    * date : 26-08-2024
   */

   // createdDatetime to create_datetime 
   $chat_session = $_GET['chat_session'];
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
   .attachment_images{
    color: #566069;
    font-size: 11px;
    margin-left: 4px;
   }
   title{
        color: black; /* Change the color to your desired color */
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
               <h3 class="card-title">Previous session's</h3>
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
               <h3 class="card-title">Current Conversations with <strong><?php echo $customer_name;?></strong></h3>
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
                  <form name="agent_bot_form" id="agent_bot_form" action="<?php echo site_url('agentwebchatbot/send_whatsapp_message') ?>" method="post">
                     <input type="hidden" name="conversation_id" id="conversation_id" value="<?php echo $conversation_id ?>">
                     <input type="hidden" name="agent_id" id="agent_id" value="<?php echo $agent_id ?>">
                     <input type="hidden" name="chat_session" id="chat_session" value="<?php echo $chat_session ?>">
                     <div class="input-group">
                     <input type="text" name="bot_message" id="bot_message" placeholder="Type your Message here..." class="form-control">
                     <span class="input-group-append">
                     <span class="publisher-btn file-group"> <i class="fa fa-paperclip file-browser" style="margin-right: 8px;" id="image_event"></i> 
                        <!-- <input type="file" style="display: none" name="upload_images">  -->
                        <input type="file" id="file" name="file" style="display: none" multiple="" data-original-title="upload photos"></span> 
                     <button type="submit" class="btn btn-info" style="background:#1e3c72;border-color:#1e3c72">Send</button>&nbsp;&nbsp;
                     <button type="button" class="btn btn-info" style="background:#1e3c72;border-color:#1e3c72" id="close" data-id="<?php echo $session->chat_session ;?>">Close</button>
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
               <h3 class="card-title">Old Conversations : <small id="current_chat_id"><?php echo $current_session; ?></small></h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body bot_session_history" style="overflow: auto;">
               <!-- Conversations are loaded here -->
               <div class="direct-chat-messages" id="bot_session_history">
                     <?php if(count($current_bot_chat)):
                           foreach ($current_bot_chat as $key => $value) { ?>

                              <?php if($value->direction == 'OUT'): ?>
                              <div class="direct-chat-msg">
                                 <div class="direct-chat-infos clearfix">
                                    <span class="direct-chat-name float-left" style="font-weight:100;font-size: smaller;color:#6c757d">BOT</span>
                                    <span class="direct-chat-timestamp float-right">
                                    <?php echo date('d M H:s a', strtotime($value->create_datetime)) ?>
                                    </span>
                                 </div>
                                 
                                 <div class="direct-chat-text" style="background:aliceblue">
                                    <?php echo $value->message ?>
                                 </div>
                                 <?php 
                                 if(!empty($value->attachment)) { ?>
                                    <p class="attachment_images" >
                                    <a href="../../../<?php echo $value->attachment; ?>" target="_blank">attachment</a>
                                    </p>
                                 <?php } ?>
                              </div>

                              <?php else: ?>  
                       
                        <div class="direct-chat-msg right"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-right" style="font-weight:100;font-size: smaller;color:#6c757d">Customer</span><span class="direct-chat-timestamp float-left"><?php echo date('d M H:s a', strtotime($value->create_datetime)) ?></span></div>
                        <?php 
                        if(!empty($value->message)){ ?>
                          <div class="direct-chat-text" style="background:#f3fff0"><?php echo $value->message ?></div>
                        <?php }?>

                        </div>
                         <?php 
                        if(!empty($value->attachment)){
                        ?>
                        <p class="attachment_images">
                          <a href="../../../<?php echo $value->attachment;?>" target="_blank">attachment</a>
                        </p>
                        <?php }?>
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
// vastvikta 05-12-2024 for sending attachment throuh  agent side 
// Trigger the file input when the paperclip icon is clicked
$("#image_event").on('click', function () {
    $('#file').trigger('click');  
});

// Change the icon color when a file is selected
$(document).on("change", "#file", function(){
    $('#image_event').css("color", "blue"); // Change icon color to blue when a file is selected
});


// Reset the icon color to original when the Send button is clicked
$("button[type='submit']").on('click', function() {
    $('#image_event').css("color", "#d9d4cc"); // Reset color back to original
});

   var last_message ='';
   var list_count = 0 ;
   var img_path = "<?php echo base_url() . '/assets/images/loading.gif' ?>";
      

   $("#agent_bot_form").submit(function (e) {
    e.preventDefault(); // Prevent form submission
    $("[class$='_error']").html("");
    $(".custom_loader").html('<img src="' + img_path + '">');
    var url = $(this).attr('action');

    // Get the form inputs
    var bot_message = $("#bot_message").val();
    var file = $('#file')[0].files[0];

    // Create a FormData object to include both the message and the file
    var formData = new FormData();
    formData.append("bot_message", bot_message);
    if (file) {
        formData.append("file", file);
    }
    formData.append("conversation_id", $("#conversation_id").val());
    formData.append("agent_id", $("#agent_id").val());
    formData.append("chat_session", $("#chat_session").val());

   // Add message or file to the chat
   if (bot_message || file) {
        // Display the message and/or file link
        var messageHTML = '<div class="direct-chat-msg">';
        messageHTML += '<div class="direct-chat-infos clearfix"><span class="direct-chat-name float-left" style="font-weight:100;font-size: smaller;color:#6c757d">Agent</span><span class="direct-chat-timestamp float-right">' + new Date().toLocaleString() + '</span></div>';
        if (bot_message) {
            messageHTML += '<div class="direct-chat-text" style="background:aliceblue">' + bot_message + '</div>';
        }
        if (file) {
            var fileLink = "../../../" + file.name; // Construct file link
            messageHTML += '<div class="direct-chat-text" style="background:#d9fdd3;color:black;font-weight:20px;"><a href="' + fileLink + '" target="_blank"><strong>attachment</strong></a></div>';
        }
        messageHTML += '</div>';
        
        // Append the message to the chat board
        $("#conversation_board").append(messageHTML);

        // Ensure the chat board scrolls to the bottom to show the latest message
        $(".conversation_board").stop().animate({ scrollTop: $(".conversation_board")[0].scrollHeight }, 1000);

        // Clear the input fields
        $("#bot_message").val(''); // Clear the text input
        $('#file').val(''); // Clear the file input

        // Send the data via AJAX
        $.ajax({
            type: "POST",
            url: url,
            data: formData,
            processData: false, // Prevent jQuery from automatically transforming the FormData object
            contentType: false, // Prevent jQuery from overriding the content type
            dataType: 'JSON',
            success: function (data) {
                $("#bot_message").val(''); // Clear the input field
                $('#file').val(''); // Clear the file input
                if (data.status === 'close') {
                    successMsg(data.msg);
                    setTimeout(function () {
                        var encodedToken = btoa('chat');
                        window.location.href = '<?php echo site_url('../CRM/omni_channel.php?token=') ?>' + encodeURIComponent(encodedToken);
                    }, 2000);
                } else {
                    successMsg(data.msg);
                }
                $(".custom_loader").html("");
            },
            error: function () {
                $(".custom_loader").html("");
                // Alert for failure removed
            }
        });
    } else {
        alert("Please enter a message or select a file to upload.");
        $(".custom_loader").html("");
    }
   });



    $("#agent_bot_form_close").submit(function (e) {
        $("[class$='_error']").html("");
       
         $(".custom_loader").html('<img src="' + img_path + '">');
         var url = $(this).attr('action'); // the script where you handle the form input.
         
         var message = '<div class="direct-chat-msg"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-left" style="font-weight:100;font-size: smaller;color:#6c757d">Agent</span><span class="direct-chat-timestamp float-right">23 Jan 2:05 pm</span></div><div class="direct-chat-text" style="background:aliceblue">We have not got any response from your side and we are closing this session.</div></div>';
        playNotificationSound();
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

      var current_count = null;
      var old_count = null;
    //   $(document).ready(function() {
    //     // Check if the current tab is active or not
    //     var tabIsActive = !document.hidden;

    //     if (tabIsActive) {
    //         // The current tab is active
    //         console.log("This tab is active.");
    //     } else {
    //         // The current tab is not active
    //         console.log("This tab is not active.");
    //     }

    //     // Listen for visibility change events
    //     document.addEventListener("visibilitychange", function() {
    //         if (document.hidden) {
    //             console.log("This tab is now inactive.");
    //         } else {
    //             console.log("This tab is now active.");
    //         }
    //     });
    // });


      /**
       * auth : vastvikta nishad
       * date : 26-08-2024
       * description : function to play notification sound when message is sent 
       */
      // Function to play notification sound
      function playNotificationSound() {
         console.log('Playing notification sound');
         
         const audio = new Audio('../message.mp3'); // Update the path to your sound file
         audio.play();
      }

      var lastMessageTimestamp = 0; // Set to 0 to ensure proper initial comparison

      var lastMessageCount = 0; // Track the number of messages

function agent_customer_conversations(oldheight) {
   var st = '';
   var url = '<?php echo site_url('agentwebchatbot/agent_customer_conversations?chat_session='.$chat_session)?>';

   var tabIsActive = !document.hidden;
   if (current_count > old_count && !tabIsActive) {
      checkForNewData(current_count);
      old_count = current_count;
   }
   if (current_count > old_count && tabIsActive) {
      old_count = current_count;
      document.title = 'HELB chat';
   }
   if (current_count == old_count && tabIsActive && document.title != 'HELB chat') {
      document.title = 'HELB chat';
   }

   $.ajax({
      url: url,
      data: false,
      dataType: 'JSON',
      method: 'GET',
      processData: false,
      contentType: false,
      cache: false,
      success: function(data, textStatus, jqXHR) {
         if (textStatus == 'success') {
            list_count = data.list.length;
            $("#conversation_board").empty();
            var st = '';
            current_count = data.list.length;

            $.each(data.list, function(i, val) {
               var attachments_list = '';
               var message = '';

               // Check if there is an attachment[vastvikta][07-12-2024]
               if (val.attachment) {
                  // Check the direction of the attachment
                  if (val.direction == 'OUT') {
                     // Attachments going OUT should have the specific CSS applied
                     fileLink = '../../../' + val.attachment;
                     attachments_list = '<div class="direct-chat-text" style="background:#d9fdd3;color:black;font-weight:20px;"" ><a href="' + fileLink + '" target="_blank" ><strong>attachment</strong></a></div>';
                  } else if (val.direction == 'IN') {
                     // Attachments coming IN use the original path and styling
                     fileLink = '../../../' + val.attachment;
                     attachments_list = '<p class="attachment_images" style="color:black;font-weight:20px;"><a href="' + fileLink + '" target="_blank"><strong>attachment</strong></a></p>';
                  }
               }

               // Check if the message exists and is not empty
               if (val.message) {
                  message = '<div class="direct-chat-text" style="background:#f3fff0">' + val.message + '</div>';
                  console.log('message is displaying');
               }

               // Check if the message direction is OUT
               if (val.direction == 'OUT') {
                  // Only append attachment or message if they exist
                  if (attachments_list || message) {
                     st += '<div class="direct-chat-msg"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-left" style="font-weight:100;font-size: smaller;color:#6c757d">Agent</span><span class="direct-chat-timestamp float-right">' + val.createdDatetime + '</span></div>';
                     if (message) {
                        st += '<div class="direct-chat-text" style="background:#d9fdd3;">' + val.message + '</div>';
                     }
                     if (attachments_list) {
                        st += attachments_list;  // Only append if there is an attachment
                     }
                     st += '</div>';
                  }
               } else {
                  // Handle incoming messages
                  if (attachments_list || message) {
                     st += '<div class="direct-chat-msg right"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-right" style="font-weight:100;font-size: smaller;color:#6c757d">Customer</span><span class="direct-chat-timestamp float-left">' + val.createdDatetime + '</span></div>';
                     if (message) {
                        st += message;  // Only append if there is a message
                     }
                     if (attachments_list) {
                        st += attachments_list;  // Only append if there is an attachment
                     }
                     st += '</div>';
                  }
               }
            });

            $("#conversation_board").append(st);
            $("#conversation_board").stop().animate({ scrollTop: $(".conversation_board")[0].scrollHeight }, 1000);
         }
      },
      error: function(jqXHR, textStatus, errorThrown) {},
      complete: function() {},
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
                       if(val.direction=='OUT'){
                         $("#bot_session_history").append('<div class="direct-chat-msg"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-left" style="font-weight:100;font-size: smaller;color:#6c757d">Agent</span><span class="direct-chat-timestamp float-right">'+val.createdDatetime+'</span></div><div class="direct-chat-text" style="background:aliceblue">'+val.message+'</div></div>');
                       } 
                       else{
                         $("#bot_session_history").append('<div class="direct-chat-msg right"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-right" style="font-weight:100;font-size: smaller;color:#6c757d">Customer</span><span class="direct-chat-timestamp float-left">'+val.createdDatetime+'</span></div><div class="direct-chat-text" style="background:#f3fff0">'+val.message+'</div></div>');
                       }
                        $(".bot_session_history").stop().animate({ scrollTop: $(".bot_session_history")[0].scrollHeight}, 1000);
                     });
                     $(".bot_session_history").stop().animate({ scrollTop: $(".bot_session_history")[0].scrollHeight}, 1000);
                     $(".conversation_board").stop().animate({ scrollTop: $(".conversation_board")[0].scrollHeight}, 1000);
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
            
   var divHeight = {h:0};
     var gatway_status = setInterval(function() {
       agent_customer_conversations(divHeight);
       
     }, 5000);


     //close chat
     $("#close").click(function(e)
     {
      e.preventDefault();
      var conversation_id = $("#conversation_id").val();
      var chat_session_id = $(this).data('id');
      $.ajax({
         url:"<?php echo site_url()?>/agentwebchatbot/close_chat",
         method:"post",
         data:{'id':conversation_id,'chat_session_id':chat_session_id},
         dataType:'JSON',
         success:function(data)
         {
            console.log('data')
            console.log(data)
            console.log(data.status);
            if(data.status == 'success')
            { console.log('chat close');
               setTimeout(function(){
                  // history.go(-1)
                  var encodedToken = btoa('chat');
                  // window.location = '../CRM/omni_channel.php?token='+ encodeURIComponent(encodedToken);
                window.location.href = '<?php echo site_url('../CRM/omni_channel.php?token=')?>'+ encodeURIComponent(encodedToken);
               },2000);

            }

            history.go(-1)
         },
         error:function(error)
         {
             console.log('error')
             console.log(error)
         }


      });

     });

        function checkForNewData(count) {
          document.title = '\uD83D\uDCE9 New Message!';
          // Send an AJAX request to the server-side PHP script
          // fetch('getData.php')
          //     .then(response => response.json())
          //     .then(data => {
          //         // Check if there is new data
          //         if (data.newData) {
          //             // Update the tab title with a notification
          //              document.title = '\u2709 New Data Available!'; // Unicode characters for a message icon
          //             // You can also show a desktop notification here using the Notification API
          //         }
          //     });
        }
 
       
          // Check for new data periodically (e.g., every 5 seconds)
          // setInterval(checkForNewData, 5000); // 5000 milliseconds = 5 seconds
      
</script>

<!-- <div class="direct-chat-msg right"><div class="direct-chat-infos clearfix"><span class="direct-chat-name float-right" style="font-weight:100;font-size: smaller;color:#6c757d">Sarah Bullock</span><span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span></div><div class="direct-chat-text" style="background:aliceblue">What is this offer? Can you explain me?</div></div> --> 