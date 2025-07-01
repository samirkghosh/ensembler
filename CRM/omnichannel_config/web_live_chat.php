<?php
/***
 * Chat Listing page
 * Auth: Vastvikta Nishad
 * Date:  26 Mar  2024
 * Description: Handling Chat Listing Data Also Create Case and Case details 
 * 
*/
// Including necessary files
include_once("../../config/web_mysqlconnect.php");
include_once("../function/classify_function.php");
// Checking module license 
$module_flag_customer = module_license('chat');
$id = $_POST['id'];
if($module_flag_customer !='1'){
   // Redirecting to dashboard if module license is not valid
   header("Location:web_admin_dashboard.php"); 
   exit();
     }
   /***END***/
   $name= $_SESSION['logged'];
   $todaysdate=
   $selection1= $_REQUEST['selection'];
   if($_GET['seltype']){ $selection1=$_GET['seltype']; }
   $iallstatus=(isset($_REQUEST['allstatus'])) ? $_REQUEST['allstatus'] : 4; ////              Status 
   $msg=$_REQUEST['mg'];
   $email=$_REQUEST['email'];   	
	?>
    <!-- Toastr -->
  <link rel="stylesheet" href="../../chatbox/assets/plugins/toastr/toastr.min.css">
<body >
      <div class="col-sm-10 mt-3" style="padding-left:0">
         <!-- Start Right panel -->
         <span class="breadcrumb_head" style="height:37px;padding:9px 16px">Web Chat</span>
         <div class="Reports-page#" style="display: block;padding: 15px;border: #d4d4d4 1px solid;margin-bottom:20px;min-height: 420px;margin-top:37px">
            <form method="post" name="cfrm" id="post">
               <?php
               $selectedview = getSelectedView($selection1);
                  ?>
               <div class="style-title2" style="margin-top:-1px; display: none;">
               </div>
               <div class="table" id="facebook">
                  <table width="100%" align="center" border="0" class="tableview tableview-2">
                     <tr class="background">
                        <td align="center" valign="middle" width="2%" style="text-align: center;"> S.No</td>
                        <td align="center" valign="middle" width="8%">User</td>
                        <td align="center" valign="middle" width="8%">Message</td>
                        <td align="center" valign="middle" width="8%">Mobile No</td>
                        <!-- <td align="center" valign="middle" width="10%">Comment </td> -->
                        <td align="center" valign="middle" width="8%">Date </td>
                        <!-- <td align="center" valign="middle" width="4%">Create Case </td> -->
                        <td align="center" valign="middle" width="4%">Action </td>
                     </tr>
                     <?php
                         // Call the function passing the database connection
                           $query_data = getChatSessionData();
                        $query = ''; 
                        $sno = 0;
                        while($row = mysqli_fetch_assoc($query_data))
                        {
                           $sno++;
                              $userid = $row['from'];
                              $chat_session = $row['chat_session'];
                         ?>
                     <tr>
                        <td align="center" valign="top" style="text-align: center;"><?=$sno?></td>
                        <td align="center" valign="top" style="word-break: break-all;"><?=$row['name']?></td>
                        <td align="center" valign="top" style="word-break: break-all;"><?=$row['content_text']?></td>
                        <td align="center" valign="top" style="text-align: center;"><?=$row['from']?></td>
                        <td align="center" valign="top" style="text-align: center;"><?=$row['createdDatetime']?></td>
                        <td> 
                          <?php  if($row['user_id'] == 0 ){ ?>
                          <a href="javascript:void(0)" id="joinbtn_<?php echo $row['conversation_id'] ;?>" onclick="join_agent_chat('<?php echo $row['conversation_id'] ?>','<?php echo $chat_session ?>')" >Join</button>
                            <?php } else { ?>
                              Joined By Agent
                            <?php } ?>
                        </td>
                     </tr>
                     <? }
                        if(mysqli_num_rows($query_data)<=0){
                         ?>
                     <tr>
                        <td align="center" valign="top" style="text-align: center;" colspan="6">No Record!</td>
                     </tr>
                     <? }
                        ?>
                  </table>
               </div>
               <!-- table right panel End -->
         </div>
         </form>
      </div>
   </div>
   <!-- End Right panel --> 
   <!-- Toastr -->
<script src="../../chatbox/assets/plugins/toastr/toastr.min.js"></script>
<script src="../../chatbox/assets/js/sstoast.js"></script>
   <!-- Container Start -->
   <script type="text/javascript">
      function join_agent_chat(conversation_id,chat_session) {
         console.log('zConvertion id '+ conversation_id );
        //var url = '<?php //echo site_url('agentbot/agentjoin_customer_bot_ajax')?>';
        $("#joinbtn_"+conversation_id).text('joining...').prop('disabled', true);
        $.ajax({
         url: '<?php echo $SiteURL; ?>wapchat/Agentwebchatbot/agentjoin_customer_bot_ajax',
          data:{'conversation_id' : conversation_id},
          dataType:'JSON',
          method:'POST',
          // processData:false,
          // contentType:false,
          // cache:false,
          success:function(data, textStatus, jqXHR){
            console.log('data');
            console.log(data);
            if(data.status=='success'){
                  $("#joinbtn_"+conversation_id).text('joined').prop('disabled', false);
                  // $("#joinbtn_"+conversation_id).text('Joined By '+'<?php echo $_SESSION['logged'];?>').prop('disabled', false);
               successMsg(data.msg);
               setTimeout(function(){
                 // window.location.href = 'http://165.232.183.220/ensembler/wapchat/Agentwebchatbot/agentwindow';
               window.open("<?php echo $SiteURL; ?>wapchat/Agentwebchatbot/agentwindow?chat_session=" + chat_session, '_blank');
               },2000); 
            }
            else{
               $("#joinbtn_"+conversation_id).text('join').prop('disabled', false);
              errorMsg(data.msg);
            }
          },
          error:function(jqXHR, textStatus, errorThrown){
            $("#joinbtn_"+conversation_id).text('join').prop('disabled', false);
            console.log(jqXHR);
          },
          complete:function(){},
        });
      }
   </script>
</body>
</html>
