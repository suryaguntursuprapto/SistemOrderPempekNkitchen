@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-lg">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Lupa Password Anda?
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Jangan khawatir. Masukkan email Anda dan kami akan mengirimkan link untuk mereset password Anda.
            </p>
        </div>

        @if (session('success'))
            <div class="bg-green-50 text-green-600 text-sm p-3 rounded-lg text-center">
                {{ session('success') }}
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="rounded-md shadow-sm">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Alamat Email</label>
                    <input id="email" name="email" type="email" required 
                           class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-xl focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm @error('email') border-red-500 @enderror" 
                           placeholder="Email Anda" value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-md transition-all hover:shadow-lg">
                    Kirim Link Reset Password
                </button>
            </div>
            
            <p class="mt-4 text-center text-sm text-gray-600">
                Ingat password Anda?
                <a href="{{ route('login') }}" class="font-medium text-orange-600 hover:text-orange-500">
                    Login di sini
                </a>
            </p>
        </form>
    </div>
</div>
@endsection