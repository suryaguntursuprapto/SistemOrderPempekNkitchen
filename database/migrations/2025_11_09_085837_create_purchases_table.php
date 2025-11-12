<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->date('purchase_date'); // Tanggal nota pembelian
            $table->string('supplier_name')->nullable(); // Nama Pemasok
            $table->string('invoice_number')->nullable(); // No. Faktur dari Pemasok
            $table->decimal('total_amount', 15, 2); // Total nilai pembelian
            
            // Status: 'paid' (lunas) atau 'unpaid' (hutang)
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
            
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};