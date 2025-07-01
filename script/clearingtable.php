<?php 
/***
 * Script garbage
 * Author: Aarti Ojha
 * Date: 27-11-2024
 * This file is handling need to create one script to clean all garbage data from db before putting on any live site 
 * 
 */
include "../config/web_mysqlconnect.php"; // Connection to database // Please do not remove this

// Start clearing tables
Clear_Tables();

// This function clears data from all specified tables
function Clear_Tables() {
    global $link, $db;
    
    // List of tables to clear
    $tables = [
        "agent_break",
        "Instagram_in_queue",
        "Instagram_out_queue",
        "interaction",
        "logip",
        "messenger_in_queue",
        "messenger_out_queue",
        "multichannel_disposition",
        "tbl_ces",
        "tbl_civrs_cdr",
        "customer_effort",
        "uniautoatt",
        "tbl_facebook",
        "uniuserreportedto",
        "tbl_im_new",
        "tbl_nps",
        "tbl_smsmessages",
        "tbl_smsmessagesin",
        "tbl_survey_request",
        "tbl_tweet",
        "user_channel_assignment",
        "web_accounts",
        "web_audit_history",
        "web_case_interaction",
        "web_documents",
        "web_email_information",
        "web_email_information_out",
        "web_problemdefination",
        "web_twitter_directmsg",
        "web_wrapcall",
        "whatsapp_in_queue",
        "whatsapp_out_queue"
    ];

    // Loop through each table and delete its data
    foreach ($tables as $table) {
        echo "Start deleting data from table: $table<br/>";
        
        $delete_sql = "DELETE FROM $db.$table";
        if (mysqli_query($link, $delete_sql)) {
            echo "Successfully cleared table: $table<br/>";
        } else {
            echo "Error clearing table $table: " . mysqli_error($link) . "<br/>";
        }
    }
    
    echo "All specified tables have been processed.<br/>";
}
?>
