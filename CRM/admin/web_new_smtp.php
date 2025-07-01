<?php
/**
 * View for editing an SMTP setting.
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
<form id="smtpForm" method="post">
    <input type="hidden" name="id" id="smtp_id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <input type="hidden" name="action" value="update_smtp">

    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Edit SMTP Settings</h5>
            </div>
            <div class="card-body">
                 <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="v_connectionname" class="form-label">Connection Name <span class="text-danger">*</span></label>
                        <input name="v_connectionname" id="v_connectionname" type="text" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="v_ipaddress" class="form-label">Server Address <span class="text-danger">*</span></label>
                        <input name="v_ipaddress" id="v_ipaddress" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="i_port" class="form-label">Port <span class="text-danger">*</span></label>
                        <input name="i_port" id="i_port" type="number" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="v_username" class="form-label">Username <span class="text-danger">*</span></label>
                        <input name="v_username" id="v_username" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="v_pasowrd" class="form-label">Password <span class="text-danger">*</span></label>
                        <input name="v_pasowrd" id="v_pasowrd" type="password" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="v_type" class="form-label">Type (e.g., SSL, TLS) <span class="text-danger">*</span></label>
                        <input name="v_type" id="v_type" type="text" class="form-control" required>
                    </div>
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
    const smtpId = $('#smtp_id').val();

    if (smtpId) {
        $.ajax({
            url: 'admin/imap_smtp.php',
            type: 'GET',
            data: { action: 'get_smtp', id: smtpId },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    $('#v_connectionname').val(data.v_connectionname);
                    $('#v_ipaddress').val(data.v_ipaddress);
                    $('#i_port').val(data.i_port);
                    $('#v_username').val(data.v_username);
                    $('#v_pasowrd').val(data.v_pasowrd);
                    $('#v_type').val(data.v_type);
                }
            }
        });
    }

    $('#smtpForm').on('submit', function(e) {
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
