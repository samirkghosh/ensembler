<?php
/**
 * View for adding and viewing Spam Mail entries.
 *
 * @author AI
 * @since 2024-07-25
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
?>

<div class="container-fluid">
    <div id="response-message" class="alert" style="display:none;"></div>
    
    <!-- Add Spam Mail Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Add New Spam Email</h5>
        </div>
        <div class="card-body">
            <form id="spamMailForm" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="submit_spam_mail">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input name="email" id="email" type="email" class="form-control" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Add Spam Email</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Spam Mail List -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Spam Email List</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered" id="spam-mail-table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Email</th>
                        <th>Created On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const responseMessage = $('#response-message');
    const csrfToken = '<?php echo $csrf_token; ?>';

    function loadSpamMails() {
        $.ajax({
            url: 'admin/spam_mail.php',
            type: 'GET',
            dataType: 'json',
            data: { action: 'get_spam_mails' },
            success: function(response) {
                const tableBody = $('#spam-mail-table tbody');
                tableBody.empty();
                if (response.success && response.data.length > 0) {
                    let count = 1;
                    response.data.forEach(function(mail) {
                        const row = `
                            <tr>
                                <td>${count++}</td>
                                <td>${mail.mail}</td>
                                <td>${mail.created_on}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger delete-spam-mail" data-id="${mail.id}">Delete</button>
                                </td>
                            </tr>`;
                        tableBody.append(row);
                    });
                } else {
                    tableBody.append('<tr><td colspan="4" class="text-center">No spam emails found.</td></tr>');
                }
            },
            error: function() {
                responseMessage.addClass('alert-danger').text('Failed to load spam emails.').show();
            }
        });
    }

    // Initial load
    loadSpamMails();

    // Handle form submission
    $('#spamMailForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        $.ajax({
            url: 'admin/spam_mail.php',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                responseMessage.removeClass('alert-danger alert-success').show();
                if (response.success) {
                    responseMessage.addClass('alert-success').text(response.message);
                    form[0].reset();
                    loadSpamMails(); // Refresh the list
                } else {
                    responseMessage.addClass('alert-danger').text(response.message);
                }
            },
            error: function() {
                responseMessage.addClass('alert-danger').text('An unexpected error occurred.').show();
            }
        });
    });

    // Handle delete button click
    $('body').on('click', '.delete-spam-mail', function() {
        if (!confirm('Are you sure you want to delete this spam mail entry?')) return;

        const id = $(this).data('id');
        $.ajax({
            url: 'admin/spam_mail.php',
            type: 'POST',
            data: { id: id, csrf_token: csrfToken, action: 'delete_spam_mail' },
            dataType: 'json',
            success: function(response) {
                responseMessage.removeClass('alert-danger alert-success').show();
                if (response.success) {
                    responseMessage.addClass('alert-success').text(response.message);
                    loadSpamMails(); // Refresh the list
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
