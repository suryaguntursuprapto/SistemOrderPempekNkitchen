@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="py-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.menu.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $menu->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">Detail informasi menu</p>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($menu->image)
            <img class="w-full h-64 object-cover" src="{{ Storage::url($menu->image) }}" alt="{{ $menu->name }}">
        @else
            <div class="w-full h-64 bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                <svg class="h-24 w-24 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
        @endif
        
        <div class="p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $menu->name }}</h2>
                    <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full mt-2 inline-block">{{ ucfirst($menu->category) }}</span>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-orange-600 mb-2">{{ $menu->formatted_price }}</div>
                    @if($menu->is_available)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Tersedia
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                            Tidak Tersedia
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Deskripsi</h3>
                <p class="text-gray-600 leading-relaxed">{{ $menu->description }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-2">Informasi Menu</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID Menu:</span>
                            <span class="text-gray-900">#{{ $menu->id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Kategori:</span>
                            <span class="text-gray-900">{{ ucfirst($menu->category) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Harga:</span>
                            <span class="text-gray-900 font-semibold">{{ $menu->formatted_price }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="text-gray-900">{{ $menu->is_available ? 'Tersedia' : 'Tidak Tersedia' }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-2">Statistik</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dibuat:</span>
                            <span class="text-gray-900">{{ $menu->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Terakhir Diupdate:</span>
                            <span class="text-gray-900">{{ $menu->updated_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Pesanan:</span>
                            <span class="text-gray-900">{{ $menu->orderItems->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.menu.index') }}" 
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Kembali
                </a>
                <a href="{{ route('admin.menu.edit', $menu) }}" 
                   class="bg-orange-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-orange-700">
                    Edit Menu
                </a>
            </div>
        </div>
    </div>
</div>
@endsection