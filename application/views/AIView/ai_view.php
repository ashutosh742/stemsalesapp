<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AI Chat (OpenAI ChatGPT)</title>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #212121;
        color: #eee;
        display: flex;
        height: 100vh;
        overflow: hidden;
    }
    .sidebar {
        width: 270px;
        background-color: #111;
        border-right: 1px solid #333;
        padding: 10px;
        display: flex;
        flex-direction: column;
    }
    .sidebar h2 {
        color: #10a37f;
        text-align: center;
        margin-bottom: 10px;
    }
    .search-box {
        margin-bottom: 10px;
    }
    .search-box input {
        width: 100%;
        padding: 8px;
        border-radius: 6px;
        border: none;
        outline: none;
        background-color: #222;
        color: #eee;
        font-size: 14px;
    }
    .chat-list {
        flex: 1;
        overflow-y: auto;
    }
    .chat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px;
        margin-bottom: 6px;
        background: #1e1e1e;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.2s;
    }
    .chat-item:hover {
        background: #2a2a2a;
    }
    .chat-item.active {
        background: #333;
        border: 1px solid #10a37f;
    }
    .chat-name {
        flex: 1;
        outline: none;
        border: none;
        background: transparent;
        color: #eee;
        font-size: 14px;
        cursor: pointer;
        margin-right: 5px;
    }
    .chat-name.editing {
        background: #222;
        cursor: text;
        border-radius: 4px;
        padding: 3px 5px;
    }
    .chat-actions {
        display: flex;
        gap: 6px;
    }
    .edit-btn, .delete-btn {
        background: none;
        border: none;
        color: #ccc;
        cursor: pointer;
        font-size: 14px;
        transition: 0.2s;
    }
    .edit-btn:hover { color: #10a37f; }
    .delete-btn:hover { color: #ff5252; }
    .new-chat-btn {
        background: #10a37f;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.2s;
        margin-bottom: 10px;
    }
    .new-chat-btn:hover {
        background: #0d8c6d;
    }
    .api-key-section {
        margin-top: auto;
        padding-top: 10px;
        border-top: 1px solid #333;
    }
    .api-key-section input {
        width: 100%;
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #444;
        outline: none;
        background-color: #222;
        color: #eee;
        font-size: 14px;
        margin-bottom: 10px;
    }
    .api-key-section input:focus {
        border-color: #10a37f;
    }
    .save-key-btn {
        width: 100%;
        background: #444;
        color: white;
        border: none;
        padding: 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.2s;
    }
    .save-key-btn:hover {
        background: #555;
    }
    .chat-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background-color: #2b2b2b;
        position: relative;
    }
    .chat-area {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
    }
    .message {
        margin: 10px 0;
        padding: 10px 15px;
        border-radius: 8px;
        max-width: 80%;
        line-height: 1.5;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .user {
        background: #10a37f;
        color: white;
        align-self: flex-end;
        margin-left: auto;
    }
    .ai {
        background: #333;
        border: 1px solid #444;
    }
    pre, code {
        background: #111;
        color: #10a37f;
        padding: 8px;
        border-radius: 5px;
        overflow-x: auto;
    }
    table {
        border-collapse: collapse;
        margin-top: 10px;
    }
    table, th, td {
        border: 1px solid #555;
        padding: 5px 10px;
    }
    th {
        background-color: #444;
    }
    .input-area {
        display: flex;
        padding: 15px;
        border-top: 1px solid #333;
        background: #1e1e1e;
    }
    textarea {
        flex: 1;
        padding: 10px;
        border: none;
        border-radius: 8px;
        resize: none;
        background: #333;
        color: #eee;
        font-size: 14px;
    }
    button#askBtn {
        background: #10a37f;
        color: white;
        border: none;
        margin-left: 10px;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        transition: 0.2s;
    }
    button#askBtn:hover {
        background: #0d8c6d;
    }
    .model-selector {
        margin: 10px 0;
    }
    .model-selector select {
        width: 100%;
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #444;
        outline: none;
        background-color: #222;
        color: #eee;
        font-size: 14px;
    }
    .model-selector select:focus {
        border-color: #10a37f;
    }
    /* Loader CSS */
    .loader-container {
        display: none;
        justify-content: center;
        align-items: center;
        height: 100%;
        width: 100%;
        position: absolute;
        top: 0;
        left: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 10;
    }
    .loader {
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        border-top: 4px solid #10a37f;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .please-wait {
        margin-top: 10px;
        color: #eee;
        font-size: 16px;
    }
    .error-message {
        color: #ff5252;
        background: rgba(255, 82, 82, 0.1);
        padding: 10px;
        border-radius: 6px;
        margin: 10px 0;
        border: 1px solid #ff5252;
    }
</style>
</head>
<body>
<div class="sidebar">
    <h2>🤖 ChatGPT</h2>
    <div class="search-box">
        <input type="text" id="searchChat" placeholder="Search chat...">
    </div>
    <button class="new-chat-btn" id="newChat">+ New Chat</button>
    <div class="chat-list" id="chatList"></div>
    
    <div class="api-key-section">
        <div class="model-selector">
            <select id="modelSelect">
                <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                <option value="gpt-4">GPT-4</option>
                <option value="gpt-4-turbo-preview">GPT-4 Turbo</option>
            </select>
        </div>
        <input type="password" id="apiKey" placeholder="Enter your OpenAI API key">
        <button class="save-key-btn" id="saveKey">Save API Key</button>
    </div>
</div>
<div class="chat-container">
    <div class="chat-area" id="chatArea"></div>
    <div class="input-area">
        <textarea id="message" rows="2" placeholder="Type your question... (Press Shift+Enter for new line)"></textarea>
        <button id="askBtn">Ask</button>
    </div>
    <!-- Loader -->
    <div id="loader" class="loader-container">
        <div class="loader"></div>
        <div class="please-wait">Thinking...</div>
    </div>
</div>
<script>
let chats = JSON.parse(localStorage.getItem("chats") || "{}");
let currentChatId = null;
let apiKey = localStorage.getItem("openai_api_key") || "";
let selectedModel = localStorage.getItem("openai_model") || "gpt-3.5-turbo";

// Show loader
function showLoader() {
    $("#loader").show();
}

// Hide loader
function hideLoader() {
    $("#loader").hide();
}

// Render chat list
function renderChats(filter = "") {
    $("#chatList").empty();
    const filtered = Object.entries(chats).filter(([id, chat]) =>
        chat.name.toLowerCase().includes(filter.toLowerCase())
    );
    if (filtered.length === 0) {
        $("#chatList").append("<p style='text-align:center;color:#777;'>No chats found</p>");
        return;
    }
    for (let [id, chat] of filtered) {
        const active = id === currentChatId ? "active" : "";
        $("#chatList").append(`
            <div class="chat-item ${active}" data-id="${id}">
                <input class="chat-name" value="${chat.name}" readonly />
                <div class="chat-actions">
                    <button class="edit-btn" title="Edit Name">✏️</button>
                    <button class="delete-btn" title="Delete Chat">❌</button>
                </div>
            </div>
        `);
    }
}

// Render messages
function renderMessages() {
    showLoader();
    const chat = chats[currentChatId];
    $("#chatArea").empty();
    if (!chat) {
        hideLoader();
        return;
    }
    chat.messages.forEach(m => {
        const div = $("<div>").addClass("message " + m.sender).html(m.text);
        $("#chatArea").append(div);
    });
    $("#chatArea").scrollTop($("#chatArea")[0].scrollHeight);
    hideLoader();
}

// Save chats to localStorage
function saveChats() {
    localStorage.setItem("chats", JSON.stringify(chats));
}

// Create new chat
function newChat() {
    const id = Date.now().toString();
    chats[id] = { name: "New Chat", messages: [] };
    currentChatId = id;
    saveChats();
    renderChats($("#searchChat").val());
    renderMessages();
}

// Initialize
if (Object.keys(chats).length === 0) {
    newChat();
} else {
    currentChatId = Object.keys(chats)[0];
    renderChats();
    renderMessages();
}

// Initialize API key and model from localStorage
$("#apiKey").val(apiKey);
$("#modelSelect").val(selectedModel);

// New chat button
$("#newChat").click(newChat);

// Search
$("#searchChat").on("input", function() {
    renderChats($(this).val());
});

// Switch chat
$(document).on("click", ".chat-item", function(e) {
    if ($(e.target).is(".edit-btn, .delete-btn, .chat-name")) return;
    showLoader();
    const id = $(this).data("id");
    if (currentChatId !== id) {
        currentChatId = id;
        $(".chat-item").removeClass("active");
        $(this).addClass("active");
        setTimeout(renderMessages, 300);
    }
});

// Edit chat name
$(document).on("click", ".edit-btn", function(e) {
    e.stopPropagation();
    const input = $(this).closest(".chat-item").find(".chat-name");
    input.prop("readonly", false).addClass("editing").focus().select();
});

// Save chat name
$(document).on("blur", ".chat-name", function() {
    const id = $(this).closest(".chat-item").data("id");
    chats[id].name = $(this).val().trim() || "Untitled Chat";
    $(this).prop("readonly", true).removeClass("editing");
    saveChats();
    renderChats($("#searchChat").val());
    $(`.chat-item[data-id='${currentChatId}']`).addClass("active");
});

// Delete chat
$(document).on("click", ".delete-btn", function(e) {
    e.stopPropagation();
    showLoader();
    const id = $(this).closest(".chat-item").data("id");
    delete chats[id];
    if (currentChatId === id) {
        currentChatId = Object.keys(chats)[0] || null;
    }
    saveChats();
    renderChats($("#searchChat").val());
    renderMessages();
});

// Save API key
$("#saveKey").click(function() {
    apiKey = $("#apiKey").val().trim();
    if (!apiKey) {
        alert("Please enter your OpenAI API key");
        return;
    }
    localStorage.setItem("openai_api_key", apiKey);
    localStorage.setItem("openai_model", selectedModel);
    alert("API key saved!");
});

// Model selection
$("#modelSelect").change(function() {
    selectedModel = $(this).val();
    localStorage.setItem("openai_model", selectedModel);
});

// Send message to OpenAI API
async function sendToOpenAI(messages) {
    if (!apiKey) {
        throw new Error("Please enter your OpenAI API key in the sidebar");
    }

    const response = await fetch("https://api.openai.com/v1/chat/completions", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${apiKey}`
        },
        body: JSON.stringify({
            model: selectedModel,
            messages: messages,
            temperature: 0.7,
            max_tokens: 2000
        })
    });

    if (!response.ok) {
        const error = await response.json();
        throw new Error(error.error?.message || "Failed to get response from OpenAI");
    }

    const data = await response.json();
    return data.choices[0].message.content;
}

// Format messages for OpenAI API
function formatMessagesForAPI(chatMessages) {
    return chatMessages.map(msg => ({
        role: msg.sender === "user" ? "user" : "assistant",
        content: msg.text
    }));
}

// Ask AI
$("#askBtn").click(async function() {
    const msg = $("#message").val().trim();
    if (!msg || !currentChatId) return;
    if (!apiKey) {
        alert("Please enter your OpenAI API key in the sidebar first!");
        return;
    }

    $("#message").val("");
    const chat = chats[currentChatId];
    
    // Add user message
    chat.messages.push({ sender: "user", text: msg });
    renderMessages();
    saveChats();
    
    // Show thinking message
    const thinkingMsg = $("<div class='message ai'>Thinking...</div>");
    $("#chatArea").append(thinkingMsg);
    $("#chatArea").scrollTop($("#chatArea")[0].scrollHeight);
    
    try {
        showLoader();
        
        // Format messages for OpenAI (include only last 20 messages to avoid token limit)
        const recentMessages = chat.messages.slice(-20);
        const formattedMessages = formatMessagesForAPI(recentMessages);
        
        // Get response from OpenAI
        const response = await sendToOpenAI(formattedMessages);
        
        // Update thinking message with actual response
        thinkingMsg.html(response);
        
        // Add AI response to chat history
        chat.messages.push({ sender: "ai", text: response });
        saveChats();
        
    } catch (error) {
        thinkingMsg.html("");
        const errorDiv = $("<div class='error-message'></div>").text("Error: " + error.message);
        $("#chatArea").append(errorDiv);
        console.error("OpenAI API error:", error);
    } finally {
        hideLoader();
        $("#chatArea").scrollTop($("#chatArea")[0].scrollHeight);
    }
});

// Allow Shift+Enter for new line, Enter to send
$("#message").on("keydown", function(e) {
    if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();
        $("#askBtn").click();
    }
});

// Auto-resize textarea
$("#message").on("input", function() {
    this.style.height = "auto";
    this.style.height = (this.scrollHeight) + "px";
});
</script>
</body>
</html>