@extends('layouts.report')

@section('report_content')

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Akun</h1>
            <p class="text-gray-600 mt-1">Menampilkan rincian untuk akun [{{ $chartOfAccount->code }}] {{ $chartOfAccount->name }}</p>
        </div>
        <a href="{{ route('admin.chart_of_accounts.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-xl font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150">
            Kembali ke Daftar Akun
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        
        <div class="divide-y divide-gray-200">
            
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Kode Akun</dt>
                <dd class="text-sm text-gray-900 font-semibold md:col-span-2">{{ $chartOfAccount->code }}</dd>
            </div>

            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Nama Akun</dt>
                <dd class="text-sm text-gray-900 md:col-span-2">{{ $chartOfAccount->name }}</dd>
            </div>
            
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Tipe Akun</dt>
                <dd class="text-sm text-gray-900 md:col-span-2">{{ $chartOfAccount->type }}</dd>
            </div>

            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Saldo Normal</dt>
                <dd class="text-sm text-gray-900 md:col-span-2">{{ $chartOfAccount->normal_balance }}</dd>
            </div>

            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Akun Induk (Parent)</dt>
                <dd class="text-sm text-gray-900 md:col-span-2">
                    @if ($chartOfAccount->parent)
                        [{{ $chartOfAccount->parent->code }}] {{ $chartOfAccount->parent->name }}
                    @else
                        <span class="text-gray-500">- Tidak Ada Induk -</span>
                    @endif
                </dd>
            </div>

            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                <dd class="text-sm text-gray-900 md:col-span-2 whitespace-pre-line">
                    {{ $chartOfAccount->description ?? '-' }}
                </dd>
            </div>

        </div>

        <div class="bg-gray-50 px-6 py-4 text-right">
             <a href="{{ route('admin.chart_of_accounts.edit', $chartOfAccount->id) }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
svg>
                Edit Akun Ini
            </a>
        </div>
    </div>

@endsection