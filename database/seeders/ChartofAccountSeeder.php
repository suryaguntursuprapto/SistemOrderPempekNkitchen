<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['code' => '1101', 'name' => 'Kas', 'type' => 'Asset', 'normal_balance' => 'Debit'],
            ['code' => '1102', 'name' => 'Piutang Usaha', 'type' => 'Asset', 'normal_balance' => 'Debit'],
            ['code' => '4000', 'name' => 'Pendapatan Penjualan', 'type' => 'Revenue', 'normal_balance' => 'Credit'],
            ['code' => '5000', 'name' => 'Beban Operasional', 'type' => 'Expense', 'normal_balance' => 'Debit'],
            ['code' => '5001', 'name' => 'Beban Bahan Baku', 'type' => 'Expense', 'normal_balance' => 'Debit'],
        ];

        foreach ($accounts as $account) {
            ChartOfAccount::create($account);
        }
    }
}