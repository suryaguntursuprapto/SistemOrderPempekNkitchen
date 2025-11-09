<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id(); // Primary Key (bigint unsigned)
            
            // Kode akun (e.g., '1101', '4000')
            $table->string('code')->unique(); 
            
            // Nama akun (e.g., 'Kas', 'Pendapatan Penjualan')
            $table->string('name'); 
            
            // Tipe akun utama
            $table->enum('type', ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense']);
            
            // Saldo normal akun (Debit atau Kredit)
            $table->enum('normal_balance', ['Debit', 'Credit']);

            // Untuk struktur hierarki (parent-child)
            $table->unsignedBigInteger('parent_id')->nullable(); 

            $table->text('description')->nullable(); // Deskripsi tambahan (opsional)
            
            $table->timestamps(); // Kolom created_at dan updated_at

            // Definisi Foreign Key untuk parent_id
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('chart_of_accounts')
                  ->onDelete('set null'); // Jika parent dihapus, set ID ini ke null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};