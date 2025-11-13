@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-lg">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Reset Password Anda
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Masukkan password baru Anda di bawah ini.
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('password.update') }}" method="POST">
            @csrf
            
            <!-- Token -->
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" required readonly
                       class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-xl bg-gray-100 focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm @error('email') border-red-500 @enderror" 
                       placeholder="Email Anda" value="{{ $email ?? old('email') }}">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                <input id="password" name="password" type="password" required 
                       class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-xl focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm @error('password') border-red-500 @enderror" 
                       placeholder="Password Baru">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Konfirmasi Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required 
                       class="mt-1 appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-xl focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm" 
                       placeholder="Konfirmasi Password Baru">
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-md transition-all hover:shadow-lg">
                    Simpan Password Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection