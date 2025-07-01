
$(function() {

//-------------------- special character validation
//  use this rules { regex:"^[a-zA-Z .']+$" }
$.validator.addMethod("regex",
   function (value, element, regexp) {
      var re = new RegExp(regexp);
      return this.optional(element) || re.test(value);
   },"Special Character Not Allowed"
);




//
$("#commentForm").validate({
    
        // Specify the validation rules
        rules: {
            
			  mem_company:{accept:"[a-zA-Z]+",minlength:4,maxlength:64},
			  mem_fname:{required:true,minlength:2,maxlength:64,regex:"^[a-zA-Z .']+$"},
			  mem_lname:{required:true,minlength:2,maxlength:64,regex:"^[a-zA-Z .']+$"},
			  mem_email:{required:true,email:true,maxlength:255},
			  mem_phone:{required:false,digits:true,minlength:9,maxlength:15},
			  mem_mobile:{required:true,digits:true,minlength:10,maxlength:12},
			  mem_address:{required:true,},
			  mem_location:{maxlength:64},
			  mem_city:{required:true},
			  mem_state:{required:true},
			  mem_country:{required:true},
			  mem_zipcode:{required:true, digits:true,minlength:5,maxlength:10},
			 
	 
            agree: "required"
        },
        
        // Specify the validation error messages
        messages: {
            	  mem_company:{accept:"No Numbers - Alphabets only!",minlength:"Minimum 4 characters long!",maxlength:"Maximum 64 characters long!"},
				  mem_fname:{required:"Please Enter First Name",accept:"Please Enter Characters only for First Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  mem_lname:{required:"Please Enter Last Name",accept:"Please Enter Characters only for Last Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  mem_email:{required:"Please Enter Email",email:"Enter Valid Email",maxlength:"Max length 255 Characters"},
				  mem_phone:{required:"Please Enter phone",digits:"Digits only",minlength:"Minimum 9 digits long!",maxlength:"Maximum 15 digits long!"},
				  mem_mobile:{required:"Please Enter Mobile Number",digits:"Digits only",minlength:"Minimum 10 digits",maxlength:"Maximum 10 digits"},
				  mem_address:{required:"Please enter address",},
				  mem_location:{maxlength:"Maximum 64 characters long!"},
				  mem_city:{required:"Enter City"},
				  mem_state:{required:"Enter State"},
				  mem_country:{required:"Select Country"},
				  mem_zipcode:{required:"Enter Zip",digits:"Digits only",minlength:"Minimum 5 digits",maxlength:"Maximum 6 digits"},
			
			
            agree: "Please accept our policy"
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});

//Zone
$("#masterzone").validate({
    
        // Specify the validation rules
        rules: {
			  V_CategoryName:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
			 },
        
        // Specify the validation error messages
        messages: {
				  V_CategoryName:{required:"Please Enter Zone Name",accept:"Please Enter Characters only for Zone Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				 },
        
        submitHandler: function(form) {
            form.submit();
        }
})

//Area
$("#masterarea").validate({
        // Specify the validation rules
        rules: {
			  i_AreaId:{required:true, number: true,  minlength:1, maxlength:2}, 
			  V_CategoryName:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
			  I_ZoneID:{required:true}		 
	         },
        
        // Specify the validation error messages
        messages: {
			i_AreaId:{required:"Please Enter Area Id",number:"Please Enter number only for Area id",minlength:"Minimum 1 characters long!",maxlength:"Maximum 2 characters long!"},
			V_CategoryName:{required:"Please Enter Area Name",accept:"Please Enter Characters only for Area Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
			I_ZoneID:{required:"Please Select Zone"}
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});




//mastercircle
$("#mastercircle").validate({
    
        // Specify the validation rules
        rules: {
              V_CategoryName:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
			
        },
        
        // Specify the validation error messages
        messages: {
				  V_CategoryName:{required:"Please Enter Circle Name",accept:"Please Enter Characters only for Circle Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
		},
        
        submitHandler: function(form) {
            form.submit();
        }
});

// SR Status
$("#mastersrstatus").validate({
    
        // Specify the validation rules
        rules: {
				V_CategoryName:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
        },
        
        // Specify the validation error messages
        messages: {
				  V_CategoryName:{required:"Please Enter SR Status Name",accept:"Please Enter Characters only for SR Status Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});


// Master Task Type
$("#mastertasktype").validate({
    
        // Specify the validation rules
        rules: {
				V_CategoryName:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
        },
        
        // Specify the validation error messages
        messages: {
				V_CategoryName:{required:"Please Enter Task Type Name",accept:"Please Enter Characters only for Task Type Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});

// Master Category
$("#mastersrcat").validate({
    
        // Specify the validation rules
        rules: {
             I_ServiceID:{required: true},
			  V_CategoryName:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
			  V_Description:{required:false,accept:"[a-zA-Z]+",minlength:2,maxlength:64},			 
	 
            agree: "required"
        },
        
        // Specify the validation error messages
        messages: {
				  I_ServiceID:{required:"Please Select Service"},
				  V_CategoryName:{required:"Please Enter Category Name",accept:"Please Enter Characters only for Category Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Description:{required:"Please Enter Description",accept:"Please Enter Characters only for Description",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
			
            agree: "Please accept our policy"
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});
 


//SR SUB CAT
$("#mastersrsubcat").validate({
    
        // Specify the validation rules
        rules: {
              I_ServiceID:{required: true},
			  I_CategoryID:{required: true},
			  V_SubCategoryName:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
			  V_Description:{required:false,accept:"[a-zA-Z]+",minlength:2,maxlength:64},			 
	 
            agree: "required"
        },
        
        // Specify the validation error messages
        messages: {
				  I_ServiceID:{required:"Please Select Service"},
				  I_CategoryID:{required:"Please Select Category"},
				  V_SubCategoryName:{required:"Please Enter Subcategory Name",accept:"Please Enter Characters only for Subcategory Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Description:{required:"Please Enter Description",accept:"Please Enter Characters only for Description",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
			
            agree: "Please accept our policy"
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});



//SR SUB SUB CAT
$("#master_subsubcat_service").validate({
    
        // Specify the validation rules
        rules: {  
 			  I_ServiceID:{required: true},
			  I_CategoryID: {required: true},
			  I_SubCategoryID: {required: true},
			  V_SubSubCategoryName:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
			  V_Description:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64},
			 // mem_country:{required:true},	
        },
        
        // Specify the validation error messages
        messages: {  		  
			   I_ServiceID:{required:"Please Select Service"},
				  I_CategoryID:{required:"Please Select SR Category"},
				  I_SubCategoryID:{required:"Please Select SR Sub Category"},
				  V_SubSubCategoryName:{required:"Please Enter SR Sub Sub Category",accept:"Please Enter SR Sub Sub Category",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Description:{required:"Please Enter Description",accept:"Please Enter Characters only for Description",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"}
				//  mem_country:{required:"Select Country"},
        },
		
	
        
        submitHandler: function(form) {
            form.submit();
        }
});

//SLA RULES
$("#frmsla1").validate({
    
        // Specify the validation rules
        rules: {  
 			  I_ServiceID:{required: true},
			  I_CategoryID: {required: true},
			  I_SubCategoryID: {required: true},
			  V_Escalation_Time:{required:true, digits:true, minlength:1,maxlength:3},
		},
        
        // Specify the validation error messages
        messages: {  		  
			   I_ServiceID:{required:"Please Select Service"},
				  I_CategoryID:{required:"Please Select Category"},
				  I_SubCategoryID:{required:"Please Select Sub Category"},
				  V_Escalation_Time:{required:"Please Enter Escalation Time",digits:"Please Enter Escalation Time in digits",minlength:"Minimum 1 digits long!",maxlength:"Maximum 3 digits long!"},
		},
		
	
        
        submitHandler: function(form) {
            form.submit();
        }
});

// Master Complain Group
$("#mastercompgroup").validate({
    
        // Specify the validation rules
        rules: {
              V_CategoryName:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
        },
        
        // Specify the validation error messages
        messages: {
				  V_CategoryName:{required:"Please Enter Complain Group Name",accept:"Please Enter Characters only for Complain Group Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});

// Master Complain Status
$("#mastercompstatus").validate({
    
        // Specify the validation rules
        rules: {
              V_CategoryName:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
        },
        
        // Specify the validation error messages
        messages: {
				  V_CategoryName:{required:"Please Enter Complain Status Name",accept:"Please Enter Characters only for Complain Status Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});

//SLA RULES
$("#frmsla1").validate({
    
        // Specify the validation rules
        rules: {  
 			  I_ServiceID:{required: true},
			  I_CategoryID: {required: true},
			  I_SubCategoryID: {required: true},
			  V_Escalation_Time:{required:true, digits:true, minlength:1,maxlength:3},
		},
        
        // Specify the validation error messages
        messages: {  		  
			   I_ServiceID:{required:"Please Select Service"},
				  I_CategoryID:{required:"Please Select Category"},
				  I_SubCategoryID:{required:"Please Select Sub Category"},
				  V_Escalation_Time:{required:"Please Enter Escalation Time",digits:"Please Enter Escalation Time in digits",minlength:"Minimum 1 digits long!",maxlength:"Maximum 3 digits long!"},
		},
		
	
        
        submitHandler: function(form) {
            form.submit();
        }
});


//master cat servuces addedit page
$("#mastersrsubsubcat").validate({
    
        // Specify the validation rules
        rules: {   
			  V_SubSubCategoryName:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
			  V_Description:{required:false,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},	
        },
        
        // Specify the validation error messages
        messages: {
				  V_SubSubCategoryName:{required:"Please Enter SR Sub Sub Category",accept:"Please Enter SR Sub Sub Category",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Description:{required:"Please Enter Description",accept:"Please Enter Characters only for Description",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});



//master cat servuces addedit page
$("#master_disposition_service").validate({
    
        // Specify the validation rules
        rules: {   
			  V_CategoryName:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
			  V_Description:{required:false,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},	
        },
        
        // Specify the validation error messages
        messages: {
				  V_CategoryName:{required:"Please Enter Disposition",accept:"Please Enter Disposition",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Description:{required:"Please Enter Description",accept:"Please Enter Characters only for Description",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});


//master priority addedit page
$("#mastersrcatdisposition").validate({
    
        // Specify the validation rules
        rules: {   
			  V_CategoryName:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
			  V_Description:{required:false,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},	
        },
        
        // Specify the validation error messages
        messages: {
				  V_CategoryName:{required:"Please Enter Priority",accept:"Please Enter Priority",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Description:{required:"Please Enter Description",accept:"Please Enter Characters only for Description",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});

////master_callstatus_services_addedit page
$("#master_callstatus_service").validate({
    
        // Specify the validation rules
        rules: {   
			  V_CategoryName:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
			  V_Description:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:200},	
        },
        
        // Specify the validation error messages
        messages: {
				  V_CategoryName:{required:"Please Enter SR Call Status",accept:"Please Enter Call Status",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Description:{required:"Please Enter Description",accept:"Please Enter Characters only for Description",minlength:"Minimum 2 characters long!",maxlength:"Maximum 200 characters long!"},
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});



////master_user_type_service page
$("#master_user_type_service").validate({
    
        // Specify the validation rules
        rules: {   
			  V_CategoryName:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
        },
        
        // Specify the validation error messages
        messages: {
				  V_CategoryName:{required:"Please Enter User Type",accept:"Please Enter User Type",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});



///master_user_type_service page
$("#master_service").validate({
    //alert("a");
        // Specify the validation rules
        rules: {  
			  V_ServiceName:{required:true,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64},
			  V_ServiceID:{required:true,accept:"[a-zA-Z0-9_]+",minlength:2,maxlength:64},
			  V_Custom1:{required:true,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64},
			  V_Custom2:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64},
			  V_Custom3:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64},
			  V_Custom4:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64},
			  V_Custom5:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64},
			 // V_Custom6:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64}
			  V_Custom6:{required:true,accept:'jpg|JPG|Jpg|gif|GIF|Gif|png|PNG|Png'},
        },
        
        // Specify the validation error messages
        messages: {
				  V_ServiceName:{required:"Please Enter Service Name",accept:"Please Enter Service Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_ServiceID:{required:"Please Enter Service Databse Name",accept:"Please Enter Service Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Custom1:{required:"Please Enter Custom Field Value",accept:"Please Enter Custom Field Value",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Custom2:{required:"Please Enter Custom Field Value",accept:"Please Enter Custom Field Value",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Custom3:{required:"Please Enter Custom Field Value",accept:"Please Enter Custom Field Value",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Custom4:{required:"Please Enter Custom Field Value",accept:"Please Enter Custom Field Value",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Custom5:{required:"Please Enter Custom Field Value",accept:"Please Enter Custom Field Value",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Custom6:{required:"Please select file",accept:"Please Enter Jpg/Gif/Png file only"},
				  //V_Custom6:{required:"Please Enter Custom Field Value",accept:"Please Enter Custom Field Value",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"}
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});

//master_timeslot
$("#master_timeslot").validate({
    // Specify the validation rules
        rules: {  
			  V_TimeslotName:{required:true,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64},
			  StartTime:{required:true,number: true,minlength:1,maxlength:10},
			  S_Meridian:{required:true,accept:"[a-zA-Z0-9 ]+",minlength:1,maxlength:3},
			  EndTime:{required:true,number: true,minlength:1,maxlength:10},
			  E_Meridian:{required:true,accept:"[a-zA-Z0-9 ]+",minlength:1,maxlength:3},
        },
        
        // Specify the validation error messages
        messages: {
				  V_TimeslotName:{required:"Please Enter Time Slot Name",accept:"Please Enter Time Slot Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  StartTime:{required:"Please Enter Start Time Slot",number:"Please Enter Start Time Slot",minlength:"Minimum 1 characters long!",maxlength:"Maximum 10 characters long!"},
				  S_Meridian:{required:"Please Enter Start Slot Meridian",accept:"Please Enter Start Slot Meridian",minlength:"Minimum 1 characters long!",maxlength:"Maximum 3 characters long!"},
				  EndTime:{required:"Please Enter End TIme Slot",number:"Please Enter End TIme Slot",minlength:"Minimum 1 characters long!",maxlength:"Maximum 10 characters long!"},
				  E_Meridian:{required:"Please Enter End TIme Slot Meridian",accept:"Please Enter End TIme Slot Meridian",minlength:"Minimum 1 characters long!",maxlength:"Maximum 3 characters long!"},
		},
        
        submitHandler: function(form) {
            form.submit();
        }
}); 

 ////Service Request Validation on View Customer Page
$("#frmviewcustomer").validate({
    
        // Specify the validation rules
        rules: {  
 			  I_SRtypeID: {required: true},
			  I_PriorityID: {required: true},
			  I_ServiceID: {required: true},
			  Subject:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64},
			  I_CategoryID: {required: true},
			  I_SubCategoryID: {required: true},
			  V_Description:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64},
			  I_SRStatusID: {required: true},
			  AssignedTo:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:8,maxlength:200},
        },
        
        // Specify the validation error messages
        messages: {  		  
			 I_SRtypeID:{required:"Please Select SR Type"},
			 I_PriorityID:{required:"Please Select Priority"},
			 I_ServiceID:{required:"Please Select Service"},
			 Subject:{required:"Please Enter Subject",accept:"Please Enter Subject",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
			 I_CategoryID:{required:"Please Select Category"},
			 I_SubCategoryID:{required:"Please Select Sub Category"},
			 V_Description:{required:"Please Enter Description",accept:"Please Enter Characters only for Description",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
			 I_SRStatusID:{required:"Please Select Status"},
			 AssignedTo:{required:"Please Enter Assigned To",accept:"Please Enter Characters only for Assigned To",minlength:"Minimum 8 characters long!",maxlength:"Maximum 200 characters long!"},
        },
		        
        submitHandler: function(form) {
            form.submit();
        }
});



/*$.validator.addMethod("custom_number", function(value, element) {
    return this.optional(element) || value === "NA" ||
        value.match(/^[0-9,\+-]+$/);
}, "Please enter a valid number, or 'NA'");*/


//
 ///Add Edit Customer Page
$("#customerself").validate({
      // Specify the validation rules
	  
        rules: {  
			I_RegionID: {required: false},
			fname: {required:true,minlength:2,maxlength:64, regex:"^[a-zA-Z0-9 .']+$" },
			// fname: {required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64, regex:"^[a-zA-Z0-9]+$"},
			lname:{required:false, minlength:2,maxlength:64, regex:"^[a-zA-Z0-9 .']+$"},
			//phone: {required:true,accept:"[0-9]+",minlength:10,maxlength:10},
			phone:{required:true, digits:true, minlength:10,maxlength:10},
			phone2: {required:false,accept:"[0-9]+",minlength:3,maxlength:15},
			altphone: {required:false,accept:"[0-9]+",minlength:3,maxlength:15},
			email:{required:false,email:true,maxlength:255},
			bzone:{required:true},
			barea:{required:true},
			//bhouseno: {required:true,accept:"[a-zA-Z0-9 .-/]+",minlength:2,maxlength:8},
			bhouseno: {required:true,minlength:2,maxlength:11, regex:"^[a-zA-Z0-9 /._-]+$"},
			//bhousetype: {required:true},
			bstreet:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:5,maxlength:100},
			bcity:{required:false,accept:"[a-zA-Z ]+",minlength:2,maxlength:40},
			bstate: {required:false,accept:"[a-zA-Z ]+",minlength:2,maxlength:40},
			bpin: {required:false,accept:"[0-9]+",minlength:6,maxlength:6},
			// fileup:{required:false,accept:'jpg|JPG|Jpg|gif|GIF|Gif|png|PNG|Png'},
        },
        
        // Specify the validation error messages
        messages: {  		  
			I_RegionID:{required:"Please Select Region"},
			fname:{required:"Please Enter First Name",accept:"Please Enter Characters only for First Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
			lname:{required:"Please Enter Last Name",accept:"Please Enter Characters only for Last Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
			phone:{required:"Please Enter Mobile No",digits:"Please Enter Mobile No in Digits",minlength:"Minimum 10 Digits long!",maxlength:"Maximum 10 Digits long!"},
			phone2:{required:"Please Enter Landline No",accept:"Please Enter Landline No",minlength:"Minimum 3 characters long!",maxlength:"Maximum 15 characters long!"},
			altphone:{required:"Please Enter Alternate Phone No",accept:"Please Enter Alternate Phone No",minlength:"Minimum 3 characters long!",maxlength:"Maximum 15 characters long!"},
			email:{required:"Please Enter Email",email:"Enter Valid Email",maxlength:"Max length 255 Characters"},
			bzone:{required:"Please Enter Zone"},
			barea:{required:"Please Select Area "},
			bhouseno:{required:"Please Enter House No",accept:"Please Enter Valid House Number",minlength:"Minimum 2 characters long!",maxlength:"Maximum 11 characters long!"},
			//bhousetype:{required:"Please Enter House Type"},
			bstreet:{required:"Please Enter Street Address",accept:"Please Enter Street Address",minlength:"Minimum 5 characters long!",maxlength:"Maximum 100 characters long!"},
			bcity:{required:"Please Enter City",accept:"Please Enter Characters only for City",minlength:"Minimum 2 characters long!",maxlength:"Maximum 40 characters long!"},
			bstate:{required:"Please Enter State",accept:"Please Enter Characters only for State",minlength:"Minimum 2 characters long!",maxlength:"Maximum 40 characters long!"},
			bpin:{required:"Please Enter Pincode",accept:"Please Enter number only for Pincode",minlength:"Minimum 6 number long!",maxlength:"Maximum 6 number long!"},
			// fileup:{required:"Please select file",accept:"Please Enter csv file only"},
        },
		  
        submitHandler: function(form) {
            form.submit();
        }
});

 ///Add Edit Customer Page
$("#customer").validate({
      // Specify the validation rules
	  
        rules: {  
 			  I_RegionID: {required: false},
			  fname: {required:true,minlength:2,maxlength:64, regex:"^[a-zA-Z0-9 .']+$" },
			 // fname: {required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:64, regex:"^[a-zA-Z0-9]+$"},
			  lname:{required:false, minlength:2,maxlength:64, regex:"^[a-zA-Z0-9 .']+$"},
			  //phone: {required:false,accept:"[0-9]+",minlength:3,maxlength:15},
			  phone:{required:true, digits:true, minlength:10,maxlength:10},
			  phone2: {required:false,accept:"[0-9]+",minlength:3,maxlength:15},
			  altphone: {required:false,accept:"[0-9]+",minlength:3,maxlength:15},
			  email:{required:false,email:true,maxlength:255},
			  bzone:{required:true},
			  barea:{required:true},
			  //bhouseno: {required:true,accept:"[a-zA-Z0-9 .-/]+",minlength:2,maxlength:8},
			  bhouseno: {required:true,minlength:2,maxlength:11, regex:"^[a-zA-Z0-9 /._-]+$"},
			  //bhousetype: {required:true},
			  bstreet:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:5,maxlength:100},
			  bcity:{required:false,accept:"[a-zA-Z ]+",minlength:2,maxlength:40},
			  bstate: {required:false,accept:"[a-zA-Z ]+",minlength:2,maxlength:40},
			  bpin: {required:false,accept:"[0-9]+",minlength:6,maxlength:6},
			 // fileup:{required:false,accept:'jpg|JPG|Jpg|gif|GIF|Gif|png|PNG|Png'},
        },
        
        // Specify the validation error messages
        messages: {  		  
			 I_RegionID:{required:"Please Select Region"},
			 fname:{required:"Please Enter First Name",accept:"Please Enter Characters only for First Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
			 lname:{required:"Please Enter Last Name",accept:"Please Enter Characters only for Last Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
			 //phone:{required:"Please Enter Phone",accept:"Please Enter Phone",minlength:"Minimum 3 characters long!",maxlength:"Maximum 15 characters long!"},
			 phone:{required:"Please Enter Mobile No",digits:"Please Enter Mobile No in Digits",minlength:"Minimum 10 Digits long!",maxlength:"Maximum 10 Digits long!"},
			 phone2:{required:"Please Enter Landline No",accept:"Please Enter Landline No",minlength:"Minimum 3 characters long!",maxlength:"Maximum 15 characters long!"},
			 altphone:{required:"Please Enter Alternate Phone No",accept:"Please Enter Alternate Phone No",minlength:"Minimum 3 characters long!",maxlength:"Maximum 15 characters long!"},
			 email:{required:"Please Enter Email",email:"Enter Valid Email",maxlength:"Max length 255 Characters"},
			 bzone:{required:"Please Enter Zone"},
			 barea:{required:"Please Select Area "},
			 bhouseno:{required:"Please Enter House No",accept:"Please Enter Valid House Number",minlength:"Minimum 2 characters long!",maxlength:"Maximum 11 characters long!"},
			 //bhousetype:{required:"Please Enter House Type"},
			 bstreet:{required:"Please Enter Street Address",accept:"Please Enter Street Address",minlength:"Minimum 5 characters long!",maxlength:"Maximum 100 characters long!"},
			 bcity:{required:"Please Enter City",accept:"Please Enter Characters only for City",minlength:"Minimum 2 characters long!",maxlength:"Maximum 40 characters long!"},
			 bstate:{required:"Please Enter State",accept:"Please Enter Characters only for State",minlength:"Minimum 2 characters long!",maxlength:"Maximum 40 characters long!"},
			 bpin:{required:"Please Enter Pincode",accept:"Please Enter number only for Pincode",minlength:"Minimum 6 number long!",maxlength:"Maximum 6 number long!"},
			// fileup:{required:"Please select file",accept:"Please Enter csv file only"},
        },
		  
        submitHandler: function(form) {
            form.submit();
        }
});


///Add Edit view customer self Page
$("#frmviewcustomerself").validate({
      // Specify the validation rules
        rules: {  
				//I_RegionID: {required: false},
				fname: {required:true,minlength:2,maxlength:64, regex:"^[a-zA-Z0-9 .']+$"},
				lname:{required:false,minlength:2,maxlength:64, regex:"^[a-zA-Z0-9 .']+$"},
				phone:{required:true, digits:true, minlength:10,maxlength:10},
				//phone: {required:false,accept:"[0-9]+",minlength:3,maxlength:15},
				// phone2: {required:false,accept:"[0-9]+",minlength:3,maxlength:15},
				//  altphone: {required:false,accept:"[0-9]+",minlength:3,maxlength:15},
				email:{required:false,email:true,maxlength:255},
				bzone:{required:true},
				barea:{required:true},
				//bhouseno: {required:true,accept:"[0-9]+",minlength:2,maxlength:8},
				bhouseno: {required:true,minlength:2,maxlength:11, regex:"^[a-zA-Z0-9 /._-]+$"},
				//bhousetype: {required:true},
				// fileup:{required:false,accept:'jpg|JPG|Jpg|gif|GIF|Gif|png|PNG|Png'},
        },
        
        // Specify the validation error messages
        messages: {  		  
				// I_RegionID:{required:"Please Select Region"},
				fname:{required:"Please Enter First Name",accept:"Please Enter Characters only for First Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				lname:{required:"Please Enter Last Name",accept:"Please Enter Characters only for Last Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				//phone:{required:"Please Enter Phone",accept:"Please Enter Phone",minlength:"Minimum 3 characters long!",maxlength:"Maximum 15 characters long!"},
				phone:{required:"Please Enter Mobile No",digits:"Please Enter Mobile No in Digits",minlength:"Minimum 10 Digits long!",maxlength:"Maximum 10 Digits long!"},
				// phone2:{required:"Please Enter Landline No",accept:"Please Enter Landline No",minlength:"Minimum 3 characters long!",maxlength:"Maximum 15 characters long!"},
				// altphone:{required:"Please Enter Alternate Phone No",accept:"Please Enter Alternate Phone No",minlength:"Minimum 3 characters long!",maxlength:"Maximum 15 characters long!"},
				email:{required:"Please Enter Email",email:"Enter Valid Email",maxlength:"Max length 255 Characters"},
				bzone:{required:"Please Enter Zone"},
				barea:{required:"Please Select Area "},
				bhouseno:{required:"Please Enter House No",accept:"Please Enter Valid House Number",minlength:"Minimum 2 characters long!",maxlength:"Maximum 11 characters long!"},
				//bhousetype:{required:"Please Enter House Type"},
				// fileup:{required:"Please select file",accept:"Please Enter csv file only"},
        },
		  
        submitHandler: function(form) {
            form.submit();
        }
});

//
///Add Edit Customer Page
$("#newinteraction").validate({ 
      // Specify the validation rules
        rules: {  
 			//  INTCustomerID: {required: true},
			//datefrom: {required: true},
			// Field Name is used for validation
			dateto: {required: true}, 
			I_SRINTtypeID: {required: true},
			subject:{required:true,accept:"[a-zA-Z ]+",minlength:8,maxlength:200},
			description:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:400},
			I_INTStatusID:{required: true}
        },
        
        // Specify the validation error messages
        messages: {  		  
			// INTCustomerID:{required:"Please Select Customer"},
			 //datefrom:{required:"Please Select Date"},
			 dateto:{required:"Please Select Date"},
			 I_SRINTtypeID:{required:"Please Select Interaction Type"},
			 subject:{required:"Please Enter Subject",accept:"Please Enter Characters only for Subject",minlength:"Minimum 8 characters long!",maxlength:"Maximum 200 characters long!"},
			 description:{required:"Please Enter Description",accept:"Please Enter Characters only for Description",minlength:"Minimum 2 characters long!",maxlength:"Maximum 400 characters long!"},
			 I_INTStatusID:{required:"Please Select Status"}
        },
		  
        submitHandler: function(form) {
            insertinteraction();
			//form.submit();
        }
});

////Add Service Request Page
$("#frmservicerequest").validate({ 
      // Specify the validation rules
        rules: {  
 			//  INTCustomerID: {required: true},
			VCustomerNAME: {required: true},
			//I_SRtypeID: {required: true},
			I_ServiceID: {required: true},
			I_CategoryID: {required: true},
			I_SubCategoryID: {required: true},
			I_DispositionID: {required: false},
			I_PriorityID: {required: false},
			Subject:{required:true,accept:"[a-zA-Z ]+",minlength:8,maxlength:200},
			V_Description:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:400},
			I_SRStatusID:{required: true},
			AssignedTo:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:8,maxlength:200},
        },
        
        // Specify the validation error messages
        messages: {  		  
			// INTCustomerID:{required:"Please Select Customer"},
			 VCustomerNAME:{required:"Please Find and Select Customer"},
			 //I_SRtypeID:{required:"Please Select Service Request Type"},
			 I_ServiceID:{required:"Please Select Service"},
			 I_CategoryID:{required:"Please Select SR Category"},
			 I_SubCategoryID:{required:"Please Select SR Sub Category"},
			 I_DispositionID:{required:"Please Select Disposition form validation"},
			 I_PriorityID:{required:"Please Select Priority"},
			 Subject:{required:"Please Enter Subject",accept:"Please Enter Characters only for Subject",minlength:"Minimum 8 characters long!",maxlength:"Maximum 200 characters long!"},
			 V_Description:{required:"Please Enter Description",accept:"Please Enter Characters only for Description",minlength:"Minimum 2 characters long!",maxlength:"Maximum 400 characters long!"},
			 I_SRStatusID:{required:"Please Select Status"},
			 AssignedTo:{required:"Please Enter Assigned To",accept:"Please Enter Characters only for Assigned To",minlength:"Minimum 8 characters long!",maxlength:"Maximum 200 characters long!"},
        },
		  
        submitHandler: function(form) {
            insertinteraction();
			//form.submit();
        }
});


////Add Ticket Page
$("#frmticket").validate({ 
      // Specify the validation rules
        rules: {  
 			//  INTCustomerID: {required: true},
			VCustomerNAME: {required: true},
			//I_SRtypeID: {required: true},
			I_ServiceID: {required: true},
			I_CategoryID: {required: true},
			I_SubCategoryID: {required: true},
			I_PriorityID: {required: false},
			startdatetime: {required: true},
			enddatetime: {required: true},
			//Subject:{required:true,accept:"[a-zA-Z ]+",minlength:8,maxlength:200},
			//V_Description:{required:true,minlength:2,maxlength:400},
			I_ProStatusID:{required: true},
			timeslot: {required: false},
        },
        
        // Specify the validation error messages
        messages: {  		  
			// INTCustomerID:{required:"Please Select Customer"},
			 VCustomerNAME:{required:"Please Find and Select Customer"},
			 //I_SRtypeID:{required:"Please Select Service Request Type"},
			 I_ServiceID:{required:"Please Select Service"},
			 I_CategoryID:{required:"Please Select SR Category"},
			 I_SubCategoryID:{required:"Please Select SR Sub Category"},
			 I_PriorityID:{required:"Please Select Priority"},
			 startdatetime:{required:"Please Select Ticket Reported on Date & Time"},
			 enddatetime:{required:"Please Select Ticket Due By on Date & Time"},
		//	 Subject:{required:"Please Enter Subject",accept:"Please Enter Characters only for Subject",minlength:"Minimum 8 characters long!",maxlength:"Maximum 200 characters long!"},
			// V_Description:{required:"Please Enter Description",minlength:"Minimum 2 characters long!",maxlength:"Maximum 400 characters long!"},
			 I_ProStatusID:{required:"Please Select Status"},
			 timeslot:{required:"Please Select timeslot"},
        },
		  
        submitHandler: function(form) {
            insertinteraction();
			//form.submit();
        }
});


////Add Ticket From Guest Login Page
$("#frmguestticket").validate({ 
      // Specify the validation rules
        rules: {  
 			//  INTCustomerID: {required: true},
			VCustomerNAME: {required: true},
			//I_SRtypeID: {required: true},
			I_ServiceID: {required: true},
			I_CategoryID: {required: true},
			I_SubCategoryID: {required: true},
			I_PriorityID: {required: false},
			startdatetime: {required: true},
			enddatetime: {required: true},
			Subject:{required:true,accept:"[a-zA-Z ]+",minlength:8,maxlength:200},
			V_Description:{required:true,minlength:2,maxlength:400},
			I_ProStatusID:{required: true},
			timeslot: {required: false},
			contactperson:{required:true,accept:"[a-zA-Z ]+",minlength:2,maxlength:50},
			contactdetail:{required:true,accept:"[a-zA-Z0-9 ]+",minlength:8,maxlength:100},
        },
        
        // Specify the validation error messages
        messages: {  		  
			// INTCustomerID:{required:"Please Select Customer"},
			 VCustomerNAME:{required:"Please Find and Select Customer"},
			 //I_SRtypeID:{required:"Please Select Service Request Type"},
			 I_ServiceID:{required:"Please Select Service"},
			 I_CategoryID:{required:"Please Select SR Category"},
			 I_SubCategoryID:{required:"Please Select SR Sub Category"},
			 I_PriorityID:{required:"Please Select Priority"},
			 startdatetime:{required:"Please Select Ticket Reported on Date & Time"},
			 enddatetime:{required:"Please Select Ticket Due By on Date & Time"},
			 Subject:{required:"Please Enter Subject",accept:"Please Enter Characters only for Subject",minlength:"Minimum 8 characters long!",maxlength:"Maximum 200 characters long!"},
			 V_Description:{required:"Please Enter Description",minlength:"Minimum 2 characters long!",maxlength:"Maximum 400 characters long!"},
			 I_ProStatusID:{required:"Please Select Status"},
			 timeslot:{required:"Please Select timeslot"},
			 contactperson:{required:"Please Enter Your Name",accept:"Please Enter Characters only for Your Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 50 characters long!"},
			 contactdetail:{required:"Please Enter Contact Detail",accept:"Please Enter AlfaNumeric Characters only for Contact Detail",minlength:"Minimum 8 characters long!",maxlength:"Maximum 100 characters long!"},
        },
		  
        submitHandler: function(form) {
            insertinteraction();
			//form.submit();
        }
});


////Add Ticket Page
$("#frmticket2").validate({ 
      // Specify the validation rules
        rules: {  
 			comment:{required:true,minlength:10,maxlength:400},
        },
        
        // Specify the validation error messages
        messages: {  		  
			 comment:{required:"Please Enter Problem Resolution",minlength:"Minimum 10 characters long!",maxlength:"Maximum 400 characters long!"},
        },
		  
        submitHandler: function(form) {
            
			var answer = confirm('Are you sure you want to Withdraw this ticket?');
			if (answer)
			{
			  //console.log('yes');
			  form.submit();
			}
			else
			{
			  //console.log('cancel');
			}
			
        }
});

////master  upload customer page
$("#frmuploadcustomer").validate({
    
        // Specify the validation rules
        rules: {   
			  userfile:{required:true,accept:'csv|Csv|CSV'},
        },
        
        // Specify the validation error messages
        messages: {
				  userfile:{required:"Please select file",accept:"Please Enter csv file only"},
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});

////master  upload sla page
$("#frmuploadsla").validate({
    
        // Specify the validation rules
        rules: {   
			  csvfile:{required:true,accept:'csv|Csv|CSV'},
        },
        
        // Specify the validation error messages
        messages: {
				  csvfile:{required:"Please select file",accept:"Please Enter csv file only"},
        },
        
        submitHandler: function(form) {
            form.submit();
        }
});

///master_user_type_service page
$("#master_settings").validate({
    //alert("a");
        // Specify the validation rules
        rules: {  
			  Dsn:{required:true,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:20},
			  /*V_ServiceID:{required:true,accept:"[a-zA-Z0-9_]+",minlength:2,maxlength:64},
			  V_Custom1:{required:true,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64},
			  V_Custom2:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64},
			  V_Custom3:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64},
			  V_Custom4:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64},
			  V_Custom5:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64},
			 // V_Custom6:{required:false,accept:"[a-zA-Z0-9 ]+",minlength:2,maxlength:64}
			  V_Custom6:{required:true,accept:'jpg|JPG|Jpg|gif|GIF|Gif|png|PNG|Png'},*/
        },
        // Specify the validation error messages
        messages: {
				  V_ServiceName:{required:"Please Enter Dsn Name",accept:"Please Enter Dsn Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 20 characters long!"},
				 /* V_ServiceID:{required:"Please Enter Service Databse Name",accept:"Please Enter Service Name",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Custom1:{required:"Please Enter Custom Field Value",accept:"Please Enter Custom Field Value",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Custom2:{required:"Please Enter Custom Field Value",accept:"Please Enter Custom Field Value",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Custom3:{required:"Please Enter Custom Field Value",accept:"Please Enter Custom Field Value",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Custom4:{required:"Please Enter Custom Field Value",accept:"Please Enter Custom Field Value",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Custom5:{required:"Please Enter Custom Field Value",accept:"Please Enter Custom Field Value",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"},
				  V_Custom6:{required:"Please select file",accept:"Please Enter Jpg/Gif/Png file only"},
				  //V_Custom6:{required:"Please Enter Custom Field Value",accept:"Please Enter Custom Field Value",minlength:"Minimum 2 characters long!",maxlength:"Maximum 64 characters long!"}
        */
		},
		
        submitHandler: function(form) {
            form.submit();
        }
});

});	

	
