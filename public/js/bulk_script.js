var img_path = '/assets/images/loading.gif';
var base_url = window.location.origin;

// Aarti : 20-12-2023 : get count list 
function get_count_of_list(campaign_id) {
    var campaign_id = campaign_id;
    SecureAjax.get('omnichannel_config/bulk_function.php',
        {
            'campaign_id': campaign_id,
            'action': 'ajax_campaign_contact_count'
        },
        function(data) {
            console.log('ajax_campaign_contact_count');
            console.log(data);
            $('#show-counts').empty();
            $('#show-counts').html(data);
        },
        function(error) {
            console.error('Error getting contact count:', error);
        }
    );
}

$(document).ready(function() {
    $('.select2bs4s').select2({
        theme: 'bootstrap4',
        maximumSelectionLength: 20
    });
    $('.selectchannel').select2({
        theme: 'bootstrap4',
        maximumSelectionLength: 10
    });

    $("#scheduletime2").on('change', function(event) {
        console.log('check date ');
        if ($(this).is(':checked')) {
            $("#bulk_send_button").val('Save');
            $("#datepicker_div").show();
        } else {
            $("#bulk_send_button").val('Send Now');
            $("#datepicker_div").hide();
        }
    });

    $('#custom-content-above-profile-tab').on('click', function() {
        $('#custom-content-above-home').removeClass('show');
    });

    // Aarti : 20-12-2023 : Save Bulk Upload File 
    $("#select_file_name").on('change', function() {
        var selected_val = $(this).val();
        if (selected_val != '0') {
            $("#upload_file_div").hide();
            $("#file_name_div").hide();
            $("#desc_div").hide();
            $("#divider_row").hide();
        } else {
            $("#upload_file_div").show();
            $("#file_name_div").show();
            $("#desc_div").show();
            $("#divider_row").show();
        }
        $("#file").val('');
    });

    // Aarti : 20-12-2023 : Save Bulk Upload File 
    $('#import_form').on('submit', function(event) {
        event.preventDefault();
        $("#bulk_send_button").prop('disabled', true).val('Please Wait...');

        // Use secure file upload
        SecureAjax.uploadFile(
            'omnichannel_config/bulk_function.php',
            new FormData(this),
            function(progress) {
                console.log('Upload progress:', progress + '%');
            },
            function(data) {
                $("#bulk_send_button").prop('disabled', false).val('Save');
                var obj = JSON.parse(data);
                if (obj.status == 'fail') {
                    $.each(obj.msg, function(key, value) {
                        $('.' + key + "_error").html(value);
                    });
                    if (obj.error_type == '1') {
                        var check_confirm = confirm('You want to overwrite the list ?\nClick OK to overwrite or \nCancel for New file upload ?');
                        if (check_confirm) {
                            $("#file_upload_type").val('overwrite');
                            $("#import_form").trigger("submit");
                        } else {
                            // cancel for new file upload
                            $("#file_upload_type").val('no_new');
                            $("#import_form").trigger("submit");
                        }
                    }
                } else {
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            },
            function(error) {
                console.error('Error uploading file:', error);
                $("#bulk_send_button").prop('disabled', false).val('Save');
            }
        );
    });

    $("#quickForm").submit(function(e) {
        $("[class$='_error']").html("");
        $(".custom_loader").html('<img src="' + img_path + '">');
        var url = $(this).attr('action');
        $("#single_send_button").prop('disabled', true).text('Please Wait...');

        SecureAjax.post('omnichannel_config/bulk_function.php',
            $("#quickForm").serialize(),
            function(data) {
                console.log(data);
                if (data.st === 1) {
                    $.each(data.msg, function(key, value) {
                        $('.' + key + "_error").html(value);
                    });
                } else if (data.st === 2) {
                    errorMsg(data.msg);
                } else {
                    $(".custom_sms").html(data.msg);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
                $(".custom_loader").html("");
                $("#single_send_button").prop('disabled', false).text('Save');
            },
            function(error) {
                console.error('Error submitting form:', error);
                $("#single_send_button").prop('disabled', false).text('Save');
                $(".custom_loader").html("");
            }
        );
        e.preventDefault();
    });

    /* 
     * AUTHOR :: FARHAN AKHTAR
     * LAST MODIFIED ON :: 13-02-2025
     * PURPOSE :: TO IMPLEMENT FUNCTIONALLITY FOR GENERATE LIST (EXCEL SHEET) OF EMAILS (DATE WISE). 
     */
    $("#BulkEmailSubmit").click(function(event) {
        var startDate = $("#BulkEmailStartDate").val();
        var endDate = $("#BulkEmailEndDate").val();
        var dateTimePattern = /^(\d{2})-(\d{2})-(\d{4}) (\d{2}):(\d{2}):(\d{2})$/;

        // Check if both fields are filled
        if (startDate === "" || endDate === "") {
            alert("Both Start Date and End Date are required.");
            return;
        }

        // Validate the format dd-mm-yyyy hh:mm:ss
        if (!dateTimePattern.test(startDate) || !dateTimePattern.test(endDate)) {
            alert("Please enter the date and time in dd-mm-yyyy hh:mm:ss format.");
            return;
        }

        // Redirect to PHP script for CSV download
        window.location.href = "omnichannel_config/bulk_function.php?startDate=" + startDate + "&endDate=" + endDate + "&action=GET-EMAIL-LIST";
    });
});

// If condition work for reply section 
//Initialize Select2 Elements : Multiple select
$(function() {
    $('.select2bs4').select2({
        ajax: {
            delay: 250,
            url: 'omnichannel_config/bulk_function.php',
            dataType: 'json',
            data: function(params) {
                var queryParameters = {
                    q: params.term,
                    'action': 'get_contact_list'
                }
                return queryParameters;
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        },
        placeholder: 'Search for a contact',
        minimumInputLength: 3,
        maximumSelectionLength: 100
    });
});

// for Schedule toggle : Checkbox 
$("#scheduletime").click(function() {
    if ($(this).is(":checked")) {
        $("#single_send_button").text('Save');
        $("#hi").show();
    } else {
        $("#single_send_button").text('Send Now');
        $("#hi").hide();
    }
});

// aarti::  Template Redirect in textarea with Current Value :21-12-2023
$("input[name='customRadio2']").on("click", function() {
    var tempname = $("input:checked").val();
    var currentVal = $("#output").val();
    console.log('currentVal ' + currentVal);
    console.log('tempname ' + tempname);

    SecureAjax.post('omnichannel_config/bulk_function.php',
        {
            "tempname": tempname,
            'action': 'get_template_content'
        },
        function(data) {
            var obj = JSON.parse(data);
            var temp_content = obj.template_content;
            $("#message").val(currentVal + '\n' + temp_content);
        },
        function(error) {
            console.error('Error getting template content:', error);
        }
    );
});

// aarti::  Template Redirect in textarea with Current Value :02-01-2025
$("#selecttemplate").on("change", function() {
    var tempname = $("#selecttemplate").val();
    var currentVal = $("#outputid").val();

    SecureAjax.post('omnichannel_config/bulk_function.php',
        {
            "tempname": tempname,
            'action': 'get_template_content'
        },
        function(data) {
            var obj = JSON.parse(data);
            var temp_content = obj.template_content;
            $(".messagebulk").val(currentVal + '\n' + temp_content);
        },
        function(error) {
            console.error('Error getting template content:', error);
        }
    );
});

// aarti::  Get hidden Value textarea  :21-12-2023
$("#message").keyup(function(event){
    event.preventDefault();
    // Getting the current value of textarea
    var currentText = $(this).val();
    console.log('currentText');
    console.log(currentText);
    // Setting the Div content
    $("#output").val(currentText);
});

// aarti::  Get hidden Value textarea  :21-12-2023
$(".messagebulk").keyup(function(event){
    event.preventDefault();
    // Getting the current value of textarea
    var currentText = $(this).val();
    console.log('currentText');
    console.log(currentText);
    // Setting the Div content
    $("#outputid").val(currentText);
});

$('.pickdate').datetimepicker({
    format : 'd-m-Y H:i',
    formatTime: 'H:i',
    formatDate : 'd-m-Y',
    step : 30,
    minDate:new Date()
});