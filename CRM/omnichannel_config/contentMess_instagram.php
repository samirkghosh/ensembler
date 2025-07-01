<?php
/***
 * Content Messaging For Instagram
 * Author: Vastvikta Nishad
 * Last Modified On : 15-04-2025
 **/
include_once "../../config/web_mysqlconnect.php";
$customerid = $_REQUEST['customerid'];
$caseid = $_REQUEST['caseid'];
$instagramhandle = $_REQUEST['instagramhandle'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Messaging ::Instagram</title>
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


            background: #eebeec;
            border: 1px solid #fff2cc;
            border-bottom-color: #fff2cc;


            border-radius: 3px;
            -webkit-box-shadow: 0 1px 1px rgba(253, 137, 170, 0.3);
            box-shadow: 0 1px 1px rgba(253, 137, 170, 0.3);

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
            border-top-color: #fff2cc;
        }

        .contact-inner:after {
            margin-top: -1px;
            border-top-color: #edbfed;
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
            border: 1px solid #f6aded;
            border-bottom-color: #fbb20a;
            border-radius: 2px;
            -webkit-box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05), 0 1px rgba(255, 255, 255, 0.2);
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05), 0 1px rgba(255, 255, 255, 0.2);
        }

        .contact-input>input:focus,
        .contact-input>textarea:focus {
            border-color: #f0abd4;
            outline: 0;
            -webkit-box-shadow: 0 0 0 2px #f0abd4;
            box-shadow: 0 0 0 2px #fadfef;
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
            color: white;
            text-shadow: 0 1px rgba(255, 255, 255, 0.5);
            background: #fd88a5;
            border: 1px solid #f6aded;
            border-bottom-color: #fbb20a;
            border-radius: 15px;
            cursor: pointer;
            background-image: -webkit-linear-gradient(top, #feda75, #d62976);
            background-image: -moz-linear-gradient(top, #feda75, #d62976);
            background-image: -o-linear-gradient(top, #feda75, #d62976);
            background-image: linear-gradient(to bottom, #feda75, #d62976);

            -webkit-box-shadow: inset 0 1px rgba(255, 255, 255, 0.2), 
                                0 1px 1px rgba(0, 0, 0, 0.06), 
                                0 0 0 4px #fdd9ec;
            box-shadow: inset 0 1px rgba(255, 255, 255, 0.2), 
            0 1px 1px rgba(0, 0, 0, 0.06), 
            0 0 0 4px #fdd9ec;
        }

        .contact-submit > input:active {
            color: white;
            text-shadow: 0 1px rgba(0, 0, 0, 0.2);
            background-image: linear-gradient(to bottom, #d62976, #feda75); /* reversed gradient for a pressed effect */
            border-color: #f6aded #fbb20a #fbb20a;
            -webkit-box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.15), 
                0 1px rgba(255, 255, 255, 0.1), 
                0 0 0 4px #fdd9ec;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.15), 
                0 1px rgba(255, 255, 255, 0.1), 
                0 0 0 4px #fdd9ec;
}


        .title {
            font-size: large;
            font-weight: 600;
            color: #fc066e;
        }

        #custom-button {
            padding: 3px;
            color: white;
            background-color:#fda9eb;
            border: 1px solid #fda9eb;
            border-radius: 5px;
            cursor: pointer;
        }

        #custom-button:hover {
            background-color: #f86823;
            border: 1px solid #f86823;
        }

        #custom-text {
            margin-left: 10px;
            font-family: sans-serif;
            color: #aaa;
        }

        .wrap-list{
            word-break: break-all;
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
                    <p class="contact-input" style="text-align:center"><span class="title">Instagram</span> <img src="<?= $SiteURL ?>public/images/insta.png" alt="messenger" style="height:30px"></p>
                       
                        <p class="contact-input">
                            <input type="text" name="instagramhandle" id="intInput" placeholder="Type Your Instagram Id..." value="<?=$instagramhandle?>" readonly maxlength="20">
                            <input type="hidden" name="customerid" id="customerid"  value="<?=$customerid?>">
                            <input type="hidden" name="caseid" id="caseid"  value="<?=$caseid?>">
                        </p>

                        <p class="contact-input">
                            <textarea name="myTextarea" id="myTextarea" placeholder="Type Your Message..." autofocus></textarea>
                            <small id="charCount">0 / 200 characters</small> <!-- Character counter -->
                        </p>

                        <p class="contact-input">
                            <input type="file" id="real-file" name="attachments[]" hidden="hidden" multiple />
                            <button type="button" id="custom-button">Attachment</button>
                            <!-- <span id="custom-text">No file chosen, yet.</span> -->
                            <ul id="file-list"><li class="wrap-list">No files chosen, yet.</li></ul>
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
<script>
    // Close window on cross icon click
    document.getElementById("closeButton").addEventListener("click", function () {
        window.close();
    });
</script>
<script type="text/javascript">
$(document).ready(function() {

    // Get elements using jQuery
    const realFileBtn = $('#real-file');
    const customBtn = $('#custom-button');
    const fileList = $('#file-list'); // The <ul> element where the files will be listed
    const maxChars = 200; // Set the max character limit
    // Allowed file types (PDF, images, CSV, Excel, text files, ZIP, TAR, SVG)
    const allowedFileTypes = [
        'application/pdf', 
        'image/jpeg', 
        'image/png', 
        'image/gif', 
        'text/csv', 
        'application/vnd.ms-excel', 
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
        'text/plain', 
        'application/zip',       // ZIP files
        'application/x-zip-compressed',            // Compressed ZIP folder
        'application/x-tar',      // TAR files
        'image/svg+xml'                            // SVG files
    ];
    const maxFileSize = 20 * 1024 * 1024; // 20 MB in bytes

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

        // Create a FormData object to handle file uploads
        var formData = new FormData();

        // Collect form data
        formData.append('instagramhandle', $('input[name="instagramhandle"]').val().trim());
        formData.append('sendFrom', '17841470945980483');
        formData.append('reply', $('textarea[name="myTextarea"]').val().trim());
        formData.append('caseid',$('#caseid').val());
        formData.append('customerid',$('#customerid').val());
        formData.append('action', 'instagram');

        // Attach files
        var files = $('#real-file')[0].files;
        for (var i = 0; i < files.length; i++) {
            formData.append('attachments[]', files[i]); // Use an array for multiple files
        }

        // Basic validation (ensure fields are not empty)
        if (formData.get('instagramhandle') === "" || formData.get('reply') === "") {
            alert("Please fill out all fields.");
            $('#submitButton').attr('disabled', false).text('Send Message');
            return;
        }


        // Send AJAX request
        $.ajax({
            url: 'ContentMessagingAPI.php', // Replace with your server endpoint
            type: 'POST',
            data: formData,
            contentType: false, // Required for file upload
            processData: false, // Required for file upload
            dataType: 'json',
            cache: false, // Disable cache for the request
            success: function(response) {
                if (response.status === "success") {
                    // Handle success response
                    alert('Message sent successfully!');
                    $('#contactForm')[0].reset();
                    $('#charCount').text('0 / 200 characters'); // Reset character counter
                    $('#file-list').empty().append('<li>No files chosen, yet.</li>'); // Reset file list
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
     
    /* Code For Multiple Attachment */

    // Trigger file input click on custom button click
    customBtn.on('click', function() {
         realFileBtn.click();
    });

    // Handle file input change and validation
    realFileBtn.on('change', function() {
        const files = realFileBtn[0].files; // Get the file list
        fileList.empty(); // Clear the previous list items
        let totalSize = 0; // Track total size of selected files
        let hasInvalidFile = false; // Flag for invalid files

        if (files.length > 0) {
            // Loop through the files and validate each file type and size
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileType = file.type;
                const fileSize = file.size;

                // Check if the file type is allowed
                if (!allowedFileTypes.includes(fileType)) {
                    alert('Invalid file type: ' + file.name + '. Only PDF, images, CSV, Excel, text, ZIP, and TAR files are allowed.');
                    hasInvalidFile = true;
                    break;
                }

                // Check if the file size exceeds 20 MB
                totalSize += fileSize;
                if (totalSize > maxFileSize) {
                    alert('The total file size exceeds 20 MB.');
                    hasInvalidFile = true;
                    break;
                }

                // Append valid file name to the list
                fileList.append('<li class="wrap-list">' + file.name + '</li>');
            }
        } else {
            // Show a message when no files are selected
            fileList.append('<li class="wrap-list">No files chosen, yet.</li>');
        }

        // Reset file input if invalid files are found
        if (hasInvalidFile) {
            realFileBtn.val(''); // Clear the file input
            fileList.empty().append('<li class="wrap-list">No files chosen, yet.</li>'); // Reset file list
        }
    });

});
</script>

</html>