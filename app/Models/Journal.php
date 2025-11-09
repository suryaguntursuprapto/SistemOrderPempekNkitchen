<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;
    
    protected $fillable = ['date', 'description', 'referenceable_id', 'referenceable_type'];

    // Relasi ke baris-baris (debit/kredit)
    public function transactions()
    {
        return $this->hasMany(JournalTransaction::class);
    }
    
    // Relasi ke model sumber (Order, Expense, dll)
    public function referenceable()
    {
        return $this->morphTo();
    }
}