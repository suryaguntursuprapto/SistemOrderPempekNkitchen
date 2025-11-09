<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentMethod::create([
            'name' => 'Midtrans Payment Gateway',
            'type' => 'midtrans',
            'instructions' => 'Bayar aman dan otomatis melalui Midtrans (E-Wallet, Virtual Account, Kartu Kredit).',
            'is_active' => true
        ]);
    }
}