<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Andriansyah Chatbot') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome & Marked & Highlight.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(100, 116, 139, 0.4);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.6);
        }

        /* Markdown rendered styling */
        .markdown-body {
            color: #e2e8f0;
            line-height: 1.65;
            font-size: 0.95rem;
        }
        .markdown-body p {
            margin-bottom: 0.75rem;
        }
        .markdown-body p:last-child {
            margin-bottom: 0;
        }
        .markdown-body h1, .markdown-body h2, .markdown-body h3, .markdown-body h4 {
            color: #f8fafc;
            font-weight: 700;
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
        }
        .markdown-body h1 { font-size: 1.35rem; }
        .markdown-body h2 { font-size: 1.2rem; }
        .markdown-body h3 { font-size: 1.05rem; }
        .markdown-body ul, .markdown-body ol {
            margin-left: 1.25rem;
            margin-bottom: 0.75rem;
        }
        .markdown-body ul { list-style-type: disc; }
        .markdown-body ol { list-style-type: decimal; }
        .markdown-body li {
            margin-bottom: 0.25rem;
        }
        .markdown-body code:not(pre code) {
            background-color: rgba(30, 41, 59, 0.8);
            color: #93c5fd;
            padding: 0.15rem 0.35rem;
            border-radius: 0.25rem;
            font-size: 0.875em;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        .markdown-body pre {
            background-color: #0f172a;
            border: 1px solid rgba(51, 65, 85, 0.5);
            border-radius: 0.5rem;
            padding: 0.85rem;
            margin: 0.75rem 0;
            overflow-x: auto;
            position: relative;
        }
        .markdown-body pre code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.875rem;
        }
        .markdown-body blockquote {
            border-left: 3px solid #3b82f6;
            padding-left: 0.75rem;
            color: #94a3b8;
            font-style: italic;
            margin: 0.75rem 0;
        }
        .markdown-body table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.75rem 0;
        }
        .markdown-body th, .markdown-body td {
            border: 1px solid #334155;
            padding: 0.5rem 0.75rem;
            text-align: left;
        }
        .markdown-body th {
            background-color: #1e293b;
            font-weight: 600;
        }

        /* Pulsing dot animation */
        @keyframes pulseDot {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
            40% { transform: scale(1); opacity: 1; }
        }
        .dot-1 { animation: pulseDot 1.4s infinite ease-in-out; }
        .dot-2 { animation: pulseDot 1.4s infinite ease-in-out 0.2s; }
        .dot-3 { animation: pulseDot 1.4s infinite ease-in-out 0.4s; }
    </style>
</head>
<body class="h-full text-slate-100 flex flex-col antialiased select-auto">

    <div class="flex h-screen overflow-hidden bg-slate-950">
        
        <!-- Mobile Sidebar Backdrop -->
        <div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-950/70 z-40 hidden md:hidden backdrop-blur-sm transition-opacity"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed md:static inset-y-0 left-0 z-50 w-72 bg-slate-900 border-r border-slate-800 flex flex-col transition-transform duration-300 transform -translate-x-full md:translate-x-0">
            <!-- App Header -->
            <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-sky-400 flex items-center justify-center shadow-lg shadow-blue-500/20 text-white font-bold">
                        <i class="fa-solid fa-sparkles text-sm"></i>
                    </div>
                    <div>
                        <h1 class="text-base font-bold text-white tracking-tight flex items-center gap-1.5">
                            Andriansyah Chatbot <span class="text-[10px] uppercase font-semibold tracking-wider px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-400 border border-blue-500/30">L12</span>
                        </h1>
                        <p class="text-xs text-slate-400">Laravel 12 & MySQL</p>
                    </div>
                </div>
                <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- New Chat Button -->
            <div class="p-3">
                <button onclick="createNewConversation()" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-medium text-sm transition-all shadow-md shadow-blue-600/25 hover:shadow-blue-500/35">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Percakapan Baru</span>
                </button>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto px-3 py-2 space-y-1" id="conversationList">
                @forelse($conversations as $conv)
                    <div id="conv-item-{{ $conv->id }}" 
                         onclick="selectConversation({{ $conv->id }})" 
                         class="group relative flex items-center justify-between px-3 py-2.5 rounded-lg text-sm cursor-pointer transition-colors {{ $loop->first ? 'bg-slate-800 text-white font-medium' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <div class="flex items-center gap-2.5 overflow-hidden flex-1 mr-2">
                            <i class="fa-regular fa-message text-xs opacity-70"></i>
                            <span class="truncate conv-title">{{ $conv->title }}</span>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="deleteConversation(event, {{ $conv->id }})" title="Hapus chat" class="p-1 text-slate-500 hover:text-rose-400 transition-colors">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div id="emptyConvNotice" class="text-center py-8 text-xs text-slate-500">
                        <i class="fa-regular fa-comments text-2xl mb-2 opacity-50 block"></i>
                        Belum ada riwayat percakapan.
                    </div>
                @endforelse
            </div>

            <!-- Footer / System Info -->
            <div class="p-3 border-t border-slate-800 text-xs text-slate-400 flex items-center justify-between bg-slate-900/50">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block animate-pulse"></span>
                    <span class="truncate">MySQL Terhubung</span>
                </div>
                <span class="text-[11px] text-slate-500">{{ config('gemini.model', 'gemini-flash') }}</span>
            </div>
        </aside>

        <!-- Main Chat Area -->
        <main class="flex-1 flex flex-col h-full bg-slate-950 relative overflow-hidden">
            
            <!-- Top Navbar -->
            <header class="h-14 border-b border-slate-800/80 bg-slate-900/40 backdrop-blur px-4 flex items-center justify-between z-10">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="md:hidden text-slate-400 hover:text-white p-1 rounded-lg">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <div>
                        <h2 id="activeChatTitle" class="text-sm font-semibold text-slate-200 truncate max-w-xs md:max-w-md">
                            {{ $conversations->first()?->title ?? 'Percakapan Baru' }}
                        </h2>
                        <span class="text-[11px] text-slate-500 flex items-center gap-1">
                            <i class="fa-solid fa-bolt text-amber-400 text-[10px]"></i> Model: <span id="modelIndicator">{{ config('gemini.model', 'gemini-flash-latest') }}</span>
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button onclick="clearCurrentChat()" title="Bersihkan riwayat pesan ini" class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-slate-400 hover:text-rose-400 hover:bg-slate-800 border border-slate-800 hover:border-slate-700 transition">
                        <i class="fa-solid fa-broom mr-1"></i> Bersihkan
                    </button>
                    <a href="https://ai.google.dev/" target="_blank" class="px-2.5 py-1.5 rounded-lg text-xs font-medium text-slate-400 hover:text-blue-400 hover:bg-slate-800 border border-slate-800 transition hidden sm:inline-flex items-center gap-1">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Gemini Docs
                    </a>
                </div>
            </header>

            <!-- Chat Scrollable Body -->
            <div id="chatMessages" class="flex-1 overflow-y-auto px-4 md:px-8 py-6 space-y-6">
                <!-- Welcome / Empty State Placeholder -->
                <div id="welcomeScreen" class="{{ $conversations->isNotEmpty() && $conversations->first()->messages()->count() > 0 ? 'hidden' : '' }} max-w-2xl mx-auto my-auto text-center py-10">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-sky-400 flex items-center justify-center shadow-xl shadow-blue-500/20 text-white text-2xl mb-4">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">Halo! Selamat datang di Andriansyah Chatbot</h3>
                    <p class="text-sm text-slate-400 max-w-md mx-auto mb-8">
                        Saya adalah asisten AI yang terhubung dengan model Google Gemini dan database MySQL via Laravel 12. Ada yang bisa saya bantu hari ini?
                    </p>

                    <!-- Suggestion Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-left">
                        <button onclick="sendSuggestion('Jelaskan apa saja fitur unggulan di Laravel 12!')" class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-blue-500/50 hover:bg-slate-800/60 transition group">
                            <div class="flex items-center gap-2 text-xs font-semibold text-blue-400 mb-1">
                                <i class="fa-brands fa-laravel"></i> Laravel 12
                            </div>
                            <p class="text-xs text-slate-300">Jelaskan apa saja fitur unggulan di Laravel 12!</p>
                        </button>

                        <button onclick="sendSuggestion('Buatkan rancangan skema database MySQL untuk aplikasi toko online')" class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-blue-500/50 hover:bg-slate-800/60 transition group">
                            <div class="flex items-center gap-2 text-xs font-semibold text-emerald-400 mb-1">
                                <i class="fa-solid fa-database"></i> Skema MySQL
                            </div>
                            <p class="text-xs text-slate-300">Buatkan rancangan skema database MySQL toko online</p>
                        </button>

                        <button onclick="sendSuggestion('Bagaimana cara membuat REST API authentication di Laravel 12?')" class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-blue-500/50 hover:bg-slate-800/60 transition group">
                            <div class="flex items-center gap-2 text-xs font-semibold text-indigo-400 mb-1">
                                <i class="fa-solid fa-code"></i> REST API
                            </div>
                            <p class="text-xs text-slate-300">Bagaimana membuat REST API auth di Laravel 12?</p>
                        </button>

                        <button onclick="sendSuggestion('Tuliskan puisi pendek tentang teknologi kecerdasan buatan')" class="p-3.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-blue-500/50 hover:bg-slate-800/60 transition group">
                            <div class="flex items-center gap-2 text-xs font-semibold text-amber-400 mb-1">
                                <i class="fa-solid fa-pen-nib"></i> Kreatif
                            </div>
                            <p class="text-xs text-slate-300">Tuliskan puisi pendek tentang AI dan masa depan</p>
                        </button>
                    </div>
                </div>

                <!-- Messages Container -->
                <div id="messagesList" class="max-w-3xl mx-auto space-y-6"></div>

                <!-- Loading Bubble -->
                <div id="typingIndicator" class="hidden max-w-3xl mx-auto">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white text-xs shrink-0 shadow-md">
                            <i class="fa-solid fa-sparkles"></i>
                        </div>
                        <div class="p-3.5 rounded-2xl rounded-tl-sm bg-slate-900 border border-slate-800 flex items-center gap-1.5 text-slate-400">
                            <span class="text-xs font-medium mr-1 text-slate-400">Gemini sedang berpikir</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 dot-1 inline-block"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 dot-2 inline-block"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 dot-3 inline-block"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input Bar -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-900/60 backdrop-blur">
                <div class="max-w-3xl mx-auto">
                    <form id="chatForm" onsubmit="handleChatSubmit(event)" class="relative flex items-end rounded-2xl bg-slate-900 border border-slate-700/80 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition shadow-lg">
                        <textarea 
                            id="messageInput" 
                            rows="1" 
                            placeholder="Ketik pertanyaan atau perintah untuk Gemini... (Shift + Enter untuk baris baru)" 
                            class="w-full bg-transparent px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none resize-none max-h-36 overflow-y-auto leading-relaxed"
                            onkeydown="handleKeydown(event)"></textarea>
                        
                        <div class="p-2 flex items-center gap-1.5">
                            <button 
                                type="submit" 
                                id="sendBtn" 
                                class="w-9 h-9 rounded-xl bg-blue-600 hover:bg-blue-500 text-white flex items-center justify-center transition-all disabled:opacity-40 disabled:cursor-not-allowed shadow-md shadow-blue-600/30">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                            </button>
                        </div>
                    </form>
                    
                    <p class="text-[11px] text-center text-slate-500 mt-2">
                        Andriansyah Chatbot ditenagai oleh Google Gemini & Laravel 12. Harap periksa kembali informasi penting.
                    </p>
                </div>
            </div>
        </main>
    </div>

    <!-- Application Script -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let currentConversationId = {{ $conversations->first()?->id ?? 'null' }};
        let isGenerating = false;

        // Auto resize textarea
        const textarea = document.getElementById('messageInput');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 150) + 'px';
        });

        function handleKeydown(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('chatForm').dispatchEvent(new Event('submit', { cancelable: true }));
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const isHidden = sidebar.classList.contains('-translate-x-full');
            
            if (isHidden) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        // Configure marked.js options
        marked.setOptions({
            breaks: true,
            gfm: true,
            highlight: function(code, lang) {
                if (lang && hljs.getLanguage(lang)) {
                    return hljs.highlight(code, { language: lang }).value;
                }
                return hljs.highlightAuto(code).value;
            }
        });

        // Initialize: Load active conversation if exists
        document.addEventListener('DOMContentLoaded', () => {
            if (currentConversationId) {
                loadConversationMessages(currentConversationId);
            }
        });

        function selectConversation(id) {
            if (isGenerating) return;
            currentConversationId = id;
            updateConversationActiveUI(id);
            loadConversationMessages(id);

            // On mobile close sidebar
            if (window.innerWidth < 768) {
                toggleSidebar();
            }
        }

        function updateConversationActiveUI(id) {
            document.querySelectorAll('#conversationList > div').forEach(el => {
                if (el.id === `conv-item-${id}`) {
                    el.className = 'group relative flex items-center justify-between px-3 py-2.5 rounded-lg text-sm cursor-pointer transition-colors bg-slate-800 text-white font-medium';
                } else {
                    el.className = 'group relative flex items-center justify-between px-3 py-2.5 rounded-lg text-sm cursor-pointer transition-colors text-slate-400 hover:bg-slate-800/60 hover:text-slate-200';
                }
            });
        }

        async function loadConversationMessages(id) {
            try {
                const res = await fetch(`/api/conversations/${id}`);
                const data = await res.json();
                
                if (data.conversation) {
                    document.getElementById('activeChatTitle').innerText = data.conversation.title;
                    const messagesContainer = document.getElementById('messagesList');
                    messagesContainer.innerHTML = '';

                    const welcomeScreen = document.getElementById('welcomeScreen');
                    if (data.messages && data.messages.length > 0) {
                        welcomeScreen.classList.add('hidden');
                        data.messages.forEach(msg => appendMessageUI(msg.role, msg.content));
                        scrollToBottom();
                    } else {
                        welcomeScreen.classList.remove('hidden');
                    }
                }
            } catch (err) {
                console.error('Gagal memuat pesan percakapan:', err);
            }
        }

        async function createNewConversation() {
            if (isGenerating) return;
            try {
                const res = await fetch('/api/conversations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ title: 'Percakapan Baru' }),
                });
                const data = await res.json();
                if (data.success && data.conversation) {
                    currentConversationId = data.conversation.id;
                    addConversationToSidebar(data.conversation, true);
                    document.getElementById('activeChatTitle').innerText = data.conversation.title;
                    document.getElementById('messagesList').innerHTML = '';
                    document.getElementById('welcomeScreen').classList.remove('hidden');
                    
                    if (window.innerWidth < 768) {
                        toggleSidebar();
                    }
                }
            } catch (err) {
                alert('Gagal membuat percakapan baru.');
            }
        }

        function addConversationToSidebar(conv, prepend = false) {
            const list = document.getElementById('conversationList');
            const emptyNotice = document.getElementById('emptyConvNotice');
            if (emptyNotice) emptyNotice.remove();

            const item = document.createElement('div');
            item.id = `conv-item-${conv.id}`;
            item.onclick = () => selectConversation(conv.id);
            item.className = 'group relative flex items-center justify-between px-3 py-2.5 rounded-lg text-sm cursor-pointer transition-colors bg-slate-800 text-white font-medium';
            item.innerHTML = `
                <div class="flex items-center gap-2.5 overflow-hidden flex-1 mr-2">
                    <i class="fa-regular fa-message text-xs opacity-70"></i>
                    <span class="truncate conv-title">${escapeHtml(conv.title)}</span>
                </div>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="deleteConversation(event, ${conv.id})" title="Hapus chat" class="p-1 text-slate-500 hover:text-rose-400 transition-colors">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </div>
            `;

            if (prepend && list.firstChild) {
                list.insertBefore(item, list.firstChild);
            } else {
                list.appendChild(item);
            }

            updateConversationActiveUI(conv.id);
        }

        async function deleteConversation(e, id) {
            e.stopPropagation();
            if (!confirm('Apakah Anda yakin ingin menghapus percakapan ini?')) return;

            try {
                const res = await fetch(`/api/conversations/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    }
                });
                const data = await res.json();
                if (data.success) {
                    const item = document.getElementById(`conv-item-${id}`);
                    if (item) item.remove();

                    if (currentConversationId === id) {
                        const remaining = document.querySelector('#conversationList > div');
                        if (remaining) {
                            const newId = parseInt(remaining.id.replace('conv-item-', ''));
                            selectConversation(newId);
                        } else {
                            currentConversationId = null;
                            document.getElementById('activeChatTitle').innerText = 'Percakapan Baru';
                            document.getElementById('messagesList').innerHTML = '';
                            document.getElementById('welcomeScreen').classList.remove('hidden');
                        }
                    }
                }
            } catch (err) {
                alert('Gagal menghapus percakapan.');
            }
        }

        async function clearCurrentChat() {
            if (!currentConversationId) return;
            if (!confirm('Bersihkan semua pesan dalam percakapan ini?')) return;

            try {
                const res = await fetch(`/api/conversations/${currentConversationId}/clear`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                    }
                });
                const data = await res.json();
                if (data.success) {
                    document.getElementById('messagesList').innerHTML = '';
                    document.getElementById('welcomeScreen').classList.remove('hidden');
                }
            } catch (err) {
                alert('Gagal membersihkan pesan.');
            }
        }

        function sendSuggestion(text) {
            document.getElementById('messageInput').value = text;
            document.getElementById('chatForm').dispatchEvent(new Event('submit', { cancelable: true }));
        }

        async function handleChatSubmit(e) {
            e.preventDefault();
            if (isGenerating) return;

            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            if (!message) return;

            // Clear input & reset height
            input.value = '';
            input.style.height = 'auto';

            // Hide welcome
            document.getElementById('welcomeScreen').classList.add('hidden');

            // Append user message immediately
            appendMessageUI('user', message);
            scrollToBottom();

            // Set loading state
            isGenerating = true;
            document.getElementById('sendBtn').disabled = true;
            document.getElementById('typingIndicator').classList.remove('hidden');
            scrollToBottom();

            try {
                const payload = {
                    message: message,
                    conversation_id: currentConversationId,
                };

                const res = await fetch('/api/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    // Update current conversation id if new
                    if (!currentConversationId || currentConversationId !== data.conversation.id) {
                        currentConversationId = data.conversation.id;
                        addConversationToSidebar(data.conversation, true);
                    }

                    // Update conversation title if changed
                    document.getElementById('activeChatTitle').innerText = data.conversation.title;
                    const convTitleEl = document.querySelector(`#conv-item-${data.conversation.id} .conv-title`);
                    if (convTitleEl) {
                        convTitleEl.innerText = data.conversation.title;
                    }

                    // Append model response
                    appendMessageUI('model', data.assistant_message.content);
                } else {
                    appendErrorUI(data.message || 'Terjadi kesalahan saat memproses jawaban dari Gemini.');
                }
            } catch (err) {
                appendErrorUI('Gagal menghubungi server: ' + err.message);
            } finally {
                isGenerating = false;
                document.getElementById('sendBtn').disabled = false;
                document.getElementById('typingIndicator').classList.add('hidden');
                scrollToBottom();
                input.focus();
            }
        }

        function appendMessageUI(role, content) {
            const list = document.getElementById('messagesList');
            const msgEl = document.createElement('div');

            if (role === 'user') {
                msgEl.className = 'flex items-start justify-end gap-3';
                msgEl.innerHTML = `
                    <div class="max-w-[85%] md:max-w-[75%] rounded-2xl rounded-tr-sm bg-blue-600 text-white px-4 py-3 shadow-md shadow-blue-600/10 text-sm whitespace-pre-wrap leading-relaxed">
                        ${escapeHtml(content)}
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-300 text-xs shrink-0 font-bold">
                        <i class="fa-solid fa-user"></i>
                    </div>
                `;
            } else {
                msgEl.className = 'flex items-start gap-3';
                const parsedMarkdown = marked.parse(content);

                msgEl.innerHTML = `
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 via-indigo-600 to-sky-400 flex items-center justify-center text-white text-xs shrink-0 shadow-md">
                        <i class="fa-solid fa-sparkles"></i>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <div class="p-4 rounded-2xl rounded-tl-sm bg-slate-900 border border-slate-800 text-sm shadow-md">
                            <div class="markdown-body">${parsedMarkdown}</div>
                        </div>
                        <div class="flex items-center gap-2 mt-1.5 ml-1">
                            <button onclick="copyToClipboard(this, \`${escapeJsString(content)}\`)" class="text-[11px] text-slate-500 hover:text-slate-300 flex items-center gap-1 transition">
                                <i class="fa-regular fa-copy"></i> <span>Salin</span>
                            </button>
                        </div>
                    </div>
                `;
            }

            list.appendChild(msgEl);
        }

        function appendErrorUI(errorMessage) {
            const list = document.getElementById('messagesList');
            const errEl = document.createElement('div');
            errEl.className = 'flex items-start gap-3';
            errEl.innerHTML = `
                <div class="w-8 h-8 rounded-lg bg-rose-600/20 text-rose-400 border border-rose-500/30 flex items-center justify-center text-xs shrink-0">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="p-3.5 rounded-2xl rounded-tl-sm bg-rose-950/40 border border-rose-900/60 text-xs text-rose-300 leading-relaxed max-w-lg">
                    <span class="font-semibold block mb-0.5">Kesalahan Respon:</span>
                    ${escapeHtml(errorMessage)}
                </div>
            `;
            list.appendChild(errEl);
        }

        function scrollToBottom() {
            const container = document.getElementById('chatMessages');
            container.scrollTop = container.scrollHeight;
        }

        function escapeHtml(str) {
            return str.replace(/[&<>'"]/g, 
                tag => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    "'": '&#39;',
                    '"': '&quot;'
                }[tag] || tag)
            );
        }

        function escapeJsString(str) {
            return str
                .replace(/\\/g, '\\\\')
                .replace(/`/g, '\\`')
                .replace(/\${/g, '\\${');
        }

        function copyToClipboard(button, text) {
            navigator.clipboard.writeText(text).then(() => {
                const originalHtml = button.innerHTML;
                button.innerHTML = '<i class="fa-solid fa-check text-emerald-400"></i> <span class="text-emerald-400">Tersalin!</span>';
                setTimeout(() => {
                    button.innerHTML = originalHtml;
                }, 2000);
            }).catch(err => {
                console.error('Gagal menyalin:', err);
            });
        }
    </script>
</body>
</html>
