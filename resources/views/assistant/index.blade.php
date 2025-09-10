<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrar Assistant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-container { min-height: 400px; max-height: 600px; overflow-y: auto; }
        .message { padding: 12px; border-radius: 10px; margin: 8px 0; position: relative; }
        .user-message { background-color: #d1e7ff; }
        .assistant-message { background-color: #e9ecef; }
        .timestamp { font-size: 0.75em; color: #6c757d; margin-top: 4px; }
        .typing-indicator { display: none; font-style: italic; color: #6c757d; padding: 10px; }
        .fade-in { animation: fadeIn 0.5s ease-in; }
        .suggestion-btn { margin: 4px; transition: background-color 0.2s; }
        .suggestion-btn:hover { background-color: #e9ecef; }
        .error-alert { display: none; }
        .spinner { display: none; width: 20px; height: 20px; border: 3px solid #fff; border-top-color: #007bff; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes spin { to { transform: rotate(360deg); } }
        @media (max-width: 576px) {
            .chat-container { max-height: 400px; }
            .suggestion-btn { font-size: 0.9em; }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Registrar of Persons Assistant</h3>
                <div>
                    <span class="me-2">Hello, {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                    </form>
                </div>
            </div>
            <div class="card-body chat-container" id="chat-container">
                <div id="chat-messages"></div>
                <div id="typing-indicator" class="typing-indicator">Assistant is typing...</div>
                <div id="error-alert" class="alert alert-danger error-alert" role="alert"></div>
            </div>
            <div class="card-footer">
                <div class="mb-2 d-flex flex-wrap">
                    <button class="btn btn-outline-secondary btn-sm suggestion-btn" onclick="setMessage('How do I apply for a national ID in Kenya?')">Apply for ID</button>
                    <button class="btn btn-outline-secondary btn-sm suggestion-btn" onclick="setMessage('What documents are needed for a birth certificate?')">Birth Certificate</button>
                    <button class="btn btn-outline-secondary btn-sm suggestion-btn" onclick="setMessage('How to replace a lost ID?')">Replace ID</button>
                </div>
                <form id="chat-form" class="input-group">
                    <input type="text" id="message" class="form-control" placeholder="Ask about ID registration, birth certificates, etc." autocomplete="off" autofocus>
                    <button type="submit" class="btn btn-primary d-flex align-items-center" id="send-btn">
                        <span id="send-text">Send</span>
                        <div id="send-spinner" class="spinner ms-2"></div>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const chatForm = document.getElementById('chat-form');
        const chatMessages = document.getElementById('chat-messages');
        const messageInput = document.getElementById('message');
        const typingIndicator = document.getElementById('typing-indicator');
        const errorAlert = document.getElementById('error-alert');
        const sendBtn = document.getElementById('send-btn');
        const sendText = document.getElementById('send-text');
        const sendSpinner = document.getElementById('send-spinner');

        // Load chat history
        fetch('/history')
            .then(response => response.json())
            .then(data => {
                data.forEach(conv => {
                    addMessage('You', conv.user_message, 'user-message', conv.created_at);
                    addMessage('Assistant', conv.assistant_response, 'assistant-message', conv.created_at);
                });
            })
            .catch(() => showError('Failed to load chat history.'));

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = messageInput.value.trim();
            if (!message) {
                showError('Please enter a message.');
                return;
            }

            addMessage('You', message, 'user-message', new Date().toLocaleString());
            messageInput.value = '';
            toggleLoading(true);

            try {
                const response = await fetch('/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ message }),
                });

                const data = await response.json();
                toggleLoading(false);

                if (data.response.includes('Unable to connect') || data.response.includes('API key is missing')) {
                    showError(data.response);
                } else {
                    addMessage('Assistant', data.response, 'assistant-message', data.timestamp);
                }
            } catch (error) {
                toggleLoading(false);
                showError('Network error. Please try again.');
            }
        });

        messageInput.addEventListener('input', () => {
            errorAlert.style.display = 'none';
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });

        function addMessage(sender, text, className, timestamp) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${className} ${sender === 'You' ? 'ms-auto' : 'me-auto'} fade-in`;
            messageDiv.style.maxWidth = '80%';
            messageDiv.innerHTML = `
                <strong>${sender}:</strong> ${text}
                <div class="timestamp">${timestamp}</div>
            `;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function setMessage(text) {
            messageInput.value = text;
            messageInput.focus();
            errorAlert.style.display = 'none';
        }

        function showError(message) {
            errorAlert.textContent = message;
            errorAlert.style.display = 'block';
            setTimeout(() => errorAlert.style.display = 'none', 5000);
        }

        function toggleLoading(isLoading) {
            sendSpinner.style.display = isLoading ? 'block' : 'none';
            sendText.style.display = isLoading ? 'none' : 'block';
            typingIndicator.style.display = isLoading ? 'block' : 'none';
            sendBtn.disabled = isLoading;
        }
    </script>
</body>
</html>
</body>
</html>