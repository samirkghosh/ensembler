<?php
/**
 * Auth: Vastvikta Nishad
 * Modification by AI - 2024-07-22
 * Description: Display project. Rewritten for security.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enforce security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:;");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: no-referrer");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// The function being called is in web_admin_function.php
$display_name = "Department"; // Assuming this is defined somewhere, providing a default.
$SiteURL = isset($SiteURL) ? htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8') : ''; // Make sure SiteURL is defined and sanitized
?>
<div class="style2-table">
    <div class="style-title">
        <h3>Department</h3>
        <div class="row">
             <?php $new_project_token = base64_encode('new_project');?>
            <div class="col-sm-3" style="text-align: end;">
                <a href="admin_index.php?token=<?php echo htmlspecialchars($new_project_token, ENT_QUOTES, 'UTF-8');?>" class="button-orange1">New Department</a>
            </div>
         </div>  
    <div id="success"></div>
    <form name="frmService" action="" method="post" id="project_view_form">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <div class="table" id="SRallview">
            <div class="">
                <div class="div2">
                    <table class="tableview tableview-2" id="admin_table">
                        <thead>
                            <tr class="background">
                                <td align="left">S.No.</td>
                                <td align="left"><?php echo htmlspecialchars($display_name, ENT_QUOTES, 'UTF-8'); ?> Name</td>
                                <td align="left">Category</td>
                                <td align="left">Created By</td>
                                <td align="left">Created On</td>
                                <td align="left">Action</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $resultSet = view_project(); // This function is now secured
                            if ($resultSet && mysqli_num_rows($resultSet) > 0) {
                                $no = 1;
                                while ($res = mysqli_fetch_assoc($resultSet)) {
                                    $pId_safe = htmlspecialchars($res['pId'], ENT_QUOTES, 'UTF-8');
                                    $id_b64 = base64_encode($res['pId']);
                                ?>
                                <tr>
                                    <td align="left"><?php echo $no++; ?></td>
                                    <td align="left"><?php echo htmlspecialchars(ucfirst(strtolower($res['vProjectName'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td align="left">
                                        <?php
                                        if (!empty($res['Type'])) {
                                            $type_arr = explode(',', $res['Type']);
                                            $cat_names = [];
                                            foreach ($type_arr as $cat_id) {
                                                // category() function is now secure
                                                $cat_names[] = htmlspecialchars(category(trim($cat_id)), ENT_QUOTES, 'UTF-8');
                                            }
                                            echo implode(', ', $cat_names);
                                        }
                                        ?>
                                    </td>
                                    <td align="left"><?php echo htmlspecialchars($res['v_CreateBY'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td align="left"><?php echo htmlspecialchars($res['d_datetime'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <a href="admin_index.php?token=<?php echo $new_project_token;?>&id=<?php echo $id_b64; ?>">
                                            <img src="<?php echo $SiteURL; ?>public/images/edit-icon.png" border="0" alt="Edit" />
                                        </a>
                                        <a href="#" data-id="<?php echo $pId_safe; ?>" class="project_delete">
                                            <img src="<?php echo $SiteURL; ?>public/images/delete-icon.png" border="0" alt="delete">
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                }
                            } else {
                                echo "<tr><td colspan='6'>No departments found.</td></tr>";
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.project_delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this department?')) {
                const id = this.getAttribute('data-id');
                const csrfToken = document.querySelector('input[name="csrf_token"]').value;
                
                const formData = new FormData();
                formData.append('action', 'project_delete');
                formData.append('id', id);
                formData.append('csrf_token', csrfToken);

                fetch('web_admin_function.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Department deleted successfully.');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Could not delete department.'));
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });
});
</script>
