<?php

/**
 * Auth: Vastvikta Nishad
 * Date :24-12-24
 * Desc:  to handle multi chat  close insert and fetch messages 
 */
// Start the session
session_start();

include_once("../../config/web_mysqlconnect.php");

    
function get_agent_name($agent_id) {
    global $db, $link;
    
    // Prepare the SQL query
    $query = "SELECT AtxUserName FROM uniuserprofile WHERE AtxUserID = '$agent_id'";
    
    // Execute the query
    $result = mysqli_query($link, $query);
    
    // Check if the query executed successfully
    if ($result) {
        // Fetch the result
        $row = mysqli_fetch_assoc($result);
        
        // Return the agent name if found, otherwise return null
        return $row ? $row['AtxUserName'] : null;
    } else {
        // Log error in case of query failure
        error_log("Query Error: " . mysqli_error($link));
        return null;
    }
}
$userid = $_SESSION['userid'];
    
$username = get_agent_name($userid);
if (!empty($userid)) {
    //fetching  user details on the basis of the bot chat session  
    $sql = "SELECT * FROM $db.bot_chat_session WHERE chat_session != ''    ORDER BY session_start_time ,id desc";
  $result = $link->query($sql);
} else {
    $result = null;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="multi_chat/web_chat_style.css" rel="stylesheet">
</head>
<body>
    <div class="col-sm-10 mt-3" style="padding-left:0">
            <!-- Content wrapper start -->
            <div class="Reports-page#">
        
                <!-- Row start -->
                <div class="row gutters">

                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="card m-0" style="height: 600px;">

                            <div class="row no-gutters" >

                                <!-- Users list start -->
                                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-3 col-3">
                                    <div class="users-container" style="height: 600px; overflow-y: auto;">

                                        <!-- Heading for User List --> 
                                        <div class="users-header d-flex justify-content-between align-items-center" id="heading_name">
                                            <div class="d-flex align-items-center">
                                                <img src="multi_chat/user.png" alt="User" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                                                <h5 class="mb-0"><?php echo $username; ?></h5>
                                            </div>
                                            <i class="fa fa-comments" style="color:#93918f"></i>
                                        </div>

                                        <div class="chat-search-box">
                                            <div class="input-group">
                                                <input id="searchInput" class="form-control" placeholder="Search" />
                                                <div class="input-group-btn" style="color:#fefffe!important;">
                                                    <button type="button" class="btn btn-info">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <?php 
                                        $chat_user_id = isset($_GET['chat_user_id']) ? $_GET['chat_user_id'] : null;
                                        ?>
                                        <div class="user-list">
                                           
                                        </div>
                                    </div>
                                </div>
                                <!-- Users list end -->

                            <!-- Chat messages start -->
                                <div class="col-xl-8 col-lg-8 col-md-8 col-sm-9 col-9" id="message-detail" style="height: 600px;">
                                   <!-- Heading for Chat History -->
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div style="display: flex; align-items: center;">
                                            <img src="multi_chat/user2.png" alt="User" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                                            <h5 style="margin: 0;">
                                                <p id="selectedUserName" style="display: inline; margin: 0;"></p>
                                            </h5>
                                            <input type="hidden" name="chat_user_id" id="chat_user_id" value="<?php echo $chat_user_id; ?>">
                                        </div>
                                        <div class="d-flex">
                                            <button type="button" id="create_case" class="button-orange1">Create Case</button>
                                            <button type="button" id="close" class="btn btn-danger">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="chatContainer" style=" max-height:450px; padding: 10px; border: 1px solid #ddd;">
                                        <form name="agent_bot_form" id="agent_bot_form" method="post">
                                            <!-- Chat Messages -->
                                            <div id="chatMessages" style="height: 500px; overflow-y: auto;">
                                                <!-- Dynamic user details will be added here -->
                                                <div id="dynamicUserDetails" style="margin-bottom: 10px; font-weight: bold;"></div>
                                                <div id="scrollToLatest"></div>
                                            </div>
                                            <!-- Input fields -->
                                        </form>
                                    </div>
                                    <div class="form-group mt-2 mb-0 d-flex">
                                    <div id="template-options" style="position: absolute; bottom: 70px; left: 0; background: white; border: 1px solid #ccc; border-radius: 5px; display: none; z-index: 10; width: 200px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                                        <ul style="list-style: none; margin: 0; padding: 10px; position: relative;">
                                        <?php
                                            $query = "SELECT temp_name, temp_content FROM $db.webchat_template WHERE status = '1'";
                                            $result = mysqli_query($link, $query);

                                            if ($result && mysqli_num_rows($result) > 0) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    $templateName = htmlspecialchars($row['temp_name']);
                                                    $templateContent = htmlspecialchars($row['temp_content']);
                                                    echo "
                                                    <li 
                                                        onmouseover=\"showContent(`$templateContent`)\" 
                                                        onmouseout=\"hideContent()\">
                                                        <button class='dropdown-item btn btn-link' onclick='selectTemplateContent(`$templateContent`)'><b>$templateName</b></button>
                                                    </li>";
                                                }
                                            } else {
                                                echo "<li><em>No templates available</em></li>";
                                            }
                                            ?>

                                        </ul>
                                        <!-- Side content preview box -->
                                        <div id="template-preview" style="position: absolute; top: 0; left: 210px; width: 250px; padding: 10px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px; display: none; font-size: 14px; color: #333;"></div>

                                    </div>

                                        <button type="button" id="template" class="btn btn-secondary ml-1" style="background-color:#9782ff;color:white; width:50px;height:60px;margin-right:10px;">
                                            <img src="multi_chat/temp.png" alt="User" style="width: 60px; height: 30px; border-radius: 50%; ">
                                        </button>
                                        <textarea class="form-control" id="messageText" rows="2" placeholder="Type your message here..." ></textarea>
                                        <input  type="hidden" name="senderId" id="senderId" value="<?php echo $userid; ?>">
                                        <input type="hidden" name="receiverId" id="receiverId">
                                        <input type="hidden" name="sessionId" id="sessionId">
                                        <input type="hidden" name="phone" id="phone">
                                        <button type="button" id="attachFile" class="btn btn-secondary ml-1" style="background-color:#a9e326;color:white;">
                                            <i class="fas fa-paperclip" id="image_event"></i>
                                            <input type="file" id="file" name="file" style="display: none" multiple="" data-original-title="upload photos">
                                        </button>
                                        <button type="button" id="sendMessage" class="btn btn-primary ml-1">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Chat messages end -->

                            </div>
                        </div>
                    </div>

                </div>
                <!-- Row end -->

            </div>
            <!-- Content wrapper end -->

    </div>
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="multi_chat/chat_script.js"></script>
</body>

<script>
 
    function setUserName(userName, userId) {
        document.getElementById('selectedUserName').innerText = `Chat with: ${userName} (ID: ${userId})`;
        const currentUserId = '<?php echo $userid; ?>'; // Current logged-in user's ID
        fetchMessages(currentUserId, userId);
    }
    const templateBtn = document.getElementById('template');
    const optionsBox = document.getElementById('template-options');

    templateBtn.addEventListener('click', function () {
        optionsBox.style.display = optionsBox.style.display === 'none' ? 'block' : 'none';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (event) {
        if (!templateBtn.contains(event.target) && !optionsBox.contains(event.target)) {
            optionsBox.style.display = 'none';
        }
    });

    // Fill template content into the textarea
    function selectTemplateContent(content) {
        document.getElementById('messageText').value = content;
        document.getElementById('template-options').style.display = 'none';
    }

    // Show preview content on hover
    function showContent(content) {
        const previewBox = document.getElementById('template-preview');
        previewBox.textContent = content;
        previewBox.style.display = 'block';
    }

    // Hide preview on mouse out
    function hideContent() {
        document.getElementById('template-preview').style.display = 'none';
    }
</script>
