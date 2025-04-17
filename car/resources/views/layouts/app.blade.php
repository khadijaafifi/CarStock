<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - CarStock</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Styles de base */
        body {
            padding-bottom: 80px;
        }

        /* Widget de chat */
        #chatWidget {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 400px;
            max-width: 90vw;
            height: 500px;
            max-height: 70vh;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            display: none;
            flex-direction: column;
            z-index: 1000;
            border: 1px solid #ddd;
        }

        #chatHeader {
            padding: 15px;
            background: #0d6efd;
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        #chatMessages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message {
            padding: 10px 15px;
            border-radius: 18px;
            max-width: 80%;
            word-wrap: break-word;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .user-message {
            background: #0d6efd;
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 5px;
        }

        .bot-message {
            background: #f1f1f1;
            align-self: flex-start;
            border-bottom-left-radius: 5px;
        }

        #chatForm {
            padding: 15px;
            border-top: 1px solid #ddd;
            background: #f9f9f9;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        #chatToggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #0d6efd;
            color: white;
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            z-index: 1001;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .typing-indicator {
            display: inline-block;
            padding: 10px 15px;
            background: #f1f1f1;
            border-radius: 18px;
            align-self: flex-start;
            margin-bottom: 10px;
        }

        .typing-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #666;
            margin-right: 3px;
            animation: typingAnimation 1.4s infinite both;
        }

        .typing-dot:nth-child(1) { animation-delay: 0s; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typingAnimation {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-5px); }
        }

        /* Styles pour l'historique */
        #historyToggle {
            font-size: 16px;
            background: transparent;
            color: white;
            border: none;
            cursor: pointer;
        }

        #historyMessages {
            padding: 10px;
            background: #f1f1f1;
            border-radius: 10px;
            margin-top: 15px;
            max-height: 250px;
            overflow-y: auto;
            display: none;
        }

        .history-item {
            padding: 8px 12px;
            border-radius: 18px;
            background: #f1f1f1;
            margin-bottom: 5px;
            cursor: pointer;
        }

        .history-item:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        #resetChat {
            font-size: 16px;
            background: transparent;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    @include('layouts.navigation')
    
    <div class="container py-4">
        @yield('content')
    </div>

    <!-- Bouton pour ouvrir/fermer le chat -->
    <button id="chatToggle" title="Ouvrir le chat">
        <i class="fas fa-comment-dots"></i>
    </button>

    <!-- Widget de chat -->
    <div id="chatWidget">
        <div id="chatHeader">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Assistant AI</h5>
                <div>
                    <button id="historyToggle">
                        <i class="fas fa-history"></i>
                    </button>
                    <button id="resetChat">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </div>
        </div>
        <div id="chatMessages"></div>
        <form id="chatForm">
            <div class="input-group">
                <input type="text" id="chatInput" class="form-control" placeholder="Posez votre question..." autocomplete="off">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
        <div id="historyMessages">
            <div id="historyList">
                <!-- Les conversations précédentes seront ajoutées ici -->
            </div>
        </div>
    </div>

    <script>
        let conversations = JSON.parse(localStorage.getItem('chatConversations') || '[]');
        let currentConversationId = null;

        function saveConversations() {
            localStorage.setItem('chatConversations', JSON.stringify(conversations));
        }

        function addMessage(sender, content) {
            const messagesDiv = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message');
            messageDiv.classList.add(sender === 'user' ? 'user-message' : 'bot-message');
            
            const formattedContent = content
                .replace(/\n/g, '<br>')
                .replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>');
            
            messageDiv.innerHTML = formattedContent;
            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        function startNewConversation() {
            currentConversationId = Date.now().toString();
            document.getElementById('chatMessages').innerHTML = '';
        }

        function saveCurrentConversation() {
            if (!currentConversationId) return;

            const messages = Array.from(document.getElementById('chatMessages').children)
                .filter(msg => msg.classList.contains('message'))
                .map(msg => ({
                    role: msg.classList.contains('user-message') ? 'user' : 'bot',
                    content: msg.textContent || msg.innerText
                }));

            if (messages.length === 0) return;

            const conversation = {
                id: currentConversationId,
                title: messages[0].content.substring(0, 30) + (messages[0].content.length > 30 ? '...' : ''),
                date: new Date().toLocaleString(),
                messages: messages
            };

            // Vérifie si la conversation existe déjà
            const existingIndex = conversations.findIndex(c => c.id === currentConversationId);
            if (existingIndex >= 0) {
                conversations[existingIndex] = conversation;
            } else {
                conversations.unshift(conversation);
            }

            if (conversations.length > 20) {
                conversations.pop();
            }

            saveConversations();
            renderHistoryList();
        }

        function renderHistoryList() {
            const historyList = document.getElementById('historyList');
            historyList.innerHTML = '';

            if (conversations.length === 0) {
                historyList.innerHTML = '<p class="text-muted">Aucune conversation enregistrée</p>';
                return;
            }

            conversations.forEach(conv => {
                const item = document.createElement('div');
                item.classList.add('history-item');
                item.innerHTML = `
                    <div class="fw-bold">${conv.title}</div>
                    <small class="text-muted">${conv.date}</small>
                `;
                item.addEventListener('click', () => loadConversation(conv.id));
                historyList.appendChild(item);
            });
        }

        function loadConversation(conversationId) {
            const conversation = conversations.find(c => c.id === conversationId);
            if (!conversation) return;

            currentConversationId = conversationId;
            const messagesDiv = document.getElementById('chatMessages');
            messagesDiv.innerHTML = '';

            conversation.messages.forEach(msg => {
                addMessage(msg.role, msg.content);
            });

            document.getElementById('historyMessages').style.display = 'none';
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', () => {
            // Gestion du toggle du chat
            document.getElementById('chatToggle').addEventListener('click', () => {
                const widget = document.getElementById('chatWidget');
                widget.style.display = widget.style.display === 'none' ? 'flex' : 'none';
                if (widget.style.display === 'flex') {
                    startNewConversation();
                }
            });

            // Gestion de l'historique
            document.getElementById('historyToggle').addEventListener('click', () => {
                const historyMessages = document.getElementById('historyMessages');
                historyMessages.style.display = historyMessages.style.display === 'none' ? 'block' : 'none';
            });

            // Réinitialisation du chat
            document.getElementById('resetChat').addEventListener('click', () => {
                if (confirm('Voulez-vous réinitialiser la conversation et l\'historique ?')) {
                    localStorage.removeItem('chatConversations');
                    conversations = [];
                    renderHistoryList();
                    startNewConversation();
                }
            });

            // Gestion de l'envoi du message
            document.getElementById('chatForm').addEventListener('submit', async (e) => {
                e.preventDefault();

                const userInput = document.getElementById('chatInput').value.trim();
                if (!userInput) return;

                addMessage('user', userInput);
                document.getElementById('chatInput').value = '';

                // Afficher l'indicateur de frappe
                const messagesDiv = document.getElementById('chatMessages');
                const typingIndicator = document.createElement('div');
                typingIndicator.classList.add('typing-indicator');
                typingIndicator.innerHTML = `
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                `;
                messagesDiv.appendChild(typingIndicator);
                messagesDiv.scrollTop = messagesDiv.scrollHeight;

                try {
                    const response = await fetch('/get-ai-response', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ message: userInput })
                    });
                    const data = await response.json();

if (data.status === 'success') {
    addMessage('bot', data.response);
} else {
    addMessage('bot', 'Désolé, une erreur est survenue.');
}
                    // Supprimer l'indicateur de frappe
                    messagesDiv.removeChild(typingIndicator);
                    
                    // if (data.status === 'success') {
                    //     addMessage('bot', data.response);
                    //     console.log(data.response);
                    // } else {
                    //     addMessage('bot', 'Désolé, une erreur est survenue.');
                    // }
                } catch (error) {
                    messagesDiv.removeChild(typingIndicator);
                    addMessage('bot', 'Désolé, une erreur de connexion est survenue.');
                    // log the error to laravel logs
                    console.log('error .',error);
                }

                saveCurrentConversation();
            });

            // Initialiser une nouvelle conversation au chargement
            startNewConversation();
            renderHistoryList();
        });
    </script>
</body>
</html>