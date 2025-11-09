<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_transactions', function (Blueprint $table) {
            $table->id();
            
            // 1. Koneksi ke Kepala Jurnal
            $table->foreignId('journal_id')->constrained()->onDelete('cascade');
            
            // 2. KONEKSI KE CHART OF ACCOUNT
            $table->foreignId('chart_of_account_id')->constrained()->onDelete('restrict');
            
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_transactions');
    }
};