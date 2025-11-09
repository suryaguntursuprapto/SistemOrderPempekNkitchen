<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ExpensesReportSheet implements FromQuery, WithHeadings, WithMapping, WithTitle
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
        return Expense::query()
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->with('user');
    }

    public function headings(): array
    {
        return [
            'ID Biaya',
            'Tanggal',
            'Deskripsi',
            'Kategori',
            'Dicatat oleh',
            'Jumlah (Rp)',
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->id,
            $expense->date->format('Y-m-d'),
            $expense->description,
            $expense->category,
            $expense->user->name,
            $expense->amount,
        ];
    }

    public function title(): string
    {
        return 'Laporan Biaya';
    }
}
