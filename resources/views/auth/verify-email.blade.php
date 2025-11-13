@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-lg">
        <div>
            <div class="mx-auto h-20 w-20 bg-orange-100 rounded-full flex items-center justify-center">
                <svg class="h-10 w-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-2xl font-extrabold text-gray-900">
                Verifikasi Alamat Email Anda
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Terima kasih telah mendaftar! Sebelum memulai, silakan periksa email Anda untuk link verifikasi.
            </p>
            <p class="mt-4 text-center text-sm text-gray-600">
                Jika Anda tidak menerima email, kami akan mengirimkannya kembali.
            </p>
        </div>

        @if (session('success'))
            <div class="bg-green-50 text-green-600 text-sm p-3 rounded-lg text-center">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-center justify-between mt-6">
            <form class="w-full" method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-md transition-all hover:shadow-lg">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>
        </div>

        <div class="text-center mt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection