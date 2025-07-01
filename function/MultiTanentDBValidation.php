<?php
/**   
 * Author: Farhan Akhtar
 * Date: 25-12-2024
 * Fetch the database name for a given company ID and validate its existence.
 *
 * @param string $companyID The ID of the company to validate.
 * @return array An associative array with 'status', 'message', and optionally 'database_name'.
 */
function getDatabaseForCompany($companyID,$DBcreds) {
    // Database configuration constants
    $configHost = $DBcreds['configdbhost'];
    $configUser = $DBcreds['configdbuser'];
    $configPass = $DBcreds['configdbpass'];

    // Connect to CampaignTracker database
    $campaignDBConnection = mysqli_connect($configHost, $configUser, $configPass, 'CampaignTracker');
    if (!$campaignDBConnection) {
        return [
            'status' => false,
            'message' => 'Failed to connect to CampaignTracker database.'
        ];
    }

    // Query to get the related database name
    $query = "SELECT related_database_name, campaign_id, company_name FROM companies WHERE company_id = ?";
    $stmt = mysqli_prepare($campaignDBConnection, $query);
    mysqli_stmt_bind_param($stmt, "s", $companyID);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $company = mysqli_fetch_assoc($result);
        $relatedDatabaseName = $company['related_database_name'];
        $relatedCampaignName = $company['campaign_id'];
        $relatedCompanyName = $company['company_name'];

        // Check if the related database exists
        $checkDBConnection = mysqli_connect($configHost, $configUser, $configPass, $relatedDatabaseName);
        if ($checkDBConnection) {
            // mysqli_close($checkDBConnection); // Close connection after confirmation
            // mysqli_close($campaignDBConnection); // Close CampaignTracker connection

            // Return success with the related database name
            return [
                'status' => true,
                'message' => 'Database found and validated.',
                'database_name' => $relatedDatabaseName,
                'campaign_name' => $relatedCampaignName,
                'company_name' => $relatedCompanyName
            ];
        } else {
            mysqli_close($campaignDBConnection);
            return [
                'status' => false,
                'message' => 'Related database does not exist.'
            ];
        }
    } else {
        // Company not found
        mysqli_close($campaignDBConnection);
        return [
            'status' => false,
            'message' => 'Invalid Company ID'
        ];
    }
}

?>
