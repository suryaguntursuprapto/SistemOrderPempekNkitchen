<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    /**
     * Atribut yang boleh diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'purchase_id',
        'item_name',
        'quantity',
        'unit',
        'price_per_unit',
        'subtotal',
    ];

    /**
     * Tipe data bawaan.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relasi ke kepala nota pembelian.
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}