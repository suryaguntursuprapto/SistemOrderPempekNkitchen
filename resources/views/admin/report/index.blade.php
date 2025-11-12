@extends('layouts.report')

@section('report_content')

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Laporan Keuangan</h1>
        <p class="text-gray-600">Analisis penjualan, biaya, dan laba/rugi.</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('admin.report.index') }}" method="GET" class="flex flex-col md:flex-row md:items-end gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                <input type="date" name="start_date" id="start_date" 
                       value="{{ $startDate }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                <input type="date" name="end_date" id="end_date" 
                       value="{{ $endDate }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Filter
                </button>
                <a href="{{ route('admin.report.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Cetak Excel
                </a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500 mb-1">Total Penjualan</p>
            <p class="text-3xl font-bold text-green-600">
                Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500 mb-1">Total Biaya & Pembelian</p>
            <p class="text-3xl font-bold text-red-600">
                Rp {{ number_format($summary['total_expenses'], 0, ',', '.') }}
            </p>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500 mb-1">
                @if($summary['profit'] >= 0)
                    Laba Bersih
                @else
                    Rugi Bersih
                @endif
            </p>
            <p class="text-3xl font-bold {{ $summary['profit'] >= 0 ? 'text-indigo-600' : 'text-red-600' }}">
                Rp {{ number_format($summary['profit'], 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <h3 class="text-lg font-semibold text-gray-900 px-6 py-4 border-b border-gray-200">Detail Penjualan</h3>
            <div class="p-6 max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($salesData as $order)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ $order->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ $order->order_number }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm"><span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">{{ $order->status_label }}</span></td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 text-right font-medium">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-gray-500">Tidak ada data penjualan pada rentang tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <h3 class="text-lg font-semibold text-gray-900 px-6 py-4 border-b border-gray-200">Detail Biaya & Pembelian</h3>
            <div class="p-6 max-h-96 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        
                        @forelse($combinedCosts as $item)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ $item->date->format('d M Y') }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ Str::limit($item->description, 40) }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm">
                                    @if($item->type == 'expense')
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $item->category }}
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $item->category }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 text-right font-medium">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-gray-500">Tidak ada data biaya atau pembelian pada rentang tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        </div>

@endsection