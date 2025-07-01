
/* Add More Interaction case wise store Values docked_id, customer_id, source */
function addmore_interaction(docked_id, customer_id, source, rowid) {
    if (docked_id != '') {
        $("#docket_no").val(docked_id);
        $("#rowid").val(rowid);
        $("#source_id").val(source);
        $("#customer_id").val(customer_id);
        $("#ticket_no").val(docked_id);
        $("#feed_source_id").val(source);
        $("#feed_customer_id").val(customer_id);
        $("#addInteraction_form").show();
        get_case_details();
        try {
            $.scrollTo($('#addInteraction_form'), 1000);
        } catch (err) {
            console.log('err' + err);
        }
        $("#Submit_button").prop("disabled", false);
        /*$("#Submit_button").hide();
          $("#reset_button").hide();*/
    }
}

function change_new_status(status) {
    console.log('current_status');
    console.log(status);
    $("#save_current_status").val(status);
}
/* Reset Form For Create next case in one call*/
function resetform() {
    console.log('resetform');
    $('select#group_assign').val('0').prop("disabled", false);
    $('select#v_category').val('0').prop("disabled", false);
    $('select#v_subcategory').val('0').prop("disabled", false);
    $('select#status_type_').val('1').prop("disabled", false);
    $("#v_remark_type").val('');
    $("#Submit_button").prop("disabled", false);
    $("#Submit_button").show();
    //$("[name='type']:checked").prop("disabled", false);
    $("#show_ticket").empty();
    $("#show_ticket").hide();
    $(".no_disabled").prop('disabled', false);
    $("#v_remark_type").prop('disabled', false);
    var customerid = $("#customerid_new").val();
    console.log('***Reset Form customer id:' + customerid);
    if (customerid) {
        getCustomerTicketHistory(customerid, 'customer_id');
        getCustomerTicketHistory_leftSidebar(customerid, 'customer_id');
    }
    //$("[name='type']:checked").val();
}

function resetfeedbackform() {
    console.log('resetfeedbackform ');
    //$('select#group_assign').val('0');
    $('select#v_category').val('0');
    $('select#v_subcategory').val('0');
    $('select#current_status_type_').val('0');
    $("#customer_remark").val('');
    $("#responseMessage").text('');
    $("#Submit_button").prop("disabled", false);
    $("[name='feedback']:checked").val('2');
    $("#feedback_form").hide();
    resetform();
}

function get_case_details() {
    var docket_no = $("#search-docket").val();
    console.log(' get_case_details() **** Docket No: ' + docket_no);
    $("#show_ticket").text('');
    if (docket_no != '') {
        $.ajax({
            url: 'process_ajax_request.php',
            type: 'post',
            data: {
                'docket_no': docket_no
            },
            success: function(data, status) {
                console.log('data search by docket');
                console.log(data);
                var data = JSON.parse(data);
                if (data.status == 'success') {
                    var dd = data.casedetails;
                    //alert(dd.vProjectID);
                    if (dd.vCaseType == 'complaint') {
                        $("#type1").prop("checked", true);
                        web_cat(dd.vCaseType, dd.vCategory);
                    } else {
                        $("#type2").prop("checked", true);
                        //web_cat(dd.vCaseType,dd.vCategory);
                        $('#assign_to_backofice').hide();
                    }
                    //$("input[name='type'][value='"+dd.vCaseType+"']").attr("checked", true);
                    //$('select#v_category').val(dd.vCategory);
                    //$('select#v_subcategory').val(dd.vSubCategory);
                    if (dd.vSubCategory != '' && dd.vSubCategory != '0') {
                        //web_cat(type,dd.vSubCategory);
                        web_subcat(dd.vCategory, dd.vSubCategory);
                    }
                    if (dd.vProjectID != null) {
                        $('select#group_assign').val(dd.vProjectID);
                    } else {
                        $('select#group_assign').val('0');
                    }
                    $('select#status_type_').val(dd.iCaseStatus);
                    $('#ticket_no').val(dd.ticketid);
                    $("#docket_no_new").val(dd.ticketid);
                    if (dd.iCaseStatus == '8') {
                        $("#feedback_form").show();
                        $("select#current_status_type_").val(dd.iCaseStatus);
                    }
                    $("#v_remark_type").val(dd.vRemarks);
                    getCustomerTicketHistory(dd.ticketid, 'docket_no');
                    getCustomerTicketHistory_leftSidebar(dd.ticketid, 'docket_no');
                    /*************Enter customer data*************/
                    if (dd.fname != '') {
                        var fname = (dd.fname);
                        var name = fname.split(" ");
                        console.log(fname + "First Name:" + name);
                    }
                    $("#customerid").val(dd.AccountNumber);
                    $("#first_name").val(name[0]);
                    $("#phone").val(dd.phone);
                    $("#mobile").val(dd.mobile);
                    $("#last_name").val(name[1]);
                    $("#email").val(dd.email);
                    $("#gender").val(dd.gender);
                    $("#age").val(dd.age_grp);
                    $("#address_1").val(dd.address);
                    $("#address_2").val(dd.v_Location);
                    $("#district").val(dd.district).change();
                    $("#country").val(dd.country).change();
                    $("#lang").val(dd.language).change();
                    $("#fbhandle").val(dd.fbhandle);
                    $("#twitterhandle").val(dd.twitterhandle);
                    (dd.customer_account_no == '0' ? $('#account_no_').val('NA') : $('#account_no_').val(dd.customer_account_no));
                    /*************Enter customer data*************/
                    //$("#Submit_button").prop('disabled', true);
                    $("#Submit_button").hide();
                    $("#reset_button").hide();
                    $("#customerrr :input").prop('disabled', true);
                    $(".no_disabled").prop('disabled', false);
                    $("#search-docket").prop('disabled', false);
                    $("#search_btn").prop('disabled', false);
                    $("#search_res").prop('disabled', false);
                    //$("select#v_subcategory").prop('disabled', true) ;
                    //document.getElementById("v_subcategory").disabled = true;
                    $("#type1").prop("disabled", true);
                    $("#type2").prop("disabled", true);
                } else {}
            },
            error: function(xhr, desc, err) {
                //console.log(xhr);
                //console.log("Details: " + desc + "\nError:" + err);
            },
            complete: function() {
                setTimeout(function() {
                    console.log('=====================================================================================');
                    $("#interaction_remark").val('');
                    $("#responseMessage2").text('');
                    $("select#v_category").prop("disabled", true);
                    $("select#v_subcategory").prop("disabled", true);
                }, 5000);
            }
        });
    }
    webTownShip('1');
    /*$('select#group_assign').val('0');
       $('select#v_category').val('0');
     $('select#v_subcategory').val('0');
     $('select#status_type_').val('0');
     $("#v_remark_type").val('');
     $("#Submit_button").prop("disabled", false);*/
}

function go_to_newcase() {
    $('.docket_show').css('display', 'none');
    $('#search_res').css('display', 'block');
    $("#Submit_button").show();
    $("#reset_button").show();
    //window.location.replace('new_case_manual_popup_test.php');
    //$("#show_ticket").hide();
    resetform();
    resetfeedbackform();
}

function go_to_docket_search() {
    $('.docket_show').css('display', 'block');
    $('#search_res').css('display', 'none');
    $('#show_ticket').hide();
    $('#show_ticket').text('');
    $('#search_res').val('');
    $('#search-docket').val('');
    //resetform();
    //resetfeedbackform();
}

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
        alert("Please Select Disposition");
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

function getCustomerTicketHistory_leftSidebar(val, nkey) {
    console.log('getCustomerTicketHistory_leftSidebar');
    console.log(val + nkey);
    $.ajax({
        url: 'helpdesk/web_ticket_history.php',
        type: 'post',
        data: {
            'key': val,
            'nkey': nkey,
            'left': 'leftbar'
        },
        success: function(data, status) {
            console.log('get case details 22222');
            //console.log(data);
            $("#ticket_history_leftbar").empty();
            $("#ticket_history_leftbar").html(data);
            //$("#lasttickethistoy").empty();
            //$("#lasttickethistoy").html(data);
            //$("#ticket_history_docket").hide();
        },
        error: function(xhr, desc, err) {
            //console.log(xhr);
            //console.log("Details: " + desc + "\nError:" + err);
        }
    });
}
// end


function ChkIntOnly(obj) {
    var val = obj.value
    if (isNaN(val)) {
        obj.value = '';
        for (var i = 0; i < val.length; i++) {
            if (!isNaN(obj.value + val.substring(i, i + 1))) {
                obj.value = obj.value + val.substring(i, i + 1);
            } else {
                return false;
            }
        }
    }
    return true;
}
function IsNumeric(sText) {
    var ValidChars = "0123456789.";
    var IsNumber = true;
    var Char;
    var cnt;
    cnt = 0;
    for (i = 0; i < sText.length && IsNumber == true; i++) {
        Char = sText.charAt(i);
        if (ValidChars.indexOf(Char) == -1) {
            IsNumber = false;
        }
        if (Char == '.') {
            cnt = cnt + 1;
            if (cnt > 1) {
                IsNumber = false;
                cnt = 0;
                return IsNumber;
            }
        }
    }
    return IsNumber;
}
function NumOnly(obj) {
    if (IsNumeric(obj.value) == false) {
        alert("Not a valid number");
        obj.value = 0;
        obj.focus();
        return false;
    }
}
function ValidateEmail(email) {
    if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email)) {
        return (true)
    }
    alert("You have entered an invalid email address!");
    $("#email").focus();
    return (false);
}
function validate_form_submit() {
    try {
        console.log('validate_form_submit-updated');
        var phone = $('#phone').val();
        if (phone.trim() == "") {
            alert("Please Enter Caller Number!");
            $('#phone').focus();
            return false;
        }
        var first_name = $('#first_name').val();
        if (first_name.trim() == "") {
            alert("Please Enter First Name!");
            $('#first_name').focus();
            return false;
        }
        var source = $('#source').val(); //mode
        if (source.trim() == "0") {
            alert("Please Select mode!");
            $('#source').focus();
            return false;
        }
        var typeVal = $("[name='type']:checked").val();
        if (typeVal == undefined) {
            alert("Please Select Reasons of Calling!");
            return false;
        }
        var v_category = $('#v_category').val();
        if (v_category.trim() == "0") {
            alert("Please Select Category !");
            $('#v_category').focus();
            return false;
        }
        var v_subcategory = $('#v_subcategory').val();
        if (v_subcategory.trim() == "0" || v_subcategory == "") {
            alert("Please Select Sub Category !");
            $('#v_subcategory').focus();
            return false;
        }
        if (typeVal == 'complaint') {
            var group = $('#group_assign').val();
            if (group.trim() == "0") {
                alert("Please Assign Department!");
                $('#group_assign').focus();
                return false;
            }
        }
        var status = $('#status_type_').val();
        if (status.trim() == "0") {
            alert("Please Select Status!");
            $('#status_type_').focus();
            return false;
        }
        var v_remark_type = $('#v_remark_type').val();
        if (v_remark_type.trim() == "") {
            alert("Please Enter Remarks!");
            $('#v_remark_type').focus();
            return false;
        }
        return true;
    } catch (err) {
        console.log("ERROR >> " + err);
    }
}

// take feed from customer if agent resolve the case 
function getfeedback(complaint_status) {
    if (complaint_status == '8' || complaint_status == '2') {
        // $("#feedbackdiv").show();
        $("#group_assign_div").hide();
    } else {
        $("#group_assign_div").show();
        $('select#group_assign').val('0')
            // $("#feedbackdiv").hide();
    }
}