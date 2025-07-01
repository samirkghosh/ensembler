
// Added By farhan akhtar on 25-12-2024 For Multi-Tanent CompanyID Validation for number input only and the Maximum Length is 10 numbers
$(document).ready(function(){
	$("#companyID").on("input", function () {
		// Remove any non-digit characters
		let value = $(this).val().replace(/[^0-9]/g, '');

		// Limit the length to 10 characters
		if (value.length > 10) {
			value = value.substring(0, 10);
		}
		
		// Set the processed value back to the textbox
		$(this).val(value);
    });
});



$('.buttonlogin').click(function(e) {
	if(document.frmlogin.loginID.value==''){
		alert("Please enter your Email ID");
		window.document.frmlogin.loginID.focus();
		return false;
	} else if(document.frmlogin.passID.value==''){
		alert("Please enter your Password");
		window.document.frmlogin.passID.focus();
		return false;
	} else if(document.frmlogin.companyID.value==''){  // Added By farhan on 25-12-2024 For Multi-Tanent companyID Validation  
		alert("Please enter your Company ID");
		window.document.frmlogin.companyID.focus();
		return false;
	} else{	
		h=hex_md5(sha1(hex_md5(document.frmlogin.passID.value)));
		document.frmlogin.output.value=h;
		e.preventDefault(); 
		$.ajax({
	        url: 'function/web_function_login.php',
	        type: 'POST',
	        data: $('#formlogin').serialize(),
	        dataType: 'json',
	        success: function (response) {
	        	console.log(response);
	        	if(response.status == false){
	        		$('.errormsglogin').text(response.message);
	        		if(response.location){
	        			window.location.href = response.location;
	        		}	        		
	        	}else{
	        		$('.errormsglogin').text(response.message);
	        		window.location.href = response.location;
	        	}
	        },
	        error: function (error) {
	            console.error('Error inserting data:', error);
	        }
	    });
	}
    e.preventDefault();
});
function loader(){
	try{
		var b=document.frmlogin.loginID.value;
		null!==b&&0<b.length?document.frmlogin.passID.focus():document.frmlogin.loginID.focus()
	}catch(a){
		if(window==top)throw a;
	}
}
function checkCaps(b){var a=0,c=!1,a=document.all?b.keyCode:b.which,c=b.shiftKey;b=document.getElementById("pwcaps");var d=65<=a&&90>=a,a=97<=a&&122>=a;if(d&&!c||a&&c)b.style.display="block";else if(a&&!c||d&&c)b.style.display="none"};
function clearText(field) {
 if (field.defaultValue == field.value) field.value = '';
 else if (field.value == '') field.value = field.defaultValue;
}
