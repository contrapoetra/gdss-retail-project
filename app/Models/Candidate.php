<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;
    
    // KUNCI: Hanya kolom ini yang boleh diisi via form (Mass Assignment)
    protected $fillable = [
        'period_id',
        'name', 
        'age', 
        'experience_year'
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}