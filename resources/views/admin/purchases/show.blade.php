@extends('layouts.report')

@section('report_content')

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Pembelian</h1>
            <p class="text-gray-600 mt-1">
                Faktur: {{ $purchase->invoice_number ?? $purchase->id }} | 
                Pemasok: {{ $purchase->supplier_name ?? 'N/A' }}
            </p>
        </div>
        <a href="{{ route('admin.purchases.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-xl font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition ease-in-out duration-150">
            Kembali ke Daftar
        </a>
    </div>

    @if ($message = Session::get('info'))
        <div class="mb-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded-lg" role="alert">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
        
        <div class="p-6 border-b border-gray-200">
            <dl class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
                <div class="md:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Tanggal Pembelian</dt>
                    <dd class="text-base text-gray-900 font-semibold">{{ $purchase->purchase_date->format('d F Y') }}</dd>
                </div>
                <div class="md:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="text-base text-gray-900 font-semibold">
                        @if($purchase->status == 'paid')
                            <span class="text-green-700">Lunas (Paid)</span>
                        @else
                            <span class="text-yellow-700">Hutang (Unpaid)</span>
                        @endif
                    </dd>
                </div>
                <div class="md:col-span-1">
                    <dt class="text-sm font-medium text-gray-500">Total Pembelian</T>
                    <dd class="text-base text-gray-900 font-bold">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</dd>
                </div>
                <div class="md:col-span-3">
                    <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                    <dd class="text-base text-gray-900 whitespace-pre-line">{{ $purchase->notes ?? '-' }}</dd>
                </div>
            </dl>
        </div>

        <div class="overflow-x-auto">
            <h3 class="text-lg font-semibold text-gray-900 px-6 pt-5">Item yang Dibeli</h3>
            <table class="min-w-full divide-y divide-gray-200 mt-2">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Harga Satuan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($purchase->purchaseDetails as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->item_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $item->unit }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">Rp {{ number_format($item->price_per_unit, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-3 text-right text-sm font-bold text-gray-700 uppercase">
                            Total Keseluruhan
                        </td>
                        <td class="px-6 py-3 text-right text-sm font-bold text-gray-900">
                            Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection