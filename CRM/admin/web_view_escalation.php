<?php
/**
 * View for displaying all escalations.
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

$new_escalation_token = base64_encode('new_escalation');
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Escalation List</h5>
        </div>
        <div class="card-body">
            <div id="response-message" class="alert" style="display:none;"></div>
            <table class="table table-striped table-bordered" id="escalation-table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Project Name</th>
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

    // Fetch and display escalations
    $.ajax({
        url: 'admin/escalation.php',
        type: 'GET',
        dataType: 'json',
        data: { action: 'get_escalations' },
        success: function(response) {
            if (response.success && response.data.length > 0) {
                const tableBody = $('#escalation-table tbody');
                let count = 1;
                response.data.forEach(function(escalation) {
                    const editUrl = `admin_index.php?token=<?php echo $new_escalation_token; ?>&id=${escalation.id}`;
                    const row = `
                        <tr>
                            <td>${count++}</td>
                            <td>${escalation.project_name}</td>
                            <td>${escalation.created_on}</td>
                            <td>
                                <a href="${editUrl}" class="btn btn-sm btn-info">Edit</a>
                            </td>
                        </tr>`;
                    tableBody.append(row);
                });
            } else {
                $('#escalation-table tbody').append('<tr><td colspan="4" class="text-center">No escalations found.</td></tr>');
            }
        },
        error: function() {
            responseMessage.addClass('alert-danger').text('Failed to load escalations.').show();
        }
    });
});
</script>