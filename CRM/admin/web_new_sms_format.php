<?php
/**
 * View for creating or editing an SMS format.
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
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
?>

<div id="response-message" class="alert" style="display:none;"></div>
<form id="smsFormatForm" method="post">
    <input type="hidden" name="id" id="format_id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <input type="hidden" name="action" value="submit_sms_format">

    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><?php echo $id ? 'Edit' : 'New'; ?> SMS Format</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="template_name" class="form-label">Template Name <span class="text-danger">*</span></label>
                        <input name="template_name" id="template_name" type="text" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Type</label>
                        <input name="type" id="type" type="text" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="header" class="form-label">Header</label>
                    <input name="header" id="header" type="text" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="body" class="form-label">Body <span class="text-danger">*</span></label>
                    <textarea name="body" id="body" rows="4" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="footer" class="form-label">Footer</label>
                    <input name="footer" id="footer" type="text" class="form-control">
                </div>
                 <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="description" class="form-label">Description</label>
                        <input name="description" id="description" type="text" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="expiry" class="form-label">Expiry (in hours)</label>
                        <input name="expiry" id="expiry" type="number" class="form-control">
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary"><?php echo $id ? 'Update' : 'Create'; ?></button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    const formatId = $('#format_id').val();

    if (formatId) {
        // Fetch the SMS format data for editing
        $.ajax({
            url: 'admin/sms_format.php',
            type: 'GET',
            data: { action: 'get_sms_format', id: formatId },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    $('#template_name').val(response.data.template_name);
                    $('#type').val(response.data.type);
                    $('#header').val(response.data.header);
                    $('#body').val(response.data.body);
                    $('#footer').val(response.data.footer);
                    $('#description').val(response.data.description);
                    $('#expiry').val(response.data.expiry);
                }
            }
        });
    }

    $('#smsFormatForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const responseMessage = $('#response-message');

        $.ajax({
            url: 'admin/sms_format.php',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                responseMessage.removeClass('alert-danger alert-success').show();
                if (response.success) {
                    responseMessage.addClass('alert-success').text(response.message);
                    setTimeout(function() {
                        var encodedToken = btoa('view_sms_format');
                        window.location.href = "admin_index.php?action=view_sms_format&token=" + encodeURIComponent(encodedToken);
                    }, 1500);
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