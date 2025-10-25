@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('customer.message.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Pesan</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $message->subject }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $message->subject }}</h3>
                    <p class="text-sm text-gray-600">Dikirim: {{ $message->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    @if($message->admin_reply)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Sudah Dibalas
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Menunggu Balasan
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="px-6 py-6 space-y-6">
            <!-- Original Message -->
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">Pesan Anda</h4>
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $message->message }}</p>
                </div>
            </div>

            <!-- Admin Reply -->
            @if($message->admin_reply)
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-gray-900">Balasan dari N-Kitchen</h4>
                        <span class="text-xs text-gray-500">{{ $message->replied_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="bg-orange-50 border-l-4 border-orange-400 p-4 rounded-lg">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $message->admin_reply }}</p>
                    </div>
                </div>
            @else
                <div class="border-t border-gray-200 pt-6">
                    <div class="text-center py-6">
                        <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Menunggu Balasan</h3>
                        <p class="mt-1 text-sm text-gray-500">Kami akan membalas pesan Anda dalam waktu 24 jam</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
            <a href="{{ route('customer.message.index') }}" 
               class="text-orange-600 hover:text-orange-500 text-sm font-medium">
                ← Kembali ke Daftar Pesan
            </a>
            
            @if(!$message->admin_reply)
                <a href="{{ route('customer.message.create') }}" 
                   class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded text-sm font-medium">
                    Kirim Pesan Baru
                </a>
            @endif
        </div>
    </div>

    <!-- Help Section -->
    @if($message->admin_reply)
        <div class="mt-6 bg-green-50 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-800">
                        Pesan Anda telah dibalas. Jika Anda memiliki pertanyaan lanjutan, silakan kirim pesan baru.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection