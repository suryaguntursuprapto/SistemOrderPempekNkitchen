<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesReportSheet implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    private $startDate;
    private $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        // Ambil hanya order yang sudah selesai/terkonfirmasi
        return Order::query()
            ->whereIn('status', ['delivered', 'confirmed', 'ready']) 
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->with('user');
    }

    public function headings(): array
    {
        return [
            'ID Pesanan',
            'Nomor Pesanan',
            'Tanggal',
            'Customer',
            'Status',
            'Total (Rp)',
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->order_number,
            $order->created_at->format('Y-m-d H:i'),
            $order->user->name,
            $order->status_label,
            $order->total_amount,
        ];
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }
}
