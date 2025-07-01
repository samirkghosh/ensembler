<?php
/**
 * View for editing an IMAP setting.
 *
 * @author Vastvikta Nishad
 * @since 2024-07-25 (Refactored by AI)
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
<form id="imapForm" method="post">
    <input type="hidden" name="id" id="imap_id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <input type="hidden" name="action" value="update_imap">

    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Edit IMAP Settings</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="v_connectionname" class="form-label">Connection Name <span class="text-danger">*</span></label>
                        <input name="v_connectionname" id="v_connectionname" type="text" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="v_ipaddress" class="form-label">IMAP Address <span class="text-danger">*</span></label>
                        <input name="v_ipaddress" id="v_ipaddress" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="v_username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input name="v_username" id="v_username" type="text" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="v_pasowrd" class="form-label">Password <span class="text-danger">*</span></label>
                        <input name="v_pasowrd" id="v_pasowrd" type="password" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="v_type" class="form-label">Type <span class="text-danger">*</span></label>
                        <input name="v_type" id="v_type" type="text" class="form-control" required>
                    </div>
                     <div class="col-md-6 mb-3">
                        <label for="cc_mail_id" class="form-label">CC Mail ID</label>
                        <input name="cc_mail_id" id="cc_mail_id" type="email" class="form-control">
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="v_client_id" class="form-label">Client ID <span class="text-danger">*</span></label>
                        <input name="v_client_id" id="v_client_id" type="text" class="form-control" required>
                    </div>
                     <div class="col-md-6 mb-3">
                        <label for="v_client_secret" class="form-label">Client Secret <span class="text-danger">*</span></label>
                        <input name="v_client_secret" id="v_client_secret" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="v_tenant" class="form-label">Tenant <span class="text-danger">*</span></label>
                    <input name="v_tenant" id="v_tenant" type="text" class="form-control" required>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    const imapId = $('#imap_id').val();

    if (imapId) {
        $.ajax({
            url: 'admin/imap_smtp.php',
            type: 'GET',
            data: { action: 'get_imap', id: imapId },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    $('#v_connectionname').val(data.v_connectionname);
                    $('#v_ipaddress').val(data.v_ipaddress);
                    $('#v_username').val(data.v_username);
                    $('#v_pasowrd').val(data.v_pasowrd);
                    $('#v_type').val(data.v_type);
                    $('#cc_mail_id').val(data.cc_mail_id);
                    $('#v_client_id').val(data.v_client_id);
                    $('#v_client_secret').val(data.v_client_secret);
                    $('#v_tenant').val(data.v_tenant);
                }
            }
        });
    }

    $('#imapForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const responseMessage = $('#response-message');

        $.ajax({
            url: 'admin/imap_smtp.php',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                responseMessage.removeClass('alert-danger alert-success').show();
                if (response.success) {
                    responseMessage.addClass('alert-success').text(response.message);
                    setTimeout(function() {
                        var encodedToken = btoa('imap_smtp');
                        window.location.href = "admin_index.php?action=imap_smtp&token=" + encodeURIComponent(encodedToken);
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