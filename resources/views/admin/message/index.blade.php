@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <h1 class="text-3xl font-bold text-gray-900">Kelola Pesan</h1>
        <p class="mt-1 text-sm text-gray-600">Pesan dari pelanggan dan balasan Anda</p>
    </div>

    @if($messages->count() > 0)
        <div class="space-y-6">
            @foreach($messages as $message)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $message->subject }}</h3>
                                    <p class="text-sm text-gray-600">{{ $message->user->name }} • {{ $message->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($message->admin_reply)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Sudah Dibalas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Belum Dibalas
                                        </span>
                                    @endif
                                    
                                    @if(!$message->is_read)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Baru
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('admin.message.show', $message) }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                                Lihat & Balas
                            </a>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4">
                        <p class="text-sm text-gray-700 mb-2"><strong>Pesan:</strong></p>
                        <p class="text-sm text-gray-600 bg-gray-50 rounded p-3">{{ Str::limit($message->message, 150) }}</p>
                        
                        @if($message->admin_reply)
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <p class="text-sm text-gray-700 mb-2"><strong>Balasan Anda:</strong></p>
                                <p class="text-sm text-gray-600 bg-orange-50 rounded p-3">{{ Str::limit($message->admin_reply, 150) }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $message->replied_at->format('d M Y, H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $messages->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pesan</h3>
            <p class="mt-1 text-sm text-gray-500">Pesan dari pelanggan akan muncul di sini.</p>
        </div>
    @endif
</div>
@endsection