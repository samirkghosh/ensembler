jQuery(function ($){
    var User = {
        init: function (){
            User.onreloadfunction();
        	jQuery("body").on('click','.user_delete',this.HandleUserDelete); // this function for delete category
            jQuery("body").on('click', '.reset', this.HandleResetPassword);//reset password
            jQuery("body").on('keypress', '#lastname', this.HandlelastnameKeypress);// only text input allowed
            jQuery("body").on('keypress', '#fname', this.HandleFnameKeypress);// only text input allowed
            jQuery("body").on('keypress', '#phone', this.HandlephoneFnameKeypress);// only text input allowed
            jQuery('#adduserfrm input[name=fname]').on('keyup', this.createUsername);
            jQuery('#adduserfrm input[name=lastname]').on('keyup', this.createUsername);
            jQuery("body").on('change', '#jobtitle', this.Handlephonejobtitle);
            jQuery("body").on('click', '.button_test_serach', this.button_test_serach);
   			$('#userTable').DataTable({
   			    "ordering": true,
   			    "pageLength": 25
   			});
             String.prototype.toTitleCase = function() {
              return this.split(' ').map(i => i[0].toUpperCase() + i.substring(1).toLowerCase()).join(' ');
            }
   			String.prototype.toPropperCase = function() {
   			  return this.toTitleCase();
   			}
            $(document).ready(function() {
                $('input[type=radio][name="classify"]').change(function() {
                    if($(this).val() == 0){
                     $('#channel_div').hide();
                    }else{
                     $('#channel_div').show();
                    }
                });
            });
       	},
      button_test_serach:function(){
         var encodedToken = btoa('web_userhome');
         // window.location.href = "user_index.php?token=" + encodeURIComponent(encodedToken);
        document.searchfrm.action = "user_index.php?token=" + encodeURIComponent(encodedToken)+"&go=1";
        document.searchfrm.submit();
      },
      onreloadfunction:function(){
         var currentdate = new Date();
         var cuyear = currentdate.getFullYear();
         var cumonth = currentdate.getMonth();
         var cudate = currentdate.getDay();
         $('#dob').datepicker({
            // minDate:new Date(1920, 01, 01, 1, 30),
            maxDate:new Date(cuyear, cumonth, cudate, currentdate.getHours(), currentdate.getMinutes()),
            changeYear: true,
            changeMonth: true,
            yearRange:'1920:-0'
         });
         $('#dateto').datepicker();
         $('#datefrom').datepicker();
         $('#agentdateto').datepicker();
         $('#agentdatefrom').datepicker();
      
         $('#startdate1').datepicker();
         $('#enddate1').datepicker();
         $('#complainhandleddate').datepicker();
         $('#book-app').datepicker({
            minDate:0
         });
         // Start and End Date validation
         var startDateTextBox = $('#startdatetime');
         var endDateTextBox = $('#enddatetime');
         startDateTextBox.datetimepicker({ 
              timeFormat: 'HH:mm:ss',
              onClose: function(dateText, inst) {
                  if (endDateTextBox.val() != '') {
                      var testStartDate = startDateTextBox.datetimepicker('getDate');
                      var testEndDate = endDateTextBox.datetimepicker('getDate');
                      if (testStartDate > testEndDate)
                          endDateTextBox.datetimepicker('setDate', testStartDate);
                  }
                  else {
                      endDateTextBox.val(dateText);
                  }
              },
              onSelect: function (selectedDateTime){
                  endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
              }
         });
         endDateTextBox.datetimepicker({ 
              timeFormat: 'HH:mm:ss',
              onClose: function(dateText, inst) {
                  if (startDateTextBox.val() != '') {
                      var testStartDate = startDateTextBox.datetimepicker('getDate');
                      var testEndDate = endDateTextBox.datetimepicker('getDate');
                      if (testStartDate > testEndDate)
                          startDateTextBox.datetimepicker('setDate', testEndDate);
                  }
                  else {
                      startDateTextBox.val(dateText);
                  }
              },
              onSelect: function (selectedDateTime){
                  startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate') );
              }
         });
         var ex13 = $('#startdatetime');
          $('#startdatetime').click(function(){
          ex13.datetimepicker('setDate', (new Date()) );
         });
      },
		HandleUserDelete:function(){
			var flag = 0;
	        ctr = document.frmuser.count.value;
	        for (i = 1; i <= ctr; i++) {
	            t = "check" + i;
	            if (document.getElementById(t).checked) {
	                flag = 1;
	            }
	        }
	        if (flag == 0) {
	                alert("Select User to delete!");
	                return false;
	        }
			if (confirm("Are you sure to delete?")) {
				var id = $(this).data('id');
				console.log(id);
				var val = [];
		        $(':checkbox:checked').each(function(i){
		          val[i] = $(this).val();
		        });
				$.ajax({
				 method: 'POST',
				 url: 'User/web_user_function.php',
				 data: {'id':id,'action':'user_delete','checked_user':val},
				 success: function (response) {
				 	alert('User Delete Successfuly');
					// $('#success').html('<div class="alert alert-success alert-dismissible" role="alert">Category Delete Successfuly</div>');
						setTimeout(function(){ 
							location.reload();
						},2000); 
					}
				});
		    }
	 	},
	 	HandleResetPassword:function(e){
			e.preventDefault();
			// get value of id attribute on click
			var email = $(this).attr('id');
			// Display a confirmation dialog
			var result = confirm("Are you sure you want to reset the password of this user?");
			// If the user clicks 'OK', continue with form submission
			if (result) { 
				$.ajax({
					type: 'POST',
					url: 'User/reset_password.php',
					data: {'email':email},
					dataType: 'json',
					success: function(data) {
						// This function will be called when the request is successful
						if(data.status ==='success'){
							alert(data.msg);
							location.reload();
						}
						// The 'data' parameter contains the JSON response from the server
						console.log(data);
					},error: function(jqXHR, textStatus, errorThrown) {
						// This function will be called if there's an error with the request
						console.log('Error: ' + errorThrown);
					}
				});
				return true;
			}
			// If the user clicks 'Cancel', prevent form submission
			return false;
	 	},
      HandlelastnameKeypress:function(e){
         var regex = new RegExp("^[a-zA-Z]+$");
         var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
         if (regex.test(str)) {
            return true;
         }else{
            e.preventDefault();
            return false;
         }
      },
      HandleFnameKeypress:function(e){
         var regex = new RegExp("^[a-zA-Z]+$");
         var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
         if (regex.test(str)) {
            return true;
         }else{
            e.preventDefault();
            return false;
         }
      },
      HandlephoneFnameKeypress:function(e){
         var charCode = (e.which) ? e.which : event.keyCode    
         if (String.fromCharCode(charCode).match(/[^0-9]/g))    
         return false; 
      },
	 	ValidateOnKeyPress:function(e){
	 		var code;         
         	if (e)
         	code = e.keyCode || e.which;
         	else
         	code = window.event.keyCode || window.event.which;
         	var str = new String();
         	str = pointer.value;
         	if (code == 32 && pointer.value.length == 0){
         	    alert(msg);
         	    window.event.keyCode = 0;
         	}
      },
      createUsername:function(){
         var fname = $('#adduserfrm input[name=fname]').val().toLowerCase();
         var lname = $('#adduserfrm input[name=lastname]').val().toLowerCase();
         var username = fname + lname;
         $('#adduserfrm input[name=username_full]').val(username);
      },
      Handlephonejobtitle:function(){
         var groupid = $(this).val();
         if(groupid == '070000'){
            $(".classify").show();
         }else{
            $(".classify").hide();
         }
      },
      LTrim:function(str) {
      	for (var i=0; ((str.charAt(i)<=" ")&&(str.charAt(i)!="")); i++);
      	return str.substring(i,str.length);
      },
      RTrim:function(str) {
      	for (var i=str.length-1; ((str.charAt(i)<=" ")&&(str.charAt(i)!="")); i--);
      	return str.substring(0,i+1);
      },         
      Trim:function(str) {
      	return User.LTrim(User.RTrim(str));
      },
      textCounter:function(field,cntfield,maxlimit) {
         if (field.value.length > maxlimit) // if too long...trim it!
         field.value = field.value.substring(0, maxlimit);
         // otherwise, update 'characters left' counter
         else
         cntfield.value = maxlimit - field.value.length;
      },
      callchangelast:function(){
      	var newlval=User.Trim(document.adduserfrm.lastname.value);
      	document.adduserfrm.lastname.value=newlval;
      	document.adduserfrm.username.value=User.Trim(document.adduserfrm.fname.value)+" " +newlval;
      },
      callchange: function () {
      	var newval=User.Trim(document.adduserfrm.fname.value);
      	document.adduserfrm.fname.value=newval;
      	if(newval){
      		var myString1 = newval.substr(0, 4);
      		console.log(myString1);  
      		var lowercase = myString1.toLowerCase();
			var upperCase = lowercase.toUpperCase();
			var titleCase = upperCase.toTitleCase();    		
      		if(titleCase.length <= 3){
      			var myString2 = titleCase + '@1234';
      		}else{
      			var myString2 = titleCase + '@123';
      		}
      		console.log(myString2);
  			document.adduserfrm.password.value=myString2;
      	}
      	
      	document.adduserfrm.username.value=newval;
      	if(User.Trim(document.adduserfrm.lastname.value)!='')
      	{
      		document.adduserfrm.username.value=newval+' '+User.Trim(document.adduserfrm.lastname.value);
      	}
      },
      UpdateCheckAvailability:function(){
      	if(oRequest.readyState == 4){
      		if(oRequest.status == 200){
               document.getElementById("Available").innerHTML = oRequest.responseText;
            }else{
      		   document.getElementById("Available").innerHTML = "";
      		}
      	}
      },
      OnCheckAvailabilityUser:function(str){         
      	// if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
      	// 	xmlhttp=new XMLHttpRequest();
      	// }else{// code for IE6, IE5
      	// 	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      	// }
      	// xmlhttp.onreadystatechange=function(){
      	//   	if (xmlhttp.readyState==4 && xmlhttp.status==200){
      	// 		document.getElementById("divAvailableUser").innerHTML=xmlhttp.responseText;
      	//    }
      	// }
      	// xmlhttp.open("POST","User/ajax_username.php?q="+str,true);
      	// xmlhttp.send();
         $.ajax({
            method: 'POST',
            url: 'User/web_user_function.php',
            data: {'string':str,'action':'ajax_check_nameexits'},
            success: function (response) {
               document.getElementById("divAvailableUser").innerHTML=response;
            }
         });

      },
      // Author :: Farhan Akhtar 26-09-2024
	  OnCheckAvailabilityEmail:function(str){         
	   $.ajax({
		  method: 'POST',
		  url: 'User/web_user_function.php',
		  data: {'string':str,'action':'ajax_check_email_exits'},
		  success: function (response) {
			 document.getElementById("divAvailableEmail").innerHTML=response;
		  }
	   });

	},
      validateformfirst:function(val){
      	if(val==1){         
      		var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
      		var alphaExp = /^[a-z A-Z]+$/;
      		var alphaExpname = /^[a-zA-Z]+[a-zA-Z ]*$/;
      		var ipnoExpression = /^[0-9].+$/;
      		var V_FirstName = document.adduserfrm.fname.value.match(alphaExpname);
      		var V_LastName  = document.adduserfrm.lastname.value.match(alphaExp);
      		var mail        = document.adduserfrm.email.value.match(emailExp);
      		var T_ip = document.adduserfrm.ip_address.value.match(ipnoExpression);
      		var err=true;
      		var chks = document.getElementsByName('boss[]');
      		var hasSelected = false;
      		for (var i = 0; i < chks.length; i++)
      		{
      			if (chks[i].value!='')
      			{
      			hasSelected = true;
      			break;
      			}
      		}
      		if(!hasSelected){
         		alert("Please select at least one manager.");
         		chks[0].focus();
         		return false;
      		}
            if(!mail){
      			alert("Please enter your email(Like a@b.com)!");
      			document.adduserfrm.email.focus();
      			err=false;
      			return false;
      		}      
      		if(document.adduserfrm.ip_address.value!=''){
      			if(!T_ip){
      			alert("Please enter ip address for access!");
      			document.adduserfrm.ip_address.focus();
      			err=false;
      			}
      		}
      		return err;      
      	}
      },        
      validateform:function(adduserfrm,val){
         	if(val==1){
         		var size = 0;
         		var emailExp = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
         		var alphaExp = /^[a-z A-Z]+$/;
         		var alphaExpname = /^[a-zA-Z]+[a-zA-Z ]*$/;
         		var alphaExpusername = /^[a-zA-Z]+[a-zA-Z0-9\. ]*$/;
         		var ipnoExpression = /^[0-9].+$/;
         		var numexp = /^[0-9]+$/;
         		var V_FirstName = document.adduserfrm.fname.value.match(alphaExpname);
         		var V_LastName  = document.adduserfrm.lastname.value.match(alphaExp);
         		var V_username_full = document.adduserfrm.username_full.value.match(alphaExpusername);
         		var mail        = document.adduserfrm.email.value.match(emailExp);
         		var T_ip = document.adduserfrm.ip_address.value.match(ipnoExpression);
         		var phone =document.adduserfrm.phone.value.match(numexp);
				  var classify = document.adduserfrm.classify.value;
				  var jobtitle = document.adduserfrm.jobtitle.value;
				//   added code for updating  joining and leaving date on the basis of the status [vastvikta][07-1-2025]
				  var statusRadios = document.getElementsByName('status');
				  var selectedStatus = null;
				  for (var i = 0; i < statusRadios.length; i++) {
					  if (statusRadios[i].checked) {
						  selectedStatus = statusRadios[i].value;
						  break;
					  }
				  }
		  
				var dateOfLeaveInput = document.getElementById('startdate1');
				if (selectedStatus == '0' && !dateOfLeaveInput.value) {
					alert('Please select Date of Leave');

					let today = new Date();
					let formattedDate = 
						('0' + today.getDate()).slice(-2) + '-' + 
						('0' + (today.getMonth() + 1)).slice(-2) + '-' + 
						today.getFullYear();

					dateOfLeaveInput.value = formattedDate; 
					dateOfLeaveInput.focus();
					return false;
				}
				
         		if(document.getElementById('fileup').value!=''){
         			size = document.getElementById('fileup').files[0].size;
         		}         
         		if(!V_FirstName){
         			alert("Please enter your first name in characters!");
         			document.adduserfrm.fname.focus();
         			err=false;
         			return false;
         		}
         		if(User.Trim(document.adduserfrm.fname.value)==''){
         			alert("Please enter your first name in valid characters!");
         			document.adduserfrm.fname.focus();
         			err=false;
         			return false;
         		}         
         		if(!V_LastName){
         			alert("Please Enter Your Last Name In Characters !");
         			document.adduserfrm.lastname.focus();
         			err=false;
         			return false;
         		}         
         		if(User.Trim(document.adduserfrm.lastname.value)==''){
         			alert("Please enter your Last name in valid characters!");
         			document.adduserfrm.lastname.focus();
         			err=false;
         			return false;
         		}         
         		if(!V_username_full){
         			alert("Please Enter Your User Name in Alphanumeric characters!");
         			document.adduserfrm.username_full.focus();
         			err=false;
         			return false;
         		}
         		if(User.Trim(document.adduserfrm.username_full.value)==''){
         			alert("Please enter your User name in Alphanumeric characters!");
         			document.adduserfrm.username_full.focus();
         			err=false;
         			return false;
         		}         
         		if(document.adduserfrm.department.selectedIndex==''){
         		    alert( "Please enter the Department." );
         		   document.adduserfrm.department.focus();
         		 	err=false;
         		 	return false;
         		}
         		 if(document.adduserfrm.department.value!='0000' && document.adduserfrm.jobtitle.selectedIndex==''){
         		   alert( "Please select the Job Title.");
         		   document.adduserfrm.jobtitle.focus();
         			err=false;
         			return false;
         		}   
				  
         		var chks = document.getElementsByName('boss[]');
         		var hasSelected = false;
         		for (var i = 0; i < chks.length; i++){
         			if (chks[i].value!=''){
         			 hasSelected = true;
         			 break;
         			}
         		}
         		// if(document.adduserfrm.department.value!='0000' && !hasSelected){
         		//     alert("Please select at least one manager.");
         		//     chks[0].focus();
         		//     return false;
         		// }         
         		if(!mail){
         			alert("Please enter your email(Like a@b.com)!");
         			document.adduserfrm.email.focus();
         			err=false;
         			return false;
         		}        
         		if(User.Trim(document.adduserfrm.phone.value)==''){
         			alert("Please enter your phone number!");
         			document.adduserfrm.phone.focus();
         			err=false;
         			return false;
         		}
         		if(!phone){
         			alert("Please enter valid phone number!");
         			document.adduserfrm.phone.focus();
         			return false;
         		}
   				if(jobtitle=='070000'){
   					if(classify==''){
   						alert("Please select classify!");
            				document.adduserfrm.classify.focus();
            				return false;
   					}
   				}			
         		if(size!='0'){         
         			if(size>='1048576')	//1262921 // 1 MB
         			{
         				alert("Please upload file not more than 1 mb size!");
         				document.frmname.fileup.focus();
         				return false;
         			}
         		}                  
         		if(document.adduserfrm.ip_address.value!=''){
         			if(!T_ip){
         			alert("Please enter ip address for access!");
         			document.adduserfrm.ip_address.focus();
         			err=false;
         			return false;
         			}
         		}
         
         	}         
         	if(val==2){
         		var alphaExpname = /^[a-zA-Z]+[a-zA-Z ]*$/;
         		var numexp = /^[0-9]+$/;
         		var AtxPlaceBirth=document.adduserfrm.AtxPlaceBirth.value.match(alphaExpname);
         		var hcity=document.adduserfrm.hcity.value.match(alphaExpname);
         		var hcountry =document.adduserfrm.hcountry.value.match(alphaExpname);
         		var hstate =document.adduserfrm.hstate.value.match(alphaExpname);
         		var pincode =document.adduserfrm.pincode.value.match(numexp);
         		var phone =document.adduserfrm.phone.value.match(numexp);
         		var localphone=document.adduserfrm.localphone.value.match(numexp);
         		var contactphone=document.adduserfrm.contactphone.value.match(numexp);
         		var fax=document.adduserfrm.fax.value.match(numexp);
         		var AtxPhone=document.adduserfrm.AtxPhone.value.match(numexp);
         		var pager=document.adduserfrm.pager.value.match(numexp);
         		var emergencycontactlocal=document.adduserfrm.emergencycontactlocal.value.match(alphaExpname);
         		var emergencycontact=document.adduserfrm.emergencycontact.value.match(alphaExpname);
         		var emergencycontactphonelocal=document.adduserfrm.emergencycontactphonelocal.value.match(numexp);
         		var emergencycontactphone=document.adduserfrm.emergencycontactphone.value.match(numexp);
         
         		if(document.adduserfrm.AtxBloodGroup.value!='')
         		{
         			var alphaExpname = /^[a-zA-Z]+[a-zA-Z+]*$/;
         			var valAtxBloodGroup = document.adduserfrm.AtxBloodGroup.value.match(alphaExpname);
         
         			if(!valAtxBloodGroup)
         			{
         				alert("Please enter valid Blood Group!");
         				document.adduserfrm.AtxBloodGroup.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.AtxPlaceBirth.value!='')
         		{
         			if(!AtxPlaceBirth)
         			{
         				alert("Please enter valid Place of birth Characters!");
         				document.adduserfrm.AtxPlaceBirth.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.hcity.value!='')
         		{
         			if(!hcity)
         			{
         				alert("Please enter valid City Name!");
         				document.adduserfrm.hcity.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.hstate.value!='')
         		{
         			if(!hstate)
         			{
         				alert("Please enter valid State Name!");
         				document.adduserfrm.hstate.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.hcountry.value!='')
         		{
         			if(!hcountry)
         			{
         				alert("Please enter valid Country Name!");
         				document.adduserfrm.hcountry.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.pincode.value!='')
         		{
         			if(!pincode)
         			{
         				alert("Please enter valid Pincode!");
         				document.adduserfrm.pincode.focus();
         				return false;
         			}
         		}
         		if(document.adduserfrm.phone.value!='')
         		{
         			if(!phone)
         			{
         				alert("Please enter valid phone number!");
         				document.adduserfrm.phone.focus();
         				return false;
         			}
         		}
         		if(document.adduserfrm.localphone.value!='')
         		{
         			if(!localphone)
         			{
         				alert("Please enter valid localphone number!");
         				document.adduserfrm.localphone.focus();
         				return false;
         			}
         		}
         		if(document.adduserfrm.contactphone.value!='')
         		{
         			if(!contactphone)
         			{
         				alert("Please enter valid contactphone number!");
         				document.adduserfrm.contactphone.focus();
         				return false;
         			}
         		}
         		if(document.adduserfrm.fax.value!='')
         		{
         			if(!fax)
         			{
         				alert("Please enter valid fax number!");
         				document.adduserfrm.fax.focus();
         				return false;
         			}
         		}
         		if(document.adduserfrm.AtxPhone.value!='')
         		{
         			if(!AtxPhone)
         			{
         				alert("Please enter valid Phone number!");
         				document.adduserfrm.AtxPhone.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.pager.value!='')
         		{
         			if(!pager)
         			{
         				alert("Please enter valid pager number!");
         				document.adduserfrm.pager.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.mobile.value!='')
         		{
         			var mobileExpname = /^[0-9]+$/;
         			var valmobile = document.adduserfrm.mobile.value.match(mobileExpname);
         			if(!valmobile)
         			{
         				alert("Please enter valid mobile no!");
         				document.adduserfrm.mobile.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.emergencycontactlocal.value!='')
         		{
         			if(!emergencycontactlocal)
         			{
         				alert("Please enter valid local contact person!");
         				document.adduserfrm.emergencycontactlocal.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.emergencycontactphonelocal.value!='')
         		{
         			if(!emergencycontactphonelocal)
         			{
         				alert("Please enter valid local emergency phone number!");
         				document.adduserfrm.emergencycontactphonelocal.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.emergencycontact.value!='')
         		{
         			if(!emergencycontact)
         			{
         				alert("Please enter valid other contact person !");
         				document.adduserfrm.emergencycontact.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.emergencycontactphone.value!='')
         		{
         			if(!emergencycontactphone)
         			{
         				alert("Please enter valid other emergency phone number!");
         				document.adduserfrm.emergencycontactphone.focus();
         				return false;
         			}
         		}
         
         	}         
         	if(val==4){         
         		var numericExpression = /^[0-9]+$/;
         		var alfaExpression = /^[a-zA-Z]+[a-zA-Z ]*$/;
         		var alfanumericExpression = /^[A-Za-z0-9]+$/;
         		var alfanumericnameExpression = /^[A-Za-z 0-9]+$/;
         
         		var valAtxPassportNo = document.adduserfrm.AtxPassportNo.value.match(alfanumericExpression);
         		var valAtxIssuedAtP = document.adduserfrm.AtxIssuedAtP.value.match(alfaExpression);
         		var valAtxLicenseNo = document.adduserfrm.AtxLicenseNo.value.match(alfanumericExpression);
         		var valAtxIssuedAtL = document.adduserfrm.AtxIssuedAtL.value.match(alfaExpression);
         		var valAtxBankName = document.adduserfrm.AtxBankName.value.match(alfaExpression);
         		var valAtxAccountNo = document.adduserfrm.AtxAccountNo.value.match(numericExpression);
         		var valAtxBranch = document.adduserfrm.AtxBranch.value.match(alfanumericnameExpression);
         		var valAtxAccountType = document.adduserfrm.AtxAccountType.value.match(alfaExpression);        
         		//AtxPassportNo   AtxIssuedAtP  AtxLicenseNo AtxIssuedAtL AtxBankName AtxAccountNo AtxBranch AtxAccountType
         		var valFatherName = document.adduserfrm.FatherName.value.match(alfaExpression);
         		var valFatherAge = document.adduserfrm.FatherAge.value.match(numericExpression);
         		var valFatherOccupation = document.adduserfrm.FatherOccupation.value.match(alfanumericnameExpression);
         
         		var valMotherName = document.adduserfrm.MotherName.value.match(alfaExpression);
         		var valMotherAge = document.adduserfrm.MotherAge.value.match(numericExpression);
         		var valMotherOccupation = document.adduserfrm.MotherOccupation.value.match(alfanumericnameExpression);
         
         		var valSpouseName = document.adduserfrm.SpouseName.value.match(alfaExpression);
         		var valSpouseAge = document.adduserfrm.SpouseAge.value.match(numericExpression);
         		var valSpouseOccupation = document.adduserfrm.SpouseOccupation.value.match(alfanumericnameExpression);
         
         		var valChild1Name = document.adduserfrm.Child1Name.value.match(alfaExpression);
         		var valChild1Age = document.adduserfrm.Child1Age.value.match(numericExpression);
         		var valChild1Occupation = document.adduserfrm.Child1Occupation.value.match(alfanumericnameExpression);
         
         		var valChild2Name = document.adduserfrm.Child2Name.value.match(alfaExpression);
         		var valChild2Age = document.adduserfrm.Child2Age.value.match(numericExpression);
         		var valChild2Occupation = document.adduserfrm.Child2Occupation.value.match(alfanumericnameExpression);
         
         		if(document.adduserfrm.AtxPassportNo.value!='')
         		{
         			if(User.Trim(document.adduserfrm.AtxPassportNo.value)=='')
         			{
         				alert("Please enter valid Passport No.!");
         				document.adduserfrm.AtxPassportNo.focus();
         				err=false;
         				return false;
         			}
         
         			if(!valAtxPassportNo)
         			{
         				alert("Please enter valid Passport No.!");
         				document.adduserfrm.AtxPassportNo.focus();
         				err=false;
         				return false;
         			}
         		}
         
         
         		if(document.adduserfrm.AtxIssuedAtP.value!='')
         		{
         			if(User.Trim(document.adduserfrm.AtxIssuedAtP.value)=='')
         			{
         				alert("Please enter Passport Issued at!");
         				document.adduserfrm.AtxIssuedAtP.focus();
         				return false;
         			}
         
         			if(!valAtxIssuedAtP)
         			{
         				alert("Please enter Passport Issued at!");
         				document.adduserfrm.AtxIssuedAtP.focus();
         				return false;
         			}
         		}
         		if(document.adduserfrm.AtxLicenseNo.value!='')
         		{
         			if(User.Trim(document.adduserfrm.AtxLicenseNo.value)=='')
         			{
         				alert("Please enter valid License No!");
         				document.adduserfrm.AtxLicenseNo.focus();
         				return false;
         			}
         
         			if(!valAtxLicenseNo)
         			{
         				alert("Please enter valid License No!");
         				document.adduserfrm.AtxLicenseNo.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.AtxIssuedAtL.value!='')
         		{
         			if(User.Trim(document.adduserfrm.AtxIssuedAtL.value)=='')
         			{
         				alert("Please enter License Issued At!");
         				document.adduserfrm.AtxIssuedAtL.focus();
         				return false;
         			}
         
         			if(!valAtxIssuedAtL)
         			{
         				alert("Please enter License Issued At!");
         				document.adduserfrm.AtxIssuedAtL.focus();
         				return false;
         			}
         		}                  
         		if(document.adduserfrm.AtxBankName.value!='')
         		{
         			if(User.Trim(document.adduserfrm.AtxBankName.value)=='')
         			{
         				alert("Please enter valid Bank Name!");
         				document.adduserfrm.AtxBankName.focus();
         				err=false;
         				return false;
         			}
         
         			if(!valAtxBankName)
         			{
         				alert("Please enter valid Bank Name!");
         				document.adduserfrm.AtxBankName.focus();
         				err=false;
         				return false;
         			}
         		}
                 
         		if(document.adduserfrm.AtxAccountNo.value!='')
         		{
         			if(User.Trim(document.adduserfrm.AtxAccountNo.value)=='')
         			{
         				alert("Please enter valid Account No!");
         				document.adduserfrm.AtxAccountNo.focus();
         				err=false;
         				return false;
         			}
         
         			if(!valAtxAccountNo)
         			{
         				alert("Please enter valid Account No!");
         				document.adduserfrm.AtxAccountNo.focus();
         				err=false;
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.AtxBranch.value!='')
         		{
         			if(User.User.Trim(document.adduserfrm.AtxBranch.value)=='')
         			{
         				alert("Please enter valid Branch!");
         				document.adduserfrm.AtxBranch.focus();
         				err=false;
         				return false;
         			}
         
         			if(!valAtxBranch)
         			{
         				alert("Please enter valid Branch!");
         				document.adduserfrm.AtxBranch.focus();
         				err=false;
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.AtxAccountType.value!='')
         		{
         			if(User.Trim(document.adduserfrm.AtxAccountType.value)=='')
         			{
         				alert("Please enter valid Account Type!");
         				document.adduserfrm.AtxAccountType.focus();
         				err=false;
         				return false;
         			}
         
         			if(!valAtxAccountType)
         			{
         				alert("Please enter valid Account Type!");
         				document.adduserfrm.AtxAccountType.focus();
         				err=false;
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.FatherName.value!='')
         		{
         			if(User.Trim(document.adduserfrm.FatherName.value)=='')
         			{
         				alert("Please enter Father Name!");
         				document.adduserfrm.FatherName.focus();
         				return false;
         			}
         
         			if(!valFatherName)
         			{
         				alert("Please enter Father Name in valid characters!");
         				document.adduserfrm.FatherName.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.FatherAge.value!='')
         		{
         			if(User.Trim(document.adduserfrm.FatherAge.value)=='')
         			{
         				alert("Please enter Father Age!");
         				document.adduserfrm.FatherAge.focus();
         				return false;
         			}
         
         			if(!valFatherAge)
         			{
         				alert("Please enter Father Age!");
         				document.adduserfrm.FatherAge.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.FatherOccupation.value!='')
         		{
         			if(User.Trim(document.adduserfrm.FatherOccupation.value)=='')
         			{
         				alert("Please enter Father Occupation!");
         				document.adduserfrm.FatherOccupation.focus();
         				return false;
         			}
         
         			if(!valFatherOccupation)
         			{
         				alert("Please enter Father Occupation!");
         				document.adduserfrm.FatherOccupation.focus();
         				return false;
         			}
         		}
         
         
         		if(document.adduserfrm.MotherName.value!='')
         		{
         			if(User.Trim(document.adduserfrm.MotherName.value)=='')
         			{
         				alert("Please enter Mother Name!");
         				document.adduserfrm.MotherName.focus();
         				return false;
         			}
         
         			if(!valMotherName)
         			{
         				alert("Please enter Mother Name in valid characters!");
         				document.adduserfrm.MotherName.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.MotherAge.value!='')
         		{
         			if(User.Trim(document.adduserfrm.MotherAge.value)=='')
         			{
         				alert("Please enter Mother Age!");
         				document.adduserfrm.MotherAge.focus();
         				return false;
         			}
         
         			if(!valMotherAge)
         			{
         				alert("Please enter Mother Age!");
         				document.adduserfrm.MotherAge.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.MotherOccupation.value!='')
         		{
         			if(User.Trim(document.adduserfrm.MotherOccupation.value)=='')
         			{
         				alert("Please enter Mother Occupation!");
         				document.adduserfrm.MotherOccupation.focus();
         				return false;
         			}
         
         			if(!valMotherOccupation)
         			{
         				alert("Please enter Mother Occupation!");
         				document.adduserfrm.MotherOccupation.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.SpouseName.value!='')
         		{
         			if(User.Trim(document.adduserfrm.SpouseName.value)=='')
         			{
         				alert("Please enter Spouse Name!");
         				document.adduserfrm.SpouseName.focus();
         				return false;
         			}
         
         			if(!valSpouseName)
         			{
         				alert("Please enter Spouse Name in valid characters!");
         				document.adduserfrm.SpouseName.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.SpouseAge.value!='')
         		{
         			if(User.Trim(document.adduserfrm.SpouseAge.value)=='')
         			{
         				alert("Please enter Spouse Age!");
         				document.adduserfrm.SpouseAge.focus();
         				return false;
         			}
         
         			if(!valSpouseAge)
         			{
         				alert("Please enter Spouse Age!");
         				document.adduserfrm.SpouseAge.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.SpouseOccupation.value!='')
         		{
         			if(User.Trim(document.adduserfrm.SpouseOccupation.value)=='')
         			{
         				alert("Please enter Spouse Occupation!");
         				document.adduserfrm.SpouseOccupation.focus();
         				return false;
         			}
         
         			if(!valSpouseOccupation)
         			{
         				alert("Please enter Spouse Occupation!");
         				document.adduserfrm.SpouseOccupation.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.Child1Name.value!='')
         		{
         			if(User.Trim(document.adduserfrm.Child1Name.value)=='')
         			{
         				alert("Please enter Child1 Name!");
         				document.adduserfrm.Child1Name.focus();
         				return false;
         			}
         
         			if(!valChild1Name)
         			{
         				alert("Please enter Child1 Name in valid characters!");
         				document.adduserfrm.Child1Name.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.Child1Age.value!='')
         		{
         			if(User.Trim(document.adduserfrm.Child1Age.value)=='')
         			{
         				alert("Please enter Child1 Age!");
         				document.adduserfrm.Child1Age.focus();
         				return false;
         			}
         
         			if(!valChild1Age)
         			{
         				alert("Please enter Child1 Age!");
         				document.adduserfrm.Child1Age.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.Child1Occupation.value!='')
         		{
         			if(User.Trim(document.adduserfrm.Child1Occupation.value)=='')
         			{
         				alert("Please enter Child1 Occupation!");
         				document.adduserfrm.Child1Occupation.focus();
         				return false;
         			}
         
         			if(!valChild1Occupation)
         			{
         				alert("Please enter Child1 Occupation!");
         				document.adduserfrm.Child1Occupation.focus();
         				return false;
         			}
         		}
         
         
         		if(document.adduserfrm.Child2Name.value!='')
         		{
         			if(User.Trim(document.adduserfrm.Child2Name.value)=='')
         			{
         				alert("Please enter Child2 Name!");
         				document.adduserfrm.Child2Name.focus();
         				return false;
         			}
         
         			if(!valChild2Name)
         			{
         				alert("Please enter Child2 Name in valid characters!");
         				document.adduserfrm.Child2Name.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.Child2Age.value!='')
         		{
         			if(User.Trim(document.adduserfrm.Child2Age.value)=='')
         			{
         				alert("Please enter Child2 Age!");
         				document.adduserfrm.Child2Age.focus();
         				return false;
         			}
         
         			if(!valChild2Age)
         			{
         				alert("Please enter Child2 Age!");
         				document.adduserfrm.Child2Age.focus();
         				return false;
         			}
         		}
         
         		if(document.adduserfrm.Child2Occupation.value!='')
         		{
         			if(User.Trim(document.adduserfrm.Child2Occupation.value)=='')
         			{
         				alert("Please enter Child2 Occupation!");
         				document.adduserfrm.Child2Occupation.focus();
         				return false;
         			}
         
         			if(!valChild2Occupation)
         			{
         				alert("Please enter Child2 Occupation!");
         				document.adduserfrm.Child2Occupation.focus();
         				return false;
         			}
         		}
         
         	}        
         	if(val==3){
         		var numericExpression = /^[0-9]+$/;         
         		var V_PhoneNo = document.adduserfrm.authenticated_mobile.value.match(numericExpression);
         		var T_pass = document.adduserfrm.tel_password.value.match(numericExpression);        
         		if(document.adduserfrm.authenticated_mobile.value==''){
            		alert("Please enter your authenticated mobile no!");
            		document.adduserfrm.authenticated_mobile.focus();
            		return false;
         		}
         		if(!V_PhoneNo){
            		alert("Please enter your authenticated mobile no!");
            		document.adduserfrm.authenticated_mobile.focus();
            		return false;
         		}         
         		if(document.adduserfrm.tel_password.value==''){
            		alert("Please enter your telephony password!");
            		document.adduserfrm.tel_password.focus();
            		return false;
         		}
         		if(!T_pass){
            		alert("Please enter your telephony password!");
            		document.adduserfrm.tel_password.focus();
            		return false;
         		}
                        		//return true;
         	}
         	var numexp = /^[0-9]+$/;
         	var phone =document.adduserfrm.phone.value.match(numexp);
         	if(User.Trim(document.adduserfrm.phone.value)==''){
        			alert("Please enter your phone number!");
        			document.adduserfrm.phone.focus();
        			err=false;
        			return false;
     		   }
     
        		if(!phone)
        		{
    				alert("Please enter valid phone number!");
    				document.adduserfrm.phone.focus();
    				return false;
        		}
        		if(document.adduserfrm.phone.value.length>=15){
        			alert("Phone number Must be 15 Digits");
    				document.adduserfrm.phone.focus();
    				return false;
        	}
         	var formData = new FormData($('#adduserfrm')[0]);
         	$.ajax({
                method: 'POST',
                url: 'User/web_quickcreateuser.php',
                cache: false,
                processData: false,
                contentType: false,
                data: formData,
                dataType: "json",
                success: function (response) {
                	console.log(response.status);
                	if(response.status == 'true'){
                		alert(response.message);
                     var encodedToken = btoa('web_userhome');
                     window.location.href = "user_index.php?token=" + encodeURIComponent(encodedToken);
                	}else{
                		console.log('sssssssssss');
            			alert(response.message);            			
                	}
                }
            });        	
        },
        go:function(val){
         	if(val==1){
      		  var as =User.validateformfirst(val);
      		 	if(as==false){
      				return false;
      			}         
      		  var style_sheet = getStyleObject('frm2');         
         		if (style_sheet){
         			User.hideAll();
         			User.changeObjectVisibility("frm2", "visible");
           		 }
         	  }
         	  if(val==2){
         		  var style_sheet = getStyleObject('frm1');
         		   if (style_sheet){
         				User.hideAll();
         				User.changeObjectVisibility('frm1', "visible");
           			}
         	  }
         	  if(val==3){
         		var style_sheet = getStyleObject('frm3');
         		if (style_sheet){
         			User.hideAll();
         			User.changeObjectVisibility('frm3', "visible");
           		 }
         	  }
         	  if(val==4)
         	  {
         		var style_sheet = getStyleObject('frm4');
         		if (style_sheet)
         		{
         			User.hideAll();
         			User.changeObjectVisibility('frm4', "visible");
           		 }
         	  }         
        },
        hideAll:function(){
            changeObjectVisibility('frm1','hidden');
            changeObjectVisibility("frm2",'hidden');
            changeObjectVisibility("frm3",'hidden');
            changeObjectVisibility("frm4",'hidden');       
     	},
     	changeObjectVisibility:function(objectId, newVisibility){
          // first get the object's stylesheet	         
          var styleObject = getStyleObject(objectId);
          // then if we find a stylesheet, set its visibility
          // as requested
          if (styleObject)
      	{
      		styleObject.visibility = newVisibility;
      		return true;
          }
      	else
      	{
      		return false;
          }
      },
     getStyleObject:function(objectId){
        // checkW3C DOM, then MSIE 4, then NN 4.
        if(document.getElementById && document.getElementById(objectId))
        {
      	return document.getElementById(objectId).style;
        }
         else if (document.all && document.all(objectId))
         {
      	 return document.all(objectId).style;
         }
         else if (document.layers && document.layers[objectId])
         {
      	 return document.layers[objectId];
         }
         else
         {
      	return false;
         }
     }
	}
	User.init();
   // Expose User object for debugging purposes
   window.User = User;
})