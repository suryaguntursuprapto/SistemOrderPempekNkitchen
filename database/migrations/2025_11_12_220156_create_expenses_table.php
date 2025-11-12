<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            
            // HAPUS KOLOM 'category' (string) LAMA
            // $table->string('category'); 
            
            // TAMBAHKAN KOLOM 'chart_of_account_id' (koneksi)
            $table->foreignId('chart_of_account_id')->constrained()->onDelete('restrict');
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }
};