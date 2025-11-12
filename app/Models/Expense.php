<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'amount',
        'date',
        'user_id',
        'chart_of_account_id', // <-- UBAH INI (dari 'category')
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Chart of Account (PENGGANTI 'category')
     */
    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }
    
    /**
     * Relasi ke Jurnal (jika sudah ada)
     */
    public function journal()
    {
        return $this->morphOne(Journal::class, 'referenceable');
    }
}