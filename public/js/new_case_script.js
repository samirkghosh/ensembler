$(document).ready(function() {
   $.fn.select2.defaults.set("theme", "bootstrap");
   $(".select2").select2({
      width: null
   })

   /* Content Messaging Events -- Start <!-- Farhan Akhtar :: Modified On (16-10-2024) --> */

      // Email button click event
      $('.emailConMess').click(function() {
         let email = $("#email").val().trim();

         let customerid = $("#customerid").val();
         if(email == ""){
            alert('Please Enter Email ID');
            // Dismiss the modal with ID 'staticBackdrop'
            $('#staticBackdrop').modal('hide');
         }else{
            
         // If the email is not empty, open the new window
         window.open('omnichannel_config/web_send_email_reply.php?action="content-messaging"&reply_to='+email+'&customerid='+customerid,
            '_blank', 
            'height=550, width=900,scrollbars=0');
         }

      });

      // SMS button click event
      $('.smsConMess').click(function() {
         // Get the trimmed values of first and last names
         let firstName = $("#first_name").val().trim();
         let lastName = $("#last_name").val().trim();

         let customerid = $("#customerid").val();
         // Combine first and last name with a space in between
         let name = firstName + " " + lastName;
         let phone = $("#phone").val().trim();

         if(phone == ""){
            alert('Please Enter Registrer No');
            // Dismiss the modal with ID 'staticBackdrop'
            $('#staticBackdrop').modal('hide');
         }else{
            // If the email is not empty, open the new window
            window.open('omnichannel_config/contentMess_sms.php?action="content-messaging"&name='+name+'&phone='+phone+'&customerid='+customerid,
               '_blank', 
               'height=500, width=800,scrollbars=0');
         }

      });
      
      // instagram reply  code [vastvikta][15-04-2025]
      $('.instagramConMess').click(function() {
         // Get the trimmed values of first and last names
         let instagramhandle = $("#instagramhandle").val().trim();
 
         let customerid = $("#customerid").val();
 
 
         if(instagramhandle == ""){
            alert('Please Enter Instagram ID');
            // Dismiss the modal with ID 'staticBackdrop'
            $('#staticBackdrop').modal('hide');
         }else{
            // If the email is not empty, open the new window
            window.open('omnichannel_config/contentMess_instagram.php?action="content-messaging"&instagramhandle='+instagramhandle+'&customerid='+customerid,
               '_blank', 
               'height=500, width=800,scrollbars=0');
         }
 
      });

      // Whatsapp button click event
      $('.whatsappConMess').click(function() {
         // Logic for WhatsApp interaction
         let phone = $("#whatsapp_number").val().trim();
         let customerid = $("#customerid").val();
         if(phone == ""){
            alert('Please Enter Whatsapp No');
            // Dismiss the modal with ID 'staticBackdrop'
            $('#staticBackdrop').modal('hide');
         }else{
            // If the email is not empty, open the new window
            window.open('omnichannel_config/contentMess_whatsapp.php?action="content-messaging"&phone='+phone+'&customerid='+customerid,
               '_blank', 
               'height=500, width=800,scrollbars=0');
         }
      });

      // Messenger button click event
      $('.fbmessConMess').click(function() {
         // Logic for Facebook Messenger interaction
          let facebookid = $("#messengerhandle").val();
          let customerid = $("#customerid").val();
          if(facebookid == ""){
             alert('Please Enter Messenger Handle');
             // Dismiss the modal with ID 'staticBackdrop'
             $('#staticBackdrop').modal('hide');
          }else{
             // If the email is not empty, open the new window
             window.open('omnichannel_config/contentMess_messenger.php?action="content-messaging"&facebookid='+facebookid+'&customerid='+customerid,
                '_blank', 
                'height=500, width=800,scrollbars=0');
          }
      });

      // Chat button click event
      $('.liveChatConMess').click(function() {
         // Write your live chat functionality here
         alert('Live Chat button clicked inside modal');
         // Logic for live chat interaction
      });

   /* Content Messaging Events -- End */
});

$('#customerrr [name=type]').on('change', function() {
  var selvalue = $("[name='type']:checked").val();
  web_cat(selvalue, '');
  if (selvalue == 'complaint') {
     $("#assign_to_backofice").show();
     $("select#status_type_").val('1');
  }
  if (selvalue == 'Inquiry' || selvalue == 'others') {
     $("#assign_to_backofice").hide();
  }
});
//Get Category 
function web_cat(type, catid) {
   $.post("helpdesk/web_ticket_function.php", {
         type: type,
         catid: catid,
         'action':'ajax_Category',
      },
      function(data, status) {
         $("#category_div").html(data);
      });
}
//Get SubCategory 
function web_subcat(cat_id, subcat_id) {
   $.post("helpdesk/web_ticket_function.php", {
         cat_id: cat_id,
         subcat_id: subcat_id,
         'action':'ajax_subCategory',
      },
      function(data, status) {
         $("#subcategory_div").html(data);
      });
}
function get_department(category_id) {
   //alert('get_department ' +category_id);
   //console.log('type == ' + category_id);
   // console.log('catid == ' + catid);
   var option = '';
   var sel = '';
   $.ajax({
      url: 'helpdesk/web_ticket_function.php',
      type: 'post',
      data: {
         'category_id2': category_id,
         'action':'ajax_department'
      },
      dataType: 'json',
      success: function(data, status) {
         console.log('Department');
         console.log(data);
         option = '<option value="0">Select Department</option>';
         $.each(data, function(index, value) {
            option += '<option value=' + value.pId + ' selected >' + value.vProjectName + '</option>';
            get_assigne_email(value.pId);
         });


         $("#group_assign").empty();
         $("#group_assign").html(option);
      },
      error: function(xhr, desc, err) {
         console.log('err');
         console.log(err);
      },
      complete: function() {}
   });
}
/* Collect Email To show Which Diratment Assigned   */
function get_assigne_email(department_id) {
   //alert(type);
   console.log('department_id == ' + department_id);
   // console.log('catid == ' + catid);
   var option = '';
   var sel = '';
   $.ajax({
      url: 'helpdesk/web_ticket_function.php',
      type: 'post',
      data: {
       'department_id': department_id,
         'action':'ajax_Assign_Department'
      },
      // dataType: 'json',
      success: function(data, status) {
         $("#show_emails").empty();
         $("#show_emails").html('<span>' + data + '</span>').css('color', 'green');

      },
      error: function(xhr, desc, err) {},
      complete: function() {}
   });
}
/* Save  Customer Feedback Form */
$("#feedback_form").on('submit', (function(e) {
  e.preventDefault();
  console.log('ok Going to process');
  // console.log(new FormData(this));
  var remark = $("#customer_remark").val();
  if (remark == '') {
     alert('Please Enter Remark');
     $("#customer_remark").focus();
     return false;
  }
  $("#feedback_button").prop('disabled', true);
  $("#feedback_button").val('Please Wait...');
  $.ajax({
     url: 'helpdesk/process_ajax_request.php',
     type: "POST",
     data: new FormData(this),
     dataType: 'json',
     contentType: false,
     cache: false,
     processData: false,
     success: function(data) {
        console.log('data response');
        // console.log(data);
        $("#feedback_button").prop('disabled', false);
        $("#feedback_button").val('Save Feedback');
        if (data.status == "fail") {
           $("#responseMessage").text(data.message).css('color', 'red');
           // var message = "";
           // $.each(data.error, function (index, value) {
           //     message += value;
           // });
           // errorMsg(message);
        } else if (data.status == "success") {
           $("#responseMessage").text(data.message).css('color', 'green');
           $("#feedback_form :input").prop('disabled', true);
           $("#feedback_button").hide();
           $("#remark_button").hide();

           $("html, body").animate({
              scrollTop: 0
           }, "slow");
           var c_st = $("#save_current_status").val();
           $('select#current_status_type_').val(c_st);
           $('select#status_type_').val(c_st);
           if (c_st == '8') {
              $("#remark_button").prop('disabled', true);
              //$("#feedback_form").show();
              $("#addInteraction_form :input").prop('disabled', true);
              var rowid = $("#rowid").val();
           }
           var docket_no = $("#search-docket").val();
           getCustomerTicketHistory(docket_no, 'docket_no');
           console.log('aarti');
           setTimeout(function() {
              $("#responseMessage").text('');
           }, 2000);

        }
     },
     error: function(error) {
        console.log('Error');
        console.log(error);
     },
     complete: function() {
        $("#search-docket").prop('disabled', false);
        $("#search_btn").prop('disabled', false);

     }
  });
}));

/* Add new Interaction Form */
$("#addInteraction_form").on('submit', (function(e) {
  e.preventDefault();

  console.log('ok Going to process addInteraction_form');
  var remark = $("#interaction_remark").val();
  if (remark == '') {
     alert('Please Enter Remark');
     $("#interaction_remark").focus();
     return false;
  }
  
  $("#remark_button").prop('disabled', true);
  $("#remark_button").val('Please Wait...');
  $.ajax({
     url: 'helpdesk/web_ticket_function.php',
     type: "POST",
     data: new FormData(this),
     dataType: 'json',
     contentType: false,
     cache: false,
     processData: false,
     success: function(data) {
        console.log('data response');
        // console.log(data);
        $("#remark_button").prop('disabled', false);
        $("#remark_button").val('Save New Remark');
        if (data.status == "fail") {
           $("#responseMessage2").text(data.message).css('color', 'red');
        } else {
           $("#responseMessage2").text(data.message).css('color', 'green');
           // change value of status 
           var c_st = $("#save_current_status").val();
           $('select#current_status_type_').val(c_st);
           $('select#status_type_').val(c_st);
           if (c_st == '8') {
              $("#feedback_form").show();
              var rowid = $("#rowid").val();
              //$("#row_"+rowid).remove();
           }
           $("html, body").animate({
              scrollTop: 0
           }, "slow");
           setTimeout(function() {
              $('#addInteraction_form').hide();
           }, 1000);

        }
     },
     error: function(error) {
        $("#remark_button").prop('disabled', false);
        $("#remark_button").val('Save New Remark');
        console.log('Error');
        console.log(error);
     },
     complete: function() {
        $("#search-docket").prop('disabled', false);
        $("#search_btn").prop('disabled', false);
        setTimeout(function() {
           $("#interaction_remark").val('');
           $("#responseMessage2").text('');
        }, 2000);

     }
  });
}));
var ticketid = $("#ticketid").val();
var status_type_= $("#status_type_").val();;
if (ticketid && status_type_ == '8'){
  $("#feedback_form").show();
}
 var docket_no = $("#docket_no").val();
 var type = $("#type").val();
 var v_category = $("#v_category").val();
 var v_subcategory =$("#v_subcategory").val();
if (type) { 
  if (!empty(docket_no)){
    $('.docket_show').css('display', 'block');
    $('#search_res').css('display', 'none');
  }else{
   $('#phone').attr('readonly', true);
   $('#type1').attr('disabled', true);
   $('#type2').attr('disabled', true);
  }
  var complaint_type = type;
  web_cat(type, v_category);
  $("#v_category").val(v_category);
  if (complaint_type == 'complaint') {
    $("#type1").prop('checked', true);
    web_subcat(v_category, v_subcategory);
    setTimeout(function() {
       $("select#v_subcategory").prop('disabled', true);
       $("select#v_category").prop('disabled', true);
    }, 5000);
  }
  if (complaint_type == 'request') {
    web_subcat(v_category, v_subcategory);
    $("#type2").prop('checked', true);
    $("#assign_to_backofice").hide();
  }
}else{
  $('.docket_show').css('display', 'none');
  $("#type1").prop("checked", true);
  web_cat('complaint', '');
}
 function getCustomerTicketHistory(val, nkey) {
   /*
      This Section Add Sidebar to last cases
   */
   $.ajax({
      url: 'helpdesk/web_ticket_history.php',
      type: 'post',
      data: {
         'key': val,
         'nkey': nkey,
         'QUERY_STRING': 'getdata'
      },
      success: function(data, status) {
         console.log('====================================');
         // console.log(data);
         console.log('====================================');
         var obj = JSON.parse(data);
         var td_var = '';
         /* by farhan 12-04-2020 */
         var iPID = 0;
         var rowcount = 1;
         $.each(obj, function(index, val) {
            if (rowcount == 11) Continue;
            // console.log(val);
            td_var += '<tr><td>' + rowcount + '</td><td>' + val.date + '</td> <td>' + val.name + '</td> <td>' + val.ticketid + '</td> <td>' + val.caetgory + '</td> <td>' + val.status + '</td></tr>';
            iPID = val.iPID;
            rowcount++;
         });
         // console.log('Count ' + rowcount);
         if (rowcount == 0) {
            td_var += '<tr class="remove_row"><td colspan="6">No Record Found</td></tr>';
         }
         if (rowcount > 10) {
            td_var += '<tr class="remove_row"><td colspan="6"><button id="load_more" data-item="' + iPID + '" class="button-orange1">Load More</button></td></tr>';
         }
         /* end */
         // console.log(td_var);
         $("tbody#case_detail_table").empty();
         $("tbody#case_detail_table").append(td_var);
         // customer toggle
         if ($(".chat_container").is(":visible")) {
            $(".c_h .right_c .mini").text("+")
         } else {
            $(".c_h .right_c .mini").text("-")
         }

         $(".chat_container").slideToggle("slow");
      },
      error: function(xhr, desc, err) {
         //console.log(xhr);
         //console.log("Details: " + desc + "\nError:" + err);
      }
   });
   var PHP_SELF = $('#PHP_SELF').val();
   var query_string = $('#query_string').val();

   $.ajax({
      url: 'helpdesk/web_ticket_history.php',
      type: 'post',
      data: {
         'key': val,
         'nkey': nkey,
         'QUERY_STRING': query_string,
         'url': PHP_SELF
      },
      success: function(data, status) {
         console.log('get case details 11111');
         // console.log(data);
         $("#ticket_history").empty();
         $("#ticket_history").html(data);
         //$("#lasttickethistoy").empty();
         //$("#lasttickethistoy").html(data);
         $("#ticket_history_docket").hide();
      },
      error: function(xhr, desc, err) {
         //console.log(xhr);
         //console.log("Details: " + desc + "\nError:" + err);
      }
   });

   get_interaction_data(val);
}
// for interaction_data
function get_interaction_data(phone){
    $.ajax({
      url: 'helpdesk/web_ticket_function.php',
      type: 'post',
      data: {'phone':phone,'action':'interaction_data'},
      success: function(data, status) {
        $('#interaction_data').html(data);
         // Show the "Show More" button after data is successfully loaded[vastvikta][13-05-2025]
         $('#toggleInteractionBtn').show();
           
        setTimeout(function() {
          $(".ico-interaction2").colorbox({
              iframe: true,
              innerWidth: 1000,
              innerHeight: 550
          });   
        },1000)
      },
      error: function(xhr, desc, err) {
      }
   });
}
function search_customer(val, nkey) {
   //console.log(val+nkey);
   var PHP_SELF = $('#PHP_SELF').val();
   var query_string = $('#query_string').val();
   if (val.length >= 3) {
      $.ajax({
         url: 'helpdesk/search_customer.php',
         type: 'post',
         data: {
            'key': val,
            'nkey': nkey,
            'QUERY_STRING': query_string,
            'url': PHP_SELF
         },
         success: function(data, status) {
            if (nkey == 'customer') {
               $("#search_result").css("display", "block");
               $("#search_result").html(data);
            }
            if (nkey == 'city') {
               $("#search_result_city").css("display", "block");
               $("#search_result_city").html(data);
            }
         },
         error: function(xhr, desc, err) {
            //console.log(xhr);
            //console.log("Details: " + desc + "\nError:" + err);
         }
      });
   } else {
      if (nkey == 'customer') {
         $("#search_result").css("display", "none");
         $("#search_result").html("");
      }
      if (nkey == 'city') {
         $("#search_result_city").css("display", "none");
         $("#search_result_city").html("");
      }
   }
}
$('input[type=radio][name=call_type]').change(function() {
  //spam call populate values in form 
  if (this.value == 'real') {

     $("#first_name").val("");
     $("#last_name").val("");
     $("#gender").val("").change(); 
     $("#age").val("").change();
     $("#status_type_").val("").change(); 
     $("#v_remark_type").val("");
  }
  else if (this.value == 'spam') {

     $("#first_name").val("spam");
     $("#last_name").val("spam");
     $("#gender").val("M").change();  
     $("#age").val(3).change();
     $("#status_type_").val(3).change();
     $("#v_remark_type").val("spam");
     console.log("spam")
  }
});
$('#perpetrator').keypress(function (e) {    
  var charCode = (e.which) ? e.which : event.keyCode    
  if (String.fromCharCode(charCode).match(/[^0-9]/g))    
  return false;                        

});  
$('#affected').keypress(function (e) {    
  var charCode = (e.which) ? e.which : event.keyCode    
  if (String.fromCharCode(charCode).match(/[^0-9]/g))    
  return false;                        

});  
$('#servicepro').keypress(function (e) {    
  var charCode = (e.which) ? e.which : event.keyCode    
  if (String.fromCharCode(charCode).match(/[^0-9]/g))    
  return false;                        
});
$( "#perpetrator" ).keyup(function() {
    var val = $(this).val();
     if(val.length == 1)
     {
       $("#perpetrator").val("0"+val);
     }
 });
$(document).on('click', '#load_more', function(event) {
   event.preventDefault();
   var customerid = '0';
   var id = $('#load_more').attr('data-item');
   customerid = $("#customerid").val();
   if (customerid == '0') {
      customerid = $("#customerid").val();
   }
   $.ajax({
      url: "helpdesk/load_more.php",
      type: "post",
      data: {
         id: id,
         customerid: customerid
      },
      success: function(response) {
         $('#case_detail_table').html(response);
      }
   });
});
$(document).on('click', '#load_less', function(event) {
     event.preventDefault();
     var customerid = '0';
     var id = $('#load_less').attr('data-item');
     // customerid = '<?= $customer_id ?>';
     if (customerid == '0') {
        customerid = $("#customerid").val();
     }
     var revert = 'revert';
     //alert(revert);
     // alert(id);

     $.ajax({

        url: "helpdesk/load_more.php",
        type: "post",
        data: {
           id: id,
           customerid: customerid,
           revert: revert
        },
        success: function(response) {
           //$('#case_detail_table').remove();
           $('#case_detail_table').html(response);

           //alert(response);
        }

     });
  });
  changetpin();
  function changetpin(){
     $("input[name='profile_api_type']").change(function() {
          var test = $(this).val();
          // console.log(test);
          console.log('i am here');
          if(test == 'TPIN'){
            $(".nrc_div").hide();
            $(".brn_div").hide();
            $(".passport_div").hide();
            $(".tpin_div").show();
          }else if(test == 'NRC'){
            $(".nrc_div").show();
            $(".brn_div").hide();
            $(".passport_div").hide();
            $(".tpin_div").hide();
          }else if(test == 'BRN'){
            $(".nrc_div").hide();
            $(".brn_div").show();
            $(".passport_div").hide();
            $(".tpin_div").hide();
          }else if(test == 'passport'){
            $(".nrc_div").hide();
            $(".brn_div").hide();
            $(".passport_div").show();
            $(".tpin_div").hide();
          }            
     }); 
  }
  $("input[name='profile_api_type']").change(function() {
          var test = $(this).val();
          console.log(test);
          console.log('i am here');
          if(test == 'TPIN'){
            $(".nrc_div").hide();
            $(".brn_div").hide();
            $(".passport_div").hide();
            $(".tpin_div").show();
          }else if(test == 'NRC'){
            $(".nrc_div").show();
            $(".brn_div").hide();
            $(".passport_div").hide();
            $(".tpin_div").hide();
          }else if(test == 'BRN'){
            $(".nrc_div").hide();
            $(".brn_div").show();
            $(".passport_div").hide();
            $(".tpin_div").hide();
          }else if(test == 'passport'){
            $(".nrc_div").hide();
            $(".brn_div").hide();
            $(".passport_div").show();
            $(".tpin_div").hide();
          }            
     }); 
  function collapsible(){
     var coll = document.getElementsByClassName("collapsible");
     var i;
     for (i = 0; i < coll.length; i++) {
       coll[i].addEventListener("click", function() {
         this.classList.toggle("active");
         var content = this.nextElementSibling;
         if (content.style.maxHeight){
           content.style.maxHeight = null;
         } else {
           content.style.maxHeight = content.scrollHeight + "px";
         } 
       });
     }
     $('.close_return').on('click', function(){
        // console.log('close');
        $('.modal_hide').hide();
        document.getElementById('modal_hide').style.display='none';
     });
  }
  $(document).ready(function(){ 
    // Initialize select2
     $('#SelExampleC').select2();
  });
// form submit ajax call create ticket
$("#customerrr").on('submit', (function(e) {
  e.preventDefault();
  console.log('validate_form_submit-updated');
  var phone = $('#phone').val();
  if (phone.trim() == "") {
      alert("Please Enter Caller Number!");
      $('#phone').focus();
      return false;
  }
  var first_name = $('#first_name').val();
  if (first_name.trim() == "") {
      alert("Please Enter First Name!");
      $('#first_name').focus();
      return false;
  }
  var source = $('#source').val(); //mode
  if (source.trim() == "0") {
      alert("Please Select mode!");
      $('#source').focus();
      return false;
  }
  var typeVal = $("[name='type']:checked").val();
  if (typeVal == undefined) {
      alert("Please Select Reasons of Calling!");
      return false;
  }
  
  var v_category = $('#v_category').val();
  if (v_category.trim() == "0") {
      alert("Please Select Category !");
      $('#v_category').focus();
      return false;
  }
  var v_subcategory = $('#v_subcategory').val();
  if (v_subcategory.trim() == "0" || v_subcategory == "") {
      alert("Please Select Sub Category !");
      $('#v_subcategory').focus();
      return false;
  }
  if (typeVal == 'complaint') {
      var group = $('#group_assign').val();
      if (group.trim() == "0") {
          alert("Please Assign Department!");
          $('#group_assign').focus();
          return false;
      }
  }
  var status = $('#status_type_').val();
  if (status.trim() == "0") {
      alert("Please Select Status!");
      $('#status_type_').focus();
      return false;
  }
  var v_remark_type = $('#v_remark_type').val();
  if (v_remark_type.trim() == "") {
      alert("Please Enter Remarks!");
      $('#v_remark_type').focus();
      return false;
  }
  // button disabled code add for signle ticket create[Aarti][03-02-2025]
  // ajax call start
  $("#customerrr :input").prop('disabled', false);
    $("#create_ticket").prop('disabled', true);
    $("#create_ticket").val('Please Wait...');
    $.ajax({
       url: 'helpdesk/web_ticket_function.php',
       type: "POST",
       data: new FormData(this),
       dataType: 'json',
       contentType: false,
       cache: false,
       processData: false,
       success: function(data) {
          if(data.status){
            $('.successmanual').text('CASE ID :: '+data.ticketid);
            $('.successmanual').show();
            $('#docket_no_new').val(data.ticketid);
          }else{
            $('.errormanual').text('CASE ID :: '+data.message);
            $('.errormanual').show();
          }
          
          $("#create_ticket").val('Create');
          $("html, body").animate({
               scrollTop: 0
            }, "slow");

          setTimeout(function() {
              $('#show_ticket').hide();
              // $("#create_ticket").prop('disabled', false)
            }, 5000);
       },
       error: function(error) {
          $("#create_ticket").prop('disabled', false);
          $("#create_ticket").val('Create');
          console.log('Error');
          console.log(error);
       },
       complete: function() {
          // $("#create_ticket").prop('disabled', false);
          $("#create_ticket").val('Create');
       }
    });
}));
// This code for search filter option
function getCustomerDetails(customer){
  var customer_array = customer.split("||");
  var priority = customer_array[16];
  var priority_id = "#priority_"+priority;
  console.log("***********getCustomerDetails************");
  // console.log(customer_array);
  $("#search-box").val(customer_array[1]);
  $("#customerid").val(customer_array[0]);
  $("#first_name").val(customer_array[1]);  
  $("#c_full_name").val(customer_array[1]); 
  $("#phone").val(customer_array[2]);
  $("#c_mobile").val(customer_array[2]);
  $("#mobile").val(customer_array[3]);
  $("#last_name").val(customer_array[4]);
  $("#email").val(customer_array[5]);
  $("#address_1").val(customer_array[6]);
  $("#address_2").val(customer_array[7]);
  $("#district").val(customer_array[8]).change();
  $("#country").val(customer_array[9]).change();
  $("#lang").val(customer_array[10]);
  $("#fbhandle").val(customer_array[11]);
  $("#twitterhandle").val(customer_array[12]);  
  $("#age").val(customer_array[14]).change(); 
  $("#gender").val(customer_array[15]).change();  
  $("#company_name").val(customer_array[18]);
   $("#company_registration").val(customer_array[19]);
   $("#smshandle").val(customer_array[20]);
   $("#regional").val(customer_array[21]).change();
   $("#nationality").val(customer_array[22]);
   $("#SelExampleC").val(customer_array[23]).change();
   $("#whatsapp_number").val(customer_array[24]).change();
   $("#messengerhandle").val(customer_array[25]).change();//[vastvikta nishad][29-11-2024][for messenger id]
   $("#instagramhandle").val(customer_array[26]).change();//[vastvikta nishad][29-11-2024][for instagram id]
  $(priority_id).prop("checked", true); 
  $("#search_result").css("display","none");
  $("#search_result").html('');
  setTimeout(function() {
    $("#villages").val(customer_array[13]).change();
  },1000)
}
// fetch villages list
function get_villages(district_id) {
  console.log('type == ' + district_id);
  var option = '';
  var sel = '';
  $.ajax({
    url: 'helpdesk/web_ticket_function.php',
    type: 'post',
    data: {
      'district_id': district_id,
      'action':'ajax_fecth_district'
    },
    dataType: 'json',
    success: function(data, status) {
      console.log('Village');
      console.log(data);
      option = '<option value="0">Select District</option>';
      $.each(data, function(index, value) {
        option += '<option value=' + value.id + ' >' + value.vVillage + '</option>';
      });
      $("#villages").empty();
      $("#villages").html(option);
    },
    error: function(xhr, desc, err) {},
    complete: function() {}
  });
}
// check number validition
function NumOnly(obj) {
  if (IsNumeric(obj.value) == false) {
    alert("Not a valid number");
    obj.value = 0;
    obj.focus();
    return false;
  }
}
// check isnumberic
function IsNumeric(sText) {
  var ValidChars = "0123456789.";
  var IsNumber = true;
  var Char;
  var cnt;
  cnt = 0;
  for (i = 0; i < sText.length && IsNumber == true; i++) {
    Char = sText.charAt(i);
    if (ValidChars.indexOf(Char) == -1) {
      IsNumber = false;
    }
    if (Char == '.') {
      cnt = cnt + 1;
      if (cnt > 1) {
        IsNumber = false;
        cnt = 0;
        return IsNumber;
      }

    }
  }
  return IsNumber;
}
// check chonly validation
function ChkIntOnly(obj) {
  var val = obj.value
  if (isNaN(val)) {
    obj.value = '';
    for (var i = 0; i < val.length; i++) {
      if (!isNaN(obj.value + val.substring(i, i + 1))) {
        obj.value = obj.value + val.substring(i, i + 1);
      } else {
        return false;
      }
    }
  }
  return true;
}

/* Disposition Start--- */
  $("#callbk").change(function() {
  // Check if the checkbox is checked
  if ($(this).is(":checked")) {
     console.log("Callback checkbox is checked")
     $(".cb").show()
   } else {
    console.log("Callback Checkbox is unchecked")
    $(".cb").hide()
    }
  });
  // Event listener for the new button :: Dispose & Break :: Farhan Akhtar :: 31-01-2025
  $("#disposeAction").on("submit",function (event) {
      event.preventDefault()
       // updated the code for the alert on the call back time [vastvikta][27-03-2025]
      var isCallbackChecked = $("#callbk").is(":checked");
      var callbackTime = $("#cb_date").val(); // Date entered in DD-MM-YYYY HH:mm:ss

      if (isCallbackChecked) {
            if (callbackTime === '') {
               alert('Please Enter Callback Time');
               $("#cb_date").focus();
               return false;
            }

            // Convert selected callbackTime from DD-MM-YYYY HH:mm:ss to a proper JavaScript Date object
            var dateParts = callbackTime.split(" "); // Splitting date and time
            var dateOnly = dateParts[0].split("-"); // Splitting DD-MM-YYYY
            var timeOnly = dateParts[1].split(":"); // Splitting HH:mm:ss

            var inputDate = new Date(dateOnly[2], dateOnly[1] - 1, dateOnly[0], timeOnly[0], timeOnly[1]); // YYYY, MM (0-based), DD, HH, MM

            // Get current date and time
            var currentDate = new Date();

            // Remove seconds and milliseconds for comparison
            inputDate.setSeconds(0, 0);
            currentDate.setSeconds(0, 0);

            console.log("Current Date:", currentDate);
            console.log("Input Date:", inputDate);

            // Compare only Date & Time (ignoring seconds & milliseconds)
            if (inputDate < currentDate) {
               alert('Callback time cannot be a backdate.');
               $("#cb_date").focus();
               return false;
            }
         }
      // code ended here 
      let validate = validate_dispose()
      // added the code to  show alert on dispose remark [vastvikta][27-03-2025]
      var remark2 = $('#dispose_remark').val();
      if(remark2 == ''){
         alert("Please Enter Remark");
         $('#dispose_remark').focus();
         return false;
      }
      // code ended here 
      if(validate == true){
         
         let clickedButton = event.originalEvent.submitter; // The button clicked
         let formData = new FormData(this); // FormData object
 
         // Check if "Dispose & Break" button was clicked
        if (clickedButton.name === "btnDisposeNBreak") {
         formData.append("actionBreak", "dispose & break");
        }

        $("#processModel").show()
        $("#dispose").val('Please Wait...')

         // Get the button that triggered the submit

        //AJAX request
        $.ajax({
          url : 'helpdesk/web_ticket_function.php',
          type: "POST",
          data: formData,
          dataType: 'json',
          contentType: false,
          cache: false,
          processData: false,
           //  updated the success part so that alert can be displayed[vastvikta][27-03-2025]
          success: function (data) { 
            console.log('#############disposeAction#################');
            setTimeout(() => {
                $("#processModel").hide();
                $("html, body").animate({scrollTop: 0}, "slow");
            }, 1000);
            
            
        
            if (data.status === "success") {
                $(".alert").css("background", "#00CC66");
                $(".alert").css("display", "block");
                $(".alert").text(data.message);
                $("#dispose").hide();
                localStorage.removeItem('AgentOnCall');
                setTimeout(() => {
                    window.location.href = data.url;
                }, 4000);
            } else {
                $(".alert").css("background", "crimson");
                $(".alert").css("display", "block");
                $(".alert").text(data.message);
                
                // Explicit alert for failed messages
                alert(data.message);
        
                setTimeout(() => {
                    $(".alert").css("display", "none");
                }, 2000);
        
                $("#dispose").val("DISPOSE");
                $("#dispose").show();
            }
        },
        error: function (error) {
            console.log(error);
            alert("An error occurred. Please try again.");
            $("#processModel").hide();
            $("#dispose").val("DISPOSE");
            $("#dispose").show();
        }
        });
      }else{
        return false
      }
  });

   // $("input[name='btnDisposeNBreak']").on("submit", function (event) {
   //    event.preventDefault();
   //    let validate = validate_dispose();
   //    if (validate == true) {
   //       $("#processModel").show();
   //       $("#dispose").val('Please Wait...');

   //       // Create FormData object from the form
   //       let formData = new FormData(this); // 'this' refers to the form
         
   //       // Append the extra parameter
   //       formData.append('actionBreak', 'dispose & break');

   //       // AJAX request
   //       $.ajax({
   //          url: 'helpdesk/web_ticket_function.php',
   //          type: "POST",
   //          data: formData,
   //          dataType: 'json',
   //          contentType: false,
   //          cache: false,
   //          processData: false,
   //          success: function (data) {
   //                setTimeout(() => {
   //                   $("#processModel").hide();
   //                   $("html, body").animate({ scrollTop: 0 }, "slow");
   //                }, 1000);
   //                console.log(data);
   //                if (data.status == "success") {
   //                   $(".alert").css("background", "#00CC66");
   //                   $(".alert").css("display", "block");
   //                   $(".alert").text(data.message);
   //                   $("#dispose").hide();
   //                   localStorage.removeItem('AgentOnCall');
   //                   setTimeout(() => {
   //                      window.location.href = data.url;
   //                   }, 4000);
   //                } else {
   //                   $(".alert").css("background", "crimson");
   //                   $(".alert").css("display", "block");
   //                   $(".alert").text(data.message);
   //                   setTimeout(() => {
   //                      $(".alert").css("display", "none");
   //                   }, 2000);
   //                   $("#dispose").val("DISPOSE");
   //                   $("#dispose").show();
   //                }
   //          },
   //          error: function (error) {
   //                console.log(error);
   //                $("#processModel").hide();
   //                $("#dispose").val("DISPOSE");
   //                $("#dispose").show();
   //          }
   //       });
   //    } else {
   //       return false;
   //    }
   // });
   // END
  function validateCompanyName(input) {
   // Regular expression to allow only letters and spaces
   const regex = /^[a-zA-Z\s]*$/;

   if (!regex.test(input.value)) {
      alert("Please enter a valid company name.");
      input.value = input.value.replace(/[^a-zA-Z\s]/g, ""); // Remove invalid characters
   }
}
  function isAlphabetKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if ((charCode < 65 || charCode > 90) && (charCode < 97 || charCode > 122)) {
        return false;
    }
    return true;
}
// Reinitialize Colorbox for new elements
/* Disposition End--- */