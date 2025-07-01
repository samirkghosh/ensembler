<?php
/**
 * View for assigning projects to users.
 *
 * @author Vastvikta Nishad
 * @since 2024-07-23 (Refactored by AI)
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
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Assign Projects to Back Officer</h5>
        </div>
        <div class="card-body">
            <div id="response-message" class="alert" style="display:none;"></div>
            <form id="assignmentForm" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="assign_projects">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="user_id" class="form-label">Select Back Officer</label>
                        <div class="input-group">
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="">Loading users...</option>
                            </select>
                            <button class="btn btn-secondary" type="button" id="showProjectsBtn">Show Projects</button>
                        </div>
                    </div>
                </div>

                <div id="projects-container" style="display:none;">
                    <h6 class="mb-3">Available Projects</h6>
                    <div id="projects-list" class="list-group">
                        <!-- Projects will be loaded here via AJAX -->
                    </div>
                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary">Update Assignments</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const responseMessage = $('#response-message');
    const userSelect = $('#user_id');
    const projectsContainer = $('#projects-container');
    const projectsList = $('#projects-list');

    // 1. Fetch users for the dropdown
    $.ajax({
        url: 'admin/user_project_assignment.php',
        type: 'GET',
        data: { action: 'get_users' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                userSelect.empty().append('<option value="">Select a Back Officer</option>');
                response.data.forEach(function(user) {
                    userSelect.append(`<option value="${user.id}">${user.name}</option>`);
                });
            } else {
                userSelect.empty().append('<option value="">Could not load users</option>');
            }
        }
    });

    // 2. Show projects on button click
    $('#showProjectsBtn').on('click', function() {
        const userId = userSelect.val();
        if (!userId) {
            alert('Please select a user first.');
            return;
        }

        $.ajax({
            url: 'admin/user_project_assignment.php',
            type: 'GET',
            data: { action: 'get_projects', user_id: userId },
            dataType: 'json',
            success: function(response) {
                projectsList.empty();
                if (response.success && response.data.length > 0) {
                    response.data.forEach(function(project) {
                        const isChecked = project.is_assigned ? 'checked' : '';
                        const projectItem = `
                            <label class="list-group-item">
                                <input class="form-check-input me-1" type="checkbox" name="project_ids[]" value="${project.id}" ${isChecked}>
                                ${project.name}
                            </label>`;
                        projectsList.append(projectItem);
                    });
                    projectsContainer.show();
                } else {
                    projectsList.html('<p class="text-center">No projects found.</p>');
                    projectsContainer.show();
                }
            }
        });
    });

    // 3. Handle form submission
    $('#assignmentForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);

        $.ajax({
            url: 'admin/user_project_assignment.php',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                responseMessage.removeClass('alert-danger alert-success').show();
                if (response.success) {
                    responseMessage.addClass('alert-success').text(response.message);
                } else {
                    responseMessage.addClass('alert-danger').text(response.message);
                }
                // Hide message after 3 seconds
                setTimeout(() => responseMessage.fadeOut(), 3000);
            },
            error: function() {
                responseMessage.addClass('alert-danger').text('An unexpected error occurred.').show();
            }
        });
    });
});
</script>
