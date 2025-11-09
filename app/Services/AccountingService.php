<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Journal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountingService
{
    // Definisikan Kode Akun (sesuai seeder)
    const KAS_ACCOUNT = '1101';
    const REVENUE_ACCOUNT = '4000';
    const EXPENSE_ACCOUNT = '5000'; // Default
    
    /**
     * Mencatat Jurnal Penjualan dari Order
     */
    public function recordSale(Order $order)
    {
        // Pastikan kita hanya menjurnal satu kali
        if ($order->journal()->exists()) {
            Log::warning("Jurnal untuk Order {$order->order_number} sudah ada.");
            return;
        }

        try {
            // 1. Dapatkan ID Akun dari COA
            $kasAccount = ChartOfAccount::where('code', self::KAS_ACCOUNT)->firstOrFail();
            $revenueAccount = ChartOfAccount::where('code', self::REVENUE_ACCOUNT)->firstOrFail();
            $amount = $order->total_amount;
            
            DB::transaction(function () use ($order, $kasAccount, $revenueAccount, $amount) {
                // 2. Buat Kepala Jurnal
                $journal = $order->journal()->create([
                    'date' => $order->created_at->format('Y-m-d'),
                    'description' => "Penjualan " . $order->order_number,
                ]);

                // 3. Buat Baris Jurnal (Debit & Kredit)
                // (D) Kas
                $journal->transactions()->create([
                    'chart_of_account_id' => $kasAccount->id,
                    'debit' => $amount,
                    'credit' => 0,
                ]);

                // (K) Pendapatan
                $journal->transactions()->create([
                    'chart_of_account_id' => $revenueAccount->id,
                    'debit' => 0,
                    'credit' => $amount,
                ]);
            });

            Log::info("Jurnal Penjualan {$order->order_number} berhasil dibuat.");

        } catch (\Exception $e) {
            Log::error("Gagal membuat jurnal penjualan: " . $e->getMessage(), ['order_id' => $order->id]);
        }
    }

    /**
     * Mencatat Jurnal Biaya dari Expense
     */
    public function recordExpense(Expense $expense)
    {
        if ($expense->journal()->exists()) {
            Log::warning("Jurnal untuk Expense {$expense->id} sudah ada.");
            return;
        }

        try {
            // 1. Dapatkan ID Akun
            $kasAccount = ChartOfAccount::where('code', self::KAS_ACCOUNT)->firstOrFail();
            $expenseAccount = ChartOfAccount::where('code', self::EXPENSE_ACCOUNT)->firstOrFail(); // Nanti bisa disesuaikan dgn kategori
            $amount = $expense->amount;

            DB::transaction(function () use ($expense, $kasAccount, $expenseAccount, $amount) {
                // 2. Buat Kepala Jurnal
                $journal = $expense->journal()->create([
                    'date' => $expense->date->format('Y-m-d'),
                    'description' => $expense->description,
                ]);

                // 3. Buat Baris Jurnal
                // (D) Beban
                $journal->transactions()->create([
                    'chart_of_account_id' => $expenseAccount->id,
                    'debit' => $amount,
                    'credit' => 0,
                ]);
                
                // (K) Kas
                $journal->transactions()->create([
                    'chart_of_account_id' => $kasAccount->id,
                    'debit' => 0,
                    'credit' => $amount,
                ]);
            });

            Log::info("Jurnal Biaya {$expense->id} berhasil dibuat.");

        } catch (\Exception $e) {
            Log::error("Gagal membuat jurnal biaya: " . $e->getMessage(), ['expense_id' => $expense->id]);
        }
    }
}