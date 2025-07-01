<?php
// Include the database connection file
include_once("/var/www/html/ensembler/config/web_mysqlconnect.php");

web_file_information();

// Function to send subject text to the sentiment analysis API
function analyze_sentiment($filename) {
    global $process_audio_url;

   // $file_url = 'http://142.93.209.191/calls/23Sep2024/20240923174632_40517771.wav';

   // Check if the extension is .WAV and replace it with .wav
   $filename = preg_replace('/\.WAV$/', '.wav', $filename);
echo "filename: ";print_r($filename); echo"<br/>";
    // Download the file locally
    $temp_file = tempnam(sys_get_temp_dir(), 'audio');
    //file_put_contents($temp_file, file_get_contents($file_url));
    file_put_contents($temp_file, file_get_contents($filename));

    // Ensure file is downloaded successfully
    if (!file_exists($temp_file)) {
        die("Error: Unable to download the file.");
    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $process_audio_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('file'=> new CURLFile($temp_file)),
    ));

    $response = curl_exec($curl);

    // Check for errors
    if (curl_errno($curl)) {
        echo 'Error:' . curl_error($curl);
    } else {
        echo "Response from API: " . $response;
    }

    // Close cURL session and remove temp file
    curl_close($curl);
    unlink($temp_file);
    return json_decode($response, true);
}
function web_file_information(){
    global $link,$db_asterisk,$uc_ip;
    // Query to fetch subjects from the database
    $qu = "SELECT * FROM $db_asterisk.autodial_agent_log where sentiment_score='' and filename !='' order by event_time DESC limit 50";
    $resu = mysqli_query($link, $qu);
    $num = mysqli_num_rows($resu);
    echo"<br/><br/>";echo "Total Record for analyze sentiment_score = ".$num. "<br>";echo"<br/><br/>";
    if($num == 0){
        echo "<br>......All checked analyze sentiment_score .........."; echo"<br/>";echo"<br/>";;
        exit;
    }
    while($ress=mysqli_fetch_array($resu)){ 
        $agent_log_id = $ress['agent_log_id'];

        $filename = $ress['filename'];

        $org_filename = getFileName($filename);
        $filename = "http://" . $uc_ip . $org_filename;
        
        $analysis_result = analyze_sentiment($filename);

        if ($analysis_result) {

            $sentiment_score = $analysis_result['sentiment_score'];
            $emotion = $analysis_result['emotion'];
            $text_link = addslashes($analysis_result['original_text']);
            $most_common_words = $analysis_result['most_common_words'];
            $sentiment = $analysis_result['sentiment'];

            foreach ($most_common_words as $key => $value) {
               $most_common_words_list[] =$value['0'];
            }
            $most_common = implode(",", $most_common_words_list);
            // print_r($most_common);
            echo"<pre>";print_r($most_common_words_list);
        } else {

            $sentiment = '';
            $sentiment_score = '';
            $emotion = '';
            $most_common_words = '';
            $text_link = '';
        }
        echo"<pre>";print_r($analysis_result);
        if(!empty($sentiment)){
            $sqlmit="UPDATE $db_asterisk.autodial_agent_log  SET sentiment='$sentiment', sentiment_score = '$sentiment_score',emotion = '$emotion',most_common_words = '$most_common',text_link='$text_link' WHERE agent_log_id ='$agent_log_id'";
            $resultt = mysqli_query($link, $sqlmit) or die(mysqli_error($link));
            echo $sqlmit; echo"<br/>";echo"<br/>";
        }
    }
}
function getFileName($SmartFileName){

    $filename=$SmartFileName;
    $filename=substr($filename, 0, 8);//12Jul2019
    $year=substr($filename, 0, 4);
    $day=substr($filename, 6, 2);
    $m=$year.substr($filename, 4, 2).$day;
    $month1=date('M',strtotime($m));
    //echo '<br>Date::'.$day.$month1.$year;
    $folderpath=$day.$month1.$year."/";

    $path='../calls/'.$folderpath.$SmartFileName.'.wav';
    $pathWithoutExtention='/calls/'.$folderpath.$SmartFileName;
    if (file_exists($path)) {
        $pathWithoutExtention=$pathWithoutExtention.".wav";
    }else{
        $pathWithoutExtention=$pathWithoutExtention.".WAV";
    }
    return  $pathWithoutExtention;

}//end of function 
?>
