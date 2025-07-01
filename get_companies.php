<?php
// Database credentials
$configdbhost = "165.232.183.220";
$configdbuser = "cron";
$configdbpass = "All1@nc3@1986!";
$dbname = "CampaignTracker";

// Create connection
$conn = new mysqli($configdbhost, $configdbuser, $configdbpass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT company_id, related_database_name, company_name FROM companies";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "Company ID: " . $row["company_id"]. " - Database Name: " . $row["related_database_name"]. " - Company Name: " . $row["company_name"]. "\n";
    }
} else {
    echo "0 results";
}
$conn->close();
?> 