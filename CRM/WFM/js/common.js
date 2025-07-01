function minusCounter(counter,txtarea,limit)
{
	var cnt = eval($("#"+counter).val());
	var txtareachar = $("#"+txtarea).val();
	var txtlength = txtareachar.length;
	var fcnt = limit-txtlength;
	$("#"+counter).val(fcnt);
	if(fcnt<=0){ $("#"+txtarea).val(txtareachar.substring(0,500)); $("#"+counter).val(0); }
}

function printdiv(printpage)
{
var headstr = "<html><head><title></title></head><body>";
var footstr = "</body>";
var newstr = $("."+printpage).html();//document.all.item(printpage).innerHTML;
var oldstr = document.body.innerHTML;
document.body.innerHTML = headstr+newstr+footstr;
window.print();
document.body.innerHTML = oldstr;
return false;
}

function CheckUncheck_Click(fld, status)

{

	if(fld.length)

		for(i=0; i < fld.length; i++)

			fld[i].checked = status;

	else

		fld.checked = status;

}



function callback(val,e,nval,frmname,url)
{

	//var keyc = (document.all) ? e.keyCode : e.which; 
	
	var keycode;
    if (window.event)
        keyc = window.event.keyCode;
    else if (e)
        keyc = e.which;
	
	if(keyc==13 || keyc=='13')
	{ 	
		var n=eval(val);
		var nval=(n-1)*nval;
		var url1 = url+"?&start="+nval;
		if( confirm("Want to jump on page "+n+" ?") )
		{
		document.forms[frmname].action=url1;
		document.forms[frmname].submit();
		}
		else
		{
			return false;
		}
		//return true;
	}
	//return false;
}


function dosubmitback()
{
	window.history.back();
}


function showallcallsondashagentboard(ID,AtxUserID)
{
	var fromdate= '';
	var todate	= '';
	
	
		
	if(ID=='5')
	{
		fromdate=	document.getElementById("datefrom").value;
		todate	=	document.getElementById("dateto").value;
	}
	
	if(fromdate == 'From'){  fromdate=''; }
	if(todate == 'To'){  todate=''; }
	//alert(todate);
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
		var result = xmlhttp.responseText;
		tmp=result.split("@");
		document.getElementById("incalls").innerHTML=tmp[0];
		document.getElementById("outcalls").innerHTML=tmp[1];
		
			if((fromdate != '') && (todate!= '')){
			document.getElementById("dt_range_dis").innerHTML="From "+fromdate +" TO "+todate;
			document.getElementById("dt_range_dis").style.display="block";
			}else{ document.getElementById("dt_range_dis").innerHTML=""; document.getElementById("dt_range_dis").style.display="none"; }
		
		
		
    }
  }

xmlhttp.open("GET","calls_ajax.php?ID="+ID+"&AtxUserID="+AtxUserID+"&fromdate="+fromdate+"&todate="+todate,true);
xmlhttp.send();
}

var cnt=0;
function logoutcall(url)
{
	cnt=parseInt(cnt)+parseInt(1);
	if(cnt=='1')
	{
		window.location.href=url;
	}
}

/* phone and email validation */

function validatePhone(txtPhone) {
    var a = document.getElementById(txtPhone).value;
    var filter = /^[0-9-+]+$/;
    if (filter.test(a) && a.length==10) {
        return true;
    }
    else {
        return false;
    }
}

function validateEmail(sEmail) {
	var a = document.getElementById(sEmail).value;
    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
    if (filter.test(a)) {
        return true;
    }
    else {
        return false;
    }
}