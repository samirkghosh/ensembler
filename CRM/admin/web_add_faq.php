<?php
/**
 * View for creating or editing a FAQ (Knowledge Base).
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
$view_faq_token = base64_encode('view_faq');
?>

<div id="response-message" class="alert" style="display:none;"></div>
<form id="faqForm" method="post">
    <input type="hidden" name="id" id="faq_id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <input type="hidden" name="action" value="submit_faq">

    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><?php echo $id ? 'Edit' : 'New'; ?> Knowledge Base</h5>
                 <a href="admin_index.php?token=<?php echo htmlspecialchars($view_faq_token, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary">View Knowledge Base</a>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="question" class="form-label">Question <span class="text-danger">*</span></label>
                    <input name="question" id="question" type="text" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="answer" class="form-label">Answer <span class="text-danger">*</span></label>
                    <textarea name="answer" id="answer" rows="8" class="form-control" required></textarea>
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
    const faqId = $('#faq_id').val();

    if (faqId) {
        // Fetch the FAQ data for editing
        $.ajax({
            url: 'admin/faq.php',
            type: 'GET',
            data: { action: 'get_faq', id: faqId },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    $('#question').val(response.data.question);
                    $('#answer').val(response.data.answer);
                }
            }
        });
    }

    $('#faqForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const responseMessage = $('#response-message');

        $.ajax({
            url: 'admin/faq.php',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                responseMessage.removeClass('alert-danger alert-success').show();
                if (response.success) {
                    responseMessage.addClass('alert-success').text(response.message);
                    setTimeout(function() {
                        var encodedToken = btoa('view_faq');
                        window.location.href = "admin_index.php?action=view_faq&token=" + encodeURIComponent(encodedToken);
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