<?php
/**
 * Auth: Vastvikta Nishad
 * Date: 23-05-2025
 * Description: Display and Delete whatsapp template - Refactored for Security
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
$whatsapp_new_template_token = base64_encode('whatsapp_new_template');
?>
<div class="style2-table">
    <div class="style-title">
        <h3>WhatsApp Template</h3>
        <div class="row">
            <div class="col-sm-3" style="text-align: end;">
                <a href="admin_index.php?token=<?php echo htmlspecialchars($whatsapp_new_template_token, ENT_QUOTES, 'UTF-8'); ?>" class="button-orange1">New WhatsApp Format</a>
            </div>
        </div>
    </div>
    <div id="message_container" class="message-container" style="display:none;"></div>
    <div class="table" id="SRallview">
        <table class="tableview tableview-2" id="admin_table">
            <thead>
                <tr class="background">
                    <td align="left" width="5%">S.No.</td>
                    <td align="left" width="10%">Template Name</td>
                    <td align="left" width="8%">Template Type</td>
                    <td align="left" width="15%">Content</td>
                    <td align="left" width="5%">Action</td>
                </tr>
            </thead>
            <tbody id="whatsapp-template-list">
                <!-- Data will be loaded by JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '<?php echo $csrf_token; ?>';
    const newTemplateToken = '<?php echo $whatsapp_new_template_token; ?>';
    const siteUrl = '<?php echo isset($SiteURL) ? htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8') : ''; ?>';

    function fetchTemplates() {
        fetch('whatsapp.php?action=get_whatsapp_templates')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('whatsapp-template-list');
                tbody.innerHTML = '';
                if (data.success && data.templates.length > 0) {
                    let count = 1;
                    data.templates.forEach(template => {
                        const editUrl = `admin_index.php?token=${newTemplateToken}&id=${btoa(template.id)}`;
                        const row = `
                            <tr>
                                <td align="left">${count++}</td>
                                <td align="left">${template.temp_name}</td>
                                <td align="left">${template.temp_type}</td>
                                <td align="left">${template.temp_content}</td>
                                <td>
                                    <a href="${editUrl}">
                                        <img src="${siteUrl}public/images/edit-icon.png" border="0" alt="Edit" />
                                    </a>
                                    <a href="#" data-id="${template.id}" class="whatsapp_delete">
                                        <img src="${siteUrl}public/images/delete-icon.png" border="0" alt="delete">
                                    </a>
                                </td>
                            </tr>`;
                        tbody.innerHTML += row;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" align="center" class="contentred">No records found!</td></tr>';
                }
                attachDeleteHandlers();
            })
            .catch(error => console.error('Error fetching templates:', error));
    }

    function attachDeleteHandlers() {
        document.querySelectorAll('.whatsapp_delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this template?')) {
                    const id = this.getAttribute('data-id');
                    
                    const formData = new FormData();
                    formData.append('action', 'delete_whatsapp_template');
                    formData.append('id', id);
                    formData.append('csrf_token', csrfToken);

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
                        if (data.success) {
                            fetchTemplates();
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        });
    }

    fetchTemplates();
});
</script>
