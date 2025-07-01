<?php
function shortenUrls($longUrl) {

    $apiUrl = "https://tinyurl.com/api-create.php?url=" . urlencode($longUrl);

    $shortUrl = file_get_contents($apiUrl);

    return $shortUrl;

}

$longUrl = "https://alliance-infotech.in/ensembler/CRM/CES_NPS_feedback.php";

$shortUrl = shortenUrls($longUrl);


echo "Shortened URL: " . $shortUrl;
die;
 
?>