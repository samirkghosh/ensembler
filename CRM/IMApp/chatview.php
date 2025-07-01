<?php 
/***
 * IM Page
 * Author: Aarti Ojha
 * Date: 07-11-2024
 * Description: This file handles IM Realtime Chat chat flow
 */
  include_once("IMApp/function.php");
?>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Realtime Chat App</title>
    <link rel="stylesheet" href="IMApp/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"
      type="text/javascript"></script>
  </head>
<style type="text/css">
  .content_1{
  display: flex;
  align-items: center;
}
.content_1 .details{
  color: #000;
  margin-left: 20px;
}
.content_1 .details{
    margin-left: 15px;
  }
  /* Status icon styles */
#status-icon_IM {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    cursor: pointer;
    margin-right: 5px;
    vertical-align: middle;
}
  /* Status icon styles */
#status-icon{
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    cursor: pointer;
    margin-right: 5px;
    vertical-align: middle;
}
.status-available {
    background-color: green;
}

.status-away {
    background-color: gray;
}
</style>
  <div class="alert alert-info fade in alert-dismissible" style="display: none;">
    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
    <strong>Info!</strong> New message coming 
  </div>

  <div class="wrapper chatlayout wrapper_new">
    <input type="hidden" name="unique_id" class="unique_id" value="<?php echo $_SESSION['unique_id'];?>">
    <section class="users msg_section">
      <header class="messageheader">
        <div class="content_1">
          <?php 
          $outgoing_id = $_SESSION['unique_id'];
          $ChatRooms = new ChatRooms;
          $row = $ChatRooms->get_login_user_details($outgoing_id);
          $images = "IMApp/images/dummy.jpg";
          
          ?>
          <img src="<?php echo $images;?>" alt="">
          <div class="details">
            <span><?php echo $row['AtxDisplayName']?></span><span class="count"></span>
            <!-- This code handles updating the user’s status (e.g., Available, Away) in the database. -->
            <!-- Aarti ojha [07-11-2024] -->
            <span id="status-icon_IM" class="status-available"></span>
            <!-- Close -->
            <p id="IM_statustext"><?php echo $row['login_status']; ?></p>
          </div>
        </div>
       <div id="upClick"><i class="arr-up"></i></div>
       <div id="dwnClick" style="display:none"><i class="arr-down"></i></div>
      </header>
      <div class="body_msg">
        <div class="search">
          <span class="text">Select an user to start chat</span>
          <input type="text" placeholder="Enter name to search...">
          <button><i class="fas fa-search"></i></button>
        </div>
        <div class="users-list">
        </div>
      </div>
    </section>
  </div>
  <!-- chatRoom Layout -->
  <div class="wrapper chat_message_user wrapper_new" style="display:none">
    <section class="chat-area">
      <header class="chat_msg_header">
        <img class="user-img" src="" alt="">
        <div class="details">
          <span class="user_name"></span>
          <p class="user_status"></p>
        </div>
        <div class="close-button-click"><button class="close-button" aria-label="Close alert" type="button" data-close>
          <span aria-hidden="true">&times;</span>
        </button></div>
      </header>
      <div class="chat-box">
      </div>
      <form action="#" class="typing-area">
        <input type="text" class="incoming_id" name="incoming_id" value="" hidden>
        <input type="text" name="message" id="message" class="input-field" placeholder="Type a message here..." autocomplete="off">
        <div class="send_msg" onclick="btnB();"><button><i class="fab fa-telegram-plane"></i></button></div>
      </form>
    </section>
  </div>
<script src="IMApp/javascript/ChatRooms.js"></script>
