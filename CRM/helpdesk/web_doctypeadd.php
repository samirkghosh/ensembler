<?php
/***
 * Document Upload
 * Author: Aarti
 * Date: 04-04-2024
 *  This code is used in a web application to Document Upload 
-->
 **/
include("../../config/web_mysqlconnect.php"); //  Connection to database // Please do not remove this
// fetch user details
include("../web_function.php");
// include("function_doc.php");
header("Cache-Control:Private");
ini_set('max_execution_time','600');
ini_set('max_input_time','600');
ini_set('memory_limit','1024M');
ini_set('post_max_size','500M');
ini_set('upload_max_filesize','500M');
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title> Document Upload</title>
<link rel="stylesheet" type="text/css" href="<?=$SiteURL?>/public/css/styles.css" />
<script type="text/javascript" src="<?=$SiteURL?>/public/js/jquery-1.10.2.min.js"></script>
</head>
<script language="javascript">
function LTrim(str) {
for (var i=0; ((str.charAt(i)<=" ")&&(str.charAt(i)!="")); i++);
return str.substring(i,str.length);
}

function RTrim(str) {
for (var i=str.length-1; ((str.charAt(i)<=" ")&&(str.charAt(i)!="")); i--);
return str.substring(0,i+1);
}

function Trim(str) {
return LTrim(RTrim(str));
}

function docnamechange()
{
	var newval=Trim(document.quickcreatenc.V_Doc_Name.value);
	document.quickcreatenc.V_Doc_Name.value=newval;
}

function docdescchange()
{
	var newval=Trim(document.quickcreatenc.V_DOC_Description.value);
	document.quickcreatenc.V_DOC_Description.value=newval;
}



function onkeyPress(e)
{
var key = window.event ? e.keyCode : e.which;
if (key == 13)
StartClick();
e.cancelBubble = true;
e.returnValue = false;
return false;
}

function textCounter(field,cntfield,maxlimit) {
if (field.value.length > maxlimit) // if too long...trim it!
field.value = field.value.substring(0, maxlimit);
// otherwise, update 'characters left' counter
else
cntfield.value = maxlimit - field.value.length;
}
</script>
<body >
<form  method="post" enctype="multipart/form-data" name="quickcreatenc" id="quickcreatenc" >
	<!-- <input type="hidden" name="MAX_FILE_SIZE" value="10000000000"/> -->

<table width="100%" border="0"  align="left" cellpadding="0" cellspacing="0" class="main-form tableview tableview-2">
<tr class="background">
    <td colspan="2">Add New Document </td>
  </tr>


   <? if(!empty($msg)){?>
  <tr id="display-success">
    <td height="20" colspan="2" align="center" style="text-align:center; text-transform:capitalize;">
       <?=$msg?>
       
       </td>
  </tr>
  <? }?>
  <tr><td colspan="2"><!-- Status message -->
<div class="statusMsg"></div>
</td></tr>
  
	<tr >
	  <td colspan="2"><b>Document Information:</b></td>
	</tr>
 
    <tr>
  <td>Document Name <em>*</em>	</td>
	<td><input type="text" name="V_Doc_Name" id="V_Doc_Name" class="input-style1" size="23" />
    <input type="hidden" name="I_DocumentType" value="<?=$_REQUEST['doctype']?>" />
<input type="hidden" name="opportunityid" value="<?=$_REQUEST['opportunityid']?>" /></td>

</tr>
 
<tr>
  <td>Document Description</font>&nbsp;&nbsp;</b></td>
	<td><textarea name="V_DOC_Description" id="V_DOC_Description" class="text-area1" rows="5" cols="23" onBlur="docdescchange()" onKeyDown="textCounter(document.quickcreatenc.V_DOC_Description,document.quickcreatenc.remLen1,255)" onKeyUp="textCounter(document.quickcreatenc.V_DOC_Description,document.quickcreatenc.remLen1,255)"></textarea><br />
<div style="float:left; width:100%; padding-top:5px;">
<input readonly type="text" name="remLen1" size="3" maxlength="3" value="255" class="input-style1" style="width:50px; float:left;" />
<p style="padding:5px 0 0 5px; display:inline-block;">characters left</p>
</div>
</td>
</tr>
 
<tr>
  <td>	
    Upload Document  <em>*</em>	</td>
	<td>
		<!-- <input name="V_Path" type="file" class="input-style1" size="23" onKeyPress="return onkeyPress(event);" /> -->

		<input  type="file" class="input-style1" id="file" name="files[]" multiple />
            <p style="padding:7px 0 0 5px; display:inline-block; color: red;">(Please upload documents up to 5MB only)</p>
</tr>
     
  <td>	
    Document</font>&nbsp;&nbsp;</b></td>
	
	<td>
	<input type="checkbox" name="I_PP" value="check" /> Private 	</td>
   </tr>
 
 </table>
 <div class="button-all2" style="clear:both;">

	<!-- <input type="button" onClick="return validation_upload();" value="Upload"  class="button-orange1" style="float:inherit;">  -->
	  <input type="submit" name="submit"  class="button-orange1" style="float:inherit;" value="SUBMIT"/>
    </div>
</form>
<script>
$(document).ready(function(){
	$('#display-msg').hide(); 
    // Submit form data via Ajax
    $("#quickcreatenc").on('submit', function(e){
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'upload_document.php',
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData:false,
            beforeSend: function(){
                $('.submitBtn').attr("disabled","disabled");
                $('#quickcreatenc').css("opacity",".5");
            },
            success: function(response){
                $('.statusMsg').html('');
                if(response.status == 1){
                    $('#quickcreatenc')[0].reset();
                    $('.statusMsg').html('<p class="alert alert-success">'+response.message+'</p>');
                     //window.parent.location.reload();
					 setInterval ( "refreshParent()", 2000 );
                }else{
                    $('.statusMsg').html('<p class="alert alert-danger">'+response.message+'</p>');
                }
                $('#quickcreatenc').css("opacity","");
                $(".submitBtn").removeAttr("disabled");
            }
        });
    });

});
function refreshParent(){
 window.parent.location.reload();
}
</script>
<script>
    document.getElementById('file').addEventListener('change', function() {
        const files = this.files;
        const maxSize = 5 * 1024 * 1024; // 5 MB in bytes
        let isTooLarge = false;

        for (let i = 0; i < files.length; i++) {
            if (files[i].size > maxSize) {
                isTooLarge = true;
                break; // Break the loop once a file exceeds the limit
            }
        }

        if (isTooLarge) {
            alert('One or more files exceed the 5MB limit. Please upload smaller files.');
            this.value = ''; // Clear the file input if the limit is exceeded
        }
    });
</script>
</body>
</html>
