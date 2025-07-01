<?php
/***
 * Content Messaging For SMS
 * Author: Farhan Akhtar
 * Last Modified On : 17-10-2024
 * Please do not modify this file without permission.
 **/
include_once "../../config/web_mysqlconnect.php"; // Include database connection file
$name = $_REQUEST['name'];
$phone = $_REQUEST['phone'];

$customerid = $_REQUEST['customerid'];
$caseid = $_REQUEST['caseid'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Messaging :: SMS</title>
    <!-- Bootstrap CSS -->
    <link href="<?= $SiteURL ?>public/bootstrap/bootstrap.min.css" rel="stylesheet">
    <style>
        ::-moz-focus-inner {
            padding: 0;
            border: 0;
        }

        :-moz-placeholder {
            color: #879fa6 !important;
        }

        ::-webkit-input-placeholder {
            color: #879fa6;
        }

        :-ms-input-placeholder {
            color: #879fa6 !important;
        }

        body {
            font: 12px/20px 'Lucida Grande', Verdana, sans-serif;
            color: #404040;
            /* background: #e4ecef; */
        }

        input,
        textarea,
        select,
        label {
            font-family: inherit;
            font-size: 12px;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        .contact {
            position: relative;
            margin: 20px auto 30px;
            padding: 5px;
            width: 320px;
            background: #eef5f7;
            border: 1px solid #cfd5da;
            border-bottom-color: #ccd1d6;
            border-radius: 3px;
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        }

        .contact-inner {
            padding: 25px;
            background: white;
            border-radius: 2px;
        }

        .contact:before,
        .contact:after,
        .contact-inner:before,
        .contact-inner:after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -6px;
            width: 1px;
            height: 1px;
            border: outset transparent;
            border-width: 12px 14px 0;
            border-top-style: solid;
            -webkit-transform: rotate(360deg);
        }

        .contact:before {
            margin-top: 1px;
            border-top-color: #d8e1e6;
        }

        .contact-inner:before {
            border-top-color: #ccd1d6;
        }

        .contact-inner:after {
            margin-top: -1px;
            border-top-color: #eef5f7;
        }

        .contact:after {
            margin-top: -8px;
            border-top-color: white;
        }

        .contact-input {
            overflow: hidden;
            margin-bottom: 20px;
            padding: 5px;
            /* background: #eef7f9; */
            border-radius: 2px;
        }

        .contact-input>input,
        .contact-input>textarea {
            display: block;
            width: 100%;
            height: 29px;
            padding: 0 9px;
            color: #4d5a5e;
            background: white;
            border: 1px solid #cfdfe3;
            border-bottom-color: #d2e2e7;
            border-radius: 2px;
            -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05), 0 1px rgba(255, 255, 255, 0.2);
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05), 0 1px rgba(255, 255, 255, 0.2);
        }

        .contact-input>input:focus,
        .contact-input>textarea:focus {
            border-color: #93c2ec;
            outline: 0;
            -webkit-box-shadow: 0 0 0 2px #e1ecf5;
            box-shadow: 0 0 0 2px #e1ecf5;
        }

        .lt-ie9 .contact-input>input,
        .lt-ie9 .contact-input>textarea {
            line-height: 27px;
        }

        .contact-input>textarea {
            padding: 4px 8px;
            height: 90px;
            line-height: 20px;
            resize: none;
        }

        .select {
            display: block;
            position: relative;
            overflow: hidden;
            background: white;
            border: 1px solid #d2e2e7;
            border-bottom-color: #c5d4d9;
            border-radius: 2px;
            background-image: -webkit-linear-gradient(top, #fcfdff, #f2f7f7);
            background-image: -moz-linear-gradient(top, #fcfdff, #f2f7f7);
            background-image: -o-linear-gradient(top, #fcfdff, #f2f7f7);
            background-image: linear-gradient(to bottom, #fcfdff, #f2f7f7);
            -webkit-box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
        }

        .select:before,
        .select:after {
            content: '';
            position: absolute;
            right: 11px;
            width: 0;
            height: 0;
            border-left: 3px outset transparent;
            border-right: 3px outset transparent;
        }

        .select:before {
            top: 10px;
            border-bottom: 3px solid #7f9298;
        }

        .select:after {
            top: 16px;
            border-top: 3px solid #7f9298;
        }

        .select>select {
            position: relative;
            z-index: 2;
            width: 112%;
            height: 29px;
            line-height: 17px;
            padding: 5px 9px;
            padding-right: 0;
            color: #80989f;
            background: transparent;
            background: rgba(0, 0, 0, 0);
            border: 0;
            -webkit-appearance: none;
        }

        .select>select:focus {
            color: #4d5a5e;
            outline: 0;
        }

        .contact-submit {
            text-align: right;
        }

        .contact-submit>input {
            display: inline-block;
            vertical-align: top;
            padding: 0 14px;
            height: 29px;
            font-weight: bold;
            color: #729fb2;
            text-shadow: 0 1px rgba(255, 255, 255, 0.5);
            background: #deeef4;
            border: 1px solid #bed6e3;
            border-bottom-color: #accbd9;
            border-radius: 15px;
            cursor: pointer;
            background-image: -webkit-linear-gradient(top, #e6f2f7, #d0e6ee);
            background-image: -moz-linear-gradient(top, #e6f2f7, #d0e6ee);
            background-image: -o-linear-gradient(top, #e6f2f7, #d0e6ee);
            background-image: linear-gradient(to bottom, #e6f2f7, #d0e6ee);
            -webkit-box-shadow: inset 0 1px rgba(255, 255, 255, 0.2), 0 1px 1px rgba(0, 0, 0, 0.06), 0 0 0 4px #eef7f9;
            box-shadow: inset 0 1px rgba(255, 255, 255, 0.2), 0 1px 1px rgba(0, 0, 0, 0.06), 0 0 0 4px #eef7f9;
        }

        .contact-submit>input:active {
            color: #6a95a9;
            text-shadow: 0 1px rgba(255, 255, 255, 0.3);
            background: #c9dfe9;
            border-color: #a3bed0 #b5ccda #b5ccda;
            -webkit-box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px rgba(255, 255, 255, 0.2), 0 0 0 4px #eef7f9;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px rgba(255, 255, 255, 0.2), 0 0 0 4px #eef7f9;
        }

        .title {
            font-size: large;
            font-weight: 600;
            color: lightskyblue
        }

        /* Style 7
            ----------------------------- */
        /* .seven h1 {
            text-align: center;
            font-size: 22px;
            font-weight: 300;
            color: #222;
            letter-spacing: 2px;
            text-transform: uppercase;
            display: grid;
            grid-template-columns: 1fr max-content 1fr;
            grid-gap: 20px;
            align-items: center;
        }

        .seven h1:after,
        .seven h1:before {
            content: " ";
            display: block;
            border-bottom: 1px solid #c50000;
            border-top: 1px solid #c50000;
            height: 5px;
            background-color: #f8f8f8;
        } */
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- <div class="col-sm-12">
                <div class="seven">
                    <h1>Content Messaging</h1>
                </div>
            </div> -->
            <div class="col-sm-12" id="contactContainer" style="position: relative;">
            <span id="closeButton" style="position: absolute; top: 10px; right: 15px; font-size: 20px; cursor: pointer;">Close</span>

                <form action="" class="contact" id="contactForm">
                    <fieldset class="contact-inner">
                         <!-- Close Icon -->
   
                    <p class="contact-input" style="text-align:center"><span class="title">SMS</span> <img src="<?= $SiteURL ?>public/images/sms.svg" alt="SMS" style="height:30px"></p>
                        <input type="hidden" name="fullname" id="fullname" value="<?=$name?>">
                        <p class="contact-input">
                            <input type="text" name="name" id="textInput" placeholder="Type Your Name..." readonly maxlength="20">
                            <input type="hidden" name="customerid" id="customerid"  value="<?=$customerid?>">
                            <input type="hidden" name="caseid" id="caseid"  value="<?=$caseid?>">
                        </p>
                        <p class="contact-input">
                            <input type="text" name="phone" id="intInput" placeholder="Type Your Phone No..." value="<?=$phone?>" readonly maxlength="20">
                        </p>

                        <p class="contact-input">
                            <textarea name="myTextarea" id="myTextarea" placeholder="Type Your Message..." autofocus></textarea>
                            <small id="charCount">0 / 200 characters</small> <!-- Character counter -->
                        </p>

                        <p class="contact-submit">
                            <input type="submit" name="submit" id="submitButton" value="Send Message">
                        </p>
                    </fieldset>
                </form>

            </div>
        </div>
    </div>

</body>
<script type="text/javascript" src="<?= $SiteURL ?>public/js/jquery-3.7.1.min.js"></script>
<script type="text/javascript" src="<?= $SiteURL ?>public/bootstrap/bootstrap.bundle.min.js"></script>

<!-- JavaScript to close the window -->
<script>
    // Close window on cross icon click
    document.getElementById("closeButton").addEventListener("click", function () {
        window.close();
    });
</script>

<script type="text/javascript">
$(document).ready(function() {

    let fullname = $("#fullname").val().trim();
    if(fullname !=''){
        $("#textInput").val(fullname);
    }

    const maxChars = 200; // Set the max character limit

    // Allow only text (letters and spaces)
    $('#textInput').on('keypress', function(event) {
        const char = String.fromCharCode(event.which);
        const regex = /^[A-Za-z\s]*$/; // Regex for letters and spaces

        if (!regex.test(char)) {
            event.preventDefault(); // Prevent input if it doesn't match
        }
    });

    // Allow only integers
    $('#intInput').on('keypress', function(event) {
        const char = String.fromCharCode(event.which);
        const regex = /^[0-9]*$/; // Regex for numbers

        if (!regex.test(char)) {
            event.preventDefault(); // Prevent input if it doesn't match
        }
    });

    // Bind a keyup event to the textarea
    $('#myTextarea').keyup(function() {
            let textLength = $(this).val().length; // Get the current length of text

            // Update the character counter
            $('#charCount').text(textLength + " / " + maxChars + " characters");

            // If the text exceeds the maxChars limit
            if (textLength > maxChars) {
                // Trim the text to the max limit
                $(this).val($(this).val().substring(0, maxChars));

                // Update the character counter after trimming
                $('#charCount').text(maxChars + " / " + maxChars + " characters");
            }
        });

    // Submit form via AJAX
    $('#contactForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Disable the submit button to prevent double clicks
        $('#submitButton').attr('disabled', true).text('Sending...');

        // Collect form data
        var formData = {
            name: $('input[name="name"]').val().trim(),
            phone: $('input[name="phone"]').val().trim(),
            reply: $('textarea[name="myTextarea"]').val().trim(),
            caseid:$('#caseid').val(),
            customerid:$('#customerid').val(),
            action:'SMS'
        };

        // Optional: Basic validation (ensure fields are not empty)
        if (formData.name === "" || formData.phone === "" || formData.reply === "") {
            alert("Please fill out all fields.");
            $('#submitButton').attr('disabled', false).text('Send Message');
            return;
        }

        // Send AJAX request
        $.ajax({
            url: 'ContentMessagingAPI.php', // Replace with your server endpoint
            type: 'POST',
            data: formData,
            dataType: 'json',
            cache: false, // Disable cache for the request
            success: function(response) {
                if (response.status === "success") {
                    // Handle success response
                    alert('Message sent successfully!');
                    $('#contactForm')[0].reset();
                    $('#charCount').text('0 / 200 characters'); // Reset character counter
                } else {
                    alert('Server responded with an error: ' + response.message || 'No Response');
                }
            },
            error: function(xhr, status, error) {
                alert('There was an error sending your message. Please try again later.');
                console.log("Error: ", error);
                console.log("Status: ", status);
                console.log("Response: ", xhr.responseText);
            },
            complete: function() {
                $('#submitButton').attr('disabled', false).text('Send Message');
            }
        });

    });
     
});
</script>

</html>