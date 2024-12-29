<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Interface</title>
    <!-- Bootstrap CSS -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-container {
            max-width: 600px;
            margin: 30px auto;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .chat-message {
            margin-bottom: 15px;
        }
        .chat-message.user {
            text-align: right;
        }
        .chat-message.assistant {
            text-align: left;
        }
        .chat-message .message-bubble {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 15px;
            max-width: 75%;
        }
        .chat-message.user .message-bubble {
            background-color: #0d6efd;
            color: white;
        }
        .chat-message.assistant .message-bubble {
            background-color: #e9ecef;
            color: #000;
        }
    </style>
</head>
<body>

<div class="chat-container">
    <div id="chat-messages" style="height: 300px; overflow-y: auto;">
    </div>

    <form id="chat-form" class="mt-3">
        @csrf
        <div class="input-group">
            <input type="text" id="message-input" class="form-control" placeholder="Type a message..." required>
            <button class="btn btn-primary" type="submit">Send</button>
        </div>
    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const form = document.getElementById('chat-form');
    const messagesDiv = document.getElementById('chat-messages');

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const messageInput = document.getElementById('message-input');
        const userMessage = messageInput.value;

        appendMessage('user', userMessage);

        try {
            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ message: userMessage }),
            });

            const data = await response.json();
            appendMessage('assistant', data.reply);
        } catch (error) {
            appendMessage('assistant', 'Error: Something went wrong.');
        }
        messageInput.value = '';
    });

    function appendMessage(role, text) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('chat-message', role);

        const bubbleDiv = document.createElement('div');
        bubbleDiv.classList.add('message-bubble');
        bubbleDiv.textContent = text;

        messageDiv.appendChild(bubbleDiv);
        messagesDiv.appendChild(messageDiv);

        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
</script>

</body>
</html>
