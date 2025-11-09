@extends('layouts.report')

@section('report_content')

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Akun</h1>
            <p class="text-gray-600 mt-1">Mengubah data untuk [{{ $chartOfAccount->code }}] {{ $chartOfAccount->name }}</p>
        </div>
        <a href="{{ route('admin.chart_of_accounts.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-xl font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150">
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
            <p class="font-bold">Whoops! Ada masalah dengan input Anda.</p>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <form action="{{ route('admin.chart_of_accounts.update', $chartOfAccount->id) }}" method="POST">
            @csrf
            @method('PUT') 
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Kode Akun</label>
                        <input type="text" name="code" id="code" 
                               class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" 
                               value="{{ old('code', $chartOfAccount->code) }}">
                    </div>
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Akun</label>
                        <input type="text" name="name" id="name" 
                               class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" 
                               value="{{ old('name', $chartOfAccount->name) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Tipe Akun</label>
                        <select name="type" id="type" 
                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="Asset" {{ old('type', $chartOfAccount->type) == 'Asset' ? 'selected' : '' }}>Asset</option>
                            <option value="Liability" {{ old('type', $chartOfAccount->type) == 'Liability' ? 'selected' : '' }}>Liability</option>
                            <option value="Equity" {{ old('type', $chartOfAccount->type) == 'Equity' ? 'selected' : '' }}>Equity</option>
                            <option value="Revenue" {{ old('type', $chartOfAccount->type) == 'Revenue' ? 'selected' : '' }}>Revenue</option>
                            <option value="Expense" {{ old('type', $chartOfAccount->type) == 'Expense' ? 'selected' : '' }}>Expense</option>
                        </select>
                    </div>
                    <div>
                        <label for="normal_balance" class="block text-sm font-medium text-gray-700">Saldo Normal</label>
                        <select name="normal_balance" id="normal_balance" 
                                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            <option value="Debit" {{ old('normal_balance', $chartOfAccount->normal_balance) == 'Debit' ? 'selected' : '' }}>Debit</option>
                            <option value="Credit" {{ old('normal_balance', $chartOfAccount->normal_balance) == 'Credit' ? 'selected' : '' }}>Credit</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="parent_id" class="block text-sm font-medium text-gray-700">Akun Induk (Opsional)</label>
                    <select name="parent_id" id="parent_id" 
                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        <option value="">-- Tidak Ada Induk --</option>
                        @foreach ($parents as $parent)
                            @if ($parent->id != $chartOfAccount->id) <option value="{{ $parent->id }}" {{ old('parent_id', $chartOfAccount->parent_id) == $parent->id ? 'selected' : '' }}>
                                [{{ $parent->code }}] {{ $parent->name }}
                            </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                    <textarea name="description" id="description" rows="3" 
                              class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500" 
                              placeholder="Deskripsi singkat...">{{ old('description', $chartOfAccount->description) }}</textarea>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 text-right">
                <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 custom-gradient text-white rounded-xl text-sm font-medium transition-all duration-300 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                        style="background: linear-gradient(to right, #f97316, #ea580c);">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Perbarui Akun
                </button>
            </div>
        </form>
    </div>

@endsection