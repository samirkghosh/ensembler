//*******************************************Code Start case details backoffice page*********************************
// get agent status from autodial_live_agent starts
function clickcall_c2c(mobile, cid, trunk, agent) {
   $.ajax({
      url: "../../../universus/checkAgentStatus.php",
      type: "POST",
      data: {
         'user': agent,
      },
      async: true,
      crossDomain: true,
      success: function(result) {
         console.log('Agent status:' + result);
         if (result != "READY" && result != "REST") {
            var userWidth = screen.availWidth;
            var userHeight = screen.availHeight;
            var popW;
            var popH;
            var leftPos;
            var topPos;
            popW = 500;
            popH = 260;
            settings =
               'modal,scrollBars=no,resizable=no,toolbar=no,menubar=no,location=no,directories=no,';
            leftPos = (userWidth - popW) / 2,
               topPos = (userHeight - popH) / 2;
            settings += 'left=' + leftPos + ',top=' + topPos + ',width=' + popW + ', height=' +
               popH + '';
            window.open("../../universus/clicktocall_popup.php?V_Dial_Number=" + mobile + "&cid=" +
               cid + "&trunk=" + trunk + "&agent=" + agent, "Clicktocall", settings, 'true');
         } else {
            alert("You can't make call in READY mode or in Rest Break\n\nPlease chose Preview mode  to initiate a Dial Out");
       
         }
      }
      // get agent status from autodial_live_agent ends
   });
}
function Rejected() {
   //document.getElementById('div1').style.display ='none';
   document.getElementById('div1').style.display = 'block';
   $("#back_office_action").hide();
   $("#suggested_follow").hide();
   $("#OverAllRemark_div").show();
   $("#div2").show();
}
function Validated() {
   document.getElementById('div1').style.display = 'block';
   $("#back_office_action").show();
   $("#suggested_follow").show();
   $("#OverAllRemark_div").hide();
   $("#div2").hide();
}
// take feed from customer if agent resolve the case 
// Vijay : 30-11-2020
function getfeedback(complaint_status) {
   if (complaint_status == '8') {
      $("#feedbackdiv").show();
   } else {
      $("#feedbackdiv").hide();
   }
}
function cl121(nval, val) {
   document.getElementById('rec_f').innerHTML = "<embed height='40' width='100%' src='" + nval +
      "' type='audio/mpeg'>";
   return false;
}
// for validation email fields
function ValidateEmail(email) {
   if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
      return (true)
   }
   alert("You have entered an invalid email address!")
   $("#email").focus();
   return (false);
}

function sendpdf_form() {
   var emails = $("input#case").val();
   var quotation = $("input#quotation").val();
   if (emails == "") {
      alert('Please Select Suggested Follow');
      return false;
   }
   var uri = "suggested_followup_mail.php?quotation=" + quotation + "&case=" + emails;
   var uri = encodeURI(uri)
   // document.getElementById("myForm").target = "_blank";
   window.open(uri);
   // window.location.href =uri;
}
function add_remove_emails(email_id) {
   console.log('hkajhdjka');
   var id = 'suggested_follow_' + email_id;
   var newemails = '';
   if ($("#" + id).is(":checked")) {
      console.log('emails in if case addddddddd');

      var emails = $("input#case").val();
      if (emails == '') {
         $("#case").val(email_id);
      } else {
         emails += ',' + email_id;
         $("#case").val(emails);
      }
   } else {
      console.log('emails in else case remove ');

      // var email_id = 'suggested_follow_'+email_id;
      var emails = $("#case").val();
      res = emails.split(',');

      for (var i = 0; i < res.length; i++) {
         if (res[i] != email_id) {
            newemails += res[i] + ',';
         }
      }
      newemails = newemails.substr(0, newemails.length - 1);
      $("#case").val(newemails);
   }
}

/* Add More Interaction case wise store Values docked_id, customer_id, source */
function addmore_interaction(docked_id, customer_id, source, rowid) {
   if (docked_id != '') {
      $("#docket_no").val(docked_id);
      $("#rowid").val(rowid);
      // $("#source_id").val(source);
      $("#customer_id").val(customer_id);

      $("#ticket_no").val(docked_id);
      $("#feed_source_id").val(source);
      $("#feed_customer_id").val(customer_id);

      $("#addInteraction_form").show();
   }
}
//for change status code
function change_new_status(status) {
   // console.log('current_status');
   // console.log(status);
   $("#save_current_status").val(status);
}
//for set status code
function set_status_values(feedback_val) {
   if (feedback_val == '0') {
      $("select#current_status_type_").val('5');
      $("#feed_current_status_type_").val('5');
      $("#save_current_status").val(5);
   }
   if (feedback_val == '2') {
      $("select#current_status_type_").val('8');
      $("#feed_current_status_type_").val('8');
      $("#save_current_status").val(8);
   }
   if (feedback_val == '1') {
      $("select#current_status_type_").val('3');
      $("#feed_current_status_type_").val('3');
      $("#save_current_status").val(3);
   }

   // console.log('feedback_val '+ feedback_val);
}

$(document).ready(function(){
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
   $('#service').keypress(function (e) {    
      var charCode = (e.which) ? e.which : event.keyCode    
      if (String.fromCharCode(charCode).match(/[^0-9]/g))    
      return false;                        
   }); 
   $('#dfs').keypress(function (e) {    
      var charCode = (e.which) ? e.which : event.keyCode    
      if (String.fromCharCode(charCode).match(/[^0-9]/g))    
      return false;                        
   }); 
   // $("#lang").prop('disabled', true);
   $("#source").prop('disabled', true);

   //calling function to get Category 
   $('#customerrr input').on('change', function() {
      var selvalue = $("[name='type']:checked").val();
      if (selvalue != undefined) {
      }
      if (selvalue == 'complaint') {
         $("#subcategory_div").show();
      }
      if (selvalue == 'request') {
         $("#subcategory_div").hide();
      }
   });
});
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
   //   updated the code for the alert on the call back time [vastvikta][24-03-2025]
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
        console.log(data);
        $("#remark_button").prop('disabled', false);
        $("#remark_button").val('Save New Remark');
        var last_docket = $('#docket_no').val();
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
           }
           $("html, body").animate({
              scrollTop: 0
           }, "slow");
            $("#show_ticket").empty();
            $("#show_ticket").text(data.message);
            $("#show_ticket").show();
            // change value of status 
            var c_st = $("#save_current_status").val();
            $('select#current_status_type_').val(c_st);
            $('select#status_type_').val(c_st);
            if (c_st == '8') {
               $("#feedback_form").show();
               var rowid = $("#rowid").val();
            }
            setTimeout(function() {
               $('#addInteraction_form').hide();
               var encodedToken = btoa('web_case_detail');
               var docket= btoa(last_docket);
               var redirectURL = "helpdesk_index.php?token=" + encodeURIComponent(encodedToken)+"&id=" + encodeURIComponent(docket);
               // window.location.href = redirectURL; 

               window.location.reload();
               console.log(redirectURL);  
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

$("#edit_button").click(function(event) {
   event.preventDefault();
   $('.inputDisabled').prop("disabled", false); // Element(s) are now enabled.
   $("#edit_button").hide();
   $("#save_button").show();

});

// take feed from customer if agent resolve the case 
// function getfeedback(complaint_status) {
//    if (complaint_status == '8' || complaint_status == '2') {
//       // $("#feedbackdiv").show();
//       $("#group_assign_div").hide();
//    } else {
//       $("#group_assign_div").show();
//       $('select#group_assign').val('0')
//       // $("#feedbackdiv").hide();
//    }
// }
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
function RemoveCaseIssues(issue_id) {
   //alert(issue_id);
   $("#Remark" + issue_id).css('display', 'none');
   $("#issueslist_" + issue_id).val('');
}


function web_assign(val, sel) {
   $.post("helpdesk/web_helpdesk_function.php", {
         cat: val,
         sel: sel,
         'action':'web_assign',
      },
      function(data, status) {
         $("#subcategory_div").html(data);
         if (val == 7 || val == 9 || val == 21) {
            $(".class_subcategory").css("display", "none");
         } else {
            $(".class_subcategory").css("display", "block");
         }
      });
   // }

}
function checkme(val, dbval) {
   if (val == 'Partner') {
      $("#dir_in_email").html(
         '<input name="opticianemail" id="opticianemail" type="hidden" value="" class="input-style1">');
   } else {
      $('#dir_in_email').html(
         '<label style="padding:0px 25px 0 0;""></label><input type="hidden" name="opticianemail" id="opticianemail" class="input-style1" value="' +
         dbval + '">');
   }
}
function validate_existing(groupid) {
      var status = $('#inte_status_type_').val();
      if (status == "0") {
         alert("Please Select Status!");
         $('#inte_status_type_').focus();
         return false;
      }
      var text = '';
      //   Back Office first Level View (BSL) 
      if (groupid == '060000'){
         var remark = $('#backoffice_remark').val();
         text = 'backoffice_remark';
      }
      //  <!-- Back Office LAST CALL (BLL)-->
      if (groupid == '090000'){
         var remark = $('#backoffice_last_remark').val();
         text = 'backoffice_last_remark';
       }
      //   Supervisour remark  
      if (groupid == '080000'){
         var remark = $('#supervisor_remark').val();
         text = 'supervisor_remark';
       }
      //   Admnstrator remark
       if (groupid == '0000'){
         var remark = $('#v_OverAllRemark').val();
         text = 'v_OverAllRemark';
       }
      if (remark == "" && text != '') {
         alert("Please Enter Remarks!");
         $('#' + text).focus();
         return false;
      }
      return true;
}



function validate_form() {
   try {
      var login_user = '<?php echo ($groupid == "060000") ? true : false  ?>';
      var email = $("#email").val();
      if (email != '') {
         if (ValidateEmail(email) == false) {
            return false;
         }
      }
      // type of Project Status selectted
      if (login_user) {
         var case_status = $("input[name='cstype']:checked").val(); // Check Validate Or Rject Case 
         if (case_status == 1) {
            return true;
         }
         var project_type = $("input[name='type']:checked").val();
         console.log('project_type ' + project_type);
         if (project_type == 1) {
            var status1 = $("#status1").val();
            if (status1 == 0) {
               alert("Please Select Status");
               $("#status1").focus();
               return false;
            }
         } else if (project_type == 2) {
            console.log('backoffice_vEnquiry ' + backoffice_vEnquiry);
            console.log('backoffice_vActionStatus ' + backoffice_vActionStatus);
         } else if (project_type == 3) {
         }
         var backoffice_vEnquiry = $("#backoffice_vEnquiry").val();
         var backoffice_vActionStatus = $("#backoffice_vActionStatus").val();
         var overall_remark = $("#overall_remark").val();
         if (backoffice_vEnquiry == 0) {
            alert("Please Select Back Office Complaint/Enquiry!");
            $("#backoffice_vEnquiry").focus();
            return false;
         }
         if (backoffice_vActionStatus == 0) {
            alert("Please Select Back Office Status !");
            $("#backoffice_vActionStatus").focus();
            return false;
         }
         if (overall_remark == 0) {
            alert("Please Enter Remark !");
            $("#overall_remark").focus();
            return false;
         }
      } else {
         var typeVal = $("[name='type']:checked").val();
         console.log('typeVal ' + typeVal);
         // Public Relation 
         if (typeVal == 1) {
            var v_typeofcaller = $('#vTypeOfcaller').val();
            if (v_typeofcaller.trim() == "0") {
               alert("Please Select Caller Type!");
               $('#vTypeOfcaller').focus();
               return false;
            }
            var v_subcategory_type1 = $('#vSubCategory1').val();
            if (v_subcategory_type1.trim() == "0") {
               alert("Please Select Enquiry/Complaint !");
               $('#vSubCategory1').focus();
               return false;
            }
            var v_remark_type1 = $('#vRemarks1').val();
            if (v_remark_type1.trim() == "") {
               alert("Please Enter Remarks!");
               $('#vRemarks2').focus();
               return false;
            }
         }
         // Project Affected Person
         if (typeVal == 2) {
            //alert(typeVal);return false;
            var product_type2 = $('#vProjectpap').val();
            if (product_type2.trim() == "") {
               alert("Please Enter Project!");
               $('#vProjectpap').focus();
               return false;
            }
            var v_subcategory_type2 = $('#vSubCategory2').val();
            if (v_subcategory_type2.trim() == "0") {
               alert("Please Enter Enqiry/Compliant!");
               $('#vSubCategory2').focus();
               return false;
            }
            var v_remark_type2 = $('#vRemarks2').val();
            if (v_remark_type2.trim() == "") {
               alert("Please Enter Remarks!");
               $('#vRemarks2').focus();
               return false;
            }
         }

         // Project Stack Hoslder
         try {
            if (typeVal == 3) {

               var product_type3 = $('#vProjectstake').val();
               if (product_type3.trim() == "0") {
                  alert("Please Enter Project!");
                  $('#vProjectstake').focus();
                  return false;
               }
               var v_stakeholder = $('#vStakeholder').val();
               if (v_stakeholder.trim() == "0") {
                  alert("Please Enter Stakeholder!");
                  $('#vStakeholder').focus();
                  return false;
               }

               var v_subcategory_type3 = $('#vSubCategory3').val();
               if (v_subcategory_type3.trim() == "0") {
                  alert("Please Select Enquiry/Complaint !");
                  $('#vSubCategory3').focus();
                  return false;
               }

               var v_remark_type3 = $('#vRemarks3').val();
               if (v_remark_type3.trim() == "") {
                  alert("Please Enter Remarks!");
                  $('#vRemarks3').focus();
                  return false;
               }
            }

         } catch (err) {
            console.log("Error Exception : " + err);
            return false;
         }
      }
      var msg = confirm("Do you really want to update the case?");
      if (msg) return true;
      else return false;
      //return false;   
   } catch (err) {
      console.log("Error Exception : " + err);
   }
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
/* Add new Interaction Form */
$("#viewproblemfrm").on('submit', (function(e) {
   e.preventDefault();
   $("#update_btn").prop('disabled', true);
   $("#update_btn").val('Please Wait...');
   $.ajax({
      url: 'helpdesk/web_helpdesk_function.php',
      type: "POST",
      data: new FormData(this),
      dataType: 'json',
      contentType: false,
      cache: false,
      processData: false,
      success: function(data) {
         console.log('data response');
         console.log(data);
            $("#update_btn").prop('disabled', false);
            $("#update_btn").val('Update');
            $("#show_ticket").text(data);
            $("#show_ticket").show();
            $("html, body").animate({
               scrollTop: 0
            }, "slow");
            setTimeout(function() {
               window.location.reload();
            }, 2000);
      },
      error: function(error) {
         $("#update_btn").prop('disabled', false);
         $("#update_btn").val('Save New Remark');
         $("#show_ticket").text('Error');
         console.log('Error');
         console.log(error);
      }
   });   
}));


// For same category ticket merge code
function addMergeTicket(ticket_id,customer,i_source,status_type){
   $('#myModal').modal('show');
   $.ajax({
      url: 'helpdesk/merge_ticket.php',
      type: 'post',
      data: {
         'ticket_id': ticket_id,'customer':customer,'action':'fetch_ticket'
      },
      dataType: 'json',
      success: function(data) {
         console.log(data);
         $("#marge_select").html(data);
         $("#marge_source").val(i_source);
         $("#marge_status_type").val(status_type);
         $("#marge_customerr").val(customer);
         $("#marge_ticket").val(ticket_id);
      }
   });
}
// For same category ticket merge code
function handleMergeSubmit(){
   var ticket_id = $('#marge_select').val();
   var remarks = $('#merge_remarks').val();
   var source_id= $("#marge_source").val();
   var status_type = $("#marge_status_type").val();
   var customerr = $("#marge_customerr").val();
   var marge_ticket = $('#marge_ticket').val();
   $.ajax({
      url: 'helpdesk/merge_ticket.php',
      type: 'post',
      data: {
         'ticket_id': ticket_id,'customer':customerr,'remarks':remarks,'status_type':status_type,'source_id':source_id,'action':'submit_merge','marge_ticket':marge_ticket},
      dataType: 'json',
      success: function(data) {
         console.log(data);
         if(data){
            $('#myModal').modal('hide');
            window.location.reload(true);
         }else{
         }
      }
   });
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
function validate_dispose() {
    var now_date_time = Date.now();
    console.log('now_date_time ' + now_date_time);
    var typeVal = $("input[name='callbk']:checked").val();
    console.log('typeVal ' + typeVal);
    if (typeVal != undefined) {
        var cb_date = $("#cb_date").val();
        console.log('cb_date ' + cb_date);
        /*if(cb_date =='' || cb_date ==undefined){
            alert("Please Select Disposition Date");$("#cb_date").focus();return false;
        }*/
        /*if(new Date(cb_date) <= new Date(now_date_time)){
            alert("Please Select Correct Disposition Date");$("#cb_date").focus();return false;
         //compare end <=, not >=
          //your code here
         }*/
    }
    var disposition = $("#disposition").val();
    if (disposition == '0') {
        alert("Please Select Disposition backoffice");
        $("#disposition").focus();
        return false;
    }
    var remark = $("#remark").val();
    if (remark == '') {
        alert("Please Enter Remark");
        $("#remark").focus();
        return false;
    }
    return true;
}
// added code for compose message on case detail backoffice [vastvikta][03-04-2025]
// Email button click event
$('.emailConMess').click(function() {
   let email = $("#email").val().trim();
   let customer_id = $("#customer_id").val();
   let caseid = $("#docket_no").val();
   if(email == ""){
      alert('Please Enter Email ID');
      // Dismiss the modal with ID 'staticBackdrop'
      $('#staticBackdrop').modal('hide');
   }else{
      
   // If the email is not empty, open the new window
   window.open('omnichannel_config/web_send_email_reply.php?action="content-messaging"&reply_to='+email+'&customerid='+customer_id+'&caseid='+caseid,
      '_blank', 
      'height=550, width=900,scrollbars=0');
   }

});
// instagram reply  code [vastvikta][15-04-2025]
$('.instagramConMess').click(function() {
   // Get the trimmed values of first and last names
   let instagramhandle = $("#instagramhandle").val().trim();

   let customer_id = $("#customer_id").val();
   let caseid = $("#docket_no").val();

   if(instagramhandle == ""){
      alert('Please Enter Instagram Handle');
      // Dismiss the modal with ID 'staticBackdrop'
      $('#staticBackdrop').modal('hide');
   }else{
      // If the email is not empty, open the new window
      window.open('omnichannel_config/contentMess_instagram.php?action="content-messaging"&instagramhandle='+instagramhandle+'&customerid='+customer_id+'&caseid='+caseid,
         '_blank', 
         'height=500, width=800,scrollbars=0');
   }

});
// SMS button click event
$('.smsConMess').click(function() {
   // Get the trimmed values of first and last names
   let firstName = $("#first_name").val().trim();
   let lastName = $("#last_name").val().trim();
   let customer_id = $("#customer_id").val();
   let caseid = $("#docket_no").val();
  
   // Combine first and last name with a space in between
   let name = firstName + " " + lastName;
   let phone = $("#phone").val().trim();

   if(phone == ""){
      alert('Please Enter Registrer Noss');
      // Dismiss the modal with ID 'staticBackdrop'
      $('#staticBackdrop').modal('hide');
   }else{
      // If the email is not empty, open the new window
      window.open('omnichannel_config/contentMess_sms.php?action="content-messaging"&name='+name+'&phone='+phone+'&customerid='+customer_id+'&caseid='+caseid,
         '_blank', 
         'height=500, width=800,scrollbars=0');
   }

});


// Whatsapp button click event
$('.whatsappConMess').click(function() {
   // Logic for WhatsApp interaction
   let phone = $("#whatsapphandle").val();
   let customer_id = $("#customer_id").val();
   let caseid = $("#docket_no").val();
   if(phone == ""){
      alert('Please Enter Whatsapp No');
      // Dismiss the modal with ID 'staticBackdrop'
      $('#staticBackdrop').modal('hide');
   }else{
      // If the email is not empty, open the new window
      window.open('omnichannel_config/contentMess_whatsapp.php?action="content-messaging"&phone='+phone+'&customerid='+customer_id+'&caseid='+caseid,
         '_blank', 
         'height=500, width=800,scrollbars=0');
   }
});

// Messenger button click event
$('.fbmessConMess').click(function() {
   // Logic for Facebook Messenger interaction
    let facebookid = $("#messengerhandle").val();
    let customer_id = $("#customer_id").val();
    let caseid = $("#docket_no").val();
   
    if(facebookid == ""){
       alert('Please Enter Messenger Handle');
       // Dismiss the modal with ID 'staticBackdrop'
       $('#staticBackdrop').modal('hide');
    }else{
       // If the email is not empty, open the new window
       window.open('omnichannel_config/contentMess_messenger.php?action="content-messaging"&facebookid='+facebookid+'&customerid='+customer_id+'&caseid='+caseid,
          '_blank', 
          'height=500, width=800,scrollbars=0');
    }
});
// code end
$("#disposeAction").on("submit",function (event) {
   event.preventDefault()
   let validate = validate_dispose()
   if(validate == true){
     $("#processModel").show()
     $("#dispose").val('Please Wait...')
     //AJAX request
     $.ajax({
       url : 'helpdesk/web_ticket_function.php',
       type: "POST",
       data: new FormData(this),
       dataType: 'json',
       contentType: false,
       cache: false,
       processData: false,
      //  updated the success part so that alert can be displayed[vastvikta][27-03-2025]
       success: function (data) {
    
         setTimeout(() => {
            $("#processModel").hide();
            $("html, body").animate({scrollTop: 0}, "slow");
         }, 1000);

         if (data.status === "success") {
           
            $(".alert").css({"background": "#00CC66", "display": "block"}).text(data.message);
            $("#dispose").hide();
            setTimeout(() => {
                  window.location.href = data.url;
            }, 4000);
         } else {
           
            $(".alert").css({"background": "crimson", "display": "block"}).text(data.message);
            alert(data.message);
            setTimeout(() => {
                  $(".alert").css("display", "none");
            }, 2000);
            $("#dispose").val("DISPOSE").show();
         }
      },
      error: function (error) {
         console.log("AJAX error:", error);
         $("#processModel").hide();
         $("#dispose").val("DISPOSE").show();
      }

     });
   }else{
     return false
   }
});