<?php
/**
 * Auth: Vastvikta Nishad
 * Date:  19 Feb 2024
 * Modification by AI - 2024-07-22
 * Description: To Update and Insert Disposition. Rewritten for security and functionality.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enforce security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com;");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: no-referrer");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// The insert_or_update_disposition function in web_admin_function.php handles the POST request securely
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    insert_or_update_disposition(); 
    exit();
}

$id = isset($_GET['id']) ? base64_decode($_GET['id']) : '';
// Fetch data based on the ID - the function is now secure
$res = getDispositionID($id);
$disposition = $res['V_DISPO'] ?? ''; 
$isEdit = !empty($id);
?>
<div id="message_container" style="display:none;"></div>
<form id="dispositionForm" method="post" action="">
    <input type="hidden" name="id" id="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>" />
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
    <span class="breadcrumb_head" style="height:37px;padding:9px 16px">
        <?php echo ($isEdit ? "Edit" : "New"); ?> Disposition
    </span>
    <div class="style2-table">
        <div class="table">
            <table class="tableview tableview-2 main-form">
                <tbody>
                    <tr>
                        <td colspan="2" class="left">
                            <label for="disposition">Disposition<em>*</em></label>
                            <div class="log-case">
                                <input name="disposition" id="disposition" type="text" value="<?php echo $disposition; ?>" class="input-style1" required />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <center>
                                <input name="submit" type="submit" value="<?php echo ($isEdit ? 'Update' : 'Create'); ?>" class="button-orange1" id="submitclkdisposition" style="float: inherit;" />
                            </center>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('dispositionForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitButton = document.getElementById('submitclkdisposition');
        submitButton.disabled = true;
        
        fetch('web_admin_function.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const messageContainer = document.getElementById('message_container');
            let message = '';
            let isSuccess = false;

            if (data.success) {
                message = 'Operation successful!';
                isSuccess = true;
                if (!formData.get('id')) {
                    form.reset();
                    document.getElementById('id').value = '';
                }
            } else {
                message = data.error_msg || 'An unknown error occurred.';
            }

            messageContainer.textContent = message;
            messageContainer.style.display = 'block';
            messageContainer.style.color = isSuccess ? 'green' : 'red';
            messageContainer.style.padding = '10px';
            messageContainer.style.textAlign = 'center';
            messageContainer.style.background = isSuccess ? '#d4edda' : '#f8d7da';
            messageContainer.style.borderColor = isSuccess ? '#c3e6cb' : '#f5c6cb';

            submitButton.disabled = false;

            setTimeout(() => {
                messageContainer.style.display = 'none';
            }, 5000);
        })
        .catch(error => {
            console.error('Error:', error);
            const messageContainer = document.getElementById('message_container');
            messageContainer.textContent = 'An unexpected network error occurred.';
            messageContainer.style.display = 'block';
            messageContainer.style.color = 'red';
            submitButton.disabled = false;
        });
    });
});
</script>
