<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function consensusLogs()
    {
        return $this->hasMany(ConsensusLog::class);
    }

    // Helper scope to find active period
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}