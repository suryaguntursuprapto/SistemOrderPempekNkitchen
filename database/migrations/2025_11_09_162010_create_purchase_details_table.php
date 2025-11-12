<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_details', function (Blueprint $table) {
            $table->id();
            
            // Koneksi ke kepala nota
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
            
            $table->string('item_name'); // Nama barang (cth: "Ikan Tenggiri Giling")
            $table->decimal('quantity', 10, 2); // Jumlah (cth: 5.5 kg)
            $table->string('unit')->default('kg'); // Satuan (kg, pcs, liter, dll)
            $table->decimal('price_per_unit', 15, 2); // Harga per satuan
            $table->decimal('subtotal', 15, 2); // (quantity * price_per_unit)
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_details');
    }
};