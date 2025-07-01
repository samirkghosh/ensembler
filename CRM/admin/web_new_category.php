<?php
/**
 * View for creating or editing a category.
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

// --- [[ DATA FETCHING ]] ---
// Include the controller to use its functions
require_once 'category.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$category_data = $id ? get_category_data($id) : ['type' => '', 'category' => '', 'VDescription' => ''];
$complaint_types = get_category_types();
?>
<div id="response-message" class="alert" style="display:none;"></div>
<form id="categoryForm" method="post">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <input type="hidden" name="action" value="submit_category">

    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><?php echo $id ? 'Edit' : 'New'; ?> Category</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="type" class="form-label">Complaint Type <span class="text-danger">*</span></label>
                    <select name="type" id="type" class="form-select" required>
                        <option value="">Select Complaint Type</option>
                        <?php foreach ($complaint_types as $type): ?>
                            <option value="<?php echo $type; ?>" <?php echo ($category_data['type'] == $type) ? 'selected' : ''; ?>>
                                <?php echo $type; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="category" class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input name="category" id="category" type="text" value="<?php echo $category_data['category']; ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="V_Description" class="form-label">Description</label>
                    <textarea name="V_Description" id="V_Description" rows="4" class="form-control"><?php echo $category_data['VDescription']; ?></textarea>
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
    $('#categoryForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const responseMessage = $('#response-message');

        $.ajax({
            url: 'admin/category.php',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                responseMessage.removeClass('alert-danger alert-success').show();
                if (response.success) {
                    responseMessage.addClass('alert-success').text(response.message);
                    setTimeout(function() {
                        var encodedToken = btoa('view_category');
                        window.location.href = "admin_index.php?action=view_category&token=" + encodeURIComponent(encodedToken);
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