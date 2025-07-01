const themeToggle = document.querySelector('.theme-toggle');
const body = document.body;
const chatContainer = document.getElementById('chatContainer');
const messageInput = document.querySelector('.message-input');
const sendButton = document.querySelector('.send-button');
const typingIndicator = document.querySelector('.typing-indicator');

// Theme toggling
let isDarkTheme = false;
themeToggle.addEventListener('click', () => {
    isDarkTheme = !isDarkTheme;
    body.setAttribute('data-theme', isDarkTheme ? 'dark' : 'light');
    themeToggle.innerHTML = isDarkTheme ? 
        '<i class="fas fa-sun"></i>' : 
        '<i class="fas fa-moon"></i>';
});

// Chat functionality
function createMessageElement(content, isUser = false) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${isUser ? 'user-message' : 'bot-message'}`;
    
    messageDiv.innerHTML = `
        <div class="avatar">${isUser ? 'U' : 'AI'}</div>
        <div class="message-bubble">${content}</div>
    `;
    
    return messageDiv;
}

function addMessage(content, isUser = false) {
    const messageElement = createMessageElement(content, isUser);
    chatContainer.appendChild(messageElement);
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

function showTypingIndicator() {
    typingIndicator.style.display = 'block';
    chatContainer.scrollTop = chatContainer.scrollHeight;
}

function hideTypingIndicator() {
    typingIndicator.style.display = 'none';
}

// Call Knowledge Base API
async function callAskAPI(question) {
    try {
        // Construct the GET request URL
        const url = `helpdesk/web_ticket_function.php?action=Ask_KnowledgeBase&question=${encodeURIComponent(question)}`;

        // console.log("Request URL:", url);

        const response = await fetch(url, {
            method: 'GET',
        });

        // console.log("Response status:", response.status);

        const text = await response.text();
        // console.log("Raw response:", text);

        if (!text) {
            throw new Error("Empty response from server.");
        }

        const data = JSON.parse(text);
        // console.log("Parsed response:", data);

        // Replace new lines with <br>
        const formattedAnswer = data.answer ? data.answer.replace(/\n/g, "<br>") : "No answer available.";

        return formattedAnswer;
    } catch (error) {
        console.error("Error:", error);
        return "Sorry, I couldn't process your request. Please try again.";
    }
}

// Simulate bot response using the Knowledge Base API
async function simulateBotResponse(userMessage) {
    showTypingIndicator();

    // Call the PHP API and get the response
    const botResponse = await callAskAPI(userMessage);

    hideTypingIndicator();
    addMessage(botResponse); // Display the bot's response in the chat
}

function handleSendMessage() {
    const message = messageInput.value.trim();
    if (message) {
        addMessage(message, true); // Add user's message to the chat
        messageInput.value = ''; // Clear the input field
        simulateBotResponse(message); // Simulate bot response
    }
}

sendButton.addEventListener('click', handleSendMessage);
messageInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        handleSendMessage();
    }
});

// Initial bot message
setTimeout(() => {
    addMessage("Hello! I'm your AI assistant. How can I help you today?");
}, 500);

