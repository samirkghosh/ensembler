<?php 
/***
 * Script
 * Author: Vastvikta Nishad
 * Date: 12-02-2025
 * This file handles updating the logout time after 30 minutes of inactivity.
***/

// Include the database connection file
include_once("/var/www/html/ensembler/config/web_mysqlconnect.php");

// Master database
$masterdb = 'CampaignTracker';
global $configdbhost, $configdbuser, $configdbpass;

// Establish connection to the master database
$link = mysqli_connect($configdbhost, $configdbuser, $configdbpass);
if (!$link) {
    die('Failed to connect to CampaignTracker database: ' . mysqli_connect_error());
}

// Query to get the related database name
$query = "SELECT related_database_name FROM $masterdb.companies";
$stmts = mysqli_prepare($link, $query);

if ($stmts) {
    mysqli_stmt_execute($stmts);
    $results = mysqli_stmt_get_result($stmts);
    
    if ($results && mysqli_num_rows($results) > 0) {
        while ($company = mysqli_fetch_assoc($results)) {
            $childdb = $company['related_database_name'];
            echo "############### Company Database Name: ".$childdb."<br/>";
            Update_Logout_Time($childdb, $link);
        }
    } else {
        echo "No company databases found.";
        die;
    }
} else {
    die("Query preparation failed: " . mysqli_error($link));
}

function Update_Logout_Time($childdb, $link) {
    if (!$link) {
        echo "Database connection is missing.<br/>";
        return;
    }

    $sql2 = "SELECT ugdContactID FROM $childdb.unigroupdetails WHERE atxGid = '080000'";
    
    $result2 = mysqli_query($link, $sql2);
    
    if (!$result2) {
        echo "Error fetching unigroupdetails: " . mysqli_error($link) . "<br/>";
        return;
    }
    
    while ($row2 = mysqli_fetch_assoc($result2)) {
        
        if (!isset($row2['ugdContactID'])) {
            echo "ugdContactID not found.<br/>";
            continue;
        }
        
        $ugdContactID = $row2['ugdContactID'];
        
        // Fetch user profile matching the contact ID
        $sql_select = "SELECT login_datetime FROM $childdb.uniuserprofile WHERE AtxUserID = '$ugdContactID'";
        $result = mysqli_query($link, $sql_select);
        
        if ($result && $row = mysqli_fetch_assoc($result)) {
            if (!isset($row['login_datetime'])) {
                echo "login_datetime not found for AtxUserID: $ugdContactID<br/>";
                continue;
            }

            $login_datetime = $row['login_datetime'];
            $time_diff = time() - strtotime($login_datetime);
            
            if ($time_diff > 60) { // if more than 15 minutes have passed
                $logintime = date("Y-m-d H:i:s"); // Current timestamp for update
                
                $sql_update = "UPDATE $childdb.uniuserprofile 
                               SET login_status = 'offline', login_datetime = '$logintime' 
                               WHERE AtxUserID = '$ugdContactID'";
                
                if (mysqli_query($link, $sql_update)) {
                    echo "User login status updated for AtxUserID: $ugdContactID<br/>";
                } else {
                    echo "Error updating AtxUserID: $ugdContactID - " . mysqli_error($link) . "<br/>";
                }
                
            }
        } else {
            echo "No login_datetime found for AtxUserID: $ugdContactID<br/>";
        }
    }
}
?>
