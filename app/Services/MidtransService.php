<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use App\Models\Payment;

class MidtransService
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    public function createTransaction($order)
    {
        $orderItems = [];
        foreach ($order->orderItems as $item) {
            $orderItems[] = [
                'id' => $item->menu->id,
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
                'name' => $item->menu->name,
                'category' => $item->menu->category ?? 'Food'
            ];
        }

        // Tambahkan biaya pengiriman
        $shippingCost = 5000; // Rp 5.000
        $orderItems[] = [
            'id' => 'shipping',
            'price' => $shippingCost,
            'quantity' => 1,
            'name' => 'Ongkos Kirim'
        ];

        $midtransOrderId = $order->order_number . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => (int) ($order->total_amount + $shippingCost),
            ],
            'item_details' => $orderItems,
            'customer_details' => [
                'first_name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->phone,
                'billing_address' => [
                    'address' => $order->delivery_address,
                ],
                'shipping_address' => [
                    'address' => $order->delivery_address,
                ]
            ],
            'enabled_payments' => [
                'credit_card', 'mandiri_clickpay', 'cimb_clicks',
                'bca_klikbca', 'bca_klikpay', 'bri_epay', 'echannel',
                'permata_va', 'bca_va', 'bni_va', 'other_va',
                'gopay', 'shopeepay', 'indomaret', 'alfamart'
            ],
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s O'),
                'unit' => 'minutes',
                'duration' => 60
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            // ✅ FIX: Cek apakah payment record ada sebelum update
            if ($order->payment) {
                // Update payment yang sudah ada
                $order->payment->update([
                    'midtrans_order_id' => $midtransOrderId,
                    'amount' => $order->total_amount + $shippingCost
                ]);
            } else {
                // ✅ FIX: Buat payment record baru jika tidak ada
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method_id' => 1, // Default ke Midtrans (sesuaikan dengan ID di database)
                    'amount' => $order->total_amount + $shippingCost,
                    'midtrans_order_id' => $midtransOrderId,
                    'status' => 'pending'
                ]);
            }

            return $snapToken;
        } catch (\Exception $e) {
            throw new \Exception('Midtrans Error: ' . $e->getMessage());
        }
    }

    public function getTransactionStatus($orderId)
    {
        try {
            return Transaction::status($orderId);
        } catch (\Exception $e) {
            throw new \Exception('Midtrans Error: ' . $e->getMessage());
        }
    }
}