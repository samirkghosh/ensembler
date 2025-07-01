//function shifted from web_sent_whatsapp.php
function clear_btn() {
    $("textarea").val("");
    $("#response_msg").text('');
}

function clearText(field) {
    if (field.defaultValue == field.value) field.value = '';
    else if (field.value == '') field.value = field.defaultValue;
}

$(function() {
    $('.chk_boxes').on('change', function() {
        console.log('Clicked');
        $('.chk_boxes1').prop('checked', this.checked);
    });
});

$('input#dm_assign').click(function() {
    var caseid = $("#caseid").val();
    console.log("###############Assign caseid from " + caseid);
    var msgsel = $('.chk_boxes1:checkbox');
    if (msgsel.length > 0) {
        if ($('.chk_boxes1:checkbox:checked').length < 1) {
            alert('Please select at least one message to associate with the case');
            msgsel[0].focus();
            return false;
        }
    }

    // Use secure AJAX
    SecureAjax.post('assign_case.php', 
        $('form#dm_form_msg').serialize(),
        function(data) {
            if (data.success) {
                $("#response_msg").css("display", "block");
                $("#response_msg").text(data.message).css('color', 'green');
                setTimeout(function() { $("#response_msg").text(''); }, 2000);
            }
        },
        function(error) {
            console.error('Error assigning case:', error);
            alert('Error assigning case. Please try again.');
        }
    );
});

//function shifted from web_sent_dm.php
function getUserDM(receptent_id, tweet_id, v_Screenname) {
    console.log('Called show dm message function***********');
    if (receptent_id) {
        // Use secure AJAX
        SecureAjax.post('web_directmessage.php',
            {
                key: receptent_id,
                type: 'showDM',
                tweet_id: tweet_id,
                v_Screenname: v_Screenname
            },
            function(data) {
                $("#dmshowtable").empty();
                $("#dmshowtable").html(data);
            },
            function(error) {
                console.error('Error fetching DM:', error);
                alert('Error fetching direct messages. Please try again.');
            }
        );
    }
}

$(function() {
    $('.chk_boxes').on('change', function() {
        console.log('Clicked');
        $('.chk_boxes1').prop('checked', this.checked);
    });
});

$('input#dm_assign').click(function() {
    var caseid = $("#caseid").val();
    console.log("###############Assign caseid from " + caseid);

    var msgsel = $('.chk_boxes1:checkbox');
    if (msgsel.length > 0) {
        if ($('.chk_boxes1:checkbox:checked').length < 1) {
            alert('Please select at least one message to associate with the case');
            msgsel[0].focus();
            return false;
        }
    }

    // Use secure AJAX
    SecureAjax.post('assign_case.php',
        $('form#dm_form_msg').serialize(),
        function(data) {
            if (data.success) {
                $("#response_msg").css("display", "block");
                $("#response_msg").text(data.message).css('color', 'green');
                setTimeout(function() { $("#response_msg").text(''); }, 2000);
            }
        },
        function(error) {
            console.error('Error assigning case:', error);
            alert('Error assigning case. Please try again.');
        }
    );
});

//function shifted from web_send_email_reply.php
function validate() {
    if (document.frmums.V_EmailId.value == '') {
        alert("Please Enter Email!");
        document.frmums.V_EmailId.focus();
        return false;
    }
    if (document.frmums.V_Subject.value == '') {
        alert("Please Enter Subject!");
        document.frmums.V_Subject.focus();
        return false;
    } else {
        document.frmums.submit();
    }
}

document.addEventListener("DOMContentLoaded", function() {
    // Initialize CKEditor
    ClassicEditor
        .create(document.querySelector('textarea'), {
            ckfinder: {
                uploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json'
            },
            language: 'en',
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo', '|', 'spellCheck']
        })
        .then(editor => {
            // Add change event listener to the select element
            $("#template").change(function() {
                let val = $(this).val();
                console.log(val);
                // Set the content in the CKEditor instance
                editor.setData(val);
            });

            // code for generating reply with AI :: farhan akhtar :: 19-10-2024
            $("#generateReply").click(function(event) {
                event.preventDefault();

                // Show the loader and blur the background
                $("#loaderOverlay").show();

                // Simulate AI processing delay (2 seconds)
                setTimeout(function() {
                    var replyId = $("#replyid").val();
                    
                    // Use secure AJAX
                    SecureAjax.post('getAIReplyResponse.php',
                        {
                            id: replyId,
                            Action: 'AI_REPLY'
                        },
                        function(response) {
                            console.log("Success:", response);
                            // Set the content in the CKEditor instance
                            editor.setData(response.data);
                            // Hide the loader and remove background blur
                            $("#loaderOverlay").hide();
                        },
                        function(error) {
                            console.error("Error generating AI reply:", error);
                            $("#loaderOverlay").hide();
                            alert('Error generating AI reply. Please try again.');
                        }
                    );
                }, 2000);
            });
        })
        .catch(error => {
            console.error(error);
        });
});

// File upload handling
var filesToUpload = [];
$.fn.fileUploader = function(sectionIdentifier) {
    var fileIdCounter = 0;
    this.closest(".files").change(function(evt) {
        var output = [];
        for (var i = 0; i < evt.target.files.length; i++) {
            fileIdCounter++;
            var file = evt.target.files[i];
            var fileId = sectionIdentifier + fileIdCounter;

            filesToUpload.push({
                id: fileId,
                file: file
            });

            var removeLink = "<a class=\"removeFile\" href=\"#\" data-fileid=\"" + fileId + "\" id=\"" + file.name + "\">Remove</a>";

            output.push("<li><strong>", (file.name), "</strong>&nbsp; &nbsp; ", removeLink, "</li> ");
        };

        $(this).children(".fileList")
            .append(output.join(""));

        // Reset the form containing the input field
        $(this).closest('form')[0].reset();
    });

    $(this).on("click", ".removeFile", function(e) {
        e.preventDefault();

        var fileId = $(this).parent().children("a").data("fileid");
        var fileToRemove = $(this).attr("id");

        // Use secure AJAX for file removal
        SecureAjax.post('remove_attachments.php',
            { file: fileToRemove },
            function(response) {
                console.log(response);
                // Remove the corresponding list item from the file list
                $(this).closest("li").remove();
            },
            function(error) {
                console.error('Error removing file:', error);
                alert('Error removing file. Please try again.');
            }
        );
    });

    return this;
};

(function() {
    $("#files").fileUploader("attachments");

    $('#attachment').change(function(e) {
        var files = e.target.files;

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            
            // Use secure file upload
            SecureAjax.uploadFile(
                'upload.php',
                file,
                function(progress) {
                    console.log('Upload progress:', progress + '%');
                },
                function(response) {
                    console.log('File uploaded successfully:', response);
                },
                function(error) {
                    console.error('Error uploading file:', error);
                    alert('Error uploading file. Please try again.');
                }
            );
        }
    });
})();

//code shifted from queue_report.php 
$(function () {
    var inout_report = '<?php echo $report_in_out ?>' ;
          $("#report-server-side").DataTable({
    "searching": false,
    "responsive": true, 
    "pageLength": 100, 
    "lengthChange": false,
    "autoWidth": false,
    "processing": true,
    "serverSide": true,
    "searchable" : false,
    "ajax":{
        "url": "omnichannel_config/bulk_report_function.php",
        "dataType": "json",
        "type": "POST",
        "data" : {report_name : '<?php echo $report_name; ?>',
                  report_in_out : '<?php echo $report_in_out ?>',
                  from_date : '<?php echo $from_date ?>',
                  end_date : '<?php echo $end_date ?>',
                  schedule : '<?php echo $schedule ?>',
                  message_type : '<?php echo $message_type ?>',
                  status : '<?php echo $status ?>',
                  list_wise : '<?php echo $list_wise ?>',
                  user_wise : '<?php echo $user_wise ?>',
                  'action':'queue_report',
                  'channeltype':'SMS'
                }
    }, 
    "columns": [
        { "data": "#" },
        { "data": "send_to" },
        { "data": "name" },
        { "data": "message" },
        // { "data": "units" },
        { "data": "message_type_flag" },
        { "data": "status" },
        { "data": "schedule" },
        { "data": "create_date" },
        { "data": "create_by" },
      ],
    "columnDefs": [{
        targets: "_all",
        orderable: false
     }],
    "dom": "Bfrtip",
    "buttons": [ {
                  extend: 'excelHtml5',
                  text: '<i class="fas fa-file-excel"></i>',
                  titleAttr: 'Excel',
                 
                  title: $('.download_label').html(),
                  exportOptions: {
                      columns: ':visible'
                  }
                  }, {
                      extend: 'colvis',
                      text: '<i class="fa fa-columns"></i>',
                      titleAttr: 'Columns',
                      title: $('.download_label').html(),
                      postfixButtons: ['colvisRestore']
                  },],

    
                // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            });

     });
//function shifted from sms_reply.php 
$(document).ready(function () {
    $("#reply").css("resize", "none"); // resize textarea off

    var maxLength = 160; // Change this to your desired character limit
    var textArea = $('textarea');
    var charCount = $('#charCount');

    textArea.keyup(function () {
        var text = textArea.val();
        var remaining = maxLength - text.length;

        charCount.text('Characters remaining: ' + remaining);

        if (remaining < 0) {
            textArea.val(text.substring(0, maxLength));
            charCount.text('Characters remaining: 0');
        }
    });

    $('form').on('submit', function (e) {

        var reply = $("#reply").val();
        if (reply == "") {
            var msg = "Please Enter Message";
            $("#alert_fail").show();
            $("#alert_fail").text(msg);
            setTimeout(function () {
                $("#alert_fail").hide();
            }, 2000);
            return false;
        }
        e.preventDefault();
        $.ajax({
            type: 'post',
            url: 'send_sms_reply.php',
            data: $('form').serialize(),
            success: function (data) {
                if (data == true) {
                    var msg = "Message is Sent Successfully";
                    $("#alert_success").show();
                    $("#alert_success").text(msg);
                    // setTimeout(function () {
                        window.close();
                    // }, 1000);

                }
            }
        });

    });
});
//function for getting whatsapp dm 
function get_template(templateid, v_Screenname, messageid) {
    console.log("***************window v_Screenname" + v_Screenname + " message id" + messageid);
    $.ajax({
        url: 'assign_case.php',
        type: 'post',
        data: {
            'key': templateid,
            'type': 'tweetTemplate',
            'v_Screenname': v_Screenname,
            'messageid': messageid
        },
        success: function (data, status) {
            console.log("****" + data)
            $("#dm_message").empty();
            $("#dm_message").val(data);
        },
        error: function (xhr, desc, err) {
            // Handle error
        }
    });
}