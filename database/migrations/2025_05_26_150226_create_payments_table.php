<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            
            // Cek apakah tabel payment_methods sudah ada
            if (Schema::hasTable('payment_methods')) {
                $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
            } else {
                // Jika belum ada, buat kolom biasa dulu
                $table->unsignedBigInteger('payment_method_id')->nullable();
            }
            
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_proof')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Kolom Midtrans
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('midtrans_order_id')->nullable();
            $table->json('midtrans_response')->nullable();
            $table->string('payment_type')->nullable();
            $table->timestamp('midtrans_paid_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};