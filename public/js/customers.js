function getVillage(district,village)
{
	$.ajax({
	      url: 'ajaxGetVillage.php',
	      type: 'post',
	      data: {'district': district , 'village': village},
	      success: function(data, status) {
	      	//alert(data);
			$("#v_VillageC").html(data);
	      },
	      error: function(xhr, desc, err) {
	        console.log(xhr);
	        console.log("Details: " + desc + "\nError:" + err);
	      }
	    }); // end ajax call
}

function filterregion(regionval)
{
	document.frmcustomer.submit();	
}

function filterzone(zoneval)
{
	document.frmcustomer.submit();	
}

function Add_Click()
{	
	with(document.frmcustomer)
	{
		//I_SubSubCategoryID.value = ISubCategoryID;
		Action.value = 'Add';
		submit();
	}
}


function Edit_Click(ICustomerID)
{	
	with(document.frmcustomer)
	{
		CustomerID.value = ICustomerID;
		Action.value = 'Edit';
		submit();
	}
}




//====================================================================================================

//	Function Name	:	Delete_Click()

//----------------------------------------------------------------------------------------------------

function Delete_Click(ICustomerID)
{
//alert(ISubCategoryID);
	with(document.frmcustomer)
	{

		if(confirm('Are you sure you want to delete Customer?'))
		{
			CustomerID.value	= ICustomerID;
			Action.value		= 'Delete';
			submit();
		}
	}
}



//====================================================================================================

//	Function Name	:	DeleteChecked_Click()

//----------------------------------------------------------------------------------------------------

function DeleteChecked_Click()
{
	with(document.frmcustomer)
	{
		var flg=false;

		if(document.all['srcat_prod[]'].length)
		{
			for(i=0; i < document.all['srcat_prod[]'].length; i++)
			{
				if(document.all['srcat_prod[]'][i].checked)
				flg = true;
			}
		}
		else
		{
			if(document.all['srcat_prod[]'].checked)
			flg = true;
		}


		if(!flg)
		{
			alert('Please select the record you want to delete.');
			return false;
		}
			

		if(confirm('Are you sure you want to delete selected Customer ?'))
		{
			Action.value 	= 'DeleteSelected';
			submit();
		}

	}

}

function Show_Service_div(cusid,serviceid)
{
		//alert(cusid,serviceid);
		//alert(serviceid);
		var divnanme= "PowerSewageWater"+cusid;
		//alert(divnanme);
		//servicehide 
		//$( ".servicehide" ).hide();
		
	if (window.XMLHttpRequest)
	  {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	  }
	else
	  {	// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }

	xmlhttp.onreadystatechange=function()
	  { 
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			document.getElementById(divnanme).innerHTML=xmlhttp.responseText;
			$("#divnanme").show();
			$("#divnanme").addClass('active');
			
		}
	  }

	xmlhttp.open("GET","customer_service_ajax.php?cusid="+cusid+"&serviceid="+serviceid,true);
	xmlhttp.send();	
}

/*
function showArea(str)
{
	
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
		document.getElementById("divarea").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","area_ajax.php?q="+str,true);
xmlhttp.send();
}

function getCity(state,city)
{
	$.ajax({
	      url: 'ajaxGetCity.php',
	      type: 'post',
	      data: {'state': state , 'city': city},
	      success: function(data, status) {
	      	//alert(data);
			$("#district").html(data);
	      },
	      error: function(xhr, desc, err) {
	        console.log(xhr);
	        console.log("Details: " + desc + "\nError:" + err);
	      }
	    }); // end ajax call
}

function showHistory(id)
{
	//alert(id);
	$.ajax({
	      url: 'web_history.php',
	      type: 'post',
	      data: {'id': id},
	      success: function(data, status) {
		  		//$("#history").css("display","block");
		  		//alert(data);
				$("#history").html(data);
	      },
	      error: function(xhr, desc, err) {
	        console.log(xhr);
	        console.log("Details: " + desc + "\nError:" + err);
	      }
	    }); // end ajax call
}*/


function validate_signup() {

    try {
        console.log('validate_signup');
        var phone = $('#phone').val();
        if (phone.trim() == "") {
            alert("Please Enter Register Number!");
            $('#phone').focus();
            return false;
        }

        var first_name = $('#first_name').val();
        if (first_name.trim() == "") {
            alert("Please Enter First Name!");
            $('#first_name').focus();
            return false;
        }
        var last_name = $('#last_name').val();
        if (last_name.trim() == "") {
            alert("Please Enter Last Name!");
            $('#last_name').focus();
            return false;
        }

        var gender = $('#gender').val(); //gender
        console.log('gender ' + gender);
        if (gender.trim() == "0") {
            alert("Please Select Gender!");
            $('#gender').focus();
            return false;
        }
        var age = $('#age').val(); //age
        console.log('age ' + age);
        if (age.trim() == "0") {
            alert("Please Select Age Group!");
            $('#age').focus();
            return false;
        }

        var priority = $("[name='priority']:checked").val();
        console.log('typeVal');
        console.log(priority);

        if (priority == undefined || priority=='') {
            alert("Please Select Priority!");
            return false;
        }

        return true;


    } catch (err) {
        console.log("ERROR >> " + err);
    }
}



