jQuery(function ($){
	var Common = {
		init: function (){
			jQuery("body").on('click','.Update_bulletin',this.handleSubmit);
            jQuery("body").on('click','.delete_bulletin',this.handleDelete);
            jQuery("body").on('click','.submit_form',this.handleFeedbackform);
            jQuery("body").on('click','.Nps_submit_form',this.handleNpsSubmit);
            jQuery("body").on('click','.submit_form_effort',this.handleCESSubmit);
		},
		handleSubmit: function(){
            $("#registration").validate({
                ignore: [],
                rules: {
                    M_Description: {
                        required: true,
                    },
                    msg_type: {
                        required: true,
                    }
                },
                messages: {
                    M_Description: 'Please enter Description',
                    msg_type: 'Please select message type',
                },
                submitHandler: function (form) {
                    var id = jQuery('.bulletin_id').val();
                    var button = id ? jQuery(".Update_bulletin") : jQuery(".Create_bulletin");
                    button.text("Saving...");
                    button.prop('disabled', true);
        
                    var Data = {
                        id: id,
                        createdBy: jQuery('#userid').val(),
                        message: jQuery('#M_Description').val(),
                        startdatetime: jQuery('#startdatetime').val(),
                        enddatetime: jQuery('#enddatetime').val(),
                        msg_type: jQuery("#msg_type").val(),
                        action: 'addUpdateBulletin'
                    };
                    console.log(Data);
                    $.ajax({
                        method: 'POST',
                        url: "common_function.php",
                        data: Data,
                        success: function (response) {
                            button.prop('disabled', false);
                            button.text(id ? "Update" : "Create");
                            var Bulletin = btoa('Bulletin');
                            setTimeout(function () {
                                window.location.href = "admin_index.php?action=view_province&token=" + encodeURIComponent(Bulletin);
                            }, 2000);
                        },
                        error: function () {
                            button.prop('disabled', false);
                            button.text(id ? "Update" : "Create");
                            alert('An error occurred while processing.');
                        }
                    });
                }
            });
        },        
        handleDelete : function(){
            var id = $(this).data('id');
            if (confirm("Are you sure to delete?")) {
                if(id){
                    $.ajax({
                        method: 'POST',
                        url: "common_function.php",
                        data:  {id:id,action:'delete_bulletin'},
                        success: function (response) {
                            $('.table_bulletin').find("#"+id).remove();
                            var length = $('.table_bulletin').find('.row_bulletin').length;
                            if(length == '0'){
                                $('.contentred').show();
                            }
                        }
                    });
                }
            }
        },
        handleFeedbackform : function(){
            var id = $('#user_id').val();
            console.log(id);
            if(id){
                var formData = new FormData($('#feedregistration')[0]);
                // var grecaptcha = $('#g-recaptcha-response').val();
                // formData.append('grecaptcha',grecaptcha);
                // var v = grecaptcha.getResponse();
                // if(grecaptcha.length == 0){
                //     document.getElementById('captcha').innerHTML="You can't leave Captcha Code empty";
                //     return false;
                // }else{
                //     // documentsgetElementById('captcha').innerHTML="Captcha completed";
                //    // return true; 
                // }

                var rating = $(".radio_check input[type='radio']:checked");
                // Log formData content
                console.log("Form Data Content:");
                for (var pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }

                // AJAX Request
                $.ajax({
                    method: "POST",
                    url: "IMApp/function.php",
                    cache: false,
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (result) {
                        if (result == '1') {
                            alert('Feedback form is already processed');
                        } else if (result == '2') {
                            document.getElementById('captcha').innerHTML = "reCaptcha failed, please try again...";
                            alert('reCaptcha failed, please try again...');
                        } else {
                            $('.main_div').hide();
                            $('.message').show();
                        }
                    }
                });

            }else{
               alert('url is not valid') ;
            }
        },
        handleNpsSubmit : function(){
            var id = $('#user_id').val();
            if(id){
                // added code for form validation[vastvikta][17-02-2025]
                
                var formData = new FormData($('#feedregistration')[0]);
                var grecaptcha = $('#g-recaptcha-response').val();
                var rating = $(".btn-check input[type='radio']:checked");
                var rating = $(".radio_button2:checked").val(); // Check if any radio button is selected
                if (!rating) {
                    alert('Please rate before submitting.');
                    return;
                }
                $.ajax({
                    method: "POST",
                    url: "common_function.php",
                    cache: false,
                    processData: false,
                    contentType: false,
                    data:formData,
                    success:function(result){
                        if(result == '1'){
                            alert('feedback form is already processed..');
                        }else{
                            $('.main_div_nps').hide();
                            $('.message_nps').show();
                        }
                    }
                });
            }else{
               alert('url is not valid') ;
            }
        },       
        handleCESSubmit : function(){
            var id = $('#user_ids').val();
            if(id){
                // added code for form validation[vastvikta][17-02-2025]
                
                var formData = new FormData($('#customer_effort')[0]);
                var rating = $(".radio_button:checked").val(); // Check if any radio button is selected
                if (!rating) {
                    alert('Please rate before submitting.');
                    return;
                }
                $.ajax({
                    method: "POST",
                    url: "common_function.php",
                    cache: false,
                    processData: false,
                    contentType: false,
                    data:formData,
                    success:function(result){
                        if(result == '1'){
                            alert('Customer Effort form is already processed..');
                        }else{
                            $('.main_div').hide();
                            $('.message').show();
                        }
                    }
                });
            }else{
               alert('url is not valid')
            }
        }
	}
	Common.init();
});
// ******************************* Other js code old **********************
function minusCounter(counter,txtarea,limit){
    var cnt = eval($("#"+counter).val());
    var txtareachar = $("#"+txtarea).val();
    var txtlength = txtareachar.length;
    var fcnt = limit-txtlength;
    $("#"+counter).val(fcnt);
    if(fcnt<=0){ $("#"+txtarea).val(txtareachar.substring(0,500)); $("#"+counter).val(0); }
}
function printdiv(printpage){
    var headstr = "<html><head><title></title></head><body>";
    var footstr = "</body>";
    var newstr = $("."+printpage).html();//document.all.item(printpage).innerHTML;
    var oldstr = document.body.innerHTML;
    document.body.innerHTML = headstr+newstr+footstr;
    window.print();
    document.body.innerHTML = oldstr;
    return false;
}
function CheckUncheck_Click(fld, status){
    if(fld.length)

        for(i=0; i < fld.length; i++)

            fld[i].checked = status;

    else
        fld.checked = status;

}
function callback(val,e,nval,frmname,url){
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
function dosubmitback(){
    window.history.back();
}
function showallcallsondashagentboard(ID,AtxUserID){
    var fromdate= '';
    var todate  = '';
    if(ID=='5')
    {
        fromdate=   document.getElementById("datefrom").value;
        todate  =   document.getElementById("dateto").value;
    }
    
    if(fromdate == 'From'){  fromdate=''; }
    if(todate == 'To'){  todate=''; }
    //alert(todate);
    if (window.XMLHttpRequest)
      {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
      }
    else
      { // code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
      }

xmlhttp.onreadystatechange=function(){ 
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
function logoutcall(url){
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
function paginate_help(start, nval) {
 var search = "<?php echo (isset($_GET['search'])) ? $_GET['search'] : '' ?>";
 if (search != '') {
     search = '?search=' + search;
 }

 if (nval == 1) {
     start = eval(start) + 1;
     document.frmService.page.value = start;
     // document.frmService.action = "<?=$_SERVER['PHP_SELF'].'?case_status='.$case_status ?>" + search;
     document.frmService.action = "<?=$_SERVER['PHP_SELF']?>" + search;
     document.frmService.submit();

 } else if (nval == 2) {
     start = eval(start) - 1;
     document.frmService.page.value = start;
     // document.frmService.action = "<?=$_SERVER['PHP_SELF'].'?case_status='.$case_status ?>" + search;
     document.frmService.action = "<?=$_SERVER['PHP_SELF']?>" + search;
     document.frmService.submit();
 }
}

function paginate2(start, nval) {
 if (nval == 1) {
     start = eval(start) + 1;
     document.frmcustomer.page.value = start;
     document.frmcustomer.action = "<?=$_SERVER['PHP_SELF']?>";
     document.frmcustomer.submit();

 } else if (nval == 2) {
     start = eval(start) - 1;
     document.frmcustomer.page.value = start;
     document.frmcustomer.action = "<?=$_SERVER['PHP_SELF']?>";
     document.frmcustomer.submit();
 }
}

function get_status_wise_cases(status) {
if(status){
    window.location.href = 'web_helpdesk_home.php?case_status=' + status;
}else{
    window.location.href = 'web_helpdesk_home.php';
}        
}
$('.disable_menu').click(function(){
    $("#popupContainer").fadeIn();
    setTimeout(function(){ 
        $("#popupContainer").fadeOut();
    }, 2000);
});
// Close popup when close button is clicked
$("#closePopup").click(function () {
    $("#popupContainer").fadeOut();
});

