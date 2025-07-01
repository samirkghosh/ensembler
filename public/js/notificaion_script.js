$('.date_class').datetimepicker({
	format : 'd-m-Y H:i:s',
	formatTime:'H:i:s',
	formatDate:'d-m-Y'
});

$(function () {
 $(".c_h").click(function(e) {
 if ($(".chat_container").is(":visible")) {
 $(".c_h .right_c .mini").text("+")
 } else {
 $(".c_h .right_c .mini").text("-")
 }
 $(".chat_container").slideToggle("slow");
 return false
 });
});
   $(document).ready(function () {
    // Get notification
    function notification(){
        var url = 'notification/get_notif_data.php';
        $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: url,
            success: function(data) {
                // console.log('data')
                // console.log(data)

                if (data.status === 'success') 
                {
                    var obj = data.info;          
                $("#fbcount").text(obj.fbcount);
                $("#mailcount").text(obj.mailcount);
                $("#tweetcount").text(obj.tweetcount);
                $("#chatcount").text(obj.chatcount);
                $("#smscount").text(obj.smscount);
                $("#fbMcount").text(obj.fbMcount);
                $("#whatsapp_count").text(obj.whatsapp_count);
                $("#instagram_count").text(obj.instagram_count);
                console.log("Misscall Count:", obj.misscall_count); // Log the misscall_count value[vastvikta][03-02-2025]
        
                $("#misscall_count").text(obj.misscall_count);
             
                } 
            },
            error: function(data) {
                console.log('error')
                // console.log(data)
            }
        });
    }
    // miscall and voicemail today count
    function ajaxCall() {
        $.ajax({    
          type: "POST",
          url: "notification/voicemail_notif.php",             
          dataType: "html",                
          success: function(data){                    
              //console.log('voicemail')
              //console.log(data)
              $("#voicemail").html('('+data+')');

            }
        });
        $.ajax({    
            type: "POST",
            url: "notification/misscall_notif.php",             
            dataType: "html",                
            success: function(data){                    
                // console.log('misscall')
                // console.log(data)
                $("#misscall").html('('+data+')');
            }
        });
    }
    function workingStatus(){
        $.ajax({    
          type: "POST",
          url: "working_omnichannel.php",                            
          success: function(data){                    
              // console.log(data)
          }
        });
    }

    // ajaxCall(); // To output when the page loads
    setInterval(ajaxCall, (10 * 1000)); // x * 1000 to get it in seconds

    // notification();
    setInterval(notification, (10 * 1000)); // x * 1000 to get it in seconds

    setInterval(function() { 
        // workingStatus();
    },300000);

       //Examples of how to assign the Colorbox event to elements
      $(".ico-interaction2").colorbox({
      iframe: true,
      innerWidth: 800,
      innerHeight: 600
      });
   
       $(".ico-setting").colorbox({ iframe: true, innerWidth: 520, innerHeight: 380 });
       $(".ico-display").colorbox({ iframe: true, innerWidth: 800, innerHeight: 85 });
       $(".kno-display").colorbox({ iframe: true, width: "50%", height: "80%" });
       $(".supportsection").colorbox({ iframe: true, innerWidth: 550, innerHeight: 400 });
       $(".newdocument").colorbox({ iframe: true, innerWidth: 550, innerHeight: 280 });
       $(".group3").colorbox({ rel: "group3", transition: "none", width: "75%", height: "75%" });
       $(".group4").colorbox({ rel: "group4", slideshow: true });
       $(".ajax").colorbox();
       $(".form-ele").colorbox({ iframe: true, innerWidth: 250, innerHeight: 390 });
       $(".vimeo").colorbox({ iframe: true, innerWidth: 500, innerHeight: 409 });
       $(".iframe").colorbox({ iframe: true, width: "80%", height: 520 });
       $(".inline").colorbox({ inline: true, width: "450" });
       $("#inline_service_click").colorbox({ inline: true, width: 450, innerHeight: 420 });
       $(".inline_service_click2").colorbox({ inline: true, width: 450, innerHeight: 420 });
       $(".inline2").colorbox({ inline: true, width: "450", height: "80%" });
       $(".callbacks").colorbox({
           onOpen: function () {
               alert("onOpen: colorbox is about to open");
           },
           onLoad: function () {
               alert("onLoad: colorbox has started to load the targeted content");
           },
           onComplete: function () {
               alert("onComplete: colorbox has displayed the loaded content");
           },
           onCleanup: function () {
               alert("onCleanup: colorbox has begun the close process");
           },
           onClosed: function () {
               alert("onClosed: colorbox has completely closed");
           },
       });
   
       $(".non-retina").colorbox({ rel: "group5", transition: "none" });
       $(".retina").colorbox({ rel: "group5", transition: "none", retinaImage: true, retinaUrl: true });
   
       //Example of preserving a JavaScript event for inline calls.
       $("#click").click(function () {
           $("#click").css({ "background-color": "#f00", color: "#fff", cursor: "inherit" }).text("Open this window again and this message will still be here.");
           return false;
       });
   });
   document.addEventListener("DOMContentLoaded", function() {
       // make it as accordion for smaller screens
       if (window.innerWidth < 992) {
   
           document.querySelectorAll('.sidebar .nav-link').forEach(function(element) {
   
               element.addEventListener('click', function(e) {
   
                   let nextEl = element.nextElementSibling;
                   let parentEl = element.parentElement;
                   let allSubmenus_array = parentEl.querySelectorAll('.submenu');
   
                   if (nextEl && nextEl.classList.contains('submenu')) {
                       e.preventDefault();
                       if (nextEl.style.display == 'block') {
                           nextEl.style.display = 'none';
   
                       } else {
                           nextEl.style.display = 'block';
                       }
   
                   }
               });
           })
       }
       // end if innerWidth
   
   });
   // DOMContentLoaded  end

   /****Bulletin feching code start****/
   setInterval(function () {
        $(".holder > p:first")
            .removeClass("middle, right")
            .next()
            .addClass("middle").removeClass("right")
            .end()
                      
            .addClass("right").removeClass("middle").appendTo(".holder");
    }, 6000); 

  fetching_bulletin();
   inverval_timer = setInterval(function() { 
        fetching_bulletin();
    },30000);

/*fetching bulletin details*/
function fetching_bulletin(){
    var Data = {}
    Data.action = 'fetching_bulletin';
    var url = "common_function.php";
    $.ajax({
        url: url,
        type: "POST",
        data:  Data,
        dataType:"JSON",
        success: function(data){
        //  console.log('fetching_message_list');
         $('.holder').html(data);
        }
    });
}
$(document).ready(function () {
    $('#admin_table').DataTable({
    "ordering": false,
    "pageLength": 25
    });

    $('#admin_table2').DataTable({
    "ordering": false,
    "pageLength": 25
    });
});
// [Aarti][16-04-2024] for this code channel configuration and channel license acces provide
  $('.disable_menu').click(function(){
      $("#popupContainer").fadeIn();
      setTimeout(function(){ 
          $("#popupContainer").fadeOut();
      }, 2000);
  });
  $('.disable_channel').click(function(){
      $("#popupContainer_channel").fadeIn();
      setTimeout(function(){ 
          $("#popupContainer_channel").fadeOut();
      }, 2000);
  });