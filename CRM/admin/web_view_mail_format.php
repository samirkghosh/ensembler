<?php
/**
 * View for displaying and deleting Mail Formats.
 * Refactored for security and to use AJAX.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
$new_mail_token = base64_encode('web_new_mail_format'); // Corrected to the new file name
$SiteURL = isset($SiteURL) ? htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8') : '';
?>
<div class="style2-table">
    <div class="style-title">
        <h3>Mail Format</h3>
        <div class="row">
            <div class="col-sm-2" style="text-align: end;">
                <a href="admin_index.php?token=<?php echo htmlspecialchars($new_mail_token, ENT_QUOTES, 'UTF-8'); ?>" class="button-orange1">New Mail Format</a>
            </div>
        </div>
    </div>
    <div id="message_container" style="display:none; padding: 10px; text-align:center;"></div>
    <div class="table" id="SRallview">
        <table class="tableview tableview-2" id="admin_table">
            <thead>
                <tr class="background">
                    <td align="left">S.No.</td>
                    <td align="left">Template Name</td>
                    <td align="left">Content</td>
                    <td align="left">Action</td>
                </tr>
            </thead>
            <tbody id="mail-format-list">
                <!-- Data will be loaded by JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '<?php echo $csrf_token; ?>';

    function fetchMailFormats() {
        fetch('mail_format.php?action=get_mail_formats')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('mail-format-list');
                tbody.innerHTML = '';
                if (data.success && data.formats.length > 0) {
                    let count = 1;
                    data.formats.forEach(format => {
                        const editUrl = `admin_index.php?token=<?php echo $new_mail_token; ?>&MailTypeID=${btoa(format.MailTypeID)}`;
                        const content = (format.MailGreeting || '') + (format.MailBody || '') + (format.MailSignature || '');
                        const row = `
                            <tr>
                                <td align="left">${count++}</td>
                                <td align="left">${format.MailTemplateName}</td>
                                <td align="left" style="pointer-events: none;opacity: 0.7;">${content}</td>
                                <td>
                                    <a href="${editUrl}">
                                        <img src="<?php echo $SiteURL; ?>public/images/edit-icon.png" border="0" alt="Edit" />
                                    </a>
                                    <a href="#" data-id="${format.MailTypeID}" class="mail_delete">
                                        <img src="<?php echo $SiteURL; ?>public/images/delete-icon.png" border="0" alt="delete">
                                    </a>
                                </td>
                            </tr>`;
                        tbody.innerHTML += row;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" align="center">No mail formats found.</td></tr>';
                }
                attachDeleteHandlers();
            })
            .catch(error => console.error('Error fetching mail formats:', error));
    }

    function attachDeleteHandlers() {
        document.querySelectorAll('.mail_delete').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this mail format?')) {
                    const id = this.getAttribute('data-id');
                    
                    const formData = new FormData();
                    formData.append('action', 'delete_mail_format');
                    formData.append('id', id);
                    formData.append('csrf_token', csrfToken);

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
                        if (data.success) {
                            fetchMailFormats();
                        }
                        setTimeout(() => { messageContainer.style.display = 'none'; }, 5000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An unexpected error occurred.');
                    });
                }
            });
        });
    }

    fetchMailFormats();
});
</script>
