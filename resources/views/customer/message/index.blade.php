@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 h-[calc(100vh-5rem)]">
    
    <div class="bg-white w-full h-full rounded-2xl shadow-xl border border-gray-200 overflow-hidden flex flex-col relative">
        
       <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white z-20">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="w-11 h-11 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-lg">N</div>
                    <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-800">Admin N-Kitchen</h1>
                    <div class="flex items-center gap-1.5">
                        <p class="text-xs text-gray-500">Siap membantu Anda</p>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-2">
                <button onclick="clearMyChat()" class="p-2 text-gray-400 hover:text-red-500 transition-colors rounded-full hover:bg-red-50" title="Hapus Pesan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>

                <button onclick="location.reload()" class="p-2 text-gray-400 hover:text-orange-500 transition-colors rounded-full hover:bg-gray-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </div>
        <div id="chat-container" class="flex-1 overflow-y-auto p-6 bg-[#fafafa] custom-scrollbar">
            
            <div class="flex justify-center mb-8">
                <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                    Percakapan dimulai
                </span>
            </div>

            @include('customer.message.partials.chat-bubble', ['messages' => $messages])
            
        </div>

        <div class="p-4 bg-white border-t border-gray-100 z-20">
            <form id="chat-form" data-no-loading="true" action="{{ route('customer.message.store') }}" method="POST" class="flex items-end gap-3">
                @csrf
                
                <div class="flex-1 bg-gray-50 rounded-2xl border border-gray-200 flex items-center px-4 py-3 focus-within:ring-2 focus-within:ring-orange-500/20 focus-within:border-orange-500 transition-all shadow-sm">
                    <textarea id="message-input" name="message" rows="1" 
                              class="w-full bg-transparent border-none focus:ring-0 p-0 text-sm text-gray-800 resize-none max-h-32 placeholder-gray-400" 
                              placeholder="Tulis pesan Anda di sini..." 
                              required 
                              style="min-height: 24px; line-height: 24px;"></textarea>
                </div>

                <button type="submit" id="send-btn" class="p-3 bg-orange-600 text-white rounded-xl hover:bg-orange-700 shadow-lg hover:shadow-xl transition-all transform active:scale-95 flex-shrink-0 h-[50px] w-[50px] flex items-center justify-center">
                    <svg class="w-6 h-6 translate-x-0.5 translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </form>
            <div class="text-center mt-2">
                <p class="text-[10px] text-gray-400">Kami biasanya membalas dalam beberapa menit</p>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function scrollToBottom() {
        const container = document.getElementById('chat-container');
        container.scrollTop = container.scrollHeight;
    }

    // 1. Scroll ke bawah saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        scrollToBottom();
        document.getElementById('message-input').focus();
    });

    // 2. Handle Submit (AJAX & Optimistic UI)
    document.getElementById('chat-form').addEventListener('submit', function(e) {
        e.preventDefault(); // MENCEGAH RELOAD HALAMAN
        
        const btn = document.getElementById('send-btn');
        const input = document.getElementById('message-input');
        const message = input.value;
        
        if(message.trim() === '') return;

        // UI Optimis (Tampilkan chat langsung seolah-olah sudah terkirim)
        const chatContainer = document.getElementById('chat-container');
        
        // Hapus "Welcome Message" jika ada
        if(chatContainer.innerText.includes('Percakapan dimulai')) {
             // Jangan hapus semua, cukup biarkan tertumpuk
        }

        // Waktu Lokal
        const timeNow = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

        // Tambahkan Bubble Chat Sendiri (Orange)
        chatContainer.innerHTML += `
            <div class="flex justify-end mb-4 gap-3 opacity-70 transition-opacity duration-500" id="temp-msg">
                <div class="flex flex-col items-end max-w-[80%]">
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
        
        // Reset Input
        input.value = '';
        input.style.height = '24px';

        // Kirim ke Server (AJAX)
        fetch('{{ route("customer.message.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                // Hilangkan efek opacity (tanda sukses)
                const tempMsg = document.getElementById('temp-msg');
                if(tempMsg) {
                    tempMsg.classList.remove('opacity-70');
                    tempMsg.removeAttribute('id');
                }
                
                // Fetch ulang chat bubble untuk memastikan data sinkron (opsional tapi disarankan)
                fetchDataSilent(); 
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengirim pesan. Silakan coba lagi.');
        });
    });

    // 3. Auto Resize Textarea
    const tx = document.getElementById('message-input');
    tx.addEventListener("input", function() {
        this.style.height = "24px";
        this.style.height = (this.scrollHeight) + "px";
    }, false);

    // 4. Enter to Send
    tx.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            document.getElementById('chat-form').dispatchEvent(new Event('submit'));
        }
    });

    function clearMyChat() {
        if(confirm('Apakah Anda yakin ingin menghapus semua pesan?')) {
            fetch('{{ route("customer.message.clear") }}', {
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
                    // Reset tampilan ke welcome message saja
                    container.innerHTML = `
                        <div class="flex justify-center mb-8">
                            <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                                Percakapan dimulai
                            </span>
                        </div>
                        <div class="flex h-full flex-col items-center justify-center text-gray-400 text-sm mt-10">
                            <p>Pesan telah dihapus.</p>
                        </div>
                    `;
                }
            });
        }
    }

    // Fungsi fetch diam-diam
    function fetchDataSilent() {
        fetch('{{ route("customer.message.json") }}')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('chat-container');
                const currentHTML = container.innerHTML;
                
                // Hanya update jika ada pesan BARU dari ADMIN (atau sinkronisasi)
                // Kita cek panjang string agar simple
                if (data.html.length > currentHTML.length + 20) { 
                    const isAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 100;
                    
                    // Update konten
                    // Kita bungkus welcome message agar tidak hilang
                    const welcomeMsg = `<div class="flex justify-center mb-8"><span class="text-xs text-gray-400 bg-gray-100 px-3 py-1 rounded-full">Percakapan dimulai</span></div>`;
                    
                    container.innerHTML = welcomeMsg + data.html;
                    
                    if(isAtBottom) scrollToBottom();
                }
            });
    }

    // 5. Polling (Cek balasan admin setiap 5 detik)
    setInterval(fetchDataSilent, 5000);
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>
@endpush
@endsection