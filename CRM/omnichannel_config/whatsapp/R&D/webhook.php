<?php

$verify_token = "whatsapp_TOKEN"; // Used during webhook verification

// DB connection
$mysqli = new mysqli("165.232.183.220", "cron", "1234", "ensembler");
if ($mysqli->connect_error) {
    error_log("DB connection failed: " . $mysqli->connect_error);
    exit;
}

function getUserState($phone, $mysqli) {
    $stmt = $mysqli->prepare("SELECT current_level FROM user_states WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $stmt->bind_result($level);
    if ($stmt->fetch()) {
        return json_decode($level, true);
    }
    return ['level' => 'menu'];
}

function saveUserState($phone, $state, $mysqli) {
    $json = json_encode($state);
    $stmt = $mysqli->prepare("INSERT INTO user_states (phone, current_level) VALUES (?, ?) ON DUPLICATE KEY UPDATE current_level = VALUES(current_level)");
    $stmt->bind_param("ss", $phone, $json);
    $stmt->execute();
}

// Handle webhook verification
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['hub_mode'])) {
    $mode = $_GET['hub_mode'];
    $token = $_GET['hub_verify_token'];
    $challenge = $_GET['hub_challenge'];

    if ($mode === 'subscribe' && $token === $verify_token) {
        echo $challenge;
    } else {
        http_response_code(403);
    }
    exit;
}

// Load chatbot flow
$chatbotFlow = json_decode(file_get_contents("chatbot_flow.json"), true);

// Incoming message
$input = file_get_contents("php://input");

// $input = '{"object":"whatsapp_business_account","entry":[{"id":"511922062007891","changes":[{"value":{"messaging_product":"whatsapp","metadata":{"display_phone_number":"919220411572","phone_number_id":"543360945523071"},"statuses":[{"id":"wamid.HBgMOTE3NzQyMDMzMzY3FQIAERgSM0MwRDEyMzE0MzBBMjI0QkNDAA==","status":"read","recipient_id":"917742033367","timestamp":"1747480362"}]},"field":"messages"}]}]}';

$data = json_decode($input, true);
// Convert the data to a JSON string

// Define the file path
$filePath = 'logfile.txt'; // Replace with your desired file path
$jsonData = json_encode($data, JSON_PRETTY_PRINT);
file_put_contents($filePath, $jsonData . PHP_EOL, FILE_APPEND);


// Assuming $input contains an array of WhatsApp messages
if (!empty($data['entry'])) {
    foreach ($data['entry'] as $entry) {
        // print_r($entry);
        $messages_value =  $entry['changes'][0]['value'];

        foreach ($messages_value['messages'] as $messaging) {

            $from = $messaging['from'];
            $message = $messaging['text']['body'];

            $replyText = $chatbotFlow['menu']['text'] ?? "Welcome! Reply with a number to start.";

            if ($message !='' && $from) {

                $message = trim($message);
                $userState = getUserState($from, $mysqli);
                $currentMenu = $chatbotFlow['menu'];

                $farewellsPattern = '/\b(thank\s*you|thanks|bye|goodbye|ok|no)\b/i';
                if ($message !== "0" && preg_match($farewellsPattern, $message)) {
                    $userState['level'] = 'menu';
                    $replyText = "😊 You're welcome! If you need help again, just type any number.\n\n" . $chatbotFlow['menu']['text'];
                    saveUserState($from, $userState, $mysqli);
                    sendWhatsAppReply($from, $replyText, $mysqli);
                    exit;
                }

                // Traverse to current submenu
                foreach (explode('.', $userState['level']) as $key) {
                    if (isset($currentMenu['options'][$key]['submenu'])) {
                        $currentMenu = $currentMenu['options'][$key]['submenu'];
                    }
                }

                if ($message === "0") {

                    $parts = explode('.', $userState['level']);
                    array_pop($parts);
                    $userState['level'] = implode('.', $parts) ?: 'menu';
                    $replyText = $chatbotFlow['menu']['text'];

                } elseif (isset($currentMenu['options'][$message])) {

                    if (isset($currentMenu['options'][$message]['submenu'])) {

                        $userState['level'] .= $userState['level'] ? ".$message" : $message;
                        $replyText = $currentMenu['options'][$message]['submenu']['text'];

                    } elseif (isset($currentMenu['options'][$message]['action']['url'])) {

                        $replyText = "📄 Download here: " . $currentMenu['options'][$message]['action']['url'] ."\n\n(Reply with 0 to go back)";

                    } else {
                        $replyText = "Option selected: " . $selectedOption['label'] . "\n(Reply with 0 to go back)";
                    }

                }elseif (isset($currentMenu['input']) && $currentMenu['input'] === 'national_id') {
                    // Save National ID or process lookup
                    $nationalId = trim($message);

                    // Add validation (optional)
                    if (!preg_match('/^\d{7,10}$/', $nationalId)) {

                        $replyText = "❌ Invalid National ID. Please enter a valid one.";
                    } else {

                        // Simulate response or integrate with database/API here
                        $replyText = "✅ Loan status for ID $nationalId:\nYour loan is ACTIVE and repayment starts in July 2025.\n\n(Reply with 0 to return to main menu)";
                        
                        // Optionally reset menu
                        $_SESSION['current_menu'] = $menu['menu'];
                    }
                } else {
                    if (!is_numeric($message)) {
                        // New user or keyword
                        $userState['level'] = 'menu';
                        $replyText = $chatbotFlow['menu']['text'];
                    } else {
                        $replyText = $replyText = "⚠️ Invalid option. Please reply with a valid number or 0 to go back.";
                    }
                }               
                // for check user state 
                saveUserState($from, $userState, $mysqli);
                // for whatsapp replay 
                sendWhatsAppReply($from, $replyText,$mysqli);
                
            }            
        }
    }
}

/** Send Whatsapp replay code **/ 
function sendWhatsAppReply($from, $replyText,$mysqli){
    /* whatsapp access token and url form database */
    $sql_cdr= "SELECT * from tbl_whatsapp_connection where status=1 and debug=1 ";
    $query=mysqli_query($mysqli,$sql_cdr);
    $config = mysqli_fetch_array($query);
     // Replace with your access token
    $accessToken = $config['access_token'];

    $url = "https://api.heltar.com/v1/messages/send";
    $msg = [
        'messages' => [[
            'clientWaNumber' => $from,
            'messageType' => 'text',
            'message' => $replyText
        ]]
    ];

    $headers = [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($msg));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

http_response_code(200);
echo "EVENT_RECEIVED";
?>