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
            jQuery("body").on('click','.submit_facebook_config',this.HandleFacebookSubmit);
            jQuery("body").on('click','.Refresh',this.HandleRefresh);

            jQuery("body").on('click','.twitter_debug_channel',this.handleTwitterSubmit);
            jQuery("body").on('click','.sms_debug_channel',this.handleSMSSubmit);
            jQuery("body").on('click','.facebook_debug_channel',this.handleFaceBookSubmit);
            jQuery("body").on('click','.whatsapp_debug_channel',this.handleWhatsAppSubmit);
            jQuery("body").on('click','.messenger_debug_channel',this.handleMessengerSubmit);
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
                $('.facebook_form').hide();
            }else if(select == 'Imap'){
                $('.Imap_form').show();
                $('.Smtp_form').hide();
                $('.twitter_form').hide();
                $('.sms_form').hide();  
                $('.whatsapp_form').hide(); 
                $('.messenger_form').hide(); 
                $('.facebook_form').hide();
            }else if(select == 'Twitter'){
                $('.twitter_form').show();
                $('.Imap_form').hide();
                $('.Smtp_form').hide(); 
                $('.sms_form').hide();  
                $('.whatsapp_form').hide();  
                $('.messenger_form').hide(); 
                $('.facebook_form').hide();           
            }else if(select == 'SMS'){
                $('.sms_form').show();
                $('.twitter_form').hide();
                $('.Imap_form').hide();
                $('.Smtp_form').hide(); 
                $('.whatsapp_form').hide(); 
                $('.messenger_form').hide(); 
                $('.facebook_form').hide();
            }else if(select == 'Whatsapp'){
                $('.whatsapp_form').show();
                $('.sms_form').hide();
                $('.twitter_form').hide();
                $('.messenger_form').hide(); 
                $('.Imap_form').hide();
                $('.Smtp_form').hide(); 
                $('.facebook_form').hide();
            }else if(select == 'Facebook'){
                $('.facebook_form').show();
                $('.whatsapp_form').hide();
                $('.sms_form').hide();
                $('.twitter_form').hide();
                $('.messenger_form').hide(); 
                $('.Imap_form').hide();
                $('.Smtp_form').hide(); 
            }else if(select == 'Messenger'){
                $('.facebook_form').hide();
                $('.whatsapp_form').show();
                $('.sms_form').hide();
                $('.twitter_form').hide();
                $('.messenger_form').hide(); 
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
            console.log('i am here');
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
            }else if(select == 'onfonmedia'){
                $('.sms_onfonmedia').show();
                $('.sms_zambia').hide();
                $('.exotel').hide();
                $('.sms_common').show();
            }else if(select == 'exotel'){
                $('.sms_onfonmedia').hide();
                $('.sms_zambia').hide();
                $('.exotel').show();
                $('.sms_common').hide();
            }
        }
    }
    OmniCommon.init();
});
//script code shifted from web_email_complaint.php 
    // initialize_datatables();
    function initialize_datatables(startdatetime = '', enddatetime = '', email = '', iallstatus = '',classification = '' , sentiment = '') {
        var id = $('#inte_id').val();
        var dataTable = $('#email_complaint_table').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 25, // Set default number of records per page to 30
            "lengthMenu": [ [10, 25, 50, 100,  -1], [10, 25, 50,100, "All"] ],
            "searching": false,
            "ajax": {
                url: "omnichannel_config/fetchDataEmailComplaint.php",
                type: "POST",
                data: function(d) {
                    d.startdatetime = startdatetime;
                    d.enddatetime = enddatetime;
                    d.email = email;
                    d.iallstatus = iallstatus;
                    d.classification = classification;
                    d.sentiment = sentiment;
                    d.id = id;
                    d.action = 'email_complaint';
                }
            },
            "createdRow": function( row, data, dataIndex) {
                var clr = data[9]; // Assuming clr value is in the nine column
               
                if (clr == 'yellow') {
                    $(row).addClass('flag-yellow'); 
                } else if (clr == 'red') {
                    $(row).addClass('flag-red'); 
                }else if (clr == 'green') {
                    $(row).addClass('flag-green'); 
                }
            }
        });
        return dataTable;
    }   
$(document).ready(function() {
    // Ths code for datatable reload fetch lates data
     // Set an interval to refresh data every 2 seconds

     // Initialize the DataTable on page load
    // var dataTable = initialize_datatables();
    initialize_datatables();
    // Set an interval to refresh data every 9 seconds
    setInterval(function() {
        // Get the current page info before reloading
        var dataTable = $('#email_complaint_table').DataTable();
        var pageInfo = dataTable.page.info();

        // Reload the data without resetting the paging
        dataTable.ajax.reload(null, false);

        // After the reload, set the page back to the original page
        dataTable.page(pageInfo.page).draw(false);
    }, 20000); // 9000 milliseconds = 9 seconds

    $('#post_complaint').submit(function(e) {
        e.preventDefault();
        var startdatetime = $('#startdatetime').val();
        var enddatetime = $('#enddatetime').val();
        var email = $('#email').val();
        var iallstatus = $('#iallstatus').val();
        var classification = $('.classification').val();
        var sentiment = $('#sentiment').val();
    
        // Destroy DataTable instance to reinitialize with new data
        $('#email_complaint_table').DataTable().destroy();
    
        // Call initialize_datatables function with appropriate filter parameters
        initialize_datatables(startdatetime, enddatetime, email, iallstatus ,classification ,sentiment);
    });
    
    $('#reset_button_complaint').click(function () {
        $('#email_complaint_table').DataTable().destroy();
        $('#startdatetime,#enddatetime,#email,#iallstatus,.classification','#sentiment').val('');
        initialize_datatables();
    });

    fill_datatables_enquiry();
    // Set an interval to refresh data every 9 seconds
    setInterval(function() {
        // Get the current page info before reloading
        var dataTable_Inquiry = $('#email_enquiry_table').DataTable();
        var pageInfo = dataTable_Inquiry.page.info();
        // Reload the data without resetting the paging
        dataTable_Inquiry.ajax.reload(null, false);
        // After the reload, set the page back to the original page
        dataTable_Inquiry.page(pageInfo.page).draw(false);
    }, 20000); // 9000 milliseconds = 9 seconds

    function fill_datatables_enquiry(startdatetime = '', enddatetime = '', email = '', iallstatus = '',classification = '', sentiment = '') {
        var id = $('#inte_id').val();
        var dataTable = $('#email_enquiry_table').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [],
            "pageLength": 25, // Set default number of records per page to 30
            "lengthMenu": [ [10, 25, 50, 100,  -1], [10, 25, 50,100, "All"] ],
            "searching": false,
            "ajax": {
                url: "omnichannel_config/fetchDataEmailComplaint.php",
                type: "POST",
                data:{
                    startdatetime: startdatetime,
                    enddatetime: enddatetime,
                    email: email,
                    iallstatus: iallstatus,
                    classification : classification,
                    sentiment:sentiment,
                    id:id,
                    action:'email_enquiry'
                }
            },
            "createdRow": function( row, data, dataIndex) {
                var clr = data[9]; // Assuming clr value is in the sixth column
            
                if (clr == 'yellow') {
                    $(row).addClass('flag-yellow'); 
                } else if (clr == 'red') {
                    $(row).addClass('flag-red'); 
                }else if (clr == 'green') {
                    $(row).addClass('flag-green'); 
                }
            }
        });
    }  
    $('#post_enquiry').submit(function(e) {
        e.preventDefault();
        var startdatetime = $('#startdatetime').val();
        var enddatetime = $('#enddatetime').val();
        var email = $('#email').val();
        var iallstatus = $('#iallstatus').val();
        var classification = $('.classification').val(); 
        var sentiment = $('#sentiment').val();
    
        console.log(sentiment);
        // Destroy DataTable instance to reinitialize with new data
        $('#email_enquiry_table').DataTable().destroy();
    
        // Call fill_datatables function with appropriate filter parameters
        fill_datatables_enquiry(startdatetime, enddatetime, email, iallstatus ,classification ,sentiment);
    });
     
    $('#reset_button_enquiry').click(function () {
        $('#email_enquiry_table').DataTable().destroy();
        $('#startdatetime,#enddatetime,#email,#iallstatus,.classification','#sentiment').val('');
        fill_datatables_enquiry();
    });

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
                    $(row).addClass('flag-yellow'); 
                } else if (clr == 'red') {
                    $(row).addClass('flag-red'); 
                }else if (clr == 'green') {
                    $(row).addClass('flag-green'); 
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
    
   
    fill_datatables_sms();
    function fill_datatables_sms(startdatetime = '', enddatetime = '', phone = ''){
        var id = $('#inte_id').val();
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
                    action:'sms'
                }
            },
           "createdRow": function( row, data, dataIndex) {
                var clr = data[5]; // Assuming clr value is in the sixth column
            
                if (clr == 'yellow') {
                    $(row).addClass('flag-yellow'); 
                } else if (clr == 'red') {
                    $(row).addClass('flag-red'); 
                }else if (clr == 'green') {
                    $(row).addClass('flag-green'); 
                }
            }
        });
    }
    $('#post_sms').submit(function(e) {
        e.preventDefault();
        var startdatetime = $('#startdatetime').val();
        var enddatetime = $('#enddatetime').val();
        var phone = $('#phone').val();
       
        // Destroy DataTable instance to reinitialize with new data
        $('#sms_table').DataTable().destroy();
    
        // Call fill_datatables function with appropriate filter parameters
        fill_datatables_sms(startdatetime, enddatetime, phone);
        console.log(startdatetime);
        console.log(enddatetime);
        console.log(phone);
    });
    
    $('#reset_button_sms').click(function () {
        $('#sms_table').DataTable().destroy();
        var startdatetime = $('#startdatetime').val();
        var enddatetime = $('#enddatetime').val();
        $('#phone').val('');
        fill_datatables_sms();
    });

    // Initialize DataTable on document ready
    $(document).ready(function() {
        console.log('fill_datatables_twitter');
        fill_datatables_twitter();
    });
    function fill_datatables_twitter(startdatetime = '', enddatetime = '', v_Screenname = '') {
        var id = $('#inte_id').val();
        var dataTable = $('#twitter_table').DataTable({
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
                    v_Screenname: v_Screenname,
                    id:id,
                    action:'twitter'
                }
            },
            "createdRow": function( row, data, dataIndex) {
                var clr = data[7]; // Assuming clr value 
               
                if (clr == 'yellow') {
                    $(row).addClass('flag-yellow'); 
                } else if (clr == 'red') {
                    $(row).addClass('flag-red'); 
                }else if (clr == 'green') {
                    $(row).addClass('flag-green'); 
                }
            },
            "drawCallback": function( settings ) {
                // Reinitialize Colorbox for new elements
                $(".ico-interaction2").colorbox({
                    iframe: true,
                    innerWidth: 800,
                    innerHeight: 600
                });
                // Reinitialize Colorbox for new elements
                // $(".openPopup").colorbox({iframe:true, width:"80%", height:"80%"});
            }
        });
    }   
    
$('#post_twitter').submit(function(e) {
    e.preventDefault();
    var startdatetime = $('#startdatetime').val();
    var enddatetime = $('#enddatetime').val();
    var v_Screenname = $('#v_Screenname').val();
   
    // Destroy DataTable instance to reinitialize with new data
    $('#twitter_table').DataTable().destroy();

    // Call fill_datatables function with appropriate filter parameters
    fill_datatables_twitter(startdatetime, enddatetime, v_Screenname);
});

$('#reset_twitter').click(function () {
    $('#twitter_table').DataTable().destroy();
    $('#startdatetime').val();
    $('#enddatetime').val();
    $('#v_Screenname').val('');
    
    // Reinitialize DataTable with default or no filters
    fill_datatables_twitter();
});

    
    fill_datatables_whatsapp();
    function fill_datatables_whatsapp(startdatetime = '', enddatetime = '', phone = '')
    {   
        var id = $('#inte_id').val();
        var dataTable = $('#whatsapp_table').DataTable({
            "processing": false,
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
                    id:id,
                    action:'whatsapp'
                }
            },
            "createdRow": function( row, data, dataIndex) {
                var clr = data[9]; // Assuming clr value 
               
                if (clr == 'yellow') {
                    $(row).addClass('flag-yellow'); 
                } else if (clr == 'red') {
                    $(row).addClass('flag-red'); 
                }else if (clr == 'green') {
                    $(row).addClass('flag-green'); 
                }
            },
            "drawCallback": function( settings ) {
                // Reinitialize Colorbox for new elements
                $(".ico-interaction2").colorbox({
                    iframe: true,
                    innerWidth: 800,
                    innerHeight: 600
                });
                // Reinitialize Colorbox for new elements
                // $(".openPopup").colorbox({iframe:true, width:"80%", height:"80%"});
            }
        });
    }
    $('#post_whatsapp').submit(function(e) {
        e.preventDefault();
        var startdatetime = $('#startdatetime').val();
        var enddatetime = $('#enddatetime').val();
        var phone = $('#phone').val();
       
        console.log(startdatetime);
        console.log(enddatetime);
        console.log(phone);
        // Destroy DataTable instance to reinitialize with new data
        $('#whatsapp_table').DataTable().destroy();
    
        // Call fill_datatables function with appropriate filter parameters
        fill_datatables_whatsapp(startdatetime, enddatetime, phone);
    });
    
    $('#reset_button_whatsapp').click(function () {
        $('#whatsapp_table').DataTable().destroy();
        
        // Get current date
        var currentDate = new Date();
        
        // Get the start of the month
        var startOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        
        // Format start of the month to "Y-m-01 00:00:00"
        var startOfMonthFormatted = startOfMonth.getFullYear() + '-' + 
            ('0' + (startOfMonth.getMonth() + 1)).slice(-2) + '-01 00:00:00';
        
        // Format current date to "Y-m-d 23:59:59"
        var currentDateFormatted = currentDate.getFullYear() + '-' + 
            ('0' + (currentDate.getMonth() + 1)).slice(-2) + '-' + 
            ('0' + currentDate.getDate()).slice(-2) + ' 23:59:59';
        
        // Set the values of the date input fields
        $('#startdatetime').val(startOfMonthFormatted); // Set to start of the current month
        $('#enddatetime').val(currentDateFormatted); // Set to current date with end time
        
        $('#phone').val('');
        
        // Reinitialize DataTable with default or no filters
        fill_datatables_whatsapp();
    });
  
    $('#btn_delete').click(function() {

        if (confirm("Are you sure you want to delete this Email id?")) {
            var id = [];

            $(':checkbox:checked').each(function(i) {
                id[i] = $(this).val();
                // alert(id);
            });

            if (id.length === 0) //tell you if the array is empty
            {
                alert("Please Select atleast one checkbox");
            } else {

            // alert(id);
                $.ajax({
                    url: 'delete_emailqueue.php',
                    method: 'POST',
                    data: {
                        id: id
                    },
                    success: function(data) {

                        //alert(data);
                        // if (data == 1) {
                            alert("Delete sucessfully!!!");
                            location.reload();
                        // }

                    }

                });
            }

        } else {
            return false;
        }
    });

    
    $('#serviceable').on('change', function (e) {
        var value = $(this).val();
        e.preventDefault();
    
        if (value >= 1) {
            if (confirm("Are you sure you want to classify this one ?")) {
                var id = [];
    
                $(':checkbox:checked').each(function (i) {
                    id[i] = $(this).val();
                });
    
                if (id.length === 0) {
                    alert("Please Select at least one checkbox");
                } else {
                    $.ajax({
                        url: 'omnichannel_config/servicable_emailqueue.php',
                        method: 'POST',
                        data: {
                            id: id,
                            value: value
                        },
                        success: function (data) {
                            console.log(data);
                            if (data <= 4) {
                                location.reload();
                            }
                        },
                        error: function (data) {
                            console.log(data);
                        }
                    });
                }
            } else {
                return false;
            }
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