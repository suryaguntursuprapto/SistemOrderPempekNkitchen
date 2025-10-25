@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('customer.order.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Pesanan</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $order->order_number }}</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <!-- Order Status -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Status Pesanan</h3>
            </div>
            
            <div class="px-6 py-6">
                <div class="flex items-center justify-center mb-6">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-medium
                        @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->status == 'confirmed') bg-blue-100 text-blue-800
                        @elseif($order->status == 'preparing') bg-orange-100 text-orange-800
                        @elseif($order->status == 'ready') bg-green-100 text-green-800
                        @elseif($order->status == 'delivered') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800
                        @endif">
                        {{ $order->status_label }}
                    </span>
                </div>
                
                <div class="text-center text-sm text-gray-600">
                    @if($order->status == 'pending')
                        <p>Pesanan Anda sedang menunggu konfirmasi dari kami.</p>
                    @elseif($order->status == 'confirmed')
                        <p>Pesanan Anda telah dikonfirmasi dan akan segera disiapkan.</p>
                    @elseif($order->status == 'preparing')
                        <p>Pesanan Anda sedang disiapkan dengan sepenuh hati.</p>
                    @elseif($order->status == 'ready')
                        <p>Pesanan Anda sudah siap! Silakan ambil atau tunggu pengiriman.</p>
                    @elseif($order->status == 'delivered')
                        <p>Pesanan Anda telah selesai. Terima kasih!</p>
                    @else
                        <p>Pesanan telah dibatalkan.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Detail Pesanan</h3>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $order->formatted_total }}</p>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4">
                <div class="space-y-4">
                    @foreach($order->orderItems as $item)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center space-x-4">
                                @if($item->menu->image)
                                    <img src="{{ Storage::url($item->menu->image) }}" alt="{{ $item->menu->name }}" 
                                         class="h-16 w-16 object-cover rounded-lg">
                                @else
                                    <div class="h-16 w-16 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <svg class="h-8 w-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $item->menu->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $item->quantity }}x {{ 'Rp ' . number_format($item->price, 0, ',', '.') }}</p>
                                    @if($item->notes)
                                        <p class="text-xs text-gray-500">Catatan: {{ $item->notes }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900">{{ $item->formatted_subtotal }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Delivery Info -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informasi Pengiriman</h3>
            </div>
            
            <div class="px-6 py-4">
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <svg class="h-5 w-5 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Alamat Pengiriman</p>
                            <p class="text-sm text-gray-600">{{ $order->delivery_address }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Nomor Telepon</p>
                            <p class="text-sm text-gray-600">{{ $order->phone }}</p>
                        </div>
                    </div>
                    
                    @if($order->notes)
                        <div class="flex items-start space-x-3">
                            <svg class="h-5 w-5 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Catatan</p>
                                <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if($order->status == 'delivered')
            <div class="text-center">
                <div class="bg-green-50 rounded-lg p-6">
                    <svg class="mx-auto h-12 w-12 text-green-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-green-900 mb-2">Pesanan Selesai!</h3>
                    <p class="text-sm text-green-700">Terima kasih telah memesan di N-Kitchen. Semoga Anda puas dengan pempek kami!</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection