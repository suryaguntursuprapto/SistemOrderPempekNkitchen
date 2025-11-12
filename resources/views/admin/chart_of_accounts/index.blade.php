@extends('layouts.report')

@section('report_content')

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Bagan Akun (COA)</h1>
            <p class="text-gray-600 mt-1">Mengelola semua akun untuk pencatatan jurnal.</p>
        </div>
        <a href="{{ route('admin.chart_of_accounts.create') }}" 
           class="inline-flex items-center px-4 py-2 custom-gradient text-white rounded-xl text-sm font-medium transition-all duration-300 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
           style="background: linear-gradient(to right, #f97316, #ea580c);">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Akun Baru
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg" role="alert">
            <p class="font-bold">Berhasil!</p>
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kode
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama Akun
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipe
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Saldo Normal
                        </th>
                       <th scope="col" class="px-6 py-3 text-right ...">
                            Saldo Saat Ini
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($accounts as $account)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{-- Menampilkan nomor urut yang benar untuk paginasi --}}
                                {{ $loop->iteration + $accounts->firstItem() - 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ $account->code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $account->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $account->type }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $account->normal_balance }}
                            </td>
                           <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-semibold">
                                @php
                                    // Ambil Saldo Awal
                                    $balance = $account->opening_balance;
                                    
                                    // Ambil total debit/kredit dari controller (hasil withSum)
                                    $total_debits = $account->journal_transactions_sum_debit ?? 0;
                                    $total_credits = $account->journal_transactions_sum_credit ?? 0;

                                    // Hitung saldo akhir berdasarkan Saldo Normal akun
                                    if ($account->normal_balance == 'Debit') {
                                        $current_balance = $balance + $total_debits - $total_credits;
                                    } else {
                                        // Asumsi Saldo Normal 'Credit'
                                        $current_balance = $balance - $total_debits + $total_credits;
                                    }
                                @endphp
                                
                                Rp {{ number_format($current_balance, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                                <form action="{{ route('admin.chart_of_accounts.destroy', $account->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ini?');" class="inline-flex gap-4">
                                    <a href="{{ route('admin.chart_of_accounts.show', $account->id) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                                    <a href="{{ route('admin.chart_of_accounts.edit', $account->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8C10.89 8 9.92.402 9.401 1M12 4v1m0 14v1m-6.401-2.599A2 2 0 004 16v-1M4 12H3m1 0v-1m16 1v1a2 2 0 01-1.599 1.9m1.599-1.9H21m-1 0v-1m0-8v-1a2 2 0 00-1.599-1.9M19.401 5.1H21m-1 0v-1"></path>
                                </svg>
                                <p class="mt-2 text-sm">Data Akun Kosong.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if ($accounts->hasPages())
        <div class="mt-6">
            {!! $accounts->links() !!}
        </div>
    @endif

@endsection