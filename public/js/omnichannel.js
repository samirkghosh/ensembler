jQuery(function ($){
    var OmniCommon = {
        init: function (){
            OmniCommon.HandleTestChannel();
            OmniCommon.handleTwitterDebug();
            OmniCommon.handleSMSDebug();
            OmniCommon.handleFaceBookDebug();
            OmniCommon.handleWhatsAppDebug();
            jQuery("body").on('click','.add_channel',this.HandleAddConfig);
            jQuery("body").on('click','.smtp_submit_config',this.HandleSubmit);
            jQuery("body").on('click','.test_channel',this.handleTestSubmit);
            jQuery("body").on('click','.delete_channel',this.HandleDelete);
            jQuery("body").on('click','.Cancel',this.HandleCancel);
            jQuery("body").on('click','.attctment_check',this.HandleAttchement);
            jQuery("body").on('change','.channel_name',this.HandleChannelForm);
            jQuery("body").on('change','.type_auth',this.HandleImapType); 
            jQuery("body").on('click','.Imapsubmit_config',this.HandleImapSubmit);
            jQuery("body").on('click','.submit_twitter_config',this.HandleTwitterSubmit);

            jQuery("body").on('click','.submit_sms_config',this.HandleSmsSubmit);
            jQuery("body").on('click','.submit_whatsapp_config',this.HandleWhatsappSubmit);
            jQuery("body").on('click','.submit_messenger_config',this.HandleMessengerSubmit);
            jQuery("body").on('click','.submit_instagram_config',this.HandleInstagramSubmit);
            jQuery("body").on('click','.submit_facebook_config',this.HandleFacebookSubmit);
            jQuery("body").on('click','.Refresh',this.HandleRefresh);

            jQuery("body").on('click','.twitter_debug_channel',this.handleTwitterSubmit);
            jQuery("body").on('click','.sms_debug_channel',this.handleSMSSubmit);
            jQuery("body").on('click','.facebook_debug_channel',this.handleFaceBookSubmit);
            jQuery("body").on('click','.whatsapp_debug_channel',this.handleWhatsAppSubmit);
            jQuery("body").on('click','.messenger_debug_channel',this.handleMessengerSubmit);
            // jQuery("body").on('click','.instagram_debug_channel',this.handleInstagramSubmit);
            jQuery("body").on('change','.method_type',this.handleMethod); 
            jQuery("body").on('change','.sms_type',this.handleSmsType);
            jQuery("body").on('change','.oauth_type_twitter',this.HandleTwitterChangeAuth);

        },
        HandleAddConfig:function(){
        	$('#myModal').modal('show');
        },
        HandleSubmit:function(){
            console.log("function triggered sucessfully ")
            var formData = new FormData($('#addconfig')[0]);
            formData.append("channel_name",$('.channel_name').val());
            console.log("FormData:", formData)
             $.ajax({
                method: 'POST',
                url: 'omnichannel_config/config_function.php',
                cache: false,
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    $('#success').html('<div class="alert alert-success alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Extension Insert Successfuly</div>');
                    setTimeout(function(){ 
                        var encodedToken = btoa('configuration');
                        window.location = 'omni_channel.php?token='+ encodeURIComponent(encodedToken); 
                    },2000); //reload a page after 5 seconds
                }
            });
        },
        HandleImapSubmit:function(){
            var formData = new FormData($('#imapconfig')[0]);
            formData.append("channel_name",$('.channel_name').val());
             $.ajax({
                method: 'POST',
                url: 'omnichannel_config/config_function.php',
                cache: false,
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    $('#success').html('<div class="alert alert-success alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Extension Insert Successfuly</div>');
                    setTimeout(function(){ 
                        var encodedToken = btoa('configuration');
                        window.location = 'omni_channel.php?token='+ encodeURIComponent(encodedToken); 
                    },2000); //reload a page after 5 seconds
                }
            }); 
         },
        HandleTwitterSubmit:function(){
            var formData = new FormData($('#twitterconfig')[0]);
            formData.append("channel_name",$('.channel_name').val());
             $.ajax({
                method: 'POST',
                url: 'omnichannel_config/config_function.php',
                cache: false,
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    $('#success').html('<div class="alert alert-success alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Extension Insert Successfuly</div>');
                    setTimeout(function(){ 
                        var encodedToken = btoa('configuration');
                        window.location = 'omni_channel.php?token='+ encodeURIComponent(encodedToken); 
                    },2000); //reload a page after 5 seconds
                }
            });
        },
        HandleTwitterChangeAuth(){
            console.log($(this).val());
            if($(this).val() == '0'){
                $('.bearer_token').show();
            }else{
                $('.bearer_token').hide();
            }
        },
        HandleSmsSubmit:function(){
            var formData = new FormData($('#smsconfig')[0]);
            formData.append("channel_name",$('.channel_name').val());
             $.ajax({
                method: 'POST',
                url: 'omnichannel_config/config_function.php',
                cache: false,
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    $('#success').html('<div class="alert alert-success alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Extension Insert Successfuly</div>');
                    setTimeout(function(){ 
                        var encodedToken = btoa('configuration');
                        window.location = 'omni_channel.php?token='+ encodeURIComponent(encodedToken); 
                    },2000); //reload a page after 5 seconds
                }
            });
        },
HandleWhatsappSubmit: function() {
    var formData = new FormData($('#whatsappconfig')[0]);
    formData.append("channel_name", $('.channel_name').val());

    $.ajax({
        method: 'POST',
        url: 'omnichannel_config/config_function.php',
        cache: false,
        processData: false,
        contentType: false,
        data: formData,
        success: function(response) {
            console.log('Form submitted successfully. Response:', response);
            $('#success').html('<div class="alert alert-success alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Extension Insert Successfully</div>');
            setTimeout(function() {
                window.location.reload();
            }, 2000);
        },
        error: function(xhr, status, error) {
            console.log('Form submission failed. Status:', status);
            console.log('Error:', error);
            console.log('Response:', xhr.responseText);
        }
    });
},
        HandleMessengerSubmit:function(){
            var formData = new FormData($('#messengerconfig')[0]);
            formData.append("channel_name",$('.channel_name').val());
             $.ajax({
                method: 'POST',
                url: 'omnichannel_config/config_function.php',
                cache: false,
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    $('#success').html('<div class="alert alert-success alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Extension Insert Successfuly</div>');
                    setTimeout(function(){ 
                        var encodedToken = btoa('configuration');
                        window.location = 'omni_channel.php?token='+ encodeURIComponent(encodedToken); 
                    },2000); //reload a page after 5 seconds
                }
            });
        },
        HandleInstagramSubmit:function(){
            var formData = new FormData($('#instagramconfig')[0]);
            formData.append("channel_name",$('.channel_name').val());
             $.ajax({
                method: 'POST',
                url: 'omnichannel_config/config_function.php',
                cache: false,
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    $('#success').html('<div class="alert alert-success alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Extension Insert Successfuly</div>');
                    setTimeout(function(){ 
                        var encodedToken = btoa('configuration');
                        window.location = 'omni_channel.php?token='+ encodeURIComponent(encodedToken); 
                    },2000); //reload a page after 5 seconds
                }
            });
        },

        HandleFacebookSubmit:function(){
            var formData = new FormData($('#facebookconfig')[0]);
            formData.append("channel_name",$('.channel_name').val());
             $.ajax({
                method: 'POST',
                url: 'omnichannel_config/config_function.php',
                cache: false,
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    $('#success').html('<div class="alert alert-success alert-dismissible" role="alert"> <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>Extension Insert Successfuly</div>');
                    setTimeout(function(){ 
                        var encodedToken = btoa('configuration');
                        window.location = 'omni_channel.php?token='+ encodeURIComponent(encodedToken); 
                    },2000); //reload a page after 5 seconds
                }
            });
        },
        handleTestSubmit:function(){
            console.log('handleSubmit');
            $("#testconfig").submit();
        },
        HandleTestChannel:function(){
             $("#testconfig").validate({
                ignore: [],
                rules: {
                    email_to: {
                        required:true,
                    },
                    subject: {
                        required:true,
                    },
                    body:{
                        required:true,
                    }                    
                },              
                submitHandler: function(form) {
                    var formData = new FormData($('#testconfig')[0]);
                    $.ajax({
                        method: 'POST',
                       url: 'omnichannel_config/config_function.php',
                        cache: false,
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function (response) {
                            //this will display your success message
                            // $('#table_display').hide();
                            $('.output_display').show();
                            $('.output_display').text(response);
                            // setTimeout(function(){ 
                            // window.location.reload(); 
                            // },8000); //reload a page after 5 seconds
                        }
                    });
                }
            });
        },
        HandleDelete: function () {
            var result = confirm("Are you sure to delete ?");
            if (result) {
                var id = $(this).data('id');
                var channel = $(this).data('channel');
                if (id) {
                    $.ajax({
                        method: 'POST',
                        url: 'omnichannel_config/config_function.php',
                        data: { 'action': 'ChannelDelete', 'id': id, 'channel': channel },
                        success: function (response) {
                            $('#success').html('<div class="alert alert-success alert-dismissible" role="alert"> Deletion Successful</div>');
                            setTimeout(function () {
                                $('#success').html('');
                                location.reload();
                            }, 3000);
                        }
                    });
                }
            }
        },
        HandleCancel:function(){
            var encodedToken = btoa('configuration');
            window.location = 'omni_channel.php?token='+encodeURIComponent(encodedToken); 
        },
        HandleAttchement:function(){
            if ($(this).prop('checked')==true){ 
                $('.file').show();
            }else{
                $('.file').hide();
            }
        },
        HandleChannelForm:function(){
            var select = $(this).val();
            if(select == 'Smtp'){
                $('.Smtp_form').show();
                $('.Imap_form').hide();
                $('.twitter_form').hide();
                $('.sms_form').hide();  
                $('.whatsapp_form').hide(); 
                $('.messenger_form').hide();
                $('.instagram_form').hide();  
                $('.facebook_form').hide();
            }else if(select == 'Imap'){
                $('.Imap_form').show();
                $('.Smtp_form').hide();
                $('.twitter_form').hide();
                $('.sms_form').hide();  
                $('.whatsapp_form').hide(); 
                $('.messenger_form').hide(); 
                $('.instagram_form').hide(); 
                $('.facebook_form').hide();
            }else if(select == 'Twitter'){
                $('.twitter_form').show();
                $('.Imap_form').hide();
                $('.Smtp_form').hide(); 
                $('.sms_form').hide();  
                $('.whatsapp_form').hide();  
                $('.messenger_form').hide(); 
                $('.instagram_form').hide(); 
                $('.facebook_form').hide();           
            }else if(select == 'SMS'){
                $('.sms_form').show();
                $('.twitter_form').hide();
                $('.Imap_form').hide();
                $('.Smtp_form').hide(); 
                $('.whatsapp_form').hide(); 
                $('.messenger_form').hide();
                $('.instagram_form').hide();  
                $('.facebook_form').hide();
            }else if(select == 'Whatsapp'){
                $('.whatsapp_form').show();
                $('.sms_form').hide();
                $('.twitter_form').hide();
                $('.messenger_form').hide(); 
                $('.instagram_form').hide(); 
                $('.Imap_form').hide();
                $('.Smtp_form').hide(); 
                $('.facebook_form').hide();
            }else if(select == 'Facebook'){
                $('.facebook_form').show();
                $('.whatsapp_form').hide();
                $('.sms_form').hide();
                $('.twitter_form').hide();
                $('.messenger_form').hide();
                $('.instagram_form').hide();  
                $('.Imap_form').hide();
                $('.Smtp_form').hide(); 
            }else if(select == 'Messenger'){
                $('.facebook_form').hide();
                $('.whatsapp_form').hide();
                $('.sms_form').hide();
                $('.twitter_form').hide();
                $('.messenger_form').show();
                $('.instagram_form').hide();  
                $('.Imap_form').hide();
                $('.Smtp_form').hide(); 
            }else if(select == 'Instagram'){
                $('.facebook_form').hide();
                $('.whatsapp_form').hide();
                $('.sms_form').hide();
                $('.twitter_form').hide();
                $('.messenger_form').hide();
                $('.instagram_form').show();  
                $('.Imap_form').hide();
                $('.Smtp_form').hide(); 
            }
        },
        HandleImapType:function(){
            var select = $(this).val();
            if(select == 'OAuth'){
                $('.auth2').hide();
            }else if(select == 'OAuth2'){
                $('.auth2').show();
            }
        },
        HandleRefresh:function(){
            window.location.reload();
        },
        handleTwitterSubmit:function(){
            console.log('handleTwitterSubmit');
            $("#twitterconfig").submit();
        },
        handleTwitterDebug:function(){
            $("#twitterconfig").validate({
                ignore: [],             
                submitHandler: function(form) {
                    var formData = new FormData($('#twitterconfig')[0]);
                    $.ajax({
                        method: 'POST',
                       url: 'omnichannel_config/config_function.php',
                        cache: false,
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function (response) {
                            $('.output_display_twitter').show();
                            $('.output_display_twitter').html(response);
                        }
                    });
                }
            });
        },
        handleSMSDebug:function(){
            $("#smsconfig").validate({
                ignore: [],
                rules: {
                    number: {
                        required:true,
                    },
                    sms_message:{
                        required:true,
                    }                    
                },              
                submitHandler: function(form) {
                    var formData = new FormData($('#smsconfig')[0]);
                    $.ajax({
                        method: 'POST',
                       url: 'omnichannel_config/config_function.php',
                        cache: false,
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function (response) {
                            $('.output_display_sms').show();
                            $('.output_display_sms').html(response);
                        }
                    });
                }
            });
        },
        handleSMSSubmit:function(){
            console.log('handleSMSSubmit');
            $("#smsconfig").submit();
        },
        handleWhatsAppSubmit:function(){
            $("#whatsappconfig").submit();
        },
        handleMessengerSubmit:function(){
            $("#messengerconfig").submit();
        },
        handleWhatsAppDebug:function(){
            $("#whatsappconfig").validate({
                ignore: [],
                rules: {
                    number: {
                        required:true,
                    },
                    sms_message:{
                        required:true,
                    }                    
                },              
                submitHandler: function(form) {
                    var formData = new FormData($('#whatsappconfig')[0]);
                    $.ajax({
                        method: 'POST',
                       url: 'omnichannel_config/config_function.php',
                        cache: false,
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function (response) {
                            $('.output_display_whatsapp').show();
                            $('.output_display_whatsapp').html(response);
                        }
                    });
                }
            });
        },
        handleFaceBookSubmit:function(){
            $("#facebookconfig").submit();
        },
        handleFaceBookDebug:function(){
            $("#facebookconfig").validate({
                ignore: [],
                rules: {
                    number: {
                        required:true,
                    },
                    sms_message:{
                        required:true,
                    }                    
                },              
                submitHandler: function(form) {
                    var formData = new FormData($('#facebookconfig')[0]);
                    $.ajax({
                        method: 'POST',
                       url: 'omnichannel_config/config_function.php',
                        cache: false,
                        processData: false,
                        contentType: false,
                        data: formData,
                        success: function (response) {
                            $('.output_display_facebook').show();
                            $('.output_display_facebook').html(response);
                        }
                    });
                }
            });
        },
        handleMethod:function(){
            var select = $(this).val();
            if(select == 'POST'){
                $('.show_post').show();
            }else{
                $('.show_post').hide();
            }
        },
        handleSmsType:function(){
            var select = $(this).val();
            if(select == 'url_based'){
                $('.sms_zambia').show();
                $('.sms_onfonmedia').hide();
                $('.exotel').hide();
                $('.sms_common').show();
                $('.SMPP').hide();
            }else if(select == 'onfonmedia'){
                $('.sms_onfonmedia').show();
                $('.sms_zambia').hide();
                $('.exotel').hide();
                $('.sms_common').show();
                $('.SMPP').hide();
            }else if(select == 'exotel'){
                $('.sms_onfonmedia').hide();
                $('.sms_zambia').hide();
                $('.exotel').show();
                $('.sms_common').hide();
                $('.SMPP').hide();
            }else if(select == 'SMPP'){
                $('.sms_onfonmedia').hide();
                $('.sms_zambia').hide();
                $('.exotel').hide();
                $('.sms_common').hide();
                $('.SMPP').show();
            }
        }
    }
    OmniCommon.init();
});
// ############################### WebChat code here ####################################
function initialize_datatables_web_chat(startdatetime = '', enddatetime = '', phone = '', id = '', chat_type, caseid = '',send_name ='',iallstatus ='' ) {
    var startdatetime = $('#startdatetime').val();
        var enddatetime = $('#enddatetime').val();
        
    var dataTable = $('#chat_table').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "pageLength": 25, // Set default number of records per page to 30
        "lengthMenu": [ [10, 25, 50, 100,  -1], [10, 25, 50,100, "All"] ],
        "searching": false,
        "ajax": {
            url: "omnichannel_config/fetchData.php",
            type: "POST",
            data: function(d) {
                d.startdatetime = startdatetime;
                d.enddatetime = enddatetime;
                d.phone = phone;
                d.id =  id;
                d.chat_type = chat_type;
                d.caseid = caseid;
                d.send_name =  send_name;
                d.iallstatus = iallstatus;
                d.action = 'web_chat';
            }
        },
        "createdRow": function( row, data, dataIndex) {
        var clr = data[8]; // Assuming clr value 
        if (clr == 'yellow') {
            // $(row).addClass('flag-yellow');
        } else if (clr == 'red') {
            $(row).addClass('mail-row');
        } else if (clr == 'green') {
            // $(row).addClass('flag-green');
        }
        },
        "drawCallback": function( settings ) {
            // Reinitialize Colorbox for new elements
            $(".ico-interaction2").colorbox({
                iframe: true,
                innerWidth: 1000,
                innerHeight: 450
            });
            // Reinitialize Colorbox for new elements
            // $(".openPopup").colorbox({iframe:true, width:"80%", height:"80%"});
        }

    });
    return dataTable;
}   
$(document).ready(function() {
     // Set an interval to refresh data every 2 seconds
     initialize_datatables_web_chat();
     // Set an interval to refresh data every 9 seconds
     setInterval(function() {
         // Get the current page info before reloading
         var dataTable = $('#chat_table').DataTable();
         var pageInfo = dataTable.page.info();

         // Reload the data without resetting the paging
         dataTable.ajax.reload(null, false);

         // After the reload, set the page back to the original page
         dataTable.page(pageInfo.page).draw(false);
     }, 20000); // 9000 milliseconds = 9 seconds
     $('#post_web_chat').submit(function(e) {
        e.preventDefault();
        var startdatetime = $('#startdatetime').val();
        var enddatetime = $('#enddatetime').val();
        var phone = $('#phone').val();
        var id = $('#id').val();
        var chat_type = $('#chat_type').val();
        var caseid = $('#caseid').val();
        var send_name = $('#send_name').val();
        var iallstatus = $('#iallstatus').val();
        console.log(iallstatus);
        // Destroy DataTable instance to reinitialize with new data
        $('#chat_table').DataTable().destroy();
    
        // Call initialize_datatables function with appropriate filter parameters
        initialize_datatables_web_chat(startdatetime, enddatetime, phone, id, chat_type, caseid,send_name,iallstatus);
});
$('#reset_button_chat').click(function () {
    $('#chat_table').DataTable().destroy();
    $('#startdatetime,#enddatetime,#phone,#id,#chat_type, #caseid','#send_name','#iallstatus').val('');
    initialize_datatables_web_chat();
});
// ############################### END WebChat code here ####################################

// ############################### START Facebook code here ####################################
fill_datatables_facebook();
function fill_datatables_facebook(startdatetime = '', enddatetime = '', iallstatus = '') {
    var id = $('#inte_id').val();
    var dataTable = $('#facebook_table').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "pageLength": 25, // Set default number of records per page to 30
        "lengthMenu": [ [10, 25, 50, 100,  -1], [10, 25, 50,100, "All"] ],
        "searching": false,
        "ajax": {
            url: "omnichannel_config/fetchData.php",
            type: "POST",
            data:{
                startdatetime: startdatetime,
                enddatetime: enddatetime,
                iallstatus: iallstatus,
                id:id,
                action:'facebook'
            }
        },
            "createdRow": function( row, data, dataIndex) {
            var clr = data[7]; // Assuming clr value 
           
            if (clr == 'yellow') {
                // $(row).addClass('flag-yellow');
            } else if (clr == 'red') {
                $(row).addClass('mail-row');
            } else if (clr == 'green') {
                // $(row).addClass('flag-green');
            }
        }

    });
}  
$('#post_facebook').submit(function(e) {
    e.preventDefault();
    var startdatetime = $('#startdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var iallstatus = $('#iallstatus').val();

    // Destroy DataTable instance to reinitialize with new data
    $('#facebook_table').DataTable().destroy();

    // Call fill_datatables function with appropriate filter parameters
    fill_datatables_facebook(startdatetime, enddatetime,  iallstatus );
});

// ############################### END Facebook code here ####################################

// ############################### START SMS code here ####################################
fill_datatables_sms();
function fill_datatables_sms(startdatetime = '', enddatetime = '', phone = '',ICASEID = '',iallstatus){
   
    var id = $('#inte_id').val();
    var startdatetime = $('#startdatetime').val();
    var enddatetime = $('#enddatetime').val();
   
    var dataTable = $('#sms_table').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "pageLength": 25, // Set default number of records per page to 25
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "searching": false,
        "ajax": {
            url: "omnichannel_config/fetchData.php",
            type: "POST",
            data: {
                startdatetime: startdatetime,
                enddatetime: enddatetime,
                phone: phone,
                id: id,
                ICASEID: ICASEID,
                iallstatus:iallstatus,
                action:'sms'
            }
        },
       "createdRow": function( row, data, dataIndex) {
            var clr = data[7]; // Assuming clr value is in the sixth column
        
            if (clr == 'yellow') {
                // $(row).addClass('flag-yellow');
            } else if (clr == 'red') {
                $(row).addClass('mail-row');
            } else if (clr == 'green') {
                // $(row).addClass('flag-green');
            }
        }
        ,
            "drawCallback": function( settings ) {
                // Reinitialize Colorbox for new elements
                $(".ico-interaction2").colorbox({
                    iframe: true,
                    innerWidth: 1000,
                    innerHeight: 560
                });
                // Reinitialize Colorbox for new elements
                // $(".openPopup").colorbox({iframe:true, width:"80%", height:"80%"});
            }
    });
}
$('#post_sms').submit(function(e) {
    e.preventDefault();
    var startdatetime = $('#startdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var phone = $('#phone').val();
    var ICASEID = $('#ICASEID').val();
    var iallstatus = $('#iallstatus').val();
    // Destroy DataTable instance to reinitialize with new data
    $('#sms_table').DataTable().destroy();

    // Call fill_datatables function with appropriate filter parameters
    fill_datatables_sms(startdatetime, enddatetime, phone, ICASEID, iallstatus);
  
});
    
$('#reset_button_sms').click(function () {
    $('#sms_table').DataTable().destroy();
    $('#startdatetime').val();
    $('#enddatetime').val();
    $('#phone').val('');
    $('#ICASEID').val();
    fill_datatables_sms();
});
// ############################### END SMS code here ####################################

// ############################### START Twittwr code here ####################################
// Initialize DataTable on document ready
$(document).ready(function() {
    var TwitterdataTable;
    function fill_datatables_twitter(startdatetime='', enddatetime='') {
        var id = $('#inte_id').val();
        var startdatetime = $('#startdatetime').val();
        var enddatetime = $('#enddatetime').val();
       
        TwitterdataTable = $('#twitter_table').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 25, // Set default number of records per page to 25
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "searching": false,
            "ajax": {
                url: "omnichannel_config/fetchData.php",
                type: "POST",
                data: function(d) {
                    d.startdatetime = startdatetime;
                    d.enddatetime = enddatetime;
                    d.v_Screenname = $('#v_Screenname').val();
                    d.v_name = $('#v_name').val();
                    d.ICASEID = $('#ICASEID').val();
                    d.id=id,
                    d.action='twitter'
                    console.log("Data being sent to server:", d);
                }
            },
            "createdRow": function( row, data, dataIndex) {
                var clr = data[8]; // Assuming clr value 
               
                if (clr == 'yellow') {
                    // $(row).addClass('flag-yellow');
                } else if (clr == 'red') {
                    $(row).addClass('mail-row');
                } else if (clr == 'green') {
                    // $(row).addClass('flag-green');
                }
            },
            "drawCallback": function( settings ) {
                // Reinitialize Colorbox for new elements
                $(".ico-interaction2").colorbox({
                    iframe: true,
                    innerWidth: 1000,
                    innerHeight: 550
                });
                // Reinitialize Colorbox for new elements
                // $(".openPopup").colorbox({iframe:true, width:"80%", height:"80%"});
            }
        });
    }      
    // Set an interval to refresh data every 60 seconds
    fill_datatables_twitter();
    setInterval(function() {
      TwitterdataTable.ajax.reload(null, false); // Keep the current page and draw the new data
    }, 60000); // 60000 milliseconds = 60 seconds
    $('#post_twitter').submit(function(e) {
        e.preventDefault();
       
        var startdatetime = $('#startdatetime').val();
    var enddatetime = $('#enddatetime').val();
    
    $('#twitter_table').DataTable().destroy();
    

    // Reinitialize DataTable with new filter values
    fill_datatables_twitter(startdatetime, enddatetime);
    });
    $('#reset_twitter').click(function () {
        $('#twitter_table').DataTable().destroy();
        $('#startdatetime').val();
        $('#enddatetime').val();
        $('#v_Screenname').val('');
        $('#v_name').val('');
        $('#ICASEID').val();
        // Reinitialize DataTable with default or no filters
        fill_datatables_twitter();
    });
    var twitterReport;

// Function to initialize and fill the messeneger report data table
function fill_datatables_twitter_report(startdatetime ='',enddatetime ='',status ='',recipient_id = '') {
   
    twitterReport = $('#twitter_report').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "pageLength": 25, // Default records per page
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "searching": false,
        "paging": true, // Ensure paging is enabled
        "dom": "lBfrtip", // "p" ensures pagination is included in the layout
             "buttons": [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'csv',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'Excel',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                        extend: 'pdfHtml5',
                        filename : $('.report_name').text(),
                        messageTop : $('.download_label').html(),
                        orientation: 'landscape',
                        pageSize: 'A3',
                        text: '<i class="fa fa-file-pdf-o"></i>',
                        titleAttr: 'PDF',
                        // changed logo code [vastvikta][05-05-2025]
                        customize: function ( doc ) {
                            
                            var logoBase64 = document.getElementById('pdf-logo-base64').innerText;
                            doc.images = doc.images || {};
                            doc.images.logo = logoBase64;
                        
                        doc.content.splice( 1, 0, {
                            margin: [ 0, 0, 0, 5 ],
                                alignment: 'left',
                                image: 'logo', 
                                 width: 250
                            } );
                        },
                        title: '.',
                        exportOptions: {
                            columns: ':visible'
                            
                        }
                },
                {
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    titleAttr: 'Columns',
                    title: $('.download_label').html(),
                    postfixButtons: ['colvisRestore']
                },
            ],
            "ajax": {
                url: "omnichannel_config/fetchData.php",
                type: "POST",
                data: function(d) {
                    d.startdatetime = startdatetime || $('#startdatetime').val();
                    d.enddatetime = enddatetime || $('#enddatetime').val();
                    d.status = status || $('#status').val();
                    d.recipient_id = recipient_id || $('#recipient_id').val();
                    d.action = 'twitter_report';
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables error:', error);
                }
            }
    });
}

fill_datatables_twitter_report();

// Set an interval to refresh the table every 10 seconds
setInterval(function() {
    twitterReport.ajax.reload(null, false); // Reload data without resetting pagination
}, 10000);

    // Submit form and reload data based on new filters
    // JavaScript to retain values and submit form without resetting
$('#twitter_report_form').submit(function(e) {
    e.preventDefault();
    
    // Collect filter parameters and log them
    var startdatetime = $('#startdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var status = $('#status').val();
    var recipient_id = $('#recipient_id').val();
    
  
    // Destroy the previous DataTable instance and reload with new data
    $('#twitter_report').DataTable().destroy();
    fill_datatables_twitter_report(startdatetime, enddatetime, status, recipient_id); // Pass parameters to refresh table
});
});

// ############################### END Twittwr code here ####################################

// ############################### START Whatsapp code here ####################################
$(document).ready(function() {

    var WhatsadatappReport;

// Function to initialize and fill the WhatsApp data table
function fill_datatables_whatsapp_report(startdatetime ='',enddatetime ='',status ='',send_to ='') {
        
    WhatsadatappReport = $('#whatsapp_report').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "pageLength": 25, // Default records per page
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "searching": false,
        "paging": true, // Ensure paging is enabled
        "dom": "lBfrtip", // "p" ensures pagination is included in the layout
             "buttons": [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'csv',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'Excel',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                        extend: 'pdfHtml5',
                        filename : $('.report_name').text(),
                        messageTop : $('.download_label').html(),
                        orientation: 'landscape',
                        pageSize: 'A3',
                        text: '<i class="fa fa-file-pdf-o"></i>',
                        titleAttr: 'PDF',
                        // changed logo code [vastvikta][05-05-2025]
                        customize: function ( doc ) {
                            
                            var logoBase64 = document.getElementById('pdf-logo-base64').innerText;
                            doc.images = doc.images || {};
                            doc.images.logo = logoBase64;
                        
                        doc.content.splice( 1, 0, {
                            margin: [ 0, 0, 0, 5 ],
                                alignment: 'left',
                                image: 'logo', 
                                 width: 250
                            } );
                        },
                        title: '.',
                        exportOptions: {
                            columns: ':visible'
                            
                        }
                    },{
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    titleAttr: 'Columns',
                    title: $('.download_label').html(),
                    postfixButtons: ['colvisRestore']
                },
            ],
        "ajax": {
            url: "omnichannel_config/fetchData.php",
            type: "POST",
            data: function(d) {
                d.startdatetime = $('#startdatetime').val();
                d.enddatetime = $('#enddatetime').val();
                d.send_to = $('#send_to').val();
                d.status = $('#status').val();
                d.group_by = $('input[name="group_by"]:checked').val(); // Capture selected radio button value
   
                d.action = 'Whatsapp_report';
            }
        }
    });
}

// Initialize WhatsApp report table
fill_datatables_whatsapp_report();

// Set an interval to refresh the table every 10 seconds
setInterval(function() {
    WhatsadatappReport.ajax.reload(null, false); // Reload data without resetting pagination
}, 10000);

// Submit form and reload data based on new filters
$('#whatsapp_report_form').submit(function(e) {
    console.log('exe');
    e.preventDefault();
    
    $('#whatsapp_report').DataTable().destroy();
    var startdatetime = $('#startdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var status = $('#status').val();
    var send_to = $('#send_to').val();
    fill_datatables_whatsapp_report(startdatetime,enddatetime,status,send_to);// Refresh the table with the new filter parameters
});

// Reset button functionality
$('.reset_whatsapp_report').click(function () {

    // Destroy the existing DataTable
    WhatsadatappReport.destroy();

    // Get the current date and time
    var currentDate = new Date();
    
    // Format the first day of the current month as '01-mm-yyyy 00:00:00'
    var startOfMonth = '01-' + ('0' + (currentDate.getMonth() + 1)).slice(-2) + '-' + currentDate.getFullYear() + ' 00:00:00';

    // Format the current date as 'dd-mm-yyyy 23:59:59'
    var formattedEndDate = ('0' + currentDate.getDate()).slice(-2) + '-' +
                           ('0' + (currentDate.getMonth() + 1)).slice(-2) + '-' +
                           currentDate.getFullYear() + ' 23:59:59';

    // Reset the form fields
    $('#startdatetime').val(startOfMonth);
    $('#enddatetime').val(formattedEndDate);
    $('#status').val('');
    $('#send_to').val('0'); // Reset to the default "Select" option
    $('#searchBox').val(''); // Clear the search input field

    // Optionally, clear any previous dropdown visibility or messages
    $('#dropdownContainer').hide(); // Hide the dropdown if it is visible
    $('#noPhoneNumber').hide(); // Hide the "No records found" message if visible
    $('#searchSenderBox').val('');
    $('#searchBox').val('');
    
    // Reinitialize the table with default filters
    fill_datatables_whatsapp_report(); // Ensure this function initializes the table correctly
});


var messengerReport;

// Function to initialize and fill the messeneger report data table
function fill_datatables_messenger_report(startdatetime ='',enddatetime ='',status ='',send_to = '') {
   
    messengerReport = $('#messenger_report').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "pageLength": 25, // Default records per page
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "searching": false,
        "paging": true, // Ensure paging is enabled
        "dom": "lBfrtip", // "p" ensures pagination is included in the layout
             "buttons": [
                {
                    extend: 'csvHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'CSV',
                    filename: 'csv',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i>',
                    titleAttr: 'Excel',
                    filename: 'Excel',
                    title: $('.download_label').html(),
                    exportOptions: {
                        columns: ':visible'
                    }
                },{
                        extend: 'pdfHtml5',
                        filename : $('.report_name').text(),
                        messageTop : $('.download_label').html(),
                        orientation: 'landscape',
                        pageSize: 'A3',
                        text: '<i class="fa fa-file-pdf-o"></i>',
                        titleAttr: 'PDF',
                        // changed logo code [vastvikta][05-05-2025]
                        customize: function ( doc ) {
                            
                            var logoBase64 = document.getElementById('pdf-logo-base64').innerText;
                            doc.images = doc.images || {};
                            doc.images.logo = logoBase64;
                        
                        doc.content.splice( 1, 0, {
                            margin: [ 0, 0, 0, 5 ],
                                alignment: 'left',
                                image: 'logo', 
                                 width: 250
                            } );
                        },
                        title: '.',
                        exportOptions: {
                            columns: ':visible'
                            
                        }
                    },{
                    extend: 'colvis',
                    text: '<i class="fa fa-columns"></i>',
                    titleAttr: 'Columns',
                    title: $('.download_label').html(),
                    postfixButtons: ['colvisRestore']
                },
            ],
        "ajax": {
            url: "omnichannel_config/fetchData.php",
            type: "POST",
            data: function(d) {
                d.startdatetime = $('#startdatetime').val();
                d.enddatetime = $('#enddatetime').val();
                d.status = $('#status').val();
                d.send_to = $('#send_to').val();
                d.action = 'messenger_report';
            }
        }
    });
}

// Initialize messenger report table
fill_datatables_messenger_report();

// Set an interval to refresh the table every 10 seconds
setInterval(function() {
    messengerReport.ajax.reload(null, false); // Reload data without resetting pagination
}, 10000);

// Submit form and reload data based on new filters
$('#messenger_report_form').submit(function(e) {
    console.log('exe');
    e.preventDefault();
    
    $('#messenger_report').DataTable().destroy();
    var startdatetime = $('#startdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var status = $('#status').val();
    var send_to = $('#send_to').val();
   
    fill_datatables_messenger_report(startdatetime,enddatetime,status,send_to);// Refresh the table with the new filter parameters
});
var WhatsadataTable;

function fill_datatables_whatsapp(startdatetime = '', enddatetime = '') {   
    var id = $('#inte_id').val();
    var startdatetime = $('#startdatetime').val();
    var enddatetime = $('#enddatetime').val();
   
    // Destroy existing DataTable instance before reinitializing
    if ($.fn.DataTable.isDataTable('#whatsapp_table')) {
        $('#whatsapp_table').DataTable().destroy();
    }

    WhatsadataTable = $('#whatsapp_table').DataTable({
        "processing": false,
        "serverSide": true,
        "order": [],
        "pageLength": 25, // Set default number of records per page to 25
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "searching": false,
        "ajax": {
            url: "omnichannel_config/fetchData.php",
            type: "POST",
            data: function(d) {  
                // Initially send empty filters until form is submitted
                d.startdatetime = startdatetime;
                d.enddatetime = enddatetime;
                d.phone = '';
                d.ICASEID = '';
                d.send_from = '';
                d.send_name = '';
                d.id = id;
                d.action = 'whatsapp';
            },
            error: function(xhr, error, thrown) {  
                console.log("AJAX Error: ", xhr.responseText);
            }
        },
        "createdRow": function(row, data, dataIndex) {
            var clr = data[9]; // Assuming clr value 
            if (clr == 'yellow') {
                // $(row).addClass('flag-yellow');
            } else if (clr == 'red') {
                $(row).addClass('mail-row');
            } else if (clr == 'green') {
                // $(row).addClass('flag-green');
            }
        },
        "drawCallback": function(settings) {
            // Reinitialize Colorbox for new elements
            $(".ico-interaction2").colorbox({
                iframe: true,
                innerWidth: 1000,
                innerHeight: 550
            });
        }
    });
}

// Initialize DataTable with no filters on page load
fill_datatables_whatsapp();

// Auto-refresh table data every 10 seconds
setInterval(function() {
    if ($.fn.DataTable.isDataTable('#whatsapp_table')) {
        WhatsadataTable.ajax.reload(null, false); // Keep pagination and refresh data
    }
}, 10000);

// Form submission event to update filters and reload DataTable
$('#post_whatsapp').submit(function(e) {
    e.preventDefault();

    var startdatetime = $('#startdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var phone = $('#phone').val();
    var ICASEID = $('#ICASEID').val();
    var send_from = $('#send_from').val();
    var send_name = $('#send_name').val();

    // Destroy existing DataTable instance
    $('#whatsapp_table').DataTable().destroy();

    // Reinitialize DataTable with new filter values
    fill_datatables_whatsapp(startdatetime, enddatetime, phone, ICASEID, send_from, send_name);
});

    
    
});
// Whatsapp code end
    $('#btn_delete').click(function() {
        if (confirm("Are you sure you want to delete this Email id?")) {
            var id = [];
            $(':checkbox:checked').each(function(i) {
                id[i] = $(this).val();
            });
            if (id.length === 0) //tell you if the array is empty
            {
                alert("Please Select atleast one checkbox");
            } else {
                $.ajax({
                    url: 'delete_emailqueue.php',
                    method: 'POST',
                    data: {
                        id: id
                    },
                    success: function(data) {
                        alert("Delete sucessfully!!!");
                        location.reload();
                    }
                });
            }
        } else {
            return false;
        }
    });
    
   //javascript functions  for web_email_complaint 
    //    $('.example').DataTable({
    //     "aaSorting": [],
    //     "ordering": false,
    //     "pageLength": 30,
    //     "dom": 'rtip'
    // });




    // Check or Uncheck All checkboxes
    $("#checkall").change(function() {
        var checked = $(this).is(':checked');
        if (checked) {
            $(".checkbox").each(function() {
                $(this).prop("checked", true);
            });
        } else {
            $(".checkbox").each(function() {
                $(this).prop("checked", false);
            });
        }
    });

    // Changing state of CheckAll checkbox 
    $(".checkbox").click(function() {

        if ($(".checkbox").length == $(".checkbox:checked").length) {
            $("#checkall").prop("checked", true);
        } else {
            $("#checkall").removeAttr("checked");
        }

    });


   // $('.date_class').datetimepicker();

});
//shifted from web_email_complaint.php
function clearText(field) {
    if (field.defaultValue == field.value) field.value = '';
    else if (field.value == '') field.value = field.defaultValue;
}
function CenterWindow(windowWidth, windowHeight, windowOuterHeight, url, wname, features) {
    var centerLeft = parseInt((window.screen.availWidth - windowWidth) / 2);
    var centerTop = parseInt(((window.screen.availHeight - windowHeight) / 2) - windowOuterHeight);

    var misc_features;
    if (features) {
        misc_features = ', ' + features;
    } else {
        misc_features = ', status=no, location=no, scrollbars=yes, resizable=yes';
    }
    var windowFeatures = 'width=' + 500 + ',height=' + 140 + ',left=' + centerLeft + ',top=' + centerTop +
    misc_features;
    var win = window.open(url, wname, windowFeatures);
    win.focus();
    return win;
}
function Centerss(windowWidth, windowHeight, windowOuterHeight, url, wname, features) {
    var centerLeft = parseInt((window.screen.availWidth - windowWidth) / 2);
    var centerTop = parseInt(((window.screen.availHeight - windowHeight) / 2) - windowOuterHeight);

    var misc_features;
    if (features) {
        misc_features = ', ' + features;
    } else {
        misc_features = ', status=no, location=no, scrollbars=yes, resizable=yes';
    }
    var windowFeatures = 'width=' + 600 + ',height=' + 500 + ',left=' + centerLeft + ',top=' + centerTop +
    misc_features;
    var win = window.open(url, wname, windowFeatures);
    win.focus();
    return win;
}
    
function delUser(id) {
    // alert(id);
    if (confirm("Are you sure to delete?")) {
        location.href = "web_email_complaint.php?act=del&id=" + id;
    }
}
function editcase(url) {
    window.open(url, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=100,left=200,width=500,height=300");
}

$('.set_button').click(function() {
  localStorage.setItem('session_set', '1');
});


function delUser(id) {
    // alert(id);
    if (confirm("Are you sure to delete?")) {
        location.href = "web_queue.php?act=del&id=" + id;
    }
}
function editcase(url) {
    window.open(url, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=100,left=200,width=500,height=300");
}

$('.reset_button').click(function() {
  localStorage.clear();
  var Token = btoa('email_complaint');
    window.location.href='omni_channel.php?token='+encodeURIComponent(Token);

});
//javascript functions  for subjectpopup.php to check Mail and delete mail 
function cheackMail(url)
{
	$.ajax({
      url: 'checkMail.php',
      type: 'post',
      data: {'id': Mid,'type': 'email'},
      success: function(data, status) {
      	$("#success-msg").html(data);
      	if(data!='') { return false; } 
      	else 
      	{ 
      		window.opener.location = url; window.close(); 
      	}
      },
      error: function(xhr, desc, err) {
        console.log(xhr);
        console.log("Details: " + desc + "\nError:" + err);
      }
    }); // end ajax call
}
function deleteMail() {
    if (confirm("Are you sure to delete this mail?")) {
        $.ajax({
            url: 'checkMail.php',
            type: 'post',
            data: {'id': Mid, 'del': 'del', 'type': 'email'},
            success: function(data, status) {
                $("#success-msg").html(data);
                if (data != '') {
                    return false;
                } else {
                    // Reload the page
                    location.reload();
                }
            },
            error: function(xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    }
}
//function for web_twitter.php to move the tweet to junkbox
function movetojunkbox(caseid) {
    if (confirm("Are you sure, the case should move to junkbox?")) {
        $.ajax({
            url: 'web_case_status_update.php',
            type: 'post',
            data: {
                'id': caseid,
                'type': 'twitter'
            },
            success: function(data, status) {
                alert(data); //return false;
                var encodedToken = btoa('twitter');
                window.location = 'omni_channel.php?token='+ encodeURIComponent(encodedToken); 
                /*   setTimeout(function(){
                                     window.location.href='admin-twitter_requests1.php';
                                  },2000);*/

                //$("#success-msg").html(data);
                /*if(data!='') { return false; } 
                else 
                { 
                  window.opener.cfrm.submit(); window.close();
                }*/
            },
            error: function(xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        }); // end ajax call
    }
}
//function to delete the user in twitter 
function delUser(id) {
    if (confirm("Are you sure to delete?")) {
        location.href = "<?=$_SERVER[PHP_SELF] ?>?act=del&id=" + id;
    }
}
// Function to refresh the page
function refreshPage() {
    location.reload();
}
// Set the interval for refresh (2000 milliseconds = 10 seconds)
// setInterval(refreshPage, 150000);

// Changing state of CheckAll checkbox 
$(".channel_type_post").click(function() {
    if($(this).data('id') == '1'){
        $('.facebook_titile').text('Facebook Post');
        $('.messenger_div').removeClass('channel_active');
        $('.post_div').addClass('channel_active');
    }else{
        $('.facebook_titile').text('Facebook Messanger');
        $('.messenger_div').addClass('channel_active');
        $('.post_div').removeClass('channel_active');
    }
});