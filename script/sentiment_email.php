<?php
// Include the database connection file
include_once("/var/www/html/ensembler/config/web_mysqlconnect.php");


// Master database
$masterdb = 'CampaignTracker';
global $configdbhost, $configdbuser, $configdbpass;

// Establish connection to the master database
$link = mysqli_connect($configdbhost, $configdbuser, $configdbpass);
if (!$link) {
    die('Failed to connect to CampaignTracker database.');
}

// Query to get the related database name
$query = "SELECT related_database_name FROM $masterdb.companies";
$stmts = mysqli_prepare($link, $query);
mysqli_stmt_execute($stmts);
$results = mysqli_stmt_get_result($stmts);

if (mysqli_num_rows($results) > 0){
    while ($company = $results->fetch_assoc()) {
        $childdb = $company['related_database_name'];

        echo " ############### Company Database Name : ".$childdb; echo"<br/>";

        web_email_information($childdb);
    }
}else{
    echo "No company databases found."; die;
}

// Function to send subject text to the sentiment analysis API
function analyze_sentiment($text) {
    global $email_analyze_sentiment_url;
    $data = array('text' => $text);
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ),
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($email_analyze_sentiment_url, false, $context);
    if ($result === FALSE) {
        return null;
    }
    
    return json_decode($result, true);
}

function web_email_information($childdb){
    global $link,$childdb;
    // Query to fetch subjects from the database
    $qu = "SELECT v_body,EMAIL_ID FROM $childdb.web_email_information where sentiment IS NULL order by d_email_date DESC limit 50";
    $resu = mysqli_query($link, $qu);
    $num = mysqli_num_rows($resu);
    echo "Total Record for analyze sentiment = ".$num. "<br>";
    if($num == 0){
        echo "<br>......All checked analyze sentiment .........."; echo"<br/>";echo"<br/>";;
    }else{
        while($ress=mysqli_fetch_array($resu)){ 

            $EMAIL_ID = $ress['EMAIL_ID'];
            $v_body = $ress['v_body'];
            $analysis_result = analyze_sentiment($v_body);
            if ($analysis_result) {
                $sentiment = $analysis_result['sentiment'];
                $polarity = $analysis_result['polarity'];
            } else {
                $sentiment = '';
                $polarity = '';
            }
            if(!empty($sentiment)){
                $sqlmit="UPDATE $childdb.web_email_information  SET polarity = '$polarity',sentiment='$sentiment' WHERE EMAIL_ID ='$EMAIL_ID'";
                $resultt = mysqli_query($link, $sqlmit) or die(mysqli_error($link));
                echo $sqlmit; echo"<br/>";echo"<br/>";
            }
        }
    }
}
?>