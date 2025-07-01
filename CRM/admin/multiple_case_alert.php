
<?php
/*
Author:Vastvikta Nishad
Date:25-04-2025
Description:to update details related to sending excalation mail ;
*/
  include "../../config/web_mysqlconnect.php";
  global $db, $link;
  function getMailSettings()
  {
      global $link, $db;
  
      $query = "SELECT sent_mail, case_count, status_active FROM {$db}.tbl_connection LIMIT 1";
      $result = mysqli_query($link, $query);
  
      if ($result && mysqli_num_rows($result) > 0) {
          $row = mysqli_fetch_assoc($result); // Fetch as an associative array
          return $row; // Returns array with keys: sent_mail, case_count, status_active
      } else {
          return null; // Handle error appropriately
      }
  }
?>
<div class="style2-table">
  <div class="style-title">
    <h3>Mail Alert</h3>
    <?php if (!empty($msg)) echo "<p style='color: green;'>$msg</p>"; ?>
    <?php
      $MailSettings = getMailSettings();
    ?>
    <form name="myform" method="post">
      <table class="table table-bordered main-form">
        <thead>
          <tr>
            <td style="font-size:12px;">Multiple Case Creation Email</td>
            <td>
              <input type="text" name="sent_mail" id="sent_mail" class="input-style1" 
                value="<?= $MailSettings['sent_mail']; ?>" disabled>
            </td>
            <td style="font-size:12px;">Multiple Case Creation Count</td>
            <td>
              <input type="number" name="case_count" id="case_count" class="input-style1" 
                value="<?= $MailSettings['case_count']; ?>" min="0" disabled>
            </td>
          </tr>
          <tr>
            <td style="font-size:12px;">Status</td>
            <td>
              <label>
                <input type="radio" name="status_active" class="status_active" style="font-size:1px;" value="1" 
                  <?php if ($MailSettings['status_active'] == 1) echo "checked"; ?> disabled> Active
              </label>
              <label style="margin-left: 15px;">
                <input type="radio" name="status_active" class="status_active" value="0" 
                  <?php if ($MailSettings['status_active'] == 0) echo "checked"; ?> disabled> Inactive
              </label>
            </td>
            <td>
              <input type="button" name="edit" id="edit" value="Edit" class="button-orange1">
              <input type="submit" name="update_sent_mail" id="update_sent_mail" value="Update" class="button-orange1" disabled>
            </td>
          </tr>
        </thead>
      </table>
    </form>
  </div>
</div>

<script type="text/javascript">
  $(function () {
    // Enable form on Edit click
    $("#edit").on("click", function () {
      $("#sent_mail").prop("disabled", false);
      $("#case_count").prop("disabled", false);
      $(".status_active").prop("disabled", false); 
      $("#update_sent_mail").prop("disabled", false);
    });
  });
</script>