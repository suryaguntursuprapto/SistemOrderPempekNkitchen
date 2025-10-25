<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'account_number',
        'account_name', 
        'instructions',
        'logo',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
