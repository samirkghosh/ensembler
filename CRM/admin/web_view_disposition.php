<?php
/**
 * Auth: Vastvikta Nishad
 * Date:  18 Feb 2024
 * Modification by AI - 2024-07-22
 * Description: To Display and Delete Disposition. Rewritten for security.
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

$SiteURL = isset($SiteURL) ? htmlspecialchars($SiteURL, ENT_QUOTES, 'UTF-8') : '';
?>
<div class="style2-table">
    <div class="style-title">
        <h3>Disposition</h3>
        <div class="row">
            <?php $new_disposition_token = base64_encode('new_disposition');?>
            <div class="col-sm-3" style="text-align: end;">
                <a href="admin_index.php?token=<?php echo htmlspecialchars($new_disposition_token, ENT_QUOTES, 'UTF-8');?>" class="button-orange1">New Disposition </a>
            </div>
        </div>  
    </div>
    <div id="success"></div>
    <form name="frmService" id="viewDispositionForm" action="" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <div class="table" id="SRallview"> 
            <div class="">
                <div class="div2">
                    <table class="tableview tableview-2" id="admin_table">
                        <thead>
                            <tr class="background">
                                <td align="left" width="10%">S.No.</td>
                                <td align="left">Disposition</td>
                                <td align="left" width="10%">Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $res = getDisposition(); // Secured function
                                if ($res && mysqli_num_rows($res) > 0) {
                                    $count = 1;
                                    while ($row = mysqli_fetch_assoc($res)) {
                                        $id_safe = htmlspecialchars($row['I_ID'], ENT_QUOTES, 'UTF-8');
                                        $name_safe = htmlspecialchars($row['V_DISPO'], ENT_QUOTES, 'UTF-8');
                                        $id_b64 = base64_encode($row['I_ID']);
                            ?>
                            <tr>
                                <td align="left"><?php echo $count++; ?></td>
                                <td align="left"><?php echo $name_safe; ?></td>
                                <td align="left">
                                    <a href="admin_index.php?token=<?php echo $new_disposition_token; ?>&id=<?php echo $id_b64; ?>">
                                        <img src="<?php echo $SiteURL; ?>public/images/edit-icon.png" border="0" alt="Edit" />
                                    </a>
                                    <a href="#" data-id="<?php echo $id_safe; ?>" class="disposition_delete">
                                        <img src="<?php echo $SiteURL; ?>public/images/delete-icon.png" border="0" alt="delete">
                                    </a>
                                </td>
                            </tr>
                            <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="3" align="center" class="contentred">No records found!</td></tr>';
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
    document.querySelectorAll('.disposition_delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this disposition?')) {
                const id = this.getAttribute('data-id');
                const csrfToken = document.querySelector('input[name="csrf_token"]').value;
                
                const formData = new FormData();
                formData.append('action', 'disposition_delete');
                formData.append('id', id);
                formData.append('csrf_token', csrfToken);

                fetch('web_admin_function.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Disposition deleted successfully.');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.error_msg || 'Could not delete disposition.'));
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });
});
</script>
