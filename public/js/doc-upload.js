/***
    Author: Farhan Akhtar
    Last Modified on: 04 Feb 2025
    Description: Scripts and functions to Upload Documents for Knowledge Base AI Assistant
*/

let state = {
    filesArr: []
};

// State management
function updateState(newState) {
    state = { ...state, ...newState };
    // console.log("State updated:", state);
}

// Show/hide file list
function toggleFileList() {
    if (state.filesArr.length > 0) {
        $(".files").show();
    } else {
        $(".files").hide();
    }
}

// File input change event
$(".uploadDoc").change(function (e) {
    let files = e.target.files;
    // console.log("Files selected:", files);

    // Define allowed MIME types
    const allowedTypes = [
        "application/pdf",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", // .xlsx
        "application/vnd.ms-excel", // .xls
        "application/msword", // .doc
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document", // .docx
        "text/csv"
    ];

    // Filter files to only include allowed types
    let validFiles = Array.from(files).filter(file => allowedTypes.includes(file.type));

    if (validFiles.length !== files.length) {
        alert("Only PDF, Excel, Word, and CSV files are allowed. Other files will be ignored.");
    }

    updateState({ filesArr: validFiles });
    renderFileList();
    toggleFileList();
});

// File removal event (fixed key reference)
$(".files").on("click", ".file-list-item > i", function () {
    let key = $(this).parent().data("key");
    let curArr = [...state.filesArr];
    curArr.splice(key, 1);
    updateState({ filesArr: curArr });
    renderFileList();
    toggleFileList();
});

// Form submit event with validation
$("#UploadForm").submit(function (e) {
    e.preventDefault(); // Prevent page reload

    // Validate if files are selected
    if (state.filesArr.length === 0) {
        alert("Please Select file to upload");
        return;
    }

    var descDoc = $("#descDoc").val();
    if (descDoc.trim() === "") {
        alert("Please Enter Description.");
        $("#descDoc").focus(); // Set focus back to the textarea
        return;
    }

    // console.log('Files selected:', state.filesArr);

    // Show loader
    $("#processModel").fadeIn();

    // Create FormData object
    var formData = new FormData();

    // Append each file separately
    state.filesArr.forEach((file, index) => {
        formData.append("file[]", file); // Send files as an array
    });

    // Append additional data
    formData.append("action", "Upload_DocumentAPI");
    formData.append("descDoc", descDoc); // Append description
    // Send AJAX request
    $.ajax({
        url: "admin/web_admin_function.php", // Path to your PHP script
        type: "POST",
        data: formData,
        dataType: 'json',
        contentType: false, // Required for file uploads
        processData: false, // Required for file uploads
        success: function (response) {
            console.log("Upload successful:", response);
            alert("Successfully Uploaded");
            // Optionally, reset the form or clear the file input
            $("#UploadForm")[0].reset();
            updateState({ filesArr: [] }); // Clear the files array
            state.filesArr = []; // Clear the files array
            renderFileList(); // Re-render empty list
            toggleFileList(); // Hide file list if empty
            $("#processModel").fadeOut();
            location.reload();

        },
        error: function (xhr, status, error) {
            console.log("Upload failed:", xhr.responseText);
            alert("File upload failed. Error: " + (xhr.responseText || error));
            $("#processModel").fadeOut();
           
        }
    });
});


// Render selected files list
function renderFileList() {
    // console.log("Rendering file list...");
    let fileMap = state.filesArr.map((file, index) => {
        let suffix = "bytes";
        let size = file.size;
        if (size >= 1024 && size < 1024000) {
            suffix = "KB";
            size = Math.round((size / 1024) * 100) / 100;
        } else if (size >= 1024000) {
            suffix = "MB";
            size = Math.round((size / 1024000) * 100) / 100;
        }

        return `<li class="file-list-item" data-key="${index}">${file.name} <span class="file-size">${size} ${suffix}</span> <i class="fa fa-trash"></i></li>`;
    });
    $(".file-list").html(fileMap);
}


// function to fetch uploaded files :: Farhan Akhtar [06-02-2025]
function fetchFiles() {
    $.ajax({
        url: "admin/web_admin_function.php",
        type: "POST",
        data: { action: "view_document" },
        dataType: "json",
        success: function(response) {
            console.log(response); // Debugging: Check response

            let tbody = $("#fileTable tbody");
            tbody.empty(); // Clear existing rows

            if (response.status === "success" && response.files.length > 0) {
                $.each(response.files, function(index, file) {
                    let row = `<tr>
                        <td>${index + 1}</td>
                        <td>${file.name}</td>
                        <td>${file.size}</td>
                        <td>${file.description}</td>
                        <td>${file.created_by}</td>
                        <td>${file.date_uploaded}</td>
                        <td>${file.action}</td>
                    </tr>`;
                    tbody.append(row);
                });

                // Reinitialize DataTable after data update
                $('#fileTable').DataTable({
                    "searching": true,   // Disable search bar
                    "lengthChange": false, // Remove "Show entries" dropdown
                    "pageLength": 10,      // Show 10 records per page
                    "destroy": true        // Prevent duplicate initialization
                });

            } else {
                tbody.append("<tr><td colspan='3'>No Records Found</td></tr>");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: ", error); // Debugging
            alert("Error fetching files.");
        }
    });
}


$(document).ready(function() {
    // Fetch files on page load
    fetchFiles();
  
    // Use event delegation for dynamically generated elements
    $('#fileTable').on('click', '.delete-doc', function() {
        console.log("Delete link clicked!"); // Debugging: Check if the click event is triggered

        // Get the category from the data attribute
        var category = $(this).data('category');
        var filename = $(this).attr('id');
        console.log("Category:", category); // Debugging: Check the category value

        // Confirm before deleting
        if (confirm("Are you sure you want to delete this document?")) {
            console.log("User confirmed deletion."); // Debugging: Check if the user confirmed
            // Call the delete function
            deleteDocument(category,filename);
        } else {
            console.log("User canceled deletion."); // Debugging: Check if the user canceled
        }
    });

    // Use event delegation for dynamically generated elements
    $('#fileTable').on('click', '.view-doc', function() {
        var filename = $(this).attr('id');
        var category = $(this).data('category');

        // Open the document via the proxy script
        window.open(`admin/view_document.php?category=${category}&filename=${filename}`, '_blank');
    });

    function deleteDocument(category,filename) {
        console.log("Calling deleteDocument for category:", category); // Debugging: Check if the function is called

        $.ajax({
            url: 'admin/web_admin_function.php', // Replace with the path to your PHP script
            method: 'POST',
            data: {
                action: 'delete_document',
                category: category,
                filename: filename
            },
            dataType: "json",
            success: function(response) {
                console.log("API Response:", response); // Debugging: Check the API response
             
                if (response.status === "success") {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error); // Debugging: Check for errors
                alert("An error occurred while deleting the document.");
            }
        });
    }
});
