const searchBar = document.querySelector(".search input"),
searchIcon = document.querySelector(".search button"),
usersList = document.querySelector(".users-list"),
upClick = document.querySelector("#upClick"),
dwnClick = document.querySelector("#dwnClick"),
details_list = document.querySelector(".chatbox_click");

const form = document.querySelector(".typing-area"),
incoming_id = form.querySelector(".incoming_id").value,
inputField = form.querySelector(".input-field"),
sendBtn = form.querySelector(".send_msg"),
chatBox = document.querySelector(".chat-box");

form.onsubmit = (e)=>{
    e.preventDefault();
}

inputField.focus();
inputField.onkeyup = ()=>{
    if(inputField.value != ""){
        sendBtn.classList.add("active");
    }else{
        sendBtn.classList.remove("active");
    }
}
 // click on search icon display inputbox
searchIcon.onclick = ()=>{
  console.log('here is is');
  searchBar.classList.toggle("show");
  searchIcon.classList.toggle("active");
  searchBar.focus();
  if(searchBar.classList.contains("active")){
    searchBar.value = "";
    searchBar.classList.remove("active");
    startInterval();
  }
}

//searching for user
searchBar.onkeyup = ()=>{
  let searchTerm = searchBar.value;
  console.log('searchTerm');
  if(searchTerm != ""){
    searchBar.classList.add("active");
  }else{
    searchBar.classList.remove("active");
  }
  var unique_id = $('.unique_id').val();
  $.ajax({
      type: "GET",
      url: "IMApp/function.php",
      data: { action:"get_chat",unique_id:unique_id,searchTerm:searchTerm},
      success:function(result){
        data_record = JSON.parse(result);
        let data = data_record.output;
        usersList.innerHTML = data;
        

      }
  });
}

//getting all user list with latest msg
setInterval(() =>{
  startInterval();
}, 8000);
function startInterval() {
    var unique_id = $('.unique_id').val();
      $.ajax({
        type: "GET",
        url: "IMApp/function.php",
        data: { action:"get_chat",unique_id:unique_id},
        success:function(result){
          data_record = JSON.parse(result);
          let data = data_record.output;
          if(!searchBar.classList.contains("active")){
            usersList.innerHTML = data;
          }
          setInterval(() =>{
            if (localStorage.getItem("nofity") == null) {
              var notify = data_record.notify;
              display_notification(notify,unique_id);
            }
          }, 5000);
        }
      });
};

//display notification on header
function display_notification(notify,unique_id){
  if(notify>0){
    console.log('display_notification');
    $('.alert').show();
    localStorage.setItem('nofity', 'aarti');
    setInterval(() =>{
        $.ajax({
            type: "POST",
            url: "IMApp/function.php",
            data: { action:"notification_update",unique_id:unique_id},
            success:function(result){
                // setInterval(() =>{
                  localStorage.removeItem('nofity');
                  localStorage.clear();
                  $('.alert').hide();
                // }, 5000);
            }
        });
      }, 8000);
  }
}

var intervalID;
//on reload hide chatbox
$('.chat_message_user').hide();
$('.body_msg').hide();
document.getElementById('upClick').style.display = 'block';
document.getElementById('dwnClick').style.display = 'none';   
document.getElementsByClassName('users-list')[0].style.display = 'block';
// document.getElementsByClassName('search')[0].style.display = 'block';

// on click user then prepare chat layout
setTimeout(function() {
  $(document).on('click','.flow',function(){
      clearInterval(intervalID);
      var name = $(this).find('.chatbox_click').data("name");
      var status = $(this).find('.chatbox_click').data("status");
      var img = $(this).find('.chatbox_click').data("img");
      $('.chat_message_user').show();
      $('.chat_message_user').find('.user_name').text(name);
      $('.chat_message_user').find('.user_status').text(status);
      $('.chat_message_user').find('.user-img').attr('src',img);

      var userid = $(this).find('.chatbox_click').data("userid");
      var frm_incomingid = $(this).find('.chatbox_click').data("incomingid");
      var incoming_id = $(this).find('.chatbox_click').data("incomingid");

      $('.chat_message_user').find('.typing-area').find('.incoming_id').val(frm_incomingid);
      var from = $('.unique_id').val();

      intervalID = setInterval(() =>{
        get_user_chatdata(frm_incomingid,from);
      }, 1000);

      //update read message in db
      update_read_message(from,frm_incomingid);
  });
}, 8000);

//start getting chat data
function get_user_chatdata(frm_incomingid,from){
  if(frm_incomingid){
    $.ajax({type: "GET",
        url: "IMApp/function.php",
        data: { action:"get_chat_user",unique_id:from,userid:frm_incomingid},
        success:function(result){
          data_record = JSON.parse(result);
          let innerHTML = data_record.output;
          chatBox.innerHTML = innerHTML;
          //update read message in db
          if(data_record.total>0){
            console.log('update_read_message')
              update_read_message(from,frm_incomingid);
          }
          if(!chatBox.classList.contains("active")){
            scrollToBottom();
          }
        }
    });
  }
}

//getting count of unread msg
setInterval(() =>{
    var from = $('.unique_id').val();
    $.ajax({type: "GET",
        url: "IMApp/function.php",
        data: { action:"count_message",unique_id:from,flag:'all'},
        success:function(result){
          if(result>0){
            $('.count').text(" ("+result+")");
          }else{
            $('.count').text('');
          }
        }    
    });
},8000);

//update unread to read msg
function update_read_message(sender,recived){
    $.ajax({
        type: "POST",
        url: "IMApp/function.php",
        data: { action:"update_read_message",sender:sender,recived:recived},
        success:function(result){ 
        }    
    });
}

//close-button click hide chat
$('.close-button-click').click(function(){
  clearInterval(intervalID);
  $(this).closest('.chat-area').find('.chat-box').text('');
  $(this).closest('.chat-area').find('.user_name').text('');
  $(this).closest('.chat-area').find('.user_status').text('');
  $(this).closest('.chat-area').find('.typing-area').find('.incoming_id').val('');
  $('.chat_message_user').hide();
});

//send msg to user
sendBtn.onclick = ()=>{
    var from = $('.unique_id').val();
    var frm_incomingid =  $('.chat_message_user').find('.typing-area').find('.incoming_id').val();
    var message = $('#message').val();
    $.ajax({type: "GET",
          url: "IMApp/function.php",
          data: { action:"insert_message",unique_id:from,incoming_id:frm_incomingid,message:message},
          success:function(result){
              inputField.value = "";
              scrollToBottom();
          }
    
    });
}
chatBox.onmouseenter = ()=>{
    chatBox.classList.add("active");
}
chatBox.onmouseleave = ()=>{
    chatBox.classList.remove("active");
}
function scrollToBottom(){
  chatBox.scrollTop = chatBox.scrollHeight;
}
//on click hide/show chatbox
// upClick.onclick = ()=>{
dwnClick.onclick = ()=>{
  clearInterval(intervalID);
    console.log('upClick');
    $('.body_msg').show();
    document.getElementsByClassName('body_msg')[0].style.display = 'none';
     document.getElementById('upClick').style.display = 'block';
    document.getElementById('dwnClick').style.display = 'none';  
     document.getElementsByClassName('users-list')[0].style.display = 'none';
    document.getElementsByClassName('search')[0].style.display = 'none';
    
};
upClick.onclick = ()=>{
  clearInterval(intervalID);
    console.log('dwnClick');
    $('.body_msg').show();
    document.getElementsByClassName('body_msg')[0].style.display = 'block';
    document.getElementById('upClick').style.display = 'none';
    document.getElementById('dwnClick').style.display = 'block'; 
    // document.getElementsByClassName('search')[0].style.display = 'block';
    $('.search').show();
    document.getElementsByClassName('users-list')[0].style.display = 'block';
    
};