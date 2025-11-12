@extends('layouts.report')

@section('report_content')

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Catatan Pembelian</h1>
            <p class="text-gray-600 mt-1">Mencatat semua pembelian bahan baku dan aset.</p>
        </div>
        <a href="{{ route('admin.purchases.create') }}" 
           class="inline-flex items-center px-4 py-2 custom-gradient text-white rounded-xl text-sm font-medium transition-all duration-300 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
           style="background: linear-gradient(to right, #f97316, #ea580c);">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Catat Pembelian Baru
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg" role="alert">
            <p>{{ $message }}</p>
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Pemasok / No. Faktur
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($purchases as $purchase)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $purchase->purchase_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <p class="text-gray-900 font-medium">{{ $purchase->supplier_name ?? 'N/A' }}</p>
                                <p class="text-gray-500">{{ $purchase->invoice_number ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($purchase->status == 'paid')
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Lunas (Paid)
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Hutang (Unpaid)
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-semibold">
                                Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                                <form action="{{ route('admin.purchases.destroy', $purchase->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pembelian ini? Ini juga akan menghapus jurnal terkait.');" class="inline-flex gap-4">
                                    <a href="{{ route('admin.purchases.show', $purchase->id) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                </svg>
                                <p class="mt-2 text-sm">Belum ada data pembelian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if ($purchases->hasPages())
        <div class="mt-6">
            {!! $purchases->links() !!}
        </div>
    @endif

@endsection