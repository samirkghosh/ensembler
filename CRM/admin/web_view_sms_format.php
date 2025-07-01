<?php
/**
 * View for displaying all SMS formats.
 *
 * @author Vastvikta Nishad
 * @since 2024-07-24 (Refactored by AI)
 */

//- [[ SECURITY ]]
// no direct access
if ( !defined( 'ENVO_PREVENT_ACCESS' ) ) die( '[ ENVO ] NO DIRECT ACCESS' );

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://code.jquery.com; style-src 'self' 'unsafe-inline';");
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
$new_sms_format_token = base64_encode('new_sms_format');
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">SMS Format List</h5>
            <a href="admin_index.php?token=<?php echo htmlspecialchars($new_sms_format_token, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary float-end">New SMS Format</a>
        </div>
        <div class="card-body">
            <div id="response-message" class="alert" style="display:none;"></div>
            <table class="table table-striped table-bordered" id="sms-format-table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Template Name</th>
                        <th>Type</th>
                        <th>Created On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const responseMessage = $('#response-message');
    const csrfToken = '<?php echo $csrf_token; ?>';

    // Fetch and display SMS formats
    $.ajax({
        url: 'admin/sms_format.php',
        type: 'GET',
        dataType: 'json',
        data: { action: 'get_sms_formats' },
        success: function(response) {
            if (response.success && response.data.length > 0) {
                const tableBody = $('#sms-format-table tbody');
                let count = 1;
                response.data.forEach(function(format) {
                    const editUrl = `admin_index.php?token=<?php echo $new_sms_format_token; ?>&id=${format.id}`;
                    const row = `
                        <tr>
                            <td>${count++}</td>
                            <td>${format.name}</td>
                            <td>${format.type}</td>
                            <td>${format.created_on}</td>
                            <td>
                                <a href="${editUrl}" class="btn btn-sm btn-info">Edit</a>
                                <button class="btn btn-sm btn-danger delete-sms-format" data-id="${format.id}">Delete</button>
                            </td>
                        </tr>`;
                    tableBody.append(row);
                });
            } else {
                $('#sms-format-table tbody').append('<tr><td colspan="5" class="text-center">No SMS formats found.</td></tr>');
            }
        },
        error: function() {
            responseMessage.addClass('alert-danger').text('Failed to load SMS formats.').show();
        }
    });

    // Handle delete button click
    $('body').on('click', '.delete-sms-format', function() {
        if (!confirm('Are you sure you want to delete this SMS format?')) return;

        const id = $(this).data('id');
        const button = $(this);

        $.ajax({
            url: 'admin/sms_format.php',
            type: 'POST',
            data: {
                id: id,
                csrf_token: csrfToken,
                action: 'delete_sms_format'
            },
            dataType: 'json',
            success: function(response) {
                responseMessage.removeClass('alert-danger alert-success').show();
                if (response.success) {
                    responseMessage.addClass('alert-success').text(response.message);
                    button.closest('tr').remove();
                } else {
                    responseMessage.addClass('alert-danger').text(response.message);
                }
            },
            error: function() {
                responseMessage.addClass('alert-danger').text('An unexpected error occurred.').show();
            }
        });
    });
});
</script>
