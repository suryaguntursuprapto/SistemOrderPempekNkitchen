<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method_id',
        'amount',
        'status',
        'payment_proof',
        'notes',
        'paid_at',
        'midtrans_transaction_id',
        'midtrans_order_id',
        'midtrans_response',
        'payment_type',
        'midtrans_paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'midtrans_paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'midtrans_response' => 'array'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // Check if payment is using Midtrans
    public function isMidtransPayment()
    {
        return $this->paymentMethod && $this->paymentMethod->type === 'midtrans';
    }

    // Get Midtrans payment status
    public function getMidtransStatus()
    {
        if (!$this->midtrans_response) {
            return null;
        }

        $response = $this->midtrans_response;
        return $response['transaction_status'] ?? null;
    }
}