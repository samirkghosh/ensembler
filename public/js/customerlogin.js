/**
 * Author: Ritu Modi
 * Date: 04-04-2024
 The file contains JS of customer_login page and complain_form page
 **/
// Function to handle form submission for login
$("#formlogin").submit(function (e) {
      console.log('i am here');
       // Clear any existing error messages        
      $(".errormsglogin").text('');
      var emp_mobile = $("#loginID").val();
      var num = getValidNumber(emp_mobile);
       console.log(num);
        if(num == false){          
          $(".errormsglogin").text('Please specify a valid phone number.');
          return false;
        }else{  
          $(".errormsglogin").text('');
        }
        // Perform AJAX request to submit login form data
      $.ajax({
        type: "POST",
        dataType: 'JSON',
        url: 'function/customer_function.php',
        data: $("#formlogin").serialize(),
        success: function (data, textStatus, jqXHR){
            if(data.status == true){
              // $("#formlogin").hide();
              // $(".otp_form").show();
              $('#vh_mobile').val($("#loginID").val());
              $('.errormsglogin').text('');
              setTimeout(function(){
                   window.location.href='CRM/home_customer.php';
              },2000);
            }else{
              $('.errormsglogin').text(data.message);
            }        
        },
        error: function (jqXHR, textStatus, errorThrown){
            console.log(jqXHR); 
        }
      });
      e.preventDefault(); // avoid to execute the actual submit of the form.
  });
// Function to handle form submission for OTP verification
   $("#formlogin_otp").submit(function (e) {
    console.log('please check otp');
    // Get the value of the OTP input field
        var emp_otp = $('#emp_otp').val();
        // Validate OTP
        if(emp_otp){}else{
            $(".error_label").text('Please enter opt.');
            return false;
        }
        if(emp_otp.length != '6'){
            $(".error_label").text('Please enter 6 digit OTP number.');
            return false;
        }
        if (!$.isNumeric(emp_otp)) {
            $(".error_label").text('OTP must be numeric.');
            return false;
        }
        // Perform AJAX request to verify OTP
        $.ajax({
          type: "POST",
          dataType: 'JSON',
        url: 'function/customer_function.php',
          data: $("#formlogin_otp").serialize(), // serializes the form's elements.
          success: function (data, textStatus, jqXHR){
            if(data.status == 'false'){
                $(".error_label").text(data.msg);
                return false;
            }else{
                $(".error_label").text('');
                setTimeout(function(){
                   window.location.href='CRM/home_customer.php';
              },2000);  
            }
          },
          error: function (jqXHR, textStatus, errorThrown){     
          }
        });
        e.preventDefault(); // avoid to execute the actual submit of the form.
    });
   // Function to handle click event for creating OTP
    $('#create_otp').click(function(){
      // Perform AJAX request to create OTP
          $.ajax({
            type: "POST",
            dataType: 'JSON',
            url: 'function/customer_function.php',
            data: $("#formlogin").serialize(), // serializes the form's elements.
            success: function (data, textStatus, jqXHR){  
              alert('new otp create successfully.');
              // Reload the page after 2 seconds
              setTimeout(function(){
                   location.reload(true);
              },2000);
            },
            error: function (jqXHR, textStatus, errorThrown){   
            }
          });
      });

// Function to validate full name
    $("#formsubmit").submit(function (e) {
        $(".error_label").text('');
        // Clear any existing error messages
            $(".error_label_email").text('');
            $(".error_label_name").text('');
            var regName = /^[a-zA-Z]+ [a-zA-Z]+$/;
            var name = $('.full_name').val();
            // if(!regName.test(name)){
            //     $(".error_label_name").text('Please enter on alphabets only.');
            //     console.log('Invalid');
            //     return false;
            // }else{
            //     alert('Valid name given.');
            // }
            // if (name.match('^[a-zA-Z]{3,16}$') ) {
            //     alert( "Valid name" );
            // } else {
            //    $(".error_label_name").text('Please enter on alphabets only.');
            //    return false;
            // }
            // Validate mobile number
            var emp_mobile =  $("#mobile").val(); 
            var num = getValidNumber(emp_mobile);
            if(num == false){
              $(".error_label").text('Please specify a valid phone number.');
              return false;
            }else{
              $(".error_label").text('');
            }
            var email = $("#email").val();
            if(email){
              var email_status = isEmail(email);
              if(email_status == false){
                $(".error_label_email").text('Please specify a valid email.');
                return false;
              }else{
                $(".error_label_email").text('');
              }
            }
            // Perform AJAX request to submit form data
        $.ajax({
          type: "POST",
          dataType: 'JSON',
          url: 'function/customer_function.php',
          data: $("#formsubmit").serialize(),
          success: function (data, textStatus, jqXHR){
//             if(confirm('information successfully send in mail.')){
//     window.location.reload();  
// }        // Display success message and reload page after 2 seconds
            alert('Information successfully sent to Customer Service support email.');
            // window.location.reload(); 
            setTimeout(function(){
                   window.location.reload(); 
              },2000);
          },
          error: function (jqXHR, textStatus, errorThrown){     
          }
        });
        e.preventDefault(); // avoid to execute the actual submit of the form.
    }); 
   // Function to check if email is valid
    function isEmail(email) {
      var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      return regex.test(email);
    }
        // Function to validate mobile number
    function getValidNumber(value){
        value = $.trim(value).replace(/\D/g, '');
        if (value.substring(0, 1) == '1') {
            value = value.substring(1);
        }
        if (value.length == 12 || value.length == 10 || value.length == 9) {

            return value;
        }
        return false;
    }
    // Click event handler for hiding elements and showing the login form
    $('#report_page_ticket').click(function(){
      $('#report_page_ticket').hide();
      $('#report_page').hide();
      $('#formlogin').show();
    });
