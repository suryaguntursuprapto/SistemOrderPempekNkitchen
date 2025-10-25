<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // midtrans, bank_transfer, e_wallet, cash_on_delivery
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->text('instructions')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
};