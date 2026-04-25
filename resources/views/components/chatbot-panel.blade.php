{{-- Chatbot Side Panel - Book Recommendations AI Assistant --}}
@auth
{{-- Floating Chat Toggle Button --}}
<button id="chatbot-toggle-btn"
    onclick="toggleChatbot()"
    class="fixed bottom-6 right-6 z-50 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-2xl hover:shadow-purple-500/40 hover:scale-110 transition-all duration-300 group">
    {{-- Chat icon --}}
    <svg id="chatbot-icon-open" class="w-7 h-7 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
    </svg>
    {{-- Close icon --}}
    <svg id="chatbot-icon-close" class="w-7 h-7 hidden transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
    </svg>
    {{-- Pulse ring --}}
    <span class="absolute -top-1 -right-1 w-5 h-5 bg-green-400 rounded-full border-2 border-white animate-pulse"></span>
</button>

{{-- Chat Panel --}}
<div id="chatbot-panel"
    class="fixed bottom-24 right-6 z-50 w-[400px] max-h-[600px] bg-white rounded-3xl shadow-2xl border border-gray-100 flex flex-col overflow-hidden transition-all duration-500 ease-out transform scale-0 opacity-0 origin-bottom-right"
    style="display: none;">

    {{-- Panel Header --}}
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 px-6 py-4 flex items-center gap-3 relative overflow-hidden">
        {{-- Decorative circles --}}
        <div class="absolute -top-6 -right-6 w-20 h-20 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-white/10 rounded-full"></div>

        <div class="relative">
            <div class="bg-white/20 backdrop-blur-sm rounded-2xl p-2.5">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                </svg>
            </div>
            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 rounded-full border-2 border-purple-600"></span>
        </div>
        <div class="flex-1 relative z-10">
            <h3 class="text-white font-bold text-lg leading-tight">مساعد الكتب 📚</h3>
            <p class="text-white/80 text-xs">مساعدك الذكي لاختيار الكتب</p>
        </div>
        <button onclick="toggleChatbot()" class="relative z-10 text-white/80 hover:text-white hover:bg-white/20 rounded-xl p-1.5 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Chat Messages Area --}}
    <div id="chatbot-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gradient-to-b from-gray-50 to-white" style="max-height: 380px; min-height: 380px;">
        {{-- Welcome message --}}
        <div class="flex items-start gap-2.5">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-2 flex-shrink-0 shadow-md">
                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                </svg>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl rounded-tr-md px-4 py-3 shadow-sm max-w-[85%]" dir="rtl">
                <p class="text-gray-700 text-sm leading-relaxed">
                    مرحباً {{ Auth::user()->name }}! 👋✨<br><br>
                    أنا مساعدك الذكي لتوصيات الكتب 📚<br>
                    يمكنني مساعدتك في:<br><br>
                    📖 اقتراح كتب تناسب اهتماماتك<br>
                    ⭐ تقديم ملخصات عن الكتب<br>
                    🔍 البحث عن كتب مشابهة<br><br>
                    كيف يمكنني مساعدتك اليوم؟
                </p>
            </div>
        </div>
    </div>

    {{-- Quick Suggestions --}}
    <div id="chatbot-suggestions" class="px-4 pb-2 flex flex-wrap gap-2" dir="rtl">
        <button onclick="sendQuickMessage('اقترح لي رواية عربية')" class="text-xs bg-indigo-50 text-indigo-600 px-3 py-1.5 rounded-full hover:bg-indigo-100 transition-colors border border-indigo-100">
            📖 اقترح رواية عربية
        </button>
        <button onclick="sendQuickMessage('أريد كتاب في تطوير الذات')" class="text-xs bg-purple-50 text-purple-600 px-3 py-1.5 rounded-full hover:bg-purple-100 transition-colors border border-purple-100">
            ✨ تطوير الذات
        </button>
        <button onclick="sendQuickMessage('ما هي أفضل الكتب للمبتدئين في القراءة؟')" class="text-xs bg-pink-50 text-pink-600 px-3 py-1.5 rounded-full hover:bg-pink-100 transition-colors border border-pink-100">
            🌟 كتب للمبتدئين
        </button>
    </div>

    {{-- Input Area --}}
    <div class="p-4 bg-white border-t border-gray-100">
        <form id="chatbot-form" onsubmit="handleChatSubmit(event)" class="flex items-center gap-2" dir="rtl">
            <input type="text"
                id="chatbot-input"
                placeholder="اكتب رسالتك هنا..."
                autocomplete="off"
                class="flex-1 bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all"
            >
            <button type="submit"
                id="chatbot-send-btn"
                class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl p-3 hover:shadow-lg hover:shadow-purple-500/30 transition-all transform hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                <svg class="w-5 h-5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
    // Chat state
    let chatHistory = [];
    let isChatOpen = false;
    let isLoading = false;

    function toggleChatbot() {
        const panel = document.getElementById('chatbot-panel');
        const iconOpen = document.getElementById('chatbot-icon-open');
        const iconClose = document.getElementById('chatbot-icon-close');

        if (isChatOpen) {
            // Close
            panel.classList.remove('scale-100', 'opacity-100');
            panel.classList.add('scale-0', 'opacity-0');
            setTimeout(() => { panel.style.display = 'none'; }, 300);
            iconOpen.classList.remove('hidden');
            iconClose.classList.add('hidden');
        } else {
            // Open
            panel.style.display = 'flex';
            requestAnimationFrame(() => {
                panel.classList.remove('scale-0', 'opacity-0');
                panel.classList.add('scale-100', 'opacity-100');
            });
            iconOpen.classList.add('hidden');
            iconClose.classList.remove('hidden');
            document.getElementById('chatbot-input').focus();
            scrollToBottom();
        }
        isChatOpen = !isChatOpen;
    }

    function scrollToBottom() {
        const container = document.getElementById('chatbot-messages');
        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 100);
    }

    function sendQuickMessage(msg) {
        document.getElementById('chatbot-input').value = msg;
        handleChatSubmit(new Event('submit'));
        // Hide suggestions after first use
        document.getElementById('chatbot-suggestions').style.display = 'none';
    }

    function addMessage(text, isUser) {
        const container = document.getElementById('chatbot-messages');

        if (isUser) {
            container.innerHTML += `
                <div class="flex items-start gap-2.5 justify-end">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl rounded-tl-md px-4 py-3 shadow-sm max-w-[85%]" dir="rtl">
                        <p class="text-sm leading-relaxed">${escapeHtml(text)}</p>
                    </div>
                    <div class="bg-gray-200 rounded-xl p-2 flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
            `;
        } else {
            // Format the bot message (handle markdown-like formatting)
            const formattedText = formatBotMessage(text);
            container.innerHTML += `
                <div class="flex items-start gap-2.5">
                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-2 flex-shrink-0 shadow-md">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                    </div>
                    <div class="bg-white border border-gray-100 rounded-2xl rounded-tr-md px-4 py-3 shadow-sm max-w-[85%]" dir="rtl">
                        <div class="text-gray-700 text-sm leading-relaxed chatbot-response">${formattedText}</div>
                    </div>
                </div>
            `;
        }
        scrollToBottom();
    }

    function addLoadingIndicator() {
        const container = document.getElementById('chatbot-messages');
        container.innerHTML += `
            <div id="chatbot-loading" class="flex items-start gap-2.5">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-2 flex-shrink-0 shadow-md">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl rounded-tr-md px-5 py-4 shadow-sm" dir="rtl">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></div>
                        <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 150ms;"></div>
                        <div class="w-2 h-2 bg-pink-400 rounded-full animate-bounce" style="animation-delay: 300ms;"></div>
                    </div>
                </div>
            </div>
        `;
        scrollToBottom();
    }

    function removeLoadingIndicator() {
        const el = document.getElementById('chatbot-loading');
        if (el) el.remove();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatBotMessage(text) {
        // Convert **bold** to <strong>
        let formatted = escapeHtml(text);
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        // Convert *italic* to <em>
        formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
        // Convert newlines to <br>
        formatted = formatted.replace(/\n/g, '<br>');
        return formatted;
    }

    async function handleChatSubmit(event) {
        event.preventDefault();

        const input = document.getElementById('chatbot-input');
        const sendBtn = document.getElementById('chatbot-send-btn');
        const message = input.value.trim();

        if (!message || isLoading) return;

        isLoading = true;
        sendBtn.disabled = true;
        input.value = '';

        // Add user message to UI
        addMessage(message, true);

        // Add to history
        chatHistory.push({ role: 'user', text: message });

        // Show loading
        addLoadingIndicator();

        try {
            const response = await fetch('{{ route("chatbot.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    message: message,
                    history: chatHistory.slice(-10), // Send last 10 messages for context
                }),
            });

            const data = await response.json();

            removeLoadingIndicator();

            if (data.reply) {
                addMessage(data.reply, false);
                chatHistory.push({ role: 'model', text: data.reply });
            } else if (data.error) {
                addMessage(data.error, false);
            }
        } catch (error) {
            removeLoadingIndicator();
            addMessage('عذراً، حدث خطأ في الاتصال. حاول مرة أخرى.', false);
        }

        isLoading = false;
        sendBtn.disabled = false;
        input.focus();
    }
</script>

<style>
    /* Chatbot custom scrollbar */
    #chatbot-messages::-webkit-scrollbar {
        width: 4px;
    }
    #chatbot-messages::-webkit-scrollbar-track {
        background: transparent;
    }
    #chatbot-messages::-webkit-scrollbar-thumb {
        background: #c7d2fe;
        border-radius: 20px;
    }
    #chatbot-messages::-webkit-scrollbar-thumb:hover {
        background: #818cf8;
    }

    /* Bot response styling */
    .chatbot-response strong {
        color: #4338ca;
        font-weight: 600;
    }
    .chatbot-response em {
        color: #6b7280;
    }

    /* Mobile responsiveness */
    @media (max-width: 640px) {
        #chatbot-panel {
            width: calc(100vw - 2rem) !important;
            right: 1rem !important;
            bottom: 5.5rem !important;
            max-height: 70vh !important;
        }
    }
</style>
@endauth
