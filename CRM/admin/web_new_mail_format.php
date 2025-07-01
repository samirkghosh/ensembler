<?php
/**
 * View for creating and updating Mail Formats.
 * Refactored for security and to use AJAX.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
$id = isset($_GET['MailTypeID']) ? htmlspecialchars(base64_decode($_GET['MailTypeID']), ENT_QUOTES, 'UTF-8') : '';
$isEdit = !empty($id);
?>
<div id="message_container" style="display:none;"></div>
<form id="mailFormatForm" method="post" action="">
    <input name="id" id="id" type="hidden" value="<?php echo $id; ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <span class="breadcrumb_head" style="height:37px;padding:9px 16px"><?php echo ($isEdit ? "Edit" : "New") ?> Mail Format</span>
    <div class="style2-table">
        <div class="table">
            <table class="tableview tableview-2 main-form" width="100%">
                <tbody>
                    <tr>
                        <td><label for="template_name">Template Name<em>*</em></label></td>
                        <td class="left boder0-right">
                            <input name="template_name" id="template_name" type="text" class="input-style1" required>
                        </td>
                        <td><label for="type">Mail Type <em>*</em></label></td>
                        <td class="left boder0-right">
                            <input name="type" id="type" type="text" class="input-style1" required>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="subject">Mail Subject<em>*</em></label></td>
                        <td class="left boder0-right">
                            <input name="subject" id="subject" type="text" class="input-style1" required>
                        </td>
                        <td><label for="greeting">Mail Greeting<em>*</em></label></td>
                        <td class="left boder0-right">
                            <input name="greeting" id="greeting" type="text" class="input-style1" required>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="body">Mail Body</label></td>
                        <td class="left boder0-right">
                            <textarea name="body" id="body" rows="4" class="text-area1"></textarea>
                        </td>
                        <td><label for="description">Description<em>*</em></label></td>
                        <td class="left boder0-right">
                            <input name="description" id="description" type="text" class="input-style1" required>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="signature">Mail Signature</label></td>
                        <td class="left boder0-right">
                            <textarea name="signature" id="signature" rows="4" class="text-area1"></textarea>
                        </td>
                        <td><label for="expiry">Mail Expiry (In hours)</label></td>
                        <td class="left boder0-right">
                            <input name="expiry" id="expiry" type="number" class="input-style1" maxlength="4">
                        </td>
                    </tr>
                    <tr>
                        <td class="left boder0-right" colspan="4">
                            <center>
                                <input name="submit" type="submit" value="<?php echo ($isEdit ? 'Update' : 'Create'); ?>" class="button-orange1" id="submitBtn">
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
    const form = document.getElementById('mailFormatForm');
    const id = document.getElementById('id').value;
    const isEdit = id !== '';
    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

    if (isEdit) {
        // Fetch existing data for editing
        fetch(`mail_format.php?action=get_mail_format&id=${btoa(id)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const format = data.data;
                    document.getElementById('template_name').value = format.MailTemplateName || '';
                    document.getElementById('type').value = format.MailType || '';
                    document.getElementById('subject').value = format.MailSubject || '';
                    document.getElementById('greeting').value = format.MailGreeting || '';
                    document.getElementById('body').value = format.MailBody || '';
                    document.getElementById('description').value = format.MailDescription || '';
                    document.getElementById('signature').value = format.MailSignature || '';
                    document.getElementById('expiry').value = format.MailExpiry || '';
                } else {
                    alert(data.message);
                }
            });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const action = isEdit ? 'update_mail_format' : 'create_mail_format';
        formData.append('action', action);
        formData.append('csrf_token', csrfToken);

        const submitButton = document.getElementById('submitBtn');
        submitButton.disabled = true;

        fetch('mail_format.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const messageContainer = document.getElementById('message_container');
            messageContainer.textContent = data.message;
            messageContainer.style.display = 'block';
            messageContainer.style.color = data.success ? 'green' : 'red';
            
            if (data.success && !isEdit) {
                form.reset();
                document.getElementById('id').value = '';
                document.querySelector('input[name="csrf_token"]').value = csrfToken;
            }
            
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
