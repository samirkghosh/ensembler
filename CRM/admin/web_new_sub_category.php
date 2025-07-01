<?php
/**
 * View for creating or editing a sub-category.
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

// --- [[ DATA FETCHING ]] ---
// Note: The actual data fetching for the form will be done via AJAX.
// We just need the ID for edit mode.
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
?>

<div id="response-message" class="alert" style="display:none;"></div>
<form id="subCategoryForm" method="post">
    <input type="hidden" name="id" id="sub_category_id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <input type="hidden" name="action" value="submit_subcategory">

    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><?php echo $id ? 'Edit' : 'New'; ?> Sub-Category</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="category_id" class="form-label">Parent Category <span class="text-danger">*</span></label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="">Loading categories...</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Sub-Category Name <span class="text-danger">*</span></label>
                    <input name="name" id="name" type="text" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="4" class="form-control"></textarea>
                </div>
            </div>
            <div class="card-footer text-center">
                <button type="submit" class="btn btn-primary"><?php echo $id ? 'Update' : 'Create'; ?></button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    const id = $('#sub_category_id').val();
    const categorySelect = $('#category_id');

    // Fetch parent categories for the dropdown
    $.ajax({
        url: 'admin/sub_category.php',
        type: 'GET',
        data: { action: 'get_parent_categories' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                categorySelect.empty().append('<option value="">Select Parent Category</option>');
                response.data.forEach(function(cat) {
                    categorySelect.append(`<option value="${cat.id}">${cat.name}</option>`);
                });
                // If in edit mode, fetch the sub-category data and select the correct parent
                if (id) {
                    fetchSubCategoryData(id);
                }
            } else {
                categorySelect.empty().append('<option value="">Could not load categories</option>');
            }
        }
    });

    // Function to fetch sub-category data for editing
    function fetchSubCategoryData(subId) {
        // This requires adding a 'get_subcategory' action to the controller.
        // For now, we assume the controller is extended to handle this.
        // As a placeholder, let's assume get_subcategory_data is available in sub_category.php
        // This part needs the controller to have a `get_single_subcategory` action.
    }


    $('#subCategoryForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const responseMessage = $('#response-message');

        $.ajax({
            url: 'admin/sub_category.php',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                responseMessage.removeClass('alert-danger alert-success').show();
                if (response.success) {
                    responseMessage.addClass('alert-success').text(response.message);
                    setTimeout(function() {
                        var encodedToken = btoa('view_sub_category');
                        window.location.href = "admin_index.php?action=view_sub_category&token=" + encodeURIComponent(encodedToken);
                    }, 1500);
                } else {
                    responseMessage.addClass('alert-danger').text(response.message);
                }
            },
            error: function() {
                responseMessage.removeClass('alert-success').addClass('alert-danger').text('An unexpected error occurred.').show();
            }
        });
    });
});
</script>
