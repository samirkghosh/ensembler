<?php
/**
 * View for displaying all categories.
 *
 * @author Aarti Ojha
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
$new_category_token = base64_encode('new_category');
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Category List</h5>
            <a href="admin_index.php?token=<?php echo htmlspecialchars($new_category_token, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary float-end">New Category</a>
        </div>
        <div class="card-body">
            <div id="response-message" class="alert" style="display:none;"></div>
            <table class="table table-striped table-bordered" id="category-table">
                <thead>
                    <tr>
                        <th>S.No.</th>
                        <th>Category Name</th>
                        <th>Type</th>
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

    // Fetch and display categories
    $.ajax({
        url: 'admin/category.php',
        type: 'GET',
        dataType: 'json',
        data: { action: 'get_categories' },
        success: function(response) {
            if (response.success && response.data.length > 0) {
                const tableBody = $('#category-table tbody');
                let count = 1;
                response.data.forEach(function(cat) {
                    const editUrl = `admin_index.php?token=<?php echo $new_category_token; ?>&id=${cat.id}`;
                    const row = `
                        <tr>
                            <td>${count++}</td>
                            <td>${cat.name}</td>
                            <td>${cat.type}</td>
                            <td>${cat.created_on}</td>
                            <td>
                                <a href="${editUrl}" class="btn btn-sm btn-info">Edit</a>
                                <button class="btn btn-sm btn-danger delete-category" data-id="${cat.id}">Delete</button>
                            </td>
                        </tr>`;
                    tableBody.append(row);
                });
            } else {
                $('#category-table tbody').append('<tr><td colspan="5" class="text-center">No categories found.</td></tr>');
            }
        },
        error: function() {
            responseMessage.addClass('alert-danger').text('Failed to load categories.').show();
        }
    });

    // Handle delete button click
    $('body').on('click', '.delete-category', function() {
        if (!confirm('Are you sure you want to delete this category?')) return;

        const id = $(this).data('id');
        const button = $(this);

        $.ajax({
            url: 'admin/category.php',
            type: 'POST',
            data: {
                id: id,
                csrf_token: csrfToken,
                action: 'delete_category'
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