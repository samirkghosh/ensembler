$(document).ready(function() {
    // search filter for finding user 
    function filterUsers() {
        var input, filter, ul, li, p, txtValue;
        input = $('#searchInput').val();  // Get the search input value
        filter = input.toUpperCase();  // Convert the search input to uppercase
        ul = $("#userList");  // Get the user list
        li = ul.find('li');  // Find all list items within the user list

        // Loop through all list items, and hide those who don't match the search query
        li.each(function() {
            p = $(this).find("p")[0];  // Find the <p> tag within the list item
            txtValue = p.textContent || p.innerText;  // Get the text content of the <p> tag
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                $(this).show();  // Show the list item if it matches the filter
            } else {
                $(this).hide();  // Hide the list item if it doesn't match
            }
        });
    }

    // Attach the filter function to the keyup event of the search input field
    $('#searchInput').on('keyup', filterUsers);
    function playNotificationSound() {
        console.log('Playing notification sound');
        
        const audio = new Audio('multi_chat/message.mp3'); // Update the path to your sound file
        audio.play();
    }
  
    $("#image_event").on('click', function () {
        $('#file').trigger('click');  
    });

    // Change the icon color when a file is selected
    $(document).on("change", "#file", function(){
        $('#image_event').css("color", "white"); // Change icon color to blue when a file is selected
        $('#attachFile').css("background-color", "#2c0b7f"); // Change icon color to blue when a file is selected
       
        //send the attachment as soon as the attachment is selected
        sendMessage();
    });
   
    // Send message on button click
    // Function to send the message
    function sendMessage() {
        var message = $('#messageText').val();
        var senderId = $('#senderId').val();
        var sessionId = $('#sessionId').val();
        var receiverId = $('#receiverId').val();
        var fileInput = $('#file')[0];
        var formData = new FormData();
        
        // Append data to FormData
        formData.append("action", "insert_message");
        formData.append("message", message);
        formData.append("sender_id", senderId);
        formData.append("session_id", sessionId);
        formData.append("receiver_id", receiverId);

        if (fileInput.files.length > 0) {
            formData.append("attachment", fileInput.files[0]); // Add only the first file (you can extend to handle multiple files)
        } else {
            console.log("No file selected.");
        }

        $.ajax({
            url: "multi_chat/multichat_function.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#image_event').css("color", "white");
                $('#attachFile').css("background-color", "#a9e326"); // Change icon color to blue when a file is selected
       
                // Clear message input field after success
                $('#messageText').val('');
                try {
                    var jsonResponse = JSON.parse(response);
                    if (jsonResponse.status === 'success') {
                        playNotificationSound();
                        fetchChatMessages(sessionId); // Fetch new chat messages
                    } else {
                        alert(jsonResponse.message); // Show error message
                    }
                } catch (e) {
                    console.error("Parsing error: ", e); // Log the error for debugging
                    alert("Unexpected error occurred.");
                }
            },            
            error: function() {
                alert("Failed to send message. Please try again.");
            }
        });
    }

        // Bind the click event to the sendMessage function
        $('#sendMessage').click(sendMessage);

        // Bind the keyup event to the messageText textarea
        $('#messageText').keyup(function(event) {
            if (event.which === 13) { // Check if the Enter key is pressed
                event.preventDefault(); // Prevent the default action of Enter key
                sendMessage(); // Trigger the sendMessage function
            }
        });


       // Reset the icon color to original when the Send button is clicked
       $("button[type='submit']").on('click', function() {
        $('#image_event').css("color", "#d9d4cc"); // Reset color back to original
    });
    const selectedUserName = document.querySelector('#selectedUserName');
    const chatMessages = document.querySelector('#chatMessages');
    const receiverIdInput = document.querySelector('#receiverId');
    const sessionIdInput = document.querySelector('#sessionId');
    const dynamicUserDetails = document.querySelector('#dynamicUserDetails');
    let reloadInterval;
    const messageDetail = document.querySelector('#message-detail');
    
    
    function toggleMessageDetail(visible) {
        if (visible) {
            messageDetail.style.display = 'block'; // Show the chat container
        } else {
            messageDetail.style.display = 'none'; // Hide the chat container
        }
    }
    
    // Initially hide the message-detail division
    toggleMessageDetail(false);
    // Bind click event to dynamically loaded elements using event delegation
    document.addEventListener('click', function (event) {
        // Check if the click event happened on a '.person' element or inside it
        const target = event.target.closest('.person');
        if (target && target.closest('#userList')) {
            console.log('User list item clicked');
            
            const sessionId = target.getAttribute('data-session-id');
            const userId = target.getAttribute('data-user-id');
            const userName = target.getAttribute('data-user-name');
            const phone = target.getAttribute('data-phone');

            const senderId = document.querySelector('#senderId').value;
            // Update chat header with selected user name
            if (selectedUserName) {
                selectedUserName.textContent = userName;
            }

            // Set hidden input fields with selected user data
            if (receiverIdInput) receiverIdInput.value = userId;
            if (sessionIdInput) sessionIdInput.value = sessionId;

            // Set the phone input value dynamically
            $("#phone").val(phone);

            // Trigger AJAX to fetch chat messages
            fetchChatMessages(sessionId);
            setUserId(sessionId,senderId);
            clearInterval(reloadInterval); // Clear existing interval
            reloadInterval = setInterval(() => {
                fetchChatMessages(sessionId);
            }, 10000); // 10 seconds interval

            // Show the chat container
            toggleMessageDetail(true);
        }
    });

 
    //   redirect to create case page  
    $('#create_case').on('click', function (event) {
        event.preventDefault();
    
        // Capture the phone number value
        var phone = $("#phone").val();
        var chat_session = $('#sessionId').val();
    
    
        $.ajax({
            url: 'omnichannel_config/checkMail.php',
            type: 'post',
            data: { 'chat_session': chat_session, 'type': 'chat', 'phone': phone },
            success: function (data, status) {
                console.log(data);
                if (data.trim()) {
                    $("#success-msg").html(data);
                    return false;
                } else {
                    var new_case_manual = btoa('new_case_manual');
                    var url_new = '../CRM/helpdesk_index.php?token=' + encodeURIComponent(new_case_manual);
                    console.log(url_new);
                    window.open(url_new + '&mr=5&phone_number=' + encodeURIComponent(phone) + '&chatid=' + encodeURIComponent(chat_session));
                    parent.$.colorbox.close();
                }
            },
            error: function (xhr, desc, err) {
                console.log(xhr);
                console.log("Details: " + desc + "\nError:" + err);
            }
        });
    });
    
    function setUserId(sessionId,senderId){
        $.ajax({
            url: 'multi_chat/multichat_function.php', // The PHP file to handle the request
            type: 'POST', // The request method
            data: {
                action: 'setUserId', // The action to be processed in web_function.php
                chat_session_id: sessionId,
                senderId : senderId // The session ID to fetch messages
            }, success: function (data) {
                if (data.status === 'success') {
                    console.log("userid updated");
                }
            },
            error: function (error) {
               
                console.log("Error "); // Log full error details
            }
        }
    )};

    function scrollToBottom() {
        let chatMessages = document.getElementById("chatMessages");
        chatMessages.scrollTop = chatMessages.scrollHeight;
        console.log("scroll");
    }
    
    
    // Call this function whenever new messages are added
    
     // Function to fetch chat messages using AJAX
     function fetchChatMessages(sessionId) {
        console.log(sessionId);
        $.ajax({
            url: 'multi_chat/multichat_function.php',
            type: 'POST',
            data: {
                action: 'fetch_message',
                chat_session_id: sessionId
            },
            success: function(data) {
                try {
                    const responseData = typeof data === 'string' ? JSON.parse(data) : data;
                    
                    const chatMessages = document.getElementById("chatMessages");
    
                    if (responseData && responseData.length > 0) {
                        // Clear current chat messages
                        chatMessages.innerHTML = '';
    
                        let lastDisplayedDate = null;
    
                        responseData.forEach(function(message) {
                            const messageElement = document.createElement('li');
                            let messageClass = message.direction === 'IN' ? 'chat-left' : 'chat-right';
    
                            const time = new Date(message.create_datetime);
                            const timeText = time.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
                            const messageDate = new Date(message.create_datetime);
                            const today = new Date();
                            const yesterday = new Date(today);
                            yesterday.setDate(today.getDate() - 1);
    
                            let dateText = '';
                            if (messageDate.toDateString() === today.toDateString()) {
                                dateText = 'Today';
                            } else if (messageDate.toDateString() === yesterday.toDateString()) {
                                dateText = 'Yesterday';
                            } else {
                                dateText = messageDate.toLocaleDateString();
                            }
    
                            if (lastDisplayedDate !== dateText) {
                                const dateDiv = document.createElement('div');
                                dateDiv.classList.add('chat-date');
                                dateDiv.textContent = dateText;
                                chatMessages.appendChild(dateDiv);
                                lastDisplayedDate = dateText;
                            }
    
                            messageElement.classList.add(messageClass);
    
                            const textDiv = document.createElement('div');
                            textDiv.classList.add('chat-text');
    
                            if (message.message) {
                                const messageText = document.createElement('p');
                                messageText.textContent = message.message;
                                textDiv.appendChild(messageText);
                            }
    
                            if (message.attachment) {
                                const attachmentURL = "../../../" + message.attachment;
                                const attachmentLink = document.createElement('a');
                                attachmentLink.href = attachmentURL;
                                attachmentLink.target = "_blank";
                                attachmentLink.textContent = 'Attachment';
                                textDiv.appendChild(attachmentLink);
                            }
    
                            messageElement.appendChild(textDiv);
    
                            const hourDiv = document.createElement('div');
                            hourDiv.classList.add('chat-hour');
                            hourDiv.textContent = timeText;
    
                            if (message.direction === 'IN') {
                                messageElement.appendChild(hourDiv);
                            } else {
                                messageElement.insertBefore(hourDiv, textDiv);
                            }
    
                            chatMessages.appendChild(messageElement);
                            setTimeout(scrollToBottom, 100); // Slight delay to ensure rendering
                        });
    
                        setTimeout(scrollToBottom, 100); // Slight delay to ensure rendering
                    } else {
                        chatMessages.innerHTML = 'No messages found.';
                    }
                } catch (error) {
                    console.error('Error processing chat messages:', error);
                }
            }
        });
    }
    

    $("#close").click(function (e) {
        e.preventDefault();
        console.log('chat closed');
        var senderId = $('#receiverId').val();
        var sessionId = $('#sessionId').val();
        
        $.ajax({
            url: "multi_chat/multichat_function.php",
            method: "post",
            data: { 'id': senderId, 'chat_session_id': sessionId, 'action': 'close_chat' },
            dataType: 'JSON',
            success: function (data) {
                if (data.status === 'success') {
                    setTimeout(function () {
                       // alert("Redirecting to web chat...");
                        
                        var token = 'multi_chat'; // Replace with the actual token value you want to encode
                        var encodedToken = btoa(token); // Base64 encode the token
                        
                        var redirectUrl = 'multi_chat_index.php?token=' + encodedToken;
                        
                        window.location.href = redirectUrl; // Redirect to the new URL with the encoded token
                    }, 2000); // 2 seconds delay
                }
            },
            error: function (error) {
                console.log("AJAX error callback triggered"); // Log error callback
                console.log("Error details:", error); // Log full error details
            }
        });
    });
    
     // Function to load the user list
     function loadUserList() {
        console.log("hello");
        $.ajax({
            url: 'multi_chat/multichat_function.php',  // Ensure this is the correct path to your PHP file
            type: 'GET',  // Send a GET request
            data: { action: 'get_user_list' },  // Pass the action to identify the request
            success: function(response) {
                // console.log("done");
                // console.log(response);
                // Inject the HTML response into the user-list div
                $('.user-list').html(response);
            },
            error: function() {
                alert('Error loading user list.');
            }
        });
    }

    // Load the user list when the page loads
    loadUserList();

    // Set interval to refresh the user list every 10 seconds (10000 milliseconds)
    setInterval(loadUserList, 10000);
});