<?php
/**
 * View for displaying all villages/districts.
 *
 * @author Vastvikta Nishad
 * @since 2024-07-24 (Refactored by AI)
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
$new_village_token = base64_encode('new_village');
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Village/District List</h5>
            <a href="admin_index.php?token=<?php echo htmlspecialchars($new_village_token, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary float-end">New Village/District</a>
        </div>
        <div class="card-body">
            <div id="response-message" class="alert" style="display:none;"></div>
            <table class="table table-striped table-bordered" id="village-table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Village Name</th>
                        <th>District Name</th>
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

    // Fetch and display villages
    $.ajax({
        url: 'admin/village.php',
        type: 'GET',
        dataType: 'json',
        data: { action: 'get_villages' },
        success: function(response) {
            if (response.success && response.data.length > 0) {
                const tableBody = $('#village-table tbody');
                let count = 1;
                response.data.forEach(function(village) {
                    const editUrl = `admin_index.php?token=<?php echo $new_village_token; ?>&id=${village.id}`;
                    const row = `
                        <tr>
                            <td>${count++}</td>
                            <td>${village.name}</td>
                            <td>${village.district_name}</td>
                            <td>${village.created_on}</td>
                            <td>
                                <a href="${editUrl}" class="btn btn-sm btn-info">Edit</a>
                                <button class="btn btn-sm btn-danger delete-village" data-id="${village.id}">Delete</button>
                            </td>
                        </tr>`;
                    tableBody.append(row);
                });
            } else {
                $('#village-table tbody').append('<tr><td colspan="5" class="text-center">No villages found.</td></tr>');
            }
        },
        error: function() {
            responseMessage.addClass('alert-danger').text('Failed to load villages.').show();
        }
    });

    // Handle delete button click
    $('body').on('click', '.delete-village', function() {
        if (!confirm('Are you sure you want to delete this village?')) return;

        const id = $(this).data('id');
        const button = $(this);

        $.ajax({
            url: 'admin/village.php',
            type: 'POST',
            data: {
                id: id,
                csrf_token: csrfToken,
                action: 'delete_village'
            },
            dataType: 'json',
            success: function(response) {
                responseMessage.removeClass('alert-danger alert-success').show();
                if (response.success) {
                    responseMessage.addClass('alert-success').text(response.message);
                    button.closest('tr').remove();
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
