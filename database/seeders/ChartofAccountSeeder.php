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
            ['code' => '2101', 'name' => 'Hutang Usaha', 'type' => 'Liability', 'normal_balance' => 'Credit'], // <-- TAMBAHKAN INI
            ['code' => '4000', 'name' => 'Pendapatan Penjualan', 'type' => 'Revenue', 'normal_balance' => 'Credit'],
            ['code' => '5000', 'name' => 'Beban Operasional', 'type' => 'Expense', 'normal_balance' => 'Debit'],
            ['code' => '5001', 'name' => 'Beban Bahan Baku', 'type' => 'Expense', 'normal_balance' => 'Debit'], // <-- TAMBAHKAN INI (atau ubah yg 5000)
        ];

        // Gunakan updateOrCreate agar aman dijalankan berulang kali
        foreach ($accounts as $account) {
            ChartOfAccount::updateOrCreate(['code' => $account['code']], $account);
        }
    }
}