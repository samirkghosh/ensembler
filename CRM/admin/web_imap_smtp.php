<?php
/**
 * View for displaying all IMAP and SMTP settings.
 *
 * @author Vastvikta Nishad
 * @since 2024-07-25 (Refactored by AI)
 */

//- [[ SECURITY ]]
// no direct access
if ( !defined( 'ENVO_PREVENT_ACCESS' ) ) die( '[ ENVO ] NO DIRECT ACCESS' );

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://code.jquery.com; style-src 'self' 'unsafe-inline';");
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

$new_imap_token = base64_encode('new_imap');
$new_smtp_token = base64_encode('new_smtp');
?>

<div class="container-fluid">
    <!-- IMAP Settings -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">IMAP Settings</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered" id="imap-table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Connection Name</th>
                        <th>IMAP Address</th>
                        <th>Username</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- SMTP Settings -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">SMTP Settings</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped table-bordered" id="smtp-table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Connection Name</th>
                        <th>Server Address</th>
                        <th>Username</th>
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

    $.ajax({
        url: 'admin/imap_smtp.php',
        type: 'GET',
        dataType: 'json',
        data: { action: 'get_all_settings' },
        success: function(response) {
            if (response.success) {
                // Populate IMAP Table
                const imapTableBody = $('#imap-table tbody');
                imapTableBody.empty();
                if (response.data.imap.length > 0) {
                    let count = 1;
                    response.data.imap.forEach(function(setting) {
                        const editUrl = `admin_index.php?token=<?php echo $new_imap_token; ?>&id=${setting.i_id}`;
                        const row = `
                            <tr>
                                <td>${count++}</td>
                                <td>${setting.v_connectionname}</td>
                                <td>${setting.v_ipaddress}</td>
                                <td>${setting.v_username}</td>
                                <td><a href="${editUrl}" class="btn btn-sm btn-info">Edit</a></td>
                            </tr>`;
                        imapTableBody.append(row);
                    });
                } else {
                    imapTableBody.append('<tr><td colspan="5" class="text-center">No IMAP settings found.</td></tr>');
                }

                // Populate SMTP Table
                const smtpTableBody = $('#smtp-table tbody');
                smtpTableBody.empty();
                if (response.data.smtp.length > 0) {
                    let count = 1;
                    response.data.smtp.forEach(function(setting) {
                        const editUrl = `admin_index.php?token=<?php echo $new_smtp_token; ?>&id=${setting.i_id}`;
                        const row = `
                            <tr>
                                <td>${count++}</td>
                                <td>${setting.v_connectionname}</td>
                                <td>${setting.v_ipaddress}</td>
                                <td>${setting.v_username}</td>
                                <td><a href="${editUrl}" class="btn btn-sm btn-info">Edit</a></td>
                            </tr>`;
                        smtpTableBody.append(row);
                    });
                } else {
                    smtpTableBody.append('<tr><td colspan="5" class="text-center">No SMTP settings found.</td></tr>');
                }
            } else {
                 $('#imap-table tbody').append('<tr><td colspan="5" class="text-center">Failed to load settings.</td></tr>');
                 $('#smtp-table tbody').append('<tr><td colspan="5" class="text-center">Failed to load settings.</td></tr>');
            }
        },
        error: function() {
            $('#imap-table tbody').append('<tr><td colspan="5" class="text-center">An error occurred.</td></tr>');
            $('#smtp-table tbody').append('<tr><td colspan="5" class="text-center">An error occurred.</td></tr>');
        }
    });
});
</script>
