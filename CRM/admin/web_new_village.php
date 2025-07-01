<?php
/**
 * View for creating or editing a village/district.
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
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
?>

<div id="response-message" class="alert" style="display:none;"></div>
<form id="villageForm" method="post">
    <input type="hidden" name="id" id="village_id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <input type="hidden" name="action" value="submit_village">

    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><?php echo $id ? 'Edit' : 'New'; ?> Village/District</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="district_id" class="form-label">Province (District) <span class="text-danger">*</span></label>
                    <select name="district_id" id="district_id" class="form-select" required>
                        <option value="">Loading provinces...</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">District (Village) Name <span class="text-danger">*</span></label>
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
    const villageId = $('#village_id').val();
    const districtSelect = $('#district_id');
    const nameInput = $('#name');
    const descriptionInput = $('#description');

    // Fetch parent districts for the dropdown
    $.ajax({
        url: 'admin/village.php',
        type: 'GET',
        data: { action: 'get_districts' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                districtSelect.empty().append('<option value="">Select Province</option>');
                response.data.forEach(function(district) {
                    districtSelect.append(`<option value="${district.id}">${district.name}</option>`);
                });

                if (villageId) {
                    // Fetch this specific village's data to populate the form
                    $.ajax({
                        url: 'admin/village.php',
                        type: 'GET',
                        data: { action: 'get_village', id: villageId },
                        dataType: 'json',
                        success: function(villageResponse) {
                            if (villageResponse.success && villageResponse.data) {
                                nameInput.val(villageResponse.data.name);
                                descriptionInput.val(villageResponse.data.description);
                                districtSelect.val(villageResponse.data.district_id);
                            }
                        }
                    });
                }
            } else {
                districtSelect.empty().append('<option value="">Could not load provinces</option>');
            }
        }
    });
    
    $('#villageForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const responseMessage = $('#response-message');

        $.ajax({
            url: 'admin/village.php',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                responseMessage.removeClass('alert-danger alert-success').show();
                if (response.success) {
                    responseMessage.addClass('alert-success').text(response.message);
                    setTimeout(function() {
                        var encodedToken = btoa('view_village');
                        window.location.href = "admin_index.php?action=view_village&token=" + encodeURIComponent(encodedToken);
                    }, 1500);
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