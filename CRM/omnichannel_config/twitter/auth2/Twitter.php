<?php

$full_url = 'https://api.twitter.com/2/dm_events?dm_event.fields=id,text,event_type,dm_conversation_id,created_at,sender_id,attachments,participant_ids,referenced_tweets&expansions=sender_id,referenced_tweets.id,attachments.media_keys,participant_ids';
// $curl = curl_init();
$headerTest = buildOauth($full_url);// Construct headers correctly

echo"<br/>";echo"header Old : ";print_r($headerTest);echo"<br/>";echo"<br/>";
$testt = array(
    'Content-Type: application/json',
    'Authorization: OAuth oauth_consumer_key="iiMI8WLS9qV70wbrwANJfD40y",oauth_token="1717832469775351808-KJckLxGQ0KKCxNEM8OZqVrEquSOimp",oauth_signature_method="HMAC-SHA1",oauth_timestamp="1716897471",oauth_nonce="Qkd3xhQSUO1",oauth_version="1.0",oauth_signature="6ROwhNAHHa3PF9AnnE4cP4o7rXw%3D"'
  );
echo"<br/>";echo"header Old : ";print_r($testt);echo"<br/>";echo"<br/>";
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.twitter.com/2/dm_events?dm_event.fields=id,text,event_type,dm_conversation_id,created_at,sender_id,attachments,participant_ids,referenced_tweets&expansions=sender_id,referenced_tweets.id,attachments.media_keys,participant_ids',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => $headerTest
  // CURLOPT_HTTPHEADER => array(
  //   'Content-Type: application/json',
  //   'Authorization: OAuth oauth_consumer_key="iiMI8WLS9qV70wbrwANJfD40y",oauth_token="1717832469775351808-KJckLxGQ0KKCxNEM8OZqVrEquSOimp",oauth_signature_method="HMAC-SHA1",oauth_timestamp="1716897471",oauth_nonce="Qkd3xhQSUO1",oauth_version="1.0",oauth_signature="6ROwhNAHHa3PF9AnnE4cP4o7rXw%3D"'
  // ),
));
$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curl_error = curl_error($curl);
curl_close($curl);

echo "HTTP Status Code: $http_code\n";
echo "Response: $response\n";
echo "cURL Error: $curl_error\n";

if ($http_code == 401) {
    echo "Unauthorized: Please check your OAuth credentials.\n";
}

function buildOauth($full_url){
    $consumer_key              = 'iiMI8WLS9qV70wbrwANJfD40y';
    $consumer_secret           = 'xGpk1kNk7pVtjR4EYJRqh50ejdj2wyXxaI3ZD6p1oJ6q1pQC6Q';
    $oauth_access_token        = '1717832469775351808-KJckLxGQ0KKCxNEM8OZqVrEquSOimp';
    $oauth_access_token_secret = 'eGArzTvb99VuJP6Eo1bPaUVYJaGgEjsG2WFmXFK2IylRA';

    $method = "GET";
    $params = array(
        "oauth_consumer_key" => $consumer_key,
        "oauth_nonce" => uniqid(mt_rand(), true),
        "oauth_signature_method" => "HMAC-SHA1",
        "oauth_timestamp" => time(),
        "oauth_token" => $oauth_access_token,
        "oauth_version" => "1.0"
    );

    // Combine and sort parameters
    $allParams = array_merge($params, array(/* Other request parameters */));
    ksort($allParams);

    // Generate base string
    $baseString = strtoupper($method) . "&" . rawurlencode($full_url) . "&" . rawurlencode(http_build_query($allParams));

    // Generate signing key
    $signingKey = rawurlencode($consumer_secret) . "&" . rawurlencode($oauth_access_token_secret);

    // Generate signature
    $signature = base64_encode(hash_hmac("sha1", $baseString, $signingKey, true));

    // Construct the Authorization header
    $authorizationHeader = 'OAuth oauth_consumer_key="' . rawurlencode($consumer_key) . '", oauth_nonce="' . rawurlencode($params["oauth_nonce"]) . '", oauth_signature="' . rawurlencode($signature) . '", oauth_signature_method="HMAC-SHA1", oauth_timestamp="' . rawurlencode($params["oauth_timestamp"]) . '", oauth_token="' . rawurlencode($oauth_access_token) . '", oauth_version="1.0"';
    
    // Return array of headers
    return array(
        'Content-Type: application/json',
        'Authorization: ' . $authorizationHeader
    );
}
?>
