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
        'experience_year',
        'full_name',
        'phone_number',
        'email',
        'domicile_city',
        'portfolio_link'
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}