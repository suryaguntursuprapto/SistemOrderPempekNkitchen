@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 h-screen max-h-[85vh]">
    
    <div class="bg-white w-full h-full rounded-2xl shadow-xl border border-gray-200 overflow-hidden flex">
        
        <div class="w-80 flex-shrink-0 border-r border-gray-100 bg-white flex flex-col">
            
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">Pesan</h2>
                <button onclick="location.reload()" class="text-gray-400 hover:text-orange-500 transition-colors" title="Refresh Pesan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>

            <div class="px-4 py-3">
                <div class="relative">
                    <input type="text" placeholder="Cari pelanggan..." class="w-full bg-gray-50 text-sm border-none rounded-xl pl-10 py-2.5 focus:ring-2 focus:ring-orange-500/20 transition-all placeholder-gray-400">
                    <svg class="w-4 h-4 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <div id="user-list-container" class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-1">
                @foreach($customers as $customer)
                    <div onclick="loadChat({{ $customer->id }})" 
                         class="customer-item group relative cursor-pointer p-3 rounded-xl hover:bg-orange-50 transition-all duration-200 flex items-center gap-3"
                         id="customer-{{ $customer->id }}">
                        
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-sm group-hover:bg-white group-hover:shadow-sm transition-all">
                                {{ substr($customer->name, 0, 1) }}
                            </div>
                            @if($customer->unread_count > 0)
                                <div id="badge-{{ $customer->id }}" class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold h-4 w-4 flex items-center justify-center rounded-full border-2 border-white">
                                    {{ $customer->unread_count }}
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-center mb-0.5">
                                <h3 class="text-sm font-bold text-gray-800 truncate">{{ $customer->name }}</h3>
                                <span class="text-[10px] text-gray-400" id="time-{{ $customer->id }}">
                                    @if($customer->messages->isNotEmpty())
                                        {{ $customer->messages->first()->created_at->setTimezone('Asia/Jakarta')->format('H:i') }}
                                    @endif
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 truncate" id="preview-{{ $customer->id }}">
                                @if($customer->messages->isNotEmpty())
                                    @if($customer->messages->last()->message === '[SYSTEM_INIT]')
                                        <span class="text-orange-500">Mulai percakapan</span>
                                    @else
                                        {{ Str::limit($customer->messages->last()->admin_reply ?? $customer->messages->last()->message, 25) }}
                                    @endif
                                @else
                                    <span class="text-gray-400 italic">Belum ada pesan</span>
                                @endif
                            </p>
                        </div>
                        
                        <div class="active-indicator hidden absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-orange-500 rounded-r-full"></div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex-1 flex flex-col bg-white relative">
            
            <div id="chat-header" class="h-16 px-6 border-b border-gray-100 flex items-center justify-between bg-white hidden z-20">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold text-sm" id="header-avatar">
                        </div>
                    <div>
                        <h3 id="active-user-name" class="font-bold text-gray-800 text-sm">Nama User</h3>
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                            <p class="text-xs text-gray-500">Online</p>
                        </div>
                    </div>
                </div>
                
                <button onclick="confirmClearChat()" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition-all" title="Hapus Riwayat Chat">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>

            <div id="welcome-screen" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 z-10">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center mb-4 text-orange-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Pilih Percakapan</h3>
                <p class="text-gray-500 text-sm mt-1">Pilih pelanggan di sebelah kiri untuk mulai chat.</p>
            </div>

            <div id="chat-container" class="flex-1 overflow-y-auto p-6 space-y-1 hidden custom-scrollbar bg-white">
                </div>

            <div id="chat-input-area" class="p-4 bg-white border-t border-gray-100 hidden z-20">
                <form id="reply-form" class="relative flex items-end gap-2">
                    @csrf
                    <input type="hidden" id="active-user-id" name="user_id">
                    
                    <div class="flex-1 bg-gray-50 rounded-xl border border-gray-200 flex items-center px-4 py-2 focus-within:ring-2 focus-within:ring-orange-500/20 focus-within:border-orange-500 transition-all">
                        <textarea id="message-input" name="message" rows="1" 
                                  class="w-full bg-transparent border-none focus:ring-0 p-0 text-sm text-gray-800 resize-none max-h-32 placeholder-gray-400" 
                                  placeholder="Ketik pesan balasan..." 
                                  style="min-height: 24px; line-height: 24px;"></textarea>
                    </div>

                    <button type="submit" id="send-btn" class="p-3 bg-orange-600 text-white rounded-xl hover:bg-orange-700 shadow-md transition-all transform active:scale-95 flex-shrink-0 h-[42px] w-[42px] flex items-center justify-center">
                        <svg class="w-5 h-5 translate-x-0.5 translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
                <div class="text-center mt-2">
                    <p class="text-[10px] text-gray-400">Tekan Enter untuk mengirim, Shift+Enter untuk baris baru</p>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    let activeUserId = null;

    function loadChat(userId) {
        activeUserId = userId;
        
        // UI Updates
        document.querySelectorAll('.customer-item').forEach(el => {
            el.classList.remove('bg-orange-50', 'ring-1', 'ring-orange-100');
            el.querySelector('.active-indicator').classList.add('hidden');
        });
        
        const activeItem = document.getElementById(`customer-${userId}`);
        activeItem.classList.add('bg-orange-50', 'ring-1', 'ring-orange-100');
        activeItem.querySelector('.active-indicator').classList.remove('hidden');
        
        // Sembunyikan badge unread jika ada
        const badge = document.getElementById(`badge-${userId}`);
        if(badge) badge.classList.add('hidden');

        document.getElementById('welcome-screen').classList.add('hidden');
        document.getElementById('chat-header').classList.remove('hidden');
        document.getElementById('chat-header').classList.add('flex');
        document.getElementById('chat-container').classList.remove('hidden');
        document.getElementById('chat-input-area').classList.remove('hidden');
        
        document.getElementById('active-user-id').value = userId;

        const userName = activeItem.querySelector('h3').innerText;
        document.getElementById('active-user-name').innerText = userName;
        document.getElementById('header-avatar').innerText = userName.charAt(0);

        const container = document.getElementById('chat-container');
        container.innerHTML = '<div class="flex justify-center items-center h-full"><div class="animate-spin h-6 w-6 border-2 border-orange-500 rounded-full border-t-transparent"></div></div>';

        fetch(`/admin/message/chat/${userId}`)
            .then(response => response.json())
            .then(data => {
                if(data.html.trim() === "") {
                    container.innerHTML = '<div class="flex h-full flex-col items-center justify-center text-gray-400 text-sm"><p>Belum ada riwayat chat.</p><p class="text-xs mt-1">Sapa pelanggan Anda sekarang!</p></div>';
                } else {
                    container.innerHTML = data.html;
                }
                scrollToBottom();
                document.getElementById('message-input').focus();
            });
    }

    function scrollToBottom() {
        const container = document.getElementById('chat-container');
        container.scrollTop = container.scrollHeight;
    }

    document.getElementById('reply-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const messageInput = document.getElementById('message-input');
        const message = messageInput.value;

        if(!message.trim()) return;

        const chatContainer = document.getElementById('chat-container');
        if(chatContainer.innerText.includes('Belum ada riwayat chat')) chatContainer.innerHTML = '';

        // PERBAIKAN: Gunakan Timezone WIB (Asia/Jakarta) secara paksa di JS
        const now = new Date();
        const timeNow = now.toLocaleTimeString('en-GB', {
            hour: '2-digit', 
            minute: '2-digit',
            timeZone: 'Asia/Jakarta'
        });

        // UI Optimis (Langsung muncul)
        chatContainer.innerHTML += `
            <div class="flex justify-end mb-4 gap-3 opacity-70 transition-opacity duration-500" id="temp-msg">
                <div class="flex flex-col items-end max-w-[75%]">
                    <div class="bg-orange-500 px-4 py-2.5 rounded-2xl rounded-tr-none text-sm text-white leading-relaxed shadow-sm">
                        ${message.replace(/\n/g, '<br>')}
                    </div>
                    <div class="flex items-center gap-1 mt-1 mr-1">
                        <span class="text-[10px] text-gray-400">${timeNow} WIB</span>
                        <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
        `;
        scrollToBottom();
        
        messageInput.value = '';
        messageInput.style.height = '24px';

        // PERBAIKAN: Update Sidebar (Preview & Jam) secara Realtime
        const previewElement = document.getElementById(`preview-${activeUserId}`);
        const timeElement = document.getElementById(`time-${activeUserId}`);
        const userElement = document.getElementById(`customer-${activeUserId}`);
        const listContainer = document.getElementById('user-list-container');

        if(previewElement) {
            previewElement.innerText = message;
            previewElement.classList.remove('italic', 'text-gray-400');
        }
        if(timeElement) {
            timeElement.innerText = timeNow;
        }
        // Pindahkan User ke paling atas
        if(userElement && listContainer) {
            listContainer.insertBefore(userElement, listContainer.firstChild);
        }

        // Kirim ke Backend
        fetch('{{ route("admin.message.reply") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ user_id: activeUserId, message: message })
        }).then(res => res.json()).then(data => {
            if(data.status === 'success') {
                // Hilangkan efek opacity tanda berhasil
                const tempMsg = document.getElementById('temp-msg');
                if(tempMsg) {
                    tempMsg.classList.remove('opacity-70');
                    tempMsg.id = ''; // Remove ID to avoid conflict
                }
            }
        });
    });

    const tx = document.getElementById('message-input');
    tx.addEventListener("input", function() {
        this.style.height = "24px";
        this.style.height = (this.scrollHeight) + "px";
    }, false);

    tx.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('reply-form').dispatchEvent(new Event('submit'));
        }
    });

    function confirmClearChat() {
        if(!activeUserId) return;

        if(confirm('Apakah Anda yakin ingin menghapus semua riwayat chat dengan pelanggan ini?')) {
            fetch(`/admin/message/clear/${activeUserId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    const container = document.getElementById('chat-container');
                    container.innerHTML = '<div class="flex h-full flex-col items-center justify-center text-gray-400 text-sm"><p>Riwayat chat telah dihapus.</p></div>';
                    const preview = document.getElementById(`preview-${activeUserId}`);
                    if(preview) {
                        preview.innerText = 'Belum ada pesan';
                        preview.classList.add('italic', 'text-gray-400');
                    }
                }
            });
        }
    }

    setInterval(() => {
        if(activeUserId) {
            fetch(`/admin/message/chat/${activeUserId}`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('chat-container');
                if(data.html.length !== container.innerHTML.length && data.html.trim() !== "") {
                     const isAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 150;
                     container.innerHTML = data.html;
                     if(isAtBottom) scrollToBottom();
                }
            });
        }
    }, 5000);
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>
@endpush
@endsection