<?php
// config.php or top of dynamic.php
$clientId = 'bc1c15fd-f33c-4f04-baf6-2c9aab3075dc';
$clientSecret = 't1n8Q~1iqqKES1OVTa477BxfXr8J3w8HeRhHqdoq';
$tenantId = '307e0292-8b9e-4a20-b1d0-a7349267c658';
$redirectUri = 'https://yourdomain.com/dynamic.php';
$scope = 'https://graph.microsoft.com/.default';  // or use Dynamics scope
$authority = "https://login.microsoftonline.com/$tenantId/oauth2/v2.0";

return [
    'client_id' => 'bc1c15fd-f33c-4f04-baf6-2c9aab3075dc',
    'client_secret' => 't1n8Q~1iqqKES1OVTa477BxfXr8J3w8HeRhHqdoq',
    'tenant_id' => '307e0292-8b9e-4a20-b1d0-a7349267c658',
    'resource' => 'https://org07563e53.crm8.dynamics.com',
    'orgUrl' => 'https://org07563e53.crm8.dynamics.com'
];
?>