<?php
/**
 * View for editing an Escalation.
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

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://code.jquery.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net;");
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div id="response-message" class="alert" style="display:none;"></div>
<form id="escalationForm" method="post">
    <input type="hidden" name="id" id="escalation_id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <input type="hidden" name="action" value="submit_escalation">

    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Edit Escalation</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Escalation To</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" id="escalation_to_customer" type="radio" name="escalation_to" value="1">
                                <label class="form-check-label" for="escalation_to_customer">Customer</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" id="escalation_to_department" type="radio" name="escalation_to" value="2">
                                <label class="form-check-label" for="escalation_to_department">Department</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" id="escalation_to_both" type="radio" name="escalation_to" value="3">
                                <label class="form-check-label" for="escalation_to_both">Both</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Escalation Media</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" id="escalation_media_email" type="radio" name="escalation_media" value="1">
                                <label class="form-check-label" for="escalation_media_email">Email</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" id="escalation_media_sms" type="radio" name="escalation_media" value="2">
                                <label class="form-check-label" for="escalation_media_sms">SMS</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" id="escalation_media_both" type="radio" name="escalation_media" value="3">
                                 <label class="form-check-label" for="escalation_media_both">Both</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="escalation_list" class="form-label">User List</label>
                    <select name="escalation_list[]" id="escalation_list" class="form-control" multiple="multiple" data-placeholder="Select Users">
                        <!-- Users will be loaded via AJAX -->
                    </select>
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
    const escalationId = $('#escalation_id').val();
    const userSelect = $('#escalation_list');

    userSelect.select2();

    // Fetch users and then fetch escalation data
    $.ajax({
        url: 'admin/escalation.php',
        type: 'GET',
        data: { action: 'get_users' },
        dataType: 'json',
        success: function(userResponse) {
            if (userResponse.success) {
                userSelect.empty();
                userResponse.data.forEach(function(user) {
                    userSelect.append(new Option(user.name, user.id, false, false));
                });

                if (escalationId) {
                    $.ajax({
                        url: 'admin/escalation.php',
                        type: 'GET',
                        data: { action: 'get_escalation', id: escalationId },
                        dataType: 'json',
                        success: function(escalationResponse) {
                            if (escalationResponse.success && escalationResponse.data) {
                                const data = escalationResponse.data;
                                $(`input[name="escalation_to"][value="${data.escalation_to}"]`).prop('checked', true);
                                $(`input[name="escalation_media"][value="${data.escalation_media}"]`).prop('checked', true);
                                userSelect.val(data.escalation_list).trigger('change');
                            }
                        }
                    });
                }
            }
        }
    });

    $('#escalationForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const responseMessage = $('#response-message');

        $.ajax({
            url: 'admin/escalation.php',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                responseMessage.removeClass('alert-danger alert-success').show();
                if (response.success) {
                    responseMessage.addClass('alert-success').text(response.message);
                    setTimeout(function() {
                        var encodedToken = btoa('view_escalation');
                        window.location.href = "admin_index.php?action=view_escalation&token=" + encodeURIComponent(encodedToken);
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
