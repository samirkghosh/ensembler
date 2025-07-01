    function check_working_status(docket_no, case_id, user_id) {
   console.log('DC ' + docket_no);
   console.log('cas ' + case_id);
   console.log('user ' + user_id);
   if (docket_no != '') {
      $.ajax({
         url: 'helpdesk/process_ajax_request.php',
         type: 'post',
         data: {
            'work_status': docket_no,
            'case_id': case_id,
            'user_id': user_id,
         },
         success: function(data, status) {
            // Redirect to the appropriate page after success
            var encodedToken = btoa('web_case_detail');
            var docket= btoa(docket_no);
            var redirectURL = "helpdesk_index.php?token=" + encodeURIComponent(encodedToken)+"&id=" + encodeURIComponent(docket);
            window.location.href = redirectURL; 
            console.log(redirectURL);                    
         },
         error: function(xhr, desc, err) {
            console.log(xhr);
            console.log("Details: " + desc + "\nError:" + err);
         }
      });
   }
}


$(document).ready(function(){
    // update code for adding new customer [vastvikta][03-04-2025]
    // make the form editable if customer id is not present 
    var customerid = $('#customerid').val(); // Get customer ID
    if (typeof customerid === 'undefined') { // Enable fields only when customerid is undefined or empty
        $('#formContainer').find('input')//make inpur field editable 
            .prop('readonly', false)
            .prop('disabled', false)
            .css('pointer-events', 'auto');//for registered number 
        $('#formContainer').find('select').prop('disabled', false);//make dropdown selectable 
    } 
    // code update end
    $(document).on('click', '#editBtn', function(){
    $('#formContainer').find('input').prop('readonly', false).prop('disabled', false); // Disable readonly and enable disabled
    $('#formContainer').find('select').prop('disabled', false);
        $(this).hide();
        $('#updateBtn').show();
        $('#cancelBtn').show();
    });

      // Update button click event
    $(document).on('click', '#updateBtn', function(){
      var formData = $('#frmviewcustomer').serialize();
      $.ajax({
          url: "Customer/web_consumer_function.php?action=update_data",
          type: "post",
          data: $("#frmviewcustomer").serialize(),
          success: function(response) {
              // Show alert box with success message
          alert("Data updated successfully.");

          // Reload the page after 1 sec
          setTimeout(function() {
              location.reload();
          },  1000); // 1 second = 1000 milliseconds
        },
      });
    });
    // Insert button click event[vastvikta][03-04-2025]
    $(document).on('click', '#insertBtn', function(){
      var firstName = document.getElementById('first_name').value.trim();
      var lastName = document.getElementById('last_name').value.trim();
      var phone = document.getElementById('phone').value.trim();
      var mobile = document.getElementById('mobile').value.trim();
      
      var email = document.getElementById('email').value.trim();
      var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

      // var companyName = document.getElementById('companyname').value.trim();
      // var companyReg = document.getElementById('company_registration').value.trim();

      var namePattern = /^[A-Za-z\s]+$/;
      var phonePattern = /^[0-9]{10,12}$/;

      if (phone === "") {
        alert("Registered Mobile Number is required.");
        return;
      }
      if (!phonePattern.test(phone)) {
        alert("Registered Mobile Number invalid!");
        return;
      }
      // Alternate Mobile Number Validation (if entered)
      if (mobile !== "" && !phonePattern.test(mobile)) {
          alert("Alternate Mobile Number invalid!");
          return;
      }
      if (firstName === "") {
          alert("First Name is required.");
          return;
      }
      if (!namePattern.test(firstName)) {
          alert("First Name must contain only letters.");
          return;
      }
      if (lastName !== "" && !namePattern.test(lastName)) {
          alert("Last Name must contain only letters.");
          return;
      }
      if (email === "") {
          alert("Email is required.");
          return;
      }
      if (!emailPattern.test(email)) {
          alert("Please enter a valid email address.");
          return;
      }

      var formData = $('#frmviewcustomer').serialize();//serialize the form data
      $.ajax({
          url: "Customer/web_consumer_function.php?action=update_data",
          type: "post",
          data: $("#frmviewcustomer").serialize(),
          success: function(response) {
          // Show alert box with success message of user added successfully
          alert("User Added  successfully.");

          // Redirect to the encoded link after 1 second
          setTimeout(function() {
            window.location.href = $("#backLink").attr("href");
          }, 1000);
          // 1 second = 1000 milliseconds
        },
      });
    });
    // insert code end 
    // Cancel button click event
    $(document).on('click', '#cancelBtn', function(){
        $('#editBtn').show();
        $('#updateBtn').hide();
    $('#cancelBtn').hide();
        location.reload();// Reload form when cancel button clicked
    });
    // Email button click event
      $('.emailConMess').click(function() {
         let email = $("#email").val().trim();
         let customer_id = $("#customerid").val();
         if(email == ""){
            alert('Please Enter Email ID');
            // Dismiss the modal with ID 'staticBackdrop'
            $('#staticBackdrop').modal('hide');
         }else{
            
         // If the email is not empty, open the new window
         window.open('omnichannel_config/web_send_email_reply.php?action="content-messaging"&reply_to='+email+'&customerid='+customer_id,
            '_blank', 
            'height=550, width=900,scrollbars=0');
         }

      });

      // SMS button click event
      $('.smsConMess').click(function() {
         // Get the trimmed values of first and last names
         let firstName = $("#first_name").val().trim();
         let lastName = $("#last_name").val().trim();
         let customer_id = $("#customerid").val();

         // Combine first and last name with a space in between
         let name = firstName + " " + lastName;
         let phone = $("#phone").val().trim();

         if(phone == ""){
            alert('Please Enter Registrer No');
            // Dismiss the modal with ID 'staticBackdrop'
            $('#staticBackdrop').modal('hide');
         }else{
            // If the email is not empty, open the new window
            window.open('omnichannel_config/contentMess_sms.php?action="content-messaging"&name='+name+'&phone='+phone+'&customerid='+customer_id,
               '_blank', 
               'height=500, width=800,scrollbars=0');
         }

      });

      // instagram reply  code [vastvikta][15-04-2025]
      // Instagram  button click event
      $('.instagramConMess').click(function() {
        // Get the trimmed values of first and last names
        let instagramhandle = $("#instagramhandle").val().trim();

        let customer_id = $("#customerid").val();


        if(instagramhandle == ""){
           alert('Please Enter Instagram Handle');
           // Dismiss the modal with ID 'staticBackdrop'
           $('#staticBackdrop').modal('hide');
        }else{
           // If the email is not empty, open the new window
           window.open('omnichannel_config/contentMess_instagram.php?action="content-messaging"&instagramhandle='+instagramhandle+'&customerid='+customer_id,
              '_blank', 
              'height=500, width=800,scrollbars=0');
        }

     });
     


      // Whatsapp button click event
      $('.whatsappConMess').click(function() {
         // Logic for WhatsApp interaction
         let phone = $("#whatsapphandle").val().trim();
         let customer_id = $("#customerid").val();

         if(phone == ""){
            alert('Please Enter Whatsapp No');
            // Dismiss the modal with ID 'staticBackdrop'
            $('#staticBackdrop').modal('hide');
         }else{
            // If the email is not empty, open the new window
            window.open('omnichannel_config/contentMess_whatsapp.php?action="content-messaging"&phone='+phone+'&customerid='+customer_id,
               '_blank', 
               'height=500, width=800,scrollbars=0');
         }
      });

      // Messenger button click event
      $('.fbmessConMess').click(function() {
         // Logic for Facebook Messenger interaction
          let facebookid = $("#messengerhandle").val();
          let customer_id = $("#customerid").val();

        
          if(facebookid == ""){
             alert('Please Enter Messenger Handle');
             // Dismiss the modal with ID 'staticBackdrop'
             $('#staticBackdrop').modal('hide');
          }else{
             // If the email is not empty, open the new window
             window.open('omnichannel_config/contentMess_messenger.php?action="content-messaging"&facebookid='+facebookid+'&customerid='+customer_id,
                '_blank', 
                'height=500, width=800,scrollbars=0');
          }
      });
});
jQuery(function ($){
    var Report = {
        init: function (){
            jQuery("body").on('change', '#district', this.HandleSubCounty);//function to handle break delete request
            jQuery("body").on('change', '#category', this.HandleCategory);//function to handle break delete request

            // Initialize date time picker
             // $('.date_class').datetimepicker();
             // Function to clear default text in input field on focus
             function clearText(field){
                 if (field.defaultValue == field.value) field.value = '';
                 else if (field.value == '') field.value = field.defaultValue;
             }
        },
        HandleSubCounty: function (e) {
          console.log('rituuuuuu');
          e.preventDefault();
        // ajax script for getting  subcategory data
            var disID = $(this).val();
            var vilID = $('#village').val();
            // alert(catID);
              $.ajax({
                type:'POST',
                url:'Customer/web_consumer_function.php',
                data:{'dis_id':disID,'vill_id':vilID,'action':'display_subcounty'},
                success:function(result){
                  $('#village').html(result);
                  
                }
            });
      },
      HandleCategory: function(e){
        e.preventDefault();
        var catID = $(this).val();
        var subID = $('#subcategory').val();
        // alert(catID);
          $.ajax({
            type:'POST',
            url:'web_subcat.php',
            data:{'cat_id':catID,'subcat_id':subID},
            success:function(result){
              $('#subcategory').html(result);
              
            }
          });
      }
    }
    Report.init();
});
// close
