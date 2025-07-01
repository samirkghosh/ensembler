<?php
/**
 * Auth: Vastvikta Nishad
 * Date: 31 Jan 2024
 * Description: Create and Update Whatsapp Template - Refactored for Security
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$id = isset($_GET['id']) ? base64_decode($_GET['id']) : '';

// Data will be fetched by JS
$template_name = '';
$type = '';
$body = '';

?>
<div id="message_container" class="message-container" style="display:none;"></div>
<form id="whatsappTemplateForm" method="post">
    <input name="id" id="id" type="hidden" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">
    <span class="breadcrumb_head" style="height:37px;padding:9px 16px"><?php echo (!empty($id) ? "Edit" : "New"); ?> WhatsApp Template</span>
    <div class="style2-table">
        <div class="table">
            <table class="tableview tableview-2 main-form">
                <tbody>
                    <tr>
                        <td><label for="template_name">Template Name<em>*</em></label></td>
                        <td class="left boder0-right">
                            <div class="log-case">
                                <input name="template_name" id="template_name" type="text" value="<?php echo htmlspecialchars($template_name, ENT_QUOTES, 'UTF-8'); ?>" class="input-style1" required>
                            </div>
                        </td>
                        <td><label for="type">Template Type<em>*</em></label></td>
                        <td class="left boder0-right">
                            <div class="log-case">
                                <input name="type" id="type" type="text" value="<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>" class="input-style1" required>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="body">Content<em>*</em></label></td>
                        <td class="left boder0-right" colspan="3">
                            <div class="log-case">
                                <textarea name="body" id="body" rows="4" class="text-area1" required><?php echo htmlspecialchars($body, ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="left boder0-right" colspan="4">
                            <center>
                                <input name="submit_template" type="submit" value="<?php echo (!empty($id) ? 'Update' : 'Create'); ?>" class="button-orange1" id="submitBtn">
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
    const form = document.getElementById('whatsappTemplateForm');
    const id = document.getElementById('id').value;
    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

    if (id) {
        // Fetch existing data for editing
        fetch(`whatsapp.php?action=get_whatsapp_templates`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const template = data.templates.find(t => t.id === id);
                if (template) {
                    document.getElementById('template_name').value = template.temp_name;
                    document.getElementById('type').value = template.temp_type;
                    document.getElementById('body').value = template.temp_content;
                }
            }
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const action = id ? 'update_whatsapp_template' : 'create_whatsapp_template';
        formData.append('action', action);
        formData.append('csrf_token', csrfToken);


        const submitButton = document.getElementById('submitBtn');
        submitButton.disabled = true;

        fetch('whatsapp.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const messageContainer = document.getElementById('message_container');
            messageContainer.textContent = data.message;
            messageContainer.style.display = 'block';
            messageContainer.style.color = data.success ? 'green' : 'red';
            
            if (data.success && !id) {
                form.reset();
                // Re-apply csrf token after reset
                document.querySelector('input[name="csrf_token"]').value = csrfToken;
            }

            submitButton.disabled = false;
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