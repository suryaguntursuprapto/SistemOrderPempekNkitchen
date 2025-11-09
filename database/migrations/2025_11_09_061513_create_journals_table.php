<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Tanggal transaksi
            $table->string('description'); // Deskripsi Jurnal (cth: "Penjualan ORD-123")
            
            // Ini untuk menghubungkan ke Order, Expense, dll. (Polymorphic)
            $table->morphs('referenceable'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};