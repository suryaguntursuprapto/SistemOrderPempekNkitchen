<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Journal;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountingService
{
    // Definisikan Kode Akun (sesuai seeder)
    const KAS_ACCOUNT = '1101';
    const REVENUE_ACCOUNT = '4000';
    const EXPENSE_ACCOUNT = '5000'; // Default
    const BAHAN_BAKU_ACCOUNT = '5001'; 
    const HUTANG_USAHA_ACCOUNT = '2101'; 
    
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
            
            // 2. DAPATKAN AKUN BEBAN DARI OBJEK EXPENSE (BUKAN const)
            // Ini adalah akun yang dipilih user di form, cth: "Beban Listrik", "Beban Ikan"
            $expenseAccount = $expense->chartOfAccount; 
            
            if (!$expenseAccount) {
                 throw new \Exception("Akun Beban tidak ditemukan untuk expense ID: {$expense->id}");
            }
            
            $amount = $expense->amount;

            DB::transaction(function () use ($expense, $kasAccount, $expenseAccount, $amount) {
                // 3. Buat Kepala Jurnal
                $journal = $expense->journal()->create([
                    'date' => $expense->date->format('Y-m-d'),
                    'description' => $expense->description,
                ]);

                // 4. Buat Baris Jurnal
                // (D) Beban (sesuai pilihan user)
                $journal->transactions()->create([
                    'chart_of_account_id' => $expenseAccount->id, // <-- Ini sekarang dinamis
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
    /**
     * 🛒 Mencatat Jurnal Pembelian dari Purchase
     */
    public function recordPurchase(Purchase $purchase)
    {
        if ($purchase->journal()->exists()) {
            Log::warning("Jurnal untuk Purchase {$purchase->id} sudah ada.");
            return;
        }

        try {
            // 1. Dapatkan ID Akun
            $bebanBahanBaku = ChartOfAccount::where('code', self::BAHAN_BAKU_ACCOUNT)->firstOrFail();
            $totalAmount = $purchase->total_amount;
            
            // 2. Tentukan Akun Kredit (Kas atau Hutang)
            if ($purchase->status == 'paid') {
                $creditAccount = ChartOfAccount::where('code', self::KAS_ACCOUNT)->firstOrFail();
                $description = "Pembelian tunai " . ($purchase->invoice_number ?? $purchase->id);
            } else {
                $creditAccount = ChartOfAccount::where('code', self::HUTANG_USAHA_ACCOUNT)->firstOrFail();
                $description = "Pembelian kredit " . ($purchase->invoice_number ?? $purchase->id);
            }

            DB::transaction(function () use ($purchase, $bebanBahanBaku, $creditAccount, $totalAmount, $description) {
                // 3. Buat Kepala Jurnal
                $journal = $purchase->journal()->create([
                    'date' => $purchase->purchase_date->format('Y-m-d'),
                    'description' => $description,
                ]);

                // 4. Buat Baris Jurnal
                // (D) Beban Bahan Baku / Persediaan
                $journal->transactions()->create([
                    'chart_of_account_id' => $bebanBahanBaku->id,
                    'debit' => $totalAmount,
                    'credit' => 0,
                ]);
                
                // (K) Kas / Hutang
                $journal->transactions()->create([
                    'chart_of_account_id' => $creditAccount->id,
                    'debit' => 0,
                    'credit' => $totalAmount,
                ]);
            });

            Log::info("Jurnal Pembelian {$purchase->id} berhasil dibuat.");

        } catch (\Exception $e) {
            Log::error("Gagal membuat jurnal pembelian: " . $e->getMessage(), ['purchase_id' => $purchase->id]);
            // Hapus purchase jika jurnal gagal dibuat? (Opsional)
            // $purchase->delete();
            throw new \Exception("Gagal membuat jurnal: " . $e->getMessage());
        }
    }
}