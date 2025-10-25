@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.message.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Pesan</h1>
                <p class="mt-1 text-sm text-gray-600">Dari: {{ $message->user->name }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Message Details -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $message->subject }}</h3>
                        <p class="text-sm text-gray-600">{{ $message->created_at->format('d M Y, H:i') }}</p>
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
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4">
                <!-- Customer Info -->
                <div class="mb-6 bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Informasi Pelanggan</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Nama:</span>
                            <span class="ml-2 text-gray-900">{{ $message->user->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Email:</span>
                            <span class="ml-2 text-gray-900">{{ $message->user->email }}</span>
                        </div>
                        @if($message->user->phone)
                            <div>
                                <span class="text-gray-600">Telepon:</span>
                                <span class="ml-2 text-gray-900">{{ $message->user->phone }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Original Message -->
                <div class="mb-6">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Pesan Pelanggan</h4>
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                        <p class="text-gray-700">{{ $message->message }}</p>
                    </div>
                </div>

                <!-- Admin Reply Section -->
                @if($message->admin_reply)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Balasan Anda</h4>
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                            <p class="text-gray-700">{{ $message->admin_reply }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $message->replied_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Reply Form -->
        @if(!$message->admin_reply)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Balas Pesan</h3>
                </div>
                
                <form action="{{ route('admin.message.reply', $message) }}" method="POST" class="p-6">
                    @csrf
                    <div class="mb-4">
                        <label for="admin_reply" class="block text-sm font-medium text-gray-700 mb-2">
                            Balasan Anda
                        </label>
                        <textarea name="admin_reply" id="admin_reply" rows="6" required
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 @error('admin_reply') border-red-500 @enderror"
                                  placeholder="Tulis balasan Anda di sini...">{{ old('admin_reply') }}</textarea>
                        @error('admin_reply')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('admin.message.index') }}" 
                           class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" 
                                class="bg-orange-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-orange-700">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Kirim Balasan
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection