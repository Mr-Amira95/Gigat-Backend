import axios from 'axios';

document.addEventListener('DOMContentLoaded', function () {
    const chatId = document.getElementById('chatBody').dataset.chatId;
    const currentUserId = document.getElementById('chatBody').dataset.currentUser;

    // Listen for events on the chat channel
    window.Echo.channel(`chat.${chatId}`)
        .listen('.message.sent', (e) => {
            console.log('New message received:', e);
            appendMessage(e.message, e.message.sender_id == currentUserId);
        });

    // Message send form handler
    const sendMessageForm = document.getElementById('sendMessageForm');
    if (sendMessageForm) {
        sendMessageForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            if (message === '') return;

            axios.post(sendMessageForm.action, { message })
                .then(response => {
                    messageInput.value = '';
                    appendMessage(response.data.message, true);
                })
                .catch(error => {
                    console.error('Send message error:', error);
                    alert('Failed to send message.');
                });
        });
    }

    function appendMessage(message, isSender) {
        const chatBody = document.getElementById('chatBody');
        if (!chatBody) return;

        const bubble = document.createElement('div');
        bubble.classList.add('flex', isSender ? 'justify-end' : 'justify-start');

        const content = `
            <div class="max-w-xs p-3 rounded-lg ${isSender ? 'bg-primary text-white' : 'bg-white border'}">
                <p class="text-sm">${message.message}</p>
                <span class="block text-xs text-gray-400 mt-1">
                    ${new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                </span>
            </div>
        `;

        bubble.innerHTML = content;
        chatBody.appendChild(bubble);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    // Scroll to bottom on page load
    window.addEventListener('load', () => {
        const chatBody = document.getElementById('chatBody');
        if (chatBody) chatBody.scrollTop = chatBody.scrollHeight;
    });
});
