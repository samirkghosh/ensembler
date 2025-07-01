<?php
/**
 * View for displaying all sub-categories.
 *
 * @author Vastvikta Nishad
 * @since 2024-07-23 (Refactored by AI)
 */

//- [[ SECURITY ]]
// no direct access
if ( !defined( 'ENVO_PREVENT_ACCESS' ) ) die( '[ ENVO ] NO DIRECT ACCESS' );

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://code.jquery.com; style-src 'self' 'unsafe-inline';");
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// Generate and store CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
$new_sub_category_token = base64_encode('new_sub_category');
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Sub-Category List</h5>
            <a href="admin_index.php?token=<?php echo htmlspecialchars($new_sub_category_token, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary float-end">New Sub-Category</a>
        </div>
        <div class="card-body">
            <div id="response-message" class="alert" style="display:none;"></div>
            <table class="table table-striped table-bordered" id="subcategory-table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Sub-Category Name</th>
                        <th>Parent Category</th>
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

    // Fetch and display sub-categories
    $.ajax({
        url: 'admin/sub_category.php',
        type: 'GET',
        dataType: 'json',
        data: { action: 'get_subcategories' },
        success: function(response) {
            if (response.success && response.data.length > 0) {
                const tableBody = $('#subcategory-table tbody');
                let count = 1;
                response.data.forEach(function(subcat) {
                    const editUrl = `admin_index.php?token=<?php echo $new_sub_category_token; ?>&id=${subcat.id}`;
                    const row = `
                        <tr>
                            <td>${count++}</td>
                            <td>${subcat.name}</td>
                            <td>${subcat.parent_category}</td>
                            <td>${subcat.created_on}</td>
                            <td>
                                <a href="${editUrl}" class="btn btn-sm btn-info">Edit</a>
                                <button class="btn btn-sm btn-danger delete-subcategory" data-id="${subcat.id}">Delete</button>
                            </td>
                        </tr>`;
                    tableBody.append(row);
                });
            } else {
                $('#subcategory-table tbody').append('<tr><td colspan="5" class="text-center">No sub-categories found.</td></tr>');
            }
        },
        error: function() {
            responseMessage.addClass('alert-danger').text('Failed to load sub-categories.').show();
        }
    });

    // Handle delete button click
    $('body').on('click', '.delete-subcategory', function() {
        if (!confirm('Are you sure you want to delete this sub-category?')) return;

        const id = $(this).data('id');
        const button = $(this);

        $.ajax({
            url: 'admin/sub_category.php',
            type: 'POST',
            data: {
                id: id,
                csrf_token: csrfToken,
                action: 'delete_subcategory'
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
