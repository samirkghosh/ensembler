<?php
/**
 * Author: Vastvikta Nishad
 * Date: 16 Feb 2024
 * Description: To Update Callbacks
 *
 * MODIFIED
 *      by      : AI
 *      on      : 22-July-2024
 *
 *      purpose : Security overhaul - XSS, CSRF, Prepared Statements
 */

//- [[ SECURITY ]]
// no direct access
if ( !defined( 'ENVO_PREVENT_ACCESS' ) ) die( '[ ENVO ] NO DIRECT ACCESS' );

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://code.jquery.com https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0;");
header("X-XSS-Protection: 1; mode=block");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// Generate and store CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// --- [[ GETTING DATA ]]
$id = isset($_GET['id']) ? base64_decode(filter_var($_GET['id'], FILTER_SANITIZE_URL)) : '';
if (!is_numeric($id)) {
    die('Invalid ID');
}

$res = get_callbacks($id);
if (!$res) {
    die('Callback not found.');
}

$callback_alert_time = htmlspecialchars($res['callback_alert_time'] ?? '00:00:00', ENT_QUOTES, 'UTF-8');
$callback_time_raw = $res['callback_time'] ?? '';
$callback_time = htmlspecialchars(date('Y-m-d\TH:i:s', strtotime($callback_time_raw)), ENT_QUOTES, 'UTF-8');
$callback_remark = htmlspecialchars($res['remark'] ?? '', ENT_QUOTES, 'UTF-8');

list($hours, $minutes) = explode(':', $callback_alert_time);
?>
<div id="response-message" class="alert" style="display:none;"></div>

<form id="updateCallbackForm" method="post">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <input type="hidden" name="action" value="submit_callbacks">

    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Reschedule Callback Time</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Callback Date::Time -->
                    <div class="col-md-6 mb-3">
                        <label for="callback_time" class="form-label">Callback Date & Time <span class="text-danger">*</span></label>
                        <input name="callback_time" id="callback_time" type="datetime-local" value="<?php echo $callback_time; ?>" class="form-control">
                    </div>

                    <!-- Callback Alert Time -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Callback Alert Time <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="callback_alert_hour" id="callback_alert_hour" class="form-select">
                                <?php for ($h = 0; $h < 24; $h++): ?>
                                    <?php $hour = str_pad($h, 2, '0', STR_PAD_LEFT); ?>
                                    <option value="<?php echo $hour; ?>" <?php if ($hour == $hours) echo 'selected'; ?>>
                                        <?php echo $hour; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <span class="input-group-text">Hour</span>
                            <select name="callback_alert_minute" id="callback_alert_minute" class="form-select">
                                <?php for ($m = 0; $m < 60; $m++): ?>
                                    <?php $minute = str_pad($m, 2, '0', STR_PAD_LEFT); ?>
                                    <option value="<?php echo $minute; ?>" <?php if ($minute == $minutes) echo 'selected'; ?>>
                                        <?php echo $minute; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <span class="input-group-text">Min</span>
                        </div>
                    </div>
                </div>

                <!-- Callback Remark -->
                <div class="mb-3">
                    <label for="callback_remark" class="form-label">Callback Remark <span class="text-danger">*</span></label>
                    <textarea name="callback_remark" id="callback_remark" class="form-control" rows="4"><?php echo $callback_remark; ?></textarea>
                </div>
            </div>
            <div class="card-footer text-center">
                <button name="Update" type="submit" class="btn btn-primary" id="submitclkcallbacks">Update</button>
            </div>
        </div>
    </div>
</form>
