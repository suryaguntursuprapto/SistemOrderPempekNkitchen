@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="py-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Detail Pesanan</h1>
            <p class="text-sm text-gray-600">Order #{{ $order->order_number }}</p>
        </div>
    </div>

    <!-- Order Status Banner -->
    <div class="mb-6 p-6 rounded-2xl shadow-lg border-l-4 
        @if($order->status === 'confirmed') bg-green-50 border-green-400
        @elseif($order->status === 'pending') bg-yellow-50 border-yellow-400
        @elseif($order->status === 'processing') bg-blue-50 border-blue-400
        @elseif($order->status === 'shipped') bg-purple-50 border-purple-400
        @elseif($order->status === 'delivered') bg-emerald-50 border-emerald-400
        @elseif($order->status === 'cancelled') bg-red-50 border-red-400
        @else bg-gray-50 border-gray-400
        @endif">
        
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- Status Icon -->
                <div class="flex-shrink-0">
                    @if($order->status === 'confirmed')
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    @elseif($order->status === 'pending')
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    @elseif($order->status === 'processing')
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                            </svg>
                        </div>
                    @elseif($order->status === 'delivered')
                        <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    @elseif($order->status === 'cancelled')
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    @else
                        <div class="w-12 h-12 bg-gray-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Status Text -->
                <div>
                    <h3 class="text-lg font-bold 
                        @if($order->status === 'confirmed') text-green-800
                        @elseif($order->status === 'pending') text-yellow-800
                        @elseif($order->status === 'processing') text-blue-800
                        @elseif($order->status === 'delivered') text-emerald-800
                        @elseif($order->status === 'cancelled') text-red-800
                        @else text-gray-800
                        @endif">
                        {{ ucfirst($order->status) }}
                    </h3>
                    <p class="text-sm 
                        @if($order->status === 'confirmed') text-green-600
                        @elseif($order->status === 'pending') text-yellow-600
                        @elseif($order->status === 'processing') text-blue-600
                        @elseif($order->status === 'delivered') text-emerald-600
                        @elseif($order->status === 'cancelled') text-red-600
                        @else text-gray-600
                        @endif">
                        @if($order->status === 'confirmed')
                            Pesanan Anda telah dikonfirmasi dan sedang diproses
                        @elseif($order->status === 'pending')
                            Menunggu konfirmasi pembayaran
                        @elseif($order->status === 'processing')
                            Pesanan sedang disiapkan
                        @elseif($order->status === 'shipped')
                            Pesanan dalam perjalanan
                        @elseif($order->status === 'delivered')
                            Pesanan telah sampai
                        @elseif($order->status === 'cancelled')
                            Pesanan dibatalkan
                        @else
                            Status tidak diketahui
                        @endif
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2">
                @if($order->status === 'pending' && $order->payment && $order->payment->payment_method_id)
                    @php
                        $paymentMethod = $order->payment->paymentMethod;
                    @endphp
                    
                    @if($paymentMethod && $paymentMethod->type === 'midtrans')
                        <!-- Payment Button -->
                        <a href="{{ route('customer.order.midtrans', $order) }}" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                            Bayar Sekarang
                        </a>
                        
                        <!-- Check Status Button -->
                        <a href="{{ route('customer.orders', $order) }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                            Cek Status
                        </a>
                    @endif
                @elseif($order->status === 'confirmed')
                    <!-- Reorder Button -->
                    <a href="{{ route('customer.order.reorder', $order) }}" 
                       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                        Pesan Lagi
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Order Details -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Detail Pesanan</h3>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Order Items -->
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Item Pesanan</h4>
                    @foreach($order->orderItems as $item)
                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg mb-3">
                            @if($item->menu->image)
                                <img src="{{ Storage::url($item->menu->image) }}" 
                                     alt="{{ $item->menu->name }}" 
                                     class="w-16 h-16 rounded-lg object-cover">
                            @else
                                <div class="w-16 h-16 bg-gradient-to-br from-orange-200 to-red-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h5 class="font-medium text-gray-900">{{ $item->menu->name }}</h5>
                                <p class="text-sm text-gray-600">{{ $item->quantity }}x @ Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                <p class="font-semibold text-orange-600">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Customer Info -->
                <div class="border-t pt-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Informasi Pengiriman</h4>
                    <div class="space-y-2 text-sm">
                        <p><span class="font-medium text-gray-700">Nama:</span> {{ $order->user->name }}</p>
                        <p><span class="font-medium text-gray-700">Telepon:</span> {{ $order->phone }}</p>
                        <p><span class="font-medium text-gray-700">Alamat:</span> {{ $order->delivery_address }}</p>
                        @if($order->notes)
                            <p><span class="font-medium text-gray-700">Catatan:</span> {{ $order->notes }}</p>
                        @endif
                    </div>
                </div>

                <!-- Pricing Breakdown -->
                <div class="border-t pt-4">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Ongkos Kirim:</span>
                            <span class="font-medium">Rp {{ number_format(5000, 0, ',', '.') }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-900">Total:</span>
                            <span class="text-xl font-bold text-orange-600">Rp {{ number_format($order->total_amount + 5000, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment & Status Info -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informasi Pembayaran</h3>
            </div>
            
            <div class="p-6 space-y-6">
                @if($order->payment)
                    <!-- Payment Method -->
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Metode Pembayaran</h4>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $order->payment->paymentMethod ? $order->payment->paymentMethod->name : 'Unknown' }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $order->payment->payment_type ?? 'N/A' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($order->payment->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($order->payment->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->payment->status === 'failed') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($order->payment->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="border-t pt-4">
                        <h4 class="font-semibold text-gray-900 mb-3">Detail Pembayaran</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jumlah:</span>
                                <span class="font-medium">Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</span>
                            </div>
                            @if($order->payment->midtrans_transaction_id)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Transaction ID:</span>
                                    <span class="font-medium text-xs">{{ $order->payment->midtrans_transaction_id }}</span>
                                </div>
                            @endif
                            @if($order->payment->paid_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Dibayar pada:</span>
                                    <span class="font-medium">{{ $order->payment->paid_at->format('d M Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500">Belum ada informasi pembayaran</p>
                    </div>
                @endif

                <!-- Order Timeline -->
                <div class="border-t pt-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Timeline Pesanan</h4>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                            <div class="text-sm">
                                <p class="font-medium">Pesanan dibuat</p>
                                <p class="text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($order->payment && $order->payment->paid_at)
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                <div class="text-sm">
                                    <p class="font-medium">Pembayaran dikonfirmasi</p>
                                    <p class="text-gray-500">{{ $order->payment->paid_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        @endif

                        @if($order->status === 'processing')
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                                <div class="text-sm">
                                    <p class="font-medium">Pesanan sedang diproses</p>
                                    <p class="text-gray-500">{{ $order->updated_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        @elseif($order->status === 'delivered')
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                                <div class="text-sm">
                                    <p class="font-medium">Pesanan selesai</p>
                                    <p class="text-gray-500">{{ $order->updated_at->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="py-6 flex justify-center space-x-4">
        <a href="{{ route('customer.orders') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold transition-colors">
            Kembali ke Riwayat
        </a>
        
        @if($order->status === 'delivered')
            <a href="{{ route('customer.order.reorder', $order) }}" 
               class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-semibold transition-colors">
                Pesan Lagi
            </a>
        @endif
    </div>
</div>
@endsection