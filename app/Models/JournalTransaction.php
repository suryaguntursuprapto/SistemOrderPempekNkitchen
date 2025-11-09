<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalTransaction extends Model
{
    use HasFactory;
    
    protected $fillable = ['journal_id', 'chart_of_account_id', 'debit', 'credit'];
    
    // Koneksi ke kepala jurnal
    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    // KONEKSI KE COA
    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class);
    }
}