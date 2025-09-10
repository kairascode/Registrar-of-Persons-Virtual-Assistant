<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrar Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4 max-w-2xl">
        <h1 class="text-2xl font-bold mb-4 text-center">Registrar of Persons Virtual Assistant</h1>
        <div id="chat-container" class="bg-white p-4 rounded shadow h-96 overflow-y-auto mb-4">
            <div id="chat-messages"></div>
        </div>
        <form id="chat-form" class="flex">
            <input type="text" id="message" class="flex-1 p-2 border rounded-l focus:outline-none" placeholder="Ask about ID registration, birth certificates, etc.">
            <button type="submit" class="bg-blue-500 text-white p-2 rounded-r hover:bg-blue-600">Send</button>
        </form>
    </div>

    <script>
        const chatForm = document.getElementById('chat-form');
        const chatMessages = document.getElementById('chat-messages');
        const messageInput = document.getElementById('message');

        // Load chat history
        fetch('/history')
            .then(response => response.json())
            .then(data => {
                data.forEach(conv => {
                    addMessage('You', conv.user_message, 'text-blue-600');
                    addMessage('Assistant', conv.assistant_response, 'text-gray-800');
                });
            });

        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const message = messageInput.value.trim();
            if (!message) return;

            addMessage('You', message, 'text-blue-600');
            messageInput.value = '';

            const response = await fetch('/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ message }),
            });

            const data = await response.json();
            addMessage('Assistant', data.response, 'text-gray-800');
        });

        function addMessage(sender, text, color) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `mb-2 ${sender === 'You' ? 'text-right' : 'text-left'} ${color}`;
            messageDiv.innerHTML = `<strong>${sender}:</strong> ${text}`;
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    </script>
</body>
</html>