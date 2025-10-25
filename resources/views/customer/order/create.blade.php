@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="py-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('customer.order.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-orange-600">
                        ← Kembali ke Menu
                    </a>
                </li>
            </ol>
        </nav>
        <h1 class="text-3xl font-bold text-gray-900">Checkout Pesanan</h1>
        <p class="mt-1 text-sm text-gray-600">Lengkapi data pengiriman dan pilih metode pembayaran</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Order Summary -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-50 to-red-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Ringkasan Pesanan</h3>
            </div>
            
            <div class="p-6">
                <!-- Cart Items -->
                <div class="space-y-4 mb-6">
                    @foreach($validCartItems as $item)
                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                            @if($item['image'])
                                <img src="{{ $item['image'] }}" 
                                     alt="{{ $item['name'] }}" 
                                     class="w-16 h-16 rounded-lg object-cover">
                            @else
                                <div class="w-16 h-16 bg-gradient-to-br from-orange-200 to-red-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $item['name'] }}</h4>
                                <p class="text-sm text-gray-600">{{ $item['quantity'] }}x @ Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                <p class="font-semibold text-orange-600">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Price Calculation -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Ongkos Kirim:</span>
                        <span class="font-medium">Rp 5.000</span>
                    </div>
                    <hr class="my-2">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900">Total:</span>
                        <span class="text-lg font-bold text-orange-600">Rp {{ number_format($totalAmount + 5000, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Form -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Detail Pengiriman</h3>
            </div>

            <form action="{{ route('customer.order.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Delivery Address -->
                <div>
                    <label for="delivery_address" class="block text-sm font-semibold text-gray-700 mb-2">
                        Alamat Pengiriman
                    </label>
                    <textarea name="delivery_address" id="delivery_address" required rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('delivery_address') border-red-500 @enderror"
                              placeholder="Masukkan alamat lengkap untuk pengiriman...">{{ old('delivery_address', session('reorder_data.delivery_address', auth()->user()->address ?? '')) }}</textarea>
                    @error('delivery_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                        Nomor Telepon
                    </label>
                    <input type="text" name="phone" id="phone" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                           value="{{ old('phone', session('reorder_data.phone', auth()->user()->phone ?? '')) }}" 
                           placeholder="Nomor telepon yang dapat dihubungi">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                        Catatan Pesanan (Opsional)
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('notes') border-red-500 @enderror"
                              placeholder="Catatan khusus untuk pesanan...">{{ old('notes', session('reorder_data.notes')) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-4">
                        Metode Pembayaran
                    </label>
                    <div class="space-y-3">
                        @foreach($paymentMethods as $method)
                            <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-orange-300 hover:bg-orange-50 cursor-pointer transition-all duration-200 @error('payment_method_id') border-red-500 @enderror">
                                <input type="radio" name="payment_method_id" value="{{ $method->id }}" 
                                       class="text-orange-600 focus:ring-orange-500 mr-4"
                                       {{ old('payment_method_id') == $method->id ? 'checked' : '' }}
                                       required>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $method->name }}</h4>
                                            @if($method->account_number)
                                                <p class="text-sm text-gray-600">
                                                    {{ $method->account_number }} - {{ $method->account_name }}
                                                </p>
                                            @endif
                                            @if($method->type == 'midtrans')
                                                <p class="text-sm text-gray-600">Credit Card, Bank Transfer, e-Wallet, dan lainnya</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center">
                                            @if($method->type == 'midtrans')
                                                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                    </svg>
                                                </div>
                                            @elseif($method->type == 'bank_transfer')
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if($method->instructions)
                                        <p class="text-xs text-gray-500 mt-1">{{ $method->instructions }}</p>
                                    @endif
                                    
                                    @if($method->type == 'midtrans')
                                        <div class="mt-2 flex flex-wrap gap-1">
                                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">Visa</span>
                                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">Mastercard</span>
                                            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">GoPay</span>
                                            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">ShopeePay</span>
                                            <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">BCA VA</span>
                                            <span class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded-full">Indomaret</span>
                                        </div>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('payment_method_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" 
                            class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" 
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span>Buat Pesanan</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
