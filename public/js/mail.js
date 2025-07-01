$(document).ready(function(){

    fill_datatable();
    $('li').click(function(){
        $('li').removeClass('active'); // Remove active class from all li elements
        $(this).addClass('active'); // Add active class to the clicked li element
    });

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

    // Move to trash
    $('#btn_trash').click(function() {
        
            var id = [];
            var action = 'trash';
            $(':checkbox:checked').each(function(i) {
                id[i] = $(this).val();
            });

            if (id.length === 0) //tell you if the array is empty
            {
                alert("Please Select atleast one checkbox");
            } else {
                if (confirm("Are you sure you want to move this to trash?")) {
                    $.ajax({
                        url: 'delete_emailqueue.php',
                        method: 'POST',
                        data: {
                            id: id,action:action
                        },
                        success: function(data) {
                                alert(data);
                                location.reload();
                        }

                    });

                } else {
                    return false;
                }
            }


    });

    // Delete
    $('#btn_delete').click(function() {
        
            var id = [];
            var action = 'delete';
            $(':checkbox:checked').each(function(i) {
                id[i] = $(this).val();
            });

            if (id.length === 0) //tell you if the array is empty
            {
                alert("Please Select atleast one checkbox");
            } else {
                if (confirm("Are you sure you want to delete this?")) {
                    $.ajax({
                        url: 'delete_emailqueue.php',
                        method: 'POST',
                        data: {
                            id: id,action:action
                        },
                        success: function(data) {
                                alert(data);
                                location.reload();
                        }

                    });

                } else {
                    return false;
                }
            }


    });

    // spam
    $('#btn_spam').click(function() {
            var id = [];
            var action = 'spam';
            $(':checkbox:checked').each(function(i) {
                id[i] = $(this).val();
            });

            if (id.length === 0) //tell you if the array is empty
            {
                alert("Please Select atleast one checkbox");
            } else {
                if (confirm("Are you sure you want to mark this as spam?")) {
                    $.ajax({
                        url: 'delete_emailqueue.php',
                        method: 'POST',
                        data: {
                            id: id,action:action
                        },
                        success: function(data) {
                                alert(data);
                                location.reload();
                        }

                    });

                } else {
                    return false;
                }
            }


    });

    $('#inbox').click(function(){
        $('#complaint_records').DataTable().destroy();
        fill_datatable();
    });

    $('#read').click(function(){
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var email = $("#emailid").val();
        $('#complaint_records').DataTable().destroy();
        fill_datatable(startdate,enddate,email,1,'','','','','');
    });

    $('#unread').click(function(){
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var email = $("#emailid").val();
        $('#complaint_records').DataTable().destroy();
        fill_datatable(startdate,enddate,email,'',1,'','','','');
    });

    $('#trash').click(function(){
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var email = $("#emailid").val();
        $('#complaint_records').DataTable().destroy();
        fill_datatable(startdate,enddate,email,'','','',1,'','');
    });

    $('#spam').click(function(){
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var email = $("#emailid").val();
        $('#complaint_records').DataTable().destroy();
        fill_datatable(startdate,enddate,email,'','',1,'','','');
    });

    $('#search-record').click(function(){
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();
        var email = $("#emailid").val();
        $('#complaint_records').DataTable().destroy();
        fill_datatable(startdate,enddate,email,'','','','','','');
    });


    // Close the dropdown if the user clicks outside of it
    window.onclick = function(event) {
        if (!event.target.matches('.dropbtn')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        var i;
        for (i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
            openDropdown.classList.remove('show');
            }
        }
        }
    }


    //modal for viewing mails
    $(document).on('click', '#btn_viewmail', function(e){

        e.preventDefault(); // Prevent default link behavior
        var id = $(this).data('id');
     
        $('#dispostion_type').val('');  // Clears the value of #dispostion_type
        $('#email_remark').val('');     // Clears the value of #email_remark

        $.ajax({
            url : 'mail_popup.php',
            method : 'post',
            dataType : 'json',
            data : {id : id , action : 'view_mail'},
            success : function(response){
                console.log(response);
                response.subject = $.trim(response.subject);
                response.content = $.trim(response.content);
                $('.email-body').css({
                    'background-color': '#f9f9f9',
                    'word-wrap': 'break-word'
                });
                $('.modal-title').html(response.subject)
                $('.mail-id').html(response.emailid)
                $('.email-date').html(response.emaildatetime)
                $('.email-body').html(response.content)
                $('.email-attachment').html(response.attachments)
                $('.modal-footer').html(response.caseinfo+response.replybtn)
                $('#channel_id').val(response.mid)
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Request Failed');
                console.error('Status:', textStatus);
                console.error('Error:', errorThrown);
                console.error('Response:', jqXHR.responseText);
            }

        });

        
    })

    $(document).on('click', '#createcase', function(e) {

        var url = $(this).data('param1');
        var id = $(this).data('param2');
        checkMail(url,id);

    })

    $('#create_disposition').click(function(){
  
        const channel_id= $('#channel_id').val();
        const channel_type = $('#channel_type').val();
        const dispositionType = $('#dispostion_type').val();
        const emailRemark = $('#email_remark').val();

        if (!dispositionType) {
            alert('Please select a disposition type.');
            $('#dispostion_type').focus();
            return;
        }

        if (!emailRemark.trim()) {
            alert('Please enter a remark.');
            $('#email_remark').focus();
            return;
        }

        $.ajax({
            url: 'fetch_omnichannels.php',
            method: 'POST',
            data: {
                action :'dispostion_channel_insert',
                dispostion_type:dispositionType,
                channel_id:channel_id,
                remarks:emailRemark,
                channel_type:channel_type
            },
            success: function(data) {
                location.reload();
            }
        });	
    })

    
    function fill_datatable(startdate = '', enddate = '' , email = '' , read = '', unread = '' , spam = '', trash ='' , classify = '', sentiment = ''){
        $('#complaint_records').DataTable({
            "processing" : true,
            "serverSide" : true,
            "order" : [],
            "ordering": false,
            "searching" : false,
            "ajax" : {
                url:"fetch_omnichannels.php",
                type:"POST",
                data:{
                    action:'mail_complaint',
                    startdate:startdate, 
                    enddate:enddate,
                    email:email,
                    read:read,
                    unread:unread,
                    spam:spam,
                    trash:trash
                }
            }
        });
    }

    function checkMail(url,id){
        $.ajax({
        url: 'checkMail.php',
        type: 'post',
        data: {id: id,type: 'email'},
        success: function(data) {
            console.log(data)
            $(".msg-alert").html(data);

            if(data!='') { return false; } 
            else { 
                // Open the URL in a new tab
                window.open(url, '_blank');
                // window.close(); 
            }
        },
        error: function(xhr, desc, err) {
            console.log(xhr);
            console.log("Details: " + desc + "\nError:" + err);
        }
        }); 
    }

});




/* When the user clicks on the button, toggle between hiding and showing the dropdown content */
function myDropdown() {
    document.getElementById("myDropdown").classList.toggle("show");
}
  

