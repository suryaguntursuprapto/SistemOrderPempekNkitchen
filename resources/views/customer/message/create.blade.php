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
                <h1 class="text-3xl font-bold text-gray-900">Kirim Pesan</h1>
                <p class="mt-1 text-sm text-gray-600">Sampaikan pertanyaan atau keluhan Anda kepada kami</p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Formulir Pesan</h3>
            <p class="mt-1 text-sm text-gray-600">Kami akan merespons pesan Anda dalam waktu 24 jam</p>
        </div>
        
        <form action="{{ route('customer.message.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">Subjek</label>
                <input type="text" name="subject" id="subject" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 @error('subject') border-red-500 @enderror"
                       value="{{ old('subject') }}" placeholder="Contoh: Pertanyaan tentang menu">
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Pesan</label>
                <textarea name="message" id="message" rows="6" required
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 @error('message') border-red-500 @enderror"
                          placeholder="Tuliskan pesan Anda di sini...">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-500">Jelaskan dengan detail agar kami dapat membantu Anda dengan lebih baik.</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Informasi Kontak Anda</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Nama:</span>
                        <span class="ml-2 text-gray-900">{{ auth()->user()->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Email:</span>
                        <span class="ml-2 text-gray-900">{{ auth()->user()->email }}</span>
                    </div>
                    @if(auth()->user()->phone)
                        <div>
                            <span class="text-gray-600">Telepon:</span>
                            <span class="ml-2 text-gray-900">{{ auth()->user()->phone }}</span>
                        </div>
                    @endif
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    Jika informasi di atas tidak akurat, silakan perbarui profil Anda terlebih dahulu.
                </p>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('customer.message.index') }}" 
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Batal
                </a>
                <button type="submit" 
                        class="bg-orange-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Kirim Pesan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
