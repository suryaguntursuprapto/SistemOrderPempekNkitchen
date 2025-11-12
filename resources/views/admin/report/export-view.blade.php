<!DOCTYPE html>
<html>
<head>
    <style>
        /* CSS dasar agar Excel bisa membacanya */
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f0f0f0; font-weight: bold; text-align: left; }
        .header { font-size: 16px; font-weight: bold; }
        .subheader { font-size: 14px; font-weight: bold; }
        .total { font-weight: bold; background-color: #f0f0f0; }
        .profit { background-color: #d0f0d0; }
        .loss { background-color: #f0d0d0; }
        .align-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">Laporan Laba/Rugi</div>
    <div>Periode: {{ $startDate }} s/d {{ $endDate }}</div>
    <br>

    <div class="subheader">Ringkasan</div>
    <table>
        <tbody>
            <tr>
                <td>Total Penjualan (Revenue)</td>
                <td class="align-right">Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Total Biaya & Pembelian (Costs)</td>
                <td class="align-right">Rp {{ number_format($summary['total_expenses'], 0, ',', '.') }}</td>
            </tr>
            <tr class="total {{ $summary['profit'] >= 0 ? 'profit' : 'loss' }}">
                <td>Laba/Rugi Bersih</td>
                <td class="align-right">Rp {{ number_format($summary['profit'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    <br>

    <div class="subheader">Detail Penjualan</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Order ID</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salesData as $order)
                <tr>
                    <td>{{ $order->created_at->format('Y-m-d') }}</td>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->status_label }}</td>
                    <td class="align-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Tidak ada data penjualan pada periode ini.</td></tr>
            @endforelse
            <tr class="total">
                <td colspan="3" class="align-right">Total Penjualan</td>
                <td class="align-right">Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    <br>

    <div class="subheader">Detail Biaya & Pembelian</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($combinedCosts as $item)
                <tr>
                    <td>{{ $item->date->format('Y-m-d') }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->category }}</td>
                    <td class="align-right">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Tidak ada data biaya atau pembelian pada periode ini.</td></tr>
            @endforelse
            <tr class="total">
                <td colspan="3" class="align-right">Total Biaya & Pembelian</td>
                <td class="align-right">Rp {{ number_format($summary['total_expenses'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>